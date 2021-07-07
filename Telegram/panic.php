<?php

class TPanic extends TBase {
  private $subcomand;
  private $unit;
  private $dependences;
  private $validPre = array('l','g','r','gp','s');
 
  /****************************************************
    constructor de la classe. Inicialitza variables
  ****************************************************/
  public function __construct($params, $dataObj) {
    parent::__construct($dataObj);
    
    $this->unit = '';
    $this->dependences = '';
    
    $this->allyCode = $dataObj->allycode;
     
    // agafem el subcomando i l'extraem de $params
    $this->subcomand = explode(' ',trim($params[0]));
    $this->subcomand = $this->subcomand[1];
    unset($params[0]);
    
    // actuem segons la quantitat de paràmetres
    switch (count($params)) {
      case 0: 
        break;
		
      case 1: 
        $this->unit = $params[1];
        break;
		
      case 2: // només per quan vingui de gf.php
        $this->unit = $params[1];
        $this->dependences = $params[2];
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
      return $this->getHelp("panic", $this->error);
  
    $initialTime = microtime(true);
    
    switch (strtolower($this->subcomand)) {
      case 'add':
        $res = $this->add();
        break;
      case 'del':
        $res = $this->del();
        break;
      case 'list':
        $res = $this->list();
        break;
      default:
        $res = $this->getPanic();
        return $res;
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
  /****************************************************
    afegeix una unidad Panic a la llista de Panics
  ****************************************************/
  private function add() {
    if (($this->unit == "") || ($this->dependences == ""))
      $this->error = $this->translatedText("error1");                           // Bad request. See help: \n\n
    if ($this->error != "")
      return $this->getHelp("panic", $this->error);

    // carreguem fitxer JSON de panic  
    $panicArr = $this->loadFile();

    // mirem que existeixi la unitat
    $defId = TAlias::aliasSearch($this->unit, $this->dataObj);
    if ($defId == "") {
      return $this->translatedText("error2", $this->unit);                      // "Unit not found into the inventory.\n\n";
    }
    
    // mirem que existeixin les unitats dependents (si existeixen agafem ja el seu ID)
    $units = explode(',', $this->dependences);
    $unitsFinal = array();
    foreach ($units as $key => $unit) { // $unit serà o alias o alias(prerequisits)
      // mirem si té prerequisits i els posem en un array
      $unitPre = array();
      if (strpos($unit, ')') > 0) { // si té paréntesi té prerequisit
        $unitTmp = substr($unit, 0, -1);
  
        $pre = explode('(', $unitTmp);
        $unit = $pre[0];
        unset($pre[0]);
        $pre_only = explode(',', $pre[1]); // si té més d'u nprerequisit aniran separats per comes
        foreach ($pre_only as $p) {
          $p = explode('=', $p);  
          if (!in_array($p[0], $this->validPre)) {
            return $this->translatedText("panicerr3", $unit);                     // "Incorrect prerequisite.\n\n";
          }
          $unitPre[$p[0]] = $p[1];
        }
      }
      
      // busquem Id unitat
      $defIdUnit = TAlias::aliasSearch($unit, $this->dataObj);
      if ($defIdUnit == ""){
        return $this->translatedText("error2", $unit);                          // "Unit not found into the inventory.\n\n";
      }
      $unitsFinal[$defIdUnit] = array("id" =>$defIdUnit, 
                                 "pre" => $unitPre);
    }

    // si tots els alias existeixen, l'afegim/modifiquem
    $panicArr[$defId] = $unitsFinal;
      
    // guardem fitxer i sortim
    $a = json_encode($panicArr, true);
    file_put_contents($this->dataObj->panicFile, $a);

    $ret  = $this->translatedText("txtPanic1");               // "Panic unit added\n\n";
    $ret .= "  - ".TUnits::unitNameFromUnitId($defId, $this->dataObj)."\n";
    $ret .= "\n";
    $ret .= $this->translatedText("txtPanic2", $this->unit);  // "<b>Dependency units</b>\n";
    foreach ($unitsFinal as $key => $unit) {
      $ret .= "  - ".TUnits::unitNameFromUnitId($unit["id"], $this->dataObj)."\n";
    }
    $ret .= "\n";

    return $ret;
  }
  
  /****************************************************
    esborra una unitat Panic de la llista de Panics
  ****************************************************/
  private function del() {
    // carreguem fitxer JSON de Panics  
    $panic = $this->loadFile();
  
    // busquem ID
    $defId = TAlias::aliasSearch($this->unit, $this->dataObj);
    if ($defId == ""){
      return $this->translatedText("error2", $this->unit);                      // "Unit not found into the inventory.\n\n";
    }

    // esborrem unitat Panic
    unset($panic[$defId]); 
  
    // guardem fitxer i sortim
    $a = json_encode($panic, true);
    file_put_contents($this->dataObj->panicFile, $a);
  
    $ret = $this->translatedText("txtPanic3", TUnits::unitNameFromUnitId($defId, $this->dataObj)); // "Alias ".$this->alias." was deleted.\n\n";
  
    return $ret;
  }
  
  /****************************************************
    llista les unitats de la llista Panic
  ****************************************************/
  private function list() {
    // carreguem fitxer JSON de Panics  
    $panic = $this->loadFile();
    
    // agafem noms d'unitats
    $list = array();
    foreach ($panic as $key => $units) {
      $list[$key] = TUnits::unitNameFromUnitId($key, $this->dataObj);
    }
    
    // ordenem array
    sort($list, SORT_NATURAL | SORT_FLAG_CASE);
    
    // imprimim
    $ret = $this->translatedText("txtPanic4");                                  //"<b>List of units</b>\n\n";
    foreach ($list as $key => $unit) {
      $ret .= "  - ".$unit."\n";
    }
    $ret .= "\n";
    return $ret;
  }
  
  /****************************************************
    genera la imatge de l'equip necessari pel Panic Farm
  ****************************************************/
  private function getPanic() {
    // carreguem fitxer JSON de Panics  
    $panic = $this->loadFile();

    // busquem ID
    $defId = TAlias::aliasSearch($this->subcomand, $this->dataObj);
    if ($defId == ""){
      return $this->translatedText("error2", $this->subcomand);                 // "Unit not found into the inventory.\n\n";
    }
    
    // mirem si existeix la unitat Panic 
    if (!array_key_exists($defId, $panic)) {
      return $this->translatedText("panicerr1", TUnits::unitNameFromUnitId($defId, $this->dataObj));  // "Unit '%s' not defined into Panic list.\n\n";
    }
    
    // busquem dades del jugador
    if ($this->unit == "") {
      $player = $this->getInfoPlayer();
    }
    else {
      $player = $this->getInfoPlayer($this->unit);
    }

    // mirem que haguem trobat Id Guild
    if ($player[0]["guildRefId"] == "") {
      return $this->translatedText("error6");                                   // "Ooooops! API server may have shut down. Try again later.\n\n"
    }
    
    // busquem les unitats dependents en el rooster del jugador
    $roster = array();
    foreach ($player[0]['roster'] as $unit) {
      if (array_key_exists($unit['defId'], $panic[$defId])) {
        $unit["botpre"] = $panic[$defId][$unit["defId"]]["pre"];
        array_push($roster, $unit);
      }
    }
    
    // generem imatge
    if (count($roster) > 0) {
      $image = $this->generateImage($roster);
      $this->sendPhoto('', $image, '');
      return '';
    }
    else {
      return $this->translatedText("panicerr2");                                // "No units found into your rooster.\n\n";
    }
  }

  /****************************************************
    llegeix el fitxer de farm i retorna array amb contingut
  ****************************************************/
  private function loadFile() {
    if (file_exists($this->dataObj->panicFile)) {
      $fileContent = file_get_contents($this->dataObj->panicFile);
      $panic = json_decode($fileContent, true);
    } else {
      $panic = array();
    }
    return $panic;
  }

}