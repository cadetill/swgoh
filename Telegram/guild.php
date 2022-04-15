<?php

use Im\Shared\Infrastructure\SwgohHelpRepository;

class TGuild extends TBase {
  private $subcomand;
  private $sortBy = "";
 
  /****************************************************
    constructor de la classe. Inicialitza variables
  ****************************************************/
  public function __construct($params, $dataObj) {
    parent::__construct($dataObj);
    $this->allyCode = '';
    
    // agafem el subcomando i l'extraem de $params
    $this->subcomand = explode(' ',trim($params[0]));
    $this->subcomand = $this->subcomand[1];
    unset($params[0]);
    
    // actuem segons la quantitat de paràmetres
    switch (count($params)) {
      case 0: 
        $this->allyCode = $dataObj->allycode;
        break;
		
      case 1: 
        $tmpStr = $params[1];
        if (!$this->checkAllyCode($tmpStr)) {
          $this->allyCode = $dataObj->allycode; 
          $this->sortBy = $params[1]; 
        }
        else {
          $this->allyCode = $tmpStr; 
        }
        break;
		
      case 2: 
        $tmpStr = $params[2];
        if ($this->checkAllyCode($tmpStr)) {
          $this->allyCode = $tmpStr; 
        }
        else {
          $this->error = $this->translatedText("error3", $params[2]); // $this->error = "The ".$params[2]." parameter is a bad AllyCode parameter. See help...\n\n";
          break;
        }
        $this->sortBy = $params[1]; 
        break;
        
      default:
        $this->error = $this->translatedText("error1"); // Bad request. See help: \n\n
    }
  }
  
  /****************************************************
    FUNCIONS PUBLIQUES
  ****************************************************/
  /****************************************************
    executa el subcomando
  ****************************************************/
  public function execCommand() {
    if ($this->error != "") {
      return $this->getHelp("guild", $this->error);
    }
  
    $initialTime = microtime(true);
    
    switch (strtolower($this->subcomand)) {
      case 'info':
        $res = $this->getInfo();
        break;
      case 'gp':
        $res = $this->getGp('gp');
        break;
      case 'roster':
        $res = $this->getRoster();
        break;
      case 'top80':
        $res = $this->getTop80();
        break;
      case 'ships':
        $res = $this->getGp('gpships');
        break;
      case 'chars':
        $res = $this->getGp('gpchars');
        break;
      case 'mods':
        $res = $this->getMods();
        break;
      case 'registered':
        $res = $this->getRegistered();
        break;
      default:
        return $this->getHelp("guild");
    }

    $finalTime = microtime(true);
    $time = $finalTime - $initialTime;
    $res .= $this->translatedText("elapsed_time", gmdate("H:i:s", $time));  // "<i>Elapsed time: ".gmdate("H:i:s", $time)."</i>\n";
    
    return $res;
  }
  
