<?php
/**************************************************************************
  function to show help from a command
**************************************************************************/
function showHelp($command = "", $lang = "") {
  if ($lang == "") {
    $lang = "ENG_US";
  }

  switch ($command) {
    case 'register':
    case '/register':
      $ret = getRegisterHelp($lang);
      break;
    case 'unregister':
    case '/unregister':
      $ret = getUnRegisterHelp($lang);
      break;
    case 'info':
    case '/info':
      $ret = getInfoHelp($lang);
      break;
    case 'zetas':
    case '/zetas':
      $ret = getZetasHelp($lang);
      break;
    case 'guild':
    case '/guild':       
      $ret = getGuildHelp($lang);
      break;
    case 'search':
    case '/search':
      $ret = getSearchHelp($lang);
      break;
    case 'search2':
    case '/search2':
      $ret = getSearch2Help($lang);
      break;
    case 'ga':
    case '/ga':
      $ret = getGAHelp($lang);
      break;
    case 'rank':
    case '/rank':
      $ret = getRankHelp($lang);
      break;
    case '+':
      $ret = adminHelp();
      break;
    case 'im':
    case '/im':
      $ret = getIMHelp($lang, false);
      break;
    case 'im+':
    case '/im+':
      $ret = getIMHelp($lang, true);
      break;
    case 'compareg':
    case '/compareg':
      $ret = getComparegHelp($lang);
      break;
    case 'champions':
    case '/champions':
      $ret = getChampionsHelp($lang);
      break;
    case 'tw':
    case '/tw':
      $ret = getTWHelp($lang, false);
      break;
    case 'tw+':
    case '/tw+':
      $ret = getTWHelp($lang, true);
      break;
    case 'alias':
    case '/alias':
      $ret = getAliasHelp($lang, false);
      break;
    case 'alias+':
    case '/alias+':
      $ret = getAliasHelp($lang, true);
      break;
    case 'units':
    case '/units':
      $ret = getUnitsHelp($lang, false);
      break;
    case 'units+':
    case '/units+':
      $ret = getUnitsHelp($lang, true);
      break;
    case 'teams':
    case '/teams':
      $ret = getTeamsHelp($lang, false);
      break;
    case 'teams+':
    case '/teams+':
      $ret = getTeamsHelp($lang, true);
      break;
    case 'gf':
    case '/gf':
      $ret = getGFHelp($lang, false);
      break;
    case 'gf+':
    case '/gf+':
      $ret = getGFHelp($lang, true);
      break;
    case 'here':
    case '/here':
      $ret = getHereHelp($lang, false);
      break;
    case 'here+':
    case '/here+':
      $ret = getHereHelp($lang, true);
      break;
    case 'panic':
    case '/panic':
      $ret = getPanicHelp($lang, false);
      break;
    case 'panic+':
    case '/panic+':
      $ret = getPanicHelp($lang, true);
      break;
    case 'rancor':
    case '/rancor':
      $ret = getRancorHelp($lang, false);
      break;
    case 'rancor+':
    case '/rancor+':
      $ret = getRancorHelp($lang, true);
      break;
    case 'stats':
    case '/stats':
      $ret = getStatsHelp($lang, false);
      break;
    case 'stats+':
    case '/stats+':
      $ret = getStatsHelp($lang, true);
      break;
    case 'statg':
    case '/statg':
      $ret = getStatsGuildHelp($lang, false);
      break;
    case 'statg+':
    case '/statg+':
      $ret = getStatsGuildHelp($lang, true);
      break;
    case 'twcheck':
    case '/twcheck':
        $ret = getTwCheckHelp($lang, false);
        break;
    case 'twcheck+':
    case '/twcheck+':
        $ret = getTwCheckHelp($lang, true);
        break;
    default:
      $ret = getGeneralHelp($lang);
      break;
  }

  return array($ret);
}

/**************************************************************************
  retorna l'ajuda del comando /stats
**************************************************************************/
function getStatsHelp($lang, $plus) {
  switch ($lang) {
    case "SPA_XM":
      $ret = "<b>COMANDO: <i>stats</i></b> (by cadetill & Liener)\n";
      $ret .= "\n";
      $ret .= "<b>Definición:</b> \n";
      $ret .= "  Comando para obtener los stats de diferentes unidades.\n\n";
      $ret .= "<b>Sintaxis:</b> \n";
      if ($plus) {
        $ret .= "  /stats <i>SubCommand</i> +<i>aliasUnit</i> +<i>needed_aliasUnit(requisito)</i> \n";
      }
      $ret .= "  /stats <i>aliasUnit</i> \n";
      $ret .= "  /stats <i>list</i> \n";
      if ($plus) {
        $ret .= "\n";
        $ret .= "<b>SubCommand:</b> \n";
        $ret .= "<b>   - add</b>: añade un nuevo equipo \n";
        $ret .= "<i>      Requisitos válidos</i> \n";
        $ret .= "<i>        + s</i>: velocidad \n";
        $ret .= "<i>        + hp</i>: salud+protección \n";
        $ret .= "<i>        + h</i>: salud \n";
        $ret .= "<i>        + p</i>: protección \n";
        $ret .= "<i>        + pd</i>: daño físico \n";
        $ret .= "<i>        + sd</i>: daño especial \n";
        $ret .= "<i>        + po</i>: potencia \n";
        $ret .= "<i>        + t</i>: tenacidad \n";
        $ret .= "<i>        + a</i>: defensa \n";
        $ret .= "<i>        + pa</i>: evasión física \n";
        $ret .= "<i>        + sa</i>: evasión especial \n";
        $ret .= "<i>        + pcc</i>: prob. crítico físico\n";
        $ret .= "<i>        + scc</i>: prob. crítico especial\n";
        $ret .= "<i>        + cd</i>: daño crítico \n";
        $ret .= "<b>   - del</b>: borra una unidad existente \n";
      }
      $ret .= "\n";
      $ret .= "<b>Ejemplos:</b> \n";
      if ($plus) {
        $ret .= "<i>    Añadir equipo:</i> \n";
        $ret .= "        /stats add +rey +rey(pd=10000;h=180000),finnh(s=325) \n";
        $ret .= "\n  Nota: las unidades se separan <b>por coma</b> y las stats <b>por punto y coma</b>\n\n";
        $ret .= "<i>    Borrar equipo:</i> \n";
        $ret .= "        /stats del +rey \n";
      }
      $ret .= "        /stats rey \n";
      $ret .= "        /stats rey +123456789 \n";
      $ret .= "\n";
      $ret .= "<b>Tiempo requerido</b>\n";
      $ret .= "  Unos 4 minutos\n\n";
      break;
    default:
      $ret = "<b>COMMAND: <i>stats</i></b> (by cadetill & Liener)\n";
      $ret .= "\n";
      $ret .= "<b>Definition:</b> \n";
      $ret .= "  Command to obtain the stats of a set of units.\n\n";
      $ret .= "<b>Syntax:</b> \n";
      if ($plus) {
        $ret .= "  /stats <i>SubCommand</i> +<i>aliasUnit</i> +<i>needed_aliasUnit(requisite)</i> \n";
      }
      $ret .= "  /stats <i>aliasUnit</i> \n";
      $ret .= "  /stats <i>list</i> \n";
      if ($plus) {
        $ret .= "\n";
        $ret .= "<b>SubCommand:</b> \n";
        $ret .= "<b>   - add</b>: adds new team \n";
        $ret .= "<i>      Valids prerequisites</i> \n";
        $ret .= "<i>        + s</i>: speed \n";
        $ret .= "<i>        + hp</i>: health+protection \n";
        $ret .= "<i>        + h</i>: health \n";
        $ret .= "<i>        + p</i>: protection \n";
        $ret .= "<i>        + pd</i>: physical damage \n";
        $ret .= "<i>        + sd</i>: special damage \n";
        $ret .= "<i>        + po</i>: potency \n";
        $ret .= "<i>        + t</i>: tenacity \n";
        $ret .= "<i>        + a</i>: armor \n";
        $ret .= "<i>        + pa</i>: physical avoidance \n";
        $ret .= "<i>        + sa</i>: special avoidance \n";
        $ret .= "<i>        + pcc</i>: physical critical chance \n";
        $ret .= "<i>        + scc</i>: special critical chance \n";
        $ret .= "<i>        + cd</i>: critical damage \n";
        $ret .= "<b>   - del</b>: del existing unit \n";
      }
      $ret .= "\n";
      $ret .= "<b>Examples:</b> \n";
      if ($plus) {
        $ret .= "<i>    Add team:</i> \n";
        $ret .= "        /stats add +rey +rey(pd=1000;h=180000),finn(s=325) \n";
        $ret .= "\n  Nota: units are separated <b>by commas</b> and stats <b>by semicolons</b>\n\n";
        $ret .= "<i>    Del team:</i> \n";
        $ret .= "        /stats del +gas \n";
      }
      $ret .= "        /stats gas\n";
      $ret .= "        /stats gas +123456789\n";
      $ret .= "\n";
      $ret .= "<b>Time required</b>\n";
      $ret .= "  Around 4 minutes \n\n";
  }
  return $ret;
}

/**************************************************************************
    return help message for /statg
 **************************************************************************/
function getStatsGuildHelp($lang, $plus) {
    switch ($lang) {
        case "SPA_XM":
            $ret = "<b>COMANDO: <i>statg</i></b> (by Liener)\n";
            $ret .= "\n";
            $ret .= "<b>Definición:</b> \n";
            $ret .= "  Comando para obtener los jugadores que no cumplen un umbral de una estadística en una unidad.\n\n";
            $ret .= "<b>Sintaxis:</b> \n";
            $ret .= "  /statg +<i>aliasUnit</i> +<i>aliasStat</i> +<i>umbral</i> \n";
            $ret .= "\n";
            $ret .= "<b>Ejemplos:</b> \n";
            if ($plus) {
                $ret .= "<i>    Comprobar los miembros con velocidad de Piett por debajo de 325:</i> \n";
                $ret .= "        /statg +piett +s +325 \n";
            }
            $ret .= "        /statg +piett +s +325 \n";
            $ret .= "\n";
            $ret .= "<b>Tiempo requerido</b>\n";
            $ret .= "  Unos 4 minutos\n\n";
            break;
        default:
            $ret = "<b>COMMAND: <i>statg</i></b> (by Liener)\n";
            $ret .= "\n";
            $ret .= "<b>Definition:</b> \n";
            $ret .= "  Command for check guild members who don't complain unit stat.\n\n";
            $ret .= "<b>Syntax:</b> \n";
            $ret .= "  /statg +<i>aliasUnit</i> +<i>aliasStat</i> +<i>threshold</i> \n";
            $ret .= "\n";
            $ret .= "<b>Examples:</b> \n";
            if ($plus) {
                $ret .= "<i>    Check guild members with Piett speed under 325:</i> \n";
                $ret .= "        /statg +piett +s +325 \n";
            }
            $ret .= "        /statg +piett +s +325 \n";
            $ret .= "\n";
            $ret .= "<b>Time required</b>\n";
            $ret .= "  Around 4 minutes \n\n";
            break;
    }
    return $ret;
}

