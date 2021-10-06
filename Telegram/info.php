<?php
class TInfo extends TBase {
   private $image = "";
   
  /****************************************************
    constructor de la classe. Inicialitza variables
  ****************************************************/
  public function __construct($params, $dataObj) {
    parent::__construct($dataObj);
    
    switch (count($params)) {
      case 1:
        $params[1] = $dataObj->allycode;
      case 2:
        if (!$this->checkAllyCode($params[1]))
          $this->error = $this->translatedText("error3", $params[1]); // "The %s isn't a correct AllyCode.\n";
      
        $this->allyCode = $params[1];
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
      return $this->getHelp("info", $this->error);
  
    $initialTime = microtime(true);
    
    $res = $this->getInfo();

    $finalTime = microtime(true);
    $time = $finalTime - $initialTime;
    if (is_array($res)) {
      $res[count($res)-1] .= $this->translatedText("elapsed_time", gmdate("H:i:s", $time));
      //return $res;
    } 
    else {
      $res .= $this->translatedText("elapsed_time", gmdate("H:i:s", $time));
      $res = array($res);
      //return array($res);
    }
    $this->sendMessage($res);
    if ($this->image != "") {
      $this->sendPhoto('', $this->image, '', false);
    }
  }
  
  /****************************************************
    FUNCIONS PRIVADES
  ****************************************************/
  /****************************************************
    agafa la informació d'un AllyCode
  ****************************************************/
  private function getInfo() {    
    // agafem unitats que ha de controlar el comando
    $units = TUnits::unitsForCommand($this->dataObj->guildId, 'units', 'info', $this->dataObj);
    if (!is_array($units))
      $units = array();
    //print_r($units);

    $player = $this->getInfoPlayerExtra();
    //print_r($player);
    // mirem que haguem trobat Id Guild
    if ($player[0]["id"] == "")
      return $this->translatedText("error6");                                   // "Ooooops! API server may have shut down. Try again later.\n\n"
  
    $data = $this->iniPlayerArray();
    $this->processPlayer($player[0], $data, $units);
    //print_r($player);
    
    $num = count($player[0]["grandArena"]);

    $ret  = $this->translatedText("txtInfo01", $player[0]["name"]);                                // "<b>Name:</b> ".$player[0]["name"]."\n";
    $ret .= $this->translatedText("txtInfo02", $player[0]["guildName"]);                           // "<b>Guild:</b> ".$player[0]["guildName"]."\n";
    $ret .= $this->translatedText("txtInfo03", array($player[0]["stats"][0]["value"], $data["gp"]));  // "<b>Galactic Power:</b> ".$player[0]["stats"][0]["value"]." / ".$data["gp"]."\n";
    $ret .= $this->translatedText("txtInfo04", array($player[0]["stats"][1]["value"], $data["gpchars"]));  // "<b>GP (characters):</b> ".$player[0]["stats"][1]["value"]." / ".$data["gpchars"]."\n";
    $ret .= $this->translatedText("txtInfo05", array($player[0]["stats"][2]["value"], $data["gpships"]));  // "<b>GP (ships):</b> ".$player[0]["stats"][2]["value"]." / ".$data["gpships"]."\n";
    $ret .= $this->translatedText("txtInfo06");                                                    // "<b>----------- Gran Arena -----------</b>\n";
    $ret .= $this->translatedText("txtInfo07", $player[0]["grandArena"][$num-1]["rank"]);          // "<b>Actual Rank:</b> ".$player[0]["grandArena"][$num-1]["rank"]."\n";
    $ret .= $this->translatedText("txtInfo08", $player[0]["grandArena"][$num-1]["league"]);        // "<b>League:</b> ".strtolower($player[0]["grandArena"][$num-1]["league"])."\n";
    $ret .= $this->translatedText("txtInfo09", $player[0]["grandArena"][$num-1]["seasonPoints"]);  // "<b>Season Points:</b> ".$player[0]["grandArena"][$num-1]["seasonPoints"]."\n";
    $ret .= $this->translatedText("txtInfo10", $player[0]["stats"][19]["value"]);                  // "<b>Best Score Earned:</b> ".$player[0]["stats"][19]["value"]."\n";
    $ret .= $this->translatedText("txtInfo11", $player[0]["stats"][3]["value"]);                   // "<b>Lifetime Score:</b> ".$player[0]["stats"][3]["value"]."\n";
    $ret .= $this->translatedText("txtInfo12", $player[0]["stats"][13]["value"]);                  // "<b>Offensive Won:</b> ".$player[0]["stats"][13]["value"]."\n";
    $ret .= $this->translatedText("txtInfo13", $player[0]["stats"][14]["value"]);                  // "<b>Successful Defends:</b> ".$player[0]["stats"][14]["value"]."\n";
    $ret .= $this->translatedText("txtInfo14", $player[0]["stats"][18]["value"]);                  // "<b>Territories Defeated:</b> ".$player[0]["stats"][18]["value"]."\n";
    $ret .= $this->translatedText("txtInfo15", $player[0]["stats"][15]["value"]);                  // "<b>Banners Earned:</b> ".$player[0]["stats"][15]["value"]."\n";
    $ret .= $this->translatedText("txtInfo16", $player[0]["stats"][12]["value"]);                  // "<b>Promotions Earned:</b> ".$player[0]["stats"][12]["value"]."\n";
    $ret .= $this->translatedText("txtInfo17");                                                    // "<b>----------- Characters -----------</b>\n";
    $ret .= $this->translatedText("txtInfo18", $data["7chars"]);                                   // "<b>7 stars:</b> ".$data["7chars"]."\n";
    $ret .= $this->translatedText("txtInfo19", $data["g13"]);                                      // "<b>Gear 13:</b> ".$data["g13"]."\n";
    $ret .= $this->translatedText("txtInfo20", number_format($data["g13vs12"], 2));                // "<b>Gear 13vs12/11:</b> ".number_format($data["g13vs12"], 2)."\n";
    $ret .= $this->translatedText("txtInfo21", $data["g12"]);                                      // "<b>Gear 12:</b> ".$data["g12"]."\n";
    $ret .= $this->translatedText("txtInfo22", $data["g11"]);                                      // "<b>Gear 11:</b> ".$data["g11"]."\n";
    $ret .= $this->translatedText("txtInfo23", $data["g10"]);                                      // "<b>Gear 10:</b> ".$data["g10"]."\n";
    $ret .= $this->translatedText("txtInfo24", $data["g9"]);                                       // "<b>Gear 9:</b> ".$data["g9"]."\n";
    $ret .= $this->translatedText("txtInfo25", $data["g8"]);                                       // "<b>Gear 8:</b> ".$data["g8"]."\n";
    $ret .= $this->translatedText("txtInfo27", $data["zetas"]);                                    // "<b>Zetas:</b> ".$data["zetas"]."\n";
    $ret .= $this->translatedText("txtInfo28", $data["top80"]);                                    // "<b>Top 80:</b> ".$data["top80"]."\n";
    $ret .= $this->translatedText("txtInfo29");                                                    // "<b>----------- Ships -----------</b>\n";
    $ret .= $this->translatedText("txtInfo30", $data["7ships"]);                                   // "<b>7 stars:</b> ".$data["7ships"]."\n";
    $ret .= $this->translatedText("txtInfo31");                                                    // "<b>----------- Relics -----------</b>\n";
    $ret .= $this->translatedText("txtInfo32", $data["relics"]);                                   // "<b>Total:</b> ".$data["relics"]."\n";
    $ret .= $this->translatedText("txtInfo63", $data["r8"]);                                       // "<b>Relic 8:</b> ".$data["r8"]."\n";
    $ret .= $this->translatedText("txtInfo33", $data["r7"]);                                       // "<b>Relic 7:</b> ".$data["r7"]."\n";
    $ret .= $this->translatedText("txtInfo34", $data["r6"]);                                       // "<b>Relic 6:</b> ".$data["r6"]."\n";
    $ret .= $this->translatedText("txtInfo35", $data["r5"]);                                       // "<b>Relic 5:</b> ".$data["r5"]."\n";
    $ret .= $this->translatedText("txtInfo36", $data["r4"]);                                       // "<b>Relic 4:</b> ".$data["r4"]."\n";
    $ret .= $this->translatedText("txtInfo37", $data["r3"]);                                       // "<b>Relic 3:</b> ".$data["r3"]."\n";
    $ret .= $this->translatedText("txtInfo38", $data["r2"]);                                       // "<b>Relic 2:</b> ".$data["r2"]."\n";
    $ret .= $this->translatedText("txtInfo39", $data["r1"]);                                       // "<b>Relic 1:</b> ".$data["r1"]."\n";
    $ret .= $this->translatedText("txtInfo40");                                                    // "<b>----------- Mods -----------</b>\n";
    $ret .= $this->translatedText("txtInfo41", $data["mods6"]);                                    // "<b>Mods 6:</b> ".$data["mods6"]."\n";
    $ret .= $this->translatedText("txtInfo42", $data["mods10"]);                                   // "<b>Speed +10:</b> ".$data["mods10"]."\n";
    $ret .= $this->translatedText("txtInfo43", $data["mods15"]);                                   // "<b>Speed +15:</b> ".$data["mods15"]."\n";
    $ret .= $this->translatedText("txtInfo44", $data["mods20"]);                                   // "<b>Speed +20:</b> ".$data["mods20"]."\n";
    $ret .= $this->translatedText("txtInfo45", $data["mods25"]);                                   // "<b>Speed +25:</b> ".$data["mods25"]."\n";
    $ret .= $this->translatedText("txtInfo46");                                                    // "<b>----------- Arena -----------</b>\n";
    $ret .= $this->translatedText("txtInfo47", $player[0]["arena"]["ship"]["rank"]);               // "<b>Ships:</b> ".$player[0]["arena"]["ship"]["rank"]."\n";
    $ret .= $this->translatedText("txtInfo48", $data["ssquad1"]);                                  // "    <b>Capital:</b> ".$data["ssquad1"]."\n";
    $ret .= $this->translatedText("txtInfo49", $data["ssquad2"]);                                  // "    <b>Slot 1:</b> ".$data["ssquad2"]."\n";
    $ret .= $this->translatedText("txtInfo50", $data["ssquad3"]);                                  // "    <b>Slot 2:</b> ".$data["ssquad3"]."\n";
    $ret .= $this->translatedText("txtInfo51", $data["ssquad4"]);                                  // "    <b>Slot 3:</b> ".$data["ssquad4"]."\n";
    $ret .= $this->translatedText("txtInfo52", $data["ssquad5"]);                                  // "    <b>Rein. 1:</b> ".$data["ssquad5"]."\n";
    $ret .= $this->translatedText("txtInfo53", $data["ssquad6"]);                                  // "    <b>Rein. 2:</b> ".$data["ssquad6"]."\n";
    $ret .= $this->translatedText("txtInfo54", $data["ssquad7"]);                                  // "    <b>Rein. 3:</b> ".$data["ssquad7"]."\n";
    $ret .= $this->translatedText("txtInfo55", $data["ssquad8"]);                                  // "    <b>Rein. 4:</b> ".$data["ssquad8"]."\n";
    $ret .= $this->translatedText("txtInfo56", $player[0]["arena"]["char"]["rank"]);               // "<b>Characters:</b> ".$player[0]["arena"]["char"]["rank"]."\n";
    $ret .= $this->translatedText("txtInfo57", $data["csquad1"]);                                  // "    <b>Leader:</b> ".$data["csquad1"]."\n";
    $ret .= $this->translatedText("txtInfo58", $data["csquad2"]);                                  // "    <b>Slot 2:</b> ".$data["csquad2"]."\n";
    $ret .= $this->translatedText("txtInfo59", $data["csquad3"]);                                  // "    <b>Slot 3:</b> ".$data["csquad3"]."\n";
    $ret .= $this->translatedText("txtInfo60", $data["csquad4"]);                                  // "    <b>Slot 4:</b> ".$data["csquad4"]."\n";
    $ret .= $this->translatedText("txtInfo61", $data["csquad5"]);                                  // "    <b>Slot 5:</b> ".$data["csquad5"]."\n";
    $ret .= "\n";
/*    $ret .= $this->translatedText("txtInfo62");                                                    // "<b>units</b>\n";
    foreach ($data["units"] as $key => $unit) {
      $ret .= TUnits::unitNameFromUnitId($key, $this->dataObj)."\n"; 
      $ret .= "  #: ".$unit["count"]."\n";
      $ret .= "  g13: ".$unit["g13"]."\n";
      $ret .= "  g12: ".$unit["g12"]."\n";
      $ret .= "\n";
    }
*/
    $ret .= "\n";
    $ret .= $this->translatedText("last_update", $data["updated"]);                                // "<i>Last update: ".$data["updated"]."</i>\n";

    $this->image = $this->genImageCarac($data["units"]);

    return $ret;
  }  
   
}
