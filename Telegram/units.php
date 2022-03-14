<?php

class TUnits extends TBase {
  private $subcomand;
  private $param;
  private $team;

  /****************************************************
    constructor de la classe. Inicialitza variables
  ****************************************************/
  public function __construct($params, $dataObj) {
    parent::__construct($dataObj);
    
    $this->allyCode = $dataObj->allycode;
    $this->team = 'units';
     
    // agafem el subcomando i l'extraem de $params
    $this->subcomand = explode(' ',trim($params[0]));
    $this->subcomand = $this->subcomand[1] ?? null;
    unset($params[0]);
    
    // actuem segons la quantitat de paràmetres
    switch (count($params)) {
      case 0: 
        break;
		
      case 1: 
        $this->param = $params[1];
        break;
		
      case 2: // només per quan vingui de gf.php
        $this->param = $params[1];
        $this->team = $params[2];
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
      return $this->getHelp("units", $this->error);
  
    $initialTime = microtime(true);
    
    switch (strtolower($this->subcomand)) {
      case 'update':
        $res = $this->updateUnits();
        break;
      case 'list':
        $res = $this->listUnits();
        break;
      case 'add':
        $res = $this->add();
        break;
      case 'del':
        $res = $this->del();
        break;
      case 'clear':
        $res = $this->clear();
        break;
      case 'listc':
        $res = $this->listc();
        break;
      case 'addc':
        return $this->addc();
      case 'delc':
        return $this->delc();
      default:
        return $this->getHelp("units");
    }

    if ($this->team != 'units')
      return $res;
    
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
    llegeix el fitxer d'alias i retorna array amb contingut
  ****************************************************/
  public function loadFile() {
    if (file_exists($this->dataObj->unitsFile)) {
      $fileContent = file_get_contents($this->dataObj->unitsFile);
      return json_decode($fileContent, true);
    } 
    else {
      $this->updateUnits();
      return loadFile();
    }
  }
   
  /****************************************************
    obté les unitats en l'idioma del jugador
  ****************************************************/
  public function createLanguage() {
    // carreguem fitxer JSON de units  
    $units = $this->loadFile();

    // creem objecte 
    $swgoh = new SwgohHelp(array($this->dataObj->swgohUser, $this->dataObj->swgohPass));
  
    $match = new stdClass();
    $match->rarity = 7;
    $match->obtainable = true;
    $match->obtainableTime = 0;
     
    $project = new stdClass();
    $project->baseId = 1;
    $project->nameKey = 1; 
    $project->combatType = 1;
     
    $newLangJson = $swgoh->fetchData("unitsList", $this->dataObj->language, $match, $project);
    $newLang = json_decode($newLangJson, true);
    
    foreach ($units as $key => $unit) {
      foreach ($newLang as $new) {
        if (strcasecmp($new["baseId"], $unit["baseId"]) == 0) {
          $units[$key][$this->dataObj->language] = $new["nameKey"];
          break;
        }
      }
    }
    file_put_contents($this->dataObj->unitsFile, json_encode($units, true));      
  }
  
  /****************************************************
    FUNCIONS PUBLIQUES ESTATIQUES
  ****************************************************/
  /**************************************************************************
    retorna un ID de unitat segons el nom d'aquesta localitzat
  **************************************************************************/
  public static function unitIdFromUnitName($unitName, $dataObj) {
    // carreguem fitxer si existeix. Si no el generem
    $unit = new TUnits(array(""), $dataObj);
    $units = $unit->loadFile();
      
    // si no existeix el idioma del jugador, el creem
    if (!array_key_exists($dataObj->language, $units[0])) {
      $unit->createLanguage();
      $units = $unit->loadFile();
    }
      
    // aquí ja existeix el idioma. Busquem la unitat
    foreach ($units as $u) {
      if (strcasecmp($u[$dataObj->language], $unitName) == 0) {
        return $u["baseId"];
      }
    }
    return "";
  }
  
  /**************************************************************************
    retorna el nom d'una unitat localitzat segon un ID
  **************************************************************************/
  public static function unitNameFromUnitId($unitId, $dataObj) {
    // carreguem fitxer si existeix. Si no el generem
    $unit = new TUnits(array(""), $dataObj);
    $units = $unit->loadFile();
      
    // si no existeix el idioma del jugador, el creem
    if (!array_key_exists($dataObj->language, $units[0])) {
      $unit->createLanguage();
      $units = $unit->loadFile();
    }
      
    // aquí ja existeix el idioma. Busquem la unitat
    foreach ($units as $u) {
      if (strcasecmp($u['baseId'], $unitId) == 0) {
        return $u[$dataObj->language];
      }
    }
    return "";
  }
  
  /**************************************************************************
    retorna si una unitat és una nau (true) o no (false)
  **************************************************************************/
  public static function isAShip($unitId, $dataObj) {
    // carreguem fitxer si existeix. Si no el generem
    $unit = new TUnits(array(""), $dataObj);
    $units = $unit->loadFile();
      
    // si no existeix el idioma del jugador, el creem
    if (!array_key_exists($dataObj->language, $units[0])) {
      $unit->createLanguage();
      $units = $unit->loadFile();
    }
      
    // aquí ja existeix el idioma. Busquem la unitat
    foreach ($units as $u) {
      if (strcasecmp($u['baseId'], $unitId) == 0) {
        return strcasecmp($u['combatType'], 'SHIP') == 0;
      }
    }
    return false;      
  }
  
  /**************************************************************************
    retorna si es Dark o Ligth side
  **************************************************************************/
  public static function getAlignment($unitId, $dataObj) {
    // carreguem fitxer si existeix. Si no el generem
    $unit = new TUnits(array(""), $dataObj);
    $units = $unit->loadFile();
      
    // si no existeix el idioma del jugador, el creem
    if (!array_key_exists($dataObj->language, $units[0])) {
      $unit->createLanguage();
      $units = $unit->loadFile();
    }
      
    // aquí ja existeix el idioma. Busquem la unitat
    foreach ($units as $u) {
      if (strcasecmp($u['baseId'], $unitId) == 0) {
        return $u['alignment'];
      }
    }
    return false;      
  }
  
  /**************************************************************************
    retorna un array amb les unitats a mostrar en un comando, o bé un string amb l'error
  **************************************************************************/
  public static function unitsForCommand($guildRefId, $team, $command, $dataObj) {
    // carreguem fitxer si existeix. Si no el generem
    $unit = new TUnits(array(""), $dataObj);
    return $unit->getUnitsForCommand($guildRefId, $team, $command);
  }

  /****************************************************
    FUNCIONS PRIVADES
  ****************************************************/
  /****************************************************
    llistat totes les unitats ordenades per tipus (nau/personatge)
  ****************************************************/
  private function listUnits() {
    // carreguem fitxer JSON de units  
    $units = $this->loadFile();
    
    // si no existeix el idioma del jugador, el creem
    if (!array_key_exists($this->dataObj->language, $units[0])) {
      $this->createLanguage();
      $units = $this->loadFile();
    }
      
    usort($units, function($a, $b) {
      if ($a['combatType'] == $b['combatType'])
        return strtoupper($a[$this->dataObj->language]) <=> strtoupper($b[$this->dataObj->language]);
      return $a['combatType'] < $b['combatType'];
    });

    $ret = array();
    $tmpStr = $this->translatedText("txtUnits1"); //"<b>List of units</b>\n";
    $tmp = "";
    foreach($units as $unit) {
      if ($tmp != $unit['combatType']) {
        $tmpStr .= "\n<b>".$unit['combatType']."</b>\n";
        $tmp = $unit['combatType'];
      }
      $tmpStr .= "   ".$unit[$this->dataObj->language]."\n";
      if (strlen($tmpStr) > $this->dataObj->maxChars) {
        array_push($ret, $tmpStr);
        $tmpStr = "";
      }
    }
    $tmpStr .= "\n";
    array_push($ret, $tmpStr);

    return $ret;
  }
  
  /****************************************************
    actualitza la llista d'unitats
  ****************************************************/
  private function updateUnits() {
    $swgoh = new SwgohHelp(array($this->dataObj->swgohUser, $this->dataObj->swgohPass));
      
    $match = new stdClass();
    $match->rarity = 7;
    $match->obtainable = true;
    $match->obtainableTime = 0;
      
    $project = new stdClass();
    $project->baseId = 1;
    $project->nameKey = 1; 
    $project->combatType = 1;
      
    // recuperem llistat d'unitats 
    $u = $swgoh->fetchData("unitsList", "SPA_XM", $match, $project);
    $unitsJson = str_replace("nameKey", "SPA_XM", $u);
    $units = json_decode($unitsJson, true);
    
    // agafem dades de swgoh.gg per saber "alignment"
    $charsJson = file_get_contents($this->dataObj->chars_gg);
    $chars = json_decode($charsJson, true);
    $fleetJson = file_get_contents($this->dataObj->fleet_gg);
    $fleets = json_decode($fleetJson, true);
    
    foreach ($units as $key => $unit) {        
      if (strcasecmp($unit['combatType'], 'CHARACTER') == 0) {
        foreach ($chars as $char) {
          if (strcasecmp($char['base_id'], $unit['baseId']) == 0) {
            $units[$key]['alignment'] = $char['alignment'];
            break;
          }
        }
      }
      else {
        foreach ($fleets as $fleet) {
          if (strcasecmp($fleet['base_id'], $fleet['baseId']) == 0) {
            $units[$key]['alignment'] = $fleet['alignment'];
            break;
          }
        }
      }
    }
    
    // guardem fitxer
    file_put_contents($this->dataObj->unitsFile, json_encode($units, true));
      
    $ret = $this->translatedText("txtUnits2");                                  // "Units have been updated.\n\n";
      
    return $ret;
  }
  
  /****************************************************
    afegeix una unitat a la llista de control
  ****************************************************/
  private function add() {
    $unitId = TAlias::aliasSearch($this->param, $this->dataObj);
    if ($unitId == "")
      return $this->translatedText("error2", $this->param);                     // Unit '%s' not found into the inventory.\n\n
    
    // conectem a la base de dades
    $idcon = new mysqli($this->dataObj->bdserver, $this->dataObj->bduser, $this->dataObj->bdpas, $this->dataObj->bdnamebd);
    if ($idcon->connect_error) 
      return $this->translatedText("error4");                                   // "Ooooops! An error has occurred getting data.\n\n";
          
    $player = $this->getInfoPlayer();

    // agafem registre de l'equip "units"
    $sql = "SELECT * FROM teams WHERE guildRefId = '".$player[0]["guildRefId"]."' and team = '".$this->team."' ";
    $res = $idcon->query( $sql );

    // mirem si hi ha error 
    if ($idcon->error) 
      return $this->translatedText("error4");                                   // $ret = "Ooooops! An error has occurred getting data.";
    
    if ($idcon->affected_rows == 0) { // no hi ha registre, l'afegim
      $sql = "INSERT INTO teams (guildRefId, team, units) VALUES ('".$player[0]["guildRefId"]."', '".$this->team."', '".$unitId."') ";
    }
    else {
      $row = $res->fetch_assoc();
      $units = explode(',', $row['units']);
      if (in_array($unitId, $units))
        return $this->translatedText("unitserr1", TUnits::unitNameFromUnitId($unitId, $this->dataObj));      // "Unit %s already exists.\n\n";
      
      array_push($units, $unitId);
      if ($units[0] == '')
        array_shift($units);
      $sql = "update teams set units = '".implode(',', $units)."' WHERE guildRefId = '".$player[0]["guildRefId"]."' and team = '".$this->team."' ";
    }
    $idcon->query( $sql );
    
    $idcon->close(); 
    
    $ret = $this->translatedText("txtUnits3", TUnits::unitNameFromUnitId($unitId, $this->dataObj));  // "Units %s added to the control unit list.\n\n";
 
    return $ret;
  }
  
  /****************************************************
    esborra una unitat de la llista de control
  ****************************************************/
  private function del() {
    $unitId = TAlias::aliasSearch($this->param, $this->dataObj);
    if ($unitId == "")
      return $this->translatedText("error2", $this->param);                     // Unit '%s' not found into the inventory.\n\n
    
    // conectem a la base de dades
    $idcon = new mysqli($this->dataObj->bdserver, $this->dataObj->bduser, $this->dataObj->bdpas, $this->dataObj->bdnamebd);
    if ($idcon->connect_error) 
      return $this->translatedText("error4");                                   // "Ooooops! An error has occurred getting data.\n\n";
          
    $player = $this->getInfoPlayer();

    // agafem registre de l'equip "units"
    $sql = "SELECT * FROM teams WHERE guildRefId = '".$player[0]["guildRefId"]."' and team = '".$this->team."' ";
    $res = $idcon->query( $sql );

    // mirem si hi ha error 
    if ($idcon->error) 
      return $this->translatedText("error4");                                   // $ret = "Ooooops! An error has occurred getting data.";
    if ($idcon->affected_rows == 0)
      return $this->translatedText("unitserr2", $this->team);                   // "No units to control.\n\n";
    
    // agafem units actuals i esborrem la que ens demanen
    $row = $res->fetch_assoc();
    $units = explode(',', $row['units']);
    $new = array();
    foreach ($units as $value) {
      if ($value != $unitId)
        array_push($new, $value);
    }
    $sql = "update teams set units = '".implode(',', $new)."' WHERE guildRefId = '".$player[0]["guildRefId"]."' and team = '".$this->team."' ";
    $idcon->query( $sql );   
    
    $idcon->close(); 
    
    $ret = $this->translatedText("txtUnits4", TUnits::unitNameFromUnitId($unitId, $this->dataObj));  // "Unit %s has ben deleted \n\n";
 
    return $ret;
  }
  
  /****************************************************
    esborra totes les unitats de la llista de control
  ****************************************************/
  private function clear() { 
    // conectem a la base de dades
    $idcon = new mysqli($this->dataObj->bdserver, $this->dataObj->bduser, $this->dataObj->bdpas, $this->dataObj->bdnamebd);
    if ($idcon->connect_error) 
      return $this->translatedText("error4");                                   // "Ooooops! An error has occurred getting data.\n\n";
          
    $player = $this->getInfoPlayer();

    $sql = "delete from teams WHERE guildRefId = '".$player[0]["guildRefId"]."' and team = '".$this->team."' ";
    $idcon->query( $sql );   
    
    $idcon->close();   
    
    $ret = $this->translatedText("txtUnits7", TUnits::unitNameFromUnitId($unitId, $this->dataObj));  // "Unit %s has ben deleted \n\n";
 
    return $ret;
  }
  
  /****************************************************
    llistat d'unitats a controlar
  ****************************************************/
  private function listc() {
    // conectem a la base de dades
    $idcon = new mysqli($this->dataObj->bdserver, $this->dataObj->bduser, $this->dataObj->bdpas, $this->dataObj->bdnamebd);
    if ($idcon->connect_error) 
      return $this->translatedText("error4");                                   // "Ooooops! An error has occurred getting data.\n\n";
          
    $player = $this->getInfoPlayer();

    // agafem registre de l'equip "units"
    $sql = "SELECT * FROM teams WHERE guildRefId = '".$player[0]["guildRefId"]."' and team = '".$this->team."' ";
    $res = $idcon->query( $sql );

    // mirem si hi ha error 
    if ($idcon->error) 
      return $this->translatedText("error4");                                   // $ret = "Ooooops! An error has occurred getting data.";
    if ($idcon->affected_rows == 0)
      return $this->translatedText("unitserr2", $this->team);                   // "No units to control.\n\n";
    
    $row = $res->fetch_assoc();
    // agafem units actuals i les imprimim
    $ret = $this->translatedText("txtUnits5", $player[0]["guildName"]);         // "Units to control for %s \n\n";
    $units = explode(',', $row['units']);
    foreach ($units as $value) {
      $ret .= "  - ".TUnits::unitNameFromUnitId($value, $this->dataObj)."\n";  
    }
    $ret .= "\n";
    
    if ($this->team != 'units')
      return $ret;

    // agafem comandos actuals i els imprimim
    $ret .= $this->translatedText("txtUnits6");                                 // "Commands \n\n";
    $commands = explode(',', $row['command']);
    foreach ($commands as $value) {
      if ($value != "")
        $ret .= "  - ".$value."\n";  
    }
    $ret .= "\n";
 
    return $ret;
  }
  
  /****************************************************
    afegeix un comando a la llista de control
  ****************************************************/
  private function addc() {
    $team = new TTeams(array('/teams addc', 'units', $this->param), $this->dataObj);
    return $team->execCommand();
  }
  
  /****************************************************
    borra un comando de la llista de control
  ****************************************************/
  private function delc() {
    $team = new TTeams(array('/teams delc', 'units', $this->param), $this->dataObj);
    return $team->execCommand();
  }
  
  /**************************************************************************
    retorna un array amb les unitats a controlar si el comando ho necessita
  **************************************************************************/
  private function getUnitsForCommand($guildRefId, $team, $command) {
    // conectem a la base de dades
    $idcon = new mysqli($this->dataObj->bdserver, $this->dataObj->bduser, $this->dataObj->bdpas, $this->dataObj->bdnamebd);
    if ($idcon->connect_error) 
      return $this->translatedText("error4");                                   // "Ooooops! An error has occurred getting data.\n\n";

    // agafem registre de l'equip "units"
    $sql = "SELECT * FROM teams WHERE guildRefId = '".$guildRefId."' and team = '".$team."' ";
    $res = $idcon->query( $sql );

    // mirem si hi ha error 
    if ($idcon->error) 
      return $this->translatedText("error4");                                   // "Ooooops! An error has occurred getting data.";
    // mirem si hi ha register
    if ($idcon->affected_rows == 0)
      return $this->translatedText("unitserr2", $this->team);                   // "No units to control.\n\n";
    
    $row = $res->fetch_assoc();
    $commands = explode(',', $row['command']);
    if (in_array($command, $commands)) {
      return explode(',', $row['units']);
    }
    else {
      return $this->translatedText("unitserr2", $this->team);                   // "No units to control.\n\n";
    }
  }
  
}
