<?php
    require_once 'SwgohHelp.php';
    set_time_limit(600);
    header('Content-Type: application/json');

    $swgoh = new SwgohHelp(array("cadetill","MNtEswU34"));
    $jsonGuild = $swgoh->fetchGuild( '471192528', 'SPA_XM' );
    
    $arrGuild = json_decode($jsonGuild, true);
    $players = '';
    $cont = 0;
    //$arrPlayers = array();
    foreach ($arrGuild[0]['roster'] as $player) {
      if ($players != "") {
        $players .= ',';
      }
      $players .= $player['allyCode'];
      $cont++;
      if ($cont == 20) {
        $jsonPlayers = $swgoh->fetchPlayer( $players, 'SPA_XM' );
        $arrPlayers = array_merge($arrPlayers, json_encode($jsonPlayers));
        $cont = 0;
        $players = '';
      } 
    }

    
    $jsonPlayers = $swgoh->fetchPlayer( $players, 'SPA_XM' );
    $arrPlayers = array_merge($arrPlayers, json_encode($jsonPlayers));
    echo json_decode($arrPlayers);