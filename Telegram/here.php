<?php
class THere extends TBase {
  private $subcomand = "";
  private $team = "";
  private $user = "";
  private $guild = "";
  private $teams = array("ofis", "tw", "tb", "raids", "600", "recruiter", "bot", "leaders");
  private $generals = array ("recruiter", "bot");
    
  /****************************************************
    constructor de la classe. Inicialitza variables
  ****************************************************/
  public function __construct($params, $dataObj) {
    parent::__construct($dataObj);

    $this->allyCode = $dataObj->allycode;
    
    // agafem el subcomando 
    $this->subcomand = explode(' ',trim($params[0]));
    $this->subcomand = strtolower($this->subcomand[1]);
    unset($params[0]);
    
    $this->team = '';
    $this->user = '';
    $this->guild = '';
    switch (count($params)) {
      case 0: // list o tag a usuaris
        break;
      case 1: // list o tag a usuaris amb paràmetre guild
        $this->guild = strtolower($params[1]);
        break;
      case 2: // add o del
        $this->team = strtolower($params[1]);
        if (!in_array($this->team, $this->teams) || ($this->team == "all")) {
          $this->error = $this->translatedText("error1"); // Bad request. See help: \n\n
        }
        $this->user = $params[2];
        break;
      default:
        $this->error = $this->translatedText("error1"); // Bad request. See help: \n\n
    }  
  }

  /****************************************************
    FUNCIONS PUBLIQUES
  ****************************************************/
  /****************************************************
    executa el comando
  ****************************************************/
  public function execCommand() {
    if ($this->error != "") {
      return $this->getHelp("here", $this->error);
    }
  
    $initialTime = microtime(true);
    
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
      case 'recruiter':
      case 'bot':
      case 'tw':
      case 'tb':
      case 'ofis':
      case 'raids':
      case '600':
      case 'leaders':
        $res = $this->call();
        return array($res);
      default:
        return $this->getHelp("here");
    }

    $finalTime = microtime(true);
    $time = $finalTime - $initialTime;
    $res .= "<i>Elapsed time: ".gmdate("H:i:s", $time)."</i>\n";
    
