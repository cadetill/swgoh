<?php
class TBase {
  public $dataObj;
  public $allyCode;
  public $trans;
  public $error = "";

  /****************************************************
    constructor de la classe. Inicialitza variables
  ****************************************************/
  public function __construct($dataObj) {
    $this->dataObj = $dataObj;
    $this->trans = new TTranslate($this->dataObj->language);
  }

  /****************************************************
    FUNCIONS PROTECTED
  ****************************************************/ 
  /****************************************************
    comprova si el AllyCode passatper paràmetre és correcte i el retorna sense caràcters que no siguin números 
  ****************************************************/
  protected function checkAllyCode(&$allyCode) {
    $allyCode = preg_replace('/[^0-9]+/', "", $allyCode); // traiem els caràcters que no son dígits.

    if (($allyCode < 100000000) || ($allyCode > 999999999)) 
      return false;  //"The ".$allyCode." isn't a good ally code.\n";
    else
      return true; 
  }
  
  /****************************************************
    recupera l'ajuda de la funció especificada
  ****************************************************/
  protected function getHelp($help, $message = "") {
    $ret = showHelp($help, $this->dataObj->language);
    if ($message == "")
      $ret = "Bad request. See help: \n\n".$ret[0]; 
    else
      $ret = $message.$ret[0]; 
    return $ret;
  }
  
  /****************************************************
    recupera la info d'un jugador
  ****************************************************/
  protected function getInfoPlayer($allyCode = "") {
    if ($allyCode == "")
      $allyCode = $this->allyCode;
    $swgoh = new SwgohHelp(array($this->dataObj->swgohUser, $this->dataObj->swgohPass));
    $p = $swgoh->fetchPlayer( $allyCode, $this->dataObj->language );
    $player = json_decode($p, true);
    return $player;
  }
  
  /****************************************************
    recupera la info d'un jugador
  ****************************************************/
  protected function getInfoPlayerExtra($allyCode = "") {
    if ($allyCode == "")
      $allyCode = $this->allyCode;
    
    $playerArr = $this->getInfoPlayer($allyCode);
//    return $playerArr;
    $player = json_encode($playerArr[0]['roster']);
    //file_put_contents("./player", $player);
    
    $url = "https://swgoh-stat-calc.glitch.me/api?flags=gameStyle,calcGP";
    $ch = curl_init(); // 
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-type: application/json'));
    curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $player);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
    curl_setopt($ch, CURLOPT_USERAGENT, 'Apache-HttpClient/4.5.5 (Java/12.0.1)');
    $playerJson = curl_exec($ch);
    curl_close($ch);
    $playerArr[0]['roster'] = json_decode($playerJson, true);
    
    //file_put_contents("./player1", $playerJson);
	
