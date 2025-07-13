<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Ratchet\MessageComponentInterface;
use Ratchet\ConnectionInterface;

class GameWebSocketController implements MessageComponentInterface
{
    protected $clients;
    protected $games;

    public function __construct()
    {
        $this->clients = new \SplObjectStorage;
        $this->games = [];
    }

    public function onOpen(ConnectionInterface $conn)
    {
        $this->clients->attach($conn);
        $conn->gameData = [
            'id' => uniqid('player_'),
            'x' => 0,
            'y' => 0,
            'rotation' => 0,
            'score' => 0
        ];
        
        echo "New connection! ({$conn->gameData['id']})\n";
    }

    public function onMessage(ConnectionInterface $from, $msg)
    {
        $data = json_decode($msg, true);
        
        // Update player state
        if ($data['type'] === 'update') {
            $from->gameData = array_merge($from->gameData, $data['state']);
            
            // Broadcast to all other clients
            foreach ($this->clients as $client) {
                if ($from !== $client) {
                    $client->send(json_encode([
                        'type' => 'playerUpdate',
                        'player' => $from->gameData
                    ]));
                }
            }
        }
    }

    public function onClose(ConnectionInterface $conn)
    {
        $this->clients->detach($conn);
        
        // Notify others that player left
        foreach ($this->clients as $client) {
            $client->send(json_encode([
                'type' => 'playerLeft',
                'playerId' => $conn->gameData['id']
            ]));
        }
    }

    public function onError(ConnectionInterface $conn, \Exception $e)
    {
        echo "Error: {$e->getMessage()}\n";
        $conn->close();
    }
} 