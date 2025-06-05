<?php

namespace App\Bots\Executors;

use App\Bots\Interfaces\ExecutorInterface;
use Discord\Discord;
use Telegram\Bot\Api as TelegramBot;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class MultiChannelExecutor implements ExecutorInterface
{
    private Discord $discord;
    private TelegramBot $telegram;
    private array $channelConfig;

    public const CHANNEL_DISCORD = 'discord';
    public const CHANNEL_TELEGRAM = 'telegram';
    public const CHANNEL_DB = 'database';
    public const CHANNEL_EMAIL = 'email';
    public const CHANNEL_SLACK = 'slack';
    public const CHANNEL_X = 'x';
    public const CHANNEL_WEBHOOK = 'webhook';

    public function __construct(
        Discord $discord,
        TelegramBot $telegram,
        array $channelConfig
    ) {
        $this->discord = $discord;
        $this->telegram = $telegram;
        $this->channelConfig = $channelConfig;
    }

    public function handleEvent(string $eventType, array $eventData): void
    {
        $message = $this->formatMessage($eventType, $eventData);
        foreach ($this->getSupportedChannels() as $channel) {
            $this->sendNotification($channel, [
                'message' => $message,
                'data' => $eventData
            ]);
        }
    }

    public function getSupportedChannels(): array
    {
        return array_keys($this->channelConfig);
    }

    public function sendNotification(string $channel, array $data): void
    {
        switch ($channel) {
            case self::CHANNEL_DISCORD:
                $this->sendToDiscord($data);
                break;
            case self::CHANNEL_TELEGRAM:
                $this->sendToTelegram($data);
                break;
            case self::CHANNEL_DB:
                $this->saveToDatabase($data);
                break;
            case self::CHANNEL_EMAIL:
                $this->sendToEmail($data);
                break;
            case self::CHANNEL_SLACK:
                $this->sendToSlack($data);
                break;
            case self::CHANNEL_X:
                $this->sendToX($data);
                break;
            case self::CHANNEL_WEBHOOK:
                $this->sendToWebhook($data);
                break;
        }
    }

    private function sendToDiscord(array $data): void
    {
        $channelId = $this->channelConfig[self::CHANNEL_DISCORD]['channel_id'] ?? null;
        if ($channelId) {
            $channel = $this->discord->getChannel($channelId);
            $channel->sendMessage($data['message']);
        }
    }

    private function sendToTelegram(array $data): void
    {
        $chatId = $this->channelConfig[self::CHANNEL_TELEGRAM]['chat_id'] ?? null;
        if ($chatId) {
            $this->telegram->sendMessage([
                'chat_id' => $chatId,
                'text' => $data['message']
            ]);
        }
    }

    private function saveToDatabase(array $data): void
    {
        DB::table('bot_events')->insert([
            'event_type' => $data['data']['type'],
            'channel' => self::CHANNEL_DB,
            'data' => json_encode($data['data']),
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    private function sendToEmail(array $data): void
    {
        $to = $this->channelConfig[self::CHANNEL_EMAIL]['to'] ?? null;
        if ($to) {
            Mail::raw($data['message'], function ($message) use ($to) {
                $message->to($to)
                        ->subject('Bot Notification');
            });
        }
    }

    private function sendToSlack(array $data): void
    {
        $webhookUrl = $this->channelConfig[self::CHANNEL_SLACK]['webhook_url'] ?? null;
        if ($webhookUrl) {
            Http::post($webhookUrl, [
                'text' => $data['message']
            ]);
        }
    }

    private function sendToX(array $data): void
    {
        // Placeholder for X (Twitter) API integration
        // You'd use an SDK or HTTP client to post a tweet here
        Log::info('X (Twitter) notification:', $data);
    }

    private function sendToWebhook(array $data): void
    {
        $url = $this->channelConfig[self::CHANNEL_WEBHOOK]['url'] ?? null;
        if ($url) {
            Http::post($url, [
                'message' => $data['message'],
                'data' => $data['data']
            ]);
        }
    }

    private function formatMessage(string $eventType, array $eventData): string
    {
        return "🚨 {$eventType} Detected!\n" .
               (isset($eventData['field']) ? "Field: {$eventData['field']}\n" : '') .
               (isset($eventData['old_value']) ? "Old Value: {$eventData['old_value']}\n" : '') .
               (isset($eventData['new_value']) ? "New Value: {$eventData['new_value']}\n" : '') .
               (isset($eventData['value']) ? "Value: {$eventData['value']}\n" : '') .
               (isset($eventData['threshold']) ? "Threshold: {$eventData['threshold']}\n" : '') .
               (isset($eventData['pattern']) ? "Pattern: {$eventData['pattern']}\n" : '') .
               (isset($eventData['timestamp']) ? "Timestamp: {$eventData['timestamp']}" : '');
    }
}
