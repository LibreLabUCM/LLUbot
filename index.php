<?php
ini_set('display_errors','1'); error_reporting(E_ALL);
date_default_timezone_set('Europe/Madrid');
<!-- Este código está desactualizado. El código del bot actual se podrá encontrar en el repositorio de GitLab. -->
include('config.php');
include('utils.php');
include('database.php');

$available_categories = ['Libros', 'Videos', 'Articulos', 'Peliculas', 'Documentales', 'Otros'];

if (isset($_GET['setHook'])) {
  $ret = sendApiRequest('setwebhook', array('url' => $botHook));
  echo '{';
  foreach ($ret as $key => $value) {
    echo "'$key':$value,";
  }
  echo '}';
  exit;
}

$_IN = file_get_contents("php://input");
$update = json_decode($_IN, true);

if (isset($update['message'])) {
  $update = $update['message'];

  if (!empty($update['text']) && $update["chat"]["id"] == "-154398210") { // If something is received in "LLUbot Talks", send it in "LLU" and exit.
    sendMsg("-1001016650503", $update["text"], false);
    exit();
  }

  if (!empty($update['text']) && $update["chat"]["id"] == "-1001088410143") { // If something is received in "LLUbot Listens"...
    if (!empty($update['reply_to_message']) && !empty($update['reply_to_message']['forward_from'])) {
       $r = sendMsg($update['reply_to_message']['forward_from']['id'], $update["text"], false);
       if (!empty($r['ok']) && $r['ok']) {
           sendMsg($update["chat"]["id"], "Sent", false, $update['message_id']);
       } else {
           sendMsg($update["chat"]["id"], "<pre>".json_encode($r, JSON_PRETTY_PRINT)."</pre>", false, $update['message_id']);
       }
       exit();
    }
  }

  if ($update['chat']['type'] === "private") {// If the update is not from a group, forward it to "LLUbot Listens" group to be handled there
    forwardMsg("-1001088410143", $update['chat']['id'], $update['message_id']);
  }

  if (isset($update['new_chat_participant'])) { // Welcome new users
    $name = isset($update['new_chat_participant']['username'])? '@'.$update['new_chat_participant']['username'] : $update['new_chat_participant']['first_name'];
    if ($name == 'LLUbot') {
      sendMsg($update['chat']['id'], "Holap! Vengo a saludar a los nuevos llusers!!!", false, $update['message_id']);
    } else {
      sendMsg($update['chat']['id'], "Welcome " . $name . " !" . 
              "\n\nPor favor, ten en cuenta que para participar en este chat debes cumplir nuestro código de conducta:\n" .
              "https://librelabucm.org/codigo_de_conducta" .
              "\n\n¿Aún no eres socio? Regístrate en\n" .
              "https://account.librelabucm.org/register.php\n" .
              "¡Te invitamos a pasarte por el Despacho 120!\n",
              false, $update['message_id'], true);
    }
    // Solo para el chat de LibreLab:
    $chat_id = $update['chat']['id'];
    if ($chat_id == -1001364431108) {
      $ret = sendApiRequest('getChatMembersCount', array('chat_id' => $chat_id));
      if ($ret['ok']) {
        checkReward($ret['result'], $name, $chat_id);
      }
    }

  } else if (isset($update['left_chat_participant'])) {
    if (isset($update['left_chat_participant']['username'])) {
      sendMsg($update['chat']['id'], "Bye @" . $update['left_chat_participant']['username'] . " :(", false, $update['message_id']);
    } else {
      sendMsg($update['chat']['id'], "Bye " . $update['left_chat_participant']['first_name'] . " :(", false, $update['message_id']);
    }
  }

  // Some commands
  if (!empty($update['text'])) {
    $chat_id = $update['chat']['id'];
    $command = strtolower($update['text']);
    $commandWithoutLLubot = strstr($command, '@llubot', true);
    $command = ($commandWithoutLLubot)? $commandWithoutLLubot : $command;

    if ($command == "/start") {
      sendMsg($update['chat']['id'], "Hola!\n\nSomos LibreLabUCM, la Asociación de Alumnos de Software y Cultura Libre de la Facultad de Informática de la Universidad Complutense de Madrid.\n\nCon este bot podrás acceder a nuestros /grupos y acceder a otros servicios que ofrecemos. \nPuedes ver mis comandos con /help 🙈\n", false, $update['message_id']);
    } elseif ($command == "/help") {
      sendMsg($update['chat']['id'], "

⭐ <b>¿Quieres obtener información sobre algunas cosillas?</b>

/calendario - Link al calendario de actividades
/mailinglist - Link a la lista de correo
/web - Link a la web
/email - El e-mail de la asociación
/github - Link a nuestro github

⭐ <b>¿Quieres entrar en alguno de nuestros grupos?</b>

Solo tienes que ejecutar el comando /grupos

⭐ <b>¿Te molaría realizar algún taller o proponer una actividad?</b>

/forms - Links a nuestros formularios

⭐ <b> Otros comandos que pueden interesarte </b>

/trinity - Link a Trinity y podio

", false, $update['message_id']);
    } elseif ($command == "/mailinglist" || $command == "/listacorreo") {
      sendMsg($update['chat']['id'], "<a href=\"https://groups.google.com/forum/#!forum/librelabucm/join\">Lista de correo</a>", false, $update['message_id']);
    }
    elseif ($command == "/email" || $command == "/correo") {
      sendMsg($update['chat']['id'], "librelab@ucm.es", false, $update['message_id']);
    }
    elseif ($command == "/calendario" || $command == "/calendar") {
      sendMsg($update['chat']['id'], "<a href=\"https://calendar.librelabucm.org\">Calendario</a> de eventos y actividades", false, 
$update['message_id']);
    }
    elseif ($command == "/web") {
      sendMsg($update['chat']['id'], "<a href=\"www.librelabucm.org\">LLu Web</a>", false, $update['message_id']);
    }
    elseif ($command == "/grupos" || $command == "/groups") {
      $textToSend = "\n Para acceder al grupo general: <a href=\"https://t.me/LLUchat\">Grupo LLU</a>\n";
      $textToSend .= "\nPara estar informado sobre las actividades que realizamos: <a href=\"https://t.me/librelabucm\">Canal de noticias de LLU</a>\n";
      $textToSend .= "\n Grupos de trabajo: \n";
      $textToSend .= "   ✔️ <a href=\"https://t.me/joinchat/Btutqwglu5cmJFLPG0L6wg\">Rompiendo Hardware</a>\n";
      $textToSend .= "   ✔️ <a href=\"https://t.me/joinchat/Apxn5UCACYFe4R1JTfrTLg\">Security Team</a>\n";
      $textToSend .= "   ✔️ <a href=\"https://t.me/joinchat/FrBcTE6XitNjwcP4tiMUzA\">Liberar FDI</a>\n";
      $textToSend .= "   ✔️ <a href=\"https://t.me/joinchat/FrBcTESo5EJNlv8Wh-AV_Q\">Debates LibreLab</a>\n";
      $textToSend .= "\n Otros: \n";
      $textToSend .= "   ❔ <a href=\"https://t.me/joinchat/AAAAAEAvkZUS4P0UV5MdCQ\">Grupo contacto externo</a>\n";
      $textToSend .= "   😸 <a href=\"https://t.me/LibreLab\">Dudas privadas a @LibreLab</a>\n";
      $textToSend .= "   🤖 <a href=\"https://t.me/LLUbot\">Bot @LLUbot</a>\n";
      $textToSend .= "   🤖 <a href=\"https://t.me/@LLUDiscourseBot\">Bot @LLUDiscourseBot</a>\n";
      //$textToSend .= "   🎲 #<a href=\"https://t.me/joinchat/AC_OwECMttsmiW5vfZjo7g\">Random</a>(offtopic)\n";
      sendMsg($update['chat']['id'], $textToSend, false, $update['message_id'], true);
    }
    elseif ($command == "/forms" || $command == "/formularios") {
      $textToSend = "¿Te gustaría proponer un taller, una charla, o un curso?\n";
      $textToSend .= "Pásate por el Despacho 120 que estaremos más que encantados de recibirte o rellena el formulario y 📝 <a href=\"https://goo.gl/forms/VyAXiFfgfDZIj5w43\">¡Coméntanos tus intereses!</a>\n";
      $textToSend .= "\n\n¿Te gustaría realizar alguna actividad en la facultad? Nosotros desde LibreLabUCM podemos ayudarte a organizarlo, reservar aulas, pedir material, ...\n";
      $textToSend .= " 📝 <a href=\"https://goo.gl/forms/OuNAEh5qaXCLUQbA3\">¡Coméntanos tu actividad!</a>\n";
      sendMsg($update['chat']['id'], $textToSend, false, $update['message_id'], true);
    }
    elseif ($command == "/github") {
      sendMsg($update['chat']['id'], "<a href=\"https://github.com/librelabucm\">Nuestro GitHub!</a>", false, $update['message_id']);
    }
    elseif ($command == "/hack4fun" || $command == "/hack") {
      if ($update['chat']['type'] == 'private') {
        $text = [
          "Hola, terrícola...",
          "Hemos recibido tu señal desde nuestro planeta....",
          "¿Buscas más información sobre el taller? ¡Pregunta en el Despacho 120!",
        ];
        foreach ($text as $t) {
          sendMsg($update['chat']['id'], $t, false, $update['message_id'], true);
          usleep(100000);
        }
      }
    }
    elseif (preg_match('/^\/delrecom\s([0-9]+)/', $command, $matches)) {
      if ($chat_id === -1001088410143 || $chat_id === 380656716) {
        $recommendationDeleteId = $matches[1];
        $query = "DELETE FROM RECOMMENDATIONS WHERE id=$recommendationDeleteId";
        if ($db->query($query))
          sendMsg($chat_id, "Deleted $recommendationDeleteId");
        else
          sendMsg($chat_id, "Error");
      }
    }
    /*elseif (($category = getCategory($command))) {
      $query = "SELECT id, name, URI, comment FROM RECOMMENDATIONS WHERE category = '$category';";
      //~ $results = $pdo->query($query) or die('db error');
      $results = $db->query($query) or die('db error');
      $numr = 0;
      $ret = '';
      while ($row = $results->fetchArray(SQLITE3_ASSOC)) {
        if ($row['URI']) {
          $row_str = '<a href="' . $row['URI'] . '">' . $row['name'] . '</a>. ';
        } else {
          $row_str = '<b>' . $row['name'] . '</b>. ';
        }

        if ($row['comment']) $row_str .= ' <i>' . $row['comment'] . '</i>.';
        if ($chat_id === -1001088410143 || $chat_id === 380656716) $row_str = $row['id'].' '.$row_str;
        $ret .= "~&gt; " . $row_str . "\n";
        ++$numr;
      }
      $ret .= "\n";
      if ($numr == 0)
        sendMsg($chat_id, "No hay todavía ninguna recomendación para $category");
      else
        sendMsg($chat_id, $ret, null, "0", true);
    }*/
    # format: /recommend "<name>" <category> [URI] "[comment]"
    /*elseif (preg_match('/^\/recom/', $command) === 1 ) {
      $cmd = $update['text'];
      // Extract arguments from command:
      $cmd_re = preg_match(
              '/\/recom\w*\s"([^"]+)"\s([^\s"]+)(\s[^\s"]+)?(\s"[^"]+")?/',
              $cmd,
              $matches);
      $noOfMatches = count($matches);
      if ($cmd_re !== 1 || $noOfMatches < 3) {
        showHelpCommandRecommend($chat_id);
        exit(1);
      }
      else {
        $name = trim($matches[1]);
        $category = strtolower(trim($matches[2]));
        if (!checkCategoryExists($category)) {
          showHelpCategoryRecommend($chat_id);
          exit(1);
        }
        $uri = '';
        $comment = '';
        if ($noOfMatches > 3) {
          $uri = trim($matches[3]);
          if (!isURIScheme($uri)) {
            showInvalidURI($chat_id);
            exit(1);
          }
          if ($noOfMatches > 4)
            $comment = trim($matches[4]);
        }
        # Now, insert it into database
        $query = $pdo->prepare('INSERT INTO RECOMMENDATIONS (name, category, uri, comment) VALUES ( ?, ?, ?, ? )');
        $recommendation_data = array($name, $category, $uri, $comment);
        $query->execute($recommendation_data);
        $msg = "Nueva recomendación añadida:\n";
        $msg .= "  Nombre: $name\n";
        $msg .= "  Categoría: $category\n";
        if ($uri) $msg .= "  Enlace donde encontrarlo: $uri\n";
        if ($comment) $msg .= "  Comentarios: $comment\n";
        sendMsg($chat_id, $msg, null, $update['message_id'], true);
      }
    } */elseif (preg_match('/^\/trinity(?:\@LLUbot)?\s?([0-9]+)?$/', $command, $matches)) {
        $trinityBaseUrl = 'https://trinity.librelabucm.org';
        $curl = curl_init();
        curl_setopt_array($curl, array(
          CURLOPT_RETURNTRANSFER => 1,
          CURLOPT_URL => $trinityBaseUrl.'/json?view=scoreboard&user_type=0',
        ));
        $resp = curl_exec($curl);
        curl_close($curl);
        $resp = json_decode($resp, true);
        //$msg = print_r($resp, true);
        $numberUsers = count($resp['standings']);
        $count = 5;
        if (!empty($matches[1]) && is_numeric($matches[1])) {
          $count = (int) $matches[1];
        }
        if ($count > 15) $count = 15;
        $emoji = ['🥇', '🥈', '🥉', '4️⃣', '5️⃣'];
        $msg  = "<a href='$trinityBaseUrl/'>Trinity</a>, plataforma CTF de retos. ☠️💻\nPara registrarse es necesario usar un correo @ucm.es (abriremos el registro sin restricción de correo en un futuro).\n\n";
        $msg .= "Total: $numberUsers usuarios\n";
        $msg .= "Podio top $count:\n";
        for($i = 0; $i < $count; $i++) {
          $thisemoji = (!empty($emoji[$i])) ? $emoji[$i] : '😄';
          $msg .= "   {$thisemoji}".str_pad($resp['standings'][$i]['score'], 4, ' ', STR_PAD_LEFT)." | \t<a href='$trinityBaseUrl/user?id={$resp['standings'][$i]['user_id']}'>{$resp['standings'][$i]['team']}</a>\n";
        }
        sendMsg($chat_id, $msg, null, $update['message_id'], true);
    }/* elseif (preg_match('/^\/redeclipse(?:\@LLUbot)?$/', $command, $matches)) {
        $curl = curl_init();
        curl_setopt_array($curl, array(
          CURLOPT_RETURNTRANSFER => 1,
          CURLOPT_URL => 'https://redflare.ofthings.net/reports',
        ));
        $resp = curl_exec($curl);
        curl_close($curl);
        $resp = json_decode($resp, true);
        $msg = json_encode($resp['147.96.25.74:28801'], JSON_PRETTY_PRINT);
        $data = $resp['147.96.25.74:28801'];
        $gameMode = ucwords(str_replace('-', ' ', $data['gameMode']));
        $mapName = ucwords($data['mapName']);
        $timeLeft_hours = floor($data['timeLeft'] / 3600);
        $timeLeft_mins = floor($data['timeLeft'] / 60 % 60);
        $timeLeft_secs = floor($data['timeLeft'] % 60);
        $timeLeft = $timeLeft_secs . ' seconds';
        if ($timeLeft_mins > 0) $timeLeft = $timeLeft_mins . ' minutes ' . $timeLeft;
        if ($timeLeft_hours > 0) $timeLeft = $timeLeft_hours . ' hours ' . $timeLeft;

        $msg = <<<EOT
🖥🎮 <b>RedEclipse</b> 🔫🔴

<a href="https://redeclipse.librelabucm.org">redeclipse.librelabucm.org ({$data['host']}:{$data['port']})</a>
{$data['description']}
🔅Game Mode: $gameMode
🔅Map: $mapName
🔅Time Left: ~ $timeLeft
🔅Version: {$data['versionName']} ({$data['versionbranch']})
🔅Players: {$data['clients']}/{$data['maxClients']}

EOT;
        $msg .= "\nPlayers:\n";
        for ($i = 0; $i < count($data['players']); $i++) {
          $msg .= '  👤';
          $msg .= "{$data['players'][$i]['name']} ";
          if (!empty($data['authNames'][$i]['plain']))
            $msg .= '✔️';
          switch ($data['players'][$i]['privilege']) {
            case 'none':
              break;
            case 'localsupporter':
              $msg .= '💪';
              break;
            case 'localmoderator':
              $msg .= '🔨';
              break;
            case 'localoperator':
              $msg .= '🔧';
              break;
            case 'localadministrator':
              $msg .= '🔮';
              break;
            default:
              break;
          }
          $msg .= "\n";
        }
        sendMsg($chat_id, $msg, null, $update['message_id'], true);
    }  elseif (preg_match('/^\/minetest(?:\@LLUbot)?$/', $command, $matches)) {
        $curl = curl_init();
        curl_setopt_array($curl, array(
          CURLOPT_RETURNTRANSFER => 1,
          CURLOPT_URL => 'http://servers.minetest.net/list?proto_version_min=1&proto_version_max=30',
        ));
        $resp = curl_exec($curl);
        curl_close($curl);
        $resp = json_decode($resp, true);
        $data = false;
        foreach ($resp['list'] as $server) {
          if ($server['address'] == 'minetest.librelabucm.org') {
            $data = $server;
          }
        }
        if (!$data) {
          sendMsg($chat_id, "No data found... :(", null, $update['message_id'], true);
          exit();
        }
        $gameid = ucwords($data['gameid']);
        $damage = $data['damage']  ? 'enabled' : 'disabled';
        $mods = count($data['mods']);
        $msg = <<<EOT
🖥🎮 <b>Minetest</b> 📦⛏

<a href="{$data['url']}">{$data['url']} ({$data['address']}:{$data['port']})</a>
{$data['description']}
🔅Game Mode: $gameid
🔅Damage: $damage
🔅Uptime: ~ {$data['uptime']}
🔅Version: {$data['version']}
🔅Mods: $mods
🔅Players: {$data['clients']}/{$data['clients_max']}

EOT;
        $msg .= "\nPlayers:\n";
        foreach ($data['clients_list'] as $client) {
          $msg .= "  👤$client\n";
        }
        sendMsg($chat_id, $msg, null, $update['message_id'], true);
    } */elseif (preg_match('/^\/propuestas(?:\@LLUbot)?$/', $command, $matches)) {
        $propuestasCategoryId = 8;
        $baseURL = 'https://foro.librelabucm.org';

        $r = file_get_contents("$baseURL/c/$propuestasCategoryId.json");
        $r = json_decode($r, true);

        $msg = "<a href='$baseURL/c/$propuestasCategoryId'>Propuestas</a>: \n\n";
        foreach($r['topic_list']['topics'] as $topic) {
            $topicInfo = file_get_contents(sprintf('%s/t/%d.json', $baseURL, $topic['id'] ));
            $topicInfo = json_decode($topicInfo, true);
            $posts = [];
            foreach($topicInfo['post_stream']['posts'] as $post) {
               $posts[$post['id']] = $post;
            }
            //$stream = $topicInfo['post_stream']['stream'];
            //foreach($stream as $postid) {
            //}
            $author = $topicInfo['details']['created_by']['username'];
            $msg .= sprintf("%s <a href='%s/t/%s/%d'>%s</a> | By: <a href='%s/u/%s'>%s</a> | ❤️%d \n", $topicInfo['closed'] ? '📕' : '📖', $baseURL, $topicInfo['slug'], $topicInfo['id'], $topicInfo['title'], $baseURL, $author, $author, $topicInfo['like_count']);
        }
        sendMsg($chat_id, $msg, null, $update['message_id'], true);
    }

  }
}

function getCategory($command) {
  switch ($command) {
    case "/libros":
    case "/books":
      return 'libros';
    case "/articulos":
    case "/articles":
      return 'articulos';
    case "/videos":
      return 'videos';
    case "/documentales":
    case "/documentaries":
      return 'documentales';
    case "/peliculas":
    case "/pelis":
    case "/movies":
    case "/films":
      return 'peliculas';
    case "/otros":
    case "/others":
      return 'otros';
    default:
      return false;
  }
}

function checkCategoryExists($cat) {
  global $available_categories;
  foreach ($available_categories as $c) {
    if (strtolower($c) === $cat) return true;
   }
  return false;
}

function showHelpCommandRecommend($chat_id) {
  global $available_categories;
  $str = <<<EOD
  Ups, formato incorrecto de recomendación (&gt;_&lt;)
El formato  para añadir recomendaciones es este (ten en cuenta las comillas):
  /recomendar &lt;"nombre"&gt; &lt;categoria&gt; [URI] ["comentario"]

EOD;
  $str .= 'La categoría tiene que ser una de estas: ' . join(', ', $available_categories) . '.';
  $ret = sendMsg($chat_id, $str);
}

function showHelpCategoryRecommend($chat_id) {
  global $available_categories;
  $str = "<b>¡Categoría no reconocida!</b>\n";
  $str .= 'La categoría tiene que ser una de estas: ' . join(', ', $available_categories) . '.';
  sendMsg($chat_id, $str);
}

function showInvalidURI($chat_id) {
  $str = <<<EOD
  Me temo que esa localización no tiene pinta de estar en un <b>formato URI válido</b>.
Por favor, compruébala de nuevo e introduce el comando otra vez.
EOD;
  sendMsg($chat_id, $str);
}

function forwardMsg($chat_id, $from_chat_id, $message_id, $disable_notification = true) {
   return sendApiRequest('forwardMessage',
         array(
            'chat_id' => $chat_id,
            'from_chat_id' => $from_chat_id,
            'disable_notification' => $disable_notification,
            'message_id' => $message_id
         )
      );
}

function sendMsg($id, $text, $keyboard = null, $reply_to_message_id = "0", $disable_web_page_preview = false) {
   if ($keyboard === null) {
      $r = sendApiRequest('sendMessage',
         array(
            'chat_id' => $id,
            'text' => $text,
            'parse_mode' => 'HTML',
            'reply_to_message_id' => $reply_to_message_id,
            'disable_web_page_preview' => $disable_web_page_preview
         )
      );
   } else if ($keyboard === false){
      $r = sendApiRequest('sendMessage',
         array(
            'chat_id' => $id,
            'text' => $text,
            'parse_mode' => 'HTML',
            'reply_markup' => '{"hide_keyboard":true}',
            'reply_to_message_id' => $reply_to_message_id,
            'disable_web_page_preview' => $disable_web_page_preview
         )
      );
   } else {
      $r = sendApiRequest('sendMessage',
         array(
            'chat_id' => $id,
            'text' => $text,
            'parse_mode' => 'HTML',
            'reply_markup' => '{"keyboard":'.json_encode($keyboard).',"one_time_keyboard":true}',
            'reply_to_message_id' => $reply_to_message_id,
            'disable_web_page_preview' => $disable_web_page_preview
         )
      );
   }
   return $r;
}

function sendApiRequest($method, $params = array()) {
   $curl = curl_init();
   curl_setopt_array($curl, array(
       CURLOPT_RETURNTRANSFER => 1,
       CURLOPT_URL => 'https://api.telegram.org/bot'. TOKEN . '/' . $method . '?' . http_build_query($params),
       CURLOPT_SSL_VERIFYPEER => false
   ));
   $data = curl_exec($curl);
   return json_decode($data, true);
}
