<?php

class TStats extends TBase {
  private $subcomand;
  private $unit;
  private $dependences;
  private $validPre = array('s',   // speed
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
                            );

  public static function crinoloAliasFromPre($pre) {
      return [
          's'   => 'Speed',   // speed
          'hp'  => '',  // health+protection
          'h'   => 'Health',   // health
          'p'   => 'Protection',   // protection
          'pd'  => 'Physical Damage',  // physical damage
          'sd'  => 'Special Damage',  // special damage
          'po'  => 'Potency',  // potency
          't'   => 'Tenacity',   // tenacity
          'a'   => 'Armor',   // armor
          'pa'  => 'Physical Critical Avoidance',  // physical avoidance
          'sa'  => 'Special Critical Avoidance',  // special avoidance
          'pcc' => 'Physical Critical Chance', // physical critical chance
          'scc' => 'Special Critical Chance', // special critical chance
          'cd'  => 'Critical Damage'   // critical damage
      ][$pre];
  }

    public static function isPercentual($pre) {
        return [
            's'   => false,   // speed
            'hp'  => false,  // health+protection
            'h'   => false,   // health
            'p'   => false,   // protection
            'pd'  => false,  // physical damage
            'sd'  => false,  // special damage
            'po'  => true,  // potency
            't'   => false,   // tenacity
            'a'   => true,   // armor
            'pa'  => false,  // physical avoidance
            'sa'  => false,  // special avoidance
            'pcc' => true, // physical critical chance
            'scc' => true, // special critical chance
            'cd'  => false   // critical damage
        ][$pre];
    }
 
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
        $this->error = $this->translatedText("error1");                         // Bad request. See help: \n\n
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
      return $this->getHelp("stats", $this->error);
  
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
        $res = $this->getStats();
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
    afegeix una unitat Stats a la llista de Ststs
  ****************************************************/
  private function add() {
    if (($this->unit == "") || ($this->dependences == ""))
      $this->error = $this->translatedText("error1");                           // Bad request. See help: \n\n
    if ($this->error != "")
      return $this->getHelp("stats", $this->error);

    // mirem que existeixi la unitat
    $defId = TAlias::aliasSearch($this->unit, $this->dataObj);
    if ($defId == "") {
      return $this->translatedText("error2", $this->unit);                      // "Unit not found into the inventory.\n\n";
    }
    
    // agafem ID gremi
    $player = $this->getInfoPlayer();
    if ($player[0]["guildRefId"] == "") {
      return $this->translatedText("error6");                                   // "Ooooops! API server may have shut down. Try again later.\n\n"
    }
    $guildId = $player[0]["guildRefId"];
        
    // mirem que existeixin les unitats dependents (si existeixen agafem ja el seu ID)
    $units = explode(',', $this->dependences);
    $unitsFinal = array();
    foreach ($units as $key => $unit) { // $unit serà o alias o alias(requisits)
      // mirem si té requisits i els posem en un array
      $unitPre = array();
      if (strpos($unit, ')') > 0) { // si té paréntesi té requisit
        $unitTmp = substr($unit, 0, -1);
  
        $pre = explode('(', $unitTmp);
        $unit = $pre[0];
        unset($pre[0]);
        $pre_only = explode(';', $pre[1]); // si té més d'un requisit aniran separats per ;
        foreach ($pre_only as $p) {
          $p = explode('=', $p);  
          if (!in_array($p[0], $this->validPre)) {
            return $this->translatedText("staterr3", $unit);                    // "Incorrect prerequisite.\n\n";
          }
          $unitPre[$p[0]] = $p[1];
        }
      }
      // busquem Id unitat
      $defIdUnit = TAlias::aliasSearch($unit, $this->dataObj);
      if ($defIdUnit == ""){
        return $this->translatedText("error2", $unit);                          // "Unit not found into the inventory.\n\n";
      }
      $unitsFinal[$defIdUnit] = $unitPre;
    }

    // conectem a la base de dades
    $idcon = new mysqli($this->dataObj->bdserver, $this->dataObj->bduser, $this->dataObj->bdpas, $this->dataObj->bdnamebd);
    if ($idcon->connect_error) 
      return $this->translatedText("error4");                                   // "Ooooops! An error has occurred getting data.\n\n";

    // recuperem registre de la base de dades
    $sql  = "SELECT * FROM stats where guildRefId = '".$guildId."' and team = '".$defId."'";
    $res = $idcon->query( $sql );
    $statArr = $res->fetch_assoc();
    if (!is_array($statArr)) {  // si no existeix
      $statArr = array();
    }
    else {
      $statArr = json_decode($statArr['units'], true);
      if (!is_array($statArr))  // si no existeix
        $statArr = array();
    }

    // si tots els alias existeixen, l'afegim/modifiquem
    $statArr[$defId] = $unitsFinal;
    $sql  = "INSERT INTO stats (guildRefId, team, units) VALUES ('".$guildId."', '".$defId."', '".json_encode($statArr, true)."')";
    $sql .= "ON DUPLICATE KEY UPDATE units='".json_encode($statArr, true)."'";
    $idcon->query( $sql );
    $idcon->close(); 

    // imprimim sortida
    $ret  = $this->translatedText("txtStats1");                                 // "Stat unit added\n\n";
    $ret .= "  - ".TUnits::unitNameFromUnitId($defId, $this->dataObj)."\n";
    $ret .= "\n";
    $ret .= $this->translatedText("txtStats2");                                 // "<b>Units to control</b>\n";
    foreach ($unitsFinal as $key => $unit) {
      $ret .= "  - ".TUnits::unitNameFromUnitId($key, $this->dataObj)."\n";
      foreach ($unit as $hab => $value) {
        $ret .= $this->translatedText("txtStats16", array($this->getDescHability($hab), $value));  // "    + %s: %s\n";
      }
    }
    $ret .= "\n";

    return $ret;
  }
  
