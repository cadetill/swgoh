<?php
class TGF extends TBase {
  private $params = array();
  private $subcomand;
  private $alias;

  /****************************************************
    constructor de la classe. Inicialitza variables
  ****************************************************/
  public function __construct($params, $dataObj) {
    parent::__construct($dataObj);
    
    $this->allyCode = $dataObj->allycode;
     
    // agafem el subcomando i l'extraem de $params
    $this->subcomand = explode(' ', trim($params[0]));
    $this->subcomand = $this->subcomand[1];
    unset($params[0]);

    $this->params = $params;
  }
  
  /****************************************************
    FUNCIONS PUBLIQUES
  ****************************************************/
  /****************************************************
    executa el subcomando
  ****************************************************/
  public function execCommand() {
    $initialTime = microtime(true);

    switch ($this->subcomand) {
      case 'add':
        switch (count($this->params)) {
          case 1: $this->alias = $this->params[1]; break;
          default: return $this->getHelp("gf", $this->translatedText("error1")); // Bad request. See help: \n\n
        }
        $res = $this->add();
        break;
      case 'del':
        switch (count($this->params)) {
          case 1: $this->alias = $this->params[1]; break;
          default: return $this->getHelp("gf", $this->translatedText("error1")); // Bad request. See help: \n\n
        }
        $res = $this->del();
        break;
      case 'clear':
        $res = $this->clear();
        break;
      case 'list':
        $res = $this->list();
        break;
      case 'check':
        $res = $this->check();
        break;
      default:
        return $this->getHelp("gf", $this->translatedText("error1"));           // Bad request. See help: \n\n
    }

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
  /**************************************************************************
    afegeix una unitat a la llista
  **************************************************************************/
  private function add() {
    $units = new TUnits(array('units add', $this->alias, 'gf'), $this->dataObj);
    return $units->execCommand();
  }
  
  /**************************************************************************
    esborra una unitat a la llista
  **************************************************************************/
  private function del() {
    $units = new TUnits(array('units del', $this->alias, 'gf'), $this->dataObj);
    return $units->execCommand();
  }
  
  /**************************************************************************
    esborra tota la llista
  **************************************************************************/
  private function clear() {
    $units = new TUnits(array('units clear', '_', 'gf'), $this->dataObj);
    return $units->execCommand();
  }
  
  /**************************************************************************
    llista les unitats a comprovar 
  **************************************************************************/
  private function list() {
    $units = new TUnits(array('units listc', '_', 'gf'), $this->dataObj);
    return $units->execCommand();
  }
  
  /**************************************************************************
    fa la comprovació de les unitats
  **************************************************************************/
  private function check() {
    ini_set('memory_limit', '-1');
          
    // agafem info del gremi
    $guild = $this->getInfoGuild();
    $players = $this->getInfoGuildExtra($guild);
    
    // conectem a la base de dades
    $idcon = new mysqli($this->dataObj->bdserver, $this->dataObj->bduser, $this->dataObj->bdpas, $this->dataObj->bdnamebd);
    if ($idcon->connect_error) {
      return $this->translatedText("error4");                                   // "Ooooops! An error has occurred getting data.\n\n";
    }

    // mirem si hi ha error 
    if ($idcon->error) {
      return $this->translatedText("error4");                                   // $ret = "Ooooops! An error has occurred getting data.";
    }

    // agafem registre de l'equip "units"
    $sql = "SELECT * FROM teams WHERE guildRefId = '".$guild[0]["id"]."' and team = 'gf' ";
    $res = $idcon->query( $sql );

    // creem array de les unitats
    $row = $res->fetch_assoc();
    $units = explode(',', $row['units']);
    
    // tanquem connexió a la base de dades
    $idcon->close();
    
    // agafem informació general (primer missatge)
    $info = array();
    foreach ($players as $player) {
      $data = $this->iniPlayerArray();
      $this->processPlayer($player, $data);
      $info[$player["name"]] = array (
                                      'r7' => str_pad(intval($data['r7']), 3, " ", STR_PAD_LEFT),
                                      'relics' => str_pad(intval($data['relics']), 3, " ", STR_PAD_LEFT),
                                      'g13' => str_pad(intval($data['g13']), 3, " ", STR_PAD_LEFT),
                                      'mods6' => str_pad(intval($data['mods6']), 3, " ", STR_PAD_LEFT),
                                      'mods' => str_pad(intval(($data['mods10']+$data['mods15']+$data['mods20']+$data['mods25'])), 3, " ", STR_PAD_LEFT),
                                      'name' => $player["name"]
                                     );
    }

    
    // agafem informació de les unitats
    $uarr = array();
    foreach ($units as $unit) {
      $unitName = TUnits::unitNameFromUnitId($unit, $this->dataObj);
      $uarr[$unitName] = array(
                               'unitName' => $unitName,
                               'players' => array()
                              );
      
      // recorrem tots els jugadors
      foreach ($players as $player) {
        $uarr[$unitName]['players'][$player["name"]] = array(
                                                             'gear' => "---",
                                                             'zetas' => '',
                                                             'name' => $player["name"]
                                                            );
        // recorrem les unitats del jugador buscant la que toca
        foreach ($player["roster"] as $u) {
          if (strcasecmp($unit, $u["defId"]) != 0) {
            continue;
          }
          
          // gear
          if ($u["relic"]["currentTier"] != 1) {
            $uarr[$unitName]['players'][$player["name"]]['gear'] = str_pad('r'.intval($u["relic"]["currentTier"]-2), 3, " ", STR_PAD_LEFT);
          }
          else {
            $uarr[$unitName]['players'][$player["name"]]['gear'] = str_pad('g'.intval($u["gear"]), 3, " ", STR_PAD_LEFT);
          }
          
          // zetas
          usort($u['skills'], function($a, $b) {
            return $a['id'] <=> $b['id'];
          });
          foreach($u['skills'] as $skill) {
            if (!$skill["isZeta"]) {
              continue;
            }
      
            $uarr[$unitName]['players'][$player["name"]]['zetas'] .= " ";
      
            if ($skill["tier"] != $skill["tiers"]) {
              continue;
            }
      
            if (strpos($skill["id"], "unique") !== false) {
              $uarr[$unitName]['players'][$player["name"]]['zetas'][strlen($uarr[$unitName]['players'][$player["name"]]['zetas']) - 1] = 'u';
            }
            else {
              if (strpos($skill["id"], "leader") !== false) {
                $uarr[$unitName]['players'][$player["name"]]['zetas'][strlen($uarr[$unitName]['players'][$player["name"]]['zetas']) - 1] = 'l';
              }
              else {
                if (strpos($skill["id"], "special") !== false) {
                  $uarr[$unitName]['players'][$player["name"]]['zetas'][strlen($uarr[$unitName]['players'][$player["name"]]['zetas']) - 1] = 'e';
                }
                else {
                  $uarr[$unitName]['players'][$player["name"]]['zetas'][strlen($uarr[$unitName]['players'][$player["name"]]['zetas']) - 1] = 'b';
                }
              }
            }
          }  // foreach zetas

        } // foreach roster
      } // foreach players
    } // forearch units

    
    //echo "\n\n\n";print_r($uarr);echo "\n\n\n";
    
    // imprimim primer missatge
    usort($info, function($a, $b) {
       return strtoupper($a['name']) <=> strtoupper($b['name']);
    });
    $ret = array();
    $ret[0]  = $this->translatedText("txtGf01", $guild[0]["name"]);             // "<b>Guild</b>: ".$this->guild[0]["name"]."\n";
    $ret[0] .= $this->translatedText("txtGf02");                                // "General Information \n\n";
    $ret[0] .= "<pre>";
    $ret[0] .= " R7|Rel|G13|M6 |M10 \n";
    foreach ($info as $player) {
      $ret[0] .= $player['r7']."|".$player['relics']."|".$player['g13']."|".$player['mods6']."|".$player['mods']." - ".$player['name']."\n";
    }
    $ret[0] .= "</pre>";
    $ret[0] .= "\n";
    //echo "\n\n\n";print_r($uarr);echo "\n\n\n";
    // imprimim 1 missatge per unitat
    usort($uarr, function($a, $b) {
       return strtoupper($a['unitName']) <=> strtoupper($b['unitName']);
    });
    $pos = 0;
    foreach ($uarr as $unit) {
      $pos++;
      $ret[$pos]  = $this->translatedText("txtGf01", $guild[0]["name"]);        // "<b>Guild</b>: ".$this->guild[0]["name"]."\n";
      $ret[$pos] .= $unit['unitName']."\n\n";
      $ret[$pos] .= "<pre>";
      $ret[$pos] .= "gear|zetas \n";
      usort($unit['players'], function($a, $b) {
         return strtoupper($a['name']) <=> strtoupper($b['name']);
      });
      foreach ($unit['players'] as $player) {
        $ret[$pos] .= $player['gear'] . '|' . $player['zetas'] . ' - ' . $player['name'] . "\n";
      }
      $ret[$pos] .= "</pre>";
      $ret[$pos] .= "\n";
    }
    
    // retornem resultat
    return $ret;
  }
  
}
