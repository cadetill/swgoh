<?php
/*
    TODO LIST
    
    - EDS: Y ya si vas a por nota sacar lista de personajes ordenados por velocidad buscando por código de aliado. Q saque los 50 mejores por decir una cifra. Eso sería chevere
    - Dori: comando que busque un personaje o varios a un determinado nivel de gear? Devuelve la lista de personas que lo cumple.
          ex: /Search +Rjt r7 +rt r5 +finn r5
*/

/*
register - registers your ally code
unregister - unregisters your ally code
info - info about an account
zetas - shows zetas unlocked from an account
guild - shows a summary about your guild 
search - search this characters into the guild members
search2 - search this characters into the guild members
ga - compare your account with another
rank - shows the character ordered by the specified stat
im - shows all IM's guilds
compareg - compare two guilds
champions - champions for IM
tw - command for TW
alias - alias management
units - units management
teams - teams management
gf - check units for guild farming
here - mention a person/people
help - shows this help
panic - shows units needed for a specified unit
rancor - command for rancor
stats - stats for a list of units
*/
//error_reporting( E_ALL );
//ini_set('display_errors', 1);


  http_response_code(200);
  fastcgi_finish_request();
  
  require_once 'translate.php';
  require_once 'SwgohHelp.php';
  require_once 'config.php';
  require_once 'generalfunc.php'; 
  require_once 'tbase_class.php';
  require_once 'help.php';
  require_once 'register.php';
  require_once 'info.php';
  require_once 'zetas.php';
  require_once 'guild.php';
  require_once 'search.php';
  require_once 'ga.php';
  require_once 'rank.php';
  require_once 'alias.php';
  require_once 'im.php';
  require_once 'compareg.php';
  require_once 'champions.php';
  require_once 'unittocheck.php';
  require_once 'tw.php';
  require_once 'units.php';
  require_once 'teams.php';
  require_once 'gf.php';
  require_once 'here.php';
  require_once 'panic.php';
  require_once 'rancor.php';
  require_once 'stats.php';
  require_once 'statg.php';
  require_once './textimage/class.textPainter.php';

    $data = new TData();
    try {
        processRequest($data);
    } catch (\Exception $ex) {
        $debugData         = new TData();
        $debugData->chatId = $debugData->debugChatId;
        $response          = array_merge([ $ex->getMessage() ], $ex->getTrace());
        sendMessage($debugData, $response, false);
    } finally {
        debug($data, [ $data->message ]);
    }

