<?php

class TRancor extends TBase {
  private $subcomand;
  private $percen;

  /****************************************************
    constructor de la classe. Inicialitza variables
  ****************************************************/
  public function __construct($params, $dataObj) {
    parent::__construct($dataObj);
    
    $this->percen = 0;
    $this->allyCode = $dataObj->allycode;
     
    // agafem el subcomando i l'extraem de $params
    $this->subcomand = explode(' ',trim($params[0]));
    $this->subcomand = strtolower($this->subcomand[1]);
    unset($params[0]);

    // actuem segons la quantitat de paràmetres
    switch (count($params)) {
      case 0: 
        break;
		
      case 1:
        $tmp = $params[1];
        if ($this->checkAllyCode($tmp)) {
          $this->allyCode = $tmp;
        } 
        else {
          $this->percen = $params[1];
          if ($this->percen == 0) {
            $this->percen = -99;
          }
        }
        break;
		
      case 2: 
        $this->percen = $params[1];
        if ($this->percen == 0) {
          $this->percen = -99;
        }
        $this->allyCode = $params[2];
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
      case 'clear':
        $res = $this->clear();
        break;
      case 'f1':
      case 'f2':
      case 'f3':
      case 'f4':
        if ($this->percen == 0) {
          $res = $this->countFase();
        }
        else {
          $res = $this->incFase();
        }
        break;
      default:
        return $this->getHelp("rancor");
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
    inicialitza Rancor
  ****************************************************/
  private function clear() {
    $player = $this->getInfoPlayer();
          
    // conectem a la base de dades
    $idcon = new mysqli($this->dataObj->bdserver, $this->dataObj->bduser, $this->dataObj->bdpas, $this->dataObj->bdnamebd);
    if ($idcon->connect_error) {
      return $this->translatedText("error4");                                   // "Ooooops! An error has occurred getting data.\n\n";
    }
          
    // esborrem posible contingut del gremi
    $sql = "delete from raids where guildRefId = '".$player[0]["guildRefId"]."'";
    $idcon->query( $sql );
    if ($idcon->error) {
      $idcon->close();
      return $this->translatedText("error4");                                   // "Ooooops! An error has occurred getting data.";
    }

    $idcon->close();
    return $this->translatedText("txtRancor1", $player[0]["guildName"]);        // "Rancor for ".$player[0]["guildName"]." has been initialized\n\n";
  }
  
  /****************************************************
    suma el percentatge de la fase Rancor
  ****************************************************/
  private function countFase() {
    $player = $this->getInfoPlayer();
          
    // conectem a la base de dades
    $idcon = new mysqli($this->dataObj->bdserver, $this->dataObj->bduser, $this->dataObj->bdpas, $this->dataObj->bdnamebd);
    if ($idcon->connect_error) {
      return $this->translatedText("error4");                                   // "Ooooops! An error has occurred getting data.\n\n";
    }
          
    // realitzem select sobre la taula
    $sql = "select * from raids where guildRefId = '".$player[0]["guildRefId"]."' and raid = 'rancor' and fase = '".$this->subcomand."' order by percen desc";
    $res = $idcon->query( $sql );
    if ($idcon->error) {
      $idcon->close();
      return $this->translatedText("error4");                                   // "Ooooops! An error has occurred getting data.";
    }

    $ret = $this->translatedText("txtRancor2", array($player[0]["guildName"], $this->subcomand));    // "Rancor para %s phase %s \n\n";
    $sum = 0;
    $first = true;
    $cont = 0;
    $ret .= "<pre>";
    while ($row = $res->fetch_assoc()) {
      $cont++;
      $ret .= str_pad(number_format($row['percen'], 2), 5, " ", STR_PAD_LEFT) . " - " . $row["name"]."\n";
      $sum += $row['percen'];
      if (($sum >= 100) && ($first)) {
        $ret .= ".......".number_format($sum, 2)." (".$cont.").......\n";
        $first = false;
      }
    }
    $ret .= "------------------\n";
    $ret .= "</pre>";
    $ret .= $this->translatedText("txtRancor3", array(number_format($sum, 2), $cont)); 
    
    $idcon->close();
    return $ret;
  }
  
  /****************************************************
    incrementa el percentatge de la fase Rancor
  ****************************************************/
  private function incFase() {
    $player = $this->getInfoPlayer();
          
    // conectem a la base de dades
    $idcon = new mysqli($this->dataObj->bdserver, $this->dataObj->bduser, $this->dataObj->bdpas, $this->dataObj->bdnamebd);
    if ($idcon->connect_error) {
      return $this->translatedText("error4");                                   // "Ooooops! An error has occurred getting data.\n\n";
    }
    
    $this->percen = str_replace(",", ".", $this->percen);
    if ($this->percen == -99) {
      $this->percen = 0;
    }
          
    // realitzem isnert sobre la taula
    $sql  = "insert into raids (guildRefId, raid, fase, allyCode, name, percen) ";
    $sql .= "values ('".$player[0]["guildRefId"]."', 'rancor', '".$this->subcomand."', '".$player[0]["allyCode"]."', '".$player[0]["name"]."', ".$this->percen.") ";
    $sql .= "ON DUPLICATE KEY UPDATE percen = ".$this->percen;
    $idcon->query( $sql );
    if ($idcon->error) {
      $idcon->close();
      return $this->translatedText("error4");                                   // "Ooooops! An error has occurred getting data.";
    }

    $ret = $this->translatedText("txtRancor4", array($player[0]["name"], $this->percen));    // "Rancor modified for %s with %s%\n\n"
    
    $idcon->close();
    return $ret;
  }
  
}
