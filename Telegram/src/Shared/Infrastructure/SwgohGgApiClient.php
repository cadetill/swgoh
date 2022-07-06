<?php

namespace Im\Shared\Infrastructure;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Im\Shared\Domain\AllyCode;

class SwgohGgApiClient
{
    private Client $client;

    public function __construct()
    {
        $this->client = new Client([ 'user_agent' => 'insomnia/2022.4.2' ]);
    }

    public function player(AllyCode $allyCode)
    {
        try {
            $response = $this->client->get($this->url('/player/' . $allyCode->value()));
            return json_decode($response->getBody(), true);
        } catch (GuzzleException $exception) {

        }
    }

    private function url(string $path)
    {
        return 'http://api.swgoh.gg'.$path.'/';
    }
}