/**************************************************************************
  retorna l'ajuda del comando /rancor
**************************************************************************/
function getRancorHelp($lang, $plus) {
  switch ($lang) {
    case "SPA_XM":
      $ret = "<b>COMANDO: <i>rancor</i></b> (by cadetill & Liener)\n";
      $ret .= "\n";
      $ret .= "<b>Definición:</b> \n";
      $ret .= "  Comando para gestionar el progreso del rancor.\n\n";
      $ret .= "<b>Sintaxis:</b> \n";
      $ret .= "  /rancor <i>SubCommand</i> +<i>percent</i> \n\n";
      $ret .= "<b>SubCommand:</b> \n";
      if ($plus) {
        $ret .= "<b>   - clear</b>: reinicia el contador del rancor \n";
      }
      $ret .= "<b>   - f1</b>: suma/muestra porcentaje a la fase 1 \n";
      $ret .= "<b>   - f2</b>: suma/muestra porcentaje a la fase 2 \n";
      $ret .= "<b>   - f3</b>: suma/muestra porcentaje a la fase 3 \n";
      $ret .= "<b>   - f4</b>: suma/muestra porcentaje a la fase 4 \n";
      $ret .= "\n";
      $ret .= "<b>Ejemplos:</b> \n";
      if ($plus) {
        $ret .= "<i>    Inicializar:</i> \n";
        $ret .= "        /rancor clear\n";
      }
      $ret .= "<i>    Incrementar porcentaje:</i> \n";
      $ret .= "        /rancor f1 +5.5\n";
      $ret .= "        /rancor f1 +5.5 +123456789\n";
      $ret .= "<i>    Listar porcentajes:</i> \n";
      $ret .= "        /rancor f1\n";
      $ret .= "        /rancor f1 +123456789\n";
      $ret .= "\n";
      $ret .= "<b>Tiempo requerido</b>\n";
      $ret .= "  Unos 30 segundos\n\n";
      break;
    default:
      $ret = "<b>COMMAND: <i>rancor</i></b> (by cadetill & Liener)\n";
      $ret .= "\n";
      $ret .= "<b>Definition:</b> \n";
      $ret .= "  Command to manage rancor progress.\n\n";
      $ret .= "<b>Syntax:</b> \n";
      $ret .= "  /rancor <i>SubCommand</i> +<i>percent</i> \n\n";
      $ret .= "<b>SubCommand:</b> \n";
      if ($plus) {
        $ret .= "<b>   - clear</b>: reset the rancor progress \n";
      }
      $ret .= "<b>   - f1</b>: sum/shows percent of phase 1 \n";
      $ret .= "<b>   - f2</b>: sum/shows percent of phase 2 \n";
      $ret .= "<b>   - f3</b>: sum/shows percent of phase 3 \n";
      $ret .= "<b>   - f4</b>: sum/shows percent of phase 4 \n";
      $ret .= "\n";
      $ret .= "<b>Examples:</b> \n";
      if ($plus) {
        $ret .= "<i>    Initialize:</i> \n";
        $ret .= "        /rancor clear\n";
      }
      $ret .= "<i>    Increase percentage:</i> \n";
      $ret .= "        /rancor f1 +5.5\n";
      $ret .= "        /rancor f1 +5.5 +123456789\n";
      $ret .= "<i>    List percentage:</i> \n";
      $ret .= "        /rancor f1\n";
      $ret .= "        /rancor f1 +123456789\n";
      $ret .= "\n";
      $ret .= "<b>Time required</b>\n";
      $ret .= "  Around 30 seconds \n\n";
  }
  return $ret;
}

/**************************************************************************
  retorna l'ajuda del comando /panic
**************************************************************************/
function getPanicHelp($lang, $plus) {
  switch ($lang) {
    case "SPA_XM":
      $ret = "<b>COMANDO: <i>panic</i></b> (by cadetill & Liener)\n";
      $ret .= "\n";
      $ret .= "<b>Definición:</b> \n";
      $ret .= "  Comando que mostrará los personajes necesarios para uno determinado.\n\n";
      $ret .= "<b>Sintaxis:</b> \n";
      if ($plus) {
        $ret .= "  /panic <i>SubCommand</i> +<i>aliasUnit</i> +<i>needed_aliasUnit(prerequisito)</i> \n";
      }
      $ret .= "  /panic <i>aliasUnit</i> \n";
      $ret .= "  /panic <i>list</i> \n";
      if ($plus) {
        $ret .= "\n";
        $ret .= "<b>SubCommand:</b> \n";
        $ret .= "<b>   - add</b>: añade una nueva unidad \n";
        $ret .= "<i>      Prerequisitos válidos</i> \n";
        $ret .= "<i>        + l</i>: nivel (1 a 85) \n";
        $ret .= "<i>        + g</i>: equipo (1 a 13) \n";
        $ret .= "<i>        + r</i>: reliquias (1 a 8) \n";
        $ret .= "<i>        + gp</i>: poder galáctico \n";
        $ret .= "<i>        + s</i>: estrellas \n";
        $ret .= "<b>   - del</b>: borra una unidad existente \n";
      }
      $ret .= "\n";
      $ret .= "<b>Ejemplos:</b> \n";
      if ($plus) {
        $ret .= "<i>    Añadir una unidad (sin prerequisitos):</i> \n";
        $ret .= "        /panic add +gat +hera,Ezra,Kanan,Sabine,zeb,chopper \n";
        $ret .= "<i>    Añadir una unidad (con prerequisitos):</i> \n";
        $ret .= "        /panic add +gas +gk(gp=17700),3po(gp=17700),ti(gp=17700),padme(gp=17700),at(gp=17700),av(gp=17700),b1(gp=17700),b2(gp=17700),magna(gp=17700),dk(gp=17700) \n";
        $ret .= "<i>    Borrar una unidad:</i> \n";
        $ret .= "        /panic del +gas \n";
      }
      $ret .= "<i>    Listar de panic:</i> \n";
      $ret .= "        /panic list\n";
      $ret .= "        /panic list +aliasUnit \n";
      $ret .= "<i>    Obtener un panic:</i> \n";
      $ret .= "        /panic gas\n";
      $ret .= "        /panic gas +123456789\n";
      $ret .= "\n";
      $ret .= "<b>Tiempo requerido</b>\n";
      $ret .= "  Unos 4 segundos\n\n";
      break;
    default:
      $ret = "<b>COMMAND: <i>panic</i></b> (by cadetill & Liener)\n";
      $ret .= "\n";
      $ret .= "<b>Definition:</b> \n";
      $ret .= "  Command that will show the necessary characters for a certain one.\n\n";
      $ret .= "<b>Syntax:</b> \n";
      if ($plus) {
        $ret .= "  /panic <i>SubCommand</i> +<i>aliasUnit</i> +<i>needed_aliasUnit(prerequisite)</i> \n";
      }
      $ret .= "  /panic <i>aliasUnit</i> \n";
      $ret .= "  /panic <i>list</i> \n";
      if ($plus) {
        $ret .= "\n";
        $ret .= "<b>SubCommand:</b> \n";
        $ret .= "<b>   - add</b>: add new unit \n";
        $ret .= "<i>      Valids prerequisites</i> \n";
        $ret .= "<i>        + l</i>: level (1 a 85) \n";
        $ret .= "<i>        + g</i>: gear (1 a 13) \n";
        $ret .= "<i>        + r</i>: relics (1 a 8) \n";
        $ret .= "<i>        + gp</i>: galactic power \n";
        $ret .= "<i>        + s</i>: stars \n";
        $ret .= "<b>   - del</b>: del existing unit \n";
      }
      $ret .= "\n";
      $ret .= "<b>Examples:</b> \n";
      if ($plus) {
        $ret .= "<i>    Add unit (without prerequisites):</i> \n";
        $ret .= "        /panic add +gat +hera,Ezra,Kanan,Sabine,zeb,chopper \n";
        $ret .= "<i>    Add unit (with prerequisites):</i> \n";
        $ret .= "        /panic add +gas +gk(gp=17700),3po(gp=17700),ti(gp=17700),padme(gp=17700),at(gp=17700),av(gp=17700),b1(gp=17700),b2(gp=17700),magna(gp=17700),dk(gp=17700) \n";
        $ret .= "<i>    Del unit:</i> \n";
        $ret .= "        /panic del +gas \n";
      }
      $ret .= "<i>    Panic list:</i> \n";
      $ret .= "        /panic list\n";
      $ret .= "        /panic list +aliasUnit \n";
      $ret .= "<i>    Get a panic:</i> \n";
      $ret .= "        /panic gas\n";
      $ret .= "        /panic gas +123456789\n";
      $ret .= "\n";
      $ret .= "<b>Time required</b>\n";
      $ret .= "  Around 4 seconds \n\n";
  }
  return $ret;
}

/**************************************************************************
  retorna l'ajuda del comando /here
**************************************************************************/
function getHereHelp($lang, $plus) {
  switch ($lang) {
    case "SPA_XM":
      $ret = "<b>COMANDO: <i>here</i></b> (by cadetill & Liener)\n";
      $ret .= "\n";
      $ret .= "<b>Definición:</b> \n";
      $ret .= "  Comando para mencionar a usuarios.\n\n";
      $ret .= "<b>Sintaxis:</b> \n";
      if ($plus) {
        $ret .= "  /here <i>SubCommand</i> +<i>group</i> +<i>telegram_nick</i> \n";
      }
      $ret .= "  /here <i>SubCommand</i> \n\n";
      $ret .= "<b>SubCommand dependientes de gremio:</b> \n";
      if ($plus) {
        $ret .= "<b>   - add</b>: añade una persona a un grupo \n";
        $ret .= "<b>   - del</b>: borra una persona de un grupo \n";
        $ret .= "<b>   - list</b>: muestra las personas de todos los grupos \n";
      }
      $ret .= "<b>   - ofis</b>: menciona el grupo OFI \n";
      $ret .= "<b>   - tw</b>: menciona el grupo TW \n";
      $ret .= "<b>   - tb</b>: menciona el grupo TB \n";
      $ret .= "<b>   - raids</b>: menciona el grupo RAIDS \n";
      $ret .= "<b>   - 600</b>: menciona el grupo 600 \n";
      $ret .= "<b>   - leaders</b>: menciona a todos los líderes de IM \n";
//      $ret .= "<b>   - all</b>: menciona a todos los de tu gremio \n\n";
      $ret .= "\n";
      $ret .= "<b>SubCommand independientes de gremio:</b> \n";
      $ret .= "<b>   - bot</b>: menciona el grupo bot \n";
      $ret .= "<b>   - recruiter</b>: menciona el grupo reclutadores \n";
      $ret .= "\n";
      $ret .= "<b>Ejemplos:</b> \n";
      if ($plus) {
        $ret .= "<i>    Añadir a un grupo:</i> \n";
        $ret .= "        /here add +tw +cadetill\n";
        $ret .= "<i>    Borrar de un grupo:</i> \n";
        $ret .= "        /here del +tw +cadetill \n";
        $ret .= "<i>    Listar grupos:</i> \n";
        $ret .= "        /here list \n";
      }
      $ret .= "<i>    Mencionar un grupo:</i> \n";
      $ret .= "        /here ofis\n";
      $ret .= "        /here tw\n";
      $ret .= "        /here tb\n";
      $ret .= "        /here raids\n";
      $ret .= "        /here 600\n";
//      $ret .= "        /here all\n\n";
      $ret .= "\n";
      $ret .= "<b>Tiempo requerido</b>\n";
      $ret .= "  Unos 4 segundos\n\n";
      break;
    default:
      $ret = "<b>COMMAND: <i>here</i></b> (by cadetill & Liener)\n";
      $ret .= "\n";
      $ret .= "<b>Definition:</b> \n";
      $ret .= "  Command to tag users.\n\n";
      $ret .= "<b>Syntax:</b> \n";
      if ($plus) {
        $ret .= "  /here <i>SubCommand</i> +<i>group</i> +<i>telegram_nick</i> \n";
      }
      $ret .= "  /here <i>SubCommand</i> \n\n";
      $ret .= "<b>SubCommand dependent of guild:</b> \n";
      if ($plus) {
        $ret .= "<b>   - add</b>: add a person to a group \n";
        $ret .= "<b>   - del</b>: del a person from a group \n";
        $ret .= "<b>   - list</b>: shows all peoples from all groups \n";
      }
      $ret .= "<b>   - ofis</b>: tag group OFI \n";
      $ret .= "<b>   - tw</b>: tag group TW \n";
      $ret .= "<b>   - tb</b>: tag group TB \n";
      $ret .= "<b>   - raids</b>: tag group RAIDS \n";
      $ret .= "<b>   - 600</b>: tag group 600 \n";
      $ret .= "<b>   - leaders</b>: tag all IM leaders \n";
//      $ret .= "<b>   - all</b>: tag all people from your guild \n\n";
      $ret .= "\n";
      $ret .= "<b>SubCommand independent of guild:</b> \n";
      $ret .= "<b>   - bot</b>: tag group bot \n";
      $ret .= "<b>   - recruiter</b>: tag group recruiter \n";
      $ret .= "\n";
      $ret .= "<b>Examples:</b> \n";
      if ($plus) {
        $ret .= "<i>    Add to a group:</i> \n";
        $ret .= "        /here add +tw +cadetill\n";
        $ret .= "<i>    Del froma group:</i> \n";
        $ret .= "        /here del +tw +cadetill \n";
        $ret .= "<i>    List all groups:</i> \n";
        $ret .= "        /here list \n";
      }
      $ret .= "<i>    Tag a group:</i> \n";
      $ret .= "        /here ofis\n";
      $ret .= "        /here tw\n";
      $ret .= "        /here tb\n";
      $ret .= "        /here raids\n";
      $ret .= "        /here 600\n";
//      $ret .= "        /here all\n\n";
      $ret .= "\n";
      $ret .= "<b>Time required</b>\n";
      $ret .= "  Around 4 seconds \n\n";
  }
  return $ret;
}

