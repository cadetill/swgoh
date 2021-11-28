<?php
class TRank extends TBase {
  private $stat = '';
  private $unit = '';
  private $stats = array('speed', 'hp', 'health', 'protection', 'physical', 'special', 'potency', 'tenacity', 'armor', 'phcrav', 'spcrav', 'gp', 'weighing', 'g13', 'mods6', 'mods10', 'relics');
    
  /****************************************************
    constructor de la classe. Inicialitza variables
  ****************************************************/
  public function __construct($params, $dataObj) {
    parent::__construct($dataObj);
        
    // agafem el subcomando i l'extraem de $params
    $this->stat = explode(' ',trim($params[0]));
    $this->stat = $this->stat[1];
    unset($params[0]);
    
    if (!in_array($this->stat, $this->stats))
      $this->error = $this->translatedText("error1"); // Bad request. See help: \n\n
    
    switch (count($params)) {
      case 0:
        $this->allyCode = $dataObj->allycode;
        break;
      case 1:
        $tmp = $params[1];
        if (!$this->checkAllyCode($tmp)) {
          $this->unit = $params[1];
          $this->allyCode = $dataObj->allycode;
        }
        else {
          $this->allyCode = $params[1];
        }
        break;
      case 2:
        if (!$this->checkAllyCode($params[2]))
          $this->error = $this->translatedText("error3", $params[2]); // "The %s isn't a correct AllyCode.\n";
      
        $this->unit = $params[1];
        $this->allyCode = $params[2];
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
      return $this->getHelp("rank", $this->error);
  
    $initialTime = microtime(true);
    
    $res = $this->getRank();

    $finalTime = microtime(true);
    $time = $finalTime - $initialTime;
    if (is_array($res)) {
      $res[count($res)-1] .= $this->translatedText("elapsed_time", gmdate("H:i:s", $time));
      return $res;
    } 
    else {
      $res .= $this->translatedText("elapsed_time", gmdate("H:i:s", $time));
      return array($res);
    }
  }
  
  /****************************************************
    FUNCIONS PRIVADES
  ****************************************************/
  /****************************************************
    agafa la informació d'un AllyCode
  ****************************************************/
  private function getRank() {
    // agafem ID de la unitat
    $unitId = TAlias::aliasSearch($this->unit, $this->dataObj);
    
    // agafem info del gremi
    $guild = $this->getInfoGuild();
    //$players = $this->getInfoGuildExtra($guild);
    
    // mirem que haguem trobat Id Guild
    if ($guild[0]["id"] == "")
      return $this->translatedText("error6");                                   // "Ooooops! API server may have shut down. Try again later.\n\n"
    
      
    // PART 4: recorrem jugadors buscant la unitat
    $result = array();
    $sum = 0;
    foreach ($players as $player) {
      switch (strtolower($this->stat)) {
        case "weighing":
        case "g13":
        case "mods6":
        case "mods10":
        case "relics":
          $data = $this->iniPlayerArray();
          $this->processPlayer($player, $data);
          switch (strtolower($this->stat)) {
            case "weighing": $result[$player["name"]] = number_format($data["g13vs12"], 2); $sum = $sum + $data["g13vs12"]; break;
            case "g13": $result[$player["name"]] = $data["g13"]; $sum = $sum + $data["g13"]; break;
            case "mods6": $result[$player["name"]] = $data["mods6"]; $sum = $sum + $data["mods6"]; break;
            case "mods10": $result[$player["name"]] = $data["mods10"]+$data["mods15"]+$data["mods20"]+$data["mods25"]; $sum = $sum + $data["mods10"]+$data["mods15"]+$data["mods20"]+$data["mods25"]; break;
            case "relics": $result[$player["name"]] = $data["relics"]; $sum = $sum + $data["relics"]; break;
          }
          break;
        default:
          foreach ($player["roster"] as $unit) {
            if ((strcasecmp($this->unit, $unit["nameKey"]) == 0) || (strcasecmp($unitId, $unit["defId"]) == 0)) {
              $nameUnit = $unit["nameKey"];
              switch (strtolower($this->stat)) {
                case "speed": $result[$player["name"]] = $unit["stats"]["final"]["Speed"]; $sum = $sum + $unit["stats"]["final"]["Speed"]; break;
                case "hp": $result[$player["name"]] = $unit["stats"]["final"]["Health"]+$unit["stats"]["final"]["Protection"]; $sum = $sum + $unit["stats"]["final"]["Health"]+$unit["stats"]["final"]["Protection"]; break;
                case "health": $result[$player["name"]] = $unit["stats"]["final"]["Health"]; $sum = $sum + $unit["stats"]["final"]["Health"]; break;
                case "protection": $result[$player["name"]] = $unit["stats"]["final"]["Protection"]; $sum = $sum + $unit["stats"]["final"]["Protection"]; break;
                case "physical": $result[$player["name"]] = $unit["stats"]["final"]["Physical Damage"]; $sum = $sum + $unit["stats"]["final"]["Physical Damage"]; break;
                case "special": $result[$player["name"]] = $unit["stats"]["final"]["Special Damage"]; $sum = $sum + $unit["stats"]["final"]["Special Damage"]; break;
                case "potency": $result[$player["name"]] = number_format($unit["stats"]["final"]["Potency"]*100, 2); $sum = $sum + $unit["stats"]["final"]["Potency"]; break;
                case "tenacity": $result[$player["name"]] = number_format($unit["stats"]["final"]["Tenacity"]*100, 2); $sum = $sum + $unit["stats"]["final"]["Tenacity"]; break;
                case "armor": $result[$player["name"]] = number_format($unit["stats"]["final"]["Armor"]*100, 2); $sum = $sum + $unit["stats"]["final"]["Armor"]; break;
                case "phcrav": $result[$player["name"]] = number_format($unit["stats"]["final"]["Physical Critical Avoidance"]*100, 2); $sum = $sum + $unit["stats"]["final"]["Physical Critical Avoidance"]; break;
                case "spcrav": $result[$player["name"]] = number_format($unit["stats"]["final"]["Special Critical Avoidance"]*100, 2); $sum = $sum + $unit["stats"]["final"]["Special Critical Avoidance"]; break;
                case "gp": $result[$player["name"]] = $unit["gp"]; $sum = $sum + $unit["gp"]; break;
              }
            }
          }
          break;
      }
    }
  
    // PART 5: bump del resultat
    $ret  = $this->translatedText("txtRank01", $guild[0]["name"]);                         // "<b>Guild</b>: ".$guild[0]["name"]."\n";
    $ret .= $this->translatedText("txtRank02", $nameUnit);                                 // "<b>Unit</b>: ".$nameUnit."\n";
    $ret .= $this->translatedText("txtRank03", ucfirst(strtolower($this->stat)));          // "<b>Sort by</b>: ".ucfirst(strtolower($this->stat))."\n";
    $ret .= $this->translatedText("txtRank04", number_format($sum/count($players), 2));    // "<b>Average</b>: ".number_format($sum/count($players), 2)."\n";
              
    $ret .= "<b>----------------------------</b>\n";
    arsort($result);
    foreach ($result as $key => $values) {
      $ret .= "<b>".$key."</b>: ".$values."\n";
    }
    $ret .= "\n";
              
    $ret .= $this->translatedText("last_update", date("d-m-Y H:i:s", substr($guild[0]["updated"], 0, -3)));    // "<i>Last update: ".date("d-m-Y H:i:s", substr($guild[0]["updated"], 0, -3))."</i>\n";

    return $ret;
  }  

}  