  /****************************************************
    FUNCIONS PRIVADES
  ****************************************************/
  /****************************************************
    mostra informació básica del gremi
  ****************************************************/
  private function getInfo() {
    $guild = $this->getInfoGuild();
    $players = $this->getInfoGuildExtra($guild);
    
    // mirem que haguem trobat Id Guild
    if ($guild[0]["id"] == "") {
      return $this->translatedText("error6");                                   // "Ooooops! API server may have shut down. Try again later.\n\n"
    }
    
    $arenachar = 0;
    $arenaships = 0;
    $gptotal = 0;
    $gpchar = 0;
    $gpships = 0;
    $countPlayers = 0;
    foreach ($players as $player) {
      $countPlayers++;
      $arenachar = $arenachar + $player["arena"]["char"]["rank"];
      $arenaships = $arenaships + $player["arena"]["ship"]["rank"];
      foreach ($player["roster"] as $unit) {
        $gptotal = $gptotal + $unit["gp"];
        if ($unit["combatType"] == 1) {
          $gpchar = $gpchar + $unit["gp"];
        }
        else {
          $gpships = $gpships + $unit["gp"];
        }
      }
    }
    $arenachar = $arenachar / $countPlayers;
    $arenaships = $arenaships / $countPlayers;
    
    $res = $this->translatedText("txtGuild01", $guild[0]["name"]);             // "<b>Guild</b>: ".$guild[0]["name"]."\n";
    $res .= $this->translatedText("txtGuild02", $guild[0]["members"]);          // "<b>Members</b>: ".$guild[0]["members"]."\n";
    $res .= $this->translatedText("txtGuild03", $guild[0]["message"]);          // "<b>Internal message</b>: ".$guild[0]["message"]."\n";
    $res .= "<b>----------------------------------------</b>\n";
    $res .= $this->translatedText("txtGuild04", $guild[0]["gp"]);               // "<b>GP InGame</b>: ".$guild[0]["gp"]."\n";
    $res .= $this->translatedText("txtGuild05", $gptotal);                      // "<b>GP Calculated</b>: ".$gptotal."\n";
    $res .= $this->translatedText("txtGuild06", $gpchar);                       // "<b>GP Characters</b>: ".$gpchar."\n";
    $res .= $this->translatedText("txtGuild07", $gpships);                      // "<b>GP Ships</b>: ".$gpships."\n";
    $res .= $this->translatedText("txtGuild08", intdiv($guild[0]["gp"], 50));   // "<b>GP Average</b>: ".intdiv($guild[0]["gp"], 50)."\n";
    $res .= "<b>----------------------------------------</b>\n";
    $res .= $this->translatedText("txtGuild09", $arenachar);                    // "<b>Average arena</b>: ".$arenachar."\n";
    $res .= $this->translatedText("txtGuild10", $arenaships);                   // "<b>Average ships</b>: ".$arenaships."\n\n";
    $res .= "\n";
    
    $res .= $this->translatedText("last_update", date("d-m-Y H:i:s", substr($guild[0]["updated"], 0, -3)));    // "<i>Last update: ".date("d-m-Y H:i:s", substr($guild[0]["updated"], 0, -3))."</i>\n\n";
        
    return $res;
  }
  
  /****************************************************
    mostra el PG dels membres del gremi
  ****************************************************/
  private function getGp($field) {
    $guild = $this->getInfoGuild();
    $players = $this->getInfoGuildExtra($guild);
    
    // mirem que haguem trobat Id Guild
    if ($guild[0]["id"] == "") {
      return $this->translatedText("error6");                                   // "Ooooops! API server may have shut down. Try again later.\n\n"
    }
    
    $arrGuild = array();
    $totalGP = 0;
    foreach ($players as $player) {
      $arrGuild[$player["name"]] = array();
      $this->processPlayer($player, $arrGuild[$player["name"]]);
      $arrGuild[$player["name"]]["name"] = $player["name"];
      $arrGuild[$player["name"]]["allyCode"] = $player["allyCode"];
      $totalGP = $totalGP + $arrGuild[$player["name"]][$field];
    }
    //print_r($arrGuild);
    // ordenem
    switch (strtolower($this->sortBy)) {
      case 'gp':
        usort($arrGuild, function($a, $b) use($field) {
          if ($a[$field] == $b[$field])
            return strtoupper($a['name']) <=> strtoupper($b['name']);
          return $a[$field] < $b[$field];
        });
        break;
      case 'allycode':
        usort($arrGuild, function($a, $b) {
          if ($a['allyCode'] == $b['allyCode'])
            return strtoupper($a['name']) <=> strtoupper($b['name']);
          return $a['allyCode'] < $b['allyCode'];
        });
        break;
      default:
        usort($arrGuild, function($a, $b) {
          return strtoupper($a['name']) <=> strtoupper($b['name']);
        });
        break;
    }

    $ret  = $this->translatedText("txtGuild01", $guild[0]["name"]);             // "<b>Guild</b>: ".$guild[0]["name"]."\n";
    $ret .= $this->translatedText("txtGuild02", $guild[0]["members"]);          // "<b>Members</b>: ".$guild[0]["members"]."\n";
    switch ($field) {
      case 'gp':
        $ret .= $this->translatedText("txtGuild11", number_format($totalGP, 0));    // "<b>GP</b>: ".number_format($totalGP, 0)."\n";
        break;
      case 'gpships':
        $ret .= $this->translatedText("txtGuild18", number_format($totalGP, 0));    // "<b>GP Ships</b>: ".number_format($totalGP, 0)."\n";
        break;
      case 'gpchars':
        $ret .= $this->translatedText("txtGuild19", number_format($totalGP, 0));    // "<b>GP Chars</b>: ".number_format($totalGP, 0)."\n";
        break;
    }
    $ret .= $this->translatedText("txtGuild12", number_format($totalGP / count($arrGuild), 2));   // "<b>Avg</b>: ".number_format($totalGP / count($arrGuild), 2)."\n";
    $ret .= "<b>----------------------------------------</b>\n";
    $ret .= "  gp|AllyCode\n";
    $ret .= "<pre>";
  
    foreach ($arrGuild as $data) {
      $ret .= str_pad($data[$field], 8, " ", STR_PAD_LEFT)."|".
              str_pad($data['allyCode'], 8, " ", STR_PAD_LEFT)." - ".
              $data['name']."\n";
    }
    $ret .= "</pre>";
    $ret .= "\n";
    
    return $ret;
  }
  
