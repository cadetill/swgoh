<?php
class TGA extends TBase {
    
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
      return $this->getHelp("ga", $this->error);
  
    $initialTime = microtime(true);
    
    $res = $this->processGa();

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
    FUNCIONS PROTEGIDES
  ****************************************************/
  /****************************************************
    inicialitza les variables necessaries per a la impressió de la GA
  ****************************************************/
  protected function getVariables(&$player, &$data0, &$data1, &$rank0, &$league0, &$rank1, &$league1, $units) {
    $player = $this->getInfoPlayer(); 
    //print_r($player);

    // mirem que haguem trobat Id Guild
    if ($player[0]["name"] == "")
      return -1;
    
    // inicialitzem
    $data0 = $this->iniPlayerArray();
    $data1 = $this->iniPlayerArray();
    //print_r($data0);
    //print_r($data1);
    
    // processem jugajor
    $this->processPlayer($player[0], $data0, $units);
    $this->processPlayer($player[1], $data1, $units);
  
    // control de variables temporal
    $pos0 = count($player[0]["grandArena"]);
    if ($pos0 > 0) {
      $rank0 = $player[0]["grandArena"][$pos0-1]["rank"];
      $league0 = $player[0]["grandArena"][$pos0-1]["league"];
    }
    $pos1 = count($player[1]["grandArena"]);
    if ($pos1 > 0) {
      $rank1 = $player[1]["grandArena"][$pos1-1]["rank"];
      $league1 = $player[1]["grandArena"][$pos1-1]["league"];
    }
    
    return 0;
  }
  
