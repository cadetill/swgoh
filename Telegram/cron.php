<?php

  require_once 'translate.php';
  require_once 'SwgohHelp.php';
  require_once 'tbase_class.php';
  require_once 'config.php';
/*
class workerThread extends Thread {
  private $allycode;
  private $dataObj;

  public function __construct($allycode, $dataObj){
    $this->allycode = $allycode;
    $this->dataObj = $dataObj;
  }

  public function run() {
    $swgoh = new SwgohHelp(array($this->dataObj->swgohUser, $this->dataObj->swgohPass));
    $g = $swgoh->fetchGuild( $this->allycode, $this->dataObj->language );
    $guild = json_decode($g, true);

    $roster = ""; 
    foreach ($guild[0]["roster"] as $member) {
      if ($roster != "") 
        $roster .= ",";
      $roster .= $member["allyCode"];
    }

    if ($roster == "") exit;
    
    $players = $swgoh->fetchPlayer( $roster, $this->dataObj->language );
    file_put_contents("./".$guild[0]["id"], '{"Items":'.$players.'}');
  }
}
*/
class TCron extends TBase {

  /****************************************************
    constructor de la classe. Inicialitza variables
  ****************************************************/
  public function __construct($dataObj) {
    parent::__construct($dataObj);
  }

  /****************************************************
    FUNCIONS PUBLIQUES
  ****************************************************/
  /****************************************************
    executa el subcomando
  ****************************************************/
  public function execCommand() {
    if (!file_exists($this->dataObj->unitsFile)) {
      exit;
    }

    $fileContent = file_get_contents($this->dataObj->imFile);
    $guilds = json_decode($fileContent, true);
    
    foreach ($guilds as $guild) {
      $swgoh = new SwgohHelp(array($this->dataObj->swgohUser, $this->dataObj->swgohPass));
      $g = $swgoh->fetchGuild( $guild['allycode'], $this->dataObj->language );
      $guild = json_decode($g, true);

      $roster = ""; 
      $cont = 0;
      $p = array();
      foreach ($guild[0]["roster"] as $member) {
        if ($roster != "") 
          $roster .= ",";
        $roster .= $member["allyCode"];
        $cont++;

        if ($cont == 8) {
          $players = $swgoh->fetchPlayer( $roster, $this->dataObj->language );
          $arr = json_decode($players, true);
          $p = array_merge($p, $arr);
          $cont = 0;
          $roster = '';
        }
      }

      if ($roster != "") {
        $players = $swgoh->fetchPlayer( $roster, $this->dataObj->language );
        $arr = json_decode($players, true);
        $p = array_merge($p, $arr);
       }
      
      file_put_contents("./".$guild[0]["id"], '{"Items":'.json_encode($p, true).'}');
      sleep(2);
      //break;    
    }
  }
    
}


  $data = new TData;
  $cron = new TCron($data);
  $cron->execCommand();