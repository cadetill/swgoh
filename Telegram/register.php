<?php
class TRegister extends TBase {
  private $lang = 'ENG_US';
  private $langs = array('CHS_CN', 'CHT_CN', 'ENG_US', 'FRE_FR', 'GER_DE', 'IND_ID', 'ITA_IT', 'JPN_JP', 'KOR_KR', 'POR_BR', 'RUS_RU', 'SPA_XM', 'THA_TH', 'TUR_TR');
  
  /****************************************************
    constructor de la classe. Inicialitza variables
  ****************************************************/
  public function __construct($params, $dataObj) {
    parent::__construct($dataObj);
     
    switch (count($params)) {
      case 2:
        if (!$this->checkAllyCode($params[1])) {
            $this->error = $this->translatedText("error3", $params[1]); // "The %s isn't a correct AllyCode.\n";
        }
        $this->allyCode = $params[1];
        break;
      case 3:
        if (!$this->checkAllyCode($params[1])) {
            $this->error = $this->translatedText("error3", $params[1]); // "The %s isn't a correct AllyCode.\n";
        }
        $this->allyCode = $params[1];
        $this->lang = $params[2];
        if (!in_array($this->lang, $this->langs)) {
            $this->error = "Bad selected language. See help for more info.\n\n\n";
        }
        break;
      default:
        $this->error = $this->translatedText("error1"); // Bad request. See help: \n\n
    } 
  }
  
  /****************************************************
    realitza el registre d'un usuari
  ****************************************************/
  public function doRegister() {
    if ($this->error != "") {
        return $this->getHelp("register", $this->error);
    }
  
    $idcon = new mysqli($this->dataObj->bdserver, $this->dataObj->bduser, $this->dataObj->bdpas, $this->dataObj->bdnamebd);
    if ($idcon->connect_error) {
        return "Ooooops! An error has occurred saving data.";
    }
      
    $this->lang = strtoupper($this->lang);
      
    $sql  = "INSERT INTO users (id, username, name, allycode, language) ";
    $sql .= "VALUES(".$this->dataObj->userId.", \"".$this->dataObj->username."\", \"".$this->dataObj->firstname."\", ".$this->allyCode.", \"".$this->lang."\") ";
    $sql .= "ON DUPLICATE KEY UPDATE allycode=".$this->allyCode.", language=\"".$this->lang."\"";
    echo "\n\n".$sql."\n\n";
    $idcon->query( $sql );
      
    if ($idcon->error) {
      $ret = "Ooooops! An error has occurred saving data.";
    } else {
      $ret  = "The user with\n\n";
      $ret .= "   <b>Telegram ID</b>: ".$this->dataObj->userId."\n";
      $ret .= "   <b>NickName:</b> ".$this->dataObj->username."\n";
      $ret .= "   <b>Name</b>: ".$this->dataObj->firstname."\n";
      $ret .= "   <b>Ally Code</b>: ".$this->allyCode."\n";
      $ret .= "   <b>Language</b>: ".$this->lang."\n\n";
      $ret .= "was registered";
    }
    $idcon->close();
    return $ret;
  }
}

class TUnRegister extends TBase { 
  /****************************************************
    constructor de la classe. Inicialitza variables
  ****************************************************/
  public function __construct($params, $dataObj) {
    parent::__construct($dataObj);
     
    switch (count($params)) {
      case 1:
        break;
      default:
        $this->error = $this->translatedText("error1"); // Bad request. See help: \n\n
    } 
  }
  
  /****************************************************
    esborra el registre d'un usuari
  ****************************************************/
  function doUnRegister() {
    if ($this->error != "")
      return $this->getHelp("unregister", $this->error);
  
    $idcon = new mysqli($this->dataObj->bdserver, $this->dataObj->bduser, $this->dataObj->bdpas, $this->dataObj->bdnamebd);
    if ($idcon->connect_error) 
      return "Ooooops! An error has occurred saving data.";
      
    $sql = 'DELETE FROM users WHERE id ='.$this->dataObj->userId;
    $idcon->query( $sql );
    if ($idcon->error) 
      $ret = "Ooooops! An error has occurred saving data.";
    else 
      $ret = "The user with\n\n  <b>Telegram ID</b>: ".$this->dataObj->userId."\n\nwas unregistered";
  
    $idcon->close();
    return $ret;
  }
}
