<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Provider;

class ProviderSeeder extends Seeder
{
    public function run(): void
    {
        $providers = [
            [
                'name' => 'Bitcoin (BTC)',
                'type' => 'ApiProvider',
                'config' => [
                    'url' => 'https://api.coingecko.com/api/v3/simple/price?ids=bitcoin&vs_currencies=usd',
                    'method' => 'GET',
                    'headers' => [],
                    'cache_key_prefix' => 'btc_price',
                    'cache_ttl_seconds' => 300
                ],
                'is_active' => true
            ],
            [
                'name' => 'Ethereum (ETH)',
                'type' => 'ApiProvider',
                'config' => [
                    'url' => 'https://api.coingecko.com/api/v3/simple/price?ids=ethereum&vs_currencies=usd',
                    'method' => 'GET',
                    'headers' => [],
                    'cache_key_prefix' => 'eth_price',
                    'cache_ttl_seconds' => 300
                ],
                'is_active' => true
            ],
            [
                'name' => 'BNB (BNB)',
                'type' => 'ApiProvider',
                'config' => [
                    'url' => 'https://api.coingecko.com/api/v3/simple/price?ids=binancecoin&vs_currencies=usd',
                    'method' => 'GET',
                    'headers' => [],
                    'cache_key_prefix' => 'bnb_price',
                    'cache_ttl_seconds' => 300
                ],
                'is_active' => true
            ],
            [
                'name' => 'Solana (SOL)',
                'type' => 'ApiProvider',
                'config' => [
                    'url' => 'https://api.coingecko.com/api/v3/simple/price?ids=solana&vs_currencies=usd',
                    'method' => 'GET',
                    'headers' => [],
                    'cache_key_prefix' => 'sol_price',
                    'cache_ttl_seconds' => 300
                ],
                'is_active' => true
            ],
            [
                'name' => 'XRP (XRP)',
                'type' => 'ApiProvider',
                'config' => [
                    'url' => 'https://api.coingecko.com/api/v3/simple/price?ids=ripple&vs_currencies=usd',
                    'method' => 'GET',
                    'headers' => [],
                    'cache_key_prefix' => 'xrp_price',
                    'cache_ttl_seconds' => 300
                ],
                'is_active' => true
            ],
            [
                'name' => 'Cardano (ADA)',
                'type' => 'ApiProvider',
                'config' => [
                    'url' => 'https://api.coingecko.com/api/v3/simple/price?ids=cardano&vs_currencies=usd',
                    'method' => 'GET',
                    'headers' => [],
                    'cache_key_prefix' => 'ada_price',
                    'cache_ttl_seconds' => 300
                ],
                'is_active' => true
            ],
            [
                'name' => 'Avalanche (AVAX)',
                'type' => 'ApiProvider',
                'config' => [
                    'url' => 'https://api.coingecko.com/api/v3/simple/price?ids=avalanche-2&vs_currencies=usd',
                    'method' => 'GET',
                    'headers' => [],
                    'cache_key_prefix' => 'avax_price',
                    'cache_ttl_seconds' => 300
                ],
                'is_active' => true
            ],
            [
                'name' => 'Dogecoin (DOGE)',
                'type' => 'ApiProvider',
                'config' => [
                    'url' => 'https://api.coingecko.com/api/v3/simple/price?ids=dogecoin&vs_currencies=usd',
                    'method' => 'GET',
                    'headers' => [],
                    'cache_key_prefix' => 'doge_price',
                    'cache_ttl_seconds' => 300
                ],
                'is_active' => true
            ],
            [
                'name' => 'Polkadot (DOT)',
                'type' => 'ApiProvider',
                'config' => [
                    'url' => 'https://api.coingecko.com/api/v3/simple/price?ids=polkadot&vs_currencies=usd',
                    'method' => 'GET',
                    'headers' => [],
                    'cache_key_prefix' => 'dot_price',
                    'cache_ttl_seconds' => 300
                ],
                'is_active' => true
            ],
            [
                'name' => 'Polygon (MATIC)',
                'type' => 'ApiProvider',
                'config' => [
                    'url' => 'https://api.coingecko.com/api/v3/simple/price?ids=matic-network&vs_currencies=usd',
                    'method' => 'GET',
                    'headers' => [],
                    'cache_key_prefix' => 'matic_price',
                    'cache_ttl_seconds' => 300
                ],
                'is_active' => true
            ],
            [
                'name' => 'Chainlink (LINK)',
                'type' => 'ApiProvider',
                'config' => [
                    'url' => 'https://api.coingecko.com/api/v3/simple/price?ids=chainlink&vs_currencies=usd',
                    'method' => 'GET',
                    'headers' => [],
                    'cache_key_prefix' => 'link_price',
                    'cache_ttl_seconds' => 300
                ],
                'is_active' => true
            ],
            [
                'name' => 'Shiba Inu (SHIB)',
                'type' => 'ApiProvider',
                'config' => [
                    'url' => 'https://api.coingecko.com/api/v3/simple/price?ids=shiba-inu&vs_currencies=usd',
                    'method' => 'GET',
                    'headers' => [],
                    'cache_key_prefix' => 'shib_price',
                    'cache_ttl_seconds' => 300
                ],
                'is_active' => true
            ],
            [
                'name' => 'Uniswap (UNI)',
                'type' => 'ApiProvider',
                'config' => [
                    'url' => 'https://api.coingecko.com/api/v3/simple/price?ids=uniswap&vs_currencies=usd',
                    'method' => 'GET',
                    'headers' => [],
                    'cache_key_prefix' => 'uni_price',
                    'cache_ttl_seconds' => 300
                ],
                'is_active' => true
            ],
            [
                'name' => 'Litecoin (LTC)',
                'type' => 'ApiProvider',
                'config' => [
                    'url' => 'https://api.coingecko.com/api/v3/simple/price?ids=litecoin&vs_currencies=usd',
                    'method' => 'GET',
                    'headers' => [],
                    'cache_key_prefix' => 'ltc_price',
                    'cache_ttl_seconds' => 300
                ],
                'is_active' => true
            ],
            [
                'name' => 'Bitcoin Cash (BCH)',
                'type' => 'ApiProvider',
                'config' => [
                    'url' => 'https://api.coingecko.com/api/v3/simple/price?ids=bitcoin-cash&vs_currencies=usd',
                    'method' => 'GET',
                    'headers' => [],
                    'cache_key_prefix' => 'bch_price',
                    'cache_ttl_seconds' => 300
                ],
                'is_active' => true
            ],
            [
                'name' => 'Stellar (XLM)',
                'type' => 'ApiProvider',
                'config' => [
                    'url' => 'https://api.coingecko.com/api/v3/simple/price?ids=stellar&vs_currencies=usd',
                    'method' => 'GET',
                    'headers' => [],
                    'cache_key_prefix' => 'xlm_price',
                    'cache_ttl_seconds' => 300
                ],
                'is_active' => true
            ],
            [
                'name' => 'Monero (XMR)',
                'type' => 'ApiProvider',
                'config' => [
                    'url' => 'https://api.coingecko.com/api/v3/simple/price?ids=monero&vs_currencies=usd',
                    'method' => 'GET',
                    'headers' => [],
                    'cache_key_prefix' => 'xmr_price',
                    'cache_ttl_seconds' => 300
                ],
                'is_active' => true
            ],
            [
                'name' => 'Cosmos (ATOM)',
                'type' => 'ApiProvider',
                'config' => [
                    'url' => 'https://api.coingecko.com/api/v3/simple/price?ids=cosmos&vs_currencies=usd',
                    'method' => 'GET',
                    'headers' => [],
                    'cache_key_prefix' => 'atom_price',
                    'cache_ttl_seconds' => 300
                ],
                'is_active' => true
            ],
            [
                'name' => 'Ethereum Classic (ETC)',
                'type' => 'ApiProvider',
                'config' => [
                    'url' => 'https://api.coingecko.com/api/v3/simple/price?ids=ethereum-classic&vs_currencies=usd',
                    'method' => 'GET',
                    'headers' => [],
                    'cache_key_prefix' => 'etc_price',
                    'cache_ttl_seconds' => 300
                ],
                'is_active' => true
            ],
            [
                'name' => 'Filecoin (FIL)',
                'type' => 'ApiProvider',
                'config' => [
                    'url' => 'https://api.coingecko.com/api/v3/simple/price?ids=filecoin&vs_currencies=usd',
                    'method' => 'GET',
                    'headers' => [],
                    'cache_key_prefix' => 'fil_price',
                    'cache_ttl_seconds' => 300
                ],
                'is_active' => true
            ]
        ];

        foreach ($providers as $provider) {
            Provider::create($provider);
        }
    }
} 