<?php
class TSearch extends TBase {
  private $subcomand = "";
  private $sortBy = "name";
  private $unit = "";
  
  private $unitId;
  private $nameUnit;
  private $guild;
  private $players;

  /****************************************************
    constructor de la classe. Inicialitza variables
  ****************************************************/
  public function __construct($params, $dataObj, $searchType) {
    parent::__construct($dataObj);
        
    $this->subcomand = $searchType;
    unset($params[0]);
    
    switch (count($params)) {
      case 1:
        $this->unit = $params[1];
        $this->sortBy = "";
        $this->allyCode = $dataObj->allycode;
        break;
    
      case 2:
        $this->unit = $params[1];
        $this->sortBy = "";
          
        $tmpStr = $params[2];
        if ($this->checkAllyCode($tmpStr)) 
          $this->allyCode = $tmpStr; 
        else {
          $this->allyCode = $dataObj->allycode;
          $this->sortBy = $params[2];
        }
        break;
        
      case 3:
        $this->unit = $params[1];
        $this->sortBy = $params[2];

        $tmpStr = $params[3];
        if ($this->checkAllyCode($tmpStr)) 
          $this->allyCode = $tmpStr; 
        else 
          $this->error = $this->translatedText("error3", $params[3]); // $this->error = "The ".$params[2]." parameter is a bad AllyCode parameter. See help...\n\n";
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
    if ($this->error != "")
      switch (strtolower($this->subcomand)) {
        case 'search2': return $this->getHelp("search2", $this->error); 
        default: return $this->getHelp("search", $this->error);
      }
      
  
    $initialTime = microtime(true);
    
    switch (strtolower($this->subcomand)) {
      case 'search':
        $this->getData();
        // mirem que haguem trobat Id Guild
        if ($this->guild[0]["id"] == "")
          return $this->translatedText("error6");                               // "Ooooops! API server may have shut down. Try again later.\n\n"
        $res = $this->printSearch();
        break;
      case 'search2':
        $this->getData();
        // mirem que haguem trobat Id Guild
        if ($this->guild[0]["id"] == "")
          return $this->translatedText("error6");                               // "Ooooops! API server may have shut down. Try again later.\n\n"
        $res = $this->printSearch2();
        break;
      default:
        return $this->getHelp("search");
    }

    $finalTime = microtime(true);
    $time = $finalTime - $initialTime;
    if (is_array($res)) {
      $res[count($res)-1] .= $this->translatedText("elapsed_time", gmdate("H:i:s", $time));  // "<i>Elapsed time: ".gmdate("H:i:s", $time)."</i>\n";
    } 
    else {
      $res .= $this->translatedText("elapsed_time", gmdate("H:i:s", $time));    // "<i>Elapsed time: ".gmdate("H:i:s", $time)."</i>\n";
      $res = array($res);
    }
    $this->sendMessage($res);
  }
  
  /****************************************************
    FUNCIONS PRIVADES
  ****************************************************/
  /****************************************************
    calcula les dades de la recerca
  ****************************************************/
  private function getData() {
    // PART 0: busquem ID de la unitat demanada
    $this->unitId = TAlias::aliasSearch($this->unit, $this->dataObj);
    //echo "unitId: ". $this->unitId . "\n\n";
    $this->nameUnit = TUnits::unitNameFromUnitId($this->unitId, $this->dataObj);
      
    // PART 1: agafem info del gremi 
    $this->guild = $this->getInfoGuild();
    $this->players = $this->getInfoGuildExtra($this->guild);
  }
    
  /****************************************************
    dóna sortida al comando search
  ****************************************************/
  private function printSearch() {      
    $count = 0; 
    $gear = array();
    $stars = array();
    $zetas = array();
    $nohave = array();
    foreach ($this->players as $player) {
      $findUnit = false;
      foreach ($player["roster"] as $unit) {
        if (strcasecmp($this->unitId, $unit["defId"]) != 0) 
          continue;
      
        $findUnit = true;
        $count++;

        // gear
        $idx = 'g'.str_pad(intval($unit["gear"]), 2, "0", STR_PAD_LEFT);    //sprintf("%'.02d\n", intval($unit["gear"]));
        if ($unit["relic"]["currentTier"] != 1) 
          $idx .= 'r'.($unit["relic"]["currentTier"]-2);
        if (array_key_exists($idx, $gear)) 
          array_push($gear[$idx], $player["name"]);
        else 
          $gear[$idx] = array($player["name"]);

        // stars
        if (array_key_exists(intval($unit["rarity"]), $stars)) 
          array_push($stars[$unit["rarity"]], $player["name"]);
        else 
          $stars[$unit["rarity"]] = array($player["name"]);

        // zetas
        usort($unit['skills'], function($a, $b) {
          return $a['id'] <=> $b['id'];
        });
        foreach($unit['skills'] as $skill) {
          if (!$skill["isZeta"]) 
            continue;
      
          if ($skill["tier"] != $skill["tiers"]) 
            continue;
      
          $tmp = "";
          if (strpos($skill["id"], "unique") !== false) 
            $tmp = "(u)-".$skill["nameKey"];
          else {
            if (strpos($skill["id"], "leader") !== false) 
              $tmp = "(l)-".$skill["nameKey"];
            else {
              if (strpos($skill["id"], "special") !== false) 
                $tmp = "(e)-".$skill["nameKey"];
              else 
                $tmp = "(b)-".$skill["nameKey"];
            }
          }
          if (array_key_exists($tmp, $zetas)) 
            array_push($zetas[$tmp], $player["name"]);
          else 
            $zetas[$tmp] = array($player["name"]);		  
        }  // foreach zetas
      }  // foreach roster
      
      if (!$findUnit) {
        $nohave[] = $player["name"];
      }
    }  //foreach players

    // PART 5: bump del resultat
    $res = array();
    $pos = 0;
    $res[$pos]  = $this->translatedText("txtSearch01", $this->guild[0]["name"]);       // "<b>Guild</b>: ".$this->guild[0]["name"]."\n";
    $res[$pos] .= $this->translatedText("txtSearch02", $this->nameUnit);               // "<b>searching</b>: ".$this->nameUnit."\n";
    $res[$pos] .= $this->translatedText("txtSearch03", $count);                        // "<b>Total</b>: ".$count."\n\n";
      
    $res[$pos] .= $this->translatedText("txtSearch04");                                // "<b>---------- Stars ----------</b>\n";
    krsort($stars);
    foreach ($stars as $key => $values) {
      $tmp = "";
      foreach ($values as $val) {
        if ($tmp != "") $tmp .= ", ";
        $tmp .= $val;
      }
      $res[$pos] .= "<b>".$key."*</b>: ".count($values)." <pre>(".$tmp.")</pre>\n\n";
      
      if (strlen($res[$pos]) > $this->dataObj->maxChars) {
        $pos++;
      }
    }
      
    $res[$pos] .= $this->translatedText("txtSearch05");                                // "<b>---------- Gear ----------</b>\n";
    krsort($gear);
    foreach ($gear as $key => $values) {
      $tmp = "";
      foreach ($values as $val) {
        if ($tmp != "") $tmp .= ", ";
        $tmp .= $val;
      }
      $res[$pos] .= "<b>".$key."</b>: ".count($values)." <pre>(".$tmp.")</pre>\n\n";
      
      if (strlen($res[$pos]) > $this->dataObj->maxChars) {
        $pos++;
      }
    }
      
    $res[$pos] .= $this->translatedText("txtSearch06");                                // "<b>---------- Zetas ----------</b>\n";
    krsort($zetas);
    foreach ($zetas as $key => $values) {
      $tmp = "";
      foreach ($values as $val) {
        if ($tmp != "") $tmp .= ", ";
        $tmp .= $val;
      }
      $res[$pos] .= "<b>".$key."</b>: ".count($values)." <pre>(".$tmp.")</pre>\n\n";
      
      if (strlen($res[$pos]) > $this->dataObj->maxChars) {
        $pos++;
      }
    }
      
    usort($nohave, 'strcasecmp');
    $res[$pos] .= $this->translatedText("txtSearch07");                                // <b>------- Don't have it -----</b>\n";
    krsort($zetas);
    foreach ($nohave as $value) {
      $tmp = "";
      $res[$pos] .= $value."\n";
    }
    $res[$pos] .= "\n";

    $res[$pos] .= $this->translatedText("last_update", date("d-m-Y H:i:s", substr($this->guild[0]["updated"], 0, -3)));    // "<i>Last update: ".date("d-m-Y H:i:s", substr($this->guild[0]["updated"], 0, -3))."</i>\n";
    $res[$pos] .= "\n";
      
    return $res;      
  }
    
  /****************************************************
    dóna sortida al comando search2
  ****************************************************/
  private function printSearch2() {
    $count = 0; 
    $all = array();
//    echo 'player';
//    print_r($this->players);
//    echo 'fi player';
    foreach ($this->players as $player) {
      foreach ($player["roster"] as $unit) {
        if (strcasecmp($this->unitId, $unit["defId"]) != 0)
          continue;
      
        $count++;
        $all[$player["name"]] = array(
                                      'name' => $player["name"],
                                      'gear' => str_pad(intval($unit["gear"]), 2, "0", STR_PAD_LEFT),
                                      'relic' => 0,
                                      'gp' => $unit["gp"],
                                      'zetas' => ""
                                     );
        if ($unit["relic"]["currentTier"] != 1) 
          $all[$player["name"]]['relic'] = $unit["relic"]["currentTier"]-2;
      
        // zetas
        usort($unit['skills'], function($a, $b) {
          return $a['id'] <=> $b['id'];
        });
        foreach($unit['skills'] as $skill) {
          if (!$skill["isZeta"]) 
            continue;
      
          $all[$player["name"]]['zetas'] = $all[$player["name"]]['zetas']." ";
      
          if ($skill["tier"] != $skill["tiers"]) 
            continue;
      
          if (strpos($skill["id"], "unique") !== false) 
            $all[$player["name"]]['zetas'][strlen($all[$player["name"]]['zetas']) - 1] = 'u';
          else {
            if (strpos($skill["id"], "leader") !== false) 
              $all[$player["name"]]['zetas'][strlen($all[$player["name"]]['zetas']) - 1] = 'l';
            else {
              if (strpos($skill["id"], "special") !== false) 
                $all[$player["name"]]['zetas'][strlen($all[$player["name"]]['zetas']) - 1] = 'e';
              else 
                $all[$player["name"]]['zetas'][strlen($all[$player["name"]]['zetas']) - 1] = 'b';
            }
          }
        }  // foreach zetas
      }  // foreach roster
    }  //foreach players
      
    // ordenem resultat segons es vulgui
    switch ($this->sortBy) {
      case 'gear':
        usort($all, function($a, $b) {
          if ($a['gear'] == $b['gear'])
            return strtoupper($a['name']) <=> strtoupper($b['name']);
          return $a['gear'] < $b['gear'];
        });
        break;
      case 'relics':
        usort($all, function($a, $b) {
          if ($a['relic'] == $b['relic'])
            return strtoupper($a['gear']) < strtoupper($b['gear']);
          return $a['relic'] < $b['relic'];
        });
        break;
      case 'gp':
        usort($all, function($a, $b) {
          if ($a['gp'] == $b['gp'])
            return strtoupper($a['name']) <=> strtoupper($b['name']);
          return $a['gp'] < $b['gp'];
        });
        break;
      case 'zetas':
        usort($all, function($a, $b) {
          if ($a['zetas'] == $b['zetas'])
            return strtoupper($a['name']) <=> strtoupper($b['name']);
          return $a['zetas'] < $b['zetas'];
        });
        break;
      default:
        usort($all, function($a, $b) {
          return strtoupper($a['name']) <=> strtoupper($b['name']);
        });
        break;
    }
    
    uksort($all, 'strnatcasecmp');
    
    // PART 5: bump del resultat
    $res  = $this->translatedText("txtSearch01", $this->guild[0]["name"]);       // "<b>Guild</b>: ".$this->guild[0]["name"]."\n";
    $res .= $this->translatedText("txtSearch02", $this->nameUnit);               // "<b>searching</b>: ".$this->nameUnit."\n";
    $res .= $this->translatedText("txtSearch03", $count);                        // "<b>Total</b>: ".$count."\n\n";
    
    $res .= "gear|relics|gp|zetas \n";
    $res .= "<pre>";
    foreach ($all as $values) {
      $res .= $values['gear'] . "|" . $values['relic'] . "|" . str_pad($values['gp'], 5, "0", STR_PAD_LEFT) . "|" . $values['zetas'] . " - " . $values['name'] . "\n";
    }
    $res .= "</pre>";
    $res .= "\n";
    $res .= $this->translatedText("last_update", date("d-m-Y H:i:s", substr($this->guild[0]["updated"], 0, -3)));    // "<i>Last update: ".date("d-m-Y H:i:s", substr($this->guild[0]["updated"], 0, -3))."</i>\n";
    $res .= "\n";
    
    return $res;
  }
    
}