  /****************************************************
    mostra el roster dels membres del gremi
  ****************************************************/
  private function getRoster() {
    $guild = $this->getInfoGuild();
    $players = $this->getInfoGuildExtra($guild);
    
    // mirem que haguem trobat Id Guild
    if ($guild[0]["id"] == "") {
        return $this->translatedText("error6");
    }

    $arrGuild = array();
    $resume = array("r8" => 0, "r7" => 0, "g13" => 0, "g12" => 0, "g11" => 0);
    foreach ($players as $player) {
      $arrGuild[$player["name"]] = array();
      $this->processPlayer($player, $arrGuild[$player["name"]]);
      $arrGuild[$player["name"]]["name"] = $player["name"];
      $resume["g13"] = $resume["g13"] + $arrGuild[$player["name"]]["g13"];
      $resume["g12"] = $resume["g12"] + $arrGuild[$player["name"]]["g12"];
      $resume["g11"] = $resume["g11"] + $arrGuild[$player["name"]]["g11"];
      $resume["r8"] = $resume["r8"] + $arrGuild[$player["name"]]["r8"];
      $resume["r7"] = $resume["r7"] + $arrGuild[$player["name"]]["r7"];
    }
    
    // ordenem
    switch ($this->sortBy) {
      case 'g13':
        usort($arrGuild, function($a, $b) {
          if ($a['g13'] == $b['g13'])
            return strtoupper($a['name']) <=> strtoupper($b['name']);
          return $a['g13'] < $b['g13'];
        });
        break;
      case 'g12':
        usort($arrGuild, function($a, $b) {
          if ($a['g12'] == $b['g12'])
            return strtoupper($a['name']) <=> strtoupper($b['name']);
          return $a['g12'] < $b['g12'];
        });
        break;
      case 'g11':
        usort($arrGuild, function($a, $b) {
          if ($a['g11'] == $b['g11'])
            return strtoupper($a['name']) <=> strtoupper($b['name']);
          return $a['g11'] < $b['g11'];
        });
        break;
      case 'avg':
        usort($arrGuild, function($a, $b) {
          if ($a['g13vs12'] == $b['g13vs12'])
            return strtoupper($a['name']) <=> strtoupper($b['name']);
          return $a['g13vs12'] < $b['g13vs12'];
        });
        break;
      case 'r8':
        usort($arrGuild, function($a, $b) {
          if ($a['r8'] == $b['r8'])
            return strtoupper($a['name']) <=> strtoupper($b['name']);
          return $a['r8'] < $b['r8'];
        });
        break;
      case 'r7':
        usort($arrGuild, function($a, $b) {
          if ($a['r7'] == $b['r7'])
            return strtoupper($a['name']) <=> strtoupper($b['name']);
          return $a['r7'] < $b['r7'];
        });
        break;
      default:
        usort($arrGuild, function($a, $b) {
          return strtoupper($a['name']) <=> strtoupper($b['name']);
        });
        break;
    }

    $ret  = $this->translatedText("txtGuild01", $guild[0]["name"]);                  // "<b>Guild</b>: ".$guild[0]["name"]."\n";
    $ret .= $this->translatedText("txtGuild02", $guild[0]["members"]);               // "<b>Members</b>: ".$guild[0]["members"]."\n";
    $ret .= $this->translatedText("txtGuild13", number_format($resume["g13"], 0));   // "<b>G13</b>: ".number_format($resume["g13"], 0)."\n";
    $ret .= $this->translatedText("txtGuild14", number_format($resume["g12"], 0));   // "<b>G12</b>: ".number_format($resume["g12"], 0)."\n";
    $ret .= $this->translatedText("txtGuild15", number_format($resume["g11"], 0));   // "<b>G11</b>: ".number_format($resume["g11"], 0)."\n";
    $ret .= $this->translatedText("txtGuild16", number_format(($resume["g13"] * 100) / ($resume["g12"]+$resume["g11"]), 2));   // "<b>Avg</b>: ".number_format(($resume["g13"] * 100) / ($resume["g12"]+$resume["g11"]), 2)."\n";
    $ret .= $this->translatedText("txtGuild24", number_format($resume["r8"], 0));   // "<b>Relic 8</b>: %s\n";
    $ret .= $this->translatedText("txtGuild25", number_format($resume["r7"], 0));   // "<b>Relic 7</b>: %s\n";
    $ret .= "<b>----------------------------------------</b>\n";
    $ret .= "g13|g12|g11|avg|r8|r7\n";
    $ret .= "<pre>";
  
    foreach ($arrGuild as $data) {
      $ret .= str_pad($data['g13'], 3, " ", STR_PAD_LEFT)."|".
              str_pad($data['g12'], 3, " ", STR_PAD_LEFT)."|".
              str_pad($data['g11'], 3, " ", STR_PAD_LEFT)."|".
              str_pad($data['g13vs12'], 6, " ", STR_PAD_LEFT)."|".
              str_pad($data['r8'], 3, " ", STR_PAD_LEFT)."|".
              str_pad($data['r7'], 3, " ", STR_PAD_LEFT)." - ".
              $data['name']."\n";
    }
    $ret .= "</pre>";
    $ret .= "\n";
    
    return $ret;
  }
  
