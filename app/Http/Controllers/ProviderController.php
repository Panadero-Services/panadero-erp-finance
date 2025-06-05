<?php

namespace App\Http\Controllers;

use App\Models\Provider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\App; // For dynamic instantiation
use Illuminate\Support\Facades\Log; // For logging
use App\Bots\Interfaces\ProviderInterface; // Your provider interface

class ProviderController extends Controller
{
    // ... (index, store, show, update, destroy methods) ...

    /**
     * Test a specific provider configuration.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Provider  $provider The Provider model instance (via route model binding)
     * @return \Illuminate\Http\JsonResponse
     */
    public function test(Request $request, Provider $provider)
    {
        $providerType = $provider->type;
        $providerConfig = $provider->config; // Already an array due to $casts

        // Construct the fully qualified class name (adjust namespace if needed)
        $className = "App\\Bots\\Providers\\" . $providerType;

        if (!class_exists($className)) {
            Log::error("Provider test: Class '{$className}' not found for provider ID {$provider->id}.");
            return response()->json(['success' => false, 'message' => "Provider class '{$className}' not found."], 500);
        }

        try {
            // Dynamically instantiate the provider.
            // Laravel's service container will attempt to resolve constructor dependencies.
            // If $providerConfig keys match constructor parameter names, they will be injected.
            $providerInstance = App::make($className, ['config' => $providerConfig]); // Pass config if constructor expects it as an array
            // Or, if constructor params match keys in $providerConfig:
            // $providerInstance = App::make($className, $providerConfig);


            if (!$providerInstance instanceof ProviderInterface) {
                Log::error("Provider test: Class '{$className}' does not implement ProviderInterface for provider ID {$provider->id}.");
                return response()->json(['success' => false, 'message' => "Provider class '{$className}' does not implement ProviderInterface."], 500);
            }

            // Execute the fetchCurrentState method (or a dedicated test method)
            // IMPORTANT: A test should generally NOT save state.
            $currentState = $providerInstance->fetchCurrentState();

            return response()->json([
                'success' => true,
                'message' => 'Provider test executed successfully.',
                'data' => $currentState
            ]);

        } catch (\Throwable $e) { // Catching Throwable for broader error capture
            Log::error("Provider test error for ID {$provider->id} ({$className}): " . $e->getMessage(), ['exception' => $e]);
            return response()->json([
                'success' => false,
                'message' => 'Error during provider test: ' . $e->getMessage(),
                // 'error_details' => $e->getTraceAsString() // Optionally include for debugging, remove for production
            ], 500);
        }
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        return response()->json(Provider::all());
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
public function store(Request $request)
{
    $validatedData = $request->validate([
        'name' => 'required|string|max:255',
        'type' => 'required|string|max:255',
        'config' => 'required|array', // Changed from 'json'
        'is_active' => 'sometimes|boolean',
    ]);

    // Ensure 'is_active' has a default if not provided or handle as needed
    $validatedData['is_active'] = $request->input('is_active', true);


    $provider = Provider::create($validatedData);
    return response()->json($provider, 201);
}

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        $provider = Provider::find($id);

        if (!$provider) {
            return response()->json(['message' => 'Provider not found'], 404);
        }

        return response()->json($provider);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
public function update(Request $request, Provider $provider) // Using route model binding
{
    $validatedData = $request->validate([
        'name' => 'sometimes|required|string|max:255',
        'type' => 'sometimes|required|string|max:255',
        'config' => 'sometimes|required|array', // Changed from 'json'
        'is_active' => 'sometimes|boolean',
    ]);

    // Ensure 'is_active' is handled correctly on update
    if ($request->has('is_active')) {
        $validatedData['is_active'] = $request->input('is_active');
    }


    $provider->update($validatedData);
    return response()->json($provider);
}

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        $provider = Provider::find($id);

        if (!$provider) {
            return response()->json(['message' => 'Provider not found'], 404);
        }

        $provider->delete();

        return response()->json(['message' => 'Provider deleted successfully']);
    }

public function getContractBalance(Provider $provider): JsonResponse
{
    $config = $provider->config;

    if (!isset($config['rpc_url']) || !isset($config['contract_address'])) {
        return response()->json([
            'success' => false,
            'message' => 'Provider configuration is missing rpc_url or contract_address.',
        ], 400);
    }

    $rpcUrl = $config['rpc_url'];
    $contractAddress = $config['contract_address'];
    $timeout = $config['rpc_timeout'] ?? 30;

    try {
        $httpProvider = new HttpProvider(new HttpRequestManager($rpcUrl, $timeout));
        $web3 = new Web3($httpProvider);
        $eth = $web3->eth; // Get the Eth object

        $balanceWei = null;
        // Use the specific method class for getBalance
        $getBalance = new GetBalance($eth->getRequestManager(), $eth->getFormatters());
        
        $getBalance->execute([$contractAddress, 'latest'], function ($err, $balance) use (&$balanceWei) {
            if ($err) {
                Log::error("Error fetching balance for {$contractAddress} on {$rpcUrl}: " . $err->getMessage());
                // This error will be caught by the outer try-catch
                throw new \RuntimeException("RPC Error: " . $err->getMessage());
            }
            $balanceWei = $balance;
        });

        if ($balanceWei === null) {
             // This case should ideally be caught by the error in callback,
             // but as a fallback if the callback completes without error but no balance.
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve balance. RPC call did not return a value.',
            ], 500);
        }

        // Convert balance from Wei to Ether (or the main unit of the chain)
        // The '18' assumes standard 18 decimal places.
        // Some chains might differ, but this is a common standard.
        $balanceEther = Utils::fromWei($balanceWei->toString(), 'ether')[0]; // Access the first element of the returned array

        return response()->json([
            'success' => true,
            'data' => [
                'contract_address' => $contractAddress,
                'balance_wei' => $balanceWei->toString(),
                'balance_ether' => $balanceEther->toString(), // Send as string to preserve precision
                'unit' => 'native currency (e.g., ETH, BNB)' // Generic unit name
            ]
        ]);

    } catch (\RuntimeException $e) { // Catch errors from the callback
        return response()->json([
            'success' => false,
            'message' => $e->getMessage(),
        ], 500);
    } catch (\Exception $e) {
        Log::error("General error fetching balance for {$contractAddress}: " . $e->getMessage());
        return response()->json([
            'success' => false,
            'message' => 'An unexpected error occurred: ' . $e->getMessage(),
        ], 500);
    }
}


    
}