/**************************************************************************
  retorna l'ajuda del comando /gf
**************************************************************************/
function getGFHelp($lang, $plus) {
  switch ($lang) {
    case "SPA_XM":
      $ret = "<b>COMANDO: <i>gf</i></b> (by cadetill & Liener)\n";
      $ret .= "\n";
      $ret .= "<b>Definición:</b> \n";
      $ret .= "  Comandos para la comprobación de unidades en el gremio.\n\n";
      $ret .= "<b>Sintaxis:</b> \n";
      $ret .= "  /gf <i>SubCommand</i> +<i>unit</i> \n\n";
      $ret .= "<b>SubCommand:</b> \n";
      if ($plus) {
        $ret .= "<b>   - add</b>: añade una unidad a la lista \n";
        $ret .= "<b>   - del</b>: borra una unidad de la lista \n";
        $ret .= "<b>   - clear</b>: borra la lista completa \n";
      }
      $ret .= "<b>   - list</b>: muestra las unidades de la lista \n";
      $ret .= "<b>   - check</b>: realiza comprobación \n\n";
      $ret .= "<b>Ejemplos:</b> \n";
      if ($plus) {
        $ret .= "<i>    Añadir unidad:</i> \n";
        $ret .= "        /gf add +gs\n";
        $ret .= "<i>    Borrar unidad:</i> \n";
        $ret .= "        /gf del +gs \n";
        $ret .= "<i>    Borrar lista:</i> \n";
        $ret .= "        /gf clear \n";
      }
      $ret .= "<i>    Listar unidades:</i> \n";
      $ret .= "        /gf list \n";
      $ret .= "<i>    Hacer comprobación:</i> \n";
      $ret .= "        /gf check\n\n";
      $ret .= "<b>Tiempo requerido</b>\n";
      $ret .= "  Unos 6 min (1h caché -> 30seg)\n\n";
      break;
    default:
      $ret = "<b>COMMAND: <i>gf</i></b> (by cadetill & Liener)\n";
      $ret .= "\n";
      $ret .= "<b>Definition:</b> \n";
      $ret .= "  Commands for checking units in the guild.\n\n";
      $ret .= "<b>Syntax:</b> \n";
      $ret .= "  /gf <i>SubCommand</i> +<i>unit</i> \n\n";
      $ret .= "<b>SubCommand:</b> \n";
      if ($plus) {
        $ret .= "<b>   - add</b>: add new unit to list \n";
        $ret .= "<b>   - del</b>: del unit from list \n";
        $ret .= "<b>   - clear</b>: clear units list \n";
      }
      $ret .= "<b>   - list</b>: shows all units into the list \n";
      $ret .= "<b>   - check</b>: do check \n\n";
      $ret .= "<b>Examples:</b> \n";
      if ($plus) {
        $ret .= "<i>    Add unit:</i> \n";
        $ret .= "        /gf add +gs\n";
        $ret .= "<i>    Del unit:</i> \n";
        $ret .= "        /gf del +gs \n";
        $ret .= "<i>    Clear list:</i> \n";
        $ret .= "        /gf clear \n";
      }
      $ret .= "<i>    Show units:</i> \n";
      $ret .= "        /gf list \n";
      $ret .= "<i>    Do check:</i> \n";
      $ret .= "        /gf check\n\n";
      $ret .= "<b>Time required</b>\n";
      $ret .= "  Around 6 min (1h cache -> 30sec)\n\n";
  }
  return $ret;
}

/**************************************************************************
  retorna l'ajuda del comando /teams
**************************************************************************/
function getTeamsHelp($lang, $plus) {
  switch ($lang) {
    case "SPA_XM":
      $ret = "<b>COMANDO: <i>teams</i></b> (by cadetill & Liener)\n";
      $ret .= "\n";
      $ret .= "<b>Definición:</b> \n";
      $ret .= "  Comando para la gestión de equipos.\n\n";
      $ret .= "<b>Sintaxis:</b> \n";
      $ret .= "  /teams <i>SubCommand</i> +<i>name_team</i> +<i>list_of_alias</i> \n\n";
      $ret .= "<b>SubCommand:</b> \n";
      if ($plus) {
        $ret .= "<b>   - add</b>: define un nuevo equipo \n";
        $ret .= "<b>   - del</b>: borra un equipo definido \n";
        //$ret .= "<b>   - addc</b>: añade el equipo a un comando del bot \n";
        //$ret .= "<b>   - delc</b>: borra el equipo de un comando del bot \n";
      }
      $ret .= "<b>   - list</b>: lista de equipos \n";
      $ret .= "<b>   - get</b>: coge información de un equipo. Puedes generar salida en CSV con +csv como parámetro de ordenación. Puedes especificar un allycode para mirar ese equipo en otro gremio. \n\n";
      $ret .= "<b>Ejemplos:</b> \n";
      if ($plus) {
        $ret .= "<i>    Definir nuevo equipo:</i> \n";
        $ret .= "        /teams add +501 +gs,echo,arc,rex,5s\n";
        $ret .= "<i>    Borrar equipo existente:</i> \n";
        $ret .= "        /teams del +501 \n";
        //$ret .= "<i>    Añadir equipo a comando:</i> \n";
        //$ret .= "        /teams addc +501 +compareg \n";
        //$ret .= "<i>    Borrar equipo de un comando:</i> \n";
        //$ret .= "        /teams delc +501 +compareg \n";
      }
      $ret .= "<i>    Listar equipos:</i> \n";
      $ret .= "        /teams list\n";
      $ret .= "<i>    Información:</i> \n";
      $ret .= "        /teams get +501\n";
      $ret .= "        /teams get +501 +gp\n";
      $ret .= "        /teams get +501 +csv\n";
      $ret .= "        /teams get +501 +123456789\n";
      $ret .= "        /teams get +501 +gp +123456789\n";
      break;
    default:
      $ret = "<b>COMMAND: <i>teams</i></b> (by cadetill & Liener)\n";
      $ret .= "\n";
      $ret .= "<b>Definition:</b> \n";
      $ret .= "  Commands for manage teams.\n\n";
      $ret .= "<b>Syntax:</b> \n";
      $ret .= "  /teams <i>SubCommand</i> +<i>name_team</i> +<i>list_of_alias</i> \n\n";
      $ret .= "<b>SubCommand:</b> \n";
      if ($plus) {
        $ret .= "<b>   - add</b>: define a new team \n";
        $ret .= "<b>   - del</b>: delete a team \n";
        //$ret .= "<b>   - addc</b>: add team to a bot command \n";
        //$ret .= "<b>   - delc</b>: del team from a bot command \n";
      }
      $ret .= "<b>   - list</b>: list a defined teams \n";
      $ret .= "<b>   - get</b>: get information about a team. You can generate a csv file with +csv as sort param. You can specifies an allycode to search this team into that guild \n\n";
      $ret .= "<b>Examples:</b> \n";
      if ($plus) {
        $ret .= "<i>    Define new team:</i> \n";
        $ret .= "        /teams add +501 +gs,echo,arc,rex,5s\n";
        $ret .= "<i>    Delete defined team:</i> \n";
        $ret .= "        /teams del +501 \n";
        //$ret .= "<i>    Add team to command:</i> \n";
        //$ret .= "        /teams addc +501 +compareg \n";
        //$ret .= "<i>    Del team from command:</i> \n";
        //$ret .= "        /teams delc +501 +compareg \n";
      }
      $ret .= "<i>    List teams:</i> \n";
      $ret .= "        /teams list\n";
      $ret .= "<i>    Information:</i> \n";
      $ret .= "        /teams get +501\n";
      $ret .= "        /teams get +501 +gp\n";
      $ret .= "        /teams get +501 +csv\n";
      $ret .= "        /teams get +501 +123456789\n";
      $ret .= "        /teams get +501 +gp +123456789\n";
  }
  return $ret;
}

