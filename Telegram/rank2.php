<?php

class TRank2 extends TBase
{
    private const LEGENDS_POINTS  = [
        'GLREY'                => 100,
        'SUPREMELEADERKYLOREN' => 100,
        'GRANDMASTERLUKE'      => 100,
        'SITHPALPATINE'        => 100,
        'JEDIMASTERKENOBI'     => 100,
        'LORDVADER'            => 100,
    ];
    private const OMICRONS_TIERS  = [
        'uniqueskill_MARAJADE01'      => 8,
        'leaderskill_BOBAFETTSCION'   => 8,
        'uniqueskill_MACEWINDU02'     => 9,
        'uniqueskill_BOBAFETTSCION01' => 8,
        'leaderskill_PHASMA'          => 9,
    ];
    private const OMICRONS_POINTS = [
        'uniqueskill_MARAJADE01'      => 50,
        'leaderskill_BOBAFETTSCION'   => 75,
        'uniqueskill_MACEWINDU02'     => 50,
        'uniqueskill_BOBAFETTSCION01' => 50,
        'leaderskill_PHASMA'          => 50,
    ];

    private const CHARS_POINTS = [
        'STARKILLER' => 75,
    ];

    private const MODS_SIX_DOTS_MINIMUM        = 300;
    private const MODS_SIX_DOTS_MINIMUM_POINTS = 50;
    private const MODS_SIX_DOTS_CHUNK          = 100;
    private const MODS_SIX_DOTS_CHUNK_POINTS   = 10;
    private const R9_POINTS                    = 20;
    private const R8_POINTS                    = 10;
    private const GT_TEAM_POINTS               = 25;
    private const GA_RANK_POINTS               = 50;

    private array $allyCodes;

    /****************************************************
     * constructor de la classe. Inicialitza variables
     ****************************************************/
    public function __construct($params, $dataObj)
    {
        parent::__construct($dataObj);
        unset($params[0]);
        switch (count($params)) {
            case 0:
                $this->allyCodes = [ $dataObj->allycode ];
                $this->allyCode  = $dataObj->allycode;
                break;
            case 1:
                $this->allyCodes = [ $dataObj->allycode, ...explode(',', $params[1]) ];
                $this->allyCode  = $dataObj->allycode;
                break;
            case 2:
                if (!$this->checkAllyCode($params[2])) {
                    $this->error = $this->translatedText("error3", $params[2]); // "The %s isn't a correct AllyCode.\n";
                }

                $this->allyCodes = [ $params[2], ...explode(',', $params[1]) ];
                $this->allyCode  = $params[2];
                break;
            default:
                $this->error = $this->translatedText("error1"); // Bad request. See help: \n\n
        }
    }

    /****************************************************
     * FUNCIONS PUBLIQUES
     ****************************************************/
    /****************************************************
     * executa el subcomando
     ****************************************************/
    public function execCommand()
    {
        if ($this->error != "") {
            return $this->getHelp("rank", $this->error);
        }

        $initialTime = microtime(true);

        $res = $this->getRank();

        $finalTime = microtime(true);
        $time      = $finalTime - $initialTime;
        if (is_array($res)) {
            $res[count($res) - 1] .= $this->translatedText("elapsed_time", gmdate("H:i:s", $time));

            return $res;
        } else {
            $res .= $this->translatedText("elapsed_time", gmdate("H:i:s", $time));

            return [ $res ];
        }
    }

    /****************************************************
     * FUNCIONS PRIVADES
     ****************************************************/
    /****************************************************
     * agafa la informació d'un AllyCode
     ****************************************************/
    private function getRank(): string
    {
        $guildsInfo  = [];
        $guildsNames = [];
        foreach ($this->allyCodes as $allyCode) {
            $guild     = $this->getInfoGuild($allyCode);
            $guildName = $guild[0]['name'];
            if (in_array($guildName, $guildsNames)) {
                continue;
            }
            $guildsNames[]          = $guildName;
            $guildsInfo[$guildName] = $this->getInfoGuildExtra($guild);
        }

        $result = [];
        foreach ($guildsInfo as $guildName => $playersInfo) {
            foreach ($playersInfo as $playerInfo) {
                $result[] = array_merge([ 'guild' => $guildName ], $this->playerResult($playerInfo));
            }
        }
        usort($result, function ($item1, $item2) {
            return $item2['points'] <=> $item1['points'];
        });

        $response = '<b>Ranking para los gremios:</b> ' . implode(', ', $guildsNames) . "\n\n";
        $response .= $this->buildCsv('rank', $result, array_keys($result[0]));

        return $response;
    }

