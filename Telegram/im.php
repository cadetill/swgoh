<?php
class TIm extends TBase {
  private $subcomand = "";
  private $acronym = "";
  private $url = "";
  private $branch = "";
  private $alias = "";

  /****************************************************
    constructor de la classe. Inicialitza variables
  ****************************************************/
  public function __construct($params, $dataObj) {
    parent::__construct($dataObj);
    $this->allyCode = "";

    // agafem el subcomando 
    $this->subcomand = explode(' ',trim($params[0]));
    $this->subcomand = $this->subcomand[1];
    unset($params[0]);

    // actuem segons la quantitat de paràmetres
    switch (count($params)) {
      case 0: 
        break;

      case 1: 
        $this->acronym = $params[1];
        break;
		
      case 5: 
        $this->alias = $params[1];
        $this->acronym = $params[3];
        $this->url = $params[4];
        $this->branch = $params[5];
        $tmpStr = $params[2];
        if ($this->checkAllyCode($tmpStr)) {
          $this->allyCode = $tmpStr; 
        }
        else {
          $this->error = $this->translatedText("error3", $params[2]); // "The %s isn't a correct AllyCode.\n Read command help: \n\n";
          break;
        }
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
      return $this->getHelp("im", $this->error);
    }
  
    $initialTime = microtime(true);
    
    switch (strtolower($this->subcomand)) {
      case 'list':
        $res = $this->listIM();
        break;
      case 'add':
        $res = $this->addIM();
        break;
      case 'del':
        $res = $this->delIM();
        break;
      default:
        return $this->getHelp("im");
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
    llista tots els gremis de IM
  ****************************************************/
  private function listIM() {
    if ($this->error != "") {
      return $this->getHelp("im", $this->error);
    }
  
    // carreguem fitxer JSON de estructura IM  
    $im = TIm::loadFile($this->dataObj);
    
    // ordenem array
    ksort($im);
  
    // creem array amb tots els AllyCodes
    $codes = "";
    $guilds = array();
    foreach($im as $guild) {
      if ($codes == "") { 
        $codes = $guild["allycode"];
      } 
      else {
        $codes .= ','.$guild["allycode"];   
        $guilds = array_merge($guilds, $this->getInfoGuild($codes));
        $codes = '';
      }
    }
    if ($codes != '') {
      $guilds = array_merge($guilds, $this->getInfoGuild($codes));
    }
    
    //print_r($guilds);
    
    // mirem que haguem trobat Id Guild
    if ($guilds[0]["id"] == "") {
      return $this->translatedText("error6");                                   // "Ooooops! API server may have shut down. Try again later.\n\n"
    }
    
    // imprimim resultat
    $ret = $this->translatedText("txtIm01");                                    // "<b>Grupo łmperio Mandaloriano</b>\n";
    $ret .= "\n";
    $branch = "";
    foreach($im as $key => $i) {
      if ($branch == "") {
        $branch = $i["branch"];
      }
      if ($branch != $i["branch"]) {
        $ret .= "----------------------------------------\n\n";
      }
      $branch = $i["branch"];
      
      $players = 0;
      foreach ($guilds as $guild) {
        if (strcasecmp($guild["name"], $i["name"]) == 0) {
          $players = count($guild["roster"]);
          $gp = $guild["gp"];
          break;
        }
      }
      $ret .= "<b>".$i["name"]." (".$key.")</b>\n";
      $ret .= "<b>Alias</b>: ".$i["alias"]."\n";
      $ret .= $this->translatedText("txtIm13", $i["branch"]);                   // "<b>Branch</b>: ".$i["branch"]."\n";
      $ret .= $this->translatedText("txtIm02", $i["lider"]);                    // "<b>Leader</b>: ".$i["lider"]."\n";
      $ret .= $this->translatedText("txtIm12", $i["allycode"]);                 // "<b>AllyCode</b>: ".$i["allycode"]."\n";
      $ret .= $this->translatedText("txtIm03", $gp);                            // "<b>GP</b>: ".$gp."\n";
      $ret .= $this->translatedText("txtIm04", $players);                       // "<b>Players</b>: ".$players."\n";
      $ret .= $this->translatedText("txtIm05", $i["url"]);                      // "<b>url</b>: ".$i["url"]."\n";
      $ret .= "\n";
    }
  
    $ret .= "----------------------------------------\n\n";
    $ret .= "More Info:\n";
    $ret .= "https://docs.google.com/spreadsheets/d/1m0g8aa6qhmtv_J0mq9WafpcXIA256Z-r41h-4y3xitc/edit#gid=0\n\n";
	
    
    $this->sendPhoto('', $this->dataObj->imPhoto, '');
    return $ret;
  }

  /****************************************************
    afegeix o modifica un gremi a IM
  ****************************************************/
  private function addIM() {
    // carreguem fitxer JSON de estructura IM  
    $im = TIm::loadFile($this->dataObj);

    // agafem informació del AllyCode per saber dades del gremi
    $player = $this->getInfoPlayer();
  
    $im[$this->acronym] = array(
                          "alias" => strtolower($this->alias),
                          "branch" => $this->branch,
                          "name" => $player[0]["guildName"],
                          "guildRefId" => $player[0]["guildRefId"],
                          "lider" => $player[0]["name"],
                          "allycode" => $this->allyCode,
                          "url" => $this->url    // la url es posa posa a pelo en el .json resultant :(
                         );
    // guardem fitxer 
    $imNew = json_encode($im, true);
    file_put_contents($this->dataObj->imFile, $imNew);
    
    $ret = $this->translatedText("txtIm07");                                    // "<b>Guild added to IM</b>\n";
    $ret .= "\n";
    $ret .= $this->translatedText("txtIm13", $this->branch);                   // "<b>Branch</b>: ".$this->branch."\n";
    $ret .= $this->translatedText("txtIm08", $player[0]["guildName"]);          // "<b>Guild Name</b>: ".$player[0]["guildName"]."\n";
    $ret .= $this->translatedText("txtIm09", $this->acronym);                   // "<b>Acronym</b>: ".$this->acronym."\n";
    $ret .= $this->translatedText("txtIm10", $player[0]["name"]);               // "<b>Líder</b>: ".$player[0]["name"]."\n";
    $ret .= "<b>Url</b>: ".$this->url."\n";
    $ret .= "\n";
    
    return $ret;
  }

  /****************************************************
    esborra un gremi de IM
  ****************************************************/
  private function delIM() {
    // carreguem fitxer JSON de estructura IM  
    $im = TIm::loadFile($this->dataObj);
  
    $guildName = $im[$this->acronym]["name"];
    unset($im[$this->acronym]);
  
    // guardem fitxer 
    $imNew = json_encode($im, true);
    file_put_contents($this->dataObj->imFile, $imNew);

    $ret = $this->translatedText("txtIm11");                                    // "<b>Guild deleted from IM</b>\n";
    $ret .= "\n";
    $ret .= $this->translatedText("txtIm08", $guildName);                       // "<b>Guild Name</b>: ".$guildName."\n";
    $ret .= "\n";

    return $ret;
  }
  
  /****************************************************
    FUNCIONS PUBLIQUES ESTATIQUES
  ****************************************************/
  /****************************************************
    llegeix el fitxer d'alias i retorna array amb contingut
  ****************************************************/
  public static function loadFile($dataObj) {
    if (file_exists($dataObj->imFile)) {
      $imFile = file_get_contents($dataObj->imFile);
      $im = json_decode($imFile, true);
    } else {
      $im = array();
    }
    return $im;
  }

  /****************************************************
    retorna el AllyCode del "alias" donat
  ****************************************************/
  public static function getAllyCodeFromAlias($alias, $dataObj) {
    $im = TIm::loadFile($dataObj);  
    
    $aliasLC = strtolower($alias);
    
    foreach($im as $guild) {
      if ($guild["alias"] == $aliasLC) {
        return $guild["allycode"]; 
      }
    }
    
    return "";
  }

  /****************************************************
    retorna un array amb tots els AllyCodes 
  ****************************************************/
  public static function getAllyCodes($dataObj) {
    $im = TIm::loadFile($dataObj);  
    
    $ac = array();
    
    foreach($im as $guild) {
      $ac[] = $guild["allycode"];
    }
    
    return $ac;
  }

}
