<?php
namespace App\Service;

use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Component\HttpFoundation\JsonResponse;


const RIOT_API='RGAPI-d62c4466-e285-4b58-ad4c-a1a100ad6a21';

class RiotApi
{
    private HttpClientInterface $client;

    public function __construct(HttpClientInterface $client)
    {
        $this->client = $client;
    }

    /**
     * @throws ClientExceptionInterface
     * @throws ServerExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws TransportExceptionInterface
     */
    public function fetchRiotApiInformation($inputName)
    {
        try {
            $response = $this->client->request('GET', "https://euw1.api.riotgames.com/lol/summoner/v4/summoners/by-name/$inputName", ['headers' => ['X-Riot-Token' => RIOT_API]]);
            $id = json_decode($response->getContent())->id;
            $accountId = json_decode($response->getContent())->accountId;
            $puuid = json_decode($response->getContent())->puuid;
            $name = json_decode($response->getContent())->name;
        }
        catch (\Exception $e) {
            return new JsonResponse(['error' => $e->getMessage()]);
        }

        $profileData = $this->client->request('GET', "https://euw1.api.riotgames.com/lol/league/v4/entries/by-summoner/$id", ['headers' => ['X-Riot-Token' => RIOT_API]]);
        $profileData = json_decode($profileData->getContent());
        $tier = $profileData[0]->tier;
        $rank = $profileData[0]->rank;
        $leaguePoints = $profileData[0]->leaguePoints;
        $wins = $profileData[0]->wins;
        $losses = $profileData[0]->losses;
        $winrate = round(($wins / ($wins + $losses)) * 100, 2);

        $profileData = $this->client->request('GET', "https://europe.api.riotgames.com/lol/match/v5/matches/by-puuid/$puuid/ids?start=0&count=20", ['headers' => ['X-Riot-Token' => RIOT_API]]);
        $matches = json_decode($profileData->getContent());
        $matches = array_slice($matches, 0, 5);
        $matches = array_reverse($matches);
        foreach ($matches as $key => $match) {
            $matches[$key] = $this->client->request('GET', "https://europe.api.riotgames.com/lol/match/v5/matches/$match", ['headers' => ['X-Riot-Token' => RIOT_API]]);
            $matches[$key] = json_decode($matches[$key]->getContent());
        }

        return new JsonResponse([
            'name' => $name,
            'tier' => $tier,
            'rank' => $rank,
            'leaguePoints' => $leaguePoints,
            'wins' => $wins,
            'losses' => $losses,
            'winrate' => $winrate,
            'matches' => $matches,
        ]);
    }

    /**
     * @throws TransportExceptionInterface
     */
    public function fetchUserInfo($name)
    {
        $response = $this->client->request('GET', "https://euw1.api.riotgames.com/lol/summoner/v4/summoners/by-name/$inputName", ['headers' => ['X-Riot-Token' => RIOT_API]]);
    }

}