    private function playerResult($playerInfo): array
    {
        $captions                 = [
            'points',
            'legends',
            'tw_omicrons',
            'special_chars',
            'mods',
            'mods_extra',
            'r9',
            'r8',
            'tw_team',
            'gac',
        ];
        $playerReport             = array_fill_keys($captions, 0);
        $playerReport['name']     = $playerInfo['name'];
        $playerReport['allycode'] = $playerInfo['allyCode'];
        $totalSixDotsMods         = 0;

        // GAC
        $lastGac = $playerInfo['grandArena'][array_key_last($playerInfo['grandArena'])];
        // Kyber 1
        if ($lastGac['division'] === 25) {
            $playerReport['points'] += self::GA_RANK_POINTS;
            $playerReport['gac']    = 1;
        }

        foreach ($playerInfo['roster'] as $unit) {
            $unitId           = $unit['defId'];
            $relicCurrentTier = $unit['relic']['currentTier'] ?? 0;
            $unitRelicTier    = max(0, $relicCurrentTier - 2);
            $unitIsGeared     = $unitRelicTier >= 7;

            if ($unitRelicTier === 9) {
                $playerReport['points'] += self::R9_POINTS;
                $playerReport['r9']     += 1;
            }

            if ($unitRelicTier === 8) {
                $playerReport['points'] += self::R8_POINTS;
                $playerReport['r8']     += 1;
            }

            // Legends
            if (isset(self::LEGENDS_POINTS[$unitId]) && $unitIsGeared) {
                $playerReport['points']  += self::LEGENDS_POINTS[$unitId];
                $playerReport['legends'] += 1;
            }

            // Special Chars
            if (isset(self::CHARS_POINTS[$unitId]) && $unitIsGeared) {
                $playerReport['points']        += self::CHARS_POINTS[$unitId];
                $playerReport['special_chars'] += 1;
            }

            foreach ($unit['skills'] as $skill) {
                $skillId     = $skill['id'];
                $skillTier   = $skill['tier'];
                $isMaxed     = $skillTier === $skill['tiers'];
                $isTwOmicron = isset(self::OMICRONS_TIERS[$skillId]);
                // $isMaxed = $skillTier === self::OMICRONS_TIERS[$skillId] ?? PHP_INT_MAX;

                // TW Omicrons
                if ($isTwOmicron && $isMaxed) {
                    $playerReport['points']      += self::OMICRONS_POINTS[$skillId];
                    $playerReport['tw_omicrons'] += 1;
                }
            }

            foreach ($unit['mods'] as $mod) {
                if ($mod['pips'] === 6) {
                    $totalSixDotsMods++;
                }
            }
        }


        // Mods 6 dots
        if ($totalSixDotsMods > self::MODS_SIX_DOTS_MINIMUM) {
            $playerReport['points'] += self::MODS_SIX_DOTS_MINIMUM_POINTS;
            $playerReport['mods']   = 1;
        }
        $modsExtra                  = max(0, $totalSixDotsMods - self::MODS_SIX_DOTS_MINIMUM);
        $modsExtraChunks            = floor($modsExtra / self::MODS_SIX_DOTS_CHUNK);
        $playerReport['points']     += self::MODS_SIX_DOTS_CHUNK_POINTS * $modsExtraChunks;
        $playerReport['mods_extra'] = $modsExtraChunks;

        // TW Teams
        $stats            = $this->loadGuildRequirements($this->dataObj->guildId);
        $unitsToLoadStats = $stats->unitIds();

        if (count($unitsToLoadStats) > 0) {
            $playerRosterWithStats = $this->playerStats($playerInfo, $unitsToLoadStats);
            $results               = $stats->playerResult($playerRosterWithStats);

            foreach ($results as $result) {
                if ($result->complain()) {
                    $playerReport['points'] += self::GT_TEAM_POINTS;
                    $playerReport['tw_team']++;
                }
            }
        }

        return $playerReport;
    }

    private function buildCsv(string $prefix, array $data, array $headers = null): string
    {
        $tmpFileName = tempnam('./tmp/', $prefix . '_');
        $handle      = fopen($tmpFileName, 'w');

        if (!is_null($headers)) {
            fputcsv($handle, $headers);
        }

        foreach ($data as $datum) {
            fputcsv($handle, $datum);
        }

        fclose($handle);
        rename($tmpFileName, $tmpFileName . '.csv');
        $tmpFileName = $tmpFileName . '.csv';
        chmod($tmpFileName, 644);

        return 'https://www.cadetill.com/swgoh/bot/tmp/' . basename($tmpFileName);
    }
}  