  /****************************************************
    mostra els mods dels membres del gremi
  ****************************************************/
  private function getMods() {
    // $repository = new SwgohHelpRepository($this->dataObj->swgohUser, $this->dataObj->swgohPass, $this->dataObj->language);
    $guild = $this->getInfoGuild();
    $allyCodes = array_column($guild[0]["roster"], 'allyCode');
    $players = $this->getInfoPlayers($allyCodes);

    // mirem que haguem trobat Id Guild
    if ($guild[0]["id"] == "") {
      return $this->translatedText("error6");                                   // "Ooooops! API server may have shut down. Try again later.\n\n"
    }
    
    $arrGuild = array();
    $resume = array("mods6" => 0, "mods25" => 0, "mods20" => 0, "mods15" => 0, "avg" => 0);
    foreach ($players as $player) {
        $playerData = ['name' => $player['name'], "mods6" => 0, "mods25" => 0, "mods20" => 0, "mods15" => 0];
        foreach ($player["roster"] as $unit) {
            foreach ($unit["mods"] as $mod) {
                if ($mod["pips"] == 6) {
                    $playerData["mods6"]++;
                }

                foreach ($mod["secondaryStat"] as $second) {
                    if ($second["unitStat"] == 5) {
                        $value = $second["value"];
                        switch ($value) {
                            case ($value >= 25):
                                $playerData["mods25"]++;
                                break;
                            case ($value >= 20):
                                $playerData["mods20"]++;
                                break;
                            case ($value >= 15):
                                $playerData["mods15"]++;
                                break;
                        }
                    }
                }
            }
        }
        $playerData["avg"] = number_format($playerData["mods6"] / ($playerData["mods25"] + $playerData["mods20"] + $playerData["mods15"]), 2);

        $resume['mods6'] += $playerData['mods6'];
        $resume['mods25'] += $playerData['mods25'];
        $resume['mods20'] += $playerData['mods20'];
        $resume['mods15'] += $playerData['mods15'];
        $arrGuild[] = $playerData;
    }
    
    // ordenem
    switch ($this->sortBy) {
      case 'm6':
        usort($arrGuild, function($a, $b) {
          if ($a['mods6'] == $b['mods6']) {
            return strtoupper($a['name']) <=> strtoupper($b['name']);
          }
          return $a['mods6'] < $b['mods6'];
        });
        break;
      case 'm25':
        usort($arrGuild, function($a, $b) {
          if ($a['mods25'] == $b['mods25']) {
            return strtoupper($a['name']) <=> strtoupper($b['name']);
          }
          return $a['mods25'] < $b['mods25'];
        });
        break;
      case 'm20':
        usort($arrGuild, function($a, $b) {
          if ($a['mods20'] == $b['mods20']) {
            return strtoupper($a['name']) <=> strtoupper($b['name']);
          }
          return $a['mods20'] < $b['mods20'];
        });
        break;
      case 'm15':
        usort($arrGuild, function($a, $b) {
          if ($a['mods15'] == $b['mods15']) {
            return strtoupper($a['name']) <=> strtoupper($b['name']);
          }
          return $a['mods15'] < $b['mods15'];
        });
        break;
      case 'avg':
        usort($arrGuild, function($a, $b) {
          if ($a['avg'] == $b['avg']) {
            return strtoupper($a['name']) <=> strtoupper($b['name']);
          }
          return $a['avg'] < $b['avg'];
        });
        break;
      default:
        usort($arrGuild, function($a, $b) {
          return strtoupper($a['name']) <=> strtoupper($b['name']);
        });
        break;
    }

    $ret  = $this->translatedText("txtGuild01", $guild[0]["name"]);                  // "<b>Guild</b>: ".$guild[0]["name"]."\n";
    $ret .= $this->translatedText("txtGuild02", $guild[0]["members"]);               // "<b>Members</b>: ".$guild[0]["members"]."\n";
    $ret .= $this->translatedText("txtGuild20", number_format($resume["mods6"], 0));   // "<b>G13</b>: ".number_format($resume["g13"], 0)."\n";
    $ret .= $this->translatedText("txtGuild21", number_format($resume["mods25"], 0));   // "<b>G12</b>: ".number_format($resume["g12"], 0)."\n";
    $ret .= $this->translatedText("txtGuild22", number_format($resume["mods20"], 0));   // "<b>G11</b>: ".number_format($resume["g11"], 0)."\n";
    $ret .= $this->translatedText("txtGuild23", number_format($resume["mods15"], 0));   // "<b>Avg</b>: ".number_format(($resume["g13"] * 100) / ($resume["g12"]+$resume["g11"]), 2)."\n";
    $ret .= "<b>----------------------------------------</b>\n";
    $ret .= "  m6|m25|m20|m15|avg\n";
    $ret .= "<pre>";

    foreach ($arrGuild as $data) {
      $ret .= str_pad($data['mods6'], 4, " ", STR_PAD_LEFT)."|".
              str_pad($data['mods25'], 3, " ", STR_PAD_LEFT)."|".
              str_pad($data['mods20'], 3, " ", STR_PAD_LEFT)."|".
              str_pad($data['mods15'], 3, " ", STR_PAD_LEFT)."|".
              str_pad($data['avg'], 3, " ", STR_PAD_LEFT)." - ".
              $data['name']."\n";
    }
    $ret .= "</pre>";
    $ret .= "\n";
    
    return $ret;      
  }
  