    return $playerArr;
  }
  
  /****************************************************
    recupera la info dels membres d'un gremi
  ****************************************************/
  protected function getInfoGuild($allyCode = "") {
    if ($allyCode == "")
      $allyCode = $this->allyCode;
    $swgoh = new SwgohHelp(array($this->dataObj->swgohUser, $this->dataObj->swgohPass));
    $g = $swgoh->fetchGuild( $allyCode, $this->dataObj->language );
    //file_put_contents("./guilds/".$guild[0]["id"]."_", $g);
    $guild = json_decode($g, true);
    return $guild;
  }
  
  /****************************************************
    recupera la info detallada dels membres d'un gremi
  ****************************************************/
  protected function getInfoGuildExtra($guild = array()) {
    $this->deleteOlderFiles(10800, "./guilds/");
            
    if (count($guild) == 0) {
      $guild = $this->getInfoGuild();
    }

    if ( (!is_array($guild[0]["roster"])) || (count($guild[0]["roster"]) == 0) ) {
      return "Not members found into the guild.";
    }
    
    // generem string amb els AllyCode
    $roster = ""; 
    foreach ($guild[0]["roster"] as $member) {
      if ($roster != "") {
        $roster .= ",";
      }
      $roster .= $member["allyCode"];
    }
  
    // comprovem si existeix el fitxer i, en cas d'existir, si té menys d'1h -> en tal cas el carreguem
    $players = "";
    if (file_exists("./guilds/".$guild[0]["id"])) {
      $fileTime = filemtime("./guilds/".$guild[0]["id"]);
      
      $fecha = new DateTime();
      $fecha->modify('-3 hours');
      
      if ($fileTime > $fecha->getTimestamp()) {//echo 'eeeeeeeee'."\n\n\n";
        $players = file_get_contents("./guilds/".$guild[0]["id"]);
        $playersArr = json_decode($players, true);
        return $playersArr;
      }
    }
    
    if ($players == "") { // si no existeix el fitxer o és més antic d'1h 
      $swgoh = new SwgohHelp(array($this->dataObj->swgohUser, $this->dataObj->swgohPass));
      $players = $swgoh->fetchPlayer( $roster, $this->dataObj->language );
      file_put_contents("./guilds/".$guild[0]["id"], $players);
      //file_put_contents("./guilds/".$guild[0]["id"].'_', $players);
    }
    $p = json_decode($players, true);
    //return $p;
    
    $finalPlayers = $this->deleteUnitsToDelete($players);
    //file_put_contents("./guilds/".$guild[0]["id"].'__', $finalPlayers);
    
    $url = "https://swgoh-stat-calc.glitch.me/api?flags=gameStyle,calcGP";
    //$url = "https://swgoh-stat-calc.glitch.me/api?flags=gameStyle";
    $ch = curl_init(); 
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-type: application/json'));
    curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $finalPlayers);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
    curl_setopt($ch, CURLOPT_USERAGENT, 'Apache-HttpClient/4.5.5 (Java/12.0.1)');
    $playersJson = curl_exec($ch);
    //echo $playersJson;
    curl_close($ch);

    file_put_contents("./guilds/".$guild[0]["id"], $playersJson);
    $playersArr = json_decode($playersJson, true);
    return $playersArr;
  }

  /****************************************************
    funció que inicialitza la traducció dels texts
  ****************************************************/
  protected function translatedText($code, $arr = '') {
    return $this->trans->getTransText($code, $arr);
  }
  
  /**************************************************************************
    functió que retorna un array de dades d'un jugador inicialitzat a 0
  **************************************************************************/
  protected function iniPlayerArray() {
    return array(
                  "7chars" => 0, 
                  "7ships" => 0,
                  "g13" => 0,
                  "g13vs12" => 0,
                  "g12" => 0,
                  "g12+1" => 0,
                  "g12+2" => 0,
                  "g12+3" => 0,
                  "g12+4" => 0,
                  "g12+5" => 0,
                  "g11" => 0,
                  "g10" => 0,
                  "g9" => 0,
                  "g8" => 0,
                  "zetas" => 0,
                  "relics" => 0,
                  "r8" => 0,
                  "r7" => 0,
                  "r6" => 0,
                  "r5" => 0,
                  "r4" => 0,
                  "r3" => 0,
                  "r2" => 0,
                  "r1" => 0,
                  "mods6" => 0,
                  "mods10" => 0,
                  "mods15" => 0,
                  "mods20" => 0,
                  "mods25" => 0,
                  "gp" => 0,
                  "gpchars" => 0,
                  "gpships" => 0,
                  "avarena" => 0,
                  "csquad1" => '',
                  "csquad2" => '',
                  "csquad3" => '',
                  "csquad4" => '',
                  "csquad5" => '',
                  "csquad5" => '',
                  "avships" => 0,
                  "ssquad1" => '',
                  "ssquad2" => '',
                  "ssquad3" => '',
                  "ssquad4" => '',
                  "ssquad5" => '',
                  "ssquad6" => '',
                  "ssquad7" => '',
                  "ssquad8" => '',
                  "updated" => '',
                  "top80" => 0,
                  "units" => array()
                 );
  }

  /**************************************************************************
    funció que processa la informació retornada per WS y agafa la info desitjada
  **************************************************************************/
  protected function processPlayer($player, &$data, $units = array()) {
    $data["updated"] = date("d-m-Y H:i:s", substr($player["updated"], 0, -3));
        
    $chars = [];    
    
    foreach ($player["roster"] as $unit) {
      // control units to show 
      if (in_array($unit["defId"], $units)) {
        if (!array_key_exists($unit["defId"], $data["units"])) {
          $data["units"][$unit["defId"]]["count"] = 0;
          $data["units"][$unit["defId"]]["g13"] = 0;
          $data["units"][$unit["defId"]]["g12"] = 0;
          $data["units"][$unit["defId"]]["r8"] = 0;
          $data["units"][$unit["defId"]]["gear"] = 0;
          $data["units"][$unit["defId"]]["level"] = 0;
          $data["units"][$unit["defId"]]["rarity"] = 0;
          $data["units"][$unit["defId"]]["relic"] = 0;
        }
        $data["units"][$unit["defId"]]["count"] = $data["units"][$unit["defId"]]["count"] + 1;
        // agafem valores màxims
        if ($data["units"][$unit["defId"]]["gear"] < $unit["gear"]) {
          $data["units"][$unit["defId"]]["gear"] = $unit["gear"];
        }
        if ($data["units"][$unit["defId"]]["level"] < $unit["level"]) {
          $data["units"][$unit["defId"]]["level"] = $unit["level"];
        }
        if ($data["units"][$unit["defId"]]["rarity"] < $unit["rarity"]) {
          $data["units"][$unit["defId"]]["rarity"] = $unit["rarity"];
        }
        if ($data["units"][$unit["defId"]]["relic"] < $unit["relic"]["currentTier"]-2) {
          $data["units"][$unit["defId"]]["relic"] = $unit["relic"]["currentTier"]-2;
        }
        // contem valors específics
        if ($unit["gear"] == 12) {
          $data["units"][$unit["defId"]]["g12"] = $data["units"][$unit["defId"]]["g12"] + 1;
        }
        if ($unit["gear"] == 13) {
          $data["units"][$unit["defId"]]["g13"] = $data["units"][$unit["defId"]]["g13"] + 1;
        }
        if ($unit["relic"]["currentTier"] == 10) {
          $data["units"][$unit["defId"]]["r8"] = $data["units"][$unit["defId"]]["r8"] + 1;
        }
      }
      
      // control GP
      $data["gp"] = $data["gp"] + $unit["gp"];
      if ($unit["combatType"] == 1) { // it is a character
        // GP into array to get +80
        $chars[$unit["defId"]] = $unit["gp"];
        
        // check zetas
        foreach ($unit["skills"] as $skill) {
          if (($skill["isZeta"]) && ($skill["tier"] == $skill["tiers"]))
            $data["zetas"] = $data["zetas"] + 1;
        }
        
        // check gear
        switch ($unit["gear"]) {
          case 8: $data["g8"] = $data["g8"] + 1; break;
          case 9: $data["g9"] = $data["g9"] + 1; break;
          case 10: $data["g10"] = $data["g10"] + 1; break;
          case 11: $data["g11"] = $data["g11"] + 1; break;
          case 12: 
            $data["g12"] = $data["g12"] + 1; 
            switch (count($unit["equipped"])) {
              case 1: $data["g12+1"] = $data["g12+1"] + 1; break;
              case 2: $data["g12+2"] = $data["g12+2"] + 1; break;
              case 3: $data["g12+3"] = $data["g12+3"] + 1; break;
              case 4: $data["g12+4"] = $data["g12+4"] + 1; break;
              case 5: $data["g12+5"] = $data["g12+5"] + 1; break;
            }
            break;
          case 13: $data["g13"] = $data["g13"] + 1; break;
        }
              
        // check mods
        foreach ($unit["mods"] as $mod) {
          if ($mod["pips"] == 6)
            $data["mods6"] = $data["mods6"] + 1;
             
          foreach ($mod["secondaryStat"] as $second) {
            if ($second["unitStat"] == 5) {
              $value = $second["value"];
              switch ($value) {
                case (($value >= 10) && ($value < 15)): $data["mods10"] = $data["mods10"] + 1; break;
                case (($value >= 15) && ($value < 20)): $data["mods15"] = $data["mods15"] + 1; break;
                case (($value >= 20) && ($value < 25)): $data["mods20"] = $data["mods20"] + 1; break;
                case (($value >= 25) && ($value < 31)): $data["mods25"] = $data["mods25"] + 1; break;
              }
            }
          }
                
          if (($mod["isZeta"]) && ($mod["tier"] == $mod["tiers"]))
            $data["zetas"] = $data["zetas"] + 1;
        }
                
        // check relic
        if ($unit["relic"]["currentTier"] != 1) {
          $data["relics"] = $data["relics"] + ($unit["relic"]["currentTier"] - 2);
          switch ($unit["relic"]["currentTier"]) {
            case 3: $data["r1"] = $data["r1"] + 1; break;
            case 4: $data["r2"] = $data["r2"] + 1; break;
            case 5: $data["r3"] = $data["r3"] + 1; break;
            case 6: $data["r4"] = $data["r4"] + 1; break;
            case 7: $data["r5"] = $data["r5"] + 1; break;
            case 8: $data["r6"] = $data["r6"] + 1; break;
            case 9: $data["r7"] = $data["r7"] + 1; break;
            case 10: $data["r8"] = $data["r8"] + 1; break;
          }
        }
            
        // check PG
        $data["gpchars"] = $data["gpchars"] + $unit["gp"];
            
        // stars
        if ($unit["rarity"] == 7) $data["7chars"] = $data["7chars"] + 1;  
      } 
      else {  // it is a ship
        // stars
        if ($unit["rarity"] == 7) $data["7ships"] = $data["7ships"] + 1;
            
        // check PG
        $data["gpships"] = $data["gpships"] + $unit["gp"]; 
      }
    } // ---> fi foreach roster
            
    if ($data["g12"] + $data["g11"] == 0) 
      $data["g13vs12"] = 0;
    else 
      $data["g13vs12"] = number_format(($data["g13"] * 100) / ($data["g12"] + $data["g11"]), 2);
            
    // arenas
    $data["avarena"] = $data["avarena"] + $player["arena"]["char"]["rank"]; 
    $data["avships"] = $data["avships"] + $player["arena"]["ship"]["rank"]; 
            
    // arena characters squand
    if (isset($player["arena"]["char"]["squad"][0]))
      $data["csquad1"] = TUnits::unitNameFromUnitId($player["arena"]["char"]["squad"][0]["defId"], $this->dataObj); 
    if (isset($player["arena"]["char"]["squad"][1]))
      $data["csquad2"] = TUnits::unitNameFromUnitId($player["arena"]["char"]["squad"][1]["defId"], $this->dataObj); 
    if (isset($player["arena"]["char"]["squad"][2]))
      $data["csquad3"] = TUnits::unitNameFromUnitId($player["arena"]["char"]["squad"][2]["defId"], $this->dataObj); 
    if (isset($player["arena"]["char"]["squad"][3]))
      $data["csquad4"] = TUnits::unitNameFromUnitId($player["arena"]["char"]["squad"][3]["defId"], $this->dataObj); 
    if (isset($player["arena"]["char"]["squad"][4]))
      $data["csquad5"] = TUnits::unitNameFromUnitId($player["arena"]["char"]["squad"][4]["defId"], $this->dataObj); 
            
    // arena ships squand
    if (isset($player["arena"]["ship"]["squad"][0]))
      $data["ssquad1"] = TUnits::unitNameFromUnitId($player["arena"]["ship"]["squad"][0]["defId"], $this->dataObj); 
    if (isset($player["arena"]["ship"]["squad"][1]))
      $data["ssquad2"] = TUnits::unitNameFromUnitId($player["arena"]["ship"]["squad"][1]["defId"], $this->dataObj); 
    if (isset($player["arena"]["ship"]["squad"][2]))
      $data["ssquad3"] = TUnits::unitNameFromUnitId($player["arena"]["ship"]["squad"][2]["defId"], $this->dataObj);
    if (isset($player["arena"]["ship"]["squad"][3]))
      $data["ssquad4"] = TUnits::unitNameFromUnitId($player["arena"]["ship"]["squad"][3]["defId"], $this->dataObj);
    if (isset($player["arena"]["ship"]["squad"][4]))
      $data["ssquad5"] = TUnits::unitNameFromUnitId($player["arena"]["ship"]["squad"][4]["defId"], $this->dataObj);
    if (isset($player["arena"]["ship"]["squad"][5]))
      $data["ssquad6"] = TUnits::unitNameFromUnitId($player["arena"]["ship"]["squad"][5]["defId"], $this->dataObj);
    if (isset($player["arena"]["ship"]["squad"][4]))
      $data["ssquad7"] = TUnits::unitNameFromUnitId($player["arena"]["ship"]["squad"][6]["defId"], $this->dataObj);
    if (isset($player["arena"]["ship"]["squad"][3]))
      $data["ssquad8"] = TUnits::unitNameFromUnitId($player["arena"]["ship"]["squad"][7]["defId"], $this->dataObj);
            
    // chars +80
    arsort($chars);
    $count = 0;
    foreach($chars as $val) {
      $data["top80"] = $data["top80"] + $val;
      $count++;
      if ($count == 80) break;
    }
  }

  /**************************************************************************
    funció que retorna si un string (yyyymmdd) conté una data vàlida 
  **************************************************************************/
  protected function isCorrectDate($date) {
    if (strlen($date) != 8) 
      return false;
            
    $day = substr($date, 6, 2);
    $month = substr($date, 4, 2);
    $year = substr($date, 0, 4);
    if (!checkdate($month, $day, $year))
      return false;
            
    $today = getdate();
    $today = $today['year'].str_pad($today['mon'], 2, "0", STR_PAD_LEFT).str_pad($today['mday'], 2, "0", STR_PAD_LEFT);
    if ($today < $date)
      return false;
            
    return true;
  }
  
  /**************************************************************************
    funció que retorna la unitat si la troba dins del array d'unitats. Sino retorna blanc
  **************************************************************************/
  protected function haveUnit($unit, $roster) {
    foreach ($roster as $u) {
      if ($u["defId"] == $unit)
        return $u;
    }
    return "";
  }
  
  /**************************************************************************
  * funció que esborra els fitxers de la carpeta $folder amb una antiguetat 
  *   superior a $seconds segons
  **************************************************************************/
  protected function deleteOlderFiles($seconds, $folder) {
    if ($handle = opendir($folder)) {
      while (false !== ($file = readdir($handle))) { 
        if (is_dir($file)) {
          continue;
        }
        
        $fileLastModified = filemtime($folder . $file);
        if ((time() - $fileLastModified) > $seconds) {
          unlink($folder . $file);
        }
      }
      closedir($handle); 
    }
  }
  
  /**************************************************************************
  * funció que genera una imatge dels personatges passats en el array $arr 
  **************************************************************************/
  protected function genImageCarac($arr) {
    $this->deleteOlderFiles(60, "./tmp/");
            
    // definim mida per a la imatge d'una unitat
    $width = 350; //180;
    $height = 160; 

    // calculem quantes files i columnes tindrem (màx. 2 columnes)
    $maxRowns = ceil(count($arr) / 2);
    $maxCols = 2;
    if (count($arr) <= 2) {
      $maxCols = 1;
    }

    // creem imatge
    $dest_image = imagecreatetruecolor($width*$maxCols, $height*$maxRowns);

    // li donem background blanc
    $white_background = imagecolorallocatealpha($dest_image, 255, 255, 255, 0); // --> fondo blanc
    imagefill($dest_image, 0, 0, $white_background);

    // recorrem unitats i les superposem a la imatge
    $rown = 0;
    $col = 0;
    //print_r($arr);
    foreach ($arr as $key => $unit) {
      // superposem imatge unitat 
      $info_imagen = getimagesize($this->dataObj->swgoh_gg.$key.'.png');
      if (($info_imagen[0] != 128) && ($info_imagen[1] != 128)) {
        $file = $this->redimensionarPNG($this->dataObj->swgoh_gg.$key.'.png', 128, 128, "");
        $a = imagecreatefrompng($file);
      } 
      else {
        $a = imagecreatefrompng($this->dataObj->swgoh_gg.$key.'.png');
      }
      imagecopy($dest_image, $a, ($width*$col)+25, ($height*$rown)+20, 0, 0, 128, 128);
      imagedestroy($a);

      $alig = TUnits::getAlignment($key, $this->dataObj);

      // superposem imatge de gear
      if ($unit['gear'] == 13) {
        if (strcasecmp($alig, 'Dark Side') == 0) {
          $b = imagecreatefrompng('./img/gear-icon-g'.$unit['gear'].'d.png');
        }
        else {
          $b = imagecreatefrompng('./img/gear-icon-g'.$unit['gear'].'l.png');
        }
      }
      else {
        $b = imagecreatefrompng('./img/gear-icon-g'.$unit['gear'].'.png');
      }
      imagecopy($dest_image, $b, ($width*$col)+25, ($height*$rown)+20, 0, 0, 128, 128);
      imagedestroy($b);

      // superposem imatge de reliquies
      if ($unit['relic'] > 0) {
        if (strcasecmp($alig, 'Dark Side') == 0) {
          $img = new textPainter('./img/relicd.png', $unit['relic'], './textimage/arial-bold.ttf', 10);
        } 
        else {
          $img = new textPainter('./img/relicl.png', $unit['relic'], './textimage/arial-bold.ttf', 10);
        }
        $img->setPosition(105, 113);
        $img->setTextColor(255,255,255);
        $filename = $img->saveImage("./tmp", "REL_");

        $d = imagecreatefrompng($filename);
        imagecopy($dest_image, $d, ($width*$col)+25, ($height*$rown)+20, 0, 0, 128, 128);
        imagedestroy($d);
      }

      // superposem imatge de nivell
      $img = new textPainter('./img/level.png', $unit['level'], './textimage/arial-bold.ttf', 10);
      if ($unit['level'] < 10)
        $img->setPosition(60, 117);
      elseif ($unit['level'] < 100)
        $img->setPosition(57, 117);
      else
        $img->setPosition(55, 117);
      $img->setTextColor(255,255,255);
      $filename = $img->saveImage("./tmp", "LVL_");

      $e = imagecreatefrompng($filename);
      imagecopy($dest_image, $e, ($width*$col)+25, ($height*$rown)+20, 0, 0, 128, 128);
      imagedestroy($e);

      // superposem imatge d'estrelles
      $f = imagecreatefrompng('./img/star'.$unit['rarity'].'.png');
      imagecopy($dest_image, $f, ($width*$col)+10, ($height*$rown), 0, 0, 160, 160);
      imagedestroy($f);
      
      // posem dades
      if (strcasecmp($alig, 'Dark Side') == 0) {
        $img = new textPainter('./img/datad.png', $unit['count'], './textimage/arial-bold.ttf', 14);
      } 
      else {
        $img = new textPainter('./img/datal.png', $unit['count'], './textimage/arial-bold.ttf', 14);
      }
      $img->setTextColor(0,0,0);
      $img->setPosition(60, 30);
      $img->writeText();
      $img->setText($unit['g13']);
      $img->setPosition(60, 65);
      $img->writeText();
      $img->setText($unit['g12']);
      $img->setPosition(60, 108);
      $img->writeText();
      $img->setText($unit['r8']);
      $img->setPosition(60, 147);
      $img->writeText();
      $img->setTextColor(255,255,255);
      $img->setText('8');
      $img->setFontSize(12);
      $img->setPosition(20, 147);
      $filename = $img->saveImage("./tmp", "REL_");

      $c = imagecreatefrompng($filename);
      imagecopy($dest_image, $c, ($width*$col)+190, ($height*$rown), 0, 0, 160, 160);
      imagedestroy($c);
      
      // control de la fila i columna
      $col++;
      if ($col >= $maxCols) {
        $col = 0;
        $rown++;
        if ($rown >= $maxRowns)  
          $rown = 0;
      }
    }
    $tempName = tempnam('./tmp/', 'FINAL_');
    imagepng($dest_image, $tempName.'.png');
    echo 'https://www.cadetill.com/swgoh/bot/tmp/'.basename($tempName).'.png';
    return 'https://www.cadetill.com/swgoh/bot/tmp/'.basename($tempName).'.png';
  }

  /**************************************************************************
  * funció que genera una imatge dels personatges passats en el array $arr 
  **************************************************************************/
  protected function generateImage($arr) {
    $this->deleteOlderFiles(60, "./tmp/");
            
    // definim mida per a la imatge d'una unitat
    $width = 180;
    $height = 160;

    // calculem quantes files i columnes tindrem (màx. 5 columnes)
    $maxRowns = ceil(count($arr) / 5);
    $maxCols = 5;
    if (count($arr) <= 5) {
      $maxCols = ((count($arr) / 5) - floor((count($arr) / 5))) * 5;
    }
    if ($maxCols == 0) {
      $maxCols = 5;
    }

    // creem imatge
    $dest_image = imagecreatetruecolor($width*$maxCols, $height*$maxRowns);

    // li donem background blanc
    $white_background = imagecolorallocatealpha($dest_image, 255, 255, 255, 0); // --> fondo blanc
    imagefill($dest_image, 0, 0, $white_background);

    // recorrem unitats i les superposem a la imatge
    $col = 0;
    $rown = 0;
    $okCount = 0;
    foreach ($arr as $unit) {
      // superposem imatge unitat
      $info_imagen = getimagesize($this->dataObj->swgoh_gg.$unit['defId'].'.png');
      if (($info_imagen[0] != 128) && ($info_imagen[1] != 128)) {
        $file = $this->redimensionarPNG($this->dataObj->swgoh_gg.$unit['defId'].'.png', 128, 128, "");
        $a = imagecreatefrompng($file);
      } 
      else {
        $a = imagecreatefrompng($this->dataObj->swgoh_gg.$unit['defId'].'.png');
      }
      imagecopy($dest_image, $a, ($width*$col)+25, ($height*$rown)+20, 0, 0, 128, 128);
      imagedestroy($a);

      $alig = TUnits::getAlignment($unit['defId'], $this->dataObj);

      // superposem imatge de gear
      if ($unit['gear'] == 13) {
        if (strcasecmp($alig, 'Dark Side') == 0) {
          $b = imagecreatefrompng('./img/gear-icon-g'.$unit['gear'].'d.png');
        }
        else {
          $b = imagecreatefrompng('./img/gear-icon-g'.$unit['gear'].'l.png');
        }
      }
      else {
        $b = imagecreatefrompng('./img/gear-icon-g'.$unit['gear'].'.png');
      }
      imagecopy($dest_image, $b, ($width*$col)+25, ($height*$rown)+20, 0, 0, 128, 128);
      imagedestroy($b);

      // superposem imatge de zetas
      $zetas = 0;
      foreach ($unit['skills'] as $skill) {
        if (($skill['tier'] == $skill['tiers']) && ($skill['isZeta'] == 'true')) {
          $zetas++;
        }
      }
      if ($zetas > 0) {
        $img = new textPainter('./img/zeta.png', $zetas, './textimage/arial-bold.ttf', 10);
        $img->setPosition(25, 138);
        $img->setTextColor(255,255,255);
        $filename = $img->saveImage("./tmp", "ZETA_");

        $c = imagecreatefrompng($filename);
        imagecopy($dest_image, $c, ($width*$col)+15, ($height*$rown), 0, 0, 180, 160);
        imagedestroy($c);
      }

      // superposem imatge de reliquies
      $relic = 0;
      if ($unit['relic']['currentTier'] > 1) {
        $relic = $unit['relic']['currentTier'] - 2;
      }
      if ($relic > 0) {
        if (strcasecmp($alig, 'Dark Side') == 0) {
          $img = new textPainter('./img/relicd.png', $relic, './textimage/arial-bold.ttf', 10);
        } 
        else {
          $img = new textPainter('./img/relicl.png', $relic, './textimage/arial-bold.ttf', 10);
        }
        $img->setPosition(105, 113);
        $img->setTextColor(255,255,255);
        $filename = $img->saveImage("./tmp", "REL_");

        $d = imagecreatefrompng($filename);
        imagecopy($dest_image, $d, ($width*$col)+25, ($height*$rown)+20, 0, 0, 128, 128);
        imagedestroy($d);
      }

      // superposem imatge de nivell
      $img = new textPainter('./img/level.png', $unit['level'], './textimage/arial-bold.ttf', 10);
      if ($unit['level'] < 10)
        $img->setPosition(60, 117);
      elseif ($unit['level'] < 100)
        $img->setPosition(57, 117);
      else
        $img->setPosition(55, 117);
      $img->setTextColor(255,255,255);
      $filename = $img->saveImage("./tmp", "LVL_");

      $e = imagecreatefrompng($filename);
      imagecopy($dest_image, $e, ($width*$col)+25, ($height*$rown)+20, 0, 0, 128, 128);
      imagedestroy($e);

      // superposem imatge d'estrelles
      $f = imagecreatefrompng('./img/star'.$unit['rarity'].'.png');
      imagecopy($dest_image, $f, ($width*$col)+10, ($height*$rown), 0, 0, 160, 160);
      imagedestroy($f);
      
      // controlem prerequisits i posem "check" si els compleix
      $isOk = true;
      foreach ($unit['botpre'] as $key => $pre) {
        switch ($key) {
          case 'gp':
            if ($unit['gp'] < $pre) {
              $isOk = false;  
            }
            break;
          case 'l':
            if ($unit['level'] < $pre) {
              $isOk = false;  
            }
            break;
          case 'g':
            if ($unit['gear'] < $pre) {
              $isOk = false;  
            }
            break;
          case 'r':
            if (($unit['gear'] < 13) || (($unit['relic']['currentTier']-2) < $pre)) {
              $isOk = false;  
            }
            break;
          case 's':
            if ($unit['rarity'] < $pre) {
              $isOk = false;  
            }
            break;
        }
      }
      if (($isOk) && ($unit["rarity"] == 7)) {
        $ok = imagecreatefrompng('./img/ok.png');
        imagecopy($dest_image, $ok, ($width*$col)+10, ($height*$rown), 0, 0, 160, 160);
        imagedestroy($ok);
        $okCount++;
      }

      // control de la fila i columna
      $col++;
      if ($col >= $maxCols) {
        $col = 0;
        $rown++;
        if ($rown >= $maxRowns)  
          $rown = 0;
      }
    }
    
    // si tenim acabats tots els personatges, posem el "approved"
    if ($okCount == count($arr)) {
      $x = $width*$maxCols;
      $y = $height*$maxRowns;
      if ($x > $y) {
        $file = $this->redimensionarPNG('./img/approved.png', $y-60, $y-60, "");
      }
      else {
        $file = $this->redimensionarPNG('./img/approved.png', $x-60, $x-60, "");
      }
      $apr = imagecreatefrompng($file);
      $black = imagecolorallocate($apr, 0, 0, 0);
      imagecolortransparent($apr, $black);
      $apr = $this->imageSetOpacity($apr, 0.45);
      if ($x > $y) {
        imagecopy($dest_image, $apr, intval(($x-$y+60) / 2), 30, 0, 0, $y-60, $y-60);
      }
      else {
        imagecopy($dest_image, $apr, 30, intval(($y-$x+60) / 2), 0, 0, $x-60, $x-60);
      }
      imagedestroy($apr);
    }

    $tempName = tempnam('./tmp/', 'FINAL_');
    imagepng($dest_image, $tempName.'.png');
    return 'https://www.cadetill.com/swgoh/bot/tmp/'.basename($tempName).'.png';
  }
  
  /**************************************************************************
  * funció que enviarà fotos a Telegram
  **************************************************************************/
  protected function sendPhoto($photoFile, $photoUrl, $photoText, $reply = true) {
    if ($photoFile != '') {
      $photo = '@'.$photoFile;
    }
    else {
      $photo = $photoUrl;
    }
    if ($reply) {
      $url = $this->dataObj->website.'/sendPhoto?chat_id='.$this->dataObj->chatId.'&reply_to_message_id='.$this->dataObj->messageId.'&parse_mode=HTML&caption='.urlencode($photoText).'&photo='.$photo;
    }
    else {
      $url = $this->dataObj->website.'/sendPhoto?chat_id='.$this->dataObj->chatId.'&parse_mode=HTML&caption='.urlencode($photoText).'&photo='.$photo;
    }
     //echo $url."\n\n";       
    file_get_contents($url);
  }

  /**************************************************************************
  * funció que enviarà missatges a Telegram
  **************************************************************************/
  protected function sendMessage($response, $reply = true, $keyboard = NULL) {
    if (isset($keyboard)) {
      $teclado = '&reply_markup={"keyboard":['.$keyboard.'], "resize_keyboard":true, "one_time_keyboard":true}';
    }
          
    $first = true;
    foreach ($response as $res) {
      if (($first) && ($reply)) {
        $url = $this->dataObj->website.'/sendMessage?chat_id='.$this->dataObj->chatId.'&reply_to_message_id='.$this->dataObj->messageId.'&parse_mode=HTML&text='.urlencode($res).$teclado;
        $first = false;
      } 
      else {
        $url = $this->dataObj->website.'/sendMessage?chat_id='.$this->dataObj->chatId.'&parse_mode=HTML&text='.urlencode($res).$teclado;
      }
          
      file_get_contents($url);
    }
  }

  /****************************************************
    FUNCIONS PRIVATED
  ****************************************************/ 
  /**************************************************************************
    funció que redimensiona una imatge
  **************************************************************************/
  private function redimensionarPNG($origen, $ancho_max, $alto_max, $fijar) {
    $destino = tempnam('./tmp/', 'CHAR_').".png";

    $info_imagen = getimagesize($origen);
    $ancho = $info_imagen[0];
    $alto = $info_imagen[1];
    if ($ancho >= $alto) {
      $nuevo_alto = round($alto * $ancho_max / $ancho,0);
      $nuevo_ancho = $ancho_max;
    } 
    else {
      $nuevo_ancho = round($ancho * $alto_max / $alto,0);
      $nuevo_alto = $alto_max;
    }
    switch ($fijar) {
      case "ancho":
        $nuevo_alto = round($alto * $ancho_max / $ancho,0);
        $nuevo_ancho = $ancho_max;
        break;
      case "alto":
        $nuevo_ancho = round($ancho * $alto_max / $alto,0);
        $nuevo_alto = $alto_max;
        break;
      default:
        $nuevo_ancho = $nuevo_ancho;
        $nuevo_alto = $nuevo_alto;
        break;
    }
    $imagen_nueva = imagecreatetruecolor($nuevo_ancho, $nuevo_alto);
    $imagen_vieja = imagecreatefrompng($origen);
    imagecopyresampled($imagen_nueva, $imagen_vieja, 0, 0, 0, 0, $nuevo_ancho, $nuevo_alto, $ancho, $alto);
    imagepng($imagen_nueva, $destino);
    imagedestroy($imagen_nueva);
    imagedestroy($imagen_vieja);
    return 'https://www.cadetill.com/swgoh/bot/tmp/'.basename($destino);
  }
  
  /**************************************************************************
    funció que canvia la opacitat a una imatge
  **************************************************************************/
  private function imageSetOpacity( $imageSrc, $opacity ) {
    $width  = imagesx( $imageSrc );
    $height = imagesy( $imageSrc );
    
    // Duplicate image and convert to TrueColor
    $imageDst = imagecreatetruecolor( $width, $height );
    imagealphablending( $imageDst, false );
    imagefill( $imageDst, 0, 0, imagecolortransparent( $imageDst ));
    imagecopy( $imageDst, $imageSrc, 0, 0, 0, 0, $width, $height );
    
    // Set new opacity to each pixel
    for ( $x = 0; $x < $width; ++$x ) {
      for ( $y = 0; $y < $height; ++$y ) {
        $pixelColor = imagecolorat( $imageDst, $x, $y );
        $pixelOpacity = 127 - (( $pixelColor >> 24 ) & 0xFF );
        if ( $pixelOpacity > 0 ) {
            $pixelOpacity = $pixelOpacity * $opacity;
            $pixelColor = ( $pixelColor & 0xFFFFFF ) | ( (int)round( 127 - $pixelOpacity ) << 24 );
            imagesetpixel( $imageDst, $x, $y, $pixelColor );
        }
      }
    }
    
    return $imageDst;
  }
  
  /**************************************************************************
    funció que esborra les unitats que no volem processar
  **************************************************************************/
  private function deleteUnitsToDelete( $players ) {
    //echo "\n\nentrada delete units\n\n";
    $p = json_decode($players, true);
    foreach ($p as $pkey => $player) {
      foreach ($player["roster"] as $key => $unit) {
        if (in_array($unit["defId"], $this->dataObj->unitsToDelete)) {
      //print_r($p[$pkey]["roster"][$key]);
          unset($p[$pkey]["roster"][$key]);
        }
      }
    }
    //echo "\n\nentrada delete units\n\n";
    
    return json_encode($p, true);
  }
}
