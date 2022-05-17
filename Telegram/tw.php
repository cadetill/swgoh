<?php

use Im\Commands\Tw\Shared\Domain\GuildRequirements;
use Im\Commands\Tw\Shared\Domain\RequirementCollection;
use Im\Shared\Exception\ImException;
use Im\Shared\Infrastructure\ApiUnitRepository;
use Im\Shared\Infrastructure\MemoryStatService;

class TTW extends TBase {
  private $params = array();
  private $subcomand;
  private $alias = "";
  private $alias2 = "";
  private $sort = "name";
  private $points = 0;
  private $date = "";
  private $all = false;

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

    $normalCacheSubcommands = [ 'check', 'checkg' ];
    if (!in_array($this->subcomand, $normalCacheSubcommands)) {
        $this->configCache('tw', new DateInterval('P3D'));
    }

    switch ($this->subcomand) {
      case 'new':
      case 'start':
        $res = $this->initializeTW();
        break;
      case 'def':
        $this->alias = $this->params[1];
        switch (count($this->params)) {
          case 1: break;
          case 2: $this->allyCode = $this->params[2]; break;
          default: return $this->getHelp("tw", $this->translatedText("error1"));  // Bad request. See help: \n\n
        }
        $res = $this->add('def');
        break;                                                 
      case 'off':                                
        $this->alias = $this->params[1];
        $this->points = $this->params[2];
        $this->alias2 = $this->params[3];
        switch (count($this->params)) {
          case 3: break;
          case 4: $this->allyCode = $this->params[4]; break;
          default: return $this->getHelp("tw", $this->translatedText("error1"));  // Bad request. See help: \n\n
        }
        $res = $this->add('off');
        break;
      case 'used':
        $this->alias = $this->params[1];
        switch (count($this->params)) {
          case 1: break;
          case 2: $this->allyCode = $this->params[2]; break;
          default: return $this->getHelp("tw", $this->translatedText("error1"));  // Bad request. See help: \n\n
        }
        $res = $this->add('used');
        break;                                                 
      case 'search':
        $this->alias = $this->params[1];
        switch (count($this->params)) {
          case 1: break;
          case 2: $this->allyCode = $this->params[2]; break;
          default: return $this->getHelp("tw", $this->translatedText("error1"));  // Bad request. See help: \n\n
        }
//        if (count($this->params) != 1) 
//          return $this->getHelp("tw", $this->translatedText("error1"));  // Bad request. See help: \n\n
        $res = $this->search();
        break;
      case 'del':
        $this->alias = $this->params[1];
        switch (count($this->params)) {
          case 1: break;
          case 2: $this->allyCode = $this->params[4]; break;
          default: return $this->getHelp("tw", $this->translatedText("error1"));  // Bad request. See help: \n\n
        }
        $res = $this->del();
        break;
      case 'me':
        switch (count($this->params)) {
          case 0: break;
          case 1: $this->allyCode = $this->params[1]; break;
          default: return $this->getHelp("tw", $this->translatedText("error1"));  // Bad request. See help: \n\n
        }
        $res = $this->me();
        break;
      case 'all':
        if (count($this->params) > 0)
          return $this->getHelp("tw", $this->translatedText("error1"));  // Bad request. See help: \n\n
        $res = $this->all();
        break;
      case 'rogue':
        switch (count($this->params)) {
          case 0: break;
          case 1: $this->allyCode = $this->params[1]; break;
          default: return $this->getHelp("tw", $this->translatedText("error1"));  // Bad request. See help: \n\n
        }
        $res = $this->add('rogue');
        break;
      case 'roguelist':
        if (count($this->params) > 0)
          return $this->getHelp("tw", $this->translatedText("error1"));  // Bad request. See help: \n\n
        $res = $this->rogueList();
        break;
      case 'estampometro':
      case 'review':
        if (count($this->params) > 0)
          return $this->getHelp("tw", $this->translatedText("error1"));  // Bad request. See help: \n\n
        $res = $this->estampometro();
        break;
      case 'attacks':
      case 'rogue':
        switch (count($this->params)) {
          case 0: break;
          case 1: $this->sort = $this->params[1]; break;
          default: return $this->getHelp("tw", $this->translatedText("error1"));  // Bad request. See help: \n\n
        }
        $res = $this->attacks();
        break;
      case 'save':
        $this->date = $this->params[1];
        if (count($this->params) != 1) 
          return $this->getHelp("tw", $this->translatedText("error1"));  // Bad request. See help: \n\n
        $res = $this->save();
        break;
      case 'delh':
        $this->date = $this->params[1];
        if (count($this->params) != 1) 
          return $this->getHelp("tw", $this->translatedText("error1"));  // Bad request. See help: \n\n
        $res = $this->deleteH();
        break;
      case 'listh':
        switch (count($this->params)) {
          case 0: break;
          case 1: $this->date = $this->params[1]; break;
          default: return $this->getHelp("tw", $this->translatedText("error1"));  // Bad request. See help: \n\n
        }
        $res = $this->listH();
        break; 
      case 'history':
        $this->all = false;
        $this->sort = 'name';
        switch (count($this->params)) {
          case 0: break;
          case 1:
            if (strcasecmp($this->params[1], "all") == 0)
              $this->all = true;
            else
              $this->sort = $this->params[1];
            break;
          case 2:
            $this->all = true;
            $this->sort = $this->params[2];
            if (strcasecmp($this->params[1], "all") != 0) 
              return $this->getHelp("tw", $this->translatedText("error1"));  // Bad request. See help: \n\n
            break;
          default: return $this->getHelp("tw", $this->translatedText("error1"));  // Bad request. See help: \n\n
        }
        $res = $this->history();
        break; 
      case 'defenses':
        switch (count($this->params)) {
          case 0: break;
          case 1: $this->sort = $this->params[1]; break;
          default: return $this->getHelp("tw", $this->translatedText("error1"));  // Bad request. See help: \n\n
        }
            $res = $this->defenses();
        break;
      case 'dates':
        switch (count($this->params)) {
          case 0: break;
          case 1: $this->sort = $this->params[1]; break;
          default: return $this->getHelp("tw", $this->translatedText("error1"));  // Bad request. See help: \n\n
        }
        $res = $this->dates();
        break; 
      case 'noreg':
        $res = $this->noreg();
        break;
      case 'check':
          $defaults = [ null, null, null, null, null ];
          [ $_, $first, $second, $third, $fourth ] = $this->params + $defaults;
          switch ($first) {
              case null:
                  // /tw check
                  if (!is_null($second) || !is_null($third) || !is_null($fourth)) {
                      return $this->getHelp("twcheck", $this->translatedText("error1"));
                  }
                  $res = $this->check();
                  break;
              case $this->isAllyCode($first):
                  // /tw check +allyCode
                  if (!is_null($second) || !is_null($third) || !is_null($fourth)) {
                      return $this->getHelp("twcheck", $this->translatedText("error1"));
                  }
                  $res = $this->check(null, $first);
                  break;
              case 'show':
                  switch (count($this->params)) {
                      case 1:
                          // /tw check +show
                          $res = $this->checkShow();
                          break;
                      case 2:
                          // /tw check +show +allyCode
                          // /tw check +show +teamAlias
                          if ($this->isAllyCode($second)) {
                              $res = $this->checkShow(null, $second);
                          } else {
                              $res = $this->checkShow($second);
                          }
                          break;
                      case 3:
                          // /tw check +show +teamAlias +allyCode
                          if (!$this->isAllyCode($third)) {
                              return $this->getHelp("twcheck", $this->translatedText("error1"));
                          }
                          $res = $this->checkShow($second, $third);
                          break;
                  }
                  break;
              case 'del':
                  // /tw check +del +teamAlias
                  // /tw check +del +teamAlias +allyCode
                  if (is_null($second)) {
                      return $this->getHelp("twcheck", $this->translatedText("error1"));
                  }
                  if (!$this->isAllyCode($third)) {
                      return $this->getHelp("twcheck", $this->translatedText("error1"));
                  }
                  $res = $this->checkDel($second, $third);
                  break;
              case 'update':
              case 'add':
                  /*
                    /tw check +update +teamAlias +definition
                    /tw check +update +teamAlias +definition +allyCode
                    /tw check +add    +teamAlias +definition
                    /tw check +add    +teamAlias +definition +allyCode
                  */
                  // return [ print_r([ $_, $first, $second, $third, $fourth ], true), print_r($this->params, true), $this->isAllyCode($first) ];
                  if (is_null($second) || is_null($third)) {
                      return $this->getHelp("twcheck", $this->translatedText("error1"));
                  }
                  if (!is_null($fourth) && !$this->isAllyCode($fourth)) {
                      return $this->getHelp("twcheck", $this->translatedText("error1"));
                  }
                  $res = $this->checkSave($second, $third, $fourth);
                  break;
              case 'pending':
                  /*
                    /tw check +pending
                    /tw check +pending +allyCode
                   */
                  if (!is_null($third) || !is_null($fourth)) {
                      return $this->getHelp("twcheck", $this->translatedText("error1"));
                  }
                  $res = $this->check(null, $second, true);
                  break;
              default:
                  $res = $this->check($first);
                  break;
          }
          break;
      case 'checkg':
          $res = $this->checkg();
          break;
      default:
        return $this->getHelp("tw", $this->translatedText("error1"));
    }

