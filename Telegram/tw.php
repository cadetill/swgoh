<?php
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
      default:
        return $this->getHelp("tw", $this->translatedText("error1"));  // Bad request. See help: \n\n
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
    if ($idcon->error) 
      return $this->translatedText("error4");      // "Ooooops! An error has occurred getting data.";
    else 
      return $this->translatedText("txtTw01", $player[0]["guildName"]);      // "TW for ".$player[0]["guildName"]." has been initialized\n\n";
    
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

    $ret  = $this->translatedText("txtTw02", $player[0]["guildName"]);        // "TW updated for ".$player[0]["guildName"]."\n\n";
    $ret .= $this->translatedText("txtTw03", $player[0]["name"]);             // "  Player: ".$player[0]["name"]."\n";
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
    
    if ($unitId == "") {
      return $this->translatedText("twerr1", $this->alias);                     // "Unit <i>%s</i> not found.\n\n";
    }
        
    if (TUnits::isAShip($unitId, $this->dataObj)) {
      $pointsBattle = 22;
    }
    else {
      $pointsBattle = 20;
    }
    
    $players = $this->getInfoGuildExtra();
    
    if ($unitId != "rogue") {
      $tmp = array();
//      echo 'players: '.count($players)."\n";
      
      // esborrem jugadors que no tinguin la unitat
      foreach ($players as $key => $value) {
        $found = false;
        if (key_exists('roster', $value)){
          foreach ($value['roster'] as $unit) {
            if ($unit['defId'] == $unitId) {
              $found = true;
            }
          }
        }
        
        if ($found) {
          $tmp[$key] = $players[$key];  
        }
//        echo 'tmp: '.count($tmp)."\n";
      }
      
      $players = $tmp;
      $players = array_values($players);
    }
    echo 'players: '.count($players)."\n";
    
    // mirem que haguem trobat Id Guild  
//    if ($players[0]["id"] == "") {        
    if ($players[0]["guildRefId"] == "") {
      return $this->translatedText("error6");                                   // "Ooooops! API server may have shut down. Try again later.\n\n"
    }
    
    // conectem a la base de dades
    $idcon = new mysqli($this->dataObj->bdserver, $this->dataObj->bduser, $this->dataObj->bdpas, $this->dataObj->bdnamebd);
    if ($idcon->connect_error) {
      return $this->translatedText("error4");                                   // return "Ooooops! An error has occurred getting data.\n\n";
    }
    
    // busquem registres
//    $sql  = "SELECT * FROM tw where guildRefId = '".$players[0]["id"]."' and unit = '".$unitId."'";
//    $sql  = "SELECT t.*, (select u.username from users u where u.allycode = t.allyCode limit 1) username FROM tw t where guildRefId = '".$players[0]["guildRefId"]."' and unit = '".$unitId."'";
    $sql  = "SELECT * FROM tw where guildRefId = '".$players[0]["guildRefId"]."' and unit = '".$unitId."'";
    $res = $idcon->query( $sql );
    $sumOff = 0;
    if ($idcon->error) {
      return $this->translatedText("error4");                                   // $ret = "Ooooops! An error has occurred getting data.";
    }
    
    $arrAtt = array();
    $arrDef = array();
    $arrUsed = array();
    $arrRogue = array();
    while ($row = $res->fetch_assoc()) {
      $found = false;
      foreach ($players as $key => $player) {
//      foreach ($players[0]["roster"] as $key => $player) {
        if ($row['allyCode'] == $player['allyCode']) {
          $found = true;
          switch ($row['unittype']) {
            case 'def':
              array_push($arrDef, $player['name'].'-'.$player['allyCode']);
              break;
            case 'used':
              array_push($arrUsed, $player['name'].'-'.$player['allyCode']);
              break;
            case 'off':
              array_push($arrAtt, $player['name'].'-'.$player['allyCode'].' ('.$row['points'].')');
              $sumOff = $sumOff + $row['points'];
              break;
            case 'rogue':
              array_push($arrRogue, $player['name'].'-'.$player['allyCode'].' ('.$row['points'].')');
              break;
          }
            
//          unset($players[0]["roster"][$key]);
          unset($players[$key]);
          break;
        }
      }
    }

    $arrNo = array();
