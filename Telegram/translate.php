<?php
class TTranslate {
  private $defLang = array("ENG_US", "SPA_XM");
  private $lang = "";
  private $translated = array();
  
  /****************************************************
    constructor de la classe. Inicialitza variables
  ****************************************************/
  public function __construct($lang) {
    if ($this->isDefined($lang))
      $this->lang = $lang;
    else 
      $this->lang = "ENG_US";
    
    $this->iniTransText();
  }
  
  /****************************************************
    FUNCIONS PUBLIQUES
  ****************************************************/
  /****************************************************
    retorna un clau ($code) traduïda
  ****************************************************/
  public function getTransText($code, $arr = '') {      
    $tmp = $this->translated[$this->lang][$code];
    if (is_array($arr)) {
      foreach ($arr as $var) {
        $tmp = preg_replace('/%s/', $var, $tmp, 1);
      }
    }
    else
      $tmp = str_replace('%s', $arr ?? '', $tmp ?? '');
    return $tmp;
  }
  
  /****************************************************
    FUNCIONS PRIVADES
  ****************************************************/
  /****************************************************
    indica si un idioma està traduït 
  ****************************************************/
  private function isDefined($lang) {
    return in_array($lang, $this->defLang);
  }
  
  /****************************************************
    inicialitza els arrays de traducció
  ****************************************************/
  private function iniTransText() {
    // -----------------------------
    // English Text
    // -----------------------------
    // errors generals
    $this->translated["ENG_US"]["error1"] = "Bad request. Read command help: \n\n";
    $this->translated["ENG_US"]["error2"] = "Unit '%s' not found into the inventory.\n\n";
    $this->translated["ENG_US"]["error3"] = "The %s isn't a correct AllyCode.\n Read command help: \n\n";
    $this->translated["ENG_US"]["error4"] = "Ooooops! An error has occurred getting data.\n\n";
    $this->translated["ENG_US"]["error5"] = "Incorrect command %s.\n\n";
    $this->translated["ENG_US"]["error6"] = "Ooooops! API server may have shut down. Try again later.\n\n";

    // misstges generals
    $this->translated["ENG_US"]["elapsed_time"] = "\n<i>Elapsed time: %s</i>\n";
    $this->translated["ENG_US"]["last_update"] = "<i>Last update: %s</i>\n";

    // classe TAlias
    $this->translated["ENG_US"]["aliaserr1"] = "The alias '%s' for '%s' already exists into the unit '%s'.\n\n";
    $this->translated["ENG_US"]["txtAlias1"] = "<b>List of alias</b>\n\n";
    $this->translated["ENG_US"]["txtAlias2"] = "Alias added\n\n";
    $this->translated["ENG_US"]["txtAlias3"] = "   <b>Unit</b>: %s\n";
    $this->translated["ENG_US"]["txtAlias4"] = "   <b>Alias</b>: %s\n";
    $this->translated["ENG_US"]["txtAlias5"] = "Alias '%s' was deleted.\n\n";

    // clasee TUnits
    $this->translated["ENG_US"]["unitserr1"] = "Unit %s already exists.\n\n";
    $this->translated["ENG_US"]["unitserr2"] = "No units to control.\n\n";
    $this->translated["ENG_US"]["txtUnits1"] = "<b>List of units</b>\n\n";
    $this->translated["ENG_US"]["txtUnits2"] = "Units have been updated.\n\n";
    $this->translated["ENG_US"]["txtUnits3"] = "Units %s added to the control unit list.\n\n";
    $this->translated["ENG_US"]["txtUnits4"] = "Unit %s has ben deleted \n\n";
    $this->translated["ENG_US"]["txtUnits5"] = "Units to control for %s \n\n";
    $this->translated["ENG_US"]["txtUnits6"] = "Commands \n\n";
    $this->translated["ENG_US"]["txtUnits7"] = "All units erased \n\n";

    // classe TChampions
    $this->translated["ENG_US"]["txtCham01"] = "<b>----- %s vs %s -----</b>\n";
    $this->translated["ENG_US"]["txtCham02"] = "<b>GP</b>: %s vs %s\n";
    $this->translated["ENG_US"]["txtCham03"] = "<b>GP Char.</b>: %s vs %s\n";
    $this->translated["ENG_US"]["txtCham04"] = "<b>GP Ships</b>: %s vs %s\n";
    $this->translated["ENG_US"]["txtCham05"] = "<b>----------- Arena -----------</b>\n";
    $this->translated["ENG_US"]["txtCham06"] = "<b>Squad</b>: %s vs %s\n";
    $this->translated["ENG_US"]["txtCham07"] = "<b>Fleet</b>: %s vs %s\n";
    $this->translated["ENG_US"]["txtCham08"] = "<b>----------- Championships -----------</b>\n";
    $this->translated["ENG_US"]["txtCham09"] = "<b>Current Rank</b>: %s vs %s\n";
    $this->translated["ENG_US"]["txtCham10"] = "<b>Division</b>: %s vs %s\n";
    $this->translated["ENG_US"]["txtCham11"] = "<b>Best Rank Achieved</b>: %s vs %s\n";
    $this->translated["ENG_US"]["txtCham12"] = "<b>Lifetime Score</b>: %s vs %s\n";
    $this->translated["ENG_US"]["txtCham13"] = "<b>Offensive Won</b>: %s vs %s\n";
    $this->translated["ENG_US"]["txtCham14"] = "<b>Successful Defends</b>: %s vs %s\n";
    $this->translated["ENG_US"]["txtCham15"] = "<b>Territories Defeated</b>: %s vs %s\n";
    $this->translated["ENG_US"]["txtCham16"] = "<b>Banners Earned</b>: %s vs %s\n";
    $this->translated["ENG_US"]["txtCham17"] = "<b>Promotions Earned</b>: %s vs %s\n";
    $this->translated["ENG_US"]["txtCham18"] = "<b>----------- Gear -----------</b>\n";
    $this->translated["ENG_US"]["txtCham19"] = "<b>7 stars</b>: %s vs %s\n";
    $this->translated["ENG_US"]["txtCham20"] = "<b>Gear 13</b>: %s vs %s\n";
    $this->translated["ENG_US"]["txtCham21"] = "<b>Gear 12</b>: %s vs %s\n";
    $this->translated["ENG_US"]["txtCham22"] = "<b>Gear 12+1</b>: %s vs %s\n";
    $this->translated["ENG_US"]["txtCham23"] = "<b>Gear 12+2</b>: %s vs %s\n";
    $this->translated["ENG_US"]["txtCham24"] = "<b>Gear 12+3</b>: %s vs %s\n";
    $this->translated["ENG_US"]["txtCham25"] = "<b>Gear 12+4</b>: %s vs %s\n";
    $this->translated["ENG_US"]["txtCham26"] = "<b>Gear 12+5</b>: %s vs %s\n";
    $this->translated["ENG_US"]["txtCham27"] = "<b>Gear 11</b>: %s vs %s\n";
    $this->translated["ENG_US"]["txtCham28"] = "<b>Zetas</b>: %s vs %s\n";
    $this->translated["ENG_US"]["txtCham29"] = "<b>----------- Relics -----------</b>\n";
    $this->translated["ENG_US"]["txtCham30"] = "<b>Total</b>: %s vs %s\n";
    $this->translated["ENG_US"]["txtCham31"] = "<b>Relic 7</b>: %s vs %s\n";
    $this->translated["ENG_US"]["txtCham32"] = "<b>Relic 6</b>: %s vs %s\n";
    $this->translated["ENG_US"]["txtCham33"] = "<b>Relic 5</b>: %s vs %s\n";
    $this->translated["ENG_US"]["txtCham34"] = "<b>Relic 4</b>: %s vs %s\n";
    $this->translated["ENG_US"]["txtCham35"] = "<b>Relic 3</b>: %s vs %s\n";
    $this->translated["ENG_US"]["txtCham36"] = "<b>Relic 2</b>: %s vs %s\n";
    $this->translated["ENG_US"]["txtCham37"] = "<b>Relic 1</b>: %s vs %s\n";
    $this->translated["ENG_US"]["txtCham38"] = "<b>----------- Mods -----------</b>\n";
    $this->translated["ENG_US"]["txtCham39"] = "<b>Mods 6</b>: %s vs %s\n";
    $this->translated["ENG_US"]["txtCham40"] = "<b>Speed +10</b>: %s vs %s\n";
    $this->translated["ENG_US"]["txtCham41"] = "<b>Speed +15</b>: %s vs %s\n";
    $this->translated["ENG_US"]["txtCham42"] = "<b>Speed +20</b>: %s vs %s\n";
    $this->translated["ENG_US"]["txtCham43"] = "<b>Speed +25</b>: %s vs %s\n";
    $this->translated["ENG_US"]["txtCham44"] = "Higher Gp: 2 points ... <b>tie</b>\n\n";
    $this->translated["ENG_US"]["txtCham45"] = "Higher Gp: 2 points ... %s";
    $this->translated["ENG_US"]["txtCham46"] = "Higher Gp Chars: 1 point... <b>tie</b> %s\n\n";
    $this->translated["ENG_US"]["txtCham47"] = "Higher Gp Chars: 1 point... %s\n\n";
    $this->translated["ENG_US"]["txtCham48"] = "Higher Gp Ships: 1 point... <b>tie</b> %s\n\n";
    $this->translated["ENG_US"]["txtCham49"] = "Higher Gp Ships: 1 point... %s\n\n";
    $this->translated["ENG_US"]["txtCham50"] = "More Won Battles GA: 2 points... <b>tie</b> %s\n\n";
    $this->translated["ENG_US"]["txtCham51"] = "More Won Battles GA: 2 points... %s\n\n";
    $this->translated["ENG_US"]["txtCham52"] = "More Successful Defends GA: 2 points... <b>tie</b> %s\n\n";
    $this->translated["ENG_US"]["txtCham53"] = "More Successful Defends GA: 2 points... %s\n\n";
    $this->translated["ENG_US"]["txtCham54"] = "More Territories Defeated: 1 point... <b>tie</b> %s\n\n";
    $this->translated["ENG_US"]["txtCham55"] = "More Territories Defeated: 1 point... %s\n\n";
    $this->translated["ENG_US"]["txtCham56"] = "More Banners Earned: 1 point... <b>tie</b> %s\n\n";
    $this->translated["ENG_US"]["txtCham57"] = "More Banners Earned: 1 point... %s\n\n";
    $this->translated["ENG_US"]["txtCham58"] = "More Promotions Earned: 1 point... <b>tie</b> %s\n\n";
    $this->translated["ENG_US"]["txtCham59"] = "More Promotions Earned: 1 point... %s\n\n";
    $this->translated["ENG_US"]["txtCham60"] = "More 7* char.: 2 points... <b>tie</b> %s\n\n";
    $this->translated["ENG_US"]["txtCham61"] = "More 7* char.: 2 points... %s\n\n";
    $this->translated["ENG_US"]["txtCham62"] = "More g13 char.: 3 points... <b>tie</b> %s\n\n";
    $this->translated["ENG_US"]["txtCham63"] = "More g13 char.: 3 points... %s\n\n";
    $this->translated["ENG_US"]["txtCham64"] = "More g12 char.: 2 points... <b>tie</b> %s\n\n";
    $this->translated["ENG_US"]["txtCham65"] = "More g12 char.: 2 points... %s\n\n";
    $this->translated["ENG_US"]["txtCham66"] = "More g11 char.: 1 point... <b>tie</b> %s\n\n";
    $this->translated["ENG_US"]["txtCham67"] = "More g11 char.: 1 point... %s\n\n";
    $this->translated["ENG_US"]["txtCham68"] = "More zetas: 3 points... <b>tie</b> %s\n\n";
    $this->translated["ENG_US"]["txtCham69"] = "More zetas: 3 points... %s\n\n";
    $this->translated["ENG_US"]["txtCham70"] = "More number of relics: 3 points... <b>tie</b> %s\n\n";
    $this->translated["ENG_US"]["txtCham71"] = "More number of relics: 3 points... %s\n\n";
    $this->translated["ENG_US"]["txtCham72"] = "More r7: 3 points... <b>tie</b> %s\n\n";
    $this->translated["ENG_US"]["txtCham73"] = "More r7: 3 points... %s\n\n";
    $this->translated["ENG_US"]["txtCham74"] = "More mods 6*: 2 points... <b>tie</b> %s\n\n";
    $this->translated["ENG_US"]["txtCham75"] = "More mods 6*: 2 points... %s\n\n";
    $this->translated["ENG_US"]["txtCham76"] = "More mods +10: 1 point... <b>tie</b> %s\n\n";
    $this->translated["ENG_US"]["txtCham77"] = "More mods +10: 1 point... %s\n\n";
    $this->translated["ENG_US"]["txtCham78"] = "More mods +15: 2 points... <b>tie</b> %s\n\n";
    $this->translated["ENG_US"]["txtCham79"] = "More mods +15: 2 points... %s\n\n";
    $this->translated["ENG_US"]["txtCham80"] = "More mods +20: 3 points... <b>tie</b> %s\n\n";
    $this->translated["ENG_US"]["txtCham81"] = "More mods +20: 3 points... %s\n\n";
    $this->translated["ENG_US"]["txtCham82"] = "More mods +25: 4 points... <b>tie</b> %s\n\n";
    $this->translated["ENG_US"]["txtCham83"] = "More mods +25: 4 points... %s\n\n";
    $this->translated["ENG_US"]["txtCham84"] = "*Max points: 40* \n";
    $this->translated["ENG_US"]["txtCham85"] = "%s: %s points \n";
    $this->translated["ENG_US"]["txtCham86"] = "<b>Top 80</b>: %s vs %s\n";
    $this->translated["ENG_US"]["txtCham87"] = "<b>Units</b>\n";
    $this->translated["ENG_US"]["txtCham88"] = "<b>Relic 8</b>: %s vs %s\n";
    $this->translated["ENG_US"]["txtCham89"] = "<b>-------GA OMICRONS-------</b>\n\n";
    $this->translated["ENG_US"]["txtCham90"] = "<b>QGJ</b>: %s vs %s\n";
    $this->translated["ENG_US"]["txtCham91"] = "<b>DashRendar</b>: %s vs %s\n";
    $this->translated["ENG_US"]["txtCham92"] = "<b>Zam</b>: %s vs %s\n";
    $this->translated["ENG_US"]["txtCham93"] = "<b>Rose</b>: %s vs %s\n";
    $this->translated["ENG_US"]["txtCham94"] = "<b>Talon</b>: %s vs %s\n";
    $this->translated["ENG_US"]["txtCham95"] = "<b>Chirpa</b>: %s vs %s\n";
    $this->translated["ENG_US"]["txtCham96"] = "<b>Wampa</b>: %s vs %s\n";
    $this->translated["ENG_US"]["txtCham97"] = "<b>Iden Versio</b>: %s vs %s\n";
    $this->translated["ENG_US"]["txtCham98"] = "<b>Ackbar</b>: %s vs %s\n";
    $this->translated["ENG_US"]["txtCham99"] = "<b>Leia</b>: %s vs %s\n";
    $this->translated["ENG_US"]["txtCham100"] = "<b>Krennic</b>: %s vs %s\n";
    $this->translated["ENG_US"]["txtCham101"] = "<b>Starkiller (special 1)</b>: %s vs %s\n";
    $this->translated["ENG_US"]["txtCham102"] = "<b>Starkiller (special 2)</b>: %s vs %s\n";
    $this->translated["ENG_US"]["txtCham103"] = "<b>Starkiller (unique)</b>: %s vs %s\n";
    $this->translated["ENG_US"]["txtCham104"] = "<b>Relic 9</b>: %s vs %s\n";

    // classe TCompareg
    $this->translated["ENG_US"]["txtCompareg01"] = "<b>-----------SUMMARY-----------</b>\n";
    $this->translated["ENG_US"]["txtCompareg02"] = "<b>Members</b>: %s vs %s\n";
    $this->translated["ENG_US"]["txtCompareg03"] = "<b>GP</b>: %s vs %s\n";
    $this->translated["ENG_US"]["txtCompareg04"] = "<b>GP (players sum)</b>: %s vs %s\n";
    $this->translated["ENG_US"]["txtCompareg05"] = "<b>GP (characters)</b>: %s vs %s\n";
    $this->translated["ENG_US"]["txtCompareg06"] = "<b>GP (ships)</b>: %s vs %s\n";
    $this->translated["ENG_US"]["txtCompareg07"] = "<b>GP top 80</b>: %s vs %s\n";
    $this->translated["ENG_US"]["txtCompareg08"] = "<b>Av. Arena</b>: %s vs %s\n";
    $this->translated["ENG_US"]["txtCompareg09"] = "<b>Av. Ships</b>: %s vs %s\n";
    $this->translated["ENG_US"]["txtCompareg10"] = "<b>Zetas</b>: %s vs %s\n";
    $this->translated["ENG_US"]["txtCompareg11"] = "<b>Gear 13</b>: %s vs %s\n";
    $this->translated["ENG_US"]["txtCompareg12"] = "<b>Gear 12</b>: %s vs %s\n";
    $this->translated["ENG_US"]["txtCompareg13"] = "<b>Gear 12+1</b>: %s vs %s\n";
    $this->translated["ENG_US"]["txtCompareg14"] = "<b>Gear 12+2</b>: %s vs %s\n";
    $this->translated["ENG_US"]["txtCompareg15"] = "<b>Gear 12+3</b>: %s vs %s\n";
    $this->translated["ENG_US"]["txtCompareg16"] = "<b>Gear 12+4</b>: %s vs %s\n";
    $this->translated["ENG_US"]["txtCompareg17"] = "<b>Gear 12+5</b>: %s vs %s\n";
    $this->translated["ENG_US"]["txtCompareg18"] = "<b>-----------RELICS-----------</b>\n";
    $this->translated["ENG_US"]["txtCompareg19"] = "<b>Relics</b>: %s vs %s\n";
    $this->translated["ENG_US"]["txtCompareg20"] = "<b>Tier 7</b>: %s vs %s\n";
    $this->translated["ENG_US"]["txtCompareg21"] = "<b>Tier 6</b>: %s vs %s\n";
    $this->translated["ENG_US"]["txtCompareg22"] = "<b>Tier 5</b>: %s vs %s\n";
    $this->translated["ENG_US"]["txtCompareg23"] = "<b>Tier 4</b>: %s vs %s\n";
    $this->translated["ENG_US"]["txtCompareg24"] = "<b>Tier 3</b>: %s vs %s\n";
    $this->translated["ENG_US"]["txtCompareg25"] = "<b>Tier 2</b>: %s vs %s\n";
    $this->translated["ENG_US"]["txtCompareg26"] = "<b>Tier 1</b>: %s vs %s\n";
    $this->translated["ENG_US"]["txtCompareg27"] = "<b>-----------MODS-----------</b>\n";
    $this->translated["ENG_US"]["txtCompareg28"] = "<b>6*</b>: %s vs %s\n";
    $this->translated["ENG_US"]["txtCompareg29"] = "<b>25+</b>: %s vs %s\n";
    $this->translated["ENG_US"]["txtCompareg30"] = "<b>20+</b>: %s vs %s\n";
    $this->translated["ENG_US"]["txtCompareg31"] = "<b>15+</b>: %s vs %s\n";
    $this->translated["ENG_US"]["txtCompareg32"] = "<b>10+</b>: %s vs %s\n";
    $this->translated["ENG_US"]["txtCompareg33"] = "<b>Units</b>\n";
    $this->translated["ENG_US"]["txtCompareg34"] = "<b>Tier 8</b>: %s vs %s\n";
    $this->translated["ENG_US"]["txtCompareg35"] = "<b>Tier 9</b>: %s vs %s\n";
    $this->translated["ENG_US"]["txtCompareg36"] = "<b>-------TW OMICRONS-------</b>\n";
    $this->translated["ENG_US"]["txtCompareg37"] = "<b>Phasma</b>: %s vs %s\n";
    $this->translated["ENG_US"]["txtCompareg38"] = "<b>Nebit</b>: %s vs %s\n";
      $this->translated["ENG_US"]["txtCompareg39"] = "<b>Mara Jade</b>: %s vs %s\n";
      $this->translated["ENG_US"]["txtCompareg40"] = "<b>Sidious</b>: %s vs %s\n";
      $this->translated["ENG_US"]["txtCompareg41"] = "<b>Hera</b>: %s vs %s\n";
      $this->translated["ENG_US"]["txtCompareg42"] = "<b>SoJ (special)</b>: %s vs %s\n";
      $this->translated["ENG_US"]["txtCompareg43"] = "<b>SoJ (leader)</b>: %s vs %s\n";
      $this->translated["ENG_US"]["txtCompareg44"] = "<b>SoJ (unique)</b>: %s vs %s\n";
      $this->translated["ENG_US"]["txtCompareg45"] = "<b>Windu</b>: %s vs %s\n";
      $this->translated["ENG_US"]["txtCompareg46"] = "<b>Embo</b>: %s vs %s\n";
      $this->translated["ENG_US"]["txtCompareg47"] = "<b>Second Sister</b>: %s vs %s\n";
      $this->translated["ENG_US"]["txtCompareg48"] = "<b>T3-M4</b>: %s vs %s\n";
      $this->translated["ENG_US"]["txtCompareg49"] = "<b>Ninth Sister</b>: %s vs %s\n";
      $this->translated["ENG_US"]["txtCompareg50"] = "<b>Eight Brother</b>: %s vs %s\n";
      $this->translated["ENG_US"]["txtCompareg51"] = "<b>Seventh Sister</b>: %s vs %s\n";

    // classe TGuild
    $this->translated["ENG_US"]["txtGuild01"] = "<b>Guild</b>: %s\n";
    $this->translated["ENG_US"]["txtGuild02"] = "<b>Members</b>: %s\n";
    $this->translated["ENG_US"]["txtGuild03"] = "<b>Internal message</b>: %s\n";
    $this->translated["ENG_US"]["txtGuild04"] = "<b>GP InGame</b>: %s\n";
    $this->translated["ENG_US"]["txtGuild05"] = "<b>GP Calculated</b>: %s\n";
    $this->translated["ENG_US"]["txtGuild06"] = "<b>GP Characters</b>: %s\n";
    $this->translated["ENG_US"]["txtGuild07"] = "<b>GP Ships</b>: %s\n";
    $this->translated["ENG_US"]["txtGuild08"] = "<b>GP Average</b>: %s\n";
    $this->translated["ENG_US"]["txtGuild09"] = "<b>Average Arena</b>: %s\n";
    $this->translated["ENG_US"]["txtGuild10"] = "<b>Average Ships</b>: %s\n";
    $this->translated["ENG_US"]["txtGuild11"] = "<b>GP</b>: %s\n";
    $this->translated["ENG_US"]["txtGuild12"] = "<b>Avg</b>: %s\n";
    $this->translated["ENG_US"]["txtGuild13"] = "<b>Gear 13</b>: %s\n";
    $this->translated["ENG_US"]["txtGuild14"] = "<b>Gear 12</b>: %s\n";
    $this->translated["ENG_US"]["txtGuild15"] = "<b>Gear 11</b>: %s\n";
    $this->translated["ENG_US"]["txtGuild16"] = "<b>Average</b>: %s\n";
    $this->translated["ENG_US"]["txtGuild17"] = "<b>Top80</b>: %s\n";
    $this->translated["ENG_US"]["txtGuild18"] = "<b>GP Ships</b>: %s\n";
    $this->translated["ENG_US"]["txtGuild19"] = "<b>GP Chars</b>: %s\n";
    $this->translated["ENG_US"]["txtGuild20"] = "<b>Mods 6</b>: %s\n";
    $this->translated["ENG_US"]["txtGuild21"] = "<b>Mods 25</b>: %s\n";
    $this->translated["ENG_US"]["txtGuild22"] = "<b>Mods 20</b>: %s\n";
    $this->translated["ENG_US"]["txtGuild23"] = "<b>Mods 15</b>: %s\n";
    $this->translated["ENG_US"]["txtGuild24"] = "<b>Relic 8</b>: %s\n";
    $this->translated["ENG_US"]["txtGuild25"] = "<b>Relic 7</b>: %s\n";
    $this->translated["ENG_US"]["txtGuild26"] = "<b>Not Registered Members</b>\n";
    $this->translated["ENG_US"]["txtGuild27"] = "<b>Registered Members</b>\n";

    // classe TIm
    $this->translated["ENG_US"]["txtIm01"] = "<b>łmperio Mandaloriano Group</b>\n";
    $this->translated["ENG_US"]["txtIm02"] = "<b>Leader</b>: %s\n";
    $this->translated["ENG_US"]["txtIm03"] = "<b>GP</b>: %s\n";
    $this->translated["ENG_US"]["txtIm04"] = "<b>Players</b>: %s\n";
    $this->translated["ENG_US"]["txtIm05"] = "<b>url</b>: %s\n";
    $this->translated["ENG_US"]["txtIm06"] = "More Info:\n";
    $this->translated["ENG_US"]["txtIm07"] = "<b>Guild added to IM</b>\n";
    $this->translated["ENG_US"]["txtIm08"] = "<b>Guild Name</b>: %s\n";
    $this->translated["ENG_US"]["txtIm09"] = "<b>Acronym</b>: %s\n";
    $this->translated["ENG_US"]["txtIm10"] = "<b>Leader</b>: %s\n";
    $this->translated["ENG_US"]["txtIm11"] = "<b>Guild deleted from IM</b>\n";
    $this->translated["ENG_US"]["txtIm12"] = "<b>AllyCode</b>: %s\n";
    $this->translated["ENG_US"]["txtIm13"] = "<b>Branch</b>: %s\n";

    // classe TInfo
    $this->translated["ENG_US"]["txtInfo01"] = "<b>Name</b>: %s\n";
    $this->translated["ENG_US"]["txtInfo02"] = "<b>Guild</b>: %s\n";
    $this->translated["ENG_US"]["txtInfo03"] = "<b>GP</b>: %s / %s \n";
    $this->translated["ENG_US"]["txtInfo04"] = "<b>GP Char.</b>: %s / %s \n";
    $this->translated["ENG_US"]["txtInfo05"] = "<b>GP Ships</b>: %s / %s \n";
    $this->translated["ENG_US"]["txtInfo06"] = "<b>----------- Championships -----------</b>\n";
    $this->translated["ENG_US"]["txtInfo07"] = "<b>Current Rank</b>: %s \n";
    $this->translated["ENG_US"]["txtInfo08"] = "<b>League</b>: %s \n";
    $this->translated["ENG_US"]["txtInfo09"] = "<b>Season Score</b>: %s \n";
    $this->translated["ENG_US"]["txtInfo10"] = "<b>Best Rank Achieved</b>: %s \n";
    $this->translated["ENG_US"]["txtInfo11"] = "<b>Lifetime Score</b>: %s \n";
    $this->translated["ENG_US"]["txtInfo12"] = "<b>Offensive Won</b>: %s \n";
    $this->translated["ENG_US"]["txtInfo13"] = "<b>Successful Defends</b>: %s \n";
    $this->translated["ENG_US"]["txtInfo14"] = "<b>Territories Defeated</b>: %s \n";
    $this->translated["ENG_US"]["txtInfo15"] = "<b>Banners Earned</b>: %s \n";
    $this->translated["ENG_US"]["txtInfo16"] = "<b>Promotions Earned</b>: %s \n";
    $this->translated["ENG_US"]["txtInfo17"] = "<b>----------- Characters -----------</b>\n";
    $this->translated["ENG_US"]["txtInfo18"] = "<b>7 stars</b>: %s \n";
    $this->translated["ENG_US"]["txtInfo19"] = "<b>Gear 13</b>: %s \n";
    $this->translated["ENG_US"]["txtInfo20"] = "<b>Average</b>: %s \n";
    $this->translated["ENG_US"]["txtInfo21"] = "<b>Gear 12</b>: %s \n";
    $this->translated["ENG_US"]["txtInfo22"] = "<b>Gear 11</b>: %s \n";
    $this->translated["ENG_US"]["txtInfo23"] = "<b>Gear 10</b>: %s \n";
    $this->translated["ENG_US"]["txtInfo24"] = "<b>Gear 9</b>: %s \n";
    $this->translated["ENG_US"]["txtInfo25"] = "<b>Gear 8</b>: %s \n";
    $this->translated["ENG_US"]["txtInfo27"] = "<b>Zetas</b>: %s \n";
    $this->translated["ENG_US"]["txtInfo28"] = "<b>Top 80</b>: %s \n";
    $this->translated["ENG_US"]["txtInfo29"] = "<b>----------- Ships -----------</b>\n";
    $this->translated["ENG_US"]["txtInfo30"] = "<b>7 stars</b>: %s \n";
    $this->translated["ENG_US"]["txtInfo31"] = "<b>----------- Relics -----------</b>\n";
    $this->translated["ENG_US"]["txtInfo32"] = "<b>Total</b>: %s \n";
    $this->translated["ENG_US"]["txtInfo33"] = "<b>Relic 7</b>: %s \n";
    $this->translated["ENG_US"]["txtInfo34"] = "<b>Relic 6</b>: %s \n";
    $this->translated["ENG_US"]["txtInfo35"] = "<b>Relic 5</b>: %s \n";
    $this->translated["ENG_US"]["txtInfo36"] = "<b>Relic 4</b>: %s \n";
    $this->translated["ENG_US"]["txtInfo37"] = "<b>Relic 3</b>: %s \n";
    $this->translated["ENG_US"]["txtInfo38"] = "<b>Relic 2</b>: %s \n";
    $this->translated["ENG_US"]["txtInfo39"] = "<b>Relic 1</b>: %s \n";
    $this->translated["ENG_US"]["txtInfo40"] = "<b>----------- Mods -----------</b>\n";
    $this->translated["ENG_US"]["txtInfo41"] = "<b>Mods 6</b>: %s \n";
    $this->translated["ENG_US"]["txtInfo42"] = "<b>Speed +10</b>: %s \n";
    $this->translated["ENG_US"]["txtInfo43"] = "<b>Speed +15</b>: %s \n";
    $this->translated["ENG_US"]["txtInfo44"] = "<b>Speed +20</b>: %s \n";
    $this->translated["ENG_US"]["txtInfo45"] = "<b>Speed +25</b>: %s \n";
    $this->translated["ENG_US"]["txtInfo46"] = "<b>----------- Arena -----------</b>\n";
    $this->translated["ENG_US"]["txtInfo47"] = "<b>Fleet</b>: %s \n";
    $this->translated["ENG_US"]["txtInfo48"] = "    <b>Capital</b>: %s \n";
    $this->translated["ENG_US"]["txtInfo49"] = "    <b>Slot 1</b>: %s \n";
    $this->translated["ENG_US"]["txtInfo50"] = "    <b>Slot 2</b>: %s \n";
    $this->translated["ENG_US"]["txtInfo51"] = "    <b>Slot 3</b>: %s \n";
    $this->translated["ENG_US"]["txtInfo52"] = "    <b>Rein. 1</b>: %s \n";
    $this->translated["ENG_US"]["txtInfo53"] = "    <b>Rein. 2</b>: %s \n";
    $this->translated["ENG_US"]["txtInfo54"] = "    <b>Rein. 3</b>: %s \n";
    $this->translated["ENG_US"]["txtInfo55"] = "    <b>Rein. 4</b>: %s \n";
    $this->translated["ENG_US"]["txtInfo56"] = "<b>Squad</b>: %s \n";
    $this->translated["ENG_US"]["txtInfo57"] = "    <b>Leader</b>: %s \n";
    $this->translated["ENG_US"]["txtInfo58"] = "    <b>Slot 2</b>: %s \n";
    $this->translated["ENG_US"]["txtInfo59"] = "    <b>Slot 3</b>: %s \n";
    $this->translated["ENG_US"]["txtInfo60"] = "    <b>Slot 4</b>: %s \n";
    $this->translated["ENG_US"]["txtInfo61"] = "    <b>Slot 5</b>: %s \n";
    $this->translated["ENG_US"]["txtInfo62"] = "<b>Units</b>\n";
    $this->translated["ENG_US"]["txtInfo63"] = "<b>Relic 8</b>: %s \n";

    // classe TRank
    $this->translated["ENG_US"]["txtRank01"] = "<b>Guild</b>: %s\n";
    $this->translated["ENG_US"]["txtRank02"] = "<b>Unit</b>: %s\n";
    $this->translated["ENG_US"]["txtRank03"] = "<b>Sort by</b>: %s\n";
    $this->translated["ENG_US"]["txtRank04"] = "<b>Average</b>: %s\n";

    // classe TSearch
    $this->translated["ENG_US"]["txtSearch01"] = "<b>Guild</b>: %s\n";
    $this->translated["ENG_US"]["txtSearch02"] = "<b>Searching</b>: %s\n";
    $this->translated["ENG_US"]["txtSearch03"] = "<b>Total</b>: %s\n";
    $this->translated["ENG_US"]["txtSearch04"] = "<b>---------- Stars ----------</b>\n";
    $this->translated["ENG_US"]["txtSearch05"] = "<b>----------- Gear ----------</b>\n";
    $this->translated["ENG_US"]["txtSearch06"] = "<b>---------- Zetas ----------</b>\n";
    $this->translated["ENG_US"]["txtSearch07"] = "<b>------- Don't have it -----</b>\n";

    // classe TZetas
    $this->translated["ENG_US"]["txtZetas01"] = "<b>Zetas for %s</b>: %s\n\n%s";

    // classe TTW
    $this->translated["ENG_US"]["twerr1"] = "Unit <i>%s</i> not found.\n\n";
    $this->translated["ENG_US"]["twerr2"] = "Ooooops! '%s' is not a valid date. Correct date format yyyymmdd and max date today.\n\n";
    $this->translated["ENG_US"]["txtTw01"] = "TW for %s has been initialized\n\n";
    $this->translated["ENG_US"]["txtTw02"] = "TW updated for %s\n\n";
    $this->translated["ENG_US"]["txtTw03"] = "  Player: %s\n";
    $this->translated["ENG_US"]["txtTw04"] = "  Offensive unit: %s\n";
    $this->translated["ENG_US"]["txtTw05"] = "  Defensive unit: %s\n";
    $this->translated["ENG_US"]["txtTw06"] = "  Type: %s\n";
    $this->translated["ENG_US"]["txtTw07"] = "<b>Guild</b>: %s\n";
    $this->translated["ENG_US"]["txtTw08"] = "<b>Unit</b>: %s\n";
    $this->translated["ENG_US"]["txtTw09"] = "<b>unitId</b>: %s\n";
    $this->translated["ENG_US"]["txtTw10"] = "<b>Unused</b>: %s\n%s\n";
    $this->translated["ENG_US"]["txtTw11"] = "<b>Used in offense</b>: %s (%s - %s%)\n<pre>%s</pre>\n";
    $this->translated["ENG_US"]["txtTw12"] = "<b>Used in defense</b>: %s\n<pre>%s</pre>\n";
    $this->translated["ENG_US"]["txtTw13"] = "<b>Rogues</b>: %s\n<pre>%s</pre>\n";
    $this->translated["ENG_US"]["txtTw14"] = "<b>Alias</b>: %s\n";
    $this->translated["ENG_US"]["txtTw15"] = "  Rogue decreased \n\n";
    $this->translated["ENG_US"]["txtTw16"] = "  Deleted Unit: %s \n\n";
    $this->translated["ENG_US"]["txtTw17"] = "TW for %s \n\n";
    $this->translated["ENG_US"]["txtTw18"] = "Units used by %s \n\n";
    $this->translated["ENG_US"]["txtTw19"] = "<b>Offense</b>: (%s/%s - %s%)\n<pre>";
    $this->translated["ENG_US"]["txtTw20"] = "<b>Defense</b>: \n<pre>";
    $this->translated["ENG_US"]["txtTw21"] = "<b>Rogues</b>: \n<pre>";
    $this->translated["ENG_US"]["txtTw22"] = "TW General Vision for %s \n\n";
    $this->translated["ENG_US"]["txtTw23"] = "<b>Defense</b>\n";
    $this->translated["ENG_US"]["txtTw24"] = "<i>Ships</i> (def: %s)\n";
    $this->translated["ENG_US"]["txtTw25"] = "<i>Characters</i> (def: %s)\n";
    $this->translated["ENG_US"]["txtTw26"] = "<b>Offense</b>\n";
    $this->translated["ENG_US"]["txtTw27"] = "<i>Ships</i> (battles: %s, points: %s)\n";
    $this->translated["ENG_US"]["txtTw28"] = "<i>Characters</i> (battles: %s, points: %s)\n";
    $this->translated["ENG_US"]["txtTw29"] = "TW Wrong Rogue List for %s \n\n";
    $this->translated["ENG_US"]["txtTw30"] = "TW review for %s \n\n";
    $this->translated["ENG_US"]["txtTw31"] = "\n<b>Versus %s</b>\n";
    $this->translated["ENG_US"]["txtTw32"] = "<b>No first attack</b>\n\n";
    $this->translated["ENG_US"]["txtTw33"] = "TW attacks for %s \n\n";
    $this->translated["ENG_US"]["txtTw34"] = "TW defenses for %s \n\n";
    $this->translated["ENG_US"]["txtTw35"] = "TW saved for %s \n\n";
    $this->translated["ENG_US"]["txtTw36"] = "TW deleted for %s \n\n";
    $this->translated["ENG_US"]["txtTw37"] = "TW history for %s \n\n";
    $this->translated["ENG_US"]["txtTw38"] = "Displayed date: %s \n\n";
    $this->translated["ENG_US"]["txtTw39"] = "Average of...\n";
    $this->translated["ENG_US"]["txtTw40"] = "  Used unit: %s\n";
    $this->translated["ENG_US"]["txtTw41"] = "<b>Used in others teams</b>: %s\n<pre>%s</pre>\n";
    $this->translated["ENG_US"]["txtTw42"] = "<b>Used in others teams</b>: \n<pre>";
    $this->translated["ENG_US"]["txtTw43"] = "<b>Used in others teams</b>\n";
    $this->translated["ENG_US"]["txtTw44"] = "<i>Ships</i>\n";
    $this->translated["ENG_US"]["txtTw45"] = "<i>Characters</i>\n";
    $this->translated["ENG_US"]["txtTw46"] = "\n<b>With %s</b>\n";
    $this->translated["ENG_US"]["txtTw47"] = "Average (%s TWs)";
    $this->translated["ENG_US"]["txtTw48"] = "There are some allycodes incorrects for %s:\n\n";
    $this->translated["ENG_US"]["txtTw49"] = "All allycodes are discarted!\n\n";
    $this->translated["ENG_US"]["txtTw50"] = "These allycodes have been added for %s:\n\n";
    $this->translated["ENG_US"]["txtTw51"] = "Allycodes non registered for %s:\n\n";
  
    // classe TTeams
    $this->translated["ENG_US"]["teamserr1"] = "Team %s not found for %s.\n\n";
    $this->translated["ENG_US"]["teamserr2"] = "Command already exists.\n\n";
    $this->translated["ENG_US"]["txtTeams01"] = "Team added for  %s \n\n";
    $this->translated["ENG_US"]["txtTeams02"] = "<b>Units</b> \n";
    $this->translated["ENG_US"]["txtTeams03"] = "Team %s from %s has been deleted.\n\n";
    $this->translated["ENG_US"]["txtTeams04"] = "<b>Defined teams</b> for %s \n\n";
    $this->translated["ENG_US"]["txtTeams05"] = "Command %s added to %s \n\n";
    $this->translated["ENG_US"]["txtTeams06"] = "Command %s deleted from %s \n\n";
    $this->translated["ENG_US"]["txtTeams07"] = "Team name: %s \n\n";
    $this->translated["ENG_US"]["txtTeams08"] = "Team: %s \n\n";
    $this->translated["ENG_US"]["txtTeams09"] = "Units: \n";
    $this->translated["ENG_US"]["txtTeams10"] = "  U%s: %s \n";

    // classe TGF
    $this->translated["ENG_US"]["gferr1"] = "No units to control.\n\n";
    $this->translated["ENG_US"]["txtGf01"] = "<b>Guild</b>: %s\n\n";
    $this->translated["ENG_US"]["txtGf02"] = "General Information \n\n";

    // classe THere
    $this->translated["ENG_US"]["hereerr1"] = "User '%s' already exists into '%s' list.\n\n";
    $this->translated["ENG_US"]["hereerr2"] = "The '%s' user is not registered in the bot.\n\n";
    $this->translated["ENG_US"]["hereerr3"] = "No users to control into list %s.\n\n";
    $this->translated["ENG_US"]["hereerr4"] = "Guild '%s' not found into łM guilds list.\n\n";
    $this->translated["ENG_US"]["txtHere1"] = "User '%s' added to the '%s' list.\n\n";
    $this->translated["ENG_US"]["txtHere2"] = "User '%s' has ben deleted from %s list.\n\n";
    $this->translated["ENG_US"]["txtHere3"] = "Users to tag for %s \n\n";
    $this->translated["ENG_US"]["txtHere4"] = "<b>Users for %s</b> \n";
    
    // class TPanic
    $this->translated["ENG_US"]["panicerr1"] = "Unit '%s' not defined into Panic list.\n\n";
    $this->translated["ENG_US"]["panicerr2"] = "No units found into your rooster.\n\n";
    $this->translated["ENG_US"]["panicerr3"] = "Incorrect prerequisite.\n\n";
    $this->translated["ENG_US"]["txtPanic1"] = "Panic unit added.\n";
    $this->translated["ENG_US"]["txtPanic2"] = "<b>Dependency units</b>\n";
    $this->translated["ENG_US"]["txtPanic3"] = "Panic unit '%s' was deleted.\n\n";
    $this->translated["ENG_US"]["txtPanic4"] = "<b>List of units</b>\n\n";
    $this->translated["ENG_US"]["txtPanic5"] = "level";
    $this->translated["ENG_US"]["txtPanic6"] = "gear";
    $this->translated["ENG_US"]["txtPanic7"] = "relics";
    $this->translated["ENG_US"]["txtPanic8"] = "galactic power";
    $this->translated["ENG_US"]["txtPanic9"] = "stars";
    
    // class TRancor
    $this->translated["ENG_US"]["txtRancor1"] = "Rancor for %s has been initialized\n\n";
    $this->translated["ENG_US"]["txtRancor2"] = "Rancor for %s \n\n";
    $this->translated["ENG_US"]["txtRancor3"] = "Total: %s (%s)\n\n";
    $this->translated["ENG_US"]["txtRancor4"] = "Rancor modified for %s with %s%\n\n";
    
    // class TStats
    $this->translated["ENG_US"]["statserr1"] = "Unit '%s' not defined into States list.\n\n";
    $this->translated["ENG_US"]["statserr2"] = "No units found into your rooster.\n\n";
    $this->translated["ENG_US"]["statserr3"] = "Incorrect requisite.\n\n";
    $this->translated["ENG_US"]["txtStats1"] = "Stat unit added.\n";
    $this->translated["ENG_US"]["txtStats2"] = "<b>Units to control</b>\n";
    $this->translated["ENG_US"]["txtStats3"] = "Stat unit '%s' was deleted.\n\n";
    $this->translated["ENG_US"]["txtStats4"] = "<b>List of units</b>\n\n";
    $this->translated["ENG_US"]["txtStats5"] = "speed";
    $this->translated["ENG_US"]["txtStats6"] = "health+protection";
    $this->translated["ENG_US"]["txtStats7"] = "health";
    $this->translated["ENG_US"]["txtStats8"] = "protection";
    $this->translated["ENG_US"]["txtStats9"] = "physical damage";
    $this->translated["ENG_US"]["txtStats10"] = "special damage";
    $this->translated["ENG_US"]["txtStats11"] = "potency";
    $this->translated["ENG_US"]["txtStats12"] = "tenacity";
    $this->translated["ENG_US"]["txtStats13"] = "armor";
    $this->translated["ENG_US"]["txtStats14"] = "physical avoidance";
    $this->translated["ENG_US"]["txtStats15"] = "special avoidance";
    $this->translated["ENG_US"]["txtStats16"] = "    + %s: %s\n";
    $this->translated["ENG_US"]["txtStats17"] = "physical c.chance";
    $this->translated["ENG_US"]["txtStats18"] = "critical damage";
    $this->translated["ENG_US"]["txtStats19"] = "special c.chance";

    $this->translated["ENG_US"]["txtStatg1"] = "Member with <b>%s</b> <b>%s</b> under <b>%s</b>:";

    $this->translated["ENG_US"]["txtTwCheck1"] = "[%s][%s] TW Check\n\n";
    $this->translated["ENG_US"]["txtTwCheckShow1"] = "[%s][%s] List of TW Check\n\n";

    
    
    // -------------------------------------------------------------------------
    // -------------------------------------------------------------------------
    // Texto en Español
    // -------------------------------------------------------------------------
    // -------------------------------------------------------------------------
    // class TRancor
    $this->translated["SPA_XM"]["txtRancor1"] = "Rancor para %s inicializada\n\n";
    $this->translated["SPA_XM"]["txtRancor2"] = "Rancor para %s fase %s \n\n";
    $this->translated["SPA_XM"]["txtRancor3"] = "Total: %s (%s)\n\n";
    $this->translated["SPA_XM"]["txtRancor4"] = "Rancor modificado para %s con %s%\n\n";

    // class TPanic 
    $this->translated["SPA_XM"]["panicerr1"] = "La unidad '%s' no está definida en la lista Panic.\n\n";
    $this->translated["SPA_XM"]["panicerr2"] = "No se han encontrado las unidades en tu inventario.\n\n";
    $this->translated["SPA_XM"]["panicerr3"] = "Prerequisito incorrecto.\n\n";
    $this->translated["SPA_XM"]["txtPanic1"] = "Unidad Panic añadida.\n";
    $this->translated["SPA_XM"]["txtPanic2"] = "<b>Unidades dependientes</b>\n";
    $this->translated["SPA_XM"]["txtPanic3"] = "La unidad Panic '%s' ha sido borrada.\n\n";
    $this->translated["SPA_XM"]["txtPanic4"] = "<b>Lista de unidades</b>\n\n";
    $this->translated["SPA_XM"]["txtPanic5"] = "nivel";
    $this->translated["SPA_XM"]["txtPanic6"] = "equipo";
    $this->translated["SPA_XM"]["txtPanic7"] = "relíquias";
    $this->translated["SPA_XM"]["txtPanic8"] = "poder galactico";
    $this->translated["SPA_XM"]["txtPanic9"] = "estrellas";

    // class TStats
    $this->translated["SPA_XM"]["statserr1"] = "La unidad '%s' no está definida en la lista Stats.\n\n";
    $this->translated["SPA_XM"]["statserr2"] = "No se han encontrado las unidades en tu inventario.\n\n";
    $this->translated["SPA_XM"]["statserr3"] = "Requisito incorrecto.\n\n";
    $this->translated["SPA_XM"]["txtStats1"] = "Unidad Stat añadida.\n";
    $this->translated["SPA_XM"]["txtStats2"] = "<b>Unidades a controlar</b>\n";
    $this->translated["SPA_XM"]["txtStats3"] = "La unidad Stat '%s' ha sido borrada.\n\n";
    $this->translated["SPA_XM"]["txtStats4"] = "<b>Lista de unidades</b>\n\n";
    $this->translated["SPA_XM"]["txtStats5"] = "velocidad";
    $this->translated["SPA_XM"]["txtStats6"] = "salud+protección";
    $this->translated["SPA_XM"]["txtStats7"] = "salud";
    $this->translated["SPA_XM"]["txtStats8"] = "protección";
    $this->translated["SPA_XM"]["txtStats9"] = "daño físico";
    $this->translated["SPA_XM"]["txtStats10"] = "daño especial";
    $this->translated["SPA_XM"]["txtStats11"] = "potencia";
    $this->translated["SPA_XM"]["txtStats12"] = "tenacidad";
    $this->translated["SPA_XM"]["txtStats13"] = "blindaje";
    $this->translated["SPA_XM"]["txtStats14"] = "evasión física";
    $this->translated["SPA_XM"]["txtStats15"] = "evasión especial";
    $this->translated["SPA_XM"]["txtStats16"] = "    + %s: %s\n";
    $this->translated["SPA_XM"]["txtStats17"] = "prob. crítico físico";
    $this->translated["SPA_XM"]["txtStats18"] = "daño crítico";
    $this->translated["SPA_XM"]["txtStats19"] = "prob. crítico especial";

    // classe THere
    $this->translated["SPA_XM"]["hereerr1"] = "El usuario '%s' ya existe en la lista '%s'.\n\n";
    $this->translated["SPA_XM"]["hereerr2"] = "El usuario '%s' no está registrado en el bot.\n\n";
    $this->translated["SPA_XM"]["hereerr3"] = "Sin usuarios a controlar en la lista %s.\n\n";
    $this->translated["SPA_XM"]["hereerr4"] = "Gremio '%s' no encontrado en la lista de gremios łM.\n\n";
    $this->translated["SPA_XM"]["txtHere1"] = "Se ha añadido el usuario '%s' en la lista '%s'.\n\n";
    $this->translated["SPA_XM"]["txtHere2"] = "El usuario '%s' ha sido borrado de la lista %s.\n\n";
    $this->translated["SPA_XM"]["txtHere3"] = "Usuarios a mencionar para %s \n\n";
    $this->translated["SPA_XM"]["txtHere4"] = "<b>Usuarios para %s</b> \n";
    
    // errors generals
    $this->translated["SPA_XM"]["error1"] = "Petición errónea. Lea la ayuda del comando: \n\n";
    $this->translated["SPA_XM"]["error2"] = "Unidad '%s' no encontrada en el inventario. \n\n";
    $this->translated["SPA_XM"]["error3"] = "%s no es una AllyCode válido.\n Lea la ayuda del comando: \n\n";
    $this->translated["SPA_XM"]["error4"] = "Ooooops! Ha ocurrido un error cogiendo la información.\n\n";
    $this->translated["SPA_XM"]["error5"] = "Comando incorrecto %s.\n\n";
    $this->translated["SPA_XM"]["error6"] = "Ooooops! Es posible que el servidor del API haya caído. Inténtalo más tarde.\n\n";

    // misstges generals
    $this->translated["SPA_XM"]["elapsed_time"] = "\n<i>Tiempo transcurrido: %s</i>\n";
    $this->translated["SPA_XM"]["last_update"] = "<i>Última actualización: %s</i>\n";

    // classe TAlias
    $this->translated["SPA_XM"]["aliaserr1"] = "El alias '%s' para '%s' ya existe en la unidad '%s'.\n\n";
    $this->translated["SPA_XM"]["txtAlias1"] = "<b>Lista de alias</b>\n\n";
    $this->translated["SPA_XM"]["txtAlias2"] = "Alias añadido\n\n";
    $this->translated["SPA_XM"]["txtAlias3"] = "   <b>Unidad</b>: %s\n";
    $this->translated["SPA_XM"]["txtAlias4"] = "   <b>Alias</b>: %s\n";
    $this->translated["SPA_XM"]["txtAlias5"] = "Se ha borrado el alias '%s'.\n\n";

    // clasee TUnits
    $this->translated["SPA_XM"]["unitserr1"] = "La unidad %s ya existe.\n\n"; 
    $this->translated["SPA_XM"]["unitserr2"] = "Sin unidades a controlar.\n\n";
    $this->translated["SPA_XM"]["txtUnits1"] = "<b>Lista de unidades</b>\n";
    $this->translated["SPA_XM"]["txtUnits2"] = "Las unidades han sido actualizadas.\n\n";
    $this->translated["SPA_XM"]["txtUnits3"] = "Unidad %s añadida a la lista de control de unidades.\n\n";
    $this->translated["SPA_XM"]["txtUnits4"] = "Se ha borrado la unidad %s \n\n";
    $this->translated["SPA_XM"]["txtUnits5"] = "Unidades a controlar para %s \n\n";
    $this->translated["SPA_XM"]["txtUnits6"] = "Comandos \n\n";
    $this->translated["SPA_XM"]["txtUnits7"] = "Se han borrado todas las unidades \n\n";

    // classe TChampions
    $this->translated["SPA_XM"]["txtCham01"] = "<b>----- %s vs %s -----</b>\n";
    $this->translated["SPA_XM"]["txtCham02"] = "<b>PG</b>: %s vs %s\n";
    $this->translated["SPA_XM"]["txtCham03"] = "<b>PG Pjs.</b>: %s vs %s\n";
    $this->translated["SPA_XM"]["txtCham04"] = "<b>PG Naves</b>: %s vs %s\n";
    $this->translated["SPA_XM"]["txtCham05"] = "<b>----------- Arena -----------</b>\n";
    $this->translated["SPA_XM"]["txtCham06"] = "<b>Escuadrones</b>: %s vs %s\n";
    $this->translated["SPA_XM"]["txtCham07"] = "<b>Naves</b>: %s vs %s\n";
    $this->translated["SPA_XM"]["txtCham08"] = "<b>----------- Campeonatos -----------</b>\n";
    $this->translated["SPA_XM"]["txtCham09"] = "<b>Rango Actual</b>: %s vs %s\n";
    $this->translated["SPA_XM"]["txtCham10"] = "<b>División</b>: %s vs %s\n";
    $this->translated["SPA_XM"]["txtCham11"] = "<b>Mejor Puntuacion Obtenida</b>: %s vs %s\n";
    $this->translated["SPA_XM"]["txtCham12"] = "<b>Puntuacion total</b>: %s vs %s\n";
    $this->translated["SPA_XM"]["txtCham13"] = "<b>Batallas ganadas</b>: %s vs %s\n";
    $this->translated["SPA_XM"]["txtCham14"] = "<b>Defensas exitosas</b>: %s vs %s\n";
    $this->translated["SPA_XM"]["txtCham15"] = "<b>Territorios derrotados</b>:%s vs %s\n";
    $this->translated["SPA_XM"]["txtCham16"] = "<b>Estandartes conseguidos</b>: %s vs %s\n";
    $this->translated["SPA_XM"]["txtCham17"] = "<b>Ascensos conseguidos</b>: %s vs %s\n";
    $this->translated["SPA_XM"]["txtCham18"] = "<b>----------- Equipo -----------</b>\n";
    $this->translated["SPA_XM"]["txtCham19"] = "<b>7 estr</b>: %s vs %s\n";
    $this->translated["SPA_XM"]["txtCham20"] = "<b>Equipo 13</b>: %s vs %s\n";
    $this->translated["SPA_XM"]["txtCham21"] = "<b>Equipo 12</b>: %s vs %s\n";
    $this->translated["SPA_XM"]["txtCham22"] = "<b>Equipo 12+1</b>: %s vs %s\n";
    $this->translated["SPA_XM"]["txtCham23"] = "<b>Equipo 12+2</b>: %s vs %s\n";
    $this->translated["SPA_XM"]["txtCham24"] = "<b>Equipo 12+3</b>: %s vs %s\n";
    $this->translated["SPA_XM"]["txtCham25"] = "<b>Equipo 12+4</b>: %s vs %s\n";
    $this->translated["SPA_XM"]["txtCham26"] = "<b>Equipo 12+5</b>: %s vs %s\n";
    $this->translated["SPA_XM"]["txtCham27"] = "<b>Equipo 11</b>: %s vs %s\n";
    $this->translated["SPA_XM"]["txtCham28"] = "<b>Zetas</b>: %s vs %s\n";
    $this->translated["SPA_XM"]["txtCham29"] = "<b>----------- Reliquias -----------</b>\n";
    $this->translated["SPA_XM"]["txtCham30"] = "<b>Total</b>: %s vs %s\n";
    $this->translated["SPA_XM"]["txtCham31"] = "<b>Reliquia 7</b>: %s vs %s\n";
    $this->translated["SPA_XM"]["txtCham32"] = "<b>Reliquia 6</b>: %s vs %s\n";
    $this->translated["SPA_XM"]["txtCham33"] = "<b>Reliquia 5</b>: %s vs %s\n";
    $this->translated["SPA_XM"]["txtCham34"] = "<b>Reliquia 4</b>: %s vs %s\n";
    $this->translated["SPA_XM"]["txtCham35"] = "<b>Reliquia 3</b>: %s vs %s\n";
    $this->translated["SPA_XM"]["txtCham36"] = "<b>Reliquia 2</b>: %s vs %s\n";
    $this->translated["SPA_XM"]["txtCham37"] = "<b>Reliquia 1</b>: %s vs %s\n";
    $this->translated["SPA_XM"]["txtCham38"] = "<b>----------- Mods -----------</b>\n";
    $this->translated["SPA_XM"]["txtCham39"] = "<b>Mods 6</b>: %s vs %s\n";
    $this->translated["SPA_XM"]["txtCham40"] = "<b>Velocidad +10</b>: %s vs %s\n";
    $this->translated["SPA_XM"]["txtCham41"] = "<b>Velocidad +15</b>: %s vs %s\n";
    $this->translated["SPA_XM"]["txtCham42"] = "<b>Velocidad +20</b>: %s vs %s\n";
    $this->translated["SPA_XM"]["txtCham43"] = "<b>Velocidad +25</b>: %s vs %s\n";
    $this->translated["SPA_XM"]["txtCham44"] = "Mayor Pg: 2 puntos ... <b>empate</b>\n\n";
    $this->translated["SPA_XM"]["txtCham45"] = "Mayor Pg: 2 puntos ... %s";
    $this->translated["SPA_XM"]["txtCham46"] = "Mayor pg pjs: 1 punto... <b>empate</b> %s\n\n";
    $this->translated["SPA_XM"]["txtCham47"] = "Mayor pg pjs: 1 punto... %s\n\n";
    $this->translated["SPA_XM"]["txtCham48"] = "Mayor pg naves: 1 punto... <b>empate</b> %s\n\n";
    $this->translated["SPA_XM"]["txtCham49"] = "Mayor pg naves: 1 punto... %s\n\n";
    $this->translated["SPA_XM"]["txtCham50"] = "Más batallas ganadas GA: 2 puntos... <b>empate</b> %s\n\n";
    $this->translated["SPA_XM"]["txtCham51"] = "Más batallas ganadas GA: 2 puntos... %s\n\n";
    $this->translated["SPA_XM"]["txtCham52"] = "Más defensas exitosas GA: 2 puntos... <b>empate</b> %s\n\n";
    $this->translated["SPA_XM"]["txtCham53"] = "Más defensas exitosas GA: 2 puntos... %s\n\n";
    $this->translated["SPA_XM"]["txtCham54"] = "Más territorios derrotados: 1 punto... <b>empate</b> %s\n\n";
    $this->translated["SPA_XM"]["txtCham55"] = "Más territorios derrotados: 1 punto... %s\n\n";
    $this->translated["SPA_XM"]["txtCham56"] = "Más estandartes conseguidos: 1 punto... <b>empate</b> %s\n\n";
    $this->translated["SPA_XM"]["txtCham57"] = "Más estandartes conseguidos: 1 punto... %s\n\n";
    $this->translated["SPA_XM"]["txtCham58"] = "Más ascensos conseguidos: 1 punto... <b>empate</b> %s\n\n";
    $this->translated["SPA_XM"]["txtCham59"] = "Más ascensos conseguidos: 1 punto... %s\n\n";
    $this->translated["SPA_XM"]["txtCham60"] = "Más pjs 7*: 2 puntos... <b>empate</b> %s\n\n";
    $this->translated["SPA_XM"]["txtCham61"] = "Más pjs 7*: 2 puntos... %s\n\n";
    $this->translated["SPA_XM"]["txtCham62"] = "Más pjs g13: 3 puntos... <b>empate</b> %s\n\n";
    $this->translated["SPA_XM"]["txtCham63"] = "Más pjs g13: 3 puntos... %s\n\n";
    $this->translated["SPA_XM"]["txtCham64"] = "Más pjs g12: 2 puntos... <b>empate</b> %s\n\n";
    $this->translated["SPA_XM"]["txtCham65"] = "Más pjs g12: 2 puntos... %s\n\n";
    $this->translated["SPA_XM"]["txtCham66"] = "Más pjs g11: 1 punto... <b>empate</b> %s\n\n";
    $this->translated["SPA_XM"]["txtCham67"] = "Más pjs g11: 1 punto... %s\n\n";
    $this->translated["SPA_XM"]["txtCham68"] = "Más zetas: 3 puntos... <b>empate</b> %s\n\n";
    $this->translated["SPA_XM"]["txtCham69"] = "Más zetas: 3 puntos... %s\n\n";
    $this->translated["SPA_XM"]["txtCham70"] = "Mayor número de reliquias: 3 puntos... <b>empate</b> %s\n\n";
    $this->translated["SPA_XM"]["txtCham71"] = "Mayor número de reliquias: 3 puntos... %s\n\n";
    $this->translated["SPA_XM"]["txtCham72"] = "Más r7: 3 puntos... <b>empate</b> %s\n\n";
    $this->translated["SPA_XM"]["txtCham73"] = "Más r7: 3 puntos... %s\n\n";
    $this->translated["SPA_XM"]["txtCham74"] = "Más mods 6*: 2 puntos... <b>empate</b> %s\n\n";
    $this->translated["SPA_XM"]["txtCham75"] = "Más mods 6*: 2 puntos... %s\n\n";
    $this->translated["SPA_XM"]["txtCham76"] = "Más mods +10: 1 punto... <b>empate</b> %s\n\n";
    $this->translated["SPA_XM"]["txtCham77"] = "Más mods +10: 1 punto... %s\n\n";
    $this->translated["SPA_XM"]["txtCham78"] = "Más mods +15: 2 puntos... <b>empate</b> %s\n\n";
    $this->translated["SPA_XM"]["txtCham79"] = "Más mods +15: 2 puntos... %s\n\n";
    $this->translated["SPA_XM"]["txtCham80"] = "Más mods +20: 3 puntos... <b>empate</b> %s\n\n";
    $this->translated["SPA_XM"]["txtCham81"] = "Más mods +20: 3 puntos... %s\n\n";
    $this->translated["SPA_XM"]["txtCham82"] = "Más mods +25: 4 puntos... <b>empate</b> %s\n\n";
    $this->translated["SPA_XM"]["txtCham83"] = "Más mods +25: 4 puntos... %s\n\n";
    $this->translated["SPA_XM"]["txtCham84"] = "*Máximo de puntos: 40* \n";
    $this->translated["SPA_XM"]["txtCham85"] = "%s: %s puntos \n";
    $this->translated["SPA_XM"]["txtCham86"] = "<b>Top 80</b>: %s vs %s\n";
    $this->translated["SPA_XM"]["txtCham87"] = "<b>Unidades</b>\n";
    $this->translated["SPA_XM"]["txtCham88"] = "<b>Reliquia 8</b>: %s vs %s\n";
    $this->translated["SPA_XM"]["txtCham89"] = "<b>-------GA OMICRONS-------</b>\n\n";
    $this->translated["SPA_XM"]["txtCham90"] = "<b>QGJ</b>: %s vs %s\n";
    $this->translated["SPA_XM"]["txtCham91"] = "<b>DashRendar</b>: %s vs %s\n";
    $this->translated["SPA_XM"]["txtCham92"] = "<b>Zam</b>: %s vs %s\n";
    $this->translated["SPA_XM"]["txtCham93"] = "<b>Rose</b>: %s vs %s\n";
    $this->translated["SPA_XM"]["txtCham94"] = "<b>Talon</b>: %s vs %s\n";
    $this->translated["SPA_XM"]["txtCham95"] = "<b>Chirpa</b>: %s vs %s\n";
    $this->translated["SPA_XM"]["txtCham96"] = "<b>Wampa</b>: %s vs %s\n";
    $this->translated["SPA_XM"]["txtCham97"] = "<b>Iden Versio</b>: %s vs %s\n";
    $this->translated["SPA_XM"]["txtCham98"] = "<b>Ackbar</b>: %s vs %s\n";
    $this->translated["SPA_XM"]["txtCham99"] = "<b>Leia</b>: %s vs %s\n";
    $this->translated["SPA_XM"]["txtCham100"] = "<b>Krennic</b>: %s vs %s\n";
    $this->translated["SPA_XM"]["txtCham101"] = "<b>Starkiller (especial 1)</b>: %s vs %s\n";
    $this->translated["SPA_XM"]["txtCham102"] = "<b>Starkiller (especial 2)</b>: %s vs %s\n";
    $this->translated["SPA_XM"]["txtCham103"] = "<b>Starkiller (única)</b>: %s vs %s\n";
    $this->translated["SPA_XM"]["txtCham104"] = "<b>Reliquia 9</b>: %s vs %s\n";

    // classe TCompareg
    $this->translated["SPA_XM"]["txtCompareg01"] = "<b>-----------SUMARIO-----------</b>\n";
    $this->translated["SPA_XM"]["txtCompareg02"] = "<b>>Miembros</b>: %s vs %s\n";
    $this->translated["SPA_XM"]["txtCompareg03"] = "<b>PG</b>: %s vs %s\n";
    $this->translated["SPA_XM"]["txtCompareg04"] = "<b>PG (sum jugadores)</b>: %s vs %s\n";
    $this->translated["SPA_XM"]["txtCompareg05"] = "<b>PG (personajes)</b>: %s vs %s\n";
    $this->translated["SPA_XM"]["txtCompareg06"] = "<b>PG (naves)</b>: %s vs %s\n";
    $this->translated["SPA_XM"]["txtCompareg07"] = "<b>PG top 80</b>: %s vs %s\n";
    $this->translated["SPA_XM"]["txtCompareg08"] = "<b>Media Arena</b>: %s vs %s\n";
    $this->translated["SPA_XM"]["txtCompareg09"] = "<b>Media Naves</b>: %s vs %s\n";
    $this->translated["SPA_XM"]["txtCompareg10"] = "<b>Zetas</b>: %s vs %s\n";
    $this->translated["SPA_XM"]["txtCompareg11"] = "<b>Equipo 13</b>: %s vs %s\n";
    $this->translated["SPA_XM"]["txtCompareg12"] = "<b>Equipo 12</b>: %s vs %s\n";
    $this->translated["SPA_XM"]["txtCompareg13"] = "<b>Equipo 12+1</b>: %s vs %s\n";
    $this->translated["SPA_XM"]["txtCompareg14"] = "<b>Equipo 12+2</b>: %s vs %s\n";
    $this->translated["SPA_XM"]["txtCompareg15"] = "<b>Equipo 12+3</b>: %s vs %s\n";
    $this->translated["SPA_XM"]["txtCompareg16"] = "<b>Equipo 12+4</b>: %s vs %s\n";
    $this->translated["SPA_XM"]["txtCompareg17"] = "<b>Equipo 12+5</b>: %s vs %s\n";
    $this->translated["SPA_XM"]["txtCompareg18"] = "<b>-----------RELIQUIAS-----------</b>\n";
    $this->translated["SPA_XM"]["txtCompareg19"] = "<b>Reliquias</b>: %s vs %s\n";
    $this->translated["SPA_XM"]["txtCompareg20"] = "<b>Nivel 7</b>: %s vs %s\n";
    $this->translated["SPA_XM"]["txtCompareg21"] = "<b>Nivel 6</b>: %s vs %s\n";
    $this->translated["SPA_XM"]["txtCompareg22"] = "<b>Nivel 5</b>: %s vs %s\n";
    $this->translated["SPA_XM"]["txtCompareg23"] = "<b>Nivel 4</b>: %s vs %s\n";
    $this->translated["SPA_XM"]["txtCompareg24"] = "<b>Nivel 3</b>: %s vs %s\n";
    $this->translated["SPA_XM"]["txtCompareg25"] = "<b>Nivel 2</b>: %s vs %s\n";
    $this->translated["SPA_XM"]["txtCompareg26"] = "<b>Nivel 1</b>: %s vs %s\n";
    $this->translated["SPA_XM"]["txtCompareg27"] = "<b>-----------MODS-----------</b>\n";
    $this->translated["SPA_XM"]["txtCompareg28"] = "<b>6*</b>: %s vs %s\n";
    $this->translated["SPA_XM"]["txtCompareg29"] = "<b>25+</b>: %s vs %s\n";
    $this->translated["SPA_XM"]["txtCompareg30"] = "<b>20+</b>: %s vs %s\n";
    $this->translated["SPA_XM"]["txtCompareg31"] = "<b>15+</b>: %s vs %s\n";
    $this->translated["SPA_XM"]["txtCompareg32"] = "<b>10+</b>: %s vs %s\n";
    $this->translated["SPA_XM"]["txtCompareg33"] = "<b>Unidades</b>\n";
    $this->translated["SPA_XM"]["txtCompareg34"] = "<b>Nivel 8</b>: %s vs %s\n";
    $this->translated["SPA_XM"]["txtCompareg35"] = "<b>Nivel 9</b>: %s vs %s\n";
    $this->translated["SPA_XM"]["txtCompareg36"] = "<b>-------TW OMICRONS-------</b>\n";
    $this->translated["SPA_XM"]["txtCompareg37"] = "<b>Phasma</b>: %s vs %s\n";
    $this->translated["SPA_XM"]["txtCompareg38"] = "<b>Nebit</b>: %s vs %s\n";
      $this->translated["SPA_XM"]["txtCompareg39"] = "<b>Mara Jade</b>: %s vs %s\n";
      $this->translated["SPA_XM"]["txtCompareg40"] = "<b>Sidious</b>: %s vs %s\n";
      $this->translated["SPA_XM"]["txtCompareg41"] = "<b>Hera</b>: %s vs %s\n";
      $this->translated["SPA_XM"]["txtCompareg42"] = "<b>SoJ (especial)</b>: %s vs %s\n";
      $this->translated["SPA_XM"]["txtCompareg43"] = "<b>SoJ (líder)</b>: %s vs %s\n";
      $this->translated["SPA_XM"]["txtCompareg44"] = "<b>SoJ (única)</b>: %s vs %s\n";
      $this->translated["SPA_XM"]["txtCompareg45"] = "<b>Windu</b>: %s vs %s\n";
      $this->translated["SPA_XM"]["txtCompareg46"] = "<b>Embo</b>: %s vs %s\n";
      $this->translated["SPA_XM"]["txtCompareg47"] = "<b>Segunda Hermana</b>: %s vs %s\n";
      $this->translated["SPA_XM"]["txtCompareg48"] = "<b>T3-M4</b>: %s vs %s\n";
      $this->translated["SPA_XM"]["txtCompareg49"] = "<b>Novena Hermana</b>: %s vs %s\n";
      $this->translated["SPA_XM"]["txtCompareg50"] = "<b>Octavo Hermano</b>: %s vs %s\n";
      $this->translated["SPA_XM"]["txtCompareg51"] = "<b>Séptima Hermana</b>: %s vs %s\n";

    // classe TGuild
    $this->translated["SPA_XM"]["txtGuild01"] = "<b>Gremio</b>: %s\n";
    $this->translated["SPA_XM"]["txtGuild02"] = "<b>Miembros</b>: %s\n";
    $this->translated["SPA_XM"]["txtGuild03"] = "<b>Mensaje Interno</b>: %s\n";
    $this->translated["SPA_XM"]["txtGuild04"] = "<b>PG del Juego</b>: %s\n";
    $this->translated["SPA_XM"]["txtGuild05"] = "<b>PG Calculado</b>: %s\n";
    $this->translated["SPA_XM"]["txtGuild06"] = "<b>PG Personajes</b>: %s\n";
    $this->translated["SPA_XM"]["txtGuild07"] = "<b>PG de Naves</b>: %s\n";
    $this->translated["SPA_XM"]["txtGuild08"] = "<b>PG Medio</b>: %s\n";
    $this->translated["SPA_XM"]["txtGuild09"] = "<b>Media Arena</b>: %s\n";
    $this->translated["SPA_XM"]["txtGuild10"] = "<b>Media Naves</b>: %s\n";
    $this->translated["SPA_XM"]["txtGuild11"] = "<b>PG</b>: %s\n";
    $this->translated["SPA_XM"]["txtGuild12"] = "<b>Media</b>: %s\n";
    $this->translated["SPA_XM"]["txtGuild13"] = "<b>Nivel 13</b>: %s\n";
    $this->translated["SPA_XM"]["txtGuild14"] = "<b>Nivel 12</b>: %s\n";
    $this->translated["SPA_XM"]["txtGuild15"] = "<b>Nivel 11</b>: %s\n";
    $this->translated["SPA_XM"]["txtGuild16"] = "<b>Media</b>: %s\n";
    $this->translated["SPA_XM"]["txtGuild17"] = "<b>Top80</b>: %s\n";
    $this->translated["SPA_XM"]["txtGuild18"] = "<b>PG Naves</b>: %s\n";
    $this->translated["SPA_XM"]["txtGuild19"] = "<b>PG Perso.</b>: %s\n";
    $this->translated["SPA_XM"]["txtGuild20"] = "<b>Mods 6</b>: %s\n";
    $this->translated["SPA_XM"]["txtGuild21"] = "<b>Mods 25</b>: %s\n";
    $this->translated["SPA_XM"]["txtGuild22"] = "<b>Mods 20</b>: %s\n";
    $this->translated["SPA_XM"]["txtGuild23"] = "<b>Mods 15</b>: %s\n";
    $this->translated["SPA_XM"]["txtGuild24"] = "<b>Reliquia 8</b>: %s\n";
    $this->translated["SPA_XM"]["txtGuild25"] = "<b>Reliquia 7</b>: %s\n";
    $this->translated["SPA_XM"]["txtGuild26"] = "<b>Miembros no Registrados</b>\n";
    $this->translated["SPA_XM"]["txtGuild27"] = "<b>Miembros Registrados</b>\n";

    // classe TIm
    $this->translated["SPA_XM"]["txtIm01"] = "<b>Grupo łmperio Mandaloriano</b>\n";
    $this->translated["SPA_XM"]["txtIm02"] = "<b>Líder</b>: %s\n";
    $this->translated["SPA_XM"]["txtIm03"] = "<b>PG</b>: %s\n";
    $this->translated["SPA_XM"]["txtIm04"] = "<b>Jugadores</b>: %s\n";
    $this->translated["SPA_XM"]["txtIm05"] = "<b>url</b>: %s\n";
    $this->translated["SPA_XM"]["txtIm06"] = "Más info:\n";
    $this->translated["SPA_XM"]["txtIm07"] = "<b>Gremio añadido a IM</b>\n";
    $this->translated["SPA_XM"]["txtIm08"] = "<b>Nombre del Gremio</b>: %s\n";
    $this->translated["SPA_XM"]["txtIm09"] = "<b>Acrónimo</b>: %s\n";
    $this->translated["SPA_XM"]["txtIm10"] = "<b>Líder</b>: %s\n";
    $this->translated["SPA_XM"]["txtIm11"] = "<b>Gremio borrado de IM</b>\n";
    $this->translated["SPA_XM"]["txtIm12"] = "<b>AllyCode</b>: %s\n";
    $this->translated["SPA_XM"]["txtIm13"] = "<b>Rama</b>: %s\n";

    // classe TInfo
    $this->translated["SPA_XM"]["txtInfo01"] = "<b>Nombre</b>: %s\n";
    $this->translated["SPA_XM"]["txtInfo02"] = "<b>Gremio</b>: %s\n";
    $this->translated["SPA_XM"]["txtInfo03"] = "<b>PG</b>: %s / %s \n";
    $this->translated["SPA_XM"]["txtInfo04"] = "<b>PG Psj.</b>: %s / %s \n";
    $this->translated["SPA_XM"]["txtInfo05"] = "<b>PG Naves</b>: %s / %s \n";
    $this->translated["SPA_XM"]["txtInfo06"] = "<b>----------- Gran Arena -----------</b>\n";
    $this->translated["SPA_XM"]["txtInfo07"] = "<b>Rango Actual</b>: %s \n";
    $this->translated["SPA_XM"]["txtInfo08"] = "<b>División</b>: %s \n";
    $this->translated["SPA_XM"]["txtInfo09"] = "<b>Puntuación Temporada</b>: %s \n";
    $this->translated["SPA_XM"]["txtInfo10"] = "<b>Mejor Puntuacion Obtenida</b>: %s \n";
    $this->translated["SPA_XM"]["txtInfo11"] = "<b>Puntuacion Total</b>: %s \n";
    $this->translated["SPA_XM"]["txtInfo12"] = "<b>Batallas Ganadas</b>: %s \n";
    $this->translated["SPA_XM"]["txtInfo13"] = "<b>Defensas Exitosas</b>: %s \n";
    $this->translated["SPA_XM"]["txtInfo14"] = "<b>Territorios Derrotados</b>: %s \n";
    $this->translated["SPA_XM"]["txtInfo15"] = "<b>Estandartes Conseguidos</b>: %s \n";
    $this->translated["SPA_XM"]["txtInfo16"] = "<b>Ascensos Conseguidos</b>: %s \n";
    $this->translated["SPA_XM"]["txtInfo17"] = "<b>----------- Personajes -----------</b>\n";
    $this->translated["SPA_XM"]["txtInfo18"] = "<b>7 estrellas</b>: %s \n";
    $this->translated["SPA_XM"]["txtInfo19"] = "<b>Nivel 13</b>: %s \n";
    $this->translated["SPA_XM"]["txtInfo20"] = "<b>Media</b>: %s \n";
    $this->translated["SPA_XM"]["txtInfo21"] = "<b>Nivel 12</b>: %s \n";
    $this->translated["SPA_XM"]["txtInfo22"] = "<b>Nivel 11</b>: %s \n";
    $this->translated["SPA_XM"]["txtInfo23"] = "<b>Nivel 10</b>: %s \n";
    $this->translated["SPA_XM"]["txtInfo24"] = "<b>Nivel 9</b>: %s \n";
    $this->translated["SPA_XM"]["txtInfo25"] = "<b>Nivel 8</b>: %s \n";
    $this->translated["SPA_XM"]["txtInfo27"] = "<b>Zetas</b>: %s \n";
    $this->translated["SPA_XM"]["txtInfo28"] = "<b>Top 80</b>: %s \n";
    $this->translated["SPA_XM"]["txtInfo29"] = "<b>----------- Naves -----------</b>\n";
    $this->translated["SPA_XM"]["txtInfo30"] = "<b>7 estrellas</b>: %s \n";
    $this->translated["SPA_XM"]["txtInfo31"] = "<b>----------- Reliquias -----------</b>\n";
    $this->translated["SPA_XM"]["txtInfo32"] = "<b>Total</b>: %s \n";
    $this->translated["SPA_XM"]["txtInfo33"] = "<b>Reliquia 7</b>: %s \n";
    $this->translated["SPA_XM"]["txtInfo34"] = "<b>Reliquia 6</b>: %s \n";
    $this->translated["SPA_XM"]["txtInfo35"] = "<b>Reliquia 5</b>: %s \n";
    $this->translated["SPA_XM"]["txtInfo36"] = "<b>Reliquia 4</b>: %s \n";
    $this->translated["SPA_XM"]["txtInfo37"] = "<b>Reliquia 3</b>: %s \n";
    $this->translated["SPA_XM"]["txtInfo38"] = "<b>Reliquia 2</b>: %s \n";
    $this->translated["SPA_XM"]["txtInfo39"] = "<b>Reliquia 1</b>: %s \n";
    $this->translated["SPA_XM"]["txtInfo40"] = "<b>----------- Mods -----------</b>\n";
    $this->translated["SPA_XM"]["txtInfo41"] = "<b>Mods 6</b>: %s \n";
    $this->translated["SPA_XM"]["txtInfo42"] = "<b>Vel. +10</b>: %s \n";
    $this->translated["SPA_XM"]["txtInfo43"] = "<b>Vel. +15</b>: %s \n";
    $this->translated["SPA_XM"]["txtInfo44"] = "<b>Vel. +20</b>: %s \n";
    $this->translated["SPA_XM"]["txtInfo45"] = "<b>Vel. +25</b>: %s \n";
    $this->translated["SPA_XM"]["txtInfo46"] = "<b>----------- Arena -----------</b>\n";
    $this->translated["SPA_XM"]["txtInfo47"] = "<b>Naves</b>: %s \n";
    $this->translated["SPA_XM"]["txtInfo48"] = "    <b>Capital</b>: %s \n";
    $this->translated["SPA_XM"]["txtInfo49"] = "    <b>Espacio 1</b>: %s \n";
    $this->translated["SPA_XM"]["txtInfo50"] = "    <b>Espacio 2</b>: %s \n";
    $this->translated["SPA_XM"]["txtInfo51"] = "    <b>Espacio 3</b>: %s \n";
    $this->translated["SPA_XM"]["txtInfo52"] = "    <b>Ref. 1</b>: %s \n";
    $this->translated["SPA_XM"]["txtInfo53"] = "    <b>Ref. 2</b>: %s \n";
    $this->translated["SPA_XM"]["txtInfo54"] = "    <b>Ref. 3</b>: %s \n";
    $this->translated["SPA_XM"]["txtInfo55"] = "    <b>Ref. 4</b>: %s \n";
    $this->translated["SPA_XM"]["txtInfo56"] = "<b>Escuadrón</b>: %s \n";
    $this->translated["SPA_XM"]["txtInfo57"] = "    <b>Líder</b>: %s \n";
    $this->translated["SPA_XM"]["txtInfo58"] = "    <b>Espacio 2</b>: %s \n";
    $this->translated["SPA_XM"]["txtInfo59"] = "    <b>Espacio 3</b>: %s \n";
    $this->translated["SPA_XM"]["txtInfo60"] = "    <b>Espacio 4</b>: %s \n";
    $this->translated["SPA_XM"]["txtInfo61"] = "    <b>Espacio 5</b>: %s \n";
    $this->translated["SPA_XM"]["txtInfo62"] = "<b>Unidades</b>\n";
    $this->translated["SPA_XM"]["txtInfo63"] = "<b>Reliquia 8</b>: %s \n";

    // classe TRank
    $this->translated["SPA_XM"]["txtRank01"] = "<b>Gremio</b>: %s\n";
    $this->translated["SPA_XM"]["txtRank02"] = "<b>Unidad</b>: %s\n";
    $this->translated["SPA_XM"]["txtRank03"] = "<b>Ordenado por</b>: %s\n";
    $this->translated["SPA_XM"]["txtRank04"] = "<b>Media</b>: %s\n";

    // classe TSearch
    $this->translated["SPA_XM"]["txtSearch01"] = "<b>Gremio</b>: %s\n";
    $this->translated["SPA_XM"]["txtSearch02"] = "<b>Buscando</b>: %s\n";
    $this->translated["SPA_XM"]["txtSearch03"] = "<b>Total</b>: %s\n";
    $this->translated["SPA_XM"]["txtSearch04"] = "<b>-------- Estrellas --------</b>\n";
    $this->translated["SPA_XM"]["txtSearch05"] = "<b>---------- Nivel ----------</b>\n";
    $this->translated["SPA_XM"]["txtSearch06"] = "<b>---------- Zetas ----------</b>\n";
    $this->translated["SPA_XM"]["txtSearch07"] = "<b>------- No lo tienen ------</b>\n";

    // classe TZetas
    $this->translated["SPA_XM"]["txtZetas01"] = "<b>Zetas para %s</b>: %s\n\n%s";

    // classe TTW
    $this->translated["SPA_XM"]["twerr1"] = "Unidad <i>%s</i> no encontrada.\n\n";
    $this->translated["SPA_XM"]["twerr2"] = "Ooooops! '%s' no es una fecha válida. El formato de fecha correcto es yyyymmdd y el día máximo hoy.\n\n";
    $this->translated["SPA_XM"]["txtTw01"] = "GT para %s inicializada\n\n";
    $this->translated["SPA_XM"]["txtTw02"] = "GT modificada para %s\n\n";
    $this->translated["SPA_XM"]["txtTw03"] = "  Jugador: %s\n";
    $this->translated["SPA_XM"]["txtTw04"] = "  Unidad atacante: %s\n";
    $this->translated["SPA_XM"]["txtTw05"] = "  Unidad defensiva: %s\n";
    $this->translated["SPA_XM"]["txtTw06"] = "  Tipo: %s\n";
    $this->translated["SPA_XM"]["txtTw07"] = "<b>Gremio</b>: %s\n";
    $this->translated["SPA_XM"]["txtTw08"] = "<b>Unidad</b>: %s\n";
    $this->translated["SPA_XM"]["txtTw09"] = "<b>IdUnidad</b>: %s\n";
    $this->translated["SPA_XM"]["txtTw10"] = "<b>Sin Usar</b>: %s\n%s\n";
    $this->translated["SPA_XM"]["txtTw11"] = "<b>Usados en ataque</b>: %s (%s - %s%)\n<pre>%s</pre>\n";
    $this->translated["SPA_XM"]["txtTw12"] = "<b>Usdos en defensa</b>: %s\n<pre>%s</pre>\n";
    $this->translated["SPA_XM"]["txtTw13"] = "<b>Rogues</b>: %s\n<pre>%s</pre>\n";
    $this->translated["SPA_XM"]["txtTw14"] = "<b>Alias</b>: %s\n";
    $this->translated["SPA_XM"]["txtTw15"] = "  Rogue decrementada \n\n";
    $this->translated["SPA_XM"]["txtTw16"] = "  Unidad borrada: %s \n\n";
    $this->translated["SPA_XM"]["txtTw17"] = "GT para %s \n\n";
    $this->translated["SPA_XM"]["txtTw18"] = "Unidades usadas por %s \n\n";
    $this->translated["SPA_XM"]["txtTw19"] = "<b>Ataque</b>: (%s/%s - %s%)\n<pre>";
    $this->translated["SPA_XM"]["txtTw20"] = "<b>Defensa</b>: \n<pre>";
    $this->translated["SPA_XM"]["txtTw21"] = "<b>Rogues</b>: \n<pre>";
    $this->translated["SPA_XM"]["txtTw22"] = "GT Visión General para %s \n\n";
    $this->translated["SPA_XM"]["txtTw23"] = "<b>Defensa</b>\n";
    $this->translated["SPA_XM"]["txtTw24"] = "<i>Naves</i> (def: %s)\n";
    $this->translated["SPA_XM"]["txtTw25"] = "<i>Personajes</i> (def: %s)\n";
    $this->translated["SPA_XM"]["txtTw26"] = "<b>Ataque</b>\n";
    $this->translated["SPA_XM"]["txtTw27"] = "<i>Naves</i> (batallas: %s, puntos: %s)\n";
    $this->translated["SPA_XM"]["txtTw28"] = "<i>Personajes</i> (batallas: %s, puntos: %s)\n";
    $this->translated["SPA_XM"]["txtTw29"] = "GT Lista de Rogues Falsas para %s \n\n";
    $this->translated["SPA_XM"]["txtTw30"] = "GT revisión para %s \n\n";
    $this->translated["SPA_XM"]["txtTw31"] = "\n<b>Contra %s</b>\n";
    $this->translated["SPA_XM"]["txtTw32"] = "<b>No al primer ataque</b>\n\n";
    $this->translated["SPA_XM"]["txtTw33"] = "GT ataques para %s \n\n";
    $this->translated["SPA_XM"]["txtTw34"] = "GT defensas para %s \n\n";
    $this->translated["SPA_XM"]["txtTw35"] = "GT guardada para %s \n\n";
    $this->translated["SPA_XM"]["txtTw36"] = "GT borrada para %s \n\n";
    $this->translated["SPA_XM"]["txtTw37"] = "GT histórico para %s \n\n";
    $this->translated["SPA_XM"]["txtTw38"] = "Fecha mostrada: %s \n\n";
    $this->translated["SPA_XM"]["txtTw39"] = "Media de...\n";
    $this->translated["SPA_XM"]["txtTw40"] = "  Unidad usada: %s\n";
    $this->translated["SPA_XM"]["txtTw41"] = "<b>Usados en otros equipos</b>: %s\n<pre>%s</pre>\n";
    $this->translated["SPA_XM"]["txtTw42"] = "<b>Usados en otros equipos</b>: \n<pre>";
    $this->translated["SPA_XM"]["txtTw43"] = "<b>Usados en otros equipos</b>\n";
    $this->translated["SPA_XM"]["txtTw44"] = "<i>Naves</i>\n";
    $this->translated["SPA_XM"]["txtTw45"] = "<i>Personajes</i>\n";
    $this->translated["SPA_XM"]["txtTw46"] = "\n<b>Con %s</b>\n";
    $this->translated["SPA_XM"]["txtTw47"] = "Media (%s GTs)";
    $this->translated["SPA_XM"]["txtTw48"] = "Hay allycodes incorrectos para %s:\n\n";
    $this->translated["SPA_XM"]["txtTw49"] = "¡Todos los allycodes han sido descartados!\n\n";
    $this->translated["SPA_XM"]["txtTw50"] = "Se han añadido los siguientes allycodes para %s:\n\n";
    $this->translated["SPA_XM"]["txtTw51"] = "Allycodes no registrados para %s:\n\n";

    // classe TTeams
    $this->translated["SPA_XM"]["teamserr1"] = "Equipo %s no encontrado para %s.\n\n";
    $this->translated["SPA_XM"]["teamserr2"] = "El comando ya existe.\n\n";
    $this->translated["SPA_XM"]["txtTeams01"] = "Equipo añadido para %s \n\n";
    $this->translated["SPA_XM"]["txtTeams02"] = "<b>Unidades</b> \n";
    $this->translated["SPA_XM"]["txtTeams03"] = "Equipo %s de %s borrado correctamente.\n\n";
    $this->translated["SPA_XM"]["txtTeams04"] = "<b>Equipos definidos</b> para %s \n\n";
    $this->translated["SPA_XM"]["txtTeams05"] = "Comando %s añadido a %s \n\n";
    $this->translated["SPA_XM"]["txtTeams06"] = "Comando %s borrado de %s \n\n";
    $this->translated["SPA_XM"]["txtTeams07"] = "Nombre del equipo: %s \n\n";
    $this->translated["SPA_XM"]["txtTeams08"] = "Equipo: %s \n\n";
    $this->translated["SPA_XM"]["txtTeams09"] = "Unidades: \n";
    $this->translated["SPA_XM"]["txtTeams10"] = "  U%s: %s \n";

    // classe TGF
    $this->translated["SPA_XM"]["gferr1"] = "Sin unidades a controlar.\n\n";
    $this->translated["SPA_XM"]["txtGf01"] = "<b>Gremio</b>: %s\n\n";
    $this->translated["SPA_XM"]["txtGf02"] = "Información General \n\n";

    $this->translated["SPA_XM"]["txtStatg1"] = "Miembros con <b>%s</b> con <b>%s</b> por debajo de <b>%s</b>:";

    $this->translated["SPA_XM"]["txtTwCheck1"] = "[%s][%s] GT Check\n\n";
    $this->translated["SPA_XM"]["txtTwCheckShow1"] = "[%s][%s] Lista de comprobaciones para GT\n\n";
  } 
}