  /****************************************************
    mostra el top80 dels membres del gremi
  ****************************************************/
  private function getTop80() {
    $guild = $this->getInfoGuild();
    $players = $this->getInfoGuildExtra($guild);
    
    // mirem que haguem trobat Id Guild
    if ($guild[0]["id"] == "") {
      return $this->translatedText("error6");                                   // "Ooooops! API server may have shut down. Try again later.\n\n"
    }
    
    $arrGuild = array();
    $resume = array("top80" => 0);
    foreach ($players as $player) {
      $arrGuild[$player["name"]] = array();
      $this->processPlayer($player, $arrGuild[$player["name"]]);
      $arrGuild[$player["name"]]["name"] = $player["name"];
      $arrGuild[$player["name"]]["util"] = number_format($arrGuild[$player["name"]]["top80"] / $arrGuild[$player["name"]]["gp"], 2);
      $resume["top80"] = $resume["top80"] + $arrGuild[$player["name"]]["top80"];
    }
    
    // ordenem
    switch ($this->sortBy) {
      case 'gp':
        usort($arrGuild, function($a, $b) {
          if ($a['top80'] == $b['top80']) {
            return strtoupper($a['name']) <=> strtoupper($b['name']);
          }
          return $a['top80'] < $b['top80'];
        });
        break;
      case 'util':
        usort($arrGuild, function($a, $b) {
          if ($a['util'] == $b['util']) {
            return strtoupper($a['name']) <=> strtoupper($b['name']);
          }
          return $a['util'] < $b['util'];
        });
        break;
      default:
        usort($arrGuild, function($a, $b) {
          return strtoupper($a['name']) <=> strtoupper($b['name']);
        });
        break;
    }

    $ret  = $this->translatedText("txtGuild01", $guild[0]["name"]);                  // "<b>Guild</b>: ".$guild[0]["name"]."\n";
    $ret .= $this->translatedText("txtGuild02", $guild[0]["members"]);               // "<b>Members</b>: ".$guild[0]["members"]."\n";
    $ret .= $this->translatedText("txtGuild17", number_format($resume["top80"], 0));               // "<b>Top80</b>: ".number_format($resume["top80"], 0)."\n";
    $ret .= $this->translatedText("txtGuild16", number_format($resume["top80"] / count($arrGuild), 2));   // "<b>Avg</b>: ".number_format($resume["top80"] / count($arrGuild), 2)."\n";
    $ret .= "<b>----------------------------------------</b>\n";
    $ret .= "gp|util\n";
    $ret .= "<pre>";
  
    foreach ($arrGuild as $data) {
      $ret .= str_pad($data['top80'], 8, " ", STR_PAD_LEFT)."|".
              str_pad($data['util'], 5, " ", STR_PAD_LEFT)." - ".
              $data['name']."\n";
    }
    $ret .= "</pre>";
    $ret .= "\n";
    
    return $ret;
  }
  /****************************************************
    mostra informació básica del gremi
  ****************************************************/
  private function getRegistered() {
    $guild = $this->getInfoGuild();
    
    // mirem que haguem trobat Id Guild
    if ($guild[0]["id"] == "") {
      return $this->translatedText("error6");                                   // "Ooooops! API server may have shut down. Try again later.\n\n"
    }
          
    // conectem a la base de dades
    $idcon = new mysqli($this->dataObj->bdserver, $this->dataObj->bduser, $this->dataObj->bdpas, $this->dataObj->bdnamebd);
    if ($idcon->connect_error) 
      return $this->translatedText("error4");                                   // "Ooooops! An error has occurred getting data.\n\n";

    // generem string i array amb AllyCodes 
    $tmp = '';
    $members = array();
    foreach ($guild[0]['roster'] as $player) { 
      if ($tmp != '') $tmp .= ',';
      $tmp .= $player['allyCode'];
      $members[$player['allyCode']] = $player['name'];
    }
    
    // realitzem consulta
    $sql = "SELECT * FROM users where allycode in (".$tmp.")";
    $res = $idcon->query( $sql );
    if ($idcon->error) 
      return $this->translatedText("error4");                                   // "Ooooops! An error has occurred getting data.";

    // recorrem resultat per agafar usuaris existents
    $reg = array();
    while ($row = $res->fetch_assoc()) {
      $reg[$row['allycode']] = $row['name'];
    }
    
    // recorrem array 
    foreach ($reg as $key => $value) {
      if (array_key_exists($key, $members)) {
        $reg[$key] = $members[$key];
        unset($members[$key]);
      }
    }
    
    $ret  = $guild[0]['name'];
    $ret .= "\n\n";
    $ret .= $this->translatedText("txtGuild26");                                // "<b>Not Registered Members</b>\n";
    foreach ($members as $key => $value) {
      $ret .= $key . " - " . $value . "\n";
    }
    $ret .= "\n";
    $ret .= $this->translatedText("txtGuild27");                                // "<b>Registered Members</b>\n";
    foreach ($reg as $key => $value) {
      $ret .= $key . " - " . $value . "\n";
    }
    $ret .= "\n";
    
    return $ret;
  }
  
}
