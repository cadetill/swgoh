<?php
    if ($_REQUEST['json'] == "") {
      echo '<!DOCTYPE html><html lang="en"><body></br>';

    // HELP 
    echo '
      <form action="test.php" method="post"></br>
        <strong>HELP Command</strong></br>
        <label for="comando">Command:</label><input type="text" name="comando" /></br>
	<input type="hidden" name="json" value="0" />
        <input type="submit" />
      </form></br>
    ';

    // REGISTER 
    echo '
      <form action="test.php" method="post"></br>
        <strong>REGISTER Command</strong></br>
        <label for="allycode">AllyCode:</label><input type="text" name="allycode" /></br>
        <label for="allycode">lang:</label><input type="text" name="lang" /></br>
	<input type="hidden" name="json" value="12" />
        <input type="submit" />
      </form></br>
    ';

    // INFO 
    echo '
      <form action="test.php" method="post"></br>
        <strong>INFO Command</strong></br>
        <label for="allycode">*AllyCode:</label><input type="text" name="allycode" /></br>
	<input type="hidden" name="json" value="1" />
        <input type="submit" />
      </form></br>
    ';

    // ZETAS 
    echo '
      <form action="test.php" method="post"></br>
        <strong>ZETAS Command</strong></br>
        <label for="allycode">*AllyCode:</label><input type="text" name="allycode" /></br>
	<input type="hidden" name="json" value="14" />
        <input type="submit" />
      </form></br>
    ';

    // GA
    echo '
      <form action="test.php" method="post"></br>
        <strong>GA Command</strong></br>
        <label for="allycode1">AllyCode1:</label><input type="text" name="allycode1" /></br>
        <label for="allycode2">*AllyCode2:</label><input type="text" name="allycode2" /></br>
	<input type="hidden" name="json" value="2" />
        <input type="submit" />
      </form></br>
    ';

    // SEARCH
    echo '
      <form action="test.php" method="post"></br>
        <strong>SEARCH Command</strong></br>
        <label for="unit">Unit/Alias:</label><input type="text" name="unit" /></br>
        <label for="allycode">*AllyCode:</label><input type="text" name="allycode" /></br>
	<input type="hidden" name="json" value="3" />
        <input type="submit" />
      </form></br>
    ';

    // SEARCH2
    echo '
      <form action="test.php" method="post"></br>
        <strong>SEARCH2 Command</strong></br>
        <label for="unit">Unit/Alias:</label><input type="text" name="unit" /></br>
        <label for="sort">*sort:</label><input type="text" name="sort" /></br>
        <label for="allycode">*AllyCode:</label><input type="text" name="allycode" /></br>
	<input type="hidden" name="json" value="13" />
        <input type="submit" />
      </form></br>
    ';

    // GUILD
    echo '
      <form action="test.php" method="post"></br>
        <strong>GUILD Command</strong></br>
        <label for="subcomand">SubComand:<select name="subcomand">
             <option value="info">info</option>
             <option value="gp">gp</option>
             <option value="chars">chars</option>
             <option value="ships">ships</option>
             <option value="registered">registered</option>
             <option value="nothing"> </option>
        </select></br>
        <label for="allycode">*AllyCode:</label><input type="text" name="allycode" /></br>
	<input type="hidden" name="json" value="4" />
        <input type="submit" />
      </form></br>
    ';

    // RANK
    echo '
      <form action="test.php" method="post"></br>
        <strong>RANK Command</strong></br>
        <label for="stat">Stat:</label><select name="stat">
             <option value="speed">speed</option>
             <option value="hp">hp (health+protection)</option>
             <option value="health">health</option>
             <option value="protection">protection</option>
             <option value="physical">physical</option>
             <option value="special">special</option>
             <option value="potency">potency</option>
             <option value="tenacity">tenacity</option>
             <option value="armor">armor</option>
             <option value="phcrav">phcrav (Physical Critical Avoidance)</option>
             <option value="spcrav">spcrav (Special Critical Avoidance)</option>
             <option value="gp">gp</option>
             <option value="weighing">weighing</option>
             <option value="g13">g13</option>
             <option value="mods6">mods6</option>
             <option value="mods10">mods10</option>
             <option value="relics">relics</option>
             <option value="pondaration">pondaration</option>
        </select></br>
        <label for="unit">Unit/Alias:</label><input type="text" name="unit" /></br>
        <label for="allycode">*AllyCode:</label><input type="text" name="allycode" /></br>
	<input type="hidden" name="json" value="5" />
        <input type="submit" />
      </form></br>
    ';

    // IM 
    echo '
      <form action="test.php" method="post"></br>
        <strong>IM Command</strong></br>
        <label for="subcomand">SubComand:<select name="subcomand">
             <option value="list">list</option>
             <option value="add">add</option>
             <option value="del">del</option>
        </select></br>
        <label for="alias">*Alias:</label><input type="text" name="alias" /></br>
        <label for="allycode">*AllyCode:</label><input type="text" name="allycode" /></br>
        <label for="acronym">*Acronym:</label><input type="text" name="acronym" /></br>
        <label for="acronym">*Url:</label><input type="text" name="url" /></br>
        <label for="acronym">*Branch:</label><input type="text" name="branch" /></br>
	<input type="hidden" name="json" value="6" />
        <input type="submit" />
      </form></br>
    ';

    // COMPAREG
    echo '
      <form action="test.php" method="post"></br>
        <strong>COMPAREG Command</strong></br>
        <label for="allycode1">AllyCode1:</label><input type="text" name="allycode1" /></br>
        <label for="allycode2">*AllyCode2:</label><input type="text" name="allycode2" /></br>
	<input type="hidden" name="json" value="7" />
        <input type="submit" />
      </form></br>
    ';

    // CHAMPIONS
    echo '
      <form action="test.php" method="post"></br>
        <strong>CHAMPIONS Command</strong></br>
        <label for="allycode1">AllyCode1:</label><input type="text" name="allycode1" /></br>
        <label for="allycode2">AllyCode2:</label><input type="text" name="allycode2" /></br>
	<input type="hidden" name="json" value="8" />
        <input type="submit" />
      </form></br>
    ';

    // TW
    echo '
      <form action="test.php" method="post"></br>
        <strong>TW Command</strong></br>
        <label for="subcomand">SubComand:<select name="subcomand">
             <option value="new">new</option>
             <option value="def">def</option>
             <option value="off">off</option>
             <option value="rogue">rogue</option>
             <option value="search">search</option>
             <option value="me">me</option>
             <option value="all">all</option>
             <option value="del">del</option>
             <option value="estampometro">estampometro</option>
             <option value="attacks">attacks</option>
             <option value="defenses">defenses</option>
             <option value="save">save</option>
             <option value="delh">delh</option>
             <option value="listh">listh</option>
             <option value="history">history</option>
             <option value="dates">dates</option>
        </select></br>
        <label for="unit">Unit/Alias:</label><input type="text" name="unit" /></br>
        <label for="points">Points:</label><input type="text" name="points" /></br>
        <label for="allycode">AllyCode:</label><input type="text" name="allycode" /></br>
	<input type="hidden" name="json" value="9" />
        <input type="submit" />
      </form></br>
    ';

    // alias
    echo '
      <form action="test.php" method="post"></br>
        <strong>Alias Command</strong></br>
        <label for="subcomand">SubComand:<select name="subcomand">
             <option value="add">add</option>
             <option value="del">del</option>
             <option value="list">list</option>
        </select></br>
        <label for="unit">Unit:</label><input type="text" name="unit" /></br>
        <label for="alias">Alias:</label><input type="text" name="alias" /></br>
	<input type="hidden" name="json" value="10" />
        <input type="submit" />
      </form></br>
    ';

    // units
    echo '
      <form action="test.php" method="post"></br>
        <strong>Units Command</strong></br>
        <label for="subcomand">SubComand:<select name="subcomand">
             <option value="update">update</option>
             <option value="add">add</option>
             <option value="del">del</option>
             <option value="addc">addc</option>
             <option value="delc">delc</option>
             <option value="list">list</option>
             <option value="listc">listc</option>
        </select></br>
       <label for="unit">Name:</label><input type="text" name="name" /></br>
        <input type="hidden" name="json" value="11" />
        <input type="submit" />
      </form></br>
    ';

    // teams
    echo '
      <form action="test.php" method="post"></br>
        <strong>Teams Command</strong></br>
        <label for="subcomand">SubComand:<select name="subcomand">
             <option value="add">add</option>
             <option value="del">del</option>
             <option value="addc">addc</option>
             <option value="delc">delc</option>
             <option value="list">list</option>
             <option value="get">get</option>
        </select></br>
        <label for="unit">Name:</label><input type="text" name="name" /></br>
        <label for="alias">Alias:</label><input type="text" name="alias" /></br>
        <input type="hidden" name="json" value="15" />
        <input type="submit" />
      </form></br>
    ';

    // GF
    echo '
      <form action="test.php" method="post"></br>
        <strong>GF Command</strong></br>
        <label for="subcomand">SubComand:<select name="subcomand">
             <option value="add">add</option>
             <option value="del">del</option>
             <option value="clear">clear</option>
             <option value="list">list</option>
             <option value="check">check</option>
        </select></br>
        <label for="unit">Unit/Alias:</label><input type="text" name="unit" /></br>
	<input type="hidden" name="json" value="16" />
        <input type="submit" />
      </form></br>
    ';

    // HERE
    echo '
      <form action="test.php" method="post"></br>
        <strong>Here Command</strong></br>
        <label for="subcomand">SubComand:<select name="subcomand">
             <option value="add">add</option>
             <option value="del">del</option>
             <option value="list">list</option>
             <option value="tw">tw</option>
             <option value="tb">tb</option>
             <option value="ofis">ofi</option>
             <option value="raids">raids</option>
             <option value="600">600</option>
             <option value="bot">bot</option>
             <option value="recruiter">recruiter</option>
             <option value="leaders">leaders</option>
        </select></br>
        <label for="unit">Group:</label><input type="text" name="group" /></br>
        <label for="unit">Who:</label><input type="text" name="who" /></br>
	<input type="hidden" name="json" value="17" />
        <input type="submit" />
      </form></br>
    ';

    // PANIC
    echo '
      <form action="test.php" method="post"></br>
        <strong>Panic Command</strong></br>
        <label for="subcomand">SubComand:<select name="subcomand">
             <option value="add">add</option>
             <option value="del">del</option>
             <option value="list">list</option>
             <option value="gas">gas</option>
             <option value="see">see</option>
             <option value="jml">jml</option>
        </select></br>
        <label for="unit">Unit:</label><input type="text" name="unit" /></br>
        <label for="unit">Dependences:</label><input type="text" name="dependences" /></br>
	<input type="hidden" name="json" value="18" />
        <input type="submit" />
      </form></br>
    ';

    // RANCOR
    echo '
      <form action="test.php" method="post"></br>
        <strong>Rancor Command</strong></br>
        <label for="subcomand">SubComand:<select name="subcomand">
             <option value="clear">clear</option>
             <option value="f1">f1</option>
             <option value="f2">f2</option>
             <option value="f3">f3</option>
             <option value="f4">f4</option>
        </select></br>
        <label for="percen">Percen:</label><input type="text" name="percen" /></br>
	<input type="hidden" name="json" value="19" />
        <input type="submit" />
      </form></br>
    ';

  
  echo '</body>
</html>';

  exit;
  }

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
  require_once './textimage/class.textPainter.php';
  
  $data = new TData;
  
  print_r($_REQUEST);
  switch ($_REQUEST['json']) {
    case 0:  // HELP
      $json = str_replace("%comando%", $_REQUEST['comando'], '{"update_id":430390988,"message":{"message_id":7782,"from":{"id":345381881,"is_bot":false,"first_name":"cadetill Ne\'tra","username":"cadetill","language_code":"es"},"chat":{"id":345381881,"first_name":"cadetill Ne\'tra","username":"cadetill","type":"private"},"date":1582148554,"text":"/help %comando%","entities":[{"offset":0,"length":5,"type":"bot_command"}]}}');
      break;
    case 1:  // INFO 
      $json = str_replace("%allycode%", $_REQUEST['allycode'], '{"update_id":430364477,"message":{"message_id":934,"from":{"id":345381881,"is_bot":false,"first_name":"cadetill Ne\'tra","username":"cadetill","language_code":"es"},"chat":{"id":345381881,"first_name":"cadetill Ne\'tra","username":"cadetill","type":"private"},"date":1578414643,"text":"/info %allycode%","entities":[{"offset":0,"length":5,"type":"bot_command"}]}}');
      break;
    case 2:  // GA
      if ($_REQUEST['allycode1'] == "") break;
      $json = str_replace("%allycode1%", $_REQUEST['allycode1'], '{"update_id":430364485,"message":{"message_id":949,"from":{"id":345381881,"is_bot":false,"first_name":"cadetill Ne\'tra","username":"cadetill","language_code":"es"},"chat":{"id":345381881,"first_name":"cadetill Ne\'tra","username":"cadetill","type":"private"},"date":1578416022,"text":"/ga %allycode1% %allycode2%","entities":[{"offset":0,"length":3,"type":"bot_command"}]}}');
      $json = str_replace("%allycode2%", $_REQUEST['allycode2'], $json);
      break;
    case 3:  // SEARCH
      if ($_REQUEST['unit'] == "") break;
      $json = str_replace("%unit%", "+".$_REQUEST['unit'], '{"update_id":430364497,"message":{"message_id":971,"from":{"id":345381881,"is_bot":false,"first_name":"cadetill Ne\'tra","username":"cadetill","language_code":"es"},"chat":{"id":345381881,"first_name":"cadetill Ne\'tra","username":"cadetill","type":"private"},"date":1578419611,"text":"/search %unit% %allycode%","entities":[{"offset":0,"length":7,"type":"bot_command"}]}}');
      if ($_REQUEST['allycode'] == "")
        $json = str_replace("%allycode%", "", $json);
      else
        $json = str_replace("%allycode%", "+".$_REQUEST['allycode'], $json);
      break;
    case 4:  // GUILD
      if ($_REQUEST['subcomand'] == "") break;
      $json = str_replace("%subcomand%", $_REQUEST['subcomand'], '{"update_id":430368182,"message":{"message_id":1263,"from":{"id":345381881,"is_bot":false,"first_name":"cadetill Ne\'tra","username":"cadetill","language_code":"es"},"chat":{"id":345381881,"first_name":"cadetill Ne\'tra","username":"cadetill","type":"private"},"date":1579016771,"text":"/guild %subcomand% %allycode%","entities":[{"offset":0,"length":6,"type":"bot_command"}]}}');
      if ($_REQUEST['allycode'] != "") $_REQUEST['allycode'] = "+".$_REQUEST['allycode'];
      $json = str_replace("%allycode%", $_REQUEST['allycode'], $json);
      break;
    case 5:  // RANK
      if ($_REQUEST['unit'] != "") $_REQUEST['unit'] = "+".$_REQUEST['unit'];
      $json = str_replace("%unit%", $_REQUEST['unit'], '{"update_id":430365219,"message":{"message_id":1109,"from":{"id":345381881,"is_bot":false,"first_name":"cadetill Ne\'tra","username":"cadetill","language_code":"es"},"chat":{"id":345381881,"first_name":"cadetill Ne\'tra","username":"cadetill","type":"private"},"date":1578588853,"text":"/rank %stat% %unit% %allycode%","entities":[{"offset":0,"length":5,"type":"bot_command"}]}}');
      $json = str_replace("%stat%", $_REQUEST['stat'], $json);
      if ($_REQUEST['allycode'] == "")
        $json = str_replace("%allycode%", "", $json);
      else
        $json = str_replace("%allycode%", "+".$_REQUEST['allycode'], $json);
      break;
    case 6:  // IM
      $json = '{"update_id":430368416,"message":{"message_id":865,"from":{"id":345381881,"is_bot":false,"first_name":"cadetill Ne\'tra","username":"cadetill","language_code":"es"},"chat":{"id":-1001308091613,"title":"Bot ofis Netra","type":"supergroup"},"date":1579031211,"text":"/im %subcomand% %alias% %allycode% %acronym% %url% %branch%","entities":[{"offset":0,"length":14,"type":"bot_command"}]}}';
      if ($_REQUEST['alias'] != "") $_REQUEST['alias'] = "+".$_REQUEST['alias'];
      if ($_REQUEST['allycode'] != "") $_REQUEST['allycode'] = "+".$_REQUEST['allycode'];
      if ($_REQUEST['acronym'] != "") $_REQUEST['acronym'] = "+".$_REQUEST['acronym'];
      if ($_REQUEST['url'] != "") $_REQUEST['url'] = "+".$_REQUEST['url'];
      if ($_REQUEST['branch'] != "") $_REQUEST['branch'] = "+".$_REQUEST['branch'];
      $json = str_replace("%subcomand%", $_REQUEST['subcomand'], $json);
      $json = str_replace("%alias%", $_REQUEST['alias'], $json);
      $json = str_replace("%allycode%", $_REQUEST['allycode'], $json);
      $json = str_replace("%acronym%", $_REQUEST['acronym'], $json);
      $json = str_replace("%url%", $_REQUEST['url'], $json);
      $json = str_replace("%branch%", $_REQUEST['branch'], $json);
      break;
    case 7:  // COMPAREG
      //if ($_REQUEST['allycode1'] == "") break;
      $json = str_replace("%allycode1%", $_REQUEST['allycode1'], '{"update_id":430369062,"message":{"message_id":1456,"from":{"id":345381881,"is_bot":false,"first_name":"cadetill Ne\'tra","username":"cadetill","language_code":"es"},"chat":{"id":345381881,"first_name":"cadetill Ne\'tra","username":"cadetill","type":"private"},"date":1579109071,"text":"/compareg %allycode1% %allycode2%","entities":[{"offset":0,"length":9,"type":"bot_command"}]}}');
      $json = str_replace("%allycode2%", $_REQUEST['allycode2'], $json);
      break;
    case 8:  // CHAMPIONS
      if (($_REQUEST['allycode1'] == "") || ($_REQUEST['allycode2'] == "")) break;
      $json = str_replace("%allycode1%", $_REQUEST['allycode1'], '{"update_id":430370857,"message":{"message_id":1530,"from":{"id":345381881,"is_bot":false,"first_name":"cadetill Ne\'tra","username":"cadetill","language_code":"es"},"chat":{"id":345381881,"first_name":"cadetill Ne\'tra","username":"cadetill","type":"private"},"date":1579363952,"text":"/champions %allycode1% %allycode2%","entities":[{"offset":0,"length":10,"type":"bot_command"}]}}');
      $json = str_replace("%allycode2%", $_REQUEST['allycode2'], $json);
      break;
    case 9:  // TW
      if ($_REQUEST['subcomand'] == "") break;
      if ($_REQUEST['unit'] != "") $_REQUEST['unit'] = "+".$_REQUEST['unit'];
      if ($_REQUEST['points'] != "") $_REQUEST['points'] = "+".$_REQUEST['points'];
      if ($_REQUEST['allycode'] != "") $_REQUEST['allycode'] = "+".$_REQUEST['allycode'];
      $json = str_replace("%subcomand%", $_REQUEST['subcomand'], '{"update_id":430564378,"message":{"message_id":68863,"from":{"id":345381881,"is_bot":false,"first_name":"cadetill Ne\'tra","username":"cadetill","language_code":"es"},"chat":{"id":345381881,"first_name":"cadetill Ne\'tra","username":"cadetill","type":"private"},"date":1592941609,"text":"/tw %subcomand% %unit% %points% %allycode%","entities":[{"offset":0,"length":3,"type":"bot_command"}]}}');
      $json = str_replace("%unit%", $_REQUEST['unit'], $json);
      $json = str_replace("%points%", $_REQUEST['points'], $json);
      $json = str_replace("%allycode%", $_REQUEST['allycode'], $json);
      break;
    case 10:  // Alias
      if ($_REQUEST['subcomand'] == "") break;
      if ($_REQUEST['unit'] != "") $_REQUEST['unit'] = "+".$_REQUEST['unit'];
      if ($_REQUEST['alias'] != "") $_REQUEST['alias'] = "+".$_REQUEST['alias'];
      $json = str_replace("%subcomand%", $_REQUEST['subcomand'], '{"update_id":430370857,"message":{"message_id":1530,"from":{"id":345381881,"is_bot":false,"first_name":"cadetill Ne\'tra","username":"cadetill","language_code":"es"},"chat":{"id":345381881,"first_name":"cadetill Ne\'tra","username":"cadetill","type":"private"},"date":1579363952,"text":"/alias %subcomand% %unit% %alias%","entities":[{"offset":0,"length":10,"type":"bot_command"}]}}');
      $json = str_replace("%unit%", $_REQUEST['unit'], $json);
      $json = str_replace("%alias%", $_REQUEST['alias'], $json);
      break;
    case 11:  // Units
      if ($_REQUEST['subcomand'] == "") break;
      if ($_REQUEST['name'] != "") $_REQUEST['name'] = "+".$_REQUEST['name'];
      $json = str_replace("%subcomand%", $_REQUEST['subcomand'], '{"update_id":430370857,"message":{"message_id":1530,"from":{"id":345381881,"is_bot":false,"first_name":"cadetill Ne\'tra","username":"cadetill","language_code":"es"},"chat":{"id":345381881,"first_name":"cadetill Ne\'tra","username":"cadetill","type":"private"},"date":1579363952,"text":"/units %subcomand% %name% ","entities":[{"offset":0,"length":10,"type":"bot_command"}]}}');
      $json = str_replace("%name%", $_REQUEST['name'], $json);
      break;
    case 12:  // REGISTER 
      $json = str_replace("%allycode%", $_REQUEST['allycode'], '{"update_id":430364477,"message":{"message_id":934,"from":{"id":345381881,"is_bot":false,"first_name":"cadetill Ne\'tra","username":"cadetill","language_code":"es"},"chat":{"id":345381881,"first_name":"cadetill Ne\'tra","username":"cadetill","type":"private"},"date":1578414643,"text":"/register %allycode% %lang%","entities":[{"offset":0,"length":5,"type":"bot_command"}]}}');
      $json = str_replace("%lang%", $_REQUEST['lang'], $json);
      break;
    case 13:  // SEARCH2
      if ($_REQUEST['unit'] == "") break;
      if ($_REQUEST['allycode'] != "") $_REQUEST['allycode'] = "+".$_REQUEST['allycode'];
      if ($_REQUEST['sort'] != "") $_REQUEST['sort'] = "+".$_REQUEST['sort'];
      $json = str_replace("%unit%", "+".$_REQUEST['unit'], '{"update_id":430364497,"message":{"message_id":971,"from":{"id":345381881,"is_bot":false,"first_name":"cadetill Ne\'tra","username":"cadetill","language_code":"es"},"chat":{"id":345381881,"first_name":"cadetill Ne\'tra","username":"cadetill","type":"private"},"date":1578419611,"text":"/search2 %unit% %sort% %allycode%","entities":[{"offset":0,"length":7,"type":"bot_command"}]}}');
      $json = str_replace("%sort%", $_REQUEST['sort'], $json);
      $json = str_replace("%allycode%", $_REQUEST['allycode'], $json);
      break;
    case 14:  // ZETAS
      $json = str_replace("%allycode%", $_REQUEST['unit'], '{"update_id":430364497,"message":{"message_id":971,"from":{"id":345381881,"is_bot":false,"first_name":"cadetill Ne\'tra","username":"cadetill","language_code":"es"},"chat":{"id":345381881,"first_name":"cadetill Ne\'tra","username":"cadetill","type":"private"},"date":1578419611,"text":"/zetas %allycode%","entities":[{"offset":0,"length":7,"type":"bot_command"}]}}');
      break;
    case 15:  // TEAMS
      if ($_REQUEST['subcomand'] == "") break;
      if ($_REQUEST['name'] != "") $_REQUEST['name'] = "+".$_REQUEST['name'];
      if ($_REQUEST['alias'] != "") $_REQUEST['alias'] = "+".$_REQUEST['alias'];
      $json = str_replace("%subcomand%", $_REQUEST['subcomand'], '{"update_id":430370857,"message":{"message_id":1530,"from":{"id":345381881,"is_bot":false,"first_name":"cadetill Ne\'tra","username":"cadetill","language_code":"es"},"chat":{"id":345381881,"first_name":"cadetill Ne\'tra","username":"cadetill","type":"private"},"date":1579363952,"text":"/teams %subcomand% %name% %alias%","entities":[{"offset":0,"length":10,"type":"bot_command"}]}}');
      $json = str_replace("%name%", $_REQUEST['name'], $json);
      $json = str_replace("%alias%", $_REQUEST['alias'], $json);
      break;
    case 16:  // GF
      if ($_REQUEST['subcomand'] == "") break;
      if ($_REQUEST['unit'] != "") $_REQUEST['unit'] = "+".$_REQUEST['unit'];
      $json = str_replace("%subcomand%", $_REQUEST['subcomand'], '{"update_id":430546836,"message":{"message_id":62814,"from":{"id":345381881,"is_bot":false,"first_name":"cadetill Ne\'tra","username":"cadetill","language_code":"es"},"chat":{"id":345381881,"first_name":"cadetill Ne\'tra","username":"cadetill","type":"private"},"date":1591395320,"text":"/gf %subcomand% %unit% ","entities":[{"offset":0,"length":10,"type":"bot_command"}]}}');
      $json = str_replace("%unit%", $_REQUEST['unit'], $json);
      break;
    case 17:  // HERE
      if ($_REQUEST['subcomand'] == "") break;
      if ($_REQUEST['group'] != "") $_REQUEST['group'] = "+".$_REQUEST['group'];
      if ($_REQUEST['who'] != "") $_REQUEST['who'] = "+".$_REQUEST['who'];
      $json = str_replace("%subcomand%", $_REQUEST['subcomand'], '{"update_id":430546836,"message":{"message_id":62814,"from":{"id":345381881,"is_bot":false,"first_name":"cadetill Ne\'tra","username":"cadetill","language_code":"es"},"chat":{"id":345381881,"first_name":"cadetill Ne\'tra","username":"cadetill","type":"private"},"date":1591395320,"text":"/here %subcomand% %group% %who% ","entities":[{"offset":0,"length":10,"type":"bot_command"}]}}');
      $json = str_replace("%group%", $_REQUEST['group'], $json);
      $json = str_replace("%who%", $_REQUEST['who'], $json);
      break;
    case 18:  // PANIC
      if ($_REQUEST['subcomand'] == "") break;
      if ($_REQUEST['unit'] != "") $_REQUEST['unit'] = "+".$_REQUEST['unit'];
      if ($_REQUEST['dependences'] != "") $_REQUEST['dependences'] = "+".$_REQUEST['dependences'];
      $json = str_replace("%subcomand%", $_REQUEST['subcomand'], '{"update_id":430824491,"message":{"message_id":171538,"from":{"id":345381881,"is_bot":false,"first_name":"cadetill Ne\'tra","username":"cadetill","language_code":"es"},"chat":{"id":345381881,"first_name":"cadetill Ne\'tra","username":"cadetill","type":"private"},"date":1591395320,"text":"/panic %subcomand% %unit% %dependences% ","entities":[{"offset":0,"length":10,"type":"bot_command"}]}}');
      //$json = str_replace("%subcomand%", $_REQUEST['subcomand'], '{"update_id":431332571,"message":{"message_id":324656,"from":{"id":259461714,"is_bot":false,"first_name":"Samo \u0142M","username":"SrtaBlacky","language_code":"es"},"chat":{"id":259461714,"first_name":"Samo \u0142M","username":"SrtaBlacky","type":"private"},"date":1620843588,"text":"/panic %subcomand% %unit% %dependences%","entities":[{"offset":0,"length":6,"type":"bot_command"}]}}');
      $json = str_replace("%unit%", $_REQUEST['unit'], $json);
      $json = str_replace("%dependences%", $_REQUEST['dependences'], $json);
      break;
    case 19:  // RANCOR
      if ($_REQUEST['subcomand'] == "") break;
      if ($_REQUEST['percen'] != "") $_REQUEST['percen'] = "+".$_REQUEST['percen'];
      $json = str_replace("%subcomand%", $_REQUEST['subcomand'], '{"update_id":430824491,"message":{"message_id":171538,"from":{"id":345381881,"is_bot":false,"first_name":"cadetill Ne\'tra","username":"cadetill","language_code":"es"},"chat":{"id":345381881,"first_name":"cadetill Ne\'tra","username":"cadetill","type":"private"},"date":1591395320,"text":"/rancor %subcomand% %percen% ","entities":[{"offset":0,"length":10,"type":"bot_command"}]}}');
      $json = str_replace("%percen%", $_REQUEST['percen'], $json);
      break;
  }

