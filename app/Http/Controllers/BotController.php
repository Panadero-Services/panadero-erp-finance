<?php

namespace App\Http\Controllers;

use App\Models\Bot;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator; // For validation

class BotController extends Controller
{
    /**
     * Display a listing of the resource.
     */ 
    public function index()
    {
        return Bot::all();
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'type' => 'required|string|max:255',
            'provider' => 'required|json',
            'processor' => 'required|json',
            'executor' => 'required|json',
            'status' => 'sometimes|string|in:running,stopped',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        // Decode JSON strings if they are sent as strings
        $data = $request->all();
        $data['provider'] = is_string($data['provider']) ? json_decode($data['provider'], true) : $data['provider'];
        $data['processor'] = is_string($data['processor']) ? json_decode($data['processor'], true) : $data['processor'];
        $data['executor'] = is_string($data['executor']) ? json_decode($data['executor'], true) : $data['executor'];


        $bot = Bot::create($data);
        return response()->json($bot, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $bot = Bot::find($id);
        if (!$bot) {
            return response()->json(['message' => 'Bot not found'], 404);
        }
        return response()->json($bot);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $bot = Bot::find($id);
        if (!$bot) {
            return response()->json(['message' => 'Bot not found'], 404);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|required|string|max:255',
            'type' => 'sometimes|required|string|max:255',
            'provider' => 'sometimes|required|json',
            'processor' => 'sometimes|required|json',
            'executor' => 'sometimes|required|json',
            'status' => 'sometimes|string|in:running,stopped',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $data = $request->all();
        // Decode JSON strings if they are sent as strings
        if (isset($data['provider']) && is_string($data['provider'])) {
            $data['provider'] = json_decode($data['provider'], true);
        }
        if (isset($data['processor']) && is_string($data['processor'])) {
            $data['processor'] = json_decode($data['processor'], true);
        }
        if (isset($data['executor']) && is_string($data['executor'])) {
            $data['executor'] = json_decode($data['executor'], true);
        }

        $bot->update($data);
        return response()->json($bot);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $bot = Bot::find($id);
        if (!$bot) {
            return response()->json(['message' => 'Bot not found'], 404);
        }
        $bot->delete();
        return response()->json(['message' => 'Bot deleted']);
    }

    /**
     * Start the specified bot.
     */
    public function start($id)
    {
        $bot = Bot::find($id);
        if (!$bot) {
            return response()->json(['message' => 'Bot not found'], 404);
        }

        // TODO: Add actual bot starting logic here (e.g., dispatch a job)
        // For now, just update status.
        $bot->status = 'running';
        // $bot->last_run_at = now(); // Optionally update last run time
        $bot->save();

        return response()->json(['message' => "Bot {$bot->name} started", 'bot' => $bot]);
    }

    /**
     * Stop the specified bot.
     */
    public function stop($id)
    {
        $bot = Bot::find($id);
        if (!$bot) {
            return response()->json(['message' => 'Bot not found'], 404);
        }

        // TODO: Add actual bot stopping logic here
        $bot->status = 'stopped';
        $bot->save();

        return response()->json(['message' => "Bot {$bot->name} stopped", 'bot' => $bot]);
    }

    /**
     * Get the status of the specified bot.
     */
    public function status($id)
    {
        $bot = Bot::find($id);
        if (!$bot) {
            return response()->json(['message' => 'Bot not found'], 404);
        }

        // TODO: Add more detailed status/log retrieval if needed
        return response()->json([
            'id' => $bot->id,
            'name' => $bot->name,
            'status' => $bot->status,
            'last_run_at' => $bot->last_run_at,
            // 'logs' => [] // Placeholder for actual logs
        ]);
    }
}