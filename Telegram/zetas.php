<?php
class TZetas extends TBase {
 
  /****************************************************
    constructor de la classe. Inicialitza variables
  ****************************************************/
  public function __construct($params, $dataObj) {
    parent::__construct($dataObj);
    $this->allyCode = '';
    
    // actuem segons la quantitat de paràmetres
    switch (count($params)) {
      case 1:
        $this->allyCode = $dataObj->allycode;
        break;
		
      case 2: 
        $tmpStr = $params[1];
        if ($this->checkAllyCode($tmpStr))
          $this->allyCode = $tmpStr; 
        else
          $this->error = $this->translatedText("error3", $params[1]); // $this->error = "The ".$params[2]." parameter is a bad AllyCode parameter. See help...\n\n";
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
      return $this->getHelp("zetas", $this->error);
  
    $initialTime = microtime(true);
    
    $res = $this->getZetas();

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
    
    return array($res);
  }
  
  /****************************************************
    FUNCIONS PRIVADES
  ****************************************************/
  /****************************************************
    mostra informació básica del gremi
  ****************************************************/
  private function getZetas() {
    $player = $this->getInfoPlayer();
    
    // mirem que haguem trobat Id Guild
    if ($player[0]["guildRefId"] == "")
      return $this->translatedText("error6");                                   // "Ooooops! API server may have shut down. Try again later.\n\n"
      
    $zetasCount = 0;
    foreach ($player[0]["roster"] as $unit) {
      if ($unit["combatType"] == 1) {
        $tmp = "";
        $zcont = 0;
        foreach ($unit["skills"] as $skill) {
          if (($skill["isZeta"]) && ($skill["tier"] == $skill["tiers"])) {
            $zetasCount++;
            if ($tmp != "") $tmp .= "\n";
            
            if (strpos($skill["id"], "unique") !== false) 
              $tmp .= "(u)-".$skill["nameKey"];
            else {
              if (strpos($skill["id"], "leader") !== false) 
                $tmp .= "(l)-".$skill["nameKey"];
              else {
                if (strpos($skill["id"], "special") !== false) 
                  $tmp .= "(e)-".$skill["nameKey"];
                else 
                  $tmp .= "(b)-".$skill["nameKey"];
              }
            }
            
            $zcont++;
          }
        }
        
        if ($tmp != "") 
          $ret[$unit["nameKey"]." (".$zcont.")"] = $tmp;
      }
    }
      
    ksort($ret);
      
    $tmp = "";
    $r = array();
    foreach ($ret as $key => $val) {
      $tmp .= $key."\n<pre>".$val."</pre>\n\n";
      if (strlen($tmp) > 3900) {
        array_push($r, $tmp);
        $tmp = "";
      }
    }
    
    if ($tmp != "") {
      $tmp .= "\n";
      $tmp .= $this->translatedText("elapsed_time", date("d-m-Y H:i:s", substr($player[0]["updated"], 0, -3)));   // "<i>Last update: ".date("d-m-Y H:i:s", substr($player[0]["updated"], 0, -3))."</i>\n";
      
      array_push($r, $tmp);
    }
    $r[0] = $this->translatedText("txtZetas01", array($player[0]["name"], $zetasCount, $r[0]));   // "<b>Zetas for ".$player[0]["name"]."</b>: ".$zetasCount."\n\n".$r[0];
      
    return $r;
  }
}

      
      
      
/**************************************************************************
  }
  }
  }
  }
  function to get unloked zetas from AllyCode
**************************************************************************/
function getZetas($allyCode, $dataObj) {
  $allyCode = preg_replace('/[^0-9]+/', "", $allyCode); // traiem els caràcters que no son dígits.

  if (($allyCode < 100000000) || ($allyCode > 999999999)) {
    return array("The ".$allyCode." isn't a good ally code.\n");
  }
  
  $initialTime = microtime(true);

  $swgoh = new SwgohHelp(array($dataObj->swgohUser, $dataObj->swgohPass));
  $player = $swgoh->fetchPlayer( $allyCode, $dataObj->language );
  $player = json_decode($player, true);
    
  $zetasCount = 0;
  foreach ($player[0]["roster"] as $unit) {
    if ($unit["combatType"] == 1) {
      $tmp = "";
      $zcont = 0;
      foreach ($unit["skills"] as $skill) {
        if (($skill["isZeta"]) && ($skill["tier"] == $skill["tiers"])) {
          $zetasCount++;
          if ($tmp != "") $tmp .= "\n";
          if (strpos($skill["id"], "unique") !== false) {
            $tmp .= "(u)-".$skill["nameKey"];
          } else {
            if (strpos($skill["id"], "leader") !== false) {
              $tmp .= "(l)-".$skill["nameKey"];
            } else {
              if (strpos($skill["id"], "special") !== false) {
                $tmp .= "(e)-".$skill["nameKey"];
              } else {
                $tmp .= "(b)-".$skill["nameKey"];
              }
            }
          }
          $zcont++;
        }
      }
      if ($tmp != "") {
        $ret[$unit["nameKey"]." (".$zcont.")"] = $tmp;
      }
    }
  }
  
  ksort($ret);
  
  $tmp = "";
  $r = array();
  foreach ($ret as $key => $val) {
    $tmp .= $key."\n<pre>".$val."</pre>\n\n";
    if (strlen($tmp) > 3900) {
      array_push($r, $tmp);
      $tmp = "";
    }
  }
  if ($tmp != "") {
    $tmp .= "\n";
    $tmp .= "<i>Last update: ".date("d-m-Y H:i:s", substr($player[0]["updated"], 0, -3))."</i>\n";

    $finalTime = microtime(true);
    $time = $finalTime - $initialTime;
	$tmp .= "<i>Elapsed time: ".gmdate("H:i:s", $time)."</i>\n";

    array_push($r, $tmp);
  }
  $r[0] = "<b>Zetas for ".$player[0]["name"]."</b>: ".$zetasCount."\n\n".$r[0];
  
  return $r;
}