//    foreach ($players[0]["roster"] as $player) {
    foreach ($players as $player) {
      $sql  = "SELECT * FROM users where allycode = '".$player['allyCode']."' limit 1";
      $res = $idcon->query( $sql );
      $row = $res->fetch_assoc();
      array_push($arrNo, '<code>'.$player['name'].'-'.$player['allyCode'].'</code> @'.$row['username']);
    }
    
    $idcon->close(); 
                
    usort($arrAtt, 'strnatcasecmp');
    usort($arrDef, 'strnatcasecmp');
    usort($arrUsed, 'strnatcasecmp');
    usort($arrNo, 'strnatcasecmp');    
    usort($arrRogue, 'strnatcasecmp');
            
//    $ret  = $this->translatedText("txtTw07", $players[0]["name"]);              // "<b>Guild</b>: ".$players[0]["name"]."\n";
    $ret  = $this->translatedText("txtTw07", $players[0]["guildName"]);         // "<b>Guild</b>: ".$players[0]["name"]."\n";
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
      if ($maxPoints == 0)
        $ret .= $this->translatedText("txtTw11", Array(count($arrAtt), $sumOff, 0, $tmp2));    // "<b>Used in offense</b>: ".count($arrAtt)." (".$sumOff." - ".number_format(($sumOff * 100) / $maxPoints, 2)."%)\n<pre>".$tmp2."</pre>\n";
      else
        $ret .= $this->translatedText("txtTw11", Array(count($arrAtt), $sumOff, number_format(($sumOff * 100) / $maxPoints, 2), $tmp2));    // "<b>Used in offense</b>: ".count($arrAtt)." (".$sumOff." - ".number_format(($sumOff * 100) / $maxPoints, 2)."%)\n<pre>".$tmp2."</pre>\n";
            
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
    $sql  = "SELECT * FROM tw WHERE allyCode = '". $this->allyCode."'";
           
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
      
    $ret .= $this->translatedText("txtTw19", array($sumOff, 
                                                   $sumOffTot, 
                                                   number_format(($sumOffTot * 100) / $maxPoints, 2)));       // "<b>Offense</b>: (%s/%s - %s/%s%)\n<pre>";
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
//    echo "\n\n\n\n";print_r($arr);echo "\n\n\n\n";
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
      
      if (strlen($ret[$idx]) > 4000) {
        $idx++;
      }
    }
    $ret[$idx] .= "\n";
    
//    file_put_contents("./review", $ret[0]);
//    return $ret[0];

 
    // visió CON
    usort($arr, function($a, $b) {
      if ($a['unit'] == $b['unit'])
        return strtoupper($a['vs']) <=> strtoupper($b['vs']);
      return $a['unit'] > $b['unit'];
    });
