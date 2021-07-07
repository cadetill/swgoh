<?php
class TChampions extends TGA {
  
  /****************************************************
    constructor de la classe. Inicialitza variables
  ****************************************************/
  public function __construct($params, $dataObj) {
    parent::__construct($params, $dataObj);
    $this->error = '';
    
    if (count($params) == 3) {
      if (!$this->checkAllyCode($params[1]))
        $this->error = $this->translatedText("error3", $params[1]); // "The %s isn't a correct AllyCode.\n";
      if (!$this->checkAllyCode($params[2]))
        $this->error = $this->translatedText("error3", $params[2]); // "The %s isn't a correct AllyCode.\n";
      
      $this->allyCode = $params[1] . ',' . $params[2];
    } 
    else
      $this->error = $this->translatedText("error1"); // Bad request. See help: \n\n
  }
    
  /****************************************************
    FUNCIONS PUBLIQUES
  ****************************************************/
  /****************************************************
    executa el subcomando
  ****************************************************/
  public function execCommand() {
    if ($this->error != "")
      return $this->getHelp("champions", $this->error);
      
    $initialTime = microtime(true);
    
    $res = $this->processChampions();

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
  private function processChampions() {
    $player = array();
    $data0 = array();
    $data1 = array();
    $rank0 = '';
    $league0 = '';
    $rank1 = '';
    $league1 = '';
    $ret = $this->getVariables($player, $data0, $data1, $rank0, $league0, $rank1, $league1);
    
    if ($ret == -1)
      return $this->translatedText("error6");                                   // "Ooooops! API server may have shut down. Try again later.\n\n"

    // imprimim dades de GA
    $res = array(0 => $this->printGA($player, $data0, $data1, $rank0, $league0, $rank1, $league1));

    // imprimim dades de Champions
    $sum0 = 0;
    $sum1 = 0;
    if ($player[0]["stats"][0]["value"] == $player[1]["stats"][0]["value"]) {
      $res[1] = $this->translatedText("txtCham44");                                                                      // "Mayor Pg: 2 puntos ... <b>empate</b>\n\n";
    } elseif ($player[0]["stats"][0]["value"] > $player[1]["stats"][0]["value"]) {
      $res[1] = $this->translatedText("txtCham45", $player[0]["name"]);                                                  // "Mayor Pg: 2 puntos ... ".$player[0]["name"]."\n\n";
      $sum0 = $sum0 + 2;
    } else {
      $res[1] = $this->translatedText("txtCham45", $player[1]["name"]);                                                  // "Mayor Pg: 2 puntos ... ".$player[1]["name"]."\n\n";
      $sum1 = $sum1 + 2;
    }
  
    if ($player[0]["stats"][1]["value"] == $player[1]["stats"][1]["value"]) {
      if ($player[0]["stats"][0]["value"] == $player[1]["stats"][0]["value"]) {
        $res[1] .= $this->translatedText("txtCham46", "");                                                               // "Mayor pg pjs: 1 punto... <b>empate</b>\n\n";
      } elseif ($player[0]["stats"][0]["value"] > $player[1]["stats"][0]["value"]) {
        $res[1] .= $this->translatedText("txtCham46", $player[1]["name"]);                                               // "Mayor pg pjs: 1 punto... <b>empate </b>".$player[1]["name"]."\n\n";
        $sum1 = $sum1 + 1;
	  } else {
        $res[1] .= $this->translatedText("txtCham46", $player[0]["name"]);                                               // "Mayor pg pjs: 1 punto... <b>empate </b>".$player[0]["name"]."\n\n";
        $sum0 = $sum0 + 1;
      }
    } elseif ($player[0]["stats"][1]["value"] > $player[1]["stats"][1]["value"]) {
      $res[1] .= $this->translatedText("txtCham47", $player[0]["name"]);                                                 // "Mayor pg pjs: 1 punto... ".$player[0]["name"]."\n\n";
      $sum0 = $sum0 + 1;
    } else {
      $res[1] .= $this->translatedText("txtCham47", $player[1]["name"]);                                                 // "Mayor pg pjs: 1 punto... ".$player[1]["name"]."\n\n";
      $sum1 = $sum1 + 1;
    }
  
    if ($player[0]["stats"][2]["value"] == $player[1]["stats"][2]["value"]) {
      if ($player[0]["stats"][0]["value"] == $player[1]["stats"][0]["value"]) {
        $res[1] .= $this->translatedText("txtCham48", "");                                                               // "Mayor pg naves: 1 punto... <b>empate</b>\n\n";
      } elseif ($player[0]["stats"][0]["value"] > $player[1]["stats"][0]["value"]) {
        $res[1] .= $this->translatedText("txtCham48", $player[1]["name"]);                                               // "Mayor pg naves: 1 punto... <b>empate </b>".$player[1]["name"]."\n\n";
        $sum1 = $sum1 + 1;
	  } else {
        $res[1] .= $this->translatedText("txtCham48", $player[0]["name"]);                                               // "Mayor pg naves: 1 punto... <b>empate </b>".$player[0]["name"]."\n\n";
        $sum0 = $sum0 + 1;
      }
    } elseif ($player[0]["stats"][2]["value"] > $player[1]["stats"][2]["value"]) {
      $res[1] .= $this->translatedText("txtCham49", $player[0]["name"]);                                                // "Mayor pg naves: 1 punto... ".$player[0]["name"]."\n\n";
      $sum0 = $sum0 + 1;
    } else {
      $res[1] .= $this->translatedText("txtCham49", $player[1]["name"]);                                                // "Mayor pg naves: 1 punto... ".$player[1]["name"]."\n\n";
      $sum1 = $sum1 + 1;
    }
  
    if ($player[0]["stats"][13]["value"] == $player[1]["stats"][13]["value"]) {
      if ($player[0]["stats"][0]["value"] == $player[1]["stats"][0]["value"]) {
        $res[1] .= $this->translatedText("txtCham50", "");                                                              // "Más batallas ganadas ga: 2 puntos... <b>empate</b>\n\n";
      } elseif ($player[0]["stats"][0]["value"] > $player[1]["stats"][0]["value"]) {
        $res[1] .= $this->translatedText("txtCham50", $player[1]["name"]);                                              // "Más batallas ganadas ga: 2 puntos... <b>empate </b>".$player[1]["name"]."\n\n";
        $sum1 = $sum1 + 2;
	  } else {
        $res[1] .= $this->translatedText("txtCham50", $player[0]["name"]);                                              // "Más batallas ganadas ga: 2 puntos... <b>empate </b>".$player[0]["name"]."\n\n";
        $sum0 = $sum0 + 2;
      }
    } elseif ($player[0]["stats"][13]["value"] > $player[1]["stats"][13]["value"]) {
      $res[1] .= $this->translatedText("txtCham51", $player[0]["name"]);                                                // "Más batallas ganadas ga: 2 puntos... ".$player[0]["name"]."\n\n";
      $sum0 = $sum0 + 2;
    } else {
      $res[1] .= $this->translatedText("txtCham51", $player[1]["name"]);                                                // "Más batallas ganadas ga: 2 puntos... ".$player[1]["name"]."\n\n";
      $sum1 = $sum1 + 2;
    }
  
    if ($player[0]["stats"][14]["value"] == $player[1]["stats"][14]["value"]) {
      if ($player[0]["stats"][0]["value"] == $player[1]["stats"][0]["value"]) {
        $res[1] .= $this->translatedText("txtCham52", "");                                                              // "Más defensas exitosas ga: 2 puntos... <b>empate</b>\n\n";
      } elseif ($player[0]["stats"][0]["value"] > $player[1]["stats"][0]["value"]) {
        $res[1] .= $this->translatedText("txtCham52", $player[1]["name"]);                                              // "Más defensas exitosas ga: 2 puntos... <b>empate </b>".$player[1]["name"]."\n\n";
        $sum1 = $sum1 + 2;
	  } else {
        $res[1] .= $this->translatedText("txtCham52", $player[0]["name"]);                                              // "Más defensas exitosas ga: 2 puntos... <b>empate </b>".$player[0]["name"]."\n\n";
        $sum0 = $sum0 + 2;
      }
    } elseif ($player[0]["stats"][14]["value"] > $player[1]["stats"][14]["value"]) {
      $res[1] .= $this->translatedText("txtCham53", $player[0]["name"]);                                                // "Más defensas exitosas ga: 2 puntos... ".$player[0]["name"]."\n\n";
      $sum0 = $sum0 + 2;
    } else {
      $res[1] .= $this->translatedText("txtCham53", $player[1]["name"]);                                                // "Más defensas exitosas ga: 2 puntos... ".$player[1]["name"]."\n\n";
      $sum1 = $sum1 + 2;
    }
  
    if ($player[0]["stats"][18]["value"] == $player[1]["stats"][18]["value"]) {
      if ($player[0]["stats"][0]["value"] == $player[1]["stats"][0]["value"]) {
        $res[1] .= $this->translatedText("txtCham54", "");                                                              // "Más territorios derrotados: 1 punto... <b>empate</b>\n\n";
      } elseif ($player[0]["stats"][0]["value"] > $player[1]["stats"][0]["value"]) {
        $res[1] .= $this->translatedText("txtCham54", $player[1]["name"]);                                              // "Más territorios derrotados: 1 punto... <b>empate </b>".$player[1]["name"]."\n\n";
        $sum1 = $sum1 + 1;
	  } else {
        $res[1] .= $this->translatedText("txtCham54", $player[0]["name"]);                                              // "Más territorios derrotados: 1 punto... <b>empate </b>".$player[0]["name"]."\n\n";
        $sum0 = $sum0 + 1;
      }
    } elseif ($player[0]["stats"][18]["value"] > $player[1]["stats"][18]["value"]) {
      $res[1] .= $this->translatedText("txtCham55", $player[0]["name"]);                                                // "Más territorios derrotados: 1 punto... ".$player[0]["name"]."\n\n";
      $sum0 = $sum0 + 1;
    } else {
      $res[1] .= $this->translatedText("txtCham55", $player[1]["name"]);                                                // "Más territorios derrotados: 1 punto... ".$player[1]["name"]."\n\n";
      $sum1 = $sum1 + 1;
    }
  
    if ($player[0]["stats"][15]["value"] == $player[1]["stats"][15]["value"]) {
      if ($player[0]["stats"][0]["value"] == $player[1]["stats"][0]["value"]) {
        $res[1] .= $this->translatedText("txtCham56", "");                                                              // "Más estandartes conseguidos; 1 punto... <b>empate</b>\n\n";
      } elseif ($player[0]["stats"][0]["value"] > $player[1]["stats"][0]["value"]) {
        $res[1] .= $this->translatedText("txtCham56", $player[1]["name"]);                                              // "Más estandartes conseguidos; 1 punto... <b>empate </b>".$player[1]["name"]."\n\n";
        $sum1 = $sum1 + 1;
	  } else {
        $res[1] .= $this->translatedText("txtCham56", $player[0]["name"]);                                              // "Más estandartes conseguidos; 1 punto... <b>empate </b>".$player[0]["name"]."\n\n";
        $sum0 = $sum0 + 1;
      }
    } elseif ($player[0]["stats"][15]["value"] > $player[1]["stats"][15]["value"]) {
      $res[1] .= $this->translatedText("txtCham57", $player[0]["name"]);                                                // "Más estandartes conseguidos; 1 punto... ".$player[0]["name"]."\n\n";
      $sum0 = $sum0 + 1;
    } else {
      $res[1] .= $this->translatedText("txtCham57", $player[1]["name"]);                                                // "Más estandartes conseguidos; 1 punto... ".$player[1]["name"]."\n\n";
      $sum1 = $sum1 + 1;
    }
  
    if ($player[0]["stats"][12]["value"] == $player[1]["stats"][12]["value"]) {
      if ($player[0]["stats"][0]["value"] == $player[1]["stats"][0]["value"]) {
        $res[1] .= $this->translatedText("txtCham58", "");                                                              // "Más ascensos conseguidos; 1 punto... <b>empate</b>\n\n";
      } elseif ($player[0]["stats"][0]["value"] > $player[1]["stats"][0]["value"]) {
        $res[1] .= $this->translatedText("txtCham58", $player[1]["name"]);                                              // "Más ascensos conseguidos; 1 punto... <b>empate </b>".$player[1]["name"]."\n\n";
        $sum1 = $sum1 + 1;
	  } else {
        $res[1] .= $this->translatedText("txtCham58", $player[0]["name"]);                                              // "Más ascensos conseguidos; 1 punto... <b>empate </b>".$player[0]["name"]."\n\n";
        $sum0 = $sum0 + 1;
      }
    } elseif ($player[0]["stats"][12]["value"] > $player[1]["stats"][12]["value"]) {
      $res[1] .= $this->translatedText("txtCham59", $player[0]["name"]);                                                // "Más ascensos conseguidos; 1 punto... ".$player[0]["name"]."\n\n";
      $sum0 = $sum0 + 1;
    } else {
      $res[1] .= $this->translatedText("txtCham59", $player[1]["name"]);                                                // "Más ascensos conseguidos; 1 punto... ".$player[1]["name"]."\n\n";
      $sum1 = $sum1 + 1;
    }
  
    if ($data0["7chars"] == $data1["7chars"]) {
      if ($player[0]["stats"][0]["value"] == $player[1]["stats"][0]["value"]) {
        $res[1] .= $this->translatedText("txtCham60", "");                                                              // "Más pjs 7*: 2 puntos... <b>empate</b>\n\n";
      } elseif ($player[0]["stats"][0]["value"] > $player[1]["stats"][0]["value"]) {
        $res[1] .= $this->translatedText("txtCham60", $player[1]["name"]);                                              // "Más pjs 7*: 2 puntos... <b>empate </b>".$player[1]["name"]."\n\n";
        $sum1 = $sum1 + 2;
	  } else {
        $res[1] .= $this->translatedText("txtCham60", $player[0]["name"]);                                              // "Más pjs 7*: 2 puntos... <b>empate </b>".$player[0]["name"]."\n\n";
        $sum0 = $sum0 + 2;
      }
    } elseif ($data0["7chars"] > $data1["7chars"]) {
      $res[1] .= $this->translatedText("txtCham61", $player[0]["name"]);                                                // "Más pjs 7*: 2 puntos... ".$player[0]["name"]."\n\n";
      $sum0 = $sum0 + 2;
    } else {
      $res[1] .= $this->translatedText("txtCham61", $player[1]["name"]);                                                // "Más pjs 7*: 2 puntos... ".$player[1]["name"]."\n\n";
      $sum1 = $sum1 + 2;
    }
  
    if ($data0["g13"] == $data1["g13"]) {
      if ($player[0]["stats"][0]["value"] == $player[1]["stats"][0]["value"]) {
        $res[1] .= $this->translatedText("txtCham62", "");                                                              // "Más pjs g13: 3 puntos... <b>empate</b>\n\n";
      } elseif ($player[0]["stats"][0]["value"] > $player[1]["stats"][0]["value"]) {
        $res[1] .= $this->translatedText("txtCham62", $player[1]["name"]);                                              // "Más pjs g13: 3 puntos... <b>empate </b>".$player[1]["name"]."\n\n";
        $sum1 = $sum1 + 3;
	  } else {
        $res[1] .= $this->translatedText("txtCham62", $player[0]["name"]);                                              // "Más pjs g13: 3 puntos... <b>empate </b>".$player[0]["name"]."\n\n";
        $sum0 = $sum0 + 3;
      }
    } elseif ($data0["g13"] > $data1["g13"]) {
      $res[1] .= $this->translatedText("txtCham63", $player[0]["name"]);                                                // "Más pjs g13: 3 puntos... ".$player[0]["name"]."\n\n";
      $sum0 = $sum0 + 3;
    } else {
      $res[1] .= $this->translatedText("txtCham63", $player[1]["name"]);                                                // "Más pjs g13: 3 puntos... ".$player[1]["name"]."\n\n";
      $sum1 = $sum1 + 3;
    }
  
    if ($data0["g12"] == $data1["g12"]) {
      if ($player[0]["stats"][0]["value"] == $player[1]["stats"][0]["value"]) {
        $res[1] .= $this->translatedText("txtCham64", "");                                                              // "Más pjs g12: 2 puntos... <b>empate</b>\n\n";
      } elseif ($player[0]["stats"][0]["value"] > $player[1]["stats"][0]["value"]) {
        $res[1] .= $this->translatedText("txtCham64", $player[1]["name"]);                                              // "Más pjs g12: 2 puntos... <b>empate </b>".$player[1]["name"]."\n\n";
        $sum1 = $sum1 + 2;
	  } else {
        $res[1] .= $this->translatedText("txtCham64", $player[0]["name"]);                                              // "Más pjs g12: 2 puntos... <b>empate </b>".$player[0]["name"]."\n\n";
        $sum0 = $sum0 + 2;
      }
    } elseif ($data0["g12"] > $data1["g12"]) {
      $res[1] .= $this->translatedText("txtCham65", $player[0]["name"]);                                                // "Más pjs g12: 2 puntos... ".$player[0]["name"]."\n\n";
      $sum0 = $sum0 + 2;
    } else {
      $res[1] .= $this->translatedText("txtCham65", $player[1]["name"]);                                                // "Más pjs g12: 2 puntos... ".$player[1]["name"]."\n\n";
      $sum1 = $sum1 + 2;
    }
  
    if ($data0["g11"] == $data1["g11"]) {
      if ($player[0]["stats"][0]["value"] == $player[1]["stats"][0]["value"]) {
        $res[1] .= $this->translatedText("txtCham66", "");                                                              // "Más pjs g11: 1 puntos... <b>empate</b>\n\n";
      } elseif ($player[0]["stats"][0]["value"] > $player[1]["stats"][0]["value"]) {
        $res[1] .= $this->translatedText("txtCham66", $player[1]["name"]);                                              // "Más pjs g11: 1 puntos... <b>empate </b>".$player[1]["name"]."\n\n";
        $sum1 = $sum1 + 1;
	  } else {
        $res[1] .= $this->translatedText("txtCham66", $player[0]["name"]);                                              // "Más pjs g11: 1 puntos... <b>empate </b>".$player[0]["name"]."\n\n";
        $sum0 = $sum0 + 1;
      }
    } elseif ($data0["g11"] > $data1["g11"]) {
      $res[1] .= $this->translatedText("txtCham67", $player[0]["name"]);                                                // "Más pjs g11: 1 puntos... ".$player[0]["name"]."\n\n";
      $sum0 = $sum0 + 1;
    } else {
      $res[1] .= $this->translatedText("txtCham67", $player[1]["name"]);                                                // "Más pjs g11: 1 puntos... ".$player[1]["name"]."\n\n";
      $sum1 = $sum1 + 1;
    }
  
    if ($data0["zetas"] == $data1["zetas"]) {
      if ($player[0]["stats"][0]["value"] == $player[1]["stats"][0]["value"]) {
        $res[1] .= $this->translatedText("txtCham68", "");                                                              // "Más pjs zetas: 3 puntos... <b>empate</b>\n\n";
      } elseif ($player[0]["stats"][0]["value"] > $player[1]["stats"][0]["value"]) {
        $res[1] .= $this->translatedText("txtCham68", $player[1]["name"]);                                              // "Más pjs zetas: 3 puntos... <b>empate </b>".$player[1]["name"]."\n\n";
        $sum1 = $sum1 + 3;
	  } else {
        $res[1] .= $this->translatedText("txtCham68", $player[0]["name"]);                                              // "Más pjs zetas: 3 puntos... <b>empate </b>".$player[0]["name"]."\n\n";
        $sum0 = $sum0 + 3;
      }
    } elseif ($data0["zetas"] > $data1["zetas"]) {
      $res[1] .= $this->translatedText("txtCham69", $player[0]["name"]);                                                // "Más pjs zetas: 3 puntos... ".$player[0]["name"]."\n\n";
      $sum0 = $sum0 + 3;
    } else {
      $res[1] .= $this->translatedText("txtCham69", $player[1]["name"]);                                                // "Más pjs zetas: 3 puntos... ".$player[1]["name"]."\n\n";
      $sum1 = $sum1 + 3;
    }
  
    if ($data0["relics"] == $data1["relics"]) {
      if ($player[0]["stats"][0]["value"] == $player[1]["stats"][0]["value"]) {
        $res[1] .= $this->translatedText("txtCham70", "");                                                              // "Mayor número de reliquias: 3 puntos... <b>empate</b>\n\n";
      } elseif ($player[0]["stats"][0]["value"] > $player[1]["stats"][0]["value"]) {
        $res[1] .= $this->translatedText("txtCham70", $player[1]["name"]);                                              // "Mayor número de reliquias: 3 puntos... <b>empate </b>".$player[1]["name"]."\n\n";
        $sum1 = $sum1 + 3;
	  } else {
        $res[1] .= $this->translatedText("txtCham70", $player[0]["name"]);                                              // "Mayor número de reliquias: 3 puntos... <b>empate </b>".$player[0]["name"]."\n\n";
        $sum0 = $sum0 + 3;
      }
    } elseif ($data0["relics"] > $data1["relics"]) {
      $res[1] .= $this->translatedText("txtCham71", $player[0]["name"]);                                                // "Mayor número de reliquias: 3 puntos... ".$player[0]["name"]."\n\n";
      $sum0 = $sum0 + 3;
    } else {
      $res[1] .= $this->translatedText("txtCham71", $player[1]["name"]);                                                // "Mayor número de reliquias: 3 puntos... ".$player[1]["name"]."\n\n";
      $sum1 = $sum1 + 3;
    }
  
    if ($data0["r7"] == $data1["r7"]) {
      if ($player[0]["stats"][0]["value"] == $player[1]["stats"][0]["value"]) {
        $res[1] .= $this->translatedText("txtCham72", "");                                                              // "Más r7: 3 puntos... <b>empate</b>\n\n";
      } elseif ($player[0]["stats"][0]["value"] > $player[1]["stats"][0]["value"]) {
        $res[1] .= $this->translatedText("txtCham72", $player[1]["name"]);                                              // "Más r7: 3 puntos... <b>empate </b>".$player[1]["name"]."\n\n";
        $sum1 = $sum1 + 3;
	  } else {
        $res[1] .= $this->translatedText("txtCham72", $player[0]["name"]);                                              // "Más r7: 3 puntos... <b>empate </b>".$player[0]["name"]."\n\n";
        $sum0 = $sum0 + 3;
      }
    } elseif ($data0["r7"] > $data1["r7"]) {
      $res[1] .= $this->translatedText("txtCham73", $player[0]["name"]);                                                // "Más r7: 3 puntos... ".$player[0]["name"]."\n\n";
      $sum0 = $sum0 + 3;
    } else {
      $res[1] .= $this->translatedText("txtCham73", $player[1]["name"]);                                                // "Más r7: 3 puntos... ".$player[1]["name"]."\n\n";
      $sum1 = $sum1 + 3;
    }
  
    if ($data0["mods6"] == $data1["mods6"]) {
      if ($player[0]["stats"][0]["value"] == $player[1]["stats"][0]["value"]) {
        $res[1] .= $this->translatedText("txtCham74", "");                                                              // "Más mods 6*: 2 puntos... <b>empate</b>\n\n";
      } elseif ($player[0]["stats"][0]["value"] > $player[1]["stats"][0]["value"]) {
        $res[1] .= $this->translatedText("txtCham74", $player[1]["name"]);                                              // "Más mods 6*: 2 puntos... <b>empate </b>".$player[1]["name"]."\n\n";
        $sum1 = $sum1 + 2;
	  } else {
        $res[1] .= $this->translatedText("txtCham74", $player[0]["name"]);                                              // "Más mods 6*: 2 puntos... <b>empate </b>".$player[0]["name"]."\n\n";
        $sum0 = $sum0 + 2;
      }
    } elseif ($data0["mods6"] > $data1["mods6"]) {
      $res[1] .= $this->translatedText("txtCham75", $player[0]["name"]);                                                // "Más mods 6*: 2 puntos... ".$player[0]["name"]."\n\n";
      $sum0 = $sum0 + 2;
    } else {
      $res[1] .= $this->translatedText("txtCham75", $player[1]["name"]);                                                // "Más mods 6*: 2 puntos... ".$player[1]["name"]."\n\n";
      $sum1 = $sum1 + 2;
    }
  
    if ($data0["mods10"] == $data1["mods10"]) {
      if ($player[0]["stats"][0]["value"] == $player[1]["stats"][0]["value"]) {
        $res[1] .= $this->translatedText("txtCham76", "");                                                              // "Más mods +10; 1 punto... <b>empate</b>\n\n";
      } elseif ($player[0]["stats"][0]["value"] > $player[1]["stats"][0]["value"]) {
        $res[1] .= $this->translatedText("txtCham76", $player[1]["name"]);                                              // "Más mods +10; 1 punto... <b>empate </b>".$player[1]["name"]."\n\n";
        $sum1 = $sum1 + 1;
	  } else {
        $res[1] .= $this->translatedText("txtCham76", $player[0]["name"]);                                              // "Más mods +10; 1 punto... <b>empate </b>".$player[0]["name"]."\n\n";
        $sum0 = $sum0 + 1;
      }
    } elseif ($data0["mods10"] > $data1["mods10"]) {
      $res[1] .= $this->translatedText("txtCham77", $player[0]["name"]);                                                // "Más mods +10; 1 punto... ".$player[0]["name"]."\n\n";
      $sum0 = $sum0 + 1;
    } else {
      $res[1] .= $this->translatedText("txtCham77", $player[1]["name"]);                                                // "Más mods +10; 1 punto... ".$player[1]["name"]."\n\n";
      $sum1 = $sum1 + 1;
    }
  
    if ($data0["mods15"] == $data1["mods15"]) {
      if ($player[0]["stats"][0]["value"] == $player[1]["stats"][0]["value"]) {
        $res[1] .= $this->translatedText("txtCham78", "");                                                              // "Más mods +15; 2 puntos... <b>empate</b>\n\n";
      } elseif ($player[0]["stats"][0]["value"] > $player[1]["stats"][0]["value"]) {
        $res[1] .= $this->translatedText("txtCham78", $player[1]["name"]);                                              // "Más mods +15; 2 puntos... <b>empate </b>".$player[1]["name"]."\n\n";
        $sum1 = $sum1 + 2;
	  } else {
        $res[1] .= $this->translatedText("txtCham78", $player[0]["name"]);                                              // "Más mods +15; 2 puntos... <b>empate </b>".$player[0]["name"]."\n\n";
        $sum0 = $sum0 + 2;
      }
    } elseif ($data0["mods15"] > $data1["mods15"]) {
      $res[1] .= $this->translatedText("txtCham79", $player[0]["name"]);                                                // "Más mods +15; 2 puntos... ".$player[0]["name"]."\n\n";
      $sum0 = $sum0 + 2;
    } else {
      $res[1] .= $this->translatedText("txtCham79", $player[1]["name"]);                                                // "Más mods +15; 2 puntos... ".$player[1]["name"]."\n\n";
      $sum1 = $sum1 + 2;
    }
  
    if ($data0["mods20"] == $data1["mods20"]) {
      if ($player[0]["stats"][0]["value"] == $player[1]["stats"][0]["value"]) {
        $res[1] .= $this->translatedText("txtCham80", "");                                                              // "Más mods +20; 3 puntos... <b>empate</b>\n\n";
      } elseif ($player[0]["stats"][0]["value"] > $player[1]["stats"][0]["value"]) {
        $res[1] .= $this->translatedText("txtCham80", $player[1]["name"]);                                              // "Más mods +20; 3 puntos... <b>empate </b>".$player[1]["name"]."\n\n";
        $sum1 = $sum1 + 3;
	  } else {
        $res[1] .= $this->translatedText("txtCham80", $player[0]["name"]);                                              // "Más mods +20; 3 puntos... <b>empate </b>".$player[0]["name"]."\n\n";
        $sum0 = $sum0 + 3;
      }
    } elseif ($data0["mods20"] > $data1["mods20"]) {
      $res[1] .= $this->translatedText("txtCham81", $player[0]["name"]);                                               // "Más mods +20; 3 puntos... ".$player[0]["name"]."\n\n";
      $sum0 = $sum0 + 3;
    } else {
      $res[1] .= $this->translatedText("txtCham81", $player[1]["name"]);                                               // "Más mods +20; 3 puntos... ".$player[1]["name"]."\n\n";
      $sum1 = $sum1 + 3;
    }
  
    if ($data0["mods25"] == $data1["mods25"]) {
      if ($player[0]["stats"][0]["value"] == $player[1]["stats"][0]["value"]) {
        $res[1] .= $this->translatedText("txtCham82", "");                                                              // "Más mods +25; 4 puntos... <b>empate</b>\n\n";
      } elseif ($player[0]["stats"][0]["value"] > $player[1]["stats"][0]["value"]) {
        $res[1] .= $this->translatedText("txtCham82", $player[1]["name"]);                                              // "Más mods +25; 4 puntos... <b>empate </b>".$player[1]["name"]."\n\n";
        $sum1 = $sum1 + 4;
	  } else {
        $res[1] .= $this->translatedText("txtCham82", $player[0]["name"]);                                              // "Más mods +25; 4 puntos... <b>empate </b>".$player[0]["name"]."\n\n";
        $sum0 = $sum0 + 4;
      }
    } elseif ($data0["mods25"] > $data1["mods25"]) {
      $res[1] .= $this->translatedText("txtCham83", $player[0]["name"]);                                                // "Más mods +25; 4 puntos... ".$player[0]["name"]."\n\n";
      $sum0 = $sum0 + 4;
    } else {
      $res[1] .= $this->translatedText("txtCham83", $player[1]["name"]);                                                // "Más mods +25; 4 puntos... ".$player[1]["name"]."\n\n\n";
      $sum1 = $sum1 + 4;
    }
    $res[1] .= $this->translatedText("txtCham84", $player[1]["name"]);                                                  // "*Máximo de puntos; 40* \n\n";
    $res[1] .= $this->translatedText("txtCham85", array($player[0]["name"], $sum0));                                    // $player[0]["name"].": ".$sum0." puntos \n";
    $res[1] .= $this->translatedText("txtCham85", array($player[1]["name"], $sum1));                                    // $player[1]["name"].": ".$sum1." puntos \n\n";
    $res[1] .= "\n";                                    
    
    return $res;   
  }

}


