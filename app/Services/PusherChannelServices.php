<?php
namespace App\Services;

use Pusher;

class PusherChannelServices
{
    protected $pusher;

    public function __construct()
    {
        $options = [
            'cluster' => 'ap1',
            'useTLS' => true
        ];
        $this->pusher = new Pusher\Pusher('d997efccc398d5dd1947', '9330ea44908a463c807e', '2089799', $options);
    }

    public function push(string $channel, string $event, array $data)
    {
        $this->pusher->trigger($channel, $event, $data);
    }
}