  /****************************************************
    esborra una unitat Stats de la llista de Stats
  ****************************************************/
  private function del() {
    // agafem ID gremi
    $player = $this->getInfoPlayer();
    if ($player[0]["guildRefId"] == "") {
      return $this->translatedText("error6");                                   // "Ooooops! API server may have shut down. Try again later.\n\n"
    }
    $guildId = $player[0]["guildRefId"];

    // busquem ID
    $defId = TAlias::aliasSearch($this->unit, $this->dataObj);
    if ($defId == ""){
      return $this->translatedText("error2", $this->unit);                      // "Unit not found into the inventory.\n\n";
    }

    // conectem a la base de dades
    $idcon = new mysqli($this->dataObj->bdserver, $this->dataObj->bduser, $this->dataObj->bdpas, $this->dataObj->bdnamebd);
    if ($idcon->connect_error) 
      return $this->translatedText("error4");                                   // "Ooooops! An error has occurred getting data.\n\n";

    // esborrem registre
    $sql  = "DELETE FROM stats where guildRefId = '".$guildId."' and team = '".$defId."'";
    $idcon->query( $sql );
    $idcon->close(); 
    
    $ret = $this->translatedText("txtStats3", TUnits::unitNameFromUnitId($defId, $this->dataObj)); // "Stat unit '%s' was deleted.\n\n";
  
    return $ret;
  }
  
  /****************************************************
    llista les unitats de la llista Stats
  ****************************************************/
  private function list() {
    // agafem ID gremi
    $player = $this->getInfoPlayer();
    if ($player[0]["guildRefId"] == "") {
      return $this->translatedText("error6");                                   // "Ooooops! API server may have shut down. Try again later.\n\n"
    }
    $guildId = $player[0]["guildRefId"];

    // conectem a la base de dades
    $idcon = new mysqli($this->dataObj->bdserver, $this->dataObj->bduser, $this->dataObj->bdpas, $this->dataObj->bdnamebd);
    if ($idcon->connect_error) 
      return $this->translatedText("error4");                                   // "Ooooops! An error has occurred getting data.\n\n";

    // busquem registres 
    $sql  = "SELECT team FROM stats where guildRefId = '".$guildId."'";
    $res = $idcon->query( $sql );
    
    // creem array registres
    $teams = array();
    while ($row = $res->fetch_assoc()) {
      $teams[$row['team']] = TUnits::unitNameFromUnitId($row['team'], $this->dataObj);
    }
    
    $idcon->close(); 

    // ordenem array
    sort($teams, SORT_NATURAL | SORT_FLAG_CASE);

    // imprimim
    $ret = $this->translatedText("txtStats4");                                  //"<b>List of units</b>\n\n";
    foreach ($teams as $unit) {
      $ret .= "  - ".$unit."\n";
    }
    $ret .= "\n";
    return $ret;
  }
  