    return array($res);
  }
  
  /****************************************************
    FUNCIONS PRIVADES
  ****************************************************/
  /****************************************************
    afegeix una persona a un grup
  ****************************************************/
  private function add() {
    // agafem informació del AllyCode per saber dades del gremi
    $player = $this->getInfoPlayer();

    // mirem que haguem trobat Id Guild
    if ($player[0]["guildRefId"] == "") {
      return $this->translatedText("error6");                                   // "Ooooops! API server may have shut down. Try again later.\n\n"
    }  
    
    // si és un team de caracter general, la guild será "general"
    if (in_array($this->team, $this->generals)) {
      $idGuild = 'general';
    } 
    else {
      $idGuild = $player[0]["guildRefId"];
    }
          
    // conectem a la base de dades
    $idcon = new mysqli($this->dataObj->bdserver, $this->dataObj->bduser, $this->dataObj->bdpas, $this->dataObj->bdnamebd);
    if ($idcon->connect_error) {
      return $this->translatedText("error4");                                   // "Ooooops! An error has occurred getting data.\n\n";
    }

    // mirem si l'usuari està donat d'alta al bot
    $sql = "SELECT * FROM users where username = '".$this->user."' ";
    $idcon->query( $sql );
    if ($idcon->error) {
      return $this->translatedText("error4");                                   // $ret = "Ooooops! An error has occurred getting data.";
    }
    if ($idcon->affected_rows == 0) { // no hi ha registre, error
      return $this->translatedText("hereerr2", $this->user);                    // "The %s user is not registered in the bot.\n\n";
    }
    
    // agafem registre del gremi i equip
    $sql = "SELECT * FROM here where refId = '".$idGuild."' and team = '".$this->team."'";
    $res = $idcon->query( $sql );
    if ($idcon->error) {
      return $this->translatedText("error4");                                   // $ret = "Ooooops! An error has occurred getting data.";
    }
    if ($idcon->affected_rows == 0) { // no hi ha registre, l'afegim
      $sql = "INSERT INTO here (refId, team, users) VALUES ('".$idGuild."', '".$this->team."', '".$this->user."') ";
    }
    else {
      $row = $res->fetch_assoc();
      $users = explode(',', $row['users']);
      if (in_array($this->user, $users)) {
        return $this->translatedText("hereerr1", array($this->user, $this->team));      // "User %s already exists into %s list.\n\n";
      }
      
      array_push($users, $this->user);
      if ($users[0] == '') {
        array_shift($users);
      }
      $sql = "update here set users = '".implode(',', $users)."' WHERE refId = '".$idGuild."' and team = '".$this->team."' ";
    }
    $idcon->query( $sql );
    
    $idcon->close(); 
    
    $ret = $this->translatedText("txtHere1", array($this->user, $this->team));  // "User %s added to the %s list.\n\n";
 
    return $ret;
  }
  
  /****************************************************
    esborra una persona d'un grup
  ****************************************************/
  private function del() {
    // conectem a la base de dades
    $idcon = new mysqli($this->dataObj->bdserver, $this->dataObj->bduser, $this->dataObj->bdpas, $this->dataObj->bdnamebd);
    if ($idcon->connect_error) {
      return $this->translatedText("error4");                                   // "Ooooops! An error has occurred getting data.\n\n";
    }
          
    // agafem informació del AllyCode per saber dades del gremi
    $player = $this->getInfoPlayer();
    
    // si és un team de caracter general, la guild será "general"
    if (in_array($this->team, $this->generals)) {
      $idGuild = 'general';
    } 
    else {
      $idGuild = $player[0]["guildRefId"];
    }

    // agafem registre del gremi i equip
    $sql = "SELECT * FROM here WHERE refId = '".$idGuild."' and team = '".$this->team."' ";
    $res = $idcon->query( $sql );

    // mirem si hi ha error 
    if ($idcon->error) {
      return $this->translatedText("error4");                                   // $ret = "Ooooops! An error has occurred getting data.";
    }
    if ($idcon->affected_rows == 0) {
      return $this->translatedText("hereerr3", $this->team);                    // "No users to control into list %s.\n\n";
    }
    
    // agafem usuaris actuals i esborrem el que ens demanen
    $row = $res->fetch_assoc();
    $users = explode(',', $row['users']);
    $new = array();
    foreach ($users as $value) {
      if ($value != $this->user)
        array_push($new, $value);
    }
    $sql = "update here set users = '".implode(',', $new)."' WHERE refId = '".$idGuild."' and team = '".$this->team."' ";
    $idcon->query( $sql );   
    
    $idcon->close(); 
    
    $ret = $this->translatedText("txtHere2", array($this->user, $this->team));  // "User %s has ben deleted from %s list.\n\n";
 
    return $ret;
  }
  
  /****************************************************
    llista les persones de les llistes
  ****************************************************/
  private function list() {
    // conectem a la base de dades
    $idcon = new mysqli($this->dataObj->bdserver, $this->dataObj->bduser, $this->dataObj->bdpas, $this->dataObj->bdnamebd);
    if ($idcon->connect_error) {
      return $this->translatedText("error4");                                   // "Ooooops! An error has occurred getting data.\n\n";
    }
    
    // si tenim definit un gremi, agafem AllyCode del líder
    if ($this->guild != "") {
      $allyCode = TIm::getAllyCodeFromAlias($this->guild, $this->dataObj); 
      if ($allyCode == "") {
        return $this->translatedText("hereerr4", [$this->guild]);               // "Ooooops! An error has occurred getting data.\n\n";
      }
      else {
        $this->allyCode = $allyCode;
      }
    }
    
    // agafem informació del AllyCode per saber dades del gremi
    $player = $this->getInfoPlayer();
      
    // agafem registres del gremi 
    $sql = "SELECT * FROM here WHERE refId in ('".$player[0]["guildRefId"]."', 'general') order by 1, 2 ";
    $res = $idcon->query( $sql );

    // mirem si hi ha error 
    if ($idcon->error) {
      return $this->translatedText("error4");                                   // $ret = "Ooooops! An error has occurred getting data.";
    }

    // recorrem llista i imprimim
    $ret = $this->translatedText("txtHere3", $player[0]["guildName"]);          // "Users to tag for %s \n\n";
    while ($row = $res->fetch_assoc()) {
      if ($row['users'] != '') {
        $ret .= $this->translatedText("txtHere4", $row["team"]);                  // "<b>List %s</b> \n";

        $users = explode(',', $row['users']);
        foreach ($users as $value) {
          $ret .= "  - ".$value."\n";  
        }
        $ret .= "\n";
      }
    }
 
    return $ret;
  }
  
  /****************************************************
    menciona les persones d'una llista
  ****************************************************/
  private function call() {
    // conectem a la base de dades
    $idcon = new mysqli($this->dataObj->bdserver, $this->dataObj->bduser, $this->dataObj->bdpas, $this->dataObj->bdnamebd);
    if ($idcon->connect_error) {
      return $this->translatedText("error4");                                   // "Ooooops! An error has occurred getting data.\n\n";
    }
    
    if ($this->subcomand == 'leaders') {
      // agafem AllyCodes dels líders
      $allyCode = TIm::getAllyCodes($this->dataObj); 
      $str = implode (",", $allyCode);
      
      // agafem registres del gremi 
      $sql = "SELECT username users FROM users where allycode in (".$str.") ";
    }
    else {
      // si tenim definit un gremi, agafem AllyCode del líder
      if ($this->guild != "") {
        $allyCode = TIm::getAllyCodeFromAlias($this->guild, $this->dataObj); 
        if ($allyCode == "") {
          return $this->translatedText("hereerr4", [$this->guild]);               // "Ooooops! An error has occurred getting data.\n\n";
        }
        else {
          $this->allyCode = $allyCode;
        }
      }
      
      // agafem informació del AllyCode per saber dades del gremi
      $player = $this->getInfoPlayer();
    
      // si és un team de caracter general, la guild será "general"
      if (in_array($this->subcomand, $this->generals)) {
        $idGuild = 'general';
      } 
      else {
        $idGuild = $player[0]["guildRefId"];
      }
      
      // agafem registre del gremi i team
      $sql = "SELECT * FROM here WHERE refId = '".$idGuild."' and team = '".$this->subcomand."' ";
    }
//echo $sql;
    $res = $idcon->query( $sql );

    // mirem si hi ha error 
    if ($idcon->error) {
      return $this->translatedText("error4");                                   // $ret = "Ooooops! An error has occurred getting data.";
    }
    if ($idcon->affected_rows == 0) {
      return;
    }
      
    // agafem usuaris i imprimim
    $ret = '';
    while ($row = $res->fetch_assoc()) {
      if ($row['users'] != '') {
        $users = explode(',', $row['users']);
        foreach ($users as $value) {
          $ret .= " @".$value;  
        }
      }
    }
 
    return $ret;
  }  
}
