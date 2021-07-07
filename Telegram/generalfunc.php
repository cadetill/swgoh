<?php

/**************************************************************************
  funció que retorna si una Guild es de IM
**************************************************************************/
function isIMGuild($guildRefId, $dataObj) {
  // carreguem fitxer JSON de estructura IM  
  if (file_exists($dataObj->imFile)) {
    $im = file_get_contents($dataObj->imFile);
    $im = json_decode($im, true);
  } else {
    $im = array();
  }
  
  foreach ($im as $i) {
    if ($guildRefId == $i['guildRefId'])
      return true;
  }
  
  return false;
}

/**************************************************************************
  funció que retorna si un comando és acceptat pel bot
**************************************************************************/
function isCorrectCommand($command, $dataObj) {
  return (in_array($command, $dataObj->comands));
}

/**************************************************************************
  funció que retorna si un usuari és administrador
**************************************************************************/
function isUserAdmin($user, $dataObj) {
  return (in_array($user, $dataObj->admins));
}
