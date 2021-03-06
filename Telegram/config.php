<?php
  class TData {
    // variables de connexió a la base de dades
    public $bdserver = '----';
    public $bdnamebd = '----';
    public $bduser = '----'; 
    public $bdpas = '-----';
//    public $bdserver = '----';
//    public $bdnamebd = '----';
//    public $bduser = '----';
//    public $bdpas = '----';

    // variables connexió a la API de swgoh.help 
    public $swgohUser = '----';
    public $swgohPass = '----';
    public $botToken = "----";
    public $website = "----";

    // variables del xat
    public $chatId = "";
    public $userId = "";
    public $username = "";
    public $firstname = "";
    public $allyCode = "";
    public $language = "";	
    public $message = "";
    public $messageId = "";
    public $messageDate = "";
    public $guildId = "";

    // noms d'arxius
    public $unitsFile = "./json/units.json";
    public $aliasFile = "./json/alias.json";
    public $imFile = "./json/im.json";
    public $panicFile = "./json/panic.json";
    public $imPhoto = "https://cadetill.com/swgoh/bot/img/im004.jpg";
    
    // urls externes
    public $swgoh_gg = 'https://swgoh.gg/game-asset/u/';
    public $chars_gg = 'https://swgoh.gg/api/characters/';
    public $fleet_gg = 'https://swgoh.gg/api/ships/';
	
    // admins del bot
    public $admins = array('cadetill');
	
    // comandos acceptats pel bot
    public $comands = array(
                            '/help', '/help@impman_bot',                        // shows this help
                            '/register', '/register@impman_bot',                // registers your ally code
                            '/unregister', '/unregister@impman_bot',            // unregisters your ally code
                            '/info', '/info@impman_bot',                        // info about an account
                            '/zetas', '/zetas@impman_bot',                      // shows zetas unlocked from an account
                            '/guild', '/guild@impman_bot',                      // shows a summary about your guild 
                            '/search', '/search@impman_bot',                    // search this characters into the guild members
                            '/search2', '/search2@impman_bot',                  // search this characters into the guild members
                            '/ga', '/ga@impman_bot',                            // compare your account with another
                            '/rank', '/rank@impman_bot',                        // shows the character ordered by the specified stat
                            '/im', '/im@impman_bot',                            // shows all IM's guilds
                            '/compareg', '/compareg@impman_bot',                // compare two guilds
                            '/champions', '/champions@impman_bot',              // champions for IM
                            '/tw', '/tw@impman_bot',                            // command for TW
                            '/alias', '/alias@impman_bot',                      // alias management
                            '/units', '/units@impman_bot',                      // units management
                            '/teams', '/teams@impman_bot',                      // teams management
                            '/gf', '/gf@impman_bot',                            // check units for guild farming
                            '/here', '/here@impman_bot',                        // mention a person/people
                            '/panic', '/panic@impman_bot',                      // units needed to get specified unit
                            '/rancor', '/rancor@impman_bot'                     // command for Rancor raid
                           );
    
    // unitats noves o a eliminar del bot per a que funcioni la crinolo API
    public $unitsToDelete = array();
    
    public $maintenance = false;
  }
