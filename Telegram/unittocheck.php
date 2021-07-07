<?php
/**************************************************************************
  funció que afegeix una unitat a la llista d'unitats a ser comparades en 
  la consulta de GA o comparativa de gremis
**************************************************************************/
function unitToCheckAdd($unitName, $dataObj) {
  if (($dataObj->allycode < 100000000) || ($dataObj->allycode > 999999999)) {
    return array("The '".$dataObj->allycode."' isn't a good ally code.\n");
  }

  $initialTime = microtime(true);

  // carreguem fitxer JSON de unitats a comparar
  if (file_exists("./unitToCheck.json")) {
    $unitToCheck = file_get_contents("./unitToCheck.json");
    $unitToCheck = json_decode($unitToCheck, true);
  } else {
    $unitToCheck = array();
  }

  // carreguem fitxer d'alias  
  $defId = TAlias::aliasSearch($unitName);

  if ($defId == "") { // no és un alias
    // agafem roster del jugador (espero que estiguin totes les unitats) per poder buscar el ID de la unitat
    $swgoh = new SwgohHelp(array($dataObj->swgohUser, $dataObj->swgohPass));
    $player = $swgoh->fetchPlayer( $dataObj->allycode, $dataObj->language );
    $player = json_decode($player, true);

    // busquem unitat
    foreach($player[0]["roster"] as $unit) {
      if (strcasecmp($unitName, $unit["nameKey"]) == 0) {
        $defId = $unit["defId"];
        break;
      }
    }
  }
  
  if ($defId == ""){
    return array("Unit not found into the inventory '".$unitName."'.\n");
  }
  
  // trobat el defId, mirem que no existeixi ja a la llista 
  if (in_array($defId, $unitToCheck)) {
    return array("This unit already exist into the list.");
  } else {
    array_push($unitToCheck, $defId);
  }
  
  // guardem fitxer i sortim
  $unitToCheck = json_encode($unitToCheck, true);
  file_put_contents("./unitToCheck.json", $unitToCheck);
  
  $ret  = "Unit added\n\n";
  $ret .= "   <b>Unit</b>: ".$unitName."\n";

  $finalTime = microtime(true);
  $time = $finalTime - $initialTime;
  $ret .= "<i>Elapsed time: ".gmdate("H:i:s", $time)."</i>\n";

  return array($ret);
}

/**************************************************************************
  funció que llista les unitats a ser comparades
**************************************************************************/
function unitToCheckList($dataObj) {
  if (($dataObj->allycode < 100000000) || ($dataObj->allycode > 999999999)) {
    return array("The '".$dataObj->allycode."' isn't a good ally code.\n");
  }
  
  $initialTime = microtime(true);

  // carreguem fitxer JSON de alias  
  if (file_exists("./unitToCheck.json")) {
    $alias = file_get_contents("./unitToCheck.json");
    $alias = json_decode($alias, true);
  } else {
    $alias = array();
  }

  // agafem roster del jugador (espero que estiguin totes les unitats) per poder buscar el NAMEKEY de la unitat
  $swgoh = new SwgohHelp(array($dataObj->swgohUser, $dataObj->swgohPass));
  $player = $swgoh->fetchPlayer( $dataObj->allycode, $dataObj->language );
  $player = json_decode($player, true);

  // recorrem llista de alias i busquem nom real
  $units = array();
  foreach($alias as $unit) {
    $tmp = TUnits::unitNameFromUnitId($unit, $dataObj); 
    if ($tmp != "") {
      array_push($units, $tmp);
    }
  }

  asort($units);

  $ret = "<b>List of units to check</b>\n\n";
  foreach($units as $unit) {
    $ret .= "  - ".$unit."\n";
  }
  $ret .= "\n";
  
  $finalTime = microtime(true);
  $time = $finalTime - $initialTime;
  $ret .= "<i>Elapsed time: ".gmdate("H:i:s", $time)."</i>\n";

  return array($ret);
}

/**************************************************************************
  funció que esborra una unitat de la llista d'unitats a comparar
**************************************************************************/
function unitToCheckDel($unitName, $dataObj) {
  if (($dataObj->allycode < 100000000) || ($dataObj->allycode > 999999999)) {
    return array("The '".$dataObj->allycode."' isn't a good ally code.\n");
  }
  
  $initialTime = microtime(true);

  // carreguem fitxer JSON de unitats a comparar
  if (file_exists("./unitToCheck.json")) {
    $unitToCheck = file_get_contents("./unitToCheck.json");
    $unitToCheck = json_decode($unitToCheck, true);
  } else {
    $unitToCheck = array();
  }

  // carreguem fitxer d'alias  
  $defId = TAlias::aliasSearch($unitName);

  if ($defId == "") { // no és un alias
    // agafem roster del jugador (espero que estiguin totes les unitats) per poder buscar el ID de la unitat
    $swgoh = new SwgohHelp(array($dataObj->swgohUser, $dataObj->swgohPass));
    $player = $swgoh->fetchPlayer( $dataObj->allycode, $dataObj->language );
    $player = json_decode($player, true);

    // busquem unitat
    foreach($player[0]["roster"] as $unit) {
      if (strcasecmp($unitName, $unit["nameKey"]) == 0) {
        $defId = $unit["defId"];
        break;
      }
    }
  }
  
  // recorrem llista de alias i canviem key pel nom real
  foreach($unitToCheck as $key => $unit) {
    if (strcasecmp($unit, $defId) == 0) 
      unset($unitToCheck[$key]); 
  }
  
  // guardem fitxer i sortim
  $unitToCheck = json_encode($unitToCheck, true);
  file_put_contents("./unitToCheck.json", $unitToCheck);
  
  $ret = "Unit ".$unitName." was deleted.\n\n";
  
  $finalTime = microtime(true);
  $time = $finalTime - $initialTime;
  $ret .= "<i>Elapsed time: ".gmdate("H:i:s", $time)."</i>\n";

  return array($ret);
}

