<?php

namespace Im\Shared\Infrastructure;

use DateInterval;
use DateTime;
use DateTimeImmutable;
use JsonMachine\Items;
use JsonMachine\JsonDecoder\ExtJsonDecoder;
use SwgohHelp;

require_once __DIR__ . '/../../../SwgohHelp.php';

class SwgohHelpRepository
{
    private static string $CACHE_FOLDER                   = __DIR__ . '/../../../cache';
    private static int    $DEFAULT_CACHE_LIFETIME_SECONDS = 10800; // 3 hours
    private static int    $MAX_CHUNK_SIZE                 = 25;

    private SwgohHelp $client;
    private string    $lang;

    public function __construct(string $username, string $password, string $lang = 'eng_us')
    {
        $this->client = new SwgohHelp([ $username, $password ]);
        $this->lang   = $lang;
    }

    public function player(int $allyCode): array
    {
        $data = $this->playerFromCache($allyCode);
        if (!is_null($data)) {
            return json_decode($data, true);
        }

        $data = $this->client->fetchPlayer($allyCode, $this->lang);
        $player = json_decode($data, true);
        $this->storePlayerCache($data, $allyCode, intval($player[0]['updated'] / 1000));
        return $player;
    }

    public function guild(int $allyCode)
    {
        $data = $this->guildFromCache($allyCode);
        if (!is_null($data)) {
            return json_decode($data, true);
        }

        $data    = $this->client->fetchGuild($allyCode, $this->lang);
        $guild   = json_decode($data, true);
        $guildId = $guild[0]["id"];
        $this->storeGuildCache($data, strval($allyCode), $guild[0]['updated']);
        $this->storeGuildCache($data, $guildId, $guild[0]['updated']);

        return $guild;
    }

    /*
    public function playersForMods(array $allyCodes)
    {
        // $allyCodes = array_slice($allyCodes, 0, 5);
        sort($allyCodes);
        $strAllyCodes  = join(',', $allyCodes);
        $allyCodesHash = md5($strAllyCodes);
        $cacheFile     = sprintf(
            '%s/mods_%s',
            self::$CACHE_FOLDER,
            $allyCodesHash
        );

        $data = null;
        if ($this->existCacheFile($cacheFile)) {
            return Items::fromFile($cacheFile, [ 'decoder' => new ExtJsonDecoder(true) ]);
        } else {
            $chunks = ( count($allyCodes) > self::$MAX_CHUNK_SIZE )
                ? array_chunk($allyCodes, self::$MAX_CHUNK_SIZE)
                : [ $allyCodes ];

            $project  = (object)[
                'name'   => true,
                'roster' => [
                    'mods' => [
                        'pips'          => true,
                        'secondaryStat' => [
                            'unitStat' => true,
                            'value'    => true,
                        ],
                    ],
                ],
            ];
            $payloads = [];
            foreach ($chunks as $chunk) {
                $payloads[] = $this->client->fetchPlayer(join(',', $chunk), $this->lang, $project);
            }

            $data = $this->joinPayloads($payloads);
            $this->storeCache($cacheFile, $data);

            return Items::fromString($data, [ 'decoder' => new ExtJsonDecoder(true) ]);
        }
    }
    */

    private function playerFromCache(int $allyCode)
    {
        return $this->fromCache('player', $allyCode);
    }

    private function guildFromCache(int $allyCode)
    {
        return $this->fromCache('guild', $allyCode);
    }

    private function fromCache(string $type, int $allyCode)
    {
        $blobPattern = sprintf('./cache/%s_%s_*', $type, $allyCode);
        $pattern = sprintf('/%s_%s_(.*)/', $type, $allyCode);
        $now = new DateTimeImmutable();
        foreach (glob($blobPattern) as $filePath) {
            $filename = basename($filePath);
            $matches = [];
            if (preg_match($pattern, $filename, $matches)) {
                $timestamp = $matches[1];
                $timestampDate = (new DateTimeImmutable())->setTimestamp($timestamp);
                $stillValid = $timestampDate > $now;
                if ($stillValid) {
                    return file_get_contents($filePath);
                } else {
                    // ToDo: Move to fastcgi_finish_request()
                    unlink($filePath);
                }
            }
        }

        return null;
    }

    private function storePlayerCache(string $content, int $allyCode, int $updated)
    {
        $this->storeCache($content, 'player', strval($allyCode), $updated);
    }

    private function storeGuildCache(string $content, string $key, int $updated)
    {
        $this->storeCache($content, 'guild', $key, $updated);
    }

    private function storeCache(string $content, string $type, string $key, int $updated)
    {
        $ttl = new DateInterval('PT4H');
        $updatedDate = (new DateTimeImmutable())->setTimestamp($updated);
        $liveUntilDate = $updatedDate->add($ttl);
        $liveUntil = $liveUntilDate->getTimestamp();
        $filename = sprintf('./cache/%s_%s_%s', $type, $key, $liveUntil);
        if (!file_exists($filename)) {
            file_put_contents($filename, $content);
        }
    }

    private function joinPayloads(array $payloads)
    {
        return sprintf(
            '[%s]',
            join(
                ',',
                array_map(
                    function ($payload) {
                        return substr($payload, 1, -1);
                    },
                    $payloads
                )
            )
        );
    }
}