/**************************************************************************
  retorna l'ajuda general
**************************************************************************/
function getTwCheckHelp($lang, $plus)
{
    switch ($lang) {
        case "SPA_XM":
            $ret = "<b>COMANDO: <i>tw check</i></b> (by cadetill & Liener)\n";
            $ret .= "\n";
            $ret .= "<b>Definición:</b> \n";
            $ret .= "Comando para comprobar las estadísticas para Guerras de Territorio.\n\n";
            $ret .= "<b>Ejemplos:</b> \n";
            $ret .= "  - Comprobar mi roster:\n";
            $ret .= "    /tw check +<i>AllyCode</i>\n\n";
            $ret .= "  - Ver sólo los que no cumplo:\n";
            $ret .= "    /tw check +<i>pending</i> +<i>AllyCode</i>\n\n";
            $ret .= "  - Comprobar mi roster para un equipo en concreto:\n";
            $ret .= "    /tw check +<i>aliasEquipo</i> +<i>AllyCode</i>\n\n";
            $ret .= "  - Ver la lista de comprobaciones:\n";
            $ret .= "    /tw check +<i>show</i> +<i>AllyCode</i>\n\n";
            if ($plus) {
                $ret .= "  - Añadir comprobación:\n";
                $ret .= "    /tw check +<i>add</i> +<i>alias</i> +<i>definición</i> +<i>AllyCode</i>\n\n";
                $ret .= "  - Actualizar comprobación:\n";
                $ret .= "    /tw check +<i>update</i> +<i>alias</i> +<i>definición</i> +<i>AllyCode</i>\n\n";
                $ret .= "  - Eliminar comprobación:\n";
                $ret .= "    /tw check +<i>del</i> +<i>alias</i> +<i>definición</i> +<i>AllyCode</i>\n\n";
                $ret .= "<b>Sintaxis:</b> \n";
                $ret .= "  - Orden de velocidades:  order(alias1,alias2,alias3)\n";
                $ret .= "    order(bb8,ipd,t3,ig88,gg)\n\n";
                $ret .= "  - Nivel de reliquia: alias(r,nivelDeReliquiaMinimo)\n";
                $ret .= "    fennec(r,7)\n\n";
                $ret .= "  - Valor de una estadística: alias(aliasEstadistica[,comparador],valorDeEstadisticaMinimo)\n";
                $ret .= "    kuiil(s,340)\n";
                $ret .= "    cat(s,&lt;,280)\n\n";
                $ret .= "  - Valor de una estadística comparado con otra unidad: alias(aliasEstadistica,aliasSegundaUnidad,comparador,fórmula)\n";
                $ret .= "    gas(s,rex,>=,r*0.7)\n";
                $ret .= "    En la fórmula <b>r</b> representa el valor de esa estadística en la segunda unidad.\n\n";
                $ret .= "  - Habilidad maximizada: alias(sk,habilidad)\n";
                $ret .= "    mara(sk,u)\n\n";
                $ret .= "<b>Alias de las estadísticas:</b> \n";
                $ret .= "  - s: Velocidad\n";
                $ret .= "  - h: Salud\n";
                $ret .= "  - p: Protección\n";
                $ret .= "  - pd: Daño Físico\n";
                $ret .= "  - sd: Daño especial\n";
                $ret .= "  - po: Potencia\n";
                $ret .= "  - t: Tenacidad\n";
                $ret .= "  - a: Armadura\n";
                $ret .= "  - pa: Evasión crítica física\n";
                $ret .= "  - sa: Evasión crítical especial\n";
                $ret .= "  - pcc: Probabilidad de daño crítifo físico\n";
                $ret .= "  - scc: Probabilidad de daño crítico especial\n";
                $ret .= "  - cd: Daño crítico\n";
                $ret .= "<b>Alias de las habilidades:</b> \n";
                $ret .= "  - b: Básica\n";
                $ret .= "  - s: Especial\n";
                $ret .= "  - l: Líder\n";
                $ret .= "  - u: Única\n";
                $ret .= "  Por defecto será la primera habilidad de cada tipo, puede indicarse el orden, por ejemplo <b>u2</b> para la segunda única.\n";
            }
            break;
        default:
            $ret = "<b>COMMAND: <i>tw check</i></b> (by cadetill & Liener)\n";
            $ret .= "\n";
            $ret .= "<b>Definition:</b> \n";
            $ret .= "Command for check roster stats for TW.\n\n";
            $ret .= "<b>Examples:</b> \n";
            $ret .= "  - Check my roster:\n";
            $ret .= "    /tw check +<i>AllyCode</i>\n\n";
            $ret .= "  - Show non-complain requirements:\n";
            $ret .= "    /tw check +<i>pending</i> +<i>AllyCode</i>\n\n";
            $ret .= "  - Check my roster for single group:\n";
            $ret .= "    /tw check +<i>aliasEquipo</i> +<i>AllyCode</i>\n\n";
            $ret .= "  - Show the requirements:\n";
            $ret .= "    /tw check +<i>show</i> +<i>AllyCode</i>\n\n";
            if ($plus) {
                $ret .= "  - Add requirement:\n";
                $ret .= "    /tw check +<i>add</i> +<i>alias</i> +<i>definición</i> +<i>AllyCode</i>\n\n";
                $ret .= "  - Update requirement:\n";
                $ret .= "    /tw check +<i>update</i> +<i>alias</i> +<i>definición</i> +<i>AllyCode</i>\n\n";
                $ret .= "  - Delete requirement:\n";
                $ret .= "    /tw check +<i>del</i> +<i>alias</i> +<i>definición</i> +<i>AllyCode</i>\n\n";
                $ret .= "<b>Syntax:</b> \n";
                $ret .= "  - Speed order:  order(alias1,alias2,alias3)\n";
                $ret .= "    order(bb8,ipd,t3,ig88,gg)\n\n";
                $ret .= "  - Relic level: alias(r,nivelDeReliquiaMinimo)\n";
                $ret .= "    fennec(r,7)\n\n";
                $ret .= "  - Stat value: alias(statAlias[,comparator],statValue)\n";
                $ret .= "    kuiil(s,340)\n";
                $ret .= "    cat(s,&lt;,280)\n\n";
                $ret .= "  - Stat value compared against another unit: alias(statAlias,aliasUnit,comparator,formula)\n";
                $ret .= "    gas(s,rex,>=,r*0.7)\n";
                $ret .= "    In the formula <b>r</b> represent the stat valur of second unit.\n\n";
                $ret .= "  - Maxed skill: alias(sk,habilidad)\n";
                $ret .= "    mara(sk,u)\n\n";
                $ret .= "<b>Stat alias:</b> \n";
                $ret .= "  - s: Speed\n";
                $ret .= "  - h: health\n";
                $ret .= "  - p: Protection\n";
                $ret .= "  - pd: Physical damage\n";
                $ret .= "  - sd: Special Damage\n";
                $ret .= "  - po: Potency\n";
                $ret .= "  - t: Tenacity\n";
                $ret .= "  - a: Armor\n";
                $ret .= "  - pa: Physical critical avoidance\n";
                $ret .= "  - sa: Special critical avoidance\n";
                $ret .= "  - pcc: Physical crit chance\n";
                $ret .= "  - scc: Special crit chance\n";
                $ret .= "  - cd: Crit damage\n";
                $ret .= "<b>Skill alias:</b> \n";
                $ret .= "  - b: Basic\n";
                $ret .= "  - s: Special\n";
                $ret .= "  - l: Leader\n";
                $ret .= "  - u: unique\n";
                $ret .= "  By default will be first skill os its type, you can set the index, ie <b>u2</b> for the second unique.\n";
            }
            break;
    return $ret;
}

function getGeneralHelp($lang) {
  switch ($lang) {
    case "SPA_XM":
      $ret = "<b>COMANDOS</b> (by cadetill & Liener)\n";
      $ret .= "\n";
      $ret .= "<a http=''>/help</a>: muestra esta ayuda\nPara más ayuda de un comando específico: /help comando\n\n";
      $ret .= "<a http=''>/register</a>: registra tu AllyCode.\n\n";
      $ret .= "<a http=''>/unregister</a>: borra tu AllyCode.\n\n";
      $ret .= "<a http=''>/info</a>: información sobre una cuenta.\n\n";
      $ret .= "<a http=''>/zetas</a>: muestra las zetas desbloqueadas de un jugador.\n\n";
      $ret .= "<a http=''>/guild</a>: muestra información de un gremio. Ver la ayuda detallada para más información\n\n";
      $ret .= "<a http=''>/search</a>: busca una unidad en los miembros del gremio.\n\n";
      $ret .= "<a http=''>/search2</a>: busca una unidad en los miembros del gremio.\n\n";
      $ret .= "<a http=''>/ga</a>: compara una cuenta con otra.\n\n";
      $ret .= "<a http=''>/rank</a>: muestra la unidad ordenada por una estadística específica.\n\n";
      $ret .= "<a http=''>/im</a>: muestra todos los gremios del grupo Imperio Mandaloriano.\n\n";
      $ret .= "<a http=''>/compareg</a>: compara dos gremios.\n\n";
      $ret .= "<a http=''>/champions</a>: compara dos AlliCodes para una Champions interna.\n\n";
      $ret .= "<a http=''>/tw</a>: comandos para las guerras de territorio. Ver la ayuda detallada para más información.\n\n";
      $ret .= "<a http=''>/alias</a>: comandos para los alias. Ver la ayuda detallada para más información.\n\n";
      $ret .= "<a http=''>/teams</a>: comandos para la gestión de equipos. Ver la ayuda detallada para más información\n\n";
      $ret .= "<a http=''>/gf</a>: comandos para la comprobación de unidades en el gremio. Ver la ayuda detallada para más información\n\n";
      $ret .= "<a http=''>/here</a>: comando para mencionar a usuarios. Ver la ayuda detallada para más información.\n\n";
      $ret .= "<a http=''>/panic</a>: comando que mostrará los personajes necesarios para uno determinado. Ver la ayuda detallada para más información.\n\n";
      $ret .= "<a http=''>/stats</a>: comando para obtener las stats deseadas de una serie de personajes. Ver la ayuda detallada para más información.\n\n";
      $ret .= "<a http=''>/statg</a>: comando para comprobar estadísticas de unidades en el gremio. Ver la ayuda detallada para más información.\n\n";
      break;
    default:
      $ret = "<b>COMMANDS</b> (by cadetill & Liener)\n";
      $ret .= "\n";
      $ret .= "<a http=''>/help</a>: shows this help\nMore help for a specific command: /help command\n\n";
      $ret .= "<a http=''>/register</a>: registers your AllyCode.\n\n";
      $ret .= "<a http=''>/unregister</a>: unregisters your AllyCode.\n\n";
      $ret .= "<a http=''>/info</a>: info about an account.\n\n";
      $ret .= "<a http=''>/zetas</a>: shows zetas unlocked from a player.\n\n";
      $ret .= "<a http=''>/guild</a>: shows info from a guild\n\n";
      $ret .= "<a http=''>/search</a>: search a unit into the guild members.\n\n";
      $ret .= "<a http=''>/search2</a>: search a unit into the guild members.\n\n";
      $ret .= "<a http=''>/ga</a>: compare an account with another.\n\n";
      $ret .= "<a http=''>/rank</a>: shows an unit ordered by the specified stat.\n\n";
      $ret .= "<a http=''>/im</a>: shows all guilds from Imperio Mandaloriano group.\n\n";
      $ret .= "<a http=''>/compareg</a>: compare two guilds.\n\n";
      $ret .= "<a http=''>/champions</a>: compare two AlliCodes for a internal Champions.\n\n";
      $ret .= "<a http=''>/tw</a>: commands for a territory war. See detailed help for more info.\n\n";
      $ret .= "<a http=''>/alias</a>: commands for alias. See detailed help for more info.\n\n";
      $ret .= "<a http=''>/teams</a>: commands for manage teams. See detailed help for more info.\n\n";
      $ret .= "<a http=''>/gf</a>: commands for checking units in the guild. See detailed help for more info.\n\n";
      $ret .= "<a http=''>/here</a>: command to tag users. See detailed help for more info.\n\n";
      $ret .= "<a http=''>/panic</a>: command that will show the necessary characters for a certain one. See detailed help for more info.\n\n";
      $ret .= "<a http=''>/stats</a>: command to obtain some stats from a list of units. See detailed help for more info.\n\n";
      $ret .= "<a http=''>/statg</a>: command for check unit stat in guild. See detailed help for more info.\n\n";
  }
  return $ret;
}

/**************************************************************************
  retorna l'ajuda del comando /tw
**************************************************************************/
function getTWHelp($lang, $plus) {
  switch ($lang) {
    case "SPA_XM":
      $ret = "<b>COMANDO: <i>tw</i></b> (by cadetill & Liener)\n";
      $ret .= "\n";
      $ret .= "<b>Definición:</b> \n";
      $ret .= "  Comando para gestionar las Guerras de Territorio.\n\n";
      $ret .= "<b>Sintaxis:</b> \n";
      $ret .= "  /tw <i>SubCommand</i> +<i>alias</i> +<i>points</i> +<i>AllyCode</i>\n\n";
      $ret .= "  Formato para el AllyCode: xxxxxxxxx o xxx-xxx-xxx\n\n";
      $ret .= "<b>SubCommand:</b> \n";
      if ($plus) {
        $ret .= "<b>   - new</b>: crea una nueva instancia (sólo 1 vez por gt) \n";
        $ret .= "<b>   - dates</b>: listado de fecha/jugador \n";
        $ret .= "<b>   - noreg</b>: añade jugadores no registrados en la GT separados por coma. Con el parámetro +list muestra los jugadores añadidos hasta el momento. \n";
      }
      $ret .= "<b>   - def</b>: define un equipo defensivo \n";
      $ret .= "<b>   - off</b>: define un equipo atacante. Tienes que poner el equipo usado, los puntos sacados y el equipo atacado \n";
      $ret .= "<b>   - used</b>: define una unidad como usada sin contabilizarla en el resultado \n";
      $ret .= "<b>   - search</b>: lista los jugadores que han usado o no un oquipo y los puntos obtenidos \n";
      $ret .= "<b>   - del</b>: borra el equipo especificado \n";
      $ret .= "<b>   - me</b>: lista los equipos usados por un jugador \n";
      $ret .= "<b>   - all</b>: visión general de la GT \n";
      $ret .= "<b>   - rogue</b>: añade una rogue falsa \n";
      $ret .= "<b>   - roguelist</b>: lista todas las rogues falsas \n";
      $ret .= "<b>   - review</b>: muestra el resultado de la GT \n";
      $ret .= "<b>   - attacks</b>: muestra todos los ataques de la GT. Puedes ordenar el resultado usando un parámetro extra (título de la columna) \n";
      $ret .= "<b>   - defenses</b>: muestra los puntos defensivos por jugador. \n";
      $ret .= "<b>   - save</b>: guarda el resultado de la GT actual (información de attacks) en la tabla de histórico. La fecha tiene que ser en formato aaaammdd. \n";
      $ret .= "<b>   - delh</b>: borra los datos históricos guardado de una GT. La fecha tiene que ser en formato aaaammdd. \n";
      $ret .= "<b>   - listh</b>: muestra los datos guardados de las GTs de los últimos 24 meses. \n";
      $ret .= "<b>   - history</b>: histórico por jugador de los últimos 24 meses.Puedes ordenar el resultado usando un parámetro extra (título de la columna) \n";
      $ret .= "<b>   - check</b>: comprueba las estadísticas definidas por los oficiales para las Guerras Territoriales \n";
      $ret .= " \n";
      $ret .= "<b>Ejemplos:</b> \n";
      if ($plus) {
        $ret .= "<i>    Crear nueva instancia:</i> \n";
        $ret .= "        /tw new\n";
        $ret .= "<i>    Listado de fechas:</i> \n";
        $ret .= "        /tw dates\n";
        $ret .= "        /tw dates +date\n";
        $ret .= "<i>    Añadir jugadores no registrados:</i> \n";
        $ret .= "        /tw noreg +123456789,987654321,456123789\n";
        $ret .= "        /tw noreg +list\n";
      }
      $ret .= "<i>    Añadir defensa:</i> \n";
      $ret .= "        /tw def +gas\n";
      $ret .= "        /tw def +gas +123456789\n";
      $ret .= "<i>    Añadir ataque:</i> \n";
      $ret .= "        /tw off +gas +19 +gg\n";
      $ret .= "        /tw off +gas +19 +gg +123456789\n";
      $ret .= "<i>    Unidad usada:</i> \n";
      $ret .= "        /tw used +gas\n";
      $ret .= "        /tw used +gas +123456789\n";
      $ret .= "<i>    Buscar equipo:</i> \n";
      $ret .= "        /tw search +gas\n";
      $ret .= "<i>    Borrar equipo o rogue falsa:</i> \n";
      $ret .= "        /tw del +gas\n";
      $ret .= "        /tw del +rogue\n";
      $ret .= "        /tw del +gas +123456789\n";
      $ret .= "<i>    Lo que he usado:</i> \n";
      $ret .= "        /tw me\n";
      $ret .= "        /tw me +123456789\n";
      $ret .= "<i>    Visión general de la GT:</i> \n";
      $ret .= "        /tw all \n";
      $ret .= "<i>    Definir una nueva rogue falsa:</i> \n";
      $ret .= "        /tw rogue\n";
      $ret .= "        /tw rogue +123456789\n";
      $ret .= "<i>    Mostrar las rogues falsas:</i> \n";
      $ret .= "        /tw roguelist\n";
      $ret .= "<i>    Resultado de la GT:</i> \n";
      $ret .= "        /tw review \n";
      $ret .= "<i>    Todos los ataques de la GT:</i> \n";
      $ret .= "        /tw attacks\n";
      $ret .= "        /tw attacks +points\n";
      $ret .= "        /tw attacks +%\n";
      $ret .= "<i>    Todas las defensas de la GT:</i> \n";
      $ret .= "        /tw defenses\n";
      $ret .= "        /tw defenses +teams\n";
      $ret .= "<i>    Guardar resultado de la GT:</i> \n";
      $ret .= "        /tw save +20201231\n";
      $ret .= "<i>    Borrar resultado de una GT guardado:</i> \n";
      $ret .= "        /tw delh +20201231\n";
      $ret .= "<i>    Mostrar los resultados de las GTs guardadas:</i> \n";
      $ret .= "        /tw listh\n";
      $ret .= "        /tw listh +20201231\n";
      $ret .= "<i>    Historial de GTs:</i> \n";
      $ret .= "        /tw history (current members guild)\n";
      $ret .= "        /tw history +points\n";
      $ret .= "        /tw history +all (all history members guild)\n";
      $ret .= "        /tw history +all +points \n\n";
      $ret .= "<i>    Comprobar mi roster:</i> \n";
      $ret .= "        /tw check \n\n";
      $ret .= "<b>Tiempo requerido</b>\n";
      $ret .= "  Unos 5 seg\n\n";
      break;
    default:
      $ret = "<b>COMMAND: <i>tw</i></b> (by cadetill & Liener)\n";
      $ret .= "\n";
      $ret .= "<b>Definition:</b> \n";
      $ret .= "  Commands for a Territory War.\n\n";
      $ret .= "<b>Syntax:</b> \n";
      $ret .= "  /tw <i>SubCommand</i> +<i>alias</i> +<i>points</i> +<i>AllyCode</i>\n\n";
      $ret .= "  Format for AllyCode: xxxxxxxxx or xxx-xxx-xxx\n\n";
      $ret .= "<b>SubCommand:</b> \n";
      if ($plus) {
        $ret .= "<b>   - new</b>: create a new instance (only 1 time for tw) \n";
        $ret .= "<b>   - dates</b>: list date/player \n";
        $ret .= "<b>   - noreg</b>: adds non-GT registered players separated by commas. With +list param shows players added until this moment. \n";
      }
      $ret .= "<b>   - def</b>: define a defensive team \n";
      $ret .= "<b>   - off</b>: define an offensive team. You need to put your team used, points and enemy team \n";
      $ret .= "<b>   - used</b>: defines a unit as used not counted in the result \n";
      $ret .= "<b>   - search</b>: Lists users who have not used a team and those who have used it together with the score obtained \n";
      $ret .= "<b>   - del</b>: delete a specified team \n";
      $ret .= "<b>   - me</b>: Lists teams used by you \n";
      $ret .= "<b>   - all</b>: General vision of TW \n";
      $ret .= "<b>   - rogue</b>: Add a wrong rogue \n";
      $ret .= "<b>   - roguelist</b>: List all rogues \n";
      $ret .= "<b>   - review</b>: Shows result of TW \n";
      $ret .= "<b>   - attacks</b>: Shows all attacks of TW. You can sort the result using an extra param (column title) \n";
      $ret .= "<b>   - defenses</b>: Shows points defenses by player. \n";
      $ret .= "<b>   - save</b>: Saves current TW result (attacks info) into the historic table. The date must be in yyyymmdd format. \n";
      $ret .= "<b>   - delh</b>: Deletes an specific saved TW history. The date must be in yyyymmdd format. \n";
      $ret .= "<b>   - listh</b>: Lists saved results of TW from last 24 month. \n";
      $ret .= "<b>   - history</b>: History of the last 24 months per player. You can sort the result using an extra param (column title) \n";
      $ret .= "<b>   - check</b>: Check roster stats set up by officer for TW \n";
      $ret .= " \n";
      $ret .= "<b>Examples:</b> \n";
      if ($plus) {
        $ret .= "<i>    Create new instance:</i> \n";
        $ret .= "        /tw new\n";
        $ret .= "<i>    List date/player:</i> \n";
        $ret .= "        /tw dates\n";
        $ret .= "        /tw dates +date\n";
        $ret .= "<i>    Add non registered players:</i> \n";
        $ret .= "        /tw noreg +123456789,987654321,456123789\n";
        $ret .= "        /tw noreg +list\n";
      }
      $ret .= "<i>    Add defense:</i> \n";
      $ret .= "        /tw def +gas\n";
      $ret .= "        /tw def +gas +123456789\n";
      $ret .= "<i>    Add offense:</i> \n";
      $ret .= "        /tw off +gas +19 +gg\n";
      $ret .= "        /tw off +gas +19 +gg +123456789\n";
      $ret .= "<i>    Used unit:</i> \n";
      $ret .= "        /tw used +gas\n";
      $ret .= "        /tw used +gas +123456789\n";
      $ret .= "<i>    Search team:</i> \n";
      $ret .= "        /tw search +gas\n";
      $ret .= "<i>    Delete a team or rogue:</i> \n";
      $ret .= "        /tw del +gas\n";
      $ret .= "        /tw del +rogue\n";
      $ret .= "        /tw del +gas +123456789\n";
      $ret .= "<i>    What I used:</i> \n";
      $ret .= "        /tw me\n";
      $ret .= "        /tw me +123456789\n";
      $ret .= "<i>    General vision of TW:</i> \n";
      $ret .= "        /tw all \n";
      $ret .= "<i>    Define a new wrong rogue:</i> \n";
      $ret .= "        /tw rogue\n";
      $ret .= "        /tw rogue +123456789\n";
      $ret .= "<i>    List all rogues:</i> \n";
      $ret .= "        /tw roguelist\n";
      $ret .= "<i>    Result of TW:</i> \n";
      $ret .= "        /tw review \n";
      $ret .= "<i>    All attacks of TW:</i> \n";
      $ret .= "        /tw attacks\n";
      $ret .= "        /tw attacks +points\n";
      $ret .= "        /tw attacks +%\n";
      $ret .= "<i>    All defenses of TW:</i> \n";
      $ret .= "        /tw defenses\n";
      $ret .= "        /tw defenses +teams\n";
      $ret .= "<i>    Save results of TW:</i> \n";
      $ret .= "        /tw save +20201231\n";
      $ret .= "<i>    Delete saved result of TW:</i> \n";
      $ret .= "        /tw delh +20201231\n";
      $ret .= "<i>    List saved results of TW:</i> \n";
      $ret .= "        /tw listh\n";
      $ret .= "        /tw listh +20201231\n";
      $ret .= "<i>    History of TW:</i> \n";
      $ret .= "        /tw history (current members guild)\n";
      $ret .= "        /tw history +points\n";
      $ret .= "        /tw history +all (all history members guild)\n";
      $ret .= "        /tw history +all +points \n\n";
      $ret .= "<i>    Check my roster:</i> \n";
      $ret .= "        /tw check\n";
      $ret .= "<b>Time required</b>\n";
      $ret .= "  Around 5 sec\n\n";
  }
  return $ret;
}

/**************************************************************************
  retorna l'ajuda del comando /zetas
**************************************************************************/
function getZetasHelp($lang) {
  switch ($lang) {
    case "SPA_XM":
      $ret = "<b>COMANDO: <i>zetas</i></b> (by cadetill & Liener)\n";
      $ret .= "\n";
      $ret .= "<b>Definición:</b> \n";
      $ret .= "  Muestra las zetas desbloqueadas por un jugador.\n\n";
      $ret .= "<b>Sintaxis:</b> \n";
      $ret .= "  /zetas <i>AllyCode</i> (opcional)\n";
      $ret .= "  Formato para el AllyCode: xxxxxxxxx o xxx-xxx-xxx\n\n";
      $ret .= "<b>Ejemplos:</b> \n";
      $ret .= "  /zetas \n";
      $ret .= "  /zetas 123456789\n\n";
      $ret .= "<b>Tiempo requerido</b>\n";
      $ret .= "  Unos 5 seg\n\n";
      break;
    default:
      $ret = "<b>COMMAND: <i>zetas</i></b> (by cadetill & Liener)\n";
      $ret .= "\n";
      $ret .= "<b>Definition:</b> \n";
      $ret .= "  Shows zetas unlocked from a player.\n\n";
      $ret .= "<b>Syntax:</b> \n";
      $ret .= "  /zetas <i>AllyCode</i> (optional)\n";
      $ret .= "  Format for AllyCode: xxxxxxxxx or xxx-xxx-xxx\n\n";
      $ret .= "<b>Example:</b> \n";
      $ret .= "  /zetas \n";
      $ret .= "  /zetas 123456789\n\n";
      $ret .= "<b>Time required</b>\n";
      $ret .= "  Around 5 sec\n\n";
  }
  return $ret;
}

/**************************************************************************
  retorna l'ajuda del comando /info
**************************************************************************/
function getInfoHelp($lang) {
  switch ($lang) {
    case "SPA_XM":
      $ret = "<b>COMANDO: <i>info</i></b> (by cadetill & Liener)\n";
      $ret .= "\n";
      $ret .= "<b>Definición:</b> \n";
      $ret .= "  Muestra información a cerca de una cuenta.\n\n";
      $ret .= "<b>Sintaxis:</b> \n";
      $ret .= "  /info <i>AllyCode</i> (opcional)\n";
      $ret .= "  Formato para el AllyCode: xxxxxxxxx o xxx-xxx-xxx\n\n";
      $ret .= "<b>Ejemplos:</b> \n";
      $ret .= "  /info \n";
      $ret .= "  /info 123456789\n\n";
      $ret .= "<b>Tiempo requerido</b>\n";
      $ret .= "  Unos 5 seg\n\n";
      break;
    default:
      $ret = "<b>COMMAND: <i>info</i></b> (by cadetill & Liener)\n";
      $ret .= "\n";
      $ret .= "<b>Definition:</b> \n";
      $ret .= "  Shows info about an account.\n\n";
      $ret .= "<b>Syntax:</b> \n";
      $ret .= "  /info <i>AllyCode</i> (optional)\n";
      $ret .= "  Format for AllyCode: xxxxxxxxx or xxx-xxx-xxx\n\n";
      $ret .= "<b>Example:</b> \n";
      $ret .= "  /info \n";
      $ret .= "  /info 123456789\n\n";
      $ret .= "<b>Time required</b>\n";
      $ret .= "  Around 5 sec\n\n";
  }
  return $ret;
}

/**************************************************************************
  retorna l'ajuda del comando /unregister
**************************************************************************/
function getUnRegisterHelp($lang) {
  switch ($lang) {
    case "SPA_XM":
      $ret = "<b>COMANDO: <i>unregister</i></b> (by cadetill & Liener)\n";
      $ret .= "\n";
      $ret .= "<b>Definición:</b> \n";
      $ret .= "  Borra el registro de tu AllyCode del bot.\n\n";
      $ret .= "<b>Sintaxis:</b> \n";
      $ret .= "  /unregister \n\n";
      $ret .= "<b>Example:</b> \n";
      $ret .= "  /unregister \n\n";
      break;
    default:
      $ret = "<b>COMMAND: <i>unregister</i></b> (by cadetill & Liener)\n";
      $ret .= "\n";
      $ret .= "<b>Definition:</b> \n";
      $ret .= "  Unregisters your AllyCode from bot.\n\n";
      $ret .= "<b>Syntax:</b> \n";
      $ret .= "  /unregister \n\n";
      $ret .= "<b>Example:</b> \n";
      $ret .= "  /unregister \n\n";
  }
  return $ret;
}

/**************************************************************************
  retorna l'ajuda del comando /register
**************************************************************************/
function getRegisterHelp($lang) {
  switch ($lang) {
    case "SPA_XM":
      $ret = "<b>COMANDO: <i>register</i></b> (by cadetill & Liener)\n";
      $ret .= "\n";
      $ret .= "<b>Definición:</b> \n";
      $ret .= "  Registra tu AllyCode en el bot.\n\n";
      $ret .= "<b>Sintaxis:</b> \n";
      $ret .= "  /register <i>AllyCode</i> <i>language</i> (opcional)\n";
      $ret .= "  Formato para el AllyCode: xxxxxxxxx o xxx-xxx-xxx\n\n";
      $ret .= "<b>Idiomas:</b>\n";
      $ret .= "  CHS_CN \n";
      $ret .= "  CHT_CN \n";
      $ret .= "  ENG_US (defecto)\n";
      $ret .= "  FRE_FR \n";
      $ret .= "  GER_DE \n";
      $ret .= "  IND_ID \n";
      $ret .= "  ITA_IT \n";
      $ret .= "  JPN_JP \n";
      $ret .= "  KOR_KR \n";
      $ret .= "  POR_BR \n";
      $ret .= "  RUS_RU \n";
      $ret .= "  SPA_XM \n";
      $ret .= "  THA_TH \n";
      $ret .= "  TUR_TR \n\n";
      $ret .= "<b>Ejemplos:</b> \n";
      $ret .= "  /register 123456789 \n";
      $ret .= "  /register 123456789 SPA_XM \n\n";
      break;
    default:
      $ret = "<b>COMMAND: <i>register</i></b> (by cadetill & Liener)\n";
      $ret .= "\n";
      $ret .= "<b>Definition:</b> \n";
      $ret .= "  Registers your AllyCode to bot.\n\n";
      $ret .= "<b>Syntax:</b> \n";
      $ret .= "  /register <i>AllyCode</i> <i>language</i> (optional)\n";
      $ret .= "  Format for AllyCode: xxxxxxxxx or xxx-xxx-xxx\n\n";
      $ret .= "<b>Languages:</b>\n";
      $ret .= "  CHS_CN \n";
      $ret .= "  CHT_CN \n";
      $ret .= "  ENG_US (default)\n";
      $ret .= "  FRE_FR \n";
      $ret .= "  GER_DE \n";
      $ret .= "  IND_ID \n";
      $ret .= "  ITA_IT \n";
      $ret .= "  JPN_JP \n";
      $ret .= "  KOR_KR \n";
      $ret .= "  POR_BR \n";
      $ret .= "  RUS_RU \n";
      $ret .= "  SPA_XM \n";
      $ret .= "  THA_TH \n";
      $ret .= "  TUR_TR \n\n";
      $ret .= "<b>Example:</b> \n";
      $ret .= "  /register 123456789 \n";
      $ret .= "  /register 123456789 SPA_XM \n\n";
  }
  return $ret;
}

/**************************************************************************
  funció que retorna l'ajuda del comando /search2
**************************************************************************/
function getSearch2Help($lang) {
  switch ($lang) {
    case "SPA_XM":
      $ret = "<b>COMANDO: <i>search2</i></b> (by cadetill & Liener)\n";
      $ret .= "\n";
      $ret .= "<b>Definición:</b> \n";
      $ret .= "  Busca una unidad en los miembros del gremio. Si no se especifica un AllyCode, se buscará la estadística en el gremio del usuario. Si se especifica, se buscará en el gremio del AllyCode especificado.\n\n";
      $ret .= "<b>Sintaxis:</b> \n";
      $ret .= "  /search2 +<i>unit_name</i> +<i>sort</i> +<i>AllyCode</i> (opcional)\n";
      $ret .= "  Formato para el AllyCode: xxxxxxxxx o xxx-xxx-xxx\n\n";
      $ret .= "<b>Ejemplos:</b> \n";
      $ret .= "  /search2 +lobot\n";
      $ret .= "  /search2 +lobot +123456789 \n";
      $ret .= "  /search2 +lobot +gear \n";
      $ret .= "  /search2 +lobot +gear +123456789 \n\n";
      $ret .= "<b>Tiempo requerido</b>\n";
      $ret .= "  Unos 2 min (1h caché -> 30seg)\n\n";
      break;
    default:
      $ret = "<b>COMMAND: <i>search2</i></b> (by cadetill & Liener)\n";
      $ret .= "\n";
      $ret .= "<b>Definition:</b> \n";
      $ret .= "  Search an unit into the guild members. If the AllyCode is not specified, the unit will be searched in the user's guild. If specified, it will be searched in the specified AllyCode guild. You can sort the result using an extra param (column title)\n\n";
      $ret .= "<b>Syntax:</b> \n";
      $ret .= "  /search2 +<i>unit_name</i> +<i>sort</i> +<i>AllyCode</i> (optional)\n";
      $ret .= "  Format for AllyCode: xxxxxxxxx or xxx-xxx-xxx\n\n";
      $ret .= "<b>Example:</b> \n";
      $ret .= "  /search2 +lobot\n";
      $ret .= "  /search2 +lobot +123456789 \n";
      $ret .= "  /search2 +lobot +gear \n";
      $ret .= "  /search2 +lobot +gear +123456789 \n\n";
      $ret .= "<b>Time required</b>\n";
      $ret .= "  Around 2 min (1h cache -> 30sec)\n\n";
  }  
  return $ret;
}

/**************************************************************************
  funció que retorna l'ajuda del comando /search
**************************************************************************/
function getSearchHelp($lang) {
  switch ($lang) {
    case "SPA_XM":
      $ret = "<b>COMANDO: <i>search</i></b> (by cadetill & Liener)\n";
      $ret .= "\n";
      $ret .= "<b>Definición:</b> \n";
      $ret .= "  Busca una unidad en los miembros del gremio. Si no se especifica un AllyCode, se buscará la estadística en el gremio del usuario. Si se especifica, se buscará en el gremio del AllyCode especificado.\n\n";
      $ret .= "<b>Sintaxis:</b> \n";
      $ret .= "  /search +<i>unit_name</i> +<i>AllyCode</i> (opcional)\n";
      $ret .= "  Formato para el AllyCode: xxxxxxxxx o xxx-xxx-xxx\n\n";
      $ret .= "<b>Ejemplos:</b> \n";
      $ret .= "  /search +lobot\n";
      $ret .= "  /search +lobot +123456789\n\n";
      $ret .= "<b>Tiempo requerido</b>\n";
      $ret .= "  Unos 2 min (1h caché -> 30seg)\n\n";
      break;
    default:
      $ret = "<b>COMMAND: <i>search</i></b> (by cadetill & Liener)\n";
      $ret .= "\n";
      $ret .= "<b>Definition:</b> \n";
      $ret .= "  Search an unit into the guild members. If the AllyCode is not specified, the unit will be searched in the user's guild. If specified, it will be searched in the specified AllyCode guild.\n\n";
      $ret .= "<b>Syntax:</b> \n";
      $ret .= "  /search +<i>unit_name</i> +<i>AllyCode</i> (optional)\n";
      $ret .= "  Format for AllyCode: xxxxxxxxx or xxx-xxx-xxx\n\n";
      $ret .= "<b>Example:</b> \n";
      $ret .= "  /search +lobot\n";
      $ret .= "  /search +lobot +123456789\n\n";
      $ret .= "<b>Time required</b>\n";
      $ret .= "  Around 2 min (1h cache -> 30sec)\n\n";
  }  
  return $ret;
}

/**************************************************************************
  funció que retorna l'ajuda del comando /rank
**************************************************************************/
function getRankHelp($lang) {
  switch ($lang) {
    case "SPA_XM":
      $ret = "<b>COMANDO: <i>rank</i></b> (by cadetill & Liener)\n";
      $ret .= "\n";
      $ret .= "<b>Definición:</b> \n";
      $ret .= "  Muestra la unidad o el jugador ordenado por la estadística especificada. Si no se especifica un AllyCode, se buscará la estadística en el gremio del usuario. Si se especifica, se buscará en el gremio del AllyCode especificado.\n\n";
      $ret .= "<b>Sintaxis:</b> \n";
      $ret .= "  /rank <i>Stat</i> +<i>unit</i> +<i>AllyCode</i> (optional)\n";
      $ret .= "  Formato para el AllyCode: xxxxxxxxx o xxx-xxx-xxx\n\n";
      $ret .= "<b>Stat:</b> \n";
      $ret .= "   <b>Dependiente de unidad:</b> \n";
      $ret .= "     - speed\n";
      $ret .= "     - hp (salud+protección)\n";
      $ret .= "     - health\n";
      $ret .= "     - protection\n";
      $ret .= "     - physical\n";
      $ret .= "     - special\n";
      $ret .= "     - potency\n";
      $ret .= "     - tenacity\n";
      $ret .= "     - armor\n";
      $ret .= "     - phcrav (Evasión Crítico Físico)\n";
      $ret .= "     - spcrav (Evasión de Crítico Especial)\n";
      $ret .= "     - gp\n";
      $ret .= "\n";
      $ret .= "   <b>Dependiente de jugador:</b> \n";
      $ret .= "     - weighing (relación g13 vs g12+g11)\n";
      $ret .= "     - g13\n";
      $ret .= "     - mods6\n";
      $ret .= "     - mods10 (mods vel +10)\n";
      $ret .= "     - relics\n";
      $ret .= "\n";
      $ret .= "<b>Ejemplos:</b> \n";
      $ret .= "  /rank speed +lobot\n";
      $ret .= "  /rank speed +lobot +123456789\n";
      $ret .= "  /rank mods6 \n";
      $ret .= "  /rank mods6 +123456789\n";
      $ret .= "\n";
      $ret .= "<b>Tiempo requerido</b>\n";
      $ret .= "  Unos 2 min (1h cache -> 30sec)\n\n";
      break;
    default:
      $ret = "<b>COMMAND: <i>rank</i></b> (by cadetill & Liener)\n";
      $ret .= "\n";
      $ret .= "<b>Definition:</b> \n";
      $ret .= "  Shows the unit or player ordered by the specified stat. If the AllyCode is not specified, the stat will be searched in the user's guild. If specified, it will be searched in the specified AllyCode guild.\n\n";
      $ret .= "<b>Syntax:</b> \n";
      $ret .= "  /rank <i>Stat</i> +<i>unit</i> +<i>AllyCode</i> (optional)\n";
      $ret .= "  Format for AllyCode: xxxxxxxxx or xxx-xxx-xxx\n\n";
      $ret .= "<b>Stat:</b> \n";
      $ret .= "   <b>Unit Dependent:</b> \n";
      $ret .= "     - speed\n";
      $ret .= "     - hp (health+protection)\n";
      $ret .= "     - health\n";
      $ret .= "     - protection\n";
      $ret .= "     - physical\n";
      $ret .= "     - special\n";
      $ret .= "     - potency\n";
      $ret .= "     - tenacity\n";
      $ret .= "     - armor\n";
      $ret .= "     - phcrav (Physical Critical Avoidance)\n";
      $ret .= "     - spcrav (Special Critical Avoidance)\n";
      $ret .= "     - gp\n";
      $ret .= "\n";
      $ret .= "   <b>Player Dependent:</b> \n";
      $ret .= "     - weighing (relation g13 vs g12+g11)\n";
      $ret .= "     - g13\n";
      $ret .= "     - mods6\n";
      $ret .= "     - mods10 (mods vel +10)\n";
      $ret .= "     - relics\n";
      $ret .= "\n";
      $ret .= "<b>Example:</b> \n";
      $ret .= "  /rank speed +lobot\n";
      $ret .= "  /rank speed +lobot +123456789\n";
      $ret .= "  /rank mods6 \n";
      $ret .= "  /rank mods6 +123456789\n";
      $ret .= "\n";
      $ret .= "<b>Time required</b>\n";
      $ret .= "  Around 2 min (1h cache -> 30sec)\n\n";
  }  
  return $ret;
}

/**************************************************************************
  funció que retorna l'ajuda del comando /im
**************************************************************************/
function getIMHelp($lang, $plus) {
  switch ($lang) {
    case "SPA_XM":
      $ret = "<b>COMANDO: <i>im</i></b> (by cadetill & Liener)\n";
      $ret .= "\n";
      $ret .= "<b>Definición:</b> \n";
      $ret .= "  Comando para gestionar los gremios de IM.\n\n";
      $ret .= "<b>Sintaxis:</b> \n";
      if ($plus) {
        $ret .= "  /im <i>SubComando</i> +<i>lider_AllyCode</i> +<i>acrónimo</i> +<i>url</i> +<i>rama</i> \n\n";
      }
      else {
        $ret .= "  /im <i>SubComando</i> \n\n";
      }
      $ret .= "<b>SubComando:</b> \n";
      $ret .= "<b>   - list</b>: muestra los gremios del grupo Imperio Mandaloriano. \n";
      if ($plus) {
        $ret .= "<b>   - add</b>: añade o modifica una gremio de la estructura del Imperio Mandaloriano. \n";
        $ret .= "<b>   - del</b>: borra un gremio de la estructura del Imperio Mandaloriano. \n";
      }
      $ret .= "\n";
      $ret .= "<b>Ejemplos:</b> \n";
      $ret .= "<i>    Mostrar todos los gremios:</i> \n";
      $ret .= "        /im list \n";
      if ($plus) {
        $ret .= "<i>    Añadir o modificar un gremio:</i> \n";
        $ret .= "        /im add +alias +123456789 +łM D +https://swgoh.gg/g/000000/name_guild/ +1 \n";
        $ret .= "<i>    Borrar un gremio:</i> \n";
        $ret .= "        /im del +łM D \n\n";
      }
      $ret .= "\n";
      $ret .= "<b>Tiempo estimado</b>\n";
      $ret .= "  1min \n\n";
      break;
	  
    default: // ENG_US
      $ret = "<b>COMMAND: <i>im</i></b> (by cadetill & Liener)\n";
      $ret .= "\n";
      $ret .= "<b>Definition:</b> \n";
      $ret .= "  Command to manage IM guilds.\n\n";
      $ret .= "<b>Syntax:</b> \n";
      if ($plus) {
        $ret .= "  /im <i>SubCommand</i> +<i>leader_AllyCode</i> +<i>acronym</i> +<i>url</i> +<i>branch</i> \n\n";
      }
      else {
        $ret .= "  /im <i>SubCommand</i> \n\n";
      }
      $ret .= "<b>SubCommand:</b> \n";
      $ret .= "<b>   - list</b>: Shows all guilds from Imperio Mandaloriano group. \n";
      if ($plus) {
        $ret .= "<b>   - add</b>: Add or modify a new guild from the structure of Imperio Mandaloriano. \n";
        $ret .= "<b>   - del</b>: Delete a guild from the structure of Imperio Mandaloriano. \n";
      }
      $ret .= "\n";
      $ret .= "<b>Example:</b> \n";
      $ret .= "<i>    Shows all guilds:</i> \n";
      $ret .= "        /im list \n";
      if ($plus) {
        $ret .= "<i>    Add or modify a guild:</i> \n";
        $ret .= "        /im add +alias +123456789 +łM D +https://swgoh.gg/g/000000/name_guild/ +1 \n";
        $ret .= "<i>    Delete a guild:</i> \n";
        $ret .= "        /im del +łM D \n";
      }
      $ret .= "\n";
      $ret .= "<b>Tiempo requerido</b>\n";
      $ret .= "  Un 1min \n\n";
      break;  
  }
  return $ret;
}

/**************************************************************************
  funció que retorna l'ajuda del comando /guild
**************************************************************************/
function getGuildHelp($lang) {
  switch ($lang) {
    case "SPA_XM":
      $ret = "<b>COMANDO: <i>guild</i></b> (by cadetill & Liener)\n";
      $ret .= "\n";
      $ret .= "<b>Definición:</b> \n";
      $ret .= "  Comando para obtener información de un gremio.\n\n";
      $ret .= "<b>Sintaxis:</b> \n";
      $ret .= "  /guild <i>SubComando</i> +<i>ordenación</i> +<i>AllyCode</i>\n\n";
      $ret .= "  Formato para el AllyCode: xxxxxxxxx o xxx-xxx-xxx\n\n";
      $ret .= "<b>SubComando:</b> \n";
      $ret .= "<b>   - info</b>: información del gremio \n";
      $ret .= "<b>   - gp</b>: muestra el PG de todos los miembros del gremio. Puede ordenar el resultado usando un parámetro adicional (título de columna) \n";
      $ret .= "<b>   - ships</b>: muestra el PG de naves de todos los miembros del gremio. Puede ordenar el resultado usando un parámetro adicional (título de columna) \n";
      $ret .= "<b>   - chars</b>: muestra el PG de personajes de todos los miembros del gremio. Puede ordenar el resultado usando un parámetro adicional (título de columna) \n";
      $ret .= "<b>   - roster</b>: muestra el número de g13, g12 y g11 de todos los miembros del gremio. Puede ordenar el resultado usando un parámetro adicional (título de columna) \n";
      $ret .= "<b>   - top80</b>: muestra el PG del top80 de todos los miembros del gremio. Puede ordenar el resultado usando un parámetro adicional (título de columna) \n";
      $ret .= "<b>   - mods</b>: muestra los mods de los miembros del gremio. Puede ordenar el resultado usando un parámetro adicional (título de columna) \n";
      $ret .= "<b>   - registered</b>: muestra los miembros NO registrados y registrados en el bot \n\n";
//      $ret .= "<b>   - check</b>: comprueba los equipos definidos. \n\n";
      $ret .= "<b>Ejemplos:</b> \n";
      $ret .= "<i>    Información del Gremio:</i> \n";
      $ret .= "        /guild info \n";
      $ret .= "        /guild info +123456789 \n";
      $ret .= "<i>    PG de los Miembros:</i> \n";
      $ret .= "        /guild gp \n";
      $ret .= "        /guild gp +123456789 \n";
      $ret .= "        /guild gp +gp \n";
      $ret .= "        /guild gp +gp +123456789 \n";
      $ret .= "<i>    Roster de los Miembros:</i> \n";
      $ret .= "        /guild roster \n";
      $ret .= "        /guild roster +123456789 \n";
      $ret .= "        /guild roster +g13 \n";
      $ret .= "        /guild roster +avg +123456789 \n";
      $ret .= "<i>    PG Top80:</i> \n";
      $ret .= "        /guild top80 \n";
      $ret .= "        /guild top80 +123456789 \n";
      $ret .= "        /guild top80 +gp \n";
      $ret .= "        /guild top80 +gp +123456789 \n";
      $ret .= "<i>    Mods:</i> \n";
      $ret .= "        /guild mods \n";
      $ret .= "        /guild mods +123456789 \n";
      $ret .= "        /guild mods +m25 \n";
      $ret .= "        /guild mods +m25 +123456789 \n";
      $ret .= "<i>    Miembros registrados:</i> \n";
      $ret .= "        /guild registered \n";
      $ret .= "        /guild registered +123456789 \n";
//      $ret .= "<i>    Comprobar equipos definidos:</i> \n";
//      $ret .= "        /guild check <- propio gremio \n";
//      $ret .= "        /guild check +123456789  <- otro gremio \n";
//      $ret .= "        /guild check +501 \n";
//      $ret .= "        /guild check +501 +123456789 \n\n";
      $ret .= "\n";
      $ret .= "<b>Tiempo requerido</b>\n";
      $ret .= "  Unos 2 min\n\n";
      break;
    default:
      $ret = "<b>COMMAND: <i>guild</i></b> (by cadetill & Liener)\n";
      $ret .= "\n";
      $ret .= "<b>Definition:</b> \n";
      $ret .= "  Command to get guild info.\n\n";
      $ret .= "<b>Syntax:</b> \n";
      $ret .= "  /guild <i>SubCommand</i> +<i>order</i> +<i>AllyCode</i>\n\n";
      $ret .= "  Format for AllyCode: xxxxxxxxx or xxx-xxx-xxx\n\n";
      $ret .= "<b>SubCommand:</b> \n";
      $ret .= "<b>   - info</b>: guild info \n";
      $ret .= "<b>   - gp</b>: shows GP of all ally members. You can sort the result using an extra param (column title) \n";
      $ret .= "<b>   - ships</b>: shows ships GP of all ally members. You can sort the result using an extra param (column title) \n";
      $ret .= "<b>   - chars</b>: shows character GP of all ally members. You can sort the result using an extra param (column title) \n";
      $ret .= "<b>   - roster</b>: shows g13, g12 and g11 of all allymates. You can sort the result using an extra param (column title) \n";
      $ret .= "<b>   - top80</b>: shows top80 GP for all ally members. You can sort the result using an extra param (column title) \n";
      $ret .= "<b>   - mods</b>: shows mods of all allymates. You can sort the result using an extra param (column title) \n";
      $ret .= "<b>   - registered</b>: shows members NOT registered and registered into the bot  \n\n";
//      $ret .= "<b>   - check</b>: check teams defined \n\n";
      $ret .= "<b>Example:</b> \n";
      $ret .= "<i>    Guild info:</i> \n";
      $ret .= "        /guild info \n";
      $ret .= "        /guild info +123456789 \n";
      $ret .= "<i>    Members GP:</i> \n";
      $ret .= "        /guild gp \n";
      $ret .= "        /guild gp +123456789 \n";
      $ret .= "        /guild gp +gp \n";
      $ret .= "        /guild gp +gp +123456789 \n";
      $ret .= "<i>    Members Roster:</i> \n";
      $ret .= "        /guild roster \n";
      $ret .= "        /guild roster +123456789 \n";
      $ret .= "        /guild roster +g13 \n";
      $ret .= "        /guild roster +avg +123456789 \n";
      $ret .= "<i>    Top80 GP:</i> \n";
      $ret .= "        /guild top80 \n";
      $ret .= "        /guild top80 +123456789 \n";
      $ret .= "        /guild top80 +gp \n";
      $ret .= "        /guild top80 +gp +123456789 \n";
      $ret .= "<i>    Mods:</i> \n";
      $ret .= "        /guild mods \n";
      $ret .= "        /guild mods +123456789 \n";
      $ret .= "        /guild mods +m25 \n";
      $ret .= "        /guild mods +m25 +123456789 \n";
      $ret .= "<i>    Miembros registrados:</i> \n";
      $ret .= "        /guild registered \n";
      $ret .= "        /guild registered +123456789 \n";
//      $ret .= "<i>    Comprobar equipos definidos:</i> \n";
//      $ret .= "        /guild check <- propio gremio \n";
//      $ret .= "        /guild check +123456789  <- otro gremio \n";
//      $ret .= "        /guild check +501 \n";
//      $ret .= "        /guild check +501 +123456789 \n\n";
      $ret .= "\n";
      $ret .= "<b>Time required</b>\n";
      $ret .= "  Around 2 min\n\n";
  }  
  return $ret;
}

/**************************************************************************
  funció que retorna l'ajuda del comando /ga
**************************************************************************/
function getGAHelp($lang) {
  switch ($lang) {
    case "SPA_XM":
      $ret = "<b>COMANDO: <i>ga</i></b> (by cadetill & Liener)\n";
      $ret .= "\n";
      $ret .= "<b>Definición:</b> \n";
      $ret .= "  Compara una cuenta con otra. Si sólo se especifica un AllyCode, el AllyCode del usuario se usara como segundo AllyCode.\n\n";
      $ret .= "<b>Sintaxis:</b> \n";
      $ret .= "  /ga <i>AllyCode1</i> <i>AllyCode2</i> (opcional)\n";
      $ret .= "  Formato para el AllyCode: xxxxxxxxx o xxx-xxx-xxx\n\n";
      $ret .= "<b>Ejemplos:</b> \n";
      $ret .= "  /ga 123456789\n";
      $ret .= "  /ga 123456789 987654321\n\n";
      $ret .= "<b>Tiempo requerido</b>\n";
      $ret .= "  Unos 10 seg\n\n";
      break;
    default:
      $ret = "<b>COMMAND: <i>ga</i></b> (by cadetill & Liener)\n";
      $ret .= "\n";
      $ret .= "<b>Definition:</b> \n";
      $ret .= "  Compare an account with another. If only one AllyCode is specified, the user's one will be used as the second AllyCode.\n\n";
      $ret .= "<b>Syntax:</b> \n";
      $ret .= "  /ga <i>AllyCode1</i> <i>AllyCode2</i> (optional)\n";
      $ret .= "  Format for AllyCode: xxxxxxxxx or xxx-xxx-xxx\n\n";
      $ret .= "<b>Example:</b> \n";
      $ret .= "  /ga 123456789\n";
      $ret .= "  /ga 123456789 987654321\n\n";
      $ret .= "<b>Time required</b>\n";
      $ret .= "  Around 10 sec\n\n";
  }  
  return $ret;
}

/**************************************************************************
  funció que retorna l'ajuda del comando /compareg
**************************************************************************/
function getComparegHelp($lang) {
  switch ($lang) {
    case "SPA_XM":
      $ret = "<b>COMANDO: <i>compareg</i></b> (by cadetill & Liener)\n";
      $ret .= "\n";
      $ret .= "<b>Definición:</b> \n";
      $ret .= "  Compara dos gremios. Si sólo se especifica un AllyCode, el AllyCode del usuario se usara como segundo AllyCode.\n\n";
      $ret .= "<b>Sintaxis:</b> \n";
      $ret .= "  /compareg <i>AllyCode1</i> <i>AllyCode2</i> (opcional)\n";
      $ret .= "  Formato para el AllyCode: xxxxxxxxx o xxx-xxx-xxx\n\n";
      $ret .= "<b>Ejemplos:</b> \n";
      $ret .= "  /compareg 123456789\n";
      $ret .= "  /compareg 123456789 987654321\n\n";
      $ret .= "<b>Tiempo requerido</b>\n";
      $ret .= "  Unos 6 min\n\n";
      break;
    default:
      $ret = "<b>COMMAND: <i>compareg</i></b> (by cadetill & Liener)\n";
      $ret .= "\n";
      $ret .= "<b>Definition:</b> \n";
      $ret .= "  Compare two guilds. If only one AllyCode is specified, the user's one will be used as the second AllyCode.\n\n";
      $ret .= "<b>Syntax:</b> \n";
      $ret .= "  /compareg <i>AllyCode1</i> <i>AllyCode2</i> (optional)\n";
      $ret .= "  Format for AllyCode: xxxxxxxxx or xxx-xxx-xxx\n\n";
      $ret .= "<b>Example:</b> \n";
      $ret .= "  /compareg 123456789\n";
      $ret .= "  /compareg 123456789 987654321\n\n";
      $ret .= "<b>Time required</b>\n";
      $ret .= "  Around 6 min\n\n";
  }  
  return $ret;
}

/**************************************************************************
  funció que retorna l'ajuda del comando /champions
**************************************************************************/
function getChampionsHelp($lang) {
  switch ($lang) {
    case "SPA_XM":
      $ret = "<b>COMANDO: <i>champions</i></b> (by cadetill & Liener)\n";
      $ret .= "\n";
      $ret .= "<b>Definición:</b> \n";
      $ret .= "  Compara dos AllyCodes para una Champions interna.\n\n";
      $ret .= "<b>Sintaxis:</b> \n";
      $ret .= "  /champions <i>AllyCode1</i> <i>AllyCode2</i>\n";
      $ret .= "  Formato para el AllyCode: xxxxxxxxx o xxx-xxx-xxx\n\n";
      $ret .= "<b>Ejemplo:</b> \n";
      $ret .= "  /champions 123456789 987654321\n\n";
      $ret .= "<b>Tiempo requerido</b>\n";
      $ret .= "  Unos 10 seg.\n\n";
       break;
   default:
      $ret = "<b>COMMAND: <i>champions</i></b> (by cadetill & Liener)\n";
      $ret .= "\n";
      $ret .= "<b>Definition:</b> \n";
      $ret .= "  Compare two AllyCodes for a internal Champions.\n\n";
      $ret .= "<b>Syntax:</b> \n";
      $ret .= "  /champions <i>AllyCode1</i> <i>AllyCode2</i>\n";
      $ret .= "  Format for AllyCode: xxxxxxxxx or xxx-xxx-xxx\n\n";
      $ret .= "<b>Example:</b> \n";
      $ret .= "  /champions 123456789 987654321\n\n";
      $ret .= "<b>Time required</b>\n";
      $ret .= "  Around 10 sec\n\n";
  }  
  return $ret;
}

/**************************************************************************
  funció que retorna l'ajuda del comando /alias
**************************************************************************/
function getAliasHelp($lang, $plus) {
  switch ($lang) {
    case "SPA_XM":
      $ret = "<b>COMANDO: <i>alias</i></b> (by cadetill & Liener)\n";
      $ret .= "\n";
      $ret .= "<b>Definición:</b> \n";
      $ret .= "  Comando para la gestión de alias.\n\n";
      $ret .= "<b>Sintaxis:</b> \n";
      if ($plus)
        $ret .= "  /alias <i>subcomando</i> +<i>unidad</i> +<i>alias</i>\n\n";
      else
        $ret .= "  /alias <i>subcomando</i> \n\n";
      $ret .= "<b>Subcomandos:</b> \n";
      if ($plus) {
        $ret .= "<b>   - add</b>: añade un nuevo alias para la unidad especificada. \n";
        $ret .= "<b>   - del</b>: borra un alias existente de la lista de alias. \n";
      }
      $ret .= "<b>   - list</b>: lista los alias definidos. \n\n";
      $ret .= "<b>Ejemplos:</b> \n";
      if ($plus) {
        $ret .= "<i>    Añadir nuevo alias</i> \n";
        $ret .= "        /alias add +General Skywalker +gs \n";
        $ret .= "<i>    Borrar alias</i> \n";
        $ret .= "        /alias del +gs \n";
      }
      $ret .= "<i>    Lista de alias definidos</i> \n";
      $ret .= "        /alias list \n\n";
      break;
	  
    default: // ENG_US
      $ret = "<b>COMMAND: <i>alias</i></b> (by cadetill & Liener)\n";
      $ret .= "\n";
      $ret .= "<b>Definition:</b> \n";
      $ret .= "  Commands for alias management.\n\n";
      $ret .= "<b>Syntax:</b> \n";
      if ($plus)
        $ret .= "  /alias <i>subcommand</i> +<i>unit</i> +<i>alias</i>\n\n";
      else
        $ret .= "  /alias <i>subcommand</i> \n\n";
      $ret .= "<b>Subcommands:</b> \n";
      if ($plus) {
        $ret .= "<b>   - add</b>: add a new alias for the specified unit. \n";
        $ret .= "<b>   - del</b>: delete an existing alias from the alias list. \n";
      }
      $ret .= "<b>   - list</b>: list all aliases defined. \n\n";
      $ret .= "<b>Examples:</b> \n";
      if ($plus) {
        $ret .= "<i>    Add a new alias</i> \n";
        $ret .= "        /alias add +General Skywalker +gs \n";
        $ret .= "<i>    Delete an alias</i> \n";
        $ret .= "        /alias del +gs \n";
      }
      $ret .= "<i>    List all aliases defined</i> \n";
      $ret .= "        /alias list \n\n";
      break;  
  }
  return $ret;
}

/**************************************************************************
  funció que retorna l'ajuda del comando /units
**************************************************************************/
function getUnitsHelp($lang, $plus) {
  switch ($lang) {
    case "SPA_XM":
      $ret  = "<b>COMANDO: <i>units</i></b> (by cadetill & Liener)\n\n";
      $ret .= "<b>Definición:</b> \n";
      $ret .= "  Comando para la gestión de las unidades.\n\n";
      $ret .= "<b>Sintaxis:</b> \n";
      $ret .= "  /units <i>subcomando</i> \n\n";
      $ret .= "<b>Subcomandos:</b> \n";
      if ($plus) {
        $ret .= "<b>   - update</b>: actualiza la lista de unidades. \n";
      }
      $ret .= "<b>   - list</b>: lista las unidades. \n";
      if ($plus) {
        $ret .= "<b>   - add</b>: añade una unidad a la lista de control. \n";
        $ret .= "<b>   - del</b>: borra una unidad de la lista de control. \n";
        $ret .= "<b>   - addc</b>: añade un comando para las unidades. \n";
        $ret .= "<b>   - delc</b>: borra un comando de las unidades. \n";
      }
      $ret .= "<b>   - listc</b>: lista las unidades a controlar. \n\n";
      $ret .= "<b>Ejemplos:</b> \n";
      if ($plus) {
        $ret .= "<i>    Actualizar lista de unidades</i> \n";
        $ret .= "        /units update \n";
      }
      $ret .= "<i>    Listar las unidades </i> \n";
      $ret .= "        /units list \n";
      if ($plus) {
        $ret .= "<i>    Añadir unidad a controlar</i> \n";
        $ret .= "        /units add +gas \n";
        $ret .= "<i>    Borrar unidad del control</i> \n";
        $ret .= "        /units del +gas \n";
        $ret .= "<i>    Añadir comando</i> \n";
        $ret .= "        /units addc +compareg \n";
        $ret .= "<i>    Borrar comando</i> \n";
        $ret .= "        /units delc +compareg \n";
       }
      $ret .= "<i>    Unidades a controlar </i> \n";
      $ret .= "        /units listc \n\n";
      break;
	  
    default: // ENG_US
      $ret  = "<b>COMMAND: <i>units</i></b> (by cadetill & Liener)\n\n";
      $ret .= "<b>Definition:</b> \n";
      $ret .= "  Commands for units management.\n\n";
      $ret .= "<b>Syntax:</b> \n";
      $ret .= "  /units <i>subcommand</i> \n\n";
      $ret .= "<b>Subcommands:</b> \n";
      if ($plus) {
        $ret .= "<b>   - update</b>: update units list. \n";
      }
      $ret .= "<b>   - list</b>: list all units. \n";
      if ($plus) {
        $ret .= "<b>   - add</b>: add a unit to the control list. \n";
        $ret .= "<b>   - del</b>: del a unit from the control list. \n";
        $ret .= "<b>   - addc</b>: add a command for units. \n";
        $ret .= "<b>   - delc</b>: del command from units. \n";
      }
      $ret .= "<b>   - listc</b>: list units to control. \n\n";
      $ret .= "<b>Examples:</b> \n";
      if ($plus) {
        $ret .= "<i>    Update list</i> \n";
        $ret .= "        /units update \n";
      }
      $ret .= "<i>    Units list</i> \n";
      $ret .= "        /units list \n";
      if ($plus) {
        $ret .= "<i>    Add unit to control</i> \n";
        $ret .= "        /units add +gas \n";
        $ret .= "<i>    Del unit from control</i> \n";
        $ret .= "        /units del +gas \n";
        $ret .= "<i>    Add command</i> \n";
        $ret .= "        /units addc +compareg \n";
        $ret .= "<i>    Del command</i> \n";
        $ret .= "        /units delc +compareg \n";
      }
      $ret .= "<i>    Units ton control</i> \n";
      $ret .= "        /units listc \n\n";
      break;  
  }
  return $ret;
}

// ajuda pels administradors
function adminHelp() {
  $ret = "<b>ADMIN COMMANDS</b> (by cadetill & Liener)\n";
  $ret .= "\n";
  $ret .= "<b>----------UNITS TO CHECK COMMANDS----------</b>\n";
  $ret .= "<b>/help +unitToCheckAdd</b>\n";
  $ret .= "  <b>Definition:</b> \n";
  $ret .= "    Adds a new unit to the unit list to check in some commands such /ga or /compareg. You can use an alias\n";
  $ret .= "  <b>Syntax:</b> \n";
  $ret .= "    /help +<i>unitToCheckAdd</i> +<i>unit</i>\n";
  $ret .= "  <b>Example:</b> \n";
  $ret .= "    /help +unitToCheckAdd +General Skywalker\n\n";
  $ret .= "\n";
  $ret .= "<b>/help +unitToCheckList</b>\n";
  $ret .= "  <b>Definition:</b> \n";
  $ret .= "    Displays the list of units to check.\n";
  $ret .= "  <b>Syntax:</b> \n";
  $ret .= "    /help +<i>unitToCheckList</i> +<i>list</i>\n";
  $ret .= "  <b>Example:</b> \n";
  $ret .= "    /help +unitToCheckList +list\n\n";
  $ret .= "\n";
  $ret .= "<b>/help +unitToCheckDel</b>\n";
  $ret .= "  <b>Definition:</b> \n";
  $ret .= "    delete an unit from the unit list to check. You can use alias\n";
  $ret .= "  <b>Syntax:</b> \n";
  $ret .= "    /help +<i>unitToCheckDel</i> +<i>unit</i>\n";
  $ret .= "  <b>Example:</b> \n";
  $ret .= "    /help +unitToCheckDel +General Skywalker\n\n";
  $ret .= "\n";
  
  return $ret;
}