    $finalTime = microtime(true);
    $time = $finalTime - $initialTime;
    if (is_array($res)) {
      $res[count($res)-1] .= $this->translatedText("elapsed_time", gmdate("H:i:s", $time));
      return $res;
    } 
    else {
      $res .= $this->translatedText("elapsed_time", gmdate("H:i:s", intval($time)));
      return array($res);
    }
  }
  
  /****************************************************
    FUNCIONS PRIVADES
  ****************************************************/
  /**************************************************************************
    inicialitza una TW per un gremi donat
  **************************************************************************/
  private function initializeTW() {
    $player = $this->getInfoPlayer();
          
    // conectem a la base de dades
    $idcon = new mysqli($this->dataObj->bdserver, $this->dataObj->bduser, $this->dataObj->bdpas, $this->dataObj->bdnamebd);
    if ($idcon->connect_error) 
      return $this->translatedText("error4");      // "Ooooops! An error has occurred getting data.\n\n";
          
    // esborrem posible contingut del gremi
    $sql = "delete from tw where guildRefId = '".$player[0]["guildRefId"]."'";
    $idcon->query( $sql );
    $sql = "delete from twconf where guildRefId = '".$player[0]["guildRefId"]."'";
    $idcon->query( $sql );
    if ($idcon->error) {
        return $this->translatedText("error4");      // "Ooooops! An error has occurred getting data.";
    } else {
        $guild = $this->getInfoGuild();
        $this->getInfoGuildExtra($guild);
        return $this->translatedText("txtTw01", $player[0]["guildName"]);      // "TW for ".$player[0]["guildName"]." has been initialized\n\n";
    }
    $idcon->close();
  }

  /**************************************************************************
    afegeix una defensa, atac o rogue falsa
  **************************************************************************/
  private function add($unittype) {
    if (!$this->checkAllyCode($this->allyCode))
      return $this->getHelp("tw", $this->translatedText("error3", $this->allyCode));  // $this->error = "The ".$params[2]." parameter is a bad AllyCode parameter. See help...\n\n"; 
    
    // agafem ID de la unitat 
    if ($unittype == "rogue") 
      $unitId = $unittype;
    else {
      $unitId = TAlias::aliasSearch($this->alias, $this->dataObj);
      $vsId = TAlias::aliasSearch($this->alias2, $this->dataObj);
    }
    if ($unitId == "")
      return $this->translatedText("twerr1", $this->alias); // "Unit <i>".$unit."</i> not found.\n\n";
    if (($vsId == "") && ($unittype == "off"))
      return $this->translatedText("twerr1", $this->alias2); // "Unit <i>'".$vs."'</i> not found.\n\n";
          
    $player = $this->getInfoPlayer();
    
    // mirem que haguem trobat Id Guild
    if ($player[0]["guildRefId"] == "")
      return $this->translatedText("error6");                                   // "Ooooops! API server may have shut down. Try again later.\n\n"
          
    // conectem a la base de dades
    $idcon = new mysqli($this->dataObj->bdserver, $this->dataObj->bduser, $this->dataObj->bdpas, $this->dataObj->bdnamebd);
    if ($idcon->connect_error) 
      return $this->translatedText("error4");                                   // "Ooooops! An error has occurred getting data.\n\n";
    
    $pupdate = $this->points;
    if ($unittype == "rogue")
      $pupdate = $this->points+1;
          
    // afegim registre
    $sql  = "INSERT INTO tw (guildRefId, allyCode, unit, name, points, vs, unittype, datectrl) ";
    $sql .= "VALUES ('".$player[0]["guildRefId"]."', '".$player[0]["allyCode"]."', '".$unitId."', '".$player[0]["name"]."', ".$pupdate.", '".$vsId."', '".$unittype."', now()) ";
    if ($unittype == "rogue") {
      $sql .= "ON DUPLICATE KEY UPDATE points=points+1, unittype='".$unittype."', vs='".$vsId."'";  
    }
    else {
      $sql .= "ON DUPLICATE KEY UPDATE points=".$pupdate.", unittype='".$unittype."', vs='".$vsId."', datectrl=now()";  
    }
      //echo "\n\n sql: ".$sql."\n\n";
          
    $idcon->query( $sql );
    if ($idcon->error) 
      return $this->translatedText("error4");                                   // $ret = "Ooooops! An error has occurred getting data.";

    $ret  = $this->translatedText("txtTw02", $player[0]["guildName"]);          // "TW updated for ".$player[0]["guildName"]."\n\n";
    $ret .= $this->translatedText("txtTw03", $player[0]["name"]);               // "  Player: ".$player[0]["name"]."\n";
    switch ($unittype) {
      case 'off':
        $ret .= $this->translatedText("txtTw04", TUnits::unitNameFromUnitId($unitId, $this->dataObj));               // "  Offensive unit: ". $this->alias."\n";
        $ret .= $this->translatedText("txtTw05", TUnits::unitNameFromUnitId($vsId, $this->dataObj));              // "  Defensive unit: ". $this->alias2."\n";
        break;
      case 'def':
        $ret .= $this->translatedText("txtTw05", TUnits::unitNameFromUnitId($unitId, $this->dataObj));               // "  Defensive unit: ".$this->alias."\n";
        break;
      case 'used':
        $ret .= $this->translatedText("txtTw40", TUnits::unitNameFromUnitId($unitId, $this->dataObj));               // "  Used unit: ".$this->alias."\n";
        break;
    }
    $ret .= $this->translatedText("txtTw06", $unittype);                      // "  Type: ".$unittype."\n\n";
    $ret .= "\n";
    
    $idcon->close(); 
      
    return $ret;
  }

  /**************************************************************************
    retorna qui ha utilitat una unitat
  **************************************************************************/
  private function search() {
    ini_set('memory_limit', '-1');
    
    if (!$this->checkAllyCode($this->allyCode)) {
      return $this->getHelp("tw", $this->translatedText("error3", $this->allyCode));  // $this->error = "The ".$params[2]." parameter is a bad AllyCode parameter. See help...\n\n"; 
    }

    // agafem ID de la unitat (llista de alias o llista d'unitats)
    if ($this->alias == "rogue") {
      $unitId = $this->alias;
    }
    else {
      $unitId = TAlias::aliasSearch($this->alias, $this->dataObj);
    }

    // si la unitat no la trobem, sortim amb error
    if ($unitId == "") {
      return $this->translatedText("twerr1", $this->alias);                     // "Unit <i>%s</i> not found.\n\n";
    }
        
    // agafem punts segons sigue naus o terra
    if (TUnits::isAShip($unitId, $this->dataObj)) {
      $pointsBattle = 22;
    }
    else {
      $pointsBattle = 20;
    }
    
    // agafem info del gremi
    $playersInfo = $this->getInfoGuildExtra();

    $isRogueMode = $unitId == "rogue";

    $players = [];
    $guildRefId = null;
    $guildName = null;
    foreach ($playersInfo as $player) {
        $guildRefId = $guildRefId ?: $player['guildRefId'];
        $guildName  = $guildName ?: $player['guildName'];

        $playerUnitIds = array_column($player['roster'], 'defId');
        $hasUnit       = in_array($unitId, $playerUnitIds);
        if (!$isRogueMode && !$hasUnit) {
            continue;
        }

        $playerData             = [];
        $playerData['name']     = $player['name'];
        $playerData['allyCode'] = $player['allyCode'];

        $players[$player['allyCode']] = $playerData;
    }
    
    // mirem que haguem trobat Id Guild  
    if ($guildRefId == "") {
      return $this->translatedText("error6");                                   // "Ooooops! API server may have shut down. Try again later.\n\n"
    }

    // busquem no inscrits
    $arrNoReg = $this->getNoReg();
    foreach ($arrNoReg as $noRegAllyCode) {
        unset($players[$noRegAllyCode]);
    }
    
    // conectem a la base de dades
    $idcon = new mysqli($this->dataObj->bdserver, $this->dataObj->bduser, $this->dataObj->bdpas, $this->dataObj->bdnamebd);
    if ($idcon->connect_error) {
      return $this->translatedText("error4");                                   // return "Ooooops! An error has occurred getting data.\n\n";
    }

    // busquem registres
    $sql  = "SELECT * FROM tw where guildRefId = '".$guildRefId."' and unit = '".$unitId."'";
    $res = $idcon->query( $sql );
    $sumOff = 0;
    if ($idcon->error) {
      return $this->translatedText("error4");                                   // $ret = "Ooooops! An error has occurred getting data.";
    }
    
    // inicialitzem arrays necessaris per a la impressió 
    $arrAtt = array();
    $arrDef = array();
    $arrUsed = array();
    $arrRogue = array();

    $allyCodeThatUsed = [];
    
    // recorrem registres omplin arrays
    while ($row = $res->fetch_assoc()) {
      $player = $players[$row['allyCode']] ?? null;
      if (!$player) {
          continue;
      }
      switch ($row['unittype']) {
        case 'def':
          $arrDef[] = $player['name'] . '-' . $player['allyCode'];
          break;
        case 'used':
          $arrUsed[] = $player['name'] . '-' . $player['allyCode'];
          break;
        case 'off':
          $arrAtt[] = $player['name'] . '-' . $player['allyCode'] . ' (' . $row['points'] . ')';
          $sumOff   = $sumOff + $row['points'];
          break;
        case 'rogue':
          $arrRogue[] = $player['name'] . '-' . $player['allyCode'] . ' (' . $row['points'] . ')';
          break;
      }
      $allyCodeThatUsed[] = $player['allyCode'];
    }
    
    $arrNo = array();

    foreach ($players as $player) {
      if (!in_array($player['allyCode'], $allyCodeThatUsed)) {
        $sql  = "SELECT * FROM users where allycode = '".$player['allyCode']."' limit 1";
        $res = $idcon->query( $sql );
        $row = $res->fetch_assoc() ?? [];
        $arrNo[] = '<code>' . $player['name'] . '-' . $player['allyCode'] . '</code> @' . ( $row['username'] ?? '' );
      }
    }
    
    $idcon->close(); 
                
    usort($arrAtt, 'strnatcasecmp');
    usort($arrDef, 'strnatcasecmp');
    usort($arrUsed, 'strnatcasecmp');
    usort($arrNo, 'strnatcasecmp');    
    usort($arrRogue, 'strnatcasecmp');

    $ret  = $this->translatedText("txtTw07", $guildName);         // "<b>Guild</b>: ".$players[0]["name"]."\n";
    if ($this->alias != "rogue") {
      $ret .= $this->translatedText("txtTw08", TUnits::unitNameFromUnitId($unitId, $this->dataObj));  // "<b>Unit</b>: ".$this->alias."\n";
      $ret .= $this->translatedText("txtTw14", $this->alias);                   // "<b>Alias</b>: ".$this->alias."\n";
      $ret .= $this->translatedText("txtTw09", $unitId);                        // "<b>unitId</b>: ".$unitId."\n";
      $ret .= "\n";
            
      $tmp1 = "";
      foreach ($arrNo as $val) {
        $tmp1 .= $val."\n";
      }
      $ret .= $this->translatedText("txtTw10", Array(count($arrNo), $tmp1));    // "<b>Unused</b>: ".count($arrNo)."\n<pre>".$tmp1."</pre>\n";
            
      $tmp3 = "";
      foreach ($arrUsed as $val) {
        $tmp3 .= $val."\n";
      }
      $ret .= $this->translatedText("txtTw41", Array(count($arrUsed), $tmp3));    // "<b>Used in others teams</b>: ".count($arrDef)."\n<pre>".$tmp."</pre>\n";
          
      $tmp2 = "";
      foreach ($arrAtt as $val) {
        $tmp2 .= $val."\n";
      }
      $maxPoints = count($arrAtt) * $pointsBattle;
      if ($maxPoints == 0) {
          $ret .= $this->translatedText("txtTw11", [ count($arrAtt), $sumOff, 0, $tmp2 ]);    // "<b>Used in offense</b>: ".count($arrAtt)." (".$sumOff." - ".number_format(($sumOff * 100) / $maxPoints, 2)."%)\n<pre>".$tmp2."</pre>\n";
      } else {
          $ret .= $this->translatedText("txtTw11", [ count($arrAtt), $sumOff, number_format(( $sumOff * 100 ) / $maxPoints, 2), $tmp2 ]);    // "<b>Used in offense</b>: ".count($arrAtt)." (".$sumOff." - ".number_format(($sumOff * 100) / $maxPoints, 2)."%)\n<pre>".$tmp2."</pre>\n";
      }

        $tmp = "";
      foreach ($arrDef as $val) {
        $tmp .= $val."\n";
      }
      $ret .= $this->translatedText("txtTw12", Array(count($arrDef), $tmp));    // "<b>Used in defense</b>: ".count($arrDef)."\n<pre>".$tmp."</pre>\n";
    }
            
    $tmp = "";
    foreach ($arrRogue as $val) {
      $tmp .= $val."\n";    
    }
    $ret .= $this->translatedText("txtTw13", Array(count($arrRogue), $tmp));    // "<b>Rogues</b>: ".count($arrRogue)."\n<pre>".$tmp."</pre>\n";
            
    return $ret;
  }

  /**************************************************************************
    esborra una defensa, atac o rogue falsa
  **************************************************************************/
  private function del() {
    if (!$this->checkAllyCode($this->allyCode))
      return $this->getHelp("tw", $this->translatedText("error3", $this->allyCode));  // $this->error = "The ".$params[2]." parameter is a bad AllyCode parameter. See help...\n\n"; 

    // agafem ID de la unitat (llista de alias o llista d'unitats)
    if ($this->alias == "rogue") 
      $unitId = $this->alias;
    else 
      $unitId = TAlias::aliasSearch($this->alias, $this->dataObj);
    
    if ($unitId == "")
      return $this->translatedText("twerr1", $this->alias); // "Unit <i>".$this->alias."</i> not found.\n\n";
    
    $player = $this->getInfoPlayer();
    
    // mirem que haguem trobat Id Guild
    if ($player[0]["guildRefId"] == "")
      return $this->translatedText("error6");                                   // "Ooooops! API server may have shut down. Try again later.\n\n"
            
    // conectem a la base de dades
    $idcon = new mysqli($this->dataObj->bdserver, $this->dataObj->bduser, $this->dataObj->bdpas, $this->dataObj->bdnamebd);
    if ($idcon->connect_error) 
      return $this->translatedText("error4");                                   // return "Ooooops! An error has occurred getting data.\n\n";
            
    // esborrem registre
    if ($this->alias == "rogue") {
      $sql = "update tw set points = points-1 where guildRefId = '".$player[0]["guildRefId"]."' AND allyCode = '".$player[0]["allyCode"]."' AND unit = 'rogue' ";
      $res = $idcon->query( $sql );
      if ($idcon->error) 
        return $this->translatedText("error4");                                 // return "Ooooops! An error has occurred getting data.";
      
      if ($idcon->affected_rows > 0) {
        $sql = "select points from tw where guildRefId = '".$player[0]["guildRefId"]."' AND allyCode = '".$player[0]["allyCode"]."' AND unit = 'rogue' ";
        $res = $idcon->query( $sql );
        $row = $res->fetch_assoc();
        if ($row['points'] <= 0) {
          $sql  = "DELETE FROM tw WHERE guildRefId = '".$player[0]["guildRefId"]."' AND allyCode = '".$player[0]["allyCode"]."' AND unit = '".$unitId."'";
          $res = $idcon->query( $sql );
        }
      }
      $ret  = $this->translatedText("txtTw02", $player[0]["guildName"]);        // "TW updated for ".$player[0]["guildName"]."\n\n";
      $ret .= $this->translatedText("txtTw03", $player[0]["name"]);             // "  Player: ".$player[0]["name"]."\n";
      $ret .= $this->translatedText("txtTw15");                                 // "  Rogue decreased \n\n";
      return $ret;
    }
    else {
      $sql  = "DELETE FROM tw WHERE guildRefId = '".$player[0]["guildRefId"]."' AND allyCode = '".$player[0]["allyCode"]."' AND unit = '".$unitId."'";
      $res = $idcon->query( $sql );
      if ($idcon->error) 
        return $this->translatedText("error4");                                 // $ret = "Ooooops! An error has occurred getting data.";
      
      $ret  = $this->translatedText("txtTw02", $player[0]["guildName"]);        // "TW updated for ".$player[0]["guildName"]."\n\n";
      $ret .= $this->translatedText("txtTw03", $player[0]["name"]);             // "  Player: ".$player[0]["name"]."\n";
      $ret .= $this->translatedText("txtTw16", TUnits::unitNameFromUnitId($unitId, $this->dataObj));     // "  Deleted Unit: ".$unit."\n\n";
      
      $idcon->close(); 
            
      return $ret;
    }           
  }

  /**************************************************************************
    retorna les unitats utilitzades per un AllyCode 
  **************************************************************************/
  private function me() {
    if (!$this->checkAllyCode($this->allyCode))
      return $this->getHelp("tw", $this->translatedText("error3", $this->allyCode));  // $this->error = "The ".$params[2]." parameter is a bad AllyCode parameter. See help...\n\n"; 
           
    $player = $this->getInfoPlayer();
    
    // mirem que haguem trobat Id Guild
    if ($player[0]["guildRefId"] == "")
      return $this->translatedText("error6");                                   // "Ooooops! API server may have shut down. Try again later.\n\n"
            
    // conectem a la base de dades
    $idcon = new mysqli($this->dataObj->bdserver, $this->dataObj->bduser, $this->dataObj->bdpas, $this->dataObj->bdnamebd);
    if ($idcon->connect_error) 
      return $this->translatedText("error4");                                   // return "Ooooops! An error has occurred getting data.\n\n";
            
    // cerquem registres
    $sql  = "SELECT * FROM tw WHERE guildRefId = '".$player[0]["guildRefId"]."' and allyCode = '". $this->allyCode."'";
           
    $res = $idcon->query( $sql );
    if ($idcon->error) 
      return $this->translatedText("error4");                                   // $ret = "Ooooops! An error has occurred getting data.";
    
    $arrOff = array();
    $arrDef = array();
    $arrUsed = array();
    $arrRogue = array();
    $maxPoints = 0;
    $sumOff = 0;
    $sumOffTot = 0;
    while ($row = $res->fetch_assoc()) {
      $unitName = TUnits::unitNameFromUnitId($row['unit'], $this->dataObj);
      $vsName = "";
      if ($row['vs'] != "")
        $vsName = TUnits::unitNameFromUnitId($row['vs'], $this->dataObj);
      if ($unitName == "") $unitName = $row['unit'];

      switch ($row['unittype']) {
        case 'off':
          if (TUnits::isAShip($row['unit'], $this->dataObj))
            $maxPoints = $maxPoints + 22;
          else
            $maxPoints = $maxPoints + 20;
          $sumOff = $sumOff + $row['points'];
          $sumOffTot = $sumOffTot + $row['points'] + $this->extraPointsToSum($row['unit'], $row['points']);
          array_push($arrOff, $unitName." vs ".$vsName." (".$row['points'].")");
          break;
        case 'def':
          array_push($arrDef, $unitName);
          break;
        case 'used':
          array_push($arrUsed, $unitName);
          break;
        case 'rogue':
          array_push($arrRogue, $unitName." (".$row['points'].")");
          break;
      }
    }	
    usort($arrOff, 'strnatcasecmp');	
    usort($arrDef, 'strnatcasecmp');	
    usort($arrUsed, 'strnatcasecmp');	
    usort($arrRogue, 'strnatcasecmp');	
      
    $ret  = $this->translatedText("txtTw17", $player[0]["guildName"]);          // "TW for ".$player[0]["guildName"]."\n\n";
    $ret .= $this->translatedText("txtTw18", $player[0]["name"]);               // "Units used by ".$player[0]["name"]."\n\n";

      $arr = [
          $sumOff,
          $sumOffTot,
          ($maxPoints === 0) ? '-' : number_format(( $sumOffTot * 100 ) / $maxPoints, 2)
      ];
      $ret .= $this->translatedText("txtTw19", $arr);       // "<b>Offense</b>: (%s/%s - %s/%s%)\n<pre>";
    foreach ($arrOff as $unit) {
      $ret .= " - ".$unit."\n";
    }
    $ret .= "</pre>\n";
      
    $ret .= $this->translatedText("txtTw20");                                   // "<b>Defense</b>: \n<pre>";
    foreach ($arrDef as $unit) {
      $ret .= " - ".$unit."\n";
    }
    $ret .= "</pre>\n";
      
    $ret .= $this->translatedText("txtTw42");                                   // "<b>Usadas</b>: \n<pre>";
    foreach ($arrUsed as $unit) {
      $ret .= " - ".$unit."\n";
    }
    $ret .= "</pre>\n";
      
    $ret .= $this->translatedText("txtTw21");                                   // "<b>Rogues</b>: \n<pre>";
    foreach ($arrRogue as $unit) {
      $ret .= $unit."\n";
    }
    $ret .= "</pre>\n";
    
    $idcon->close(); 
        
    return $ret;
  }          
  
  /**************************************************************************
    retorna una visió general de la TW
  **************************************************************************/
  private function all() {
    if (!$this->checkAllyCode($this->allyCode))
      return $this->getHelp("tw", $this->translatedText("error3", $this->allyCode));  // $this->error = "The ".$params[2]." parameter is a bad AllyCode parameter. See help...\n\n"; 
        
    // conectem a la base de dades
    $idcon = new mysqli($this->dataObj->bdserver, $this->dataObj->bduser, $this->dataObj->bdpas, $this->dataObj->bdnamebd);
    if ($idcon->connect_error) 
      return $this->translatedText("error4");                                   // "Ooooops! An error has occurred getting data.\n\n";
    
    $player = $this->getInfoPlayer();
    
    // mirem que haguem trobat Id Guild
    if ($player[0]["guildRefId"] == "")
      return $this->translatedText("error6");                                   // "Ooooops! API server may have shut down. Try again later.\n\n"
        
    // cerquem registres
    $sql  = "SELECT unittype, unit, count(*) cont, sum(points) points 
             FROM tw 
             WHERE guildRefId = '".$player[0]["guildRefId"]."' and unittype in ('def', 'off', 'used') 
             group by unittype, unit";
        
    $res = $idcon->query( $sql );
    if ($idcon->error) 
      return $this->translatedText("error4");                                   // "Ooooops! An error has occurred getting data.";
   
    $arrDef = array("charPoints" => 0, "shipPoints" => 0, "charBattles" => 0, "shipBattles" => 0, "char" => array(), "ships" => array());
    $arrUsed = array("charPoints" => 0, "shipPoints" => 0, "charBattles" => 0, "shipBattles" => 0, "char" => array(), "ships" => array());
    $arrOff = array("charPoints" => 0, "shipPoints" => 0, "charBattles" => 0, "shipBattles" => 0, "char" => array(), "ships" => array());
    while ($row = $res->fetch_assoc()) {
      $unitName = TUnits::unitNameFromUnitId($row['unit'], $this->dataObj);
      if ($unitName == "") $unitName = $row['unit'];
        
      if (TUnits::isAShip($row['unit'], $this->dataObj)) {
        switch ($row['unittype']) {
          case 'off':  
            $arrOff["ships"][$unitName] = array("cont" => $row['cont'], "points" => $row['cont']);
            $arrOff["shipPoints"] = $arrOff["shipPoints"] + $row['points'];
            $arrOff["shipBattles"] = $arrOff["shipBattles"] + $row['cont'];
            break;
          case 'def':  
            $arrDef["ships"][$unitName] = array("cont" => $row['cont'], "points" => $row['cont']);
            $arrDef["shipBattles"] = $arrDef["shipBattles"] + $row['cont'];
            break;
          case 'used':  
            $arrUsed["ships"][$unitName] = array("cont" => $row['cont'], "points" => $row['cont']);
            $arrUsed["shipBattles"] = $arrUsed["shipBattles"] + $row['cont'];
            break;
        }
      } 
      else {
        switch ($row['unittype']) {
          case 'off':
            $arrOff["char"][$unitName] = array("cont" => $row['cont'], "points" => $row['cont']);
            $arrOff["charPoints"] = $arrOff["charPoints"] + $row['points'];
            $arrOff["charBattles"] = $arrOff["charBattles"] + $row['cont'];
            break;
          case 'def':
            $arrDef["char"][$unitName] = array("cont" => $row['cont'], "points" => $row['cont']);
            $arrDef["charBattles"] = $arrDef["charBattles"] + $row['cont'];
            break;
          case 'used':
            $arrUsed["char"][$unitName] = array("cont" => $row['cont'], "points" => $row['cont']);
            $arrUsed["charBattles"] = $arrUsed["charBattles"] + $row['cont'];
            break;
        }
      }
    }	
    ksort($arrOff);	
    ksort($arrDef);	
    ksort($arrUsed);	
        
    $ret  = $this->translatedText("txtTw22", $player[0]["guildName"]);          // "TW General Vision for ".$player[0]["guildName"]."\n\n";
    
    // imprimim defenses      
    $ret .= $this->translatedText("txtTw23");                                   // "<b>Defense</b>\n";
    $ret .= $this->translatedText("txtTw24", $arrDef["shipBattles"]);           // "<i>Ships</i> (def: ".$arrDef["shipBattles"].")\n";
    foreach ($arrDef["ships"] as $unit => $values) {
      $ret .= "   ".$unit.": ".$values["cont"]."\n";
    }
    $ret .= "\n";
    $ret .= $this->translatedText("txtTw25", $arrDef["charBattles"]);           // "<i>Characters</i> (def: ".$arrDef["charBattles"].")\n";
    foreach ($arrDef["char"] as $unit => $values) {
      $ret .= "   ".$unit.": ".$values["cont"]."\n";
    }
    $ret .= "\n"; 
        
    // imprimim unitats usades
    $ret .= $this->translatedText("txtTw43");                                   // "<b>Used in others teams</b>\n";
    $ret .= $this->translatedText("txtTw44", $arrUsed["shipBattles"]);          // "<i>Ships</i>\n";
    foreach ($arrUsed["ships"] as $unit => $values) {
      $ret .= "   ".$unit.": ".$values["cont"]."\n";
    }
    $ret .= "\n";
    $ret .= $this->translatedText("txtTw45", $arrUsed["charBattles"]);           // "<i>Characters</i>\n";
    foreach ($arrUsed["char"] as $unit => $values) {
      $ret .= "   ".$unit.": ".$values["cont"]."\n";
    }
    $ret .= "\n"; 
    
    // imprimim atacs      
    $ret .= $this->translatedText("txtTw26");                                   // "<b>Offense</b>\n";
    $ret .= $this->translatedText("txtTw27", array($arrOff["shipBattles"], $arrOff["shipPoints"]));   // "<i>Ships</i> (battles: ".$arrOff["shipBattles"].", points:".$arrOff["shipPoints"].")\n";
    foreach ($arrOff["ships"] as $unit => $values) {
      $ret .= "   ".$unit.": ".$values["cont"]."\n";
    }
    $ret .= "\n";
    $ret .= $this->translatedText("txtTw28", array($arrOff["charBattles"], $arrOff["charPoints"]));   // "<i>Characters</i> (battles: ".$arrOff["charBattles"].", points:".$arrOff["charPoints"].")\n";
    foreach ($arrOff["char"] as $unit => $values) {
      $ret .= "   ".$unit.": ".$values["cont"]."\n";
    }
    $ret .= "\n";
    
    $idcon->close(); 
        
    return $ret;
  }
  
  /**************************************************************************
    retorna la llista de rogues falses
  **************************************************************************/
  private function rogueList() {
    if (!$this->checkAllyCode($this->allyCode))
      return $this->getHelp("tw", $this->translatedText("error3", $this->allyCode));  // $this->error = "The ".$params[2]." parameter is a bad AllyCode parameter. See help...\n\n"; 

    $player = $this->getInfoPlayer();
    
    // mirem que haguem trobat Id Guild
    if ($player[0]["guildRefId"] == "")
      return $this->translatedText("error6");                                   // "Ooooops! API server may have shut down. Try again later.\n\n"
  
    // conectem a la base de dades
    $idcon = new mysqli($this->dataObj->bdserver, $this->dataObj->bduser, $this->dataObj->bdpas, $this->dataObj->bdnamebd);
    if ($idcon->connect_error) 
      return $this->translatedText("error4");                                   // "Ooooops! An error has occurred getting data.\n\n";
        
    // cerquem registres
    $sql  = "SELECT name, points FROM tw WHERE guildRefId = '".$player[0]["guildRefId"]."' and unittype = 'rogue' ";
    
    $res = $idcon->query( $sql );
    if ($idcon->error) 
      return $this->translatedText("error4");                                   // "Ooooops! An error has occurred getting data.";

    $arr = array();
    while ($row = $res->fetch_assoc()) {
      $arr[$row['name']] = $row['points'];
    }	
    uksort($arr, 'strnatcasecmp');
    
    $ret  = $this->translatedText("txtTw29", $player[0]["guildName"]);          // "TW General Rogue List for ".$player[0]["guildName"]."\n\n";

    foreach ($arr as $name => $points) {
      $ret .= $name.": ".$points."\n";
    }
    $ret .= "\n";

    $idcon->close(); 
  
    return $ret;
  }
 
  /**************************************************************************
    retorna tots els equips utilitzats per tots els membres del gremi en la TW
  **************************************************************************/
  private function estampometro() {
    if (!$this->checkAllyCode($this->allyCode))
      return $this->getHelp("tw", $this->translatedText("error3", $this->allyCode));  // $this->error = "The ".$params[2]." parameter is a bad AllyCode parameter. See help...\n\n"; 

    $player = $this->getInfoPlayer();
  
    // conectem a la base de dades
    $idcon = new mysqli($this->dataObj->bdserver, $this->dataObj->bduser, $this->dataObj->bdpas, $this->dataObj->bdnamebd);
    if ($idcon->connect_error) 
      return $this->translatedText("error4");                                   // "Ooooops! An error has occurred getting data.\n\n";
    
    // mirem que haguem trobat Id Guild
    if ($player[0]["guildRefId"] == "")
      return $this->translatedText("error6");                                   // "Ooooops! API server may have shut down. Try again later.\n\n"
  
    // cerquem registres
    $sql  = "SELECT * FROM tw where guildRefId = '".$player[0]["guildRefId"]."' and unittype = 'off'";

    $res = $idcon->query( $sql );
    if ($idcon->error) 
      return $this->translatedText("error4");                                   // "Ooooops! An error has occurred getting data.\n\n";
  
    // informem arrays
    $arr = array();
    $noFirst = array();
    while ($row = $res->fetch_assoc()) {
      $unitName = TUnits::unitNameFromUnitId($row['unit'], $this->dataObj);
      $vsName = TUnits::unitNameFromUnitId($row['vs'], $this->dataObj);
      $key = $row['vs'].$row['unit'];
//      $key1 = $row['unit'].$row['vs'];
      if (!array_key_exists($key, $arr)) {
        $arr[$key] = array(
                        //   'keyContra' => $key,
                        //   'keyCon' => $key1,
                           'vs' => $vsName, 
                           'unit' => $unitName, 
                           'primera' => 0, 
                           'total' => 0, 
                           'sumaPuntos' => 0,
                           'totalPuntos' => 0 
                          );
      }
        
      if (TUnits::isAShip($row['unit'], $this->dataObj)) {
        $arr[$key]['totalPuntos'] = $arr[$key]['totalPuntos'] + 22;
        if ($row['points'] >= 18) {
          $arr[$key]['primera'] = $arr[$key]['primera'] + 1;
        }
        else {
          if ($row['points'] != 0)
            $noFirst[$row['name']] = $noFirst[$row['name']] + $this->extraPointsToSum($row['unit'], $row['points']);
        }
      } 
      else {
        $arr[$key]['totalPuntos'] = $arr[$key]['totalPuntos'] + 20;
        if ($row['points'] >= 16) {
          $arr[$key]['primera'] = $arr[$key]['primera'] + 1;
        }
        else {
          if ($row['points'] != 0)
            $noFirst[$row['name']] = $noFirst[$row['name']] + $this->extraPointsToSum($row['unit'], $row['points']);
        }
      }
      $arr[$key]['total'] = $arr[$key]['total'] + 1;
      $arr[$key]['sumaPuntos'] = $arr[$key]['sumaPuntos'] + $row['points'];
    }	
    $idcon->close(); 
  
    ksort($noFirst);	
      
    // imprimim resultat
    $ret = array();
    $ret[0]  = $this->translatedText("txtTw30", $player[0]["guildName"]);       // "TW estampómetro for ".$player[0]["guildName"]."\n";

    // Visió CONTRA
    usort($arr, function($a, $b) {
      if ($a['vs'] == $b['vs'])
        return strtoupper($a['unit']) <=> strtoupper($b['unit']);
      return $a['vs'] > $b['vs'];
    });
    $tmpVersus = "";
    $idx = 0;
    foreach ($arr as $versus) {
      if ($tmpVersus != $versus['vs']) { 
        $ret[$idx] .= $this->translatedText("txtTw31", $versus['vs']);             // "\n<b>Versus ".$versus['vs']."</b>\n";
        $tmpVersus = $versus['vs'];
      }
      
      if ($versus['totalPuntos'] == 0) 
        $num = 0;
      else 
        $num = number_format(($versus['sumaPuntos'] * 100) / $versus['totalPuntos'], 2);
      
      $ret[$idx] .= "   - ".$versus['unit'].": ".$versus['primera']."P / ".$versus['total']."T / ".$versus['sumaPuntos']." puntos / ".$num."% \n";
      
      if (strlen($ret[$idx]) > $this->dataObj->maxChars) {
        $idx++;
      }
    }
    $ret[$idx] .= "\n";
    

    // visió CON
    usort($arr, function($a, $b) {
      if ($a['unit'] == $b['unit'])
        return strtoupper($a['vs']) <=> strtoupper($b['vs']);
      return $a['unit'] > $b['unit'];
    });
    $tmpVs = "";
    $idx++;
    foreach ($arr as $versus) {
      if ($tmpVs != $versus['unit']) { 
        $ret[$idx] .= $this->translatedText("txtTw46", $versus['unit']);             // "\n<b>With ".$versus['vs']."</b>\n";
        $tmpVs = $versus['unit'];
      }
      
      if ($versus['totalPuntos'] == 0) 
        $num = 0;
      else 
        $num = number_format(($versus['sumaPuntos'] * 100) / $versus['totalPuntos'], 2);
      
      $ret[$idx] .= "   - ".$versus['vs'].": ".$versus['primera']."P / ".$versus['total']."T / ".$versus['sumaPuntos']." puntos / ".$num."% \n";
      
      if (strlen($ret[$idx]) > $this->dataObj->maxChars) {
        $idx++;
      }
    }
    $ret[$idx] .= "\n";
        
    
    // atacas no al primer intent
    $tmp = $this->translatedText("txtTw32");                                    // "<b>No first attack</b>\n\n";
        
    $idx++;
    foreach ($noFirst as $key => $value) {
      $tmp .= $key.": ".$value."\n";
    }
    $tmp .= "\n";
    $ret[$idx] = $tmp;
       
    return $ret;
  }
  
  /**************************************************************************
    retorna un llistats dels atacs + rogues per persona en la TW
  **************************************************************************/
  private function attacks() {
    if (!$this->checkAllyCode($this->allyCode))
      return $this->getHelp("tw", $this->translatedText("error3", $this->allyCode));  // $this->error = "The ".$params[2]." parameter is a bad AllyCode parameter. See help...\n\n"; 

    $player = $this->getInfoPlayer();
    
    // mirem que haguem trobat Id Guild
    if ($player[0]["guildRefId"] == "")
      return $this->translatedText("error6");                                   // "Ooooops! API server may have shut down. Try again later.\n\n"
    
    // conectem a la base de dades
    $idcon = new mysqli($this->dataObj->bdserver, $this->dataObj->bduser, $this->dataObj->bdpas, $this->dataObj->bdnamebd);
    if ($idcon->connect_error) 
      return $this->translatedText("error4");                                   // "Ooooops! An error has occurred getting data.\n\n";
        
    // cerquem registres
    $sql  = "SELECT *
             FROM tw 
             WHERE guildRefId = '".$player[0]["guildRefId"]."' and unittype in ('off', 'rogue')";
        
    $res = $idcon->query( $sql );
    if ($idcon->error) 
      return $this->translatedText("error4");                                   // "Ooooops! An error has occurred getting data.";
        
    $arr = array();
    while ($row = $res->fetch_assoc()) {
      if (!array_key_exists($row['name'], $arr)) 
        $arr[$row['name']] = array(
                                   'name' => $row['name'],
                                   'off' => 0, 
                                   'points' => 0, 
                                   'maxpoints' => 0, 
                                   'rogues' => 0, 
                                   'percent' => 0
                                  );
        
      if ($row['unittype'] == 'off') {
        $arr[$row['name']]['points'] = $arr[$row['name']]['points'] + $row['points'] + $this->extraPointsToSum($row['unit'], $row['points']);
        $arr[$row['name']]['off'] = $arr[$row['name']]['off'] + 1; 
        
        if (TUnits::isAShip($row['unit'], $this->dataObj)) {
            $arr[$row['name']]['maxpoints'] = $arr[$row['name']]['maxpoints'] + 22;
        } else {
            $arr[$row['name']]['maxpoints'] = $arr[$row['name']]['maxpoints'] + 20;
        }
      }
      else {
        $arr[$row['name']]['rogues'] = $arr[$row['name']]['rogues'] + $row['points'];
      }
    }	
        
    $idcon->close(); 
        
    // calculem el %
    foreach ($arr as $key => $data) {
      $arr[$key]['percent'] = $data['maxpoints'] === 0 ? '0.00' : number_format(($data['points'] * 100)/$data['maxpoints'], 2);
    }
        
    // ordenem
    switch (strtolower($this->sort)) {
      case 'rogues':
        usort($arr, function($a, $b) {
          if ($a['rogues'] == $b['rogues'])
            return strtoupper($a['name']) <=> strtoupper($b['name']);
          return $a['rogues'] < $b['rogues'];
        });
        break;
      case 'battles':
        usort($arr, function($a, $b) {
          if ($a['off'] == $b['off'])
            return strtoupper($a['name']) <=> strtoupper($b['name']);
          return $a['off'] < $b['off'];
        });
        break;
      case 'points':
        usort($arr, function($a, $b) {
          if ($a['points'] == $b['points'])
            return strtoupper($a['name']) <=> strtoupper($b['name']);
          return $a['points'] < $b['points'];
        });
        break;
      case '%':
        usort($arr, function($a, $b) {
          if ($a['percent'] == $b['percent'])
            return strtoupper($a['name']) <=> strtoupper($b['name']);
          return $a['percent'] < $b['percent'];
        });
        break;
      default:
        usort($arr, function($a, $b) {
          return strtoupper($a['name']) <=> strtoupper($b['name']);
        });
    }
    
    // imprimim   
    $ret  = $this->translatedText("txtTw33", $player[0]["guildName"]);          // "TW attacks for ".$player[0]["guildName"]."\n\n";
    $ret .= "rogues|battles|points|%\n";
    $ret .= "<pre>";
    
    $sumOff = 0;
    foreach ($arr as $data) {
      $sumOff = $sumOff + $data['off'];
      $ret .= str_pad($data['rogues'], 2, " ", STR_PAD_LEFT)."|".
              str_pad($data['off'], 2, " ", STR_PAD_LEFT)."|".
              str_pad($data['points'], 3, " ", STR_PAD_LEFT)."|".
              str_pad($data['percent'], 6, " ", STR_PAD_LEFT)." - ".
              $data['name']."\n";
    }
    if ($sumOff > 0) {
      $ret .= "----------------\n";
      $ret .= " ".str_pad($sumOff, 3, " ", STR_PAD_LEFT)."              TOTAL\n";
    }
    $ret .= "</pre>";
    $ret .= "\n";
    
    return $ret;
  }

  /**************************************************************************
    retorna un llistats dels atacs + rogues per persona en la TW
  **************************************************************************/
  private function defenses() {
    if (!$this->checkAllyCode($this->allyCode))
      return $this->getHelp("tw", $this->translatedText("error3", $this->allyCode));  // $this->error = "The ".$params[2]." parameter is a bad AllyCode parameter. See help...\n\n"; 

    $players = $this->getInfoGuild();

    // mirem que haguem trobat Id Guild
    if ($players[0]["id"] == "") {
        return $this->translatedText("error6");                                   // "Ooooops! API server may have shut down. Try again later.\n\n"
    }

    $guildName = $players[0]['name'];
    // creem array amb tots els jugadors
    $arr = array();
      foreach ($players[0]['roster'] as $player) {
          $arr[$player['name']] = [
              'playerName' => $player['name'],
              'allyCode'   => $player['allyCode'],
              'count'      => 0,
              'def'        => 0,
          ];
      }

      // conectem a la base de dades
    $idcon = new mysqli($this->dataObj->bdserver, $this->dataObj->bduser, $this->dataObj->bdpas, $this->dataObj->bdnamebd);
    if ($idcon->connect_error) 
      return $this->translatedText("error4");                                   // "Ooooops! An error has occurred getting data.\n\n";
    // cerquem registres
    $sql  = "SELECT 
                tw.allyCode as allyCode
                ,tw.unit as unitName
                ,tw.name as playerName
                ,users.username as telegramTag
             FROM tw LEFT JOIN users ON tw.allyCode = users.allycode
             WHERE guildRefId = '".$players[0]["id"]."' and unittype in ('def')";

    $res = $idcon->query( $sql );
    if ($idcon->error) {
        return $this->translatedText("error4");                                   // "Ooooops! An error has occurred getting data.";
    }

    while ($row = $res->fetch_assoc()) {
        $playerName            = $row['playerName'];
        $record                = $arr[$playerName] ?? [
                'playerName'  => $playerName,
                'count' => 0,
                'def'   => 0,
            ];
        $record['allyCode']    = $row['allyCode'];
        $record['telegramTag'] = $row['telegramTag'];
        $record['count']       += 1;
        $record['def']         += TUnits::isAShip($row['unitName'], $this->dataObj)
            ? 34
            : 30;

        $arr[$playerName] = $record;
    }	

    foreach ($arr as $index => $record) {
        if (isset($record['telegramTag'])) {
            continue;
        }
        $sql  = "SELECT username FROM users where allycode = '".$record['allyCode']."' limit 1";
        $res = $idcon->query( $sql );
        $row = $res->fetch_assoc();
        $record['telegramTag'] = $row['username'] ?? '';
        $arr[$index] = $record;
    }
    $idcon->close();

    // ordenem
    switch (strtolower($this->sort)) {
      case 'teams':
        usort($arr, function($a, $b) {
          if ($a['count'] == $b['count'])
            return strtoupper($a['playerName']) <=> strtoupper($b['playerName']);
          return $a['count'] < $b['count'];
        });
        break;
      case 'points':
        usort($arr, function($a, $b) {
          if ($a['def'] == $b['def'])
            return strtoupper($a['playerName']) <=> strtoupper($b['playerName']);
          return $a['def'] < $b['def'];
        });
        break;
      default:
        usort($arr, function($a, $b) {
          return strtoupper($a['playerName']) <=> strtoupper($b['playerName']);
        });
        break;
    }
    
    // imprimim   
    $ret  = $this->translatedText("txtTw34", $guildName);          // "TW defenses for ".$player[0]["guildName"]."\n\n";
    $ret .= "teams|points \n";

    $lines = [];
    foreach ($arr as $data) {
        $lines[] = sprintf(
          '<code>%s|%s - [%s-%s]</code> @%s',
          str_pad($data['count'], 2, " ", STR_PAD_LEFT),
          str_pad($data['def'], 3, " ", STR_PAD_LEFT),
          $data['playerName'],
          $data['allyCode'],
          $data['telegramTag'] ?? ''
      );
    }
    $ret .= join(" \n", $lines);
    $ret .= "\n";
    
    return $ret;
  }
  
  /**************************************************************************
    guarda el resultat d'una TW a l'històric de la base de dades
  **************************************************************************/
  private function save() {
    if (!$this->checkAllyCode($this->allyCode))
      return $this->getHelp("tw", $this->translatedText("error3", $this->allyCode));  // $this->error = "The ".$params[2]." parameter is a bad AllyCode parameter. See help...\n\n"; 
  
    // comprovem data  yyyymmdd
    if (!$this->isCorrectDate($this->date))
      return $this->translatedText("twerr2", $this->date);                      // "Ooooops! '".$this->date."' is not a valid date. Correct date format yyyymmdd and max date today.\n\n";
 
    // esborrem dades que puguin haver-hi
    $this->deleteH();
      
    // agafem dades
    $player = $this->getInfoPlayer();
    
    // mirem que haguem trobat Id Guild
    if ($player[0]["guildRefId"] == "")
      return $this->translatedText("error6");                                   // "Ooooops! API server may have shut down. Try again later.\n\n"
      
    // conectem a la base de dades
    $idcon = new mysqli($this->dataObj->bdserver, $this->dataObj->bduser, $this->dataObj->bdpas, $this->dataObj->bdnamebd);
    if ($idcon->connect_error) 
      return $this->translatedText("error4");                                   // "Ooooops! An error has occurred getting data.\n\n";
  
    // cerquem registres
    $sql  = "SELECT *
             FROM tw 
             WHERE guildRefId = '".$player[0]["guildRefId"]."' and unittype in ('off', 'rogue')
             order by name";
      
    $res = $idcon->query( $sql );
    if ($idcon->error) 
      return $this->translatedText("error4");                                   // "Ooooops! An error has occurred getting data.";
      
    $arr = array();
    while ($row = $res->fetch_assoc()) {
      if (!array_key_exists($row['name'], $arr)) 
        $arr[$row['name']] = array(
                                   'name' => $row['name'],
                                   'allyCode' => $row['allyCode'], 
                                   'off' => 0, 
                                   'points' => 0, 
                                   'maxpoints' => 0,
                                   'rogues' => 0, 
                                   'percent' => 0
                                  );
      
      if ($row['unittype'] == 'off') {
        $arr[$row['name']]['points'] = $arr[$row['name']]['points'] + $row['points'] + $this->extraPointsToSum($row['unit'], $row['points']);
        $arr[$row['name']]['off'] = $arr[$row['name']]['off'] + 1; 
      
        if (TUnits::isAShip($row['unit'], $this->dataObj)) {
            $arr[$row['name']]['maxpoints'] = $arr[$row['name']]['maxpoints'] + 22;
        } else {
            $arr[$row['name']]['maxpoints'] = $arr[$row['name']]['maxpoints'] + 20;
        }
      } 
      else {
          $arr[$row['name']]['rogues'] = $arr[$row['name']]['rogues'] + $row['points'];
      }
    }	
      
    // calculem el %
    foreach ($arr as $key => $data) {
      $arr[$key]['percent'] = $data['maxpoints'] === 0
        ? 0
        : number_format(($data['points'] * 100) / $data['maxpoints'], 2);
    }
    
    // guardem 1 registre per persona  
    foreach ($arr as $data) {
      $sql  = "INSERT INTO twh (guildRefId, twDate, allyCode, name, rogues, battles, points, percent) ";
      $sql .= "VALUES ('".$player[0]["guildRefId"]."', '". $this->date."', '".$data['allyCode']."', '".$data["name"]."', ".$data["rogues"].", ".$data["off"].", ".$data["points"].", ".$data["percent"].") ";
      $sql .= "ON DUPLICATE KEY UPDATE name='".$data["name"]."', rogues=".$data["rogues"].", battles=".$data["off"].", points=".$data["points"].", percent=".$data["percent"];  
      $res = $idcon->query( $sql );
    }
    $idcon->close(); 
      
    // retornem resultat
    $ret  = $this->translatedText("txtTw35", $player[0]["guildName"]);          // "TW saved for ".$player[0]["guildName"]."\n\n";
      
    return $ret;
  }  
  
  /**************************************************************************
    esborra un resultat d'una TW de l'històric de la base de dades
  **************************************************************************/
  private function deleteH() {
    if (!$this->checkAllyCode($this->allyCode))
      return $this->getHelp("tw", $this->translatedText("error3", $this->allyCode));  // $this->error = "The ".$params[2]." parameter is a bad AllyCode parameter. See help...\n\n"; 
  
    // comprovem data  yyyymmdd
    if (!$this->isCorrectDate($this->date))
      return $this->translatedText("twerr2", $this->date);                      // "Ooooops! '".$this->date."' is not a valid date. Correct date format yyyymmdd and max date today.\n\n";
      
    // agafem dades
    $player = $this->getInfoPlayer();
    
    // mirem que haguem trobat Id Guild
    if ($player[0]["guildRefId"] == "")
      return $this->translatedText("error6");                                   // "Ooooops! API server may have shut down. Try again later.\n\n"
    
    // conectem a la base de dades
    $idcon = new mysqli($this->dataObj->bdserver, $this->dataObj->bduser, $this->dataObj->bdpas, $this->dataObj->bdnamebd);
    if ($idcon->connect_error) 
      return $this->translatedText("twerr2", $this->date);                      // "Ooooops! An error has occurred getting data.\n\n";
      
    // esborrem dades
    $sql  = "delete from twh where guildRefId = '".$player[0]["guildRefId"]."' and twDate = '".$this->date."'";
      
    $idcon->query( $sql );
    if ($idcon->error) {
      $idcon->close(); 
      return $this->translatedText("twerr2", $this->date);                      // "Ooooops! An error has occurred getting data.";
    } 
  
    // retornem resultat
    $ret  = $this->translatedText("txtTw36", $player[0]["guildName"]);          // "TW deleted for ".$player[0]["guildName"]."\n\n";
      
    return $ret;    
  }

  /**************************************************************************
    llista les dates guardades de TW o bé la info d'una determinada data
  **************************************************************************/
  private function listH() {
    if (!$this->checkAllyCode($this->allyCode))
      return $this->getHelp("tw", $this->translatedText("error3", $this->allyCode));  // $this->error = "The ".$params[2]." parameter is a bad AllyCode parameter. See help...\n\n"; 
  
    // comprovem data yyyymmdd
    if (($this->date != "") && (!$this->isCorrectDate($this->date)))
      return $this->translatedText("twerr2", $this->date);                      // "Ooooops! '".$this->date."' is not a valid date. Correct date format yyyymmdd and max date today.\n\n";
      
    // agafem dades
    $player = $this->getInfoPlayer();
    
    // mirem que haguem trobat Id Guild
    if ($player[0]["guildRefId"] == "")
      return $this->translatedText("error6");                                   // "Ooooops! API server may have shut down. Try again later.\n\n"
  
    // conectem a la base de dades
    $idcon = new mysqli($this->dataObj->bdserver, $this->dataObj->bduser, $this->dataObj->bdpas, $this->dataObj->bdnamebd);
    if ($idcon->connect_error) 
      return $this->translatedText("twerr2", $this->date);                      // "Ooooops! An error has occurred getting data.\n\n";
      
    // esborrem dades antigues (2 anys d'històric)
    $today = getdate();
    $today['year'] = $today['year'] - 2;
    $today = $today['year'].str_pad($today['mon'], 2, " ", STR_PAD_LEFT).str_pad($today['mday'], 2, " ", STR_PAD_LEFT);
    $sql = "delete from twh where twDate < '".$today."'";
    $idcon->query( $sql );
      
    // cerquem informació a la base de dades
    if ($this->date != "")
      $sql = "SELECT * FROM twh where guildRefId = '".$player[0]["guildRefId"]."' and twDate = '".$this->date."' order by name";
    else
      $sql = "SELECT twDate, sum(rogues) rogues, sum(battles) battles, sum(points) points, AVG(percent) percent
              FROM twh
              where guildRefId = '".$player[0]["guildRefId"]."'
              group by twDate
              order by twDate";
      
    $res = $idcon->query( $sql );
    if ($idcon->error) {
      $idcon->close(); 
      return $this->translatedText("twerr2", $this->date);                      // "Ooooops! An error has occurred getting data.";
    } 
  
    // recuperem resultat
    $arr = array();
    $arr['zzzavg'] = array(
                         'name' => 'zzzavg',
                         'battles' => 0,
                         'points' => 0,
                         'rogues' => 0,
                         'cont' => 0,
                         'percent' => 0
                        );
    while ($row = $res->fetch_assoc()) {
      if ($this->date != "")
        $key = $row['name'];
      else
        $key = $row['twDate'];
      
      $arr[$key] = array(
                         'name' => $key,
                         'battles' => $row['battles'], 
                         'points' => $row['points'], 
                         'rogues' => $row['rogues'], 
                         'percent' => number_format($row['percent'], 2)
                        );
      $arr['zzzavg']['battles'] = $arr['zzzavg']['battles'] + $row['battles'];
      $arr['zzzavg']['points'] = $arr['zzzavg']['points'] + $row['points'];
      $arr['zzzavg']['rogues'] = $arr['zzzavg']['rogues'] + $row['rogues'];
      $arr['zzzavg']['cont'] = $arr['zzzavg']['cont'] + 1;
      $arr['zzzavg']['percent'] = $arr['zzzavg']['percent'] + $row['percent'];
    }

    // ordenem resultat
    usort($arr, function($a, $b) {
      return strtoupper($a['name']) <=> strtoupper($b['name']);
    });
      
    // imprimim resultat
    $ret  = $this->translatedText("txtTw37", $player[0]["guildName"]);          // "TW History for ".$player[0]["guildName"]."\n\n";
    if ($this->date != "")
      $ret .= $this->translatedText("txtTw38", $this->date);                    // "Displayed Date: ".$this->date."\n\n";
    $ret .= "rogues|battles|points|%\n";
    $ret .= "<pre>";
    foreach ($arr as $data) {
      if ($data['name'] == 'zzzavg')  {
        $zzzavg = $data;
        continue;
      }
      $ret .= str_pad($data['rogues'], 2, " ", STR_PAD_LEFT)."|".
              str_pad($data['battles'], 2, " ", STR_PAD_LEFT)."|".
              str_pad($data['points'], 3, " ", STR_PAD_LEFT)."|".
              str_pad($data['percent'], 6, " ", STR_PAD_LEFT)." - ".
              $data['name']."\n";
    }
    $ret .= "------------------\n";
    $ret .= str_pad( intval($zzzavg['rogues'] / $zzzavg['cont']), 2, " ", STR_PAD_LEFT)."|".
            str_pad( intval($zzzavg['battles'] / $zzzavg['cont']), 2, " ", STR_PAD_LEFT)."|".
            str_pad( intval($zzzavg['points'] / $zzzavg['cont']), 3, " ", STR_PAD_LEFT)."|".
            str_pad( number_format($zzzavg['percent'] / $zzzavg['cont'], 2), 6, " ", STR_PAD_LEFT)." - ".
            $this->translatedText("txtTw47", $zzzavg['cont'])."\n";             // "Average (%s)";
    $ret .= "</pre>";
    $ret .= "\n";
      
    return $ret;
  }
  
   /**************************************************************************
    llista les dates guardades de TW o bé la info d'una determinada data
  **************************************************************************/
  private function history() {
    if (!$this->checkAllyCode($this->allyCode))
      return $this->getHelp("tw", $this->translatedText("error3", $this->allyCode));  // $this->error = "The ".$params[2]." parameter is a bad AllyCode parameter. See help...\n\n"; 
  
    $players = $this->getInfoGuild();
    
    // mirem que haguem trobat Id Guild
    if ($players[0]["id"] == "")
      return $this->translatedText("error6");                                   // "Ooooops! API server may have shut down. Try again later.\n\n"
      
    // conectem a la base de dades
    $idcon = new mysqli($this->dataObj->bdserver, $this->dataObj->bduser, $this->dataObj->bdpas, $this->dataObj->bdnamebd);
    if ($idcon->connect_error) 
      return $this->translatedText("twerr2", $this->date);                      // "Ooooops! An error has occurred getting data.\n\n";
      
    // agafem llistat de jugadors del gremi
    $playerList = "";
    foreach ($players[0]['roster'] as $player) {
      if ($playerList != "") 
        $playerList .= ",";
      $playerList .= "'".$player['allyCode']."'";
    }
  
    // cerquem registres
    if ($this->all)
      $sql  = "SELECT name, count(*) gts, avg(rogues) rogues, avg(battles) battles, avg(points) points, avg(percent) percent FROM twh where guildRefId = '".$players[0]["id"]."' group by name";
    else
      $sql  = "SELECT name, count(*) gts, avg(rogues) rogues, avg(battles) battles, avg(points) points, avg(percent) percent FROM twh where guildRefId = '".$players[0]["id"]."' and allyCode in (".$playerList.") group by name";
      
    $res = $idcon->query( $sql );
    if ($idcon->error) 
      return $this->translatedText("twerr2", $this->date);                      // "Ooooops! An error has occurred getting data.";
      
    $arr = array();
    while ($row = $res->fetch_assoc()) {
      $arr[$row['name']] = array(
                                 'name' => $row['name'], 
                                 'battles' => number_format($row['battles'], 1), 
                                 'points' => number_format($row['points'], 1), 
                                 'rogues' => number_format($row['rogues'], 1), 
                                 'percent' => number_format($row['percent'], 1),
                                 'gts' => number_format($row['gts'], 0)
                                );
    }	
    $idcon->close(); 
      
    // ordenem
    switch ($this->sort) {
      case 'rogues':
        usort($arr, function($a, $b) {
          if ($a['rogues'] == $b['rogues'])
            return strtoupper($a['name']) <=> strtoupper($b['name']);
          return $a['rogues'] < $b['rogues'];
        });
        break;
      case 'battles':
        usort($arr, function($a, $b) {
          if ($a['battles'] == $b['battles'])
            return strtoupper($a['name']) <=> strtoupper($b['name']);
          return $a['battles'] < $b['battles'];
        });
        break;
      case 'points':
        usort($arr, function($a, $b) {
          if ($a['points'] == $b['points'])
            return strtoupper($a['name']) <=> strtoupper($b['name']);
          return $a['points'] < $b['points'];
        });
        break;
      case '%':
        usort($arr, function($a, $b) {
          if ($a['percent'] == $b['percent'])
            return strtoupper($a['name']) <=> strtoupper($b['name']);
          return $a['percent'] < $b['percent'];
        });
        break;
      case 'gts':
        usort($arr, function($a, $b) {
          if ($a['gts'] == $b['gts'])
            return strtoupper($a['name']) <=> strtoupper($b['name']);
          return $a['gts'] < $b['gts'];
        });
        break;
      default:
        usort($arr, function($a, $b) {
          return strtoupper($a['name']) <=> strtoupper($b['name']);
        });
        break;
    }
    
    // imprimim   
    $ret  = $this->translatedText("txtTw33", $players[0]["name"]);              // "TW attacks for ".$players[0]["name"]."\n\n";
    $ret .= $this->translatedText("txtTw39");                                   // "Average of...\n";
    $ret .= "rogues|battles|points|%|gts\n";
    $ret .= "<pre>";
    
    foreach ($arr as $data) {
      $ret .= str_pad($data['rogues'], 3, " ", STR_PAD_LEFT)."|".
              str_pad($data['battles'], 4, " ", STR_PAD_LEFT)."|".
              str_pad($data['points'], 5, " ", STR_PAD_LEFT)."|".
              str_pad($data['percent'], 5, " ", STR_PAD_LEFT)."|".
              str_pad($data['gts'], 3, " ", STR_PAD_LEFT)." - ".
              $data['name']."\n";
    }
    $ret .= "</pre>";
    $ret .= "\n";
    
    return $ret;
  } 
  
  /**************************************************************************
    retorna els punts extra a sumar d'un atac en la TW
  **************************************************************************/
  private function extraPointsToSum($unitId, $points) {
    if (TUnits::isAShip($unitId, $this->dataObj)) {
      switch (true) {
        case ($points >= 18):
          return 0;
        case (($points >= 13) && ($points <= 17)):
          return 5;
        case (($points >= 1) && ($points <= 12)):
          return 10;
        default: 
          return 0;
      }
    }
    else {
      switch (true) {
        case ($points >= 16):
          return 0;
        case (($points >= 11) && ($points <= 15)):
          return 5;
        case (($points >= 1) && ($points <= 10)):
          return 10;
        default: 
          return 0;
      }
    }
  }

  /**************************************************************************
    retorna un llistat de les dates en que s'han fet els atacs 
  **************************************************************************/
  private function dates() {
    if (!$this->checkAllyCode($this->allyCode)) {
      return $this->getHelp("tw", $this->translatedText("error3", $this->allyCode));  // $this->error = "The ".$params[2]." parameter is a bad AllyCode parameter. See help...\n\n"; 
    }

    $player = $this->getInfoPlayer();
    
    // mirem que haguem trobat Id Guild
    if ($player[0]["guildRefId"] == "") {
      return $this->translatedText("error6");                                   // "Ooooops! API server may have shut down. Try again later.\n\n"
    }
  
    // conectem a la base de dades
    $idcon = new mysqli($this->dataObj->bdserver, $this->dataObj->bduser, $this->dataObj->bdpas, $this->dataObj->bdnamebd);
    if ($idcon->connect_error) {
      return $this->translatedText("error4");                                   // "Ooooops! An error has occurred getting data.\n\n";
    }
      
    // cerquem registres
    $sql  = "SELECT * FROM tw WHERE guildRefId = '".$player[0]["guildRefId"]."' and unittype in ('off') order by name, datectrl";
    $res = $idcon->query( $sql );
    if ($idcon->error) {
      return $this->translatedText("error4");                                   // "Ooooops! An error has occurred getting data.";
    }
      
    $arr = array();
    $current = '';
    $first = false;
    while ($row = $res->fetch_assoc()) {
      if ($current != $row['name']) {
        $first = true;
        $current = $row['name'];
      }
      $arr[] = array(
                     'name' => $row['name'],
                     'date' => $row['datectrl'],
                     'first' => $first
                    );
      $first = false;
    }	
    $idcon->close(); 
      
    // ordenem
    switch (strtolower($this->sort)) {
      case 'date':
        usort($arr, function($a, $b) {
          if ($a['date'] == $b['date']) {
            return strtoupper($a['name']) <=> strtoupper($b['name']);
          }
          return $a['date'] <=> $b['date'];
        });
        break;
      default:
        uasort($arr, function($a, $b) {
          if ($a['name'] == $b['name']) {
            return strtoupper($a['date']) <=> strtoupper($b['date']);
          }
          return strtoupper($a['name']) <=> strtoupper($b['name']);
        });
        break;
    }
    
    // imprimim   
    $ret = array();
    $idx = 0;
    $ret[$idx]  = $this->translatedText("txtTw33", $player[0]["guildName"]);          // "TW attacks for %s \n\n";
    $ret[$idx] .= "date|name \n";
    $ret[$idx] .= "<pre>";
    
    foreach ($arr as $data) {
      if ($ret[$idx] == "")
        $ret[$idx] = "<pre>";
      
      $ret[$idx] .= $data['date']."|".
                    $data['name'];
      if ($data['first']) {
        $ret[$idx] .= "(*)";
      }
      $ret[$idx] .= "\n";

      if (strlen($ret[$idx]) > $this->dataObj->maxChars) {
        $ret[$idx] .= "</pre>";
        $idx++;
      }
    }
    $ret[$idx] .= "</pre>";
    $ret[$idx] .= "\n";
    
    return $ret;
  }

  /**************************************************************************
    afegeix allycodes com a no registrats en la TW per a no tenir-los en compte 
  **************************************************************************/
  private function noreg() {
    // mirem si volem llistar
    if (strcasecmp($this->params[1], 'list') == 0 ) {
      return $this->listNoReg();
    }
      
    // posem allycodes en un array
    $arr = explode(',', $this->params[1]);
    
    // agafem dades de la guild
    $guild = $this->getInfoGuild();
      
    // comprovem la existència dels allycodes
    $notfound = array();
    $yesfound = array();
    foreach ($arr as $ally) {
      $found = false;
      foreach ($guild[0]['roster'] as $player) {
        if ($player['allyCode'] == $ally) {
          $found = true;
          $yesfound[$ally] = $player['name'];
        }
      }
      if ($found == false)
        array_push($notfound, $ally);
    }
    
    // i hi ha algun allycode incorrecte, els mostrem i sortim
    if (count($notfound) > 0) {
      $ret = $this->translatedText("txtTw48", $guild[0]["name"]);               // "There are some allycodes incorrects for %s:\n\n";
      foreach ($notfound as $ally) {
        $ret .= $ally."\n"; 
      }
      $ret .= "\n";
      $ret .= $this->translatedText("txtTw49");                                 // "All allycodes are discarted!\n\n";
      return $ret;
    }

    // si no hi ha allicodes incorrectes, els afegim
    // conectem a la base de dades
    $idcon = new mysqli($this->dataObj->bdserver, $this->dataObj->bduser, $this->dataObj->bdpas, $this->dataObj->bdnamebd);
    if ($idcon->connect_error) {echo "\n\n";
      return $this->translatedText("error4");                                   // "Ooooops! An error has occurred getting data.\n\n";
    }
      
    // afegim registre
    $sql  = "INSERT INTO twconf (guildRefId, noreg) ";
    $sql .= "VALUES ('".$guild[0]["id"]."', '".$this->params[1]."') ";
    $sql .= "ON DUPLICATE KEY UPDATE noreg='".$this->params[1]."'";
    $idcon->query( $sql );
    if ($idcon->error) 
      return $this->translatedText("error4");                                   // $ret = "Ooooops! An error has occurred getting data.";
    
    $idcon->close(); 

    // retornem allycodes/players afegits
    $ret = $this->translatedText("txtTw50", $guild[0]["name"]);                 // "These allycodes have been added for %s:\n\n";
    foreach ($yesfound as $ally => $player) {
      $ret .= $ally." - ".$player."\n"; 
    }
    $ret .= "\n";
    return $ret;
  }
  
  /**************************************************************************
    llista els allycodes no registrats en la TW 
  **************************************************************************/
  private function listNoReg() {
    // agafem dades de la guild
    $guild = $this->getInfoGuild();
      
    // conectem a la base de dades
    $idcon = new mysqli($this->dataObj->bdserver, $this->dataObj->bduser, $this->dataObj->bdpas, $this->dataObj->bdnamebd);
    if ($idcon->connect_error) {echo "\n\n";
      return $this->translatedText("error4");                                   // "Ooooops! An error has occurred getting data.\n\n";
    }
      
    // busquem registre
    $sql  = "SELECT noreg FROM twconf WHERE guildRefId = '".$guild[0]["id"]."'";
    $res = $idcon->query( $sql );
    if ($idcon->error) {
      return $this->translatedText("error4");                                   // $ret = "Ooooops! An error has occurred getting data.";
    }
    
    // busquem noms
    $players = array();
    $row = $res->fetch_assoc();
    $arrAllys = explode(',', $row['noreg']);
    foreach ($arrAllys as $ally) {
      foreach ($guild[0]['roster'] as $player) {
        if ($player['allyCode'] == $ally) {
          $players[$ally] = $player['name'];
        }
      }
    }
    
    $idcon->close(); 
    
    // imprimim resultat
    $ret = $this->translatedText("txtTw51", $guild[0]["name"]);                 // "There are some allycodes incorrects for %s:\n\n";
    foreach ($players as $ally => $player) {
      $ret .= $ally." - ".$player."\n"; 
    }
    $ret .= "\n";
    return $ret;
  }
  
  /**************************************************************************
    retorna un array amb els allycodes no registrats en la TW 
  **************************************************************************/
  private function getNoReg() {
    // agafem dades de la guild
    $guild = $this->getInfoGuild();
      
    // conectem a la base de dades
    $idcon = new mysqli($this->dataObj->bdserver, $this->dataObj->bduser, $this->dataObj->bdpas, $this->dataObj->bdnamebd);
    if ($idcon->connect_error) {echo "\n\n";
      return array();
    }
      
    // busquem registre
    $sql  = "SELECT noreg FROM twconf WHERE guildRefId = '".$guild[0]["id"]."'";
    $res = $idcon->query( $sql );
    if ($idcon->error) {
      return array();
    }
    
    // busquem noms
    $row = $res->fetch_assoc() ?? [];

    return explode(',', $row['noreg'] ?? '');
  }

    private function check(?string $teamAlias = null, ?string $allyCode = null, bool $onlyPending = false)
    {
        if (!is_null($allyCode)) {
            $this->actAsUser($allyCode);
        }

        $player = $this->getInfoPlayer();

        $stats = $this->loadGuildRequirements($this->dataObj->guildId, $teamAlias);
        $unitsToLoadStats = $stats->unitIds();

        if (count($unitsToLoadStats) === 0) {
            return $this->translatedText('txtTwCheck2');
        }

        $playerRosterWithStats = $this->playerStats($player[0], $unitsToLoadStats);

        $header = $this->translatedText('txtTwCheck1', [ $player[0]["guildName"], $player[0]["name"]]);
        $now = new DateTimeImmutable();
        $updated = (new DateTimeImmutable())->setTimestamp(intval($player[0]['updated'] / 1000));
        $nextUpdate = $updated->add(new DateInterval('PT4H'));
        $ago = $updated->diff($now)->format('%Hh %Im');
        $next = $now->diff($nextUpdate)->format('%Hh %Im');
        $footer = $this->translatedText('txtTwCheck3', [ $ago, $next ]);

        return [
            $header,
            ...$stats->playerReport($playerRosterWithStats, $onlyPending),
            $footer
        ];
    }

    private function checkShow($teamAlias = null, $allyCode = null)
    {
        if (!is_null($allyCode)) {
            $this->actAsUser($allyCode);
        }

        $player = $this->getInfoPlayer();
        $stats = $this->loadGuildRequirements($this->dataObj->guildId, $teamAlias);
        $header = $this->translatedText('txtTwCheckShow1', [ $this->dataObj->guildName, $player[0]['name'] ]);

        return [
            $header,
            ...$stats->show()
        ];
    }

    private function checkDel(string $teamAlias, ?string $allyCode)
    {
        if (!is_null($allyCode)) {
            $this->actAsUser($allyCode);
        }
        if (!$this->isGuildOfficer()) {
            return $this->translatedText('error7');
        }
        $this->deleteGuildRequirement($this->dataObj->guildId, $teamAlias);

        return $this->translatedText('txtTwCheckDel1', [ $this->dataObj->guildName, $teamAlias ]);
    }

    private function checkSave(string $teamAlias, string $definition, ?string $allyCode)
    {
        if (!is_null($allyCode)) {
            $this->actAsUser($allyCode);
        }

        if (!$this->isGuildOfficer()) {
            return $this->translatedText('error7');
        }

        $requirements = new RequirementCollection(
            $definition,
            new ApiUnitRepository(__DIR__, $this->dataObj->language),
            new MemoryStatService($this->dataObj)
        );

        $this->saveGuildRequirement($this->dataObj->guildId, $teamAlias, $definition);

        return [
            $this->translatedText('txtTwCheckSave1', [ $this->dataObj->guildName, $teamAlias ]),
            ...$requirements->show()
        ];
    }

    private function checkg()
    {
        /*
        if (!$this->checkAllyCode($this->allyCode)) {
            return $this->getHelp("tw", $this->translatedText("error3", $this->allyCode));  // $this->error = "The ".$params[2]." parameter is a bad AllyCode parameter. See help...\n\n";
        }

        $responses = [];

        $stats = $this->loadGuildRequirements();
        $unitsToLoadStats = $this->unitsInStats($stats);

        $guildStats = $this->guildStats($unitsToLoadStats);

        return $responses;
        */
    }

    private function deleteGuildRequirement(string $guildId, string $teamAlias)
    {
        $sql = "DELETE FROM guild_requirements WHERE guildRefId = '%s' AND alias = '%s'";
        $query = sprintf($sql, $guildId, $teamAlias);

        $this->fetchFromDb($query);
    }

    private function saveGuildRequirement(string $guildId, string $teamAlias, string $definition)
    {
        $insertSql = "DELETE FROM guild_requirements WHERE guildRefId = '%s' AND alias = '%s'";
        $insertQuery = sprintf($insertSql, $guildId, $teamAlias);
        $this->fetchFromDb($insertQuery);

        $insertSql = "INSERT INTO guild_requirements (guildRefId, alias, definition) VALUES ('%s', '%s', '%s')";
        $insertQuery = sprintf($insertSql, $guildId, $teamAlias, $definition);
        $this->fetchFromDb($insertQuery);
    }

}
