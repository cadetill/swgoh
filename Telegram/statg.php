<?php

class TStatg extends TBase
{
    private $unit;
    private $stat;
    private $threshold;
    private $validPre = [
        's',   // speed
        'hp',  // health+protection
        'h',   // health
        'p',   // protection
        'pd',  // physical damage
        'sd',  // special damage
        'po',  // potency
        't',   // tenacity
        'a',   // armor
        'pa',  // physical avoidance
        'sa',  // special avoidance
        'pcc', // physical critical chance
        'scc', // special critical chance
        'cd'   // critical damage
    ];

    const CRINOLO_STAT_ALIAS = [
        's'   => 'Speed',
        'hp'  => '',
        'h'   => 'Health',
        'p'   => 'Protection',
        'pd'  => 'Physical Damage',
        'sd'  => 'Special Damage',
        'po'  => 'Potency',
        't'   => 'Tenacity',
        'a'   => 'Armor',
        'pa'  => 'Physical Critical Avoidance',
        'sa'  => 'Special Critical Avoidance',
        'pcc' => 'Physical Critical Chance',
        'scc' => 'Special Critical Chance',
        'cd'  => 'Critical Damage',
    ];

    public function __construct($params, $dataObj)
    {
        parent::__construct($dataObj);
        $this->unit      = '';
        $this->stat      = '';
        $this->threshold = 0;

        // /statg +aliasUnit +aliasStat +threshold
        if (count($params) !== 4) {
            $this->error = $this->translatedText("error1");                         // Bad request. See help: \n\n
        }

        $this->unit      = $params[1];
        $this->stat      = $params[2];
        $this->threshold = $params[3];

        $this->allyCode = $dataObj->allycode;
    }

    public function execCommand()
    {
        $this->guard();

        $initialTime = microtime(true);

        $res = $this->execute();

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

    private function execute()
    {
        $defId      = @TAlias::aliasSearch($this->unit, $this->dataObj);
        $guildStats = $this->guildStats([ $defId ]);

        $statToCheck = self::CRINOLO_STAT_ALIAS[$this->stat];

        $toReport = [];
        foreach ($guildStats as $player) {
            foreach ($player['roster'] as $playerUnit) {
                $finalStats = $playerUnit['stats']['final'];
                $statValue  = $finalStats[$statToCheck];
                if ($statValue >= $this->threshold) {
                    continue;
                }
                $toReport[] = [
                    'playerName'   => $player['name'],
                    'allyCode'     => $player['allyCode'],
                    'currentValue' => $statValue
                ];
            }
        }

        $idcon = new mysqli($this->dataObj->bdserver, $this->dataObj->bduser, $this->dataObj->bdpas, $this->dataObj->bdnamebd);
        if ($idcon->connect_error) {
            return $this->translatedText("error4");
        }
        foreach ($toReport as $index => $record) {
            $sql  = "SELECT username FROM users where allycode = '".$record['allyCode']."' limit 1";
            $res = $idcon->query($sql);
            $row = $res->fetch_assoc();
            $record['telegramTag'] = $row['username'] ?? '';
            $toReport[$index] = $record;
        }

        $unitName = TUnits::unitNameFromUnitId($defId, $this->dataObj);
        $statName = $this->getDescHability($this->stat);
        $response = $this->translatedText("txtStatg1", [ $unitName, $statName, $this->threshold ]);
        $response .= "\n\n";

        foreach ($toReport as $record) {
            $response .= sprintf(
                "<code>%s: %s</code> @%s \n",
                $record['playerName'],
                $record['currentValue'],
                $record['telegramTag'] ?? ''
            );
        }

        $response .= "\n";

        return $response;
    }

    private function getDescHability($hab) {
        switch ($hab) {
            case 's' : return $this->translatedText("txtStats5");    // speed
            case 'hp': return $this->translatedText("txtStats6");    // health+protection
            case 'h' : return $this->translatedText("txtStats7");    // health
            case 'p' : return $this->translatedText("txtStats8");    // protection
            case 'pd': return $this->translatedText("txtStats9");    // physical damage
            case 'sd': return $this->translatedText("txtStats10");   // special damage
            case 'po': return $this->translatedText("txtStats11");   // potency
            case 't' : return $this->translatedText("txtStats12");   // tenacity
            case 'a' : return $this->translatedText("txtStats13");   // armor
            case 'pa': return $this->translatedText("txtStats14");   // physical avoidance
            case 'sa': return $this->translatedText("txtStats15");   // special avoidance
            case 'pcc': return $this->translatedText("txtStats17");  // physical critical chance
            case 'scc': return $this->translatedText("txtStats19");  // special critical chance
            case 'cd': return $this->translatedText("txtStats18");   // critical damage
        }
    }

    private function guard()
    {
        if ($this->error != "") {
            return $this->getHelp("stats", $this->error);
        }

        if (!in_array($this->stat, $this->validPre)) {
            return $this->translatedText("staterr3", $this->stat);
        }
    }
}
