<?php
class TTeams extends TBase {
  private $team = "";
  private $alias = "";
  private $guildId = "";
  private $guildName = "";
  private $ac = "";
  
  /****************************************************
    constructor de la classe. Inicialitza variables
  ****************************************************/
  public function __construct($params, $dataObj) {
    parent::__construct($dataObj);
    
    $this->allyCode = $dataObj->allycode;
    $this->ac = $dataObj->allycode;
     
    // agafem el subcomando i l'extraem de $params
    $this->subcomand = explode(' ',trim($params[0]));
    $this->subcomand = $this->subcomand[1];
    unset($params[0]);

    switch (count($params)) {
      case 0: break;
      case 1:
        $this->team = strtolower($params[1]);
        break;
      case 2:
        $this->team = strtolower($params[1]);
        $tmp = $params[2];
        if ($this->checkAllyCode($tmp)) {
          $this->ac = $tmp;
        } 
        else {
          $this->alias = $params[2];
        }
        break;
      case 3:
        $this->team = strtolower($params[1]);
        $this->alias = $params[2];
        $this->ac = $params[3];
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
    if ($this->error != "") {
      return $this->getHelp("teams", $this->error);
    }
  
    $initialTime = microtime(true);

    $player = $this->getInfoPlayer();
    if ($player[0]["guildRefId"] == "") {
      return $this->translatedText("error6");                                   // "Ooooops! API server may have shut down. Try again later.\n\n"
    }
    $this->guildId = $player[0]["guildRefId"];
    $this->guildName = $player[0]["guildName"];
    
    switch ($this->subcomand) {
      case 'add':
        $res = $this->add();
        break;
      case 'del':
        $res = $this->del();
        break;                                                 
      case 'list':                                
        $res = $this->list();
        break;
      case 'get':                                
        $res = $this->get();
        break;
      case 'addc':                                
        $res = $this->addc();
        break;
      case 'delc':                                
        $res = $this->delc();
        break;
      default:
        return $this->getHelp("teams", $this->translatedText("error1"));        // Bad request. See help: \n\n
    }

    if ($this->error != "")
      return $this->getHelp("teams", $this->error);
  
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
    afegeix o modifica un equipo
  **************************************************************************/
  private function add() {
    // busquem els Id dels alias passats (comma separated)
    $alias = explode(',', $this->alias);
    foreach ($alias as $key => $value) {
      $tmp = TAlias::aliasSearch($value, $this->dataObj);
      if ($tmp == "") {
        return $this->translatedText("error2", $value);                         // Unit '%s' not found into the inventory.\n\n
      }
      $alias[$key] = $tmp;
    }
  
    // conectem a la base de dades
    $idcon = new mysqli($this->dataObj->bdserver, $this->dataObj->bduser, $this->dataObj->bdpas, $this->dataObj->bdnamebd);
    if ($idcon->connect_error) {
      return $this->translatedText("error4");                                   // "Ooooops! An error has occurred getting data.\n\n";
    }
          
    // afegim registre
    $sql  = "INSERT INTO teams (guildRefId, team, units) ";
    $sql .= "VALUES ('".$this->guildId."', '".$this->team."', '".implode(',', $alias)."') ";
    $sql .= "ON DUPLICATE KEY UPDATE units='".implode(',', $alias)."'";  
      //echo "\n\n sql: ".$sql."\n\n";
          
    $idcon->query( $sql );
    if ($idcon->error) {
      return $this->translatedText("error4"); // $ret = "Ooooops! An error has occurred getting data.";
    }
    
    $idcon->close(); 
    
    $ret = $this->translatedText("txtTeams01", $this->guildName);               // "Team %s Add \n\n";
    $ret .= $this->translatedText("txtTeams07", $this->team);                   // "Team name: %s \n\n";
    $ret .= $this->translatedText("txtTeams02");                                // "<b>Units</b> \n";
    foreach ($alias as $value) {
      $ret .= "   ".TUnits::unitNameFromUnitId($value, $this->dataObj)."\n";
    }
    $ret .= "\n";

    return $ret;
  }
  
  /**************************************************************************
    esborra un equip definid
  **************************************************************************/
  private function del() {
    // conectem a la base de dades
    $idcon = new mysqli($this->dataObj->bdserver, $this->dataObj->bduser, $this->dataObj->bdpas, $this->dataObj->bdnamebd);
    if ($idcon->connect_error) {
      return $this->translatedText("error4");                                   // "Ooooops! An error has occurred getting data.\n\n";
    }
          
    // esborrem registre
    $sql  = "DELETE FROM teams WHERE guildRefId = '".$this->guildId."' and team = '".$this->team."'";
      //echo "\n\n sql: ".$sql."\n\n";
          
    $idcon->query( $sql );
    if ($idcon->error) {
      return $this->translatedText("error4");                                   // $ret = "Ooooops! An error has occurred getting data.";
    }
    if ($idcon->affected_rows == 0) {
      return $this->translatedText("teamserr1", array($this->team, $this->guildName)); // "Team %s not found for %s.\n\n";
    }
    
    $idcon->close(); 
    
    return $this->translatedText("txtTeams03", array($this->team, $this->guildName)); // "Team %s from %s has been deleted.\n\n";
  }
  
  /**************************************************************************
    llista els equips definits
  **************************************************************************/
  private function list() {
    // conectem a la base de dades
    $idcon = new mysqli($this->dataObj->bdserver, $this->dataObj->bduser, $this->dataObj->bdpas, $this->dataObj->bdnamebd);
    if ($idcon->connect_error) {
      return $this->translatedText("error4");                                   // "Ooooops! An error has occurred getting data.\n\n";
    }
          
    // recuperem registres
    $sql  = "SELECT * FROM teams WHERE guildRefId = '".$this->guildId."' and team <> 'units'";
      //echo "\n\n sql: ".$sql."\n\n";
          
    $res = $idcon->query( $sql );
    if ($idcon->error) {
      return $this->translatedText("error4");                                   // $ret = "Ooooops! An error has occurred getting data.";
    }
    
    $ret = $this->translatedText("txtTeams04", $this->guildName);               // "<b>Defined teams</b> for %s \n\n"
    while ($row = $res->fetch_assoc()) {
      $ret .= $row['team']." \n";
      $units = explode(',', $row['units']);
      $ret .= "<pre>";
      foreach ($units as $unitId) {
        $ret .= "  ".TUnits::unitNameFromUnitId($unitId, $this->dataObj)."\n";
      }
      $ret .= "</pre>\n";
    }
    
    return $ret;
  }
  
  /**************************************************************************
    afegeix un comando a l'equipo
  **************************************************************************/
  private function addc() {
    if (!in_array('/'.$this->alias, $this->dataObj->comands))
      return $this->translatedText("error5", $this->alias);                     // "Incorrect command %s.\n\n";
            
    // conectem a la base de dades
    $idcon = new mysqli($this->dataObj->bdserver, $this->dataObj->bduser, $this->dataObj->bdpas, $this->dataObj->bdnamebd);
    if ($idcon->connect_error) 
      return $this->translatedText("error4");                                   // "Ooooops! An error has occurred getting data.\n\n";
          
    // agafem registre de l'equip
    $sql = "SELECT * FROM teams WHERE guildRefId = '".$this->guildId."' and team = '".$this->team."'";
      //echo "\n\n sql: ".$sql."\n\n";
    $res = $idcon->query( $sql );

    // mirem si hi ha error i si hi ha registres
    if ($idcon->error) 
      return $this->translatedText("error4");                                   // $ret = "Ooooops! An error has occurred getting data.";
    if ($idcon->affected_rows == 0)
      return $this->translatedText("teamserr1", array($this->team, $this->guildName)); // "Team %s not found for %s.\n\n";
    
    // agafem comandos actuals i mirem que no hi estigui el nou
    $row = $res->fetch_assoc();
    $commands = explode(',', $row['command']);
    if (in_array($this->alias, $commands))
      return $this->translatedText("teamserr2");                                // "Command already exists.\n\n";

    // afegim el nou comando a l'equip
    array_push($commands, $this->alias);
    if ($commands[0] == '')
      array_shift($commands);
    $sql = "update teams set command = '".implode(',', $commands)."' WHERE guildRefId = '".$this->guildId."' and team = '".$this->team."'";
    $idcon->query( $sql );
    
    $idcon->close(); 
    
    $ret = $this->translatedText("txtTeams05", array($this->alias, $this->team));  // "Command %s added to team %s \n\n";
 
    return $ret;
  }
  
  /**************************************************************************
    esborra un comando de l'equip
  **************************************************************************/
  private function delc() {
    // conectem a la base de dades
    $idcon = new mysqli($this->dataObj->bdserver, $this->dataObj->bduser, $this->dataObj->bdpas, $this->dataObj->bdnamebd);
    if ($idcon->connect_error) 
      return $this->translatedText("error4");                                   // "Ooooops! An error has occurred getting data.\n\n";
          
    // agafem registre de l'equip
    $sql = "SELECT * FROM teams WHERE guildRefId = '".$this->guildId."' and team = '".$this->team."'";
      //echo "\n\n sql: ".$sql."\n\n";
    $res = $idcon->query( $sql );

    // mirem si hi ha error i si hi ha registres
    if ($idcon->error) 
      return $this->translatedText("error4");                                   // $ret = "Ooooops! An error has occurred getting data.";
    if ($idcon->affected_rows == 0)
      return $this->translatedText("teamserr1", array($this->team, $this->guildName)); // "Team %s not found for %s.\n\n";
    
    // agafem comandos actuals i esborrem el que ens demanen
    $row = $res->fetch_assoc();
    $commands = explode(',', $row['command']);
    $new = array();
    foreach ($commands as $value) {
      if ($value != $this->alias)
        array_push($new, $value);
    }
    $sql = "update teams set command = '".implode(',', $new)."' WHERE guildRefId = '".$this->guildId."' and team = '".$this->team."'";
    $idcon->query( $sql );   
    
    $idcon->close(); 
    
    $ret = $this->translatedText("txtTeams06", array($this->alias, $this->team));  // "Command %s deleted from team %s \n\n";
 
    return $ret;
  }
  
  /**************************************************************************
    agafa informació d'un determinat equip
  **************************************************************************/
  private function get() {
    $guild = $this->getInfoGuild($this->ac);
    $players = $this->getInfoGuildExtra($guild);
    if ($guild[0]["id"] == "") {
      return $this->translatedText("error6");                                   // "Ooooops! API server may have shut down. Try again later.\n\n"
    }
    
    // conectem a la base de dades
    $idcon = new mysqli($this->dataObj->bdserver, $this->dataObj->bduser, $this->dataObj->bdpas, $this->dataObj->bdnamebd);
    if ($idcon->connect_error) {
      return $this->translatedText("error4");                                   // "Ooooops! An error has occurred getting data.\n\n";
    }
          
    // recuperem registres per saber unitats del team
    $sql  = "SELECT * FROM teams WHERE guildRefId = '".$this->guildId."' and team = '".$this->team."' ";
    $res = $idcon->query( $sql );
    
    // gestió errors
    if ($idcon->error) 
      return $this->translatedText("error4");                                   // $ret = "Ooooops! An error has occurred getting data.";
    if ($idcon->affected_rows == 0) 
      return $this->translatedText("teamserr1", array($this->team, $this->guildName)); // "Team %s not found for %s.\n\n";
    
    // agafem Id d'unitats
    $row = $res->fetch_assoc();
    $units = explode(',', $row['units']);

    // omplim array $team amb la relació jugador, pg equip i unitats de l'equip
    $team = array();
    foreach ($players as $player) {
      $team[$player["name"]] = array(
                                     "name" => $player["name"],
                                     "gp" => 0
                                    );
      foreach ($units as $unit) {
        $u = $this->haveUnit($unit, $player["roster"]);  
        if ($u == "") {
          $team[$player["name"]][$unit] = '--';
        }
        else {
          if ($u["gear"] <> 13) {
            $team[$player["name"]][$unit] = 'g'.$u["gear"];
          }
          else {
            $team[$player["name"]][$unit] = 'r'.($u["relic"]["currentTier"] - 2);
          }
          $team[$player["name"]]["gp"] = $team[$player["name"]]["gp"] + $u["gp"];
        }
      }
    }
    
    $fileName = '';
    // ordenem 
    switch (strtolower($this->alias)) {
      case 'gp':
        usort($team, function($a, $b) {
          if ($a['gp'] == $b['gp'])
            return strtoupper($a['name']) <=> strtoupper($b['name']);
          return $a['gp'] < $b['gp'];
        });
        break;
      case 'csv':
        $fileName = $this->generateCSV($units, $team);
      default:
        uksort($team, 'strnatcasecmp');
    }
    
    // imprimim $team
//    $ret  = $this->guildName."\n\n";
    $ret = array();
    $pos = 0;
    $ret[$pos]  = $guild[0]['name']."\n\n";
    $ret[$pos] .= $this->translatedText("txtTeams08", $this->team);                   // "Team: %s \n\n";
    $ret[$pos] .= $this->translatedText("txtTeams09");                                // "Units: \n";
    $cont = 1;
    $title = "";
    $subtitle = "";
    foreach ($units as $unit) {        
      $ret[$pos] .= $this->translatedText("txtTeams10", array($cont, TUnits::unitNameFromUnitId($unit, $this->dataObj)));  // "  U%s: %s \n";
      if ($title != "")
        $title .= " |";
      $title .= "U".$cont;
      $subtitle .= "----";
      $cont++;
    }
    $ret[$pos] .= "\n";
    $ret[$pos] .= "<pre>";
    $ret[$pos] .= $title." |GP    \n";
    $ret[$pos] .= $subtitle."-------\n";
    
    foreach ($team as $player) {
      foreach ($units as $unit) {        
        $ret[$pos] .= str_pad($player[$unit], 3, " ", STR_PAD_LEFT)."|";
      }
      $ret[$pos] .= str_pad($player["gp"], 6, " ", STR_PAD_LEFT)." - ".$player['name']."\n"; 
      
      if (strlen($ret[$pos]) > $this->dataObj->maxChars) {
        $ret[$pos] .= "</pre>\n";
        $pos++;
        $ret[$pos] .= "<pre>";
      }
    }
    $ret[$pos] .= "</pre>\n";
    
    if ($fileName != "") {
      $ret[$pos] .= $fileName."\n";
    }
    $ret[$pos] .= "\n";
    
    return $ret;
  }
  
  /**************************************************************************
    genera un fitxer CSV temporal amb la info del team i retorna la ruta d'aquest
  **************************************************************************/
  private function generateCSV($units, $team){
    $tmpUnits = array();
    foreach ($units as $key => $unit) {
      $tmpUnits[$key] = TUnits::unitNameFromUnitId($unit, $this->dataObj);
    }
    
    $tempName = tempnam('./tmp/', 'CSV_');

    $file = fopen($tempName.".csv", 'w');
    if (!$file) {
      return "";  
    }
    
    fputs($file, 'player,gp,'.implode($tmpUnits, ',').PHP_EOL);
            
    foreach ($team as $player) {        
      fputs($file, implode($player, ',').PHP_EOL);
    }

    fclose($file);
    return 'https://www.cadetill.com/swgoh/bot/tmp/'.basename($tempName).'.csv';
  }
}
