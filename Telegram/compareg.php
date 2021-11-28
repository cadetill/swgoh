<?php
class TCompareg extends TBase {
  /****************************************************
    constructor de la classe. Inicialitza variables
  ****************************************************/
  public function __construct($params, $dataObj) {
    parent::__construct($dataObj);
    
    switch (count($params)) {
      case 2:
        $params[2] = $dataObj->allycode;
      case 3:
        if (!$this->checkAllyCode($params[1]))
          $this->error = $this->translatedText("error3", $params[1]); // "The %s isn't a correct AllyCode.\n";
        if (!$this->checkAllyCode($params[2]))
          $this->error = $this->translatedText("error3", $params[2]); // "The %s isn't a correct AllyCode.\n";
      
        $this->allyCode = $params[1] . ',' . $params[2];
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
      return $this->getHelp("compareg", $this->error);
  
    $initialTime = microtime(true);

    $timestamp = date("Y-m-d H:i:s");  
    $sql = "insert into queue (insdate, message_id, date) VALUES ('".$timestamp."', '".$this->dataObj->messageId."', '".$this->dataObj->messageDate."')";
    echo $sql;
    $idcon = new mysqli($this->dataObj->bdserver, $this->dataObj->bduser, $this->dataObj->bdpas, $this->dataObj->bdnamebd);
    if ($idcon->connect_error) 
      return $this->translatedText("error4");                                   // "Ooooops! An error has occurred getting data.\n\n";
    $idcon->query( $sql );

    $res = $this->compareGuilds();

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
    procesa o calcula el resultat de la champions
  ****************************************************/
  private function compareGuilds() {
    ini_set('memory_limit', '-1');
    
    // agafem unitats que ha de controlar el comando
    $units = TUnits::unitsForCommand($this->dataObj->guildId, 'units', 'compareg', $this->dataObj);
    if (!is_array($units))
      $units = array();
    
    $allyCodes = explode(',', $this->allyCode);
    // busquem info dels gremis
    $g1 = $this->getInfoGuild( $allyCodes[0] );
    $g2 = $this->getInfoGuild( $allyCodes[1] );

    // mirem que haguem trobat Id Guild
    if ($g1[0]["id"] == "")
      return $this->translatedText("error6");                                   // "Ooooops! API server may have shut down. Try again later.\n\n"
   
    // generem string amb els AllyCode dels jugadors dels gremis
    $roster1 = "";
    foreach ($g1 as $guild) {
      foreach ($guild["roster"] as $member) {
        if ($roster1 != "") $roster1 .= ",";
        $roster1 .= $member["allyCode"];
      }
    }
    $roster2 = "";
    foreach ($g2 as $guild) {
      foreach ($guild["roster"] as $member) {
        if ($roster2 != "") $roster2 .= ",";
        $roster2 .= $member["allyCode"];
      }
    }
  
    // agafem info de tots els jugadors dels dos gremis
    $players1 = $this->getInfoPlayer($roster1);
    $players2 = $this->getInfoPlayer($roster2);
  
    // inicialitzem arrays dels gremis
    $guild1 = $this->iniPlayerArray();
    $guild2 = $this->iniPlayerArray();
  
    // recorrem array de $players actualitzant els arrays dels gremis
    foreach ($players1 as $player) {
      $this->processPlayer($player, $guild1, $units);
    }
    foreach ($players2 as $player) {
      $this->processPlayer($player, $guild2, $units);
    }
    //print_r($guild1);
    //print_r($guild2);
    $ret  = "<b>".$players1[0]["guildName"]." vs ".$players2[0]["guildName"]."</b>\n\n";
    $ret .= "\n";
    $ret .= $this->translatedText("txtCompareg01");                                                          // "<b>-----------SUMMARY-----------</b>\n";
    $ret .= $this->translatedText("txtCompareg02", array(count($players1), count($players2)));               // "<b>Members:</b> ".count($players1)." vs ".count($players2)."\n";
    $ret .= $this->translatedText("txtCompareg03", array($g1[0]["gp"], $g2[0]["gp"]));                       // "<b>GP:</b> ".$g1[0]["gp"]." vs ".$g2[0]["gp"]."\n";
    $ret .= $this->translatedText("txtCompareg04", array($guild1["gp"], $guild2["gp"]));                     // "<b>GP (players sum):</b> ".$guild1["gp"]." vs ".$guild2["gp"]."\n";
    $ret .= $this->translatedText("txtCompareg05", array($guild1["gpchars"], $guild2["gpchars"]));           // "<b>GP (characters):</b> ".$guild1["gpchars"]." vs ".$guild2["gpchars"]."\n";
    $ret .= $this->translatedText("txtCompareg06", array($guild1["gpships"], $guild2["gpships"]));           // "<b>GP (ships):</b> ".$guild1["gpships"]." vs ".$guild2["gpships"]."\n";
    $ret .= $this->translatedText("txtCompareg07", array($guild1["top80"], $guild2["top80"]));               // "<b>GP top 80:</b> ".$guild1["top80"]." vs ".$guild2["top80"]."\n";
    $ret .= $this->translatedText("txtCompareg08", array(number_format($guild1["avarena"] / count($players1), 2), number_format($guild2["avarena"] / count($players2), 2)));               // "<b>Av. Arena:</b> ".number_format($guild1["avarena"] / count($players1), 2)." vs ".number_format($guild2["avarena"] / count($players2), 2)."\n";
    $ret .= $this->translatedText("txtCompareg09", array(number_format($guild1["avships"] / count($players1), 2), number_format($guild2["avships"] / count($players2), 2)));               // "<b>Av. Ships:</b> ".number_format($guild1["avships"] / count($players1), 2)." vs ".number_format($guild2["avships"] / count($players2), 2)."\n";
    $ret .= $this->translatedText("txtCompareg10", array($guild1["zetas"], $guild2["zetas"]));               // "<b>Zetas:</b> ".$guild1["zetas"]." vs ".$guild2["zetas"]."\n";
    $ret .= $this->translatedText("txtCompareg11", array($guild1["g13"], $guild2["g13"]));                   // "<b>Gear 13:</b> ".$guild1["g13"]." vs ".$guild2["g13"]."\n";
    $ret .= $this->translatedText("txtCompareg12", array($guild1["g12"], $guild2["g12"]));                   // "<b>Gear 12:</b> ".$guild1["g12"]." vs ".$guild2["g12"]."\n";
    $ret .= $this->translatedText("txtCompareg13", array($guild1["g12+1"], $guild2["g12+1"]));               // "<b>Gear 12+1:</b> ".$guild1["g12+1"]." vs ".$guild2["g12+1"]."\n";
    $ret .= $this->translatedText("txtCompareg14", array($guild1["g12+2"], $guild2["g12+2"]));               // "<b>Gear 12+2:</b> ".$guild1["g12+2"]." vs ".$guild2["g12+2"]."\n";
    $ret .= $this->translatedText("txtCompareg15", array($guild1["g12+3"], $guild2["g12+3"]));               // "<b>Gear 12+3:</b> ".$guild1["g12+3"]." vs ".$guild2["g12+3"]."\n";
    $ret .= $this->translatedText("txtCompareg16", array($guild1["g12+4"], $guild2["g12+4"]));               // "<b>Gear 12+4:</b> ".$guild1["g12+4"]." vs ".$guild2["g12+4"]."\n";
    $ret .= $this->translatedText("txtCompareg17", array($guild1["g12+5"], $guild2["g12+5"]));               // "<b>Gear 12+5:</b> ".$guild1["g12+5"]." vs ".$guild2["g12+5"]."\n";
    $ret .= "\n";
    $ret .= $this->translatedText("txtCompareg18");                                                          // "<b>-----------RELICS-----------</b>\n";
    $ret .= $this->translatedText("txtCompareg19", array($guild1["relics"], $guild2["relics"]));             // "<b>Relics:</b> ".$guild1["relics"]." vs ".$guild2["relics"]."\n";
    $ret .= $this->translatedText("txtCompareg35", array($guild1["r9"], $guild2["r9"]));                     // "<b>Tier 9:</b> ".$guild1["r9"]." vs ".$guild2["r9"]."\n";
    $ret .= $this->translatedText("txtCompareg34", array($guild1["r8"], $guild2["r8"]));                     // "<b>Tier 8:</b> ".$guild1["r8"]." vs ".$guild2["r8"]."\n";
    $ret .= $this->translatedText("txtCompareg20", array($guild1["r7"], $guild2["r7"]));                     // "<b>Tier 7:</b> ".$guild1["r7"]." vs ".$guild2["r7"]."\n";
    $ret .= $this->translatedText("txtCompareg21", array($guild1["r6"], $guild2["r6"]));                     // "<b>Tier 6:</b> ".$guild1["r6"]." vs ".$guild2["r6"]."\n";
    $ret .= $this->translatedText("txtCompareg22", array($guild1["r5"], $guild2["r5"]));                     // "<b>Tier 5:</b> ".$guild1["r5"]." vs ".$guild2["r5"]."\n";
    $ret .= $this->translatedText("txtCompareg23", array($guild1["r4"], $guild2["r4"]));                     // "<b>Tier 4:</b> ".$guild1["r4"]." vs ".$guild2["r4"]."\n";
    $ret .= $this->translatedText("txtCompareg24", array($guild1["r3"], $guild2["r3"]));                     // "<b>Tier 3:</b> ".$guild1["r3"]." vs ".$guild2["r3"]."\n";
    $ret .= $this->translatedText("txtCompareg25", array($guild1["r2"], $guild2["r2"]));                     // "<b>Tier 2:</b> ".$guild1["r2"]." vs ".$guild2["r2"]."\n";
    $ret .= $this->translatedText("txtCompareg26", array($guild1["r1"], $guild2["r1"]));                     // "<b>Tier 1:</b> ".$guild1["r1"]." vs ".$guild2["r1"]."\n";
    $ret .= "\n";
    $ret .= $this->translatedText("txtCompareg27");                                                          // "<b>-----------MODS-----------</b>\n";
    $ret .= $this->translatedText("txtCompareg28", array($guild1["mods6"], $guild2["mods6"]));               // "<b>6*:</b> ".$guild1["mods6"]." vs ".$guild2["mods6"]."\n";
    $ret .= $this->translatedText("txtCompareg29", array($guild1["mods25"], $guild2["mods25"]));             // "<b>25+:</b> ".$guild1["mods25"]." vs ".$guild2["mods25"]."\n";
    $ret .= $this->translatedText("txtCompareg30", array($guild1["mods20"], $guild2["mods20"]));             // "<b>20+:</b> ".$guild1["mods20"]." vs ".$guild2["mods20"]."\n";
    $ret .= $this->translatedText("txtCompareg31", array($guild1["mods15"], $guild2["mods15"]));             // "<b>15+:</b> ".$guild1["mods15"]." vs ".$guild2["mods15"]."\n";
    $ret .= $this->translatedText("txtCompareg32", array($guild1["mods10"], $guild2["mods10"]));             // "<b>10+:</b> ".$guild1["mods10"]." vs ".$guild2["mods10"]."\n";
    $ret .= "\n";
    $ret .= $this->translatedText("txtCompareg33");                                                    // "<b>Units</b>\n";

    //print_r($units);
    foreach ($units as $unit) {
      $ret .= TUnits::unitNameFromUnitId($unit, $this->dataObj)."\n"; 
      $numguild1 = 0;
      $g13guild1 = 0;
      $g12guild1 = 0;
      $r8guild1 = 0;
      $numguild2 = 0;
      $g13guild2 = 0;
      $g12guild2 = 0;
      $r8guild2 = 0;
      if (array_key_exists($unit, $guild1["units"])) {
          //print_r($guild1["units"]);
        $numguild1 = $guild1["units"][$unit]["count"];
        $g13guild1 = $guild1["units"][$unit]["g13"];
        $g12guild1 = $guild1["units"][$unit]["g12"];
        $r8guild1 = $guild1["units"][$unit]["r8"];
      }
      if (array_key_exists($unit, $guild2["units"])) {
        $numguild2 = $guild2["units"][$unit]["count"];
        $g13guild2 = $guild2["units"][$unit]["g13"];
        $g12guild2 = $guild2["units"][$unit]["g12"];
        $r8guild2 = $guild2["units"][$unit]["r8"];
      }
      $ret .= "  #: ".$numguild1." - ".$numguild2."\n";
      $ret .= "  r8: ".$r8guild1." - ".$r8guild2."\n";
      $ret .= "  g13: ".$g13guild1." - ".$g13guild2."\n";
      $ret .= "  g12: ".$g12guild1." - ".$g12guild2."\n";
      $ret .= "\n";
    }
    
    $ret .= "\n";
    $ret .= $this->translatedText("last_update", $guild1["updated"]);                                        // "<i>Last update: ".$guild1["updated"]."</i>\n";
  
    return $ret;  
  }
}

