<?php

namespace Im\Shared\Infrastructure;

use DateTime;
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

    public function player(int $allyCode)
    {
        $cacheFile = sprintf(
            '%s/players_%s',
            self::$CACHE_FOLDER,
            $allyCode
        );

        $data = null;
        if ($this->existCacheFile($cacheFile)) {
            $data = $this->fromCache($cacheFile);
        } else {
            $data = $this->client->fetchPlayer($allyCode, $this->lang);
            $this->storeCache($cacheFile, $data);
        }

        return json_decode($data, true);
    }

    public function guild(int $allyCode)
    {
        $cacheFile = sprintf(
            '%s/guild_%s',
            self::$CACHE_FOLDER,
            $allyCode
        );

        $data = null;
        if ($this->existCacheFile($cacheFile)) {
            $data = $this->fromCache($cacheFile);

            return json_decode($data, true);
        } else {
            $data = $this->client->fetchGuild($allyCode, $this->lang);
            $this->storeCache($cacheFile, $data);
            $guild              = json_decode($data, true);
            $guildId            = $guild[0]["id"];
            $cacheFileByGuildId = sprintf(
                '%s/guild_%s',
                self::$CACHE_FOLDER,
                $guildId
            );
            $this->storeCache($cacheFileByGuildId, $data);

            return $guild;
        }
    }

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

    private function existCacheFile(string $cacheFile)
    {
        $this->deleteOlderFiles();

        if (file_exists($cacheFile)) {
            $fileTime = filemtime($cacheFile);

            $fecha = new DateTime();
            $fecha->modify('-3 hours');

            if ($fileTime > $fecha->getTimestamp()) {
                return true;
            }
        }

        return false;
    }

    private function deleteOlderFiles()
    {
        $folder = self::$CACHE_FOLDER;
        if ($handle = opendir($folder)) {
            while (false !== ( $file = readdir($handle) )) {
                if (is_dir($file)) {
                    continue;
                }

                if ($file === '.gitkeep') {
                    continue;
                }

                $fileLastModified = filemtime($folder . '/' . $file);
                if (( time() - $fileLastModified ) > self::$DEFAULT_CACHE_LIFETIME_SECONDS) {
                    unlink($folder . '/' . $file);
                }
            }
            closedir($handle);
        }
    }

    private function fromCache(string $cacheFile)
    {
        return file_get_contents($cacheFile);
    }

    private function storeCache(string $cacheFile, $data)
    {
        file_put_contents($cacheFile, $data);
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