  /****************************************************
    genera el csv de l'equip pel Stats
  ****************************************************/
  private function getStats() {
    // busquem ID
    $defId = TAlias::aliasSearch($this->subcomand, $this->dataObj);
    if ($defId == ""){
      return $this->translatedText("error2", $this->subcomand);                 // "Unit not found into the inventory.\n\n";
    }
    
    // busquem dades del gremi
    if ($this->unit == "") {
      $guild = $this->getInfoGuild();
    }
    else {
      $guild = $this->getInfoGuild($this->unit);
    }
    $data = $this->getInfoGuildExtra($guild);

    // mirem que haguem trobat Id Guild
    if ($guild[0]["id"] == "") {
      return $this->translatedText("error6");                                   // "Ooooops! API server may have shut down. Try again later.\n\n"
    }

    // conectem a la base de dades
    $idcon = new mysqli($this->dataObj->bdserver, $this->dataObj->bduser, $this->dataObj->bdpas, $this->dataObj->bdnamebd);
    if ($idcon->connect_error) 
      return $this->translatedText("error4");                                   // "Ooooops! An error has occurred getting data.\n\n";

    // busquem registre
    $sql  = "SELECT * FROM stats where guildRefId = '".$guild[0]["id"]."' and team = '".$defId."'";
    $res = $idcon->query( $sql );
    
    $statArr = $res->fetch_assoc();
    if (!is_array($statArr)) {  // si no existeix
      $statArr = array();
    }
    else {
      $statArr = json_decode($statArr['units'], true);
      if (!is_array($statArr))  // si no existeix
        $statArr = array();
    }
    
    // preparem array vuit de la info necessaria per jugador
    $empty = $statArr[$defId];
    foreach ($empty as $key => $value) {
      foreach ($value as $k => $v) {
        $empty[$key][$k] = "";
      }
    }
    
//    $roster = array();
    foreach ($data as $player) {
      $roster[$player['name']] = $empty;
      foreach ($player['roster'] as $unit) {
        if (array_key_exists($unit['defId'], $statArr[$defId])) {
          foreach ($statArr[$defId][$unit['defId']] as $key => $value) {
            switch ($key) {
              case 's': $val = $unit['stats']['final']['Speed']; break;
              case 'hp': $val = $unit['stats']['final']['Health'] + $unit['stats']['final']['Protection']; break;
              case 'h': $val = $unit['stats']['final']['Health']; break;
              case 'p': $val = $unit['stats']['final']['Protection']; break;
              case 'pd': $val = $unit['stats']['final']['Physical Damage']; break;
              case 'sd': $val = $unit['stats']['final']['Special Damage']; break;
              case 'po': $val = round($unit['stats']['final']['Potency'] * 100, 2); break;
              case 't': $val = round($unit['stats']['final']['Tenacity'] * 100, 2); break;
              case 'a': $val = round($unit['stats']['final']['Armor'] * 100, 2); break;
              case 'pa': $val = round($unit['stats']['final']['Physical Critical Avoidance'] * 100, 2); break;
              case 'sa': $val = round($unit['stats']['final']['Special Critical Avoidance'] * 100, 2); break;
              case 'cd': $val = round($unit['stats']['final']['Critical Damage'] * 100, 2); break;
              case 'pcc': $val = round($unit['stats']['final']['Physical Critical Chance'] * 100, 2); break;
              case 'scc': $val = round($unit['stats']['final']['Special Critical Chance'] * 100, 2); break;
              default:
                $val = 0; break;
            }
            $roster[$player['name']][$unit['defId']][$key] = $val;
          } 
        }
      }
    }
    
    // generem CSV de sortida
    $fileName = $this->generateCSV($statArr[$defId], $roster);
    
    // imprimim resultat
    $ret = "";
    foreach ($statArr as $key => $val) {
      $ret .= TUnits::unitNameFromUnitId($key, $this->dataObj)."\n\n";
      $ret .= $this->translatedText("txtStats4");                                 // "<b>Lista de unidades</b>\n\n";
      
      foreach ($val as $k => $v) {
        $ret .= "  - ".TUnits::unitNameFromUnitId($k, $this->dataObj)."\n";
        foreach ($v as $hab => $value) {
          $ret .= $this->translatedText("txtStats16", array($this->getDescHability($hab), $value));  // "    + %s: %s\n";
        }
      }
    }
    $ret .= "\n";
    if ($fileName != "") {
      $ret .= $fileName."\n";
    }
    $ret .= "\n";

    return $ret;
  }

  /**************************************************************************
    genera un fitxer CSV temporal amb la info del team i retorna la ruta d'aquest
  **************************************************************************/
  private function generateCSV($units, $roster){
    $tempName = tempnam('./tmp/', 'CSV_');

    $file = fopen($tempName.".csv", 'w');
    if (!$file) {
      return "";  
    }

    $line = 'Player/unit';
    foreach ($units as $key => $value) {        
      foreach ($value as $k => $v) {
        if ($line != '') {
          $line .= ',';
        }
        $line .= TUnits::unitNameFromUnitId($key, $this->dataObj) . " " . $this->getDescHability($k) . " " . $v;
      }
    }
    fputs($file, $line.PHP_EOL);
    
    foreach ($roster as $player => $listUnits) {
      $line = $player;
      
      foreach ($listUnits as $value) {        
        foreach ($value as $v) {
          if ($line != '') {
            $line .= ',';
          }
          $line .= $v;
        }
      }
      fputs($file, $line.PHP_EOL);
    }

    fclose($file);
    return 'https://www.cadetill.com/swgoh/bot/tmp/'.basename($tempName).'.csv';
  }
  
  /****************************************************
    retorna la descripció de l'habilitat
  ****************************************************/
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

}