//  $json = '{"update_id":430537549,"message":{"message_id":59625,"from":{"id":387607721,"is_bot":false,"first_name":"An GeL","username":"Botrytis","language_code":"es"},"chat":{"id":387607721,"first_name":"An GeL","username":"Botrytis","type":"private"},"date":1590689920,"text":"/tw review","entities":[{"offset":0,"length":3,"type":"bot_command"}]}}';
  
  echo "\n\n".$json."\n\n";
  $json = json_decode($json, TRUE);
  //print_r($json); 
  // agafem Id del xat que ens fa la petició
  $data->chatId = $json["message"]["chat"]["id"];
  $data->messageId = $json["message"]["message_id"];
  $data->messageDate = $json["message"]["date"];

  // agafem dades de l'usuari (Id i Nom)
  $data->userId = $json["message"]['from']['id'];
  $data->username = $json["message"]['from']['username'];
  $data->firstname = $json["message"]['from']['first_name'];
  $response = getDataFromId($data);
  if ($response != "") {
    sendMessage($data, array($response));
    return;
  } 
 
  // agafem el text que ens envia
  $data->message = $json["message"]["text"];
  
  $arr = explode(' ',trim($data->message));
  $command = $arr[0];
 
  // agafem informació del jugador per saber de quin gremi és
  $swgoh = new SwgohHelp(array($data->swgohUser, $data->swgohPass));
  $playerJson = $swgoh->fetchPlayer( $data->allycode, $data->language );
  $player = json_decode($playerJson, true);  
  $data->guildId = $player[0]["guildRefId"];

  if ((isCorrectCommand($command, $data)) and ($command != '/register') && ($command != '/register@impman_bot') && ($data->allycode == "")) {
    sendMessage($data, array("You must register before using the bot.\n\n"));
    return;
  } 

  // processem la petició realitzada 
  switch ($command) {
    case '/help':
    case '/help@ImpMan_bot':
      switch (count($arr)) {
        case 1:
          $response = showHelp();
          break;
        case 2:
          $response = showHelp($arr[1]);
          break;
        default:
          $arr = explode(' +',trim($data->message));
          switch (strtolower($arr[1])) {  
            // units to check
            case "unittocheckadd":
              if (count($arr) == 3) {
                $response = unitToCheckAdd($arr[2], $data);
              } else {
                $response = showHelp("+");
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
                $response = showHelp("+");
                $response[0] = "Bad request. See help: \n\n".$response[0]; 
              }
              break;
          }
          break;
      }
      break;
    case '/register':
    case '/register@ImpMan_bot':
      $reg = new TRegister($arr, $data);
      $response = $reg->doRegister();
      break;
    case '/unregister':
    case '/unregister@ImpMan_bot':
      $reg = new TUnRegister($arr, $data);
      $response = $reg->doUnRegister();
      break;
    case '/info':
    case '/info@ImpMan_bot':
      $info = new TInfo($arr, $data);
      $response = $info->execCommand();
      break;
    case '/zetas': 
    case '/zetas@ImpMan_bot':
      $zetas = new TZetas($arr, $data);
      $response = $zetas->execCommand();
      break;
    case '/guild': 
    case '/guild@ImpMan_bot':
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
    case '/ga@ImpMan_bot':
      $ga = new TGA($arr, $data);
      $response = $ga->execCommand();
      break;
    case '/rank': 
    case '/rank@ImpMan_bot':
      $rank = new TRank(explode(' +',trim($data->message)), $data);
      $response = $rank->execCommand();
      break;
    case '/im': 
    case '/im@ImpMan_bot':
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
      $team = new TTeams(explode(' +',trim($data->message)), $data);
      $response = $team->execCommand();
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
        //echo '$data->message: '.$data->message;
      $rancor = new TRancor(explode(' +',trim($data->message)), $data);
      $response = $rancor->execCommand();
      break;
  }

  if (!is_array($response)) 
    $response = array($response);
  sendMessage($data, $response);
 