/***********************************************************************************************************************************************************
  Funcions de caràcter general
***********************************************************************************************************************************************************/
function processRequest ($data) {
    /** @var TData $data */
    // agafem el JSON que ens envia i el decodifiquem
    $jsonText = file_get_contents('php://input');
    $json = json_decode($jsonText, TRUE);
    if ($json["message"]['from']['username'] == 'dlopezp') {
        file_put_contents('./json/json.json', $jsonText);
    }

    // agafem informació de la petició
    $data->message = $json["message"]["text"];
    $data->chatId = $json["message"]["chat"]["id"];
    $data->messageId = $json["message"]["message_id"];
    $data->messageDate = $json["message"]["date"];

    // agafem posible "comando" (sempre serà la primera paraula de la conversa)
    $arr = explode(' ',trim($data->message));
    $command = strtolower($arr[0]);

    // si no es un comando correcte, ja sortim
    if (!isCorrectCommand($command, $data)) {
        $data->debugMode = false;
        return;
    }

    // grabem comando
    //file_put_contents('./json', $jsonText);

    // agafem dades de l'usuari (Id i Nom)
    $data->userId = $json["message"]['from']['id'];
    $data->username = $json["message"]['from']['username'];
    $data->firstname = $json["message"]['from']['first_name'];

    // si està en manteniment i no és un usuari administratiu, sortim
    if (($data->maintenance) && (!isUserAdmin($data->username, $data))) {
        sendMessage($data, array("Bot in maintenance. Thanks to return in few minutes.\n\n".$data->username));
        return;
    }

    // agafem informació del jugador emmagatzemada en la base de dades (allycode i idioma del bot)
    $response = getDataFromId($data);
    if ($response != "") {
        sendMessage($data, array($response));
        return;
    }

    // si no ens intentem registrar i no ho estem, sortim
    if (($command != '/register') && ($command != '/register@impman_bot') &&
        ($command != '/help') && ($command != '/help@impman_bot') &&
        ($data->allycode == "")) {
        sendMessage($data, array("You must register before using the bot.\n\n"));
        return;
    }
    else {
        // agafem informació del jugador per saber de quin gremi és
        $swgoh = new SwgohHelp(array($data->swgohUser, $data->swgohPass));
        $playerJson = $swgoh->fetchPlayer( $data->allycode, $data->language );
        $player = json_decode($playerJson, true);
        $data->guildId = $player[0]["guildRefId"];

        //sendMessage($data, array("RefId: ".$player[0]["guildRefId"]."\n\n"));
        // si no és un gremi IM, sortim
        if (($command != '/register') && ($command != '/register@impman_bot') &&
            ($command != '/help') && ($command != '/help@impman_bot') &&
            !isIMGuild($player[0]["guildRefId"], $data) &&
            (!isUserAdmin($data->username, $data))) {
            sendMessage($data, array("You are not from an IM guild.\n\n"));
            return;
        }
    }

    // processem la petició realitzada
    $reply = true;
    switch ($command) {
        case '/help':
        case '/help@impman_bot':
            switch (count($arr)) {
                case 1:
                    $response = showHelp("", $data->language);
                    break;
                case 2:
                    $response = showHelp($arr[1], $data->language);
                    break;
                default:
                    $arr = explode(' +',trim($data->message));
                    switch (strtolower($arr[1])) {
                        // units to check
                        case "unittocheckadd":
                            if (count($arr) == 3) {
                                $response = unitToCheckAdd($arr[2], $data);
                            } else {
                                $response = showHelp("+", $data->language);
                                $response[0] = "Bad request. See help: \n\n".$response[0];
                            }
                            break;
                        case "unittochecklist":
                            $response = unitToCheckList($data);
                            break;
                        case "unittocheckdel":
                            if (count($arr) == 3) {
                                $response = unitToCheckDel($arr[2], $data);
                            } else {
                                $response = showHelp("+", $data->language);
                                $response[0] = "Bad request. See help: \n\n".$response[0];
                            }
                            break;
                    }
                    break;
            }
            break;
        case '/register':
        case '/register@impman_bot':
            $reg = new TRegister($arr, $data);
            $response = $reg->doRegister();
            break;
        case '/unregister':
        case '/unregister@impman_bot':
            $reg = new TUnRegister($arr, $data);
            $response = $reg->doUnRegister();
            break;
        case '/info':
        case '/info@impman_bot':
            $info = new TInfo($arr, $data);
            $response = $info->execCommand();
            break;
        case '/zetas':
        case '/zetas@impman_bot':
            $zetas = new TZetas($arr, $data);
            $response = $zetas->execCommand();
            break;
        case '/guild':
        case '/guild@impman_bot':
            $guild = new TGuild(explode(' +',trim($data->message)), $data);
            $response = $guild->execCommand();
            break;
        case '/search':
        case '/search@impman_bot':
            $search = new TSearch(explode(' +',trim($data->message)), $data, "search");
            $response = $search->execCommand();
            break;
        case '/search2':
        case '/search2@impman_bot':
            $search = new TSearch(explode(' +',trim($data->message)), $data, "search2");
            $response = $search->execCommand();
            break;
        case '/ga':
        case '/ga@impman_bot':
            $ga = new TGA($arr, $data);
            $response = $ga->execCommand();
            break;
        case '/rank':
        case '/rank@impman_bot':
            $rank = new TRank(explode(' +',trim($data->message)), $data);
            $response = $rank->execCommand();
            break;
        case '/im':
        case '/im@impman_bot':
            $im = new TIm(explode(' +',trim($data->message)), $data);
            $response = $im->execCommand();
            break;
        case '/compareg':
        case '/compareg@impman_bot':
            $compareg = new TCompareg($arr, $data);
            $response = $compareg->execCommand();
            break;
        case '/champions':
        case '/champions@impman_bot':
            $champions = new TChampions($arr, $data);
            $response = $champions->execCommand();
            break;
        case '/tw':
        case '/tw@impman_bot':
            $tw = new TTW(explode(' +',trim($data->message)), $data);
            $response = $tw->execCommand();
            break;
        case '/alias':
        case '/alias@impman_bot':
            $alias = new TAlias(explode(' +',trim($data->message)), $data);
            $response = $alias->execCommand();
            break;
        case '/units':
        case '/units@impman_bot':
            $units = new TUnits(explode(' +',trim($data->message)), $data);
            $response = $units->execCommand();
            break;
        case '/teams':
        case '/teams@impman_bot':
            $tw = new TTeams(explode(' +',trim($data->message)), $data);
            $response = $tw->execCommand();
            break;
        case '/gf':
        case '/gf@impman_bot':
            $gf = new TGF(explode(' +',trim($data->message)), $data);
            $response = $gf->execCommand();
            break;
        case '/here':
        case '/here@impman_bot':
            $here = new THere(explode(' +',trim($data->message)), $data);
            $response = $here->execCommand();
            break;
        case '/panic':
        case '/panic@impman_bot':
            $panic = new TPanic(explode(' +',trim($data->message)), $data);
            $response = $panic->execCommand();
            break;
        case '/rancor':
        case '/rancor@impman_bot':
            $rancor = new TRancor(explode(' +',trim($data->message)), $data);
            $response = $rancor->execCommand();
            break;
        case '/stats':
        case '/stats@impman_bot':
            $stats = new TStats(explode(' +',trim($data->message)), $data);
            $response = $stats->execCommand();
            break;
        case '/statg':
        case '/statg@impman_bot':
            $stats    = new TStatg(explode(' +', trim($data->message)), $data);
            $response = $stats->execCommand();
            break;
    }
    if (!is_array($response)) {
        $response = array($response);
    }

    debug($data, [ 'Responses count: '.count($response) ]);
    debug($data, [ 'Responses size: '.strlen(json_encode($response)) ]);
    $tgResponses = sendMessage($data, $response, $reply);
    debug($data, [ 'Telegram responses count: '.count($tgResponses) ]);
    debug($data, [ 'Telegram responses size: '.strlen(json_encode($tgResponses)) ]);
    debug($data, [ 'Telegram responses: '. json_encode($tgResponses) ]);
}

