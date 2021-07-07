<?php
class TAlias extends TBase {
  private $subcomand;
  private $unit = "";
  private $alias = "";

  /****************************************************
    constructor de la classe. Inicialitza variables
  ****************************************************/
  public function __construct($params, $dataObj) {
    parent::__construct($dataObj);
    
    $this->allyCode = "";
     
    // agafem el subcomando i l'extraem de $params
    $this->subcomand = explode(' ',trim($params[0]));
    $this->subcomand = $this->subcomand[1];
    unset($params[0]);
    
    // actuem segons la quantitat de paràmetres
    switch (count($params)) {
      case 0: 
        break;
		
      case 1: 
        $this->alias = $params[1]; 
        break;
		
      case 2: 
        $this->unit = $params[1]; 
        $this->alias = $params[2]; 
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
      return $this->getHelp("alias", $this->error);
  
    $initialTime = microtime(true);
    
    switch (strtolower($this->subcomand)) {
      case 'add':
        $res = $this->addAlias();
        break;
      case 'del':
        $res = $this->delAlias();
        break;
      case 'list':
        $res = $this->listAlias();
        break;
      default:
        return $this->getHelp("alias");
    }

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
    FUNCIONS PUBLIQUES ESTATIQUES
  ****************************************************/
  /**************************************************************************
    verifica si existeix un alias en la llista
  **************************************************************************/
  public static function aliasSearch($aliasunit, $dataObj) {
    // carreguem fitxer JSON de alias  
    $al = new TAlias(array(""), $dataObj);
    $alias = $al->loadFile();
      
    $unitId = "";
    foreach($alias as $key => $units) {
      foreach($units as $unit) {
        if (strcasecmp($unit, $aliasunit) == 0) 
          $unitId = $key;
      }
    }
      
    if ($unitId == "")
      $unitId = TUnits::unitIdFromUnitName($aliasunit, $dataObj);
      
    return $unitId;
  }

  /****************************************************
    FUNCIONS PRIVADES
  ****************************************************/
  /****************************************************
    afegeix un alias a la llista d'alias
  ****************************************************/
  private function addAlias() {
    if (($this->unit == "") || ($this->alias == ""))
      $this->error = $this->translatedText("error1");                           // Bad request. See help: \n\n
    if ($this->error != "")
      return $this->getHelp("alias", $this->error);

    // carreguem fitxer JSON de alias  
    $alias = $this->loadFile();

    $defId = TAlias::aliasSearch($this->unit, $this->dataObj);
    if ($defId == ""){
      return $this->translatedText("error2", $this->unit);                      // "Unit not found into the inventory.\n\n";
    }


    // mirem que no existeixi el alias en una altra unitat
    foreach ($alias as $unitId => $aliasList) {
      foreach ($aliasList as $a) {
        if (strcasecmp($this->alias, $a) == 0) {
          $tmp = TUnits::unitNameFromUnitId($unitId, $this->dataObj);
          return $this->translatedText("aliaserr1", array($this->alias, $this->unit, $tmp)); // "The alias '".$this->alias."' for '".$this->unit."' already exists.\n\n";
        }
      }
    }

    // trobat el defId, si existeix, mirem que no existeixi el $aliasName
    if (array_key_exists($defId, $alias)) {
      foreach ($alias[$defId] as $a) {
        if (strcasecmp($this->alias, $a) == 0) {
          return $this->translatedText("aliaserr1", array($this->alias, $this->unit)); // "The alias '".$this->alias."' for '".$this->unit."' already exists.\n\n";
        }
      }
      // si arriba aquí no existeix, així que l'afegim
      array_push($alias[$defId], $this->alias);
    } 
    else {
      // si no existeix, l'afegim
      $alias[$defId] = array($this->alias);
    }
      
    // guardem fitxer i sortim
    $a = json_encode($alias, true);
    file_put_contents($this->dataObj->aliasFile, $a);

    $ret  = $this->translatedText("txtAlias2");               // "Alias added\n\n";
    $ret .= $this->translatedText("txtAlias3", $this->unit);  // "   <b>Unit</b>: ".$this->unit."\n";
    $ret .= $this->translatedText("txtAlias4", $this->alias); // "   <b>Alias</b>: ".$this->alias."\n";
    $ret .= "\n";

    return $ret;
  }

  /****************************************************
    esborra un alias de la llista d'alias
  ****************************************************/
  private function delAlias() {
    // carreguem fitxer JSON de alias  
    $alias = $this->loadFile();
  
    // recorrem llista de alias i canviem key pel nom real
    foreach($alias as $key => $units) {
      foreach($units as $key2 => $unit) {
        if (strcasecmp($unit, $this->alias) == 0) 
          unset($alias[$key][$key2]); 
      }
    }
  
    // guardem fitxer i sortim
    $a = json_encode($alias, true);
    file_put_contents($this->dataObj->aliasFile, $a);
      
    $ret = $this->translatedText("txtAlias5", $this->alias); // "Alias ".$this->alias." was deleted.\n\n";
      
    return $ret;
  }

  /****************************************************
    llistat d'alias
  ****************************************************/
  private function listAlias() {
    // carreguem fitxer JSON de alias  
    $alias = $this->loadFile();

    // recorrem llista de alias i canviem key pel nom real
    foreach($alias as $key => $units) {
      $tmp = TUnits::unitNameFromUnitId($key, $this->dataObj);
      if ($tmp != "") {
        $alias[$tmp] = $units;
        unset($alias[$key]);
      }
    }

    ksort($alias);

    $ret = array();
    $tmpStr = $this->translatedText("txtAlias1"); //"<b>List of alias</b>\n\n";
    foreach($alias as $key => $units) {
      $tmp = "";
      foreach($units as $unit) {
        if ($tmp != "") $tmp .= ", ";
        $tmp .= $unit;
      }
      $tmpStr .= "<b>".$key. "</b>: ".$tmp."\n";
      if (strlen($tmpStr) > 3000) {
        array_push($ret, $tmpStr);
        $tmpStr = "";
      }
    }
    $tmpStr .= "\n";

    array_push($ret, $tmpStr);

    return $ret;
  }
  
  /****************************************************
    llegeix el fitxer d'alias i retorna array amb contingut
  ****************************************************/
  private function loadFile() {
    if (file_exists($this->dataObj->aliasFile)) {
      $fileContent = file_get_contents($this->dataObj->aliasFile);
      $alias = json_decode($fileContent, true);
    } else {
      $alias = array();
    }
    return $alias;
  }
  
}