/***********************************************************************************************************************************************************
  Funcions de caràcter general
***********************************************************************************************************************************************************/ 

/**************************************************************************
  funció que enviarà missatges a Telegram
**************************************************************************/
function sendMessage($data, $response, $keyboard = NULL) {
  if (isset($keyboard)) {
    $teclado = '&reply_markup={"keyboard":['.$keyboard.'], "resize_keyboard":true, "one_time_keyboard":true}';
  }
  echo "\n\n";
  print_r($response);
}

/**************************************************************************
  funció que agafarà les dades guardades a la BBDD de l'usuari
**************************************************************************/
function getDataFromId($data) {
  $idcon = new mysqli($data->bdserver, $data->bduser, $data->bdpas, $data->bdnamebd);
  if ($idcon->connect_error) {
    return "Ooooops! An error has occurred getting data.";
  }
  
  $ret = "";
  $sql = 'select * FROM users WHERE id ='.$data->userId;
  $res = $idcon->query( $sql );
  if ($idcon->error) {
    $ret = "Ooooops! An error has occurred getting data.";
  } else {
    $row = $res->fetch_assoc();
    if (isset($row)) {
      $data->allycode = $row['allycode'];
      $data->language = $row['language'];
    }
  }
  $idcon->close();

  return $ret;
}
 