//    echo "\n\n\n\n";print_r($arr);echo "\n\n\n\n";
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
      
      if (strlen($ret[$idx]) > 4000) {
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
        
        if (TUnits::isAShip($row['unit'], $this->dataObj)) 
          $arr[$row['name']]['maxpoints'] = $arr[$row['name']]['maxpoints'] + 22;
        else
          $arr[$row['name']]['maxpoints'] = $arr[$row['name']]['maxpoints'] + 20;
      }
      else 
        $arr[$row['name']]['rogues'] = $arr[$row['name']]['rogues'] + $row['points'];
    }	
        
    $idcon->close(); 
        
    // calculem el %
    foreach ($arr as $key => $data) {
      $arr[$key]['percent'] = number_format(($data['points'] * 100)/$data['maxpoints'], 2);
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
    
    foreach ($arr as $data) {
      $ret .= str_pad($data['rogues'], 1, " ", STR_PAD_LEFT)."|".
              str_pad($data['off'], 2, " ", STR_PAD_LEFT)."|".
              str_pad($data['points'], 3, " ", STR_PAD_LEFT)."|".
              str_pad($data['percent'], 6, " ", STR_PAD_LEFT)." - ".
              $data['name']."\n";
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
    if ($players[0]["id"] == "")
      return $this->translatedText("error6");                                   // "Ooooops! API server may have shut down. Try again later.\n\n"
    
    // creem array amb tots els jugadors
    $arr = array();
    foreach ($players[0]['roster'] as $player) {
      $arr[$player['name']] = array(
                                    'name' => $player['name'],
                                    'count' => 0,
                                    'def' => 0
                                   );    
    }
  
    // conectem a la base de dades
    $idcon = new mysqli($this->dataObj->bdserver, $this->dataObj->bduser, $this->dataObj->bdpas, $this->dataObj->bdnamebd);
    if ($idcon->connect_error) 
      return $this->translatedText("error4");                                   // "Ooooops! An error has occurred getting data.\n\n";
      
    // cerquem registres
    $sql  = "SELECT *
             FROM tw 
             WHERE guildRefId = '".$players[0]["id"]."' and unittype in ('def')";
      
    $res = $idcon->query( $sql );
    if ($idcon->error) 
      return $this->translatedText("error4");                                   // "Ooooops! An error has occurred getting data.";
      
    while ($row = $res->fetch_assoc()) {
      if (!array_key_exists($row['name'], $arr)) 
        $arr[$row['name']] = array(
                                   'name' => $row['name'],
                                   'count' => 0,
                                   'def' => 0
                                  );
      
      $arr[$row['name']]['count'] = $arr[$row['name']]['count'] + 1;
      if (TUnits::isAShip($row['unit'], $this->dataObj)) 
        $arr[$row['name']]['def'] = $arr[$row['name']]['def'] + 34;
      else
        $arr[$row['name']]['def'] = $arr[$row['name']]['def'] + 30;
    }	
    $idcon->close(); 
      
    // ordenem
    switch (strtolower($this->sort)) {
      case 'teams':
        usort($arr, function($a, $b) {
          if ($a['count'] == $b['count'])
            return strtoupper($a['name']) <=> strtoupper($b['name']);
          return $a['count'] < $b['count'];
        });
        break;
      case 'points':
        usort($arr, function($a, $b) {
          if ($a['def'] == $b['def'])
            return strtoupper($a['name']) <=> strtoupper($b['name']);
          return $a['def'] < $b['def'];
        });
        break;
      default:
        usort($arr, function($a, $b) {
          return strtoupper($a['name']) <=> strtoupper($b['name']);
        });
        break;
    }
    
    // imprimim   
    $ret  = $this->translatedText("txtTw34", $player[0]["guildName"]);          // "TW defenses for ".$player[0]["guildName"]."\n\n";
    $ret .= "teams|points \n";
    $ret .= "<pre>";
    
    foreach ($arr as $data) {
      $ret .= str_pad($data['count'], 2, " ", STR_PAD_LEFT)."|".
              str_pad($data['def'], 3, " ", STR_PAD_LEFT)." - ".
              $data['name']."\n";
    }
    $ret .= "</pre>";
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
      
        if (TUnits::isAShip($row['unit'], $this->dataObj)) 
          $arr[$row['name']]['maxpoints'] = $arr[$row['name']]['maxpoints'] + 22;
        else
          $arr[$row['name']]['maxpoints'] = $arr[$row['name']]['maxpoints'] + 20;
      } 
      else 
        $arr[$row['name']]['rogues'] = $arr[$row['name']]['rogues'] + $row['points'];
    }	
      
    // calculem el %
    foreach ($arr as $key => $data) {
      $arr[$key]['percent'] = number_format(($data['points'] * 100)/$data['maxpoints'], 2);
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

      if (strlen($ret[$idx]) > 3000) {
        $ret[$idx] .= "</pre>";
        $idx++;
      }
    }
    $ret[$idx] .= "</pre>";
    $ret[$idx] .= "\n";
    
    return $ret;
  }
}