  /****************************************************
    inicialitza les variables necessaries per a la impressió de la GA
  ****************************************************/
  protected function printGA($player, $data0, $data1, $rank0, $league0, $rank1, $league1, $units) {
    // impressió de resultat
    $res = array();
    $res[0]  = $this->translatedText("txtCham01", array($player[0]["name"], $player[1]["name"]));                           // "<b>----- ".$player[0]["name"]." vs ".$player[1]["name"]." -----</b>\n";
    $res[0] .= $this->translatedText("txtCham02", array($player[0]["stats"][0]["value"], $player[1]["stats"][0]["value"])); // "<b>PG</b>: ".$player[0]["stats"][0]["value"]." vs ".$player[1]["stats"][0]["value"]."\n";
    $res[0] .= $this->translatedText("txtCham03", array($player[0]["stats"][1]["value"], $player[1]["stats"][1]["value"])); // "<b>PG pjs.</b>: ".$player[0]["stats"][1]["value"]." vs ".$player[1]["stats"][1]["value"]."\n";
    $res[0] .= $this->translatedText("txtCham04", array($player[0]["stats"][2]["value"], $player[1]["stats"][2]["value"])); // "<b>PG naves</b>: ".$player[0]["stats"][2]["value"]." vs ".$player[1]["stats"][2]["value"]."\n";
    $res[0] .= $this->translatedText("txtCham05");                                                                          // "<b>----------- Arena -----------</b>\n";
    $res[0] .= $this->translatedText("txtCham06", array($player[0]["arena"]["char"]["rank"], $player[1]["arena"]["char"]["rank"])); // "<b>Escuadrones</b>: ".$player[0]["arena"]["char"]["rank"]." vs ".$player[1]["arena"]["char"]["rank"]."\n";
    $res[0] .= $this->translatedText("txtCham07", array($player[0]["arena"]["ship"]["rank"], $player[1]["arena"]["ship"]["rank"])); // "<b>Naves</b>: ".$player[0]["arena"]["ship"]["rank"]." vs ".$player[1]["arena"]["ship"]["rank"]."\n";
    $res[0] .= $this->translatedText("txtCham08");                                                                          // "<b>----------- Campeonatos -----------</b>\n";
    $res[0] .= $this->translatedText("txtCham09", array($rank0, $rank1));                                                   // "<b>Rango actual</b>: ".$rank0." vs ".$rank1."\n";
    $res[0] .= $this->translatedText("txtCham10", array(ucfirst(strtolower($league0)), ucfirst(strtolower($league1))));     // "<b>Division</b>: ".ucfirst(strtolower($league0))." vs ".ucfirst(strtolower($league1))."\n";
    $res[0] .= $this->translatedText("txtCham11", array($player[0]["stats"][19]["value"], $player[1]["stats"][19]["value"])); // "<b>Mejor puntuacion obtenida</b>: ".$player[0]["stats"][19]["value"]." vs ".$player[1]["stats"][19]["value"]."\n";
    $res[0] .= $this->translatedText("txtCham12", array($player[0]["stats"][3]["value"], $player[1]["stats"][3]["value"])); // "<b>Puntuacion total</b>: ".$player[0]["stats"][3]["value"]." vs ".$player[1]["stats"][3]["value"]."\n";
    $res[0] .= $this->translatedText("txtCham13", array($player[0]["stats"][13]["value"], $player[1]["stats"][13]["value"])); // "<b>Batallas ganadas</b>: ".$player[0]["stats"][13]["value"]." vs ".$player[1]["stats"][13]["value"]."\n";
    $res[0] .= $this->translatedText("txtCham14", array($player[0]["stats"][14]["value"], $player[1]["stats"][14]["value"])); // "<b>Defensas exitosas</b>: ".$player[0]["stats"][14]["value"]." vs ".$player[1]["stats"][14]["value"]."\n";
    $res[0] .= $this->translatedText("txtCham15", array($player[0]["stats"][18]["value"], $player[1]["stats"][18]["value"])); // "<b>Territorios derrotados</b>: ".$player[0]["stats"][18]["value"]." vs ".$player[1]["stats"][18]["value"]."\n";
    $res[0] .= $this->translatedText("txtCham16", array($player[0]["stats"][15]["value"], $player[1]["stats"][15]["value"])); // "<b>Estandartes conseguidos</b>: ".$player[0]["stats"][15]["value"]." vs ".$player[1]["stats"][15]["value"]."\n";
    $res[0] .= $this->translatedText("txtCham17", array($player[0]["stats"][12]["value"], $player[1]["stats"][12]["value"])); // "<b>Ascensos conseguidos</b>: ".$player[0]["stats"][12]["value"]." vs ".$player[1]["stats"][12]["value"]."\n";
    $res[0] .= $this->translatedText("txtCham18");                                                                          // "<b>----------- Equipo -----------</b>\n";
    $res[0] .= $this->translatedText("txtCham19", array($data0["7chars"], $data1["7chars"]));                               // "<b>7 estr</b>: ".$data0["7chars"]." vs ".$data1["7chars"]."\n";
    $res[0] .= $this->translatedText("txtCham20", array($data0["g13"], $data1["g13"]));                                     // "<b>Equipo 13</b>: ".$data0["g13"]." vs ".$data1["g13"]."\n";
    $res[0] .= $this->translatedText("txtCham21", array($data0["g12"], $data1["g12"]));                                     // "<b>Equipo 12</b>: ".$data0["g12"]." vs ".$data1["g12"]."\n";
    $res[0] .= $this->translatedText("txtCham22", array($data0["g12+1"], $data1["g12+1"]));                                 // "<b>Equipo 12+1</b>: ".$data0["g12+1"]." vs ".$data1["g12+1"]."\n";
    $res[0] .= $this->translatedText("txtCham23", array($data0["g12+2"], $data1["g12+2"]));                                 // "<b>Equipo 12+2</b>: ".$data0["g12+2"]." vs ".$data1["g12+2"]."\n";
    $res[0] .= $this->translatedText("txtCham24", array($data0["g12+3"], $data1["g12+3"]));                                 // "<b>Equipo 12+3</b>: ".$data0["g12+3"]." vs ".$data1["g12+3"]."\n";
    $res[0] .= $this->translatedText("txtCham25", array($data0["g12+4"], $data1["g12+4"]));                                 // "<b>Equipo 12+4</b>: ".$data0["g12+4"]." vs ".$data1["g12+4"]."\n";
    $res[0] .= $this->translatedText("txtCham26", array($data0["g12+5"], $data1["g12+5"]));                                 // "<b>Equipo 12+5</b>: ".$data0["g12+5"]." vs ".$data1["g12+5"]."\n";
    $res[0] .= $this->translatedText("txtCham27", array($data0["g11"], $data1["g11"]));                                     // "<b>Equipo 11</b>: ".$data0["g11"]." vs ".$data1["g11"]."\n";
    $res[0] .= $this->translatedText("txtCham28", array($data0["zetas"], $data1["zetas"]));                                 // "<b>Zetas</b>: ".$data0["zetas"]." vs ".$data1["zetas"]."\n";
    $res[0] .= $this->translatedText("txtCham86", array($data0["top80"], $data1["top80"]));                                 // "<b>Top 80</b>: ".$data0["top80"]." vs ".$data1["top80"]."\n";
    $res[0] .= $this->translatedText("txtCham29");                                                                          // "<b>----------- Reliquias -----------</b>\n";
    $res[0] .= $this->translatedText("txtCham30", array($data0["relics"], $data1["relics"]));                               // "<b>Total</b>: ".$data0["relics"]." vs ".$data1["relics"]."\n";
    $res[0] .= $this->translatedText("txtCham88", array($data0["r8"], $data1["r8"]));                                       // "<b>Reliquia 8</b>: ".$data0["r7"]." vs ".$data1["r7"]."\n";
    $res[0] .= $this->translatedText("txtCham31", array($data0["r7"], $data1["r7"]));                                       // "<b>Reliquia 7</b>: ".$data0["r7"]." vs ".$data1["r7"]."\n";
    $res[0] .= $this->translatedText("txtCham32", array($data0["r6"], $data1["r6"]));                                       // "<b>Reliquia 6</b>: ".$data0["r6"]." vs ".$data1["r6"]."\n";
    $res[0] .= $this->translatedText("txtCham33", array($data0["r5"], $data1["r5"]));                                       // "<b>Reliquia 5</b>: ".$data0["r5"]." vs ".$data1["r5"]."\n";
    $res[0] .= $this->translatedText("txtCham34", array($data0["r4"], $data1["r4"]));                                       // "<b>Reliquia 4</b>: ".$data0["r4"]." vs ".$data1["r4"]."\n";
    $res[0] .= $this->translatedText("txtCham35", array($data0["r3"], $data1["r3"]));                                       // "<b>Reliquia 3</b>: ".$data0["r3"]." vs ".$data1["r3"]."\n";
    $res[0] .= $this->translatedText("txtCham36", array($data0["r2"], $data1["r2"]));                                       // "<b>Reliquia 2</b>: ".$data0["r2"]." vs ".$data1["r2"]."\n";
    $res[0] .= $this->translatedText("txtCham37", array($data0["r1"], $data1["r1"]));                                       // "<b>Reliquia 1</b>: ".$data0["r1"]." vs ".$data1["r1"]."\n";
    $res[0] .= $this->translatedText("txtCham38");                                                                          // "<b>----------- Mods -----------</b>\n";
    $res[0] .= $this->translatedText("txtCham39", array($data0["mods6"], $data1["mods6"]));                                 // "<b>Mods 6</b>: ".$data0["mods6"]." vs ".$data1["mods6"]."\n";
    $res[0] .= $this->translatedText("txtCham40", array($data0["mods10"], $data1["mods10"]));                               // "<b>Velocidad +10</b>: ".$data0["mods10"]." vs ".$data1["mods10"]."\n";
    $res[0] .= $this->translatedText("txtCham41", array($data0["mods15"], $data1["mods15"]));                               // "<b>Velocidad +15</b>: ".$data0["mods15"]." vs ".$data1["mods15"]."\n";
    $res[0] .= $this->translatedText("txtCham42", array($data0["mods20"], $data1["mods20"]));                               // "<b>Velocidad +20</b>: ".$data0["mods20"]." vs ".$data1["mods20"]."\n";
    $res[0] .= $this->translatedText("txtCham43", array($data0["mods25"], $data1["mods25"]));                               // "<b>Velocidad +25</b>: ".$data0["mods25"]." vs ".$data1["mods25"]."\n";
    $res[0] .= "\n";
    $res[1] .= $this->translatedText("txtCham87");                                                    // "<b>units</b>\n";
    foreach ($units as $unit) {
      $res[1] .= TUnits::unitNameFromUnitId($unit, $this->dataObj)."\n"; 
      $numdata0 = 0;
      $g13data0 = 0;
      $g12data0 = 0;
      $r8data0 = 0;
      $numdata1 = 0;
      $g13data1 = 0;
      $g12data1 = 0;
      $r8data1 = 0;
      if (array_key_exists($unit, $data0["units"])) {
        $numdata0 = $data0["units"][$unit]["count"];
        $g13data0 = $data0["units"][$unit]["g13"];
        $g12data0 = $data0["units"][$unit]["g12"];
        $r8data0 = $data0["units"][$unit]["r8"];
      }
      if (array_key_exists($unit, $data1["units"])) {
        $numdata1 = $data1["units"][$unit]["count"];
        $g13data1 = $data1["units"][$unit]["g13"];
        $g12data1 = $data1["units"][$unit]["g12"];
        $r8data1 = $data1["units"][$unit]["r8"];
      }
      $res[1] .= "  #: ".$numdata0." - ".$numdata1."\n";
      $res[1] .= "  r8: ".$r8data0." - ".$r8data1."\n";
      $res[1] .= "  g13: ".$g13data0." - ".$g13data1."\n";
      $res[1] .= "  g12: ".$g12data0." - ".$g12data1."\n";
      $res[1] .= "\n";
    }
    $res[1] .= "\n";

      $res[1] .= $this->translatedText("txtCham89");                                                          // "<b>-----------GA OMICRONS-----------</b>\n";
      $res[1] .= $this->translatedText(
          "txtCham90",
          [ $data0['ga_omicrons']['QUIGONJINN'], $data1['ga_omicrons']['QUIGONJINN'] ]
      );
      $res[1] .= $this->translatedText(
          "txtCham91",
          [ $data0['ga_omicrons']['DASHRENDAR'], $data1['ga_omicrons']['DASHRENDAR'] ]
      );
      $res[1] .= $this->translatedText(
          "txtCham92",
          [ $data0['ga_omicrons']['ZAMWESELL'], $data1['ga_omicrons']['ZAMWESELL'] ]
      );
      $res[1] .= $this->translatedText(
          "txtCham93",
          [ $data0['ga_omicrons']['ROSETICO'], $data1['ga_omicrons']['ROSETICO'] ]
      );
      $res[1] .= $this->translatedText(
          "txtCham94",
          [ $data0['ga_omicrons']['DARTHTALON'], $data1['ga_omicrons']['DARTHTALON'] ]
      );
      $res[1] .= $this->translatedText(
          "txtCham95",
          [ $data0['ga_omicrons']['CHIEFCHIRPA'], $data1['ga_omicrons']['CHIEFCHIRPA'] ]
      );

      return $res;
  }
  
  /****************************************************
    FUNCIONS PRIVADES
  ****************************************************/
  /****************************************************
    procesa o calcula el resultat de la GA
  ****************************************************/
  private function processGa() {
    // agafem unitats que ha de controlar el comando
    $units = TUnits::unitsForCommand($this->dataObj->guildId, 'units', 'info', $this->dataObj);
    if (!is_array($units))
      $units = array();
    //print_r($units);

    $player = array();
    $data0 = array();
    $data1 = array();
    $rank0 = '';
    $league0 = '';
    $rank1 = '';
    $league1 = '';
    $ret = $this->getVariables($player, $data0, $data1, $rank0, $league0, $rank1, $league1, $units);
    
    if ($ret == -1)
      return $this->translatedText("error6");                                   // "Ooooops! API server may have shut down. Try again later.\n\n"
    
    return $this->printGA($player, $data0, $data1, $rank0, $league0, $rank1, $league1, $units);
  }  
}
