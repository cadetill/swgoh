<?php

class TData
{
    // variables de connexió a la base de dades
    public $bdserver = 'localhost';
    public $bdnamebd = 'your_BBDD_name';
    public $bduser   = 'your_BBDD_username';
    public $bdpas    = 'your_BBDD_pass';

    // variables connexió a la API de swgoh.help 
    public $swgohUser = 'your_username';
    public $swgohPass = 'your_pass';
    public $botToken  = "your_bot_token";
    public $website   = "your_website";

    // variables del xat
    public $chatId      = "";
    public $userId      = "";
    public $username    = "";
    public $firstname   = "";
    public $allyCode    = "";
    public $language    = "";
    public $message     = "";
    public $messageId   = "";
    public $messageDate = "";
    public $guildId     = "";
    public $debugChatId = "";

    // noms d'arxius
    public $unitsFile = "./json/units.json";
    public $aliasFile = "./json/alias.json";
    public $imFile    = "./json/im.json";
    public $panicFile = "./json/panic.json";
    public $imPhoto   = "https://cadetill.com/swgoh/bot/img/im006.jpg";

    // urls externes
    public $swgoh_gg = 'https://swgoh.gg/game-asset/u/';
    public $chars_gg = 'https://swgoh.gg/api/characters/';
    public $fleet_gg = 'https://swgoh.gg/api/ships/';

    // admins del bot
    public $admins = [ 'cadetill' ];

    // comandos acceptats pel bot
    public $comands = [
        '/help',
        '/help@impman_bot',                        // shows this help
        '/register',
        '/register@impman_bot',                // registers your ally code
        '/unregister',
        '/unregister@impman_bot',            // unregisters your ally code
        '/info',
        '/info@impman_bot',                        // info about an account
        '/zetas',
        '/zetas@impman_bot',                      // shows zetas unlocked from an account
        '/guild',
        '/guild@impman_bot',                      // shows a summary about your guild
        '/search',
        '/search@impman_bot',                    // search this characters into the guild members
        '/search2',
        '/search2@impman_bot',                  // search this characters into the guild members
        '/ga',
        '/ga@impman_bot',                            // compare your account with another
        '/rank',
        '/rank@impman_bot',                        // shows the character ordered by the specified stat
        '/im',
        '/im@impman_bot',                            // shows all IM's guilds
        '/compareg',
        '/compareg@impman_bot',                // compare two guilds
        '/champions',
        '/champions@impman_bot',              // champions for IM
        '/tw',
        '/tw@impman_bot',                            // command for TW
        '/alias',
        '/alias@impman_bot',                      // alias management
        '/units',
        '/units@impman_bot',                      // units management
        '/teams',
        '/teams@impman_bot',                      // teams management
        '/gf',
        '/gf@impman_bot',                            // check units for guild farming
        '/here',
        '/here@impman_bot',                        // mention a person/people
        '/panic',
        '/panic@impman_bot',                      // units needed to get specified unit
        '/rancor',
        '/rancor@impman_bot',                    // command for Rancor raid
        '/stats',
        '/stats@impman_bot',                       // stats for a list of units
        '/statg',
        '/statg@impman_bot'
    ];

    // unitats noves o a eliminar del bot per a que funcioni la crinolo API
    public $unitsToDelete = [ ];

    //màxim de caràcters per missatge
    public $maxChars = 3000;

    public $maintenance = false;

    public $debugMode = true;

    public function __construct()
    {
        $this->loadEnvVars();
        $this->overrideDefaultAttributes();
    }

    private function loadEnvVars()
    {
        $envFilePath = __DIR__ . '/.env';
        if (!file_exists($envFilePath)) {
            return;
        }
        if (!is_readable($envFilePath)) {
            return;
        }

        $lines = file($envFilePath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        foreach ($lines as $line) {
            if (strpos(trim($line), '#') === 0) {
                continue;
            }

            list($name, $value) = explode('=', $line, 2);
            $name  = trim($name);
            $value = trim($value);

            if (!array_key_exists($name, $_SERVER) && !array_key_exists($name, $_ENV)) {
                putenv(sprintf('%s=%s', $name, $value));
                $_ENV[$name]    = $value;
                $_SERVER[$name] = $value;
            }
        }
    }

    private function overrideDefaultAttributes()
    {
        $this->bdserver = getenv('BD_SERVER');
        $this->bdnamebd = getenv('BD_NAME');
        $this->bduser   = getenv('BD_USER');
        $this->bdpas    = getenv('BD_PWD');

        $this->swgohUser = getenv('SWGOH_HELP_USER');
        $this->swgohPass = getenv('SWGOH_HELP_PWD');

        $this->botToken = getenv('TELEGRAM_BOT_TOKEN');
        $this->website = getenv('TELEGRAM_BOT_URL');

        $this->debugChatId = getenv('DEBUG_CHAT_ID');
    }
}