/**************************************************************************
  funció que enviarà missatges a Telegram
**************************************************************************/
function sendMessage($data, $response, $reply = true, $keyboard = NULL) {
    return sendGetMessage($data, $response, $reply, $keyboard);
    // sendPostMessage($data, $response, $reply, $keyboard);
}

function sendGetMessage($data, $response, $reply = true, $keyboard = NULL) {
    if (isset($keyboard)) {
        $teclado = '&reply_markup={"keyboard":['.$keyboard.'], "resize_keyboard":true, "one_time_keyboard":true}';
    }

    $telegramResponses = [];

    $first = true;
    foreach ($response as $res) {
        if ($first && $reply) {
            $url = $data->website.'/sendMessage?chat_id='.$data->chatId.'&reply_to_message_id='.$data->messageId.'&parse_mode=HTML&text='.urlencode($res).$teclado;
            $first = false;
        } else {
            $url = $data->website.'/sendMessage?chat_id='.$data->chatId.'&parse_mode=HTML&text='.urlencode($res).$teclado;
        }

        $telegramResponses[] = file_get_contents($url);
    }

    return $telegramResponses;
}

function sendPostMessage($data, $response, $reply = true, $keyboard = NULL) {
    $url = $data->website.'/sendMessage';
    $payloadBase = [
        'chat_id'       => $data->chatId,
        'parse_mode'    => 'HTML'
    ];

    foreach ($response as $res) {
        $payload = array_merge($payloadBase, [ 'text' => $res ]);
        $options = [
            'http' => [
                'header'  => join("\r\n", [ 'Content-type: application/x-www-form-urlencoded' ]),
                'method'  => 'POST',
                'content' => http_build_query($payload)
            ]
        ];
        $context  = stream_context_create($options);
        $result = file_get_contents($url, false, $context);
    }
}

/**************************************************************************
  funció que enviarà fotos a Telegram
**************************************************************************/
function sendPhoto($data, $photoFile, $photoUrl, $photoText) {
  if ($photoFile != '') {
    $photo = '@'.$photoFile;
  }
  else {
    $photo = $photoUrl;
  }
  $url = $data->website.'/sendPhoto?chat_id='.$data->chatId.'&reply_to_message_id='.$data->messageId.'&parse_mode=HTML&caption='.urlencode($photoText).'&photo='.$photo;
       
  file_get_contents($url);
}

/**************************************************************************
  funció que agafarà les dades guardades a la BBDD de l'usuari
**************************************************************************/
function getDataFromId($data) {
  $idcon = new mysqli($data->bdserver, $data->bduser, $data->bdpas, $data->bdnamebd);
  if ($idcon->connect_error) {
    echo "Error: Fallo al conectarse a MySQL debido a: \n";
    echo "Errno: " . $idcon->connect_errno . "\n";
    echo "Error: " . $idcon->connect_error . "\n";
    return "Ooooops! An error has occurred getting data...";
  }
  
  $ret = "";
  $sql = 'select * FROM users WHERE id ='.$data->userId;
  $res = $idcon->query( $sql );
  if ($idcon->error) {
    $ret = "Ooooops! An error has occurred getting data.";
  } 
  else {
    $row = $res->fetch_assoc();
    if (isset($row)) {
      $data->allycode = $row['allycode'];
      $data->language = $row['language'];
    }
  }
  $idcon->close();

  return $ret;
}

function debug ($data, $response) {
    /** @var TData $data */
    if (!$data->debugMode) return;
    $logData         = new TData();
    $logData->chatId = $logData->debugChatId;
    sendMessage($logData, array_merge([ 'debug: ' ], $response), false);
}
