<?php
ini_set('display_errors','1'); error_reporting(E_ALL);
date_default_timezone_set('Europe/Madrid');

include('config.php');

if (isset($_GET['setHook'])) {
   echo sendApiRequest('setwebhook', array('url' => $botHook));
   exit;
}

$_IN = file_get_contents("php://input");
logToFile($_IN);


$update = json_decode($_IN, true);

if (isset($update['message'])) {
   //logToFile($_IN);
   $update = $update['message'];
   //logToFile($update['chat']['id']);
   
   if ($update['chat']['id'] != "-1001016650503")
      forwardMsg("-1001088410143", $update['chat']['id'], $update['message_id']);

   if (isset($update['chat'])) {
      if (isset($update['new_chat_participant'])) {
         if ($update['new_chat_participant']['id'] == 106784966) {
            sendMsg($update['chat']['id'], "Holap! Vengo a saludar a los nuevos llusers!!!", false, $update['message_id']);
         } else {
            if (isset($update['new_chat_participant']['username'])) {
               sendMsg($update['chat']['id'], "Welcome @" . $update['new_chat_participant']['username'] . " !\nQuieres unirte a la lista de correo de LibreLabUCM?\nhttps://groups.google.com/forum/#!forum/librelabucm/join \n\n\n/bienvenido_".$update['new_chat_participant']['username']."\n\n Chat random: https://telegram.me/joinchat/ACNdBj4tPjEOdqog8pHTCw", false, $update['message_id']);
            } else {
               sendMsg($update['chat']['id'], "Welcome " . $update['new_chat_participant']['first_name'] . " !\nQuieres unirte a la lista de correo de LibreLabUCM?\nhttps://groups.google.com/forum/#!forum/librelabucm/join \n\n\n/bienvenido_".$update['new_chat_participant']['first_name']."\n\n Chat random: https://telegram.me/joinchat/ACNdBj4tPjEOdqog8pHTCw", false, $update['message_id']);
            }
         }
      } else if (isset($update['left_chat_participant'])) {
         //sendMsg($update['chat']['id'], "Bye @" . $update['left_chat_participant']['username'] . " :(", false, $update['message_id']);
         //logToFile($update['left_chat_participant']['username'] . " left!");
         if (isset($update['left_chat_participant']['username'])) {
            sendMsg($update['chat']['id'], "Bye @" . $update['left_chat_participant']['username'] . " :(", false, $update['message_id']);
         } else {
            sendMsg($update['chat']['id'], "Bye " . $update['left_chat_participant']['first_name'] . " :(", false, $update['message_id']);
         }
      } else if (isset($update['new_chat_title'])) {
         sendMsg($update['chat']['id'], "Hum.. I like that name. Or perhaps I don't. Who knows....", false, $update['message_id']);
      } else if (isset($update['new_chat_photo'])) {
         sendMsg($update['chat']['id'], "Is that a good photo? I mean... I'm just a bot, i don't understand it.", false, $update['message_id']);
      } else if (isset($update['delete_chat_photo'])) {
         sendMsg($update['chat']['id'], "That was sad", false, $update['message_id']);
      }
   }

   if (!empty($update['text']) && $update["chat"]["id"] == "-154398210") {
      sendMsg("-1001016650503", $update["text"], false);
   }
   if (!empty($update['text'])) {
      $update['text'] = strtolower($update['text']);
      
      if ($update['text'] == "/mailinglist" || $update['text'] == "/mailinglist@llubot") {
         sendMsg($update['chat']['id'], "<a href=\"https://groups.google.com/forum/#!forum/librelabucm/join\">Lista de correo</a>", false, $update['message_id']);
      }
      if ($update['text'] == "/web" || $update['text'] == "/web@llubot") {
         sendMsg($update['chat']['id'], "<a href=\"www.librelabucm.org\">LLu Web</a>", false, $update['message_id']);
      }
      if ($update['text'] == "/grupos" || $update['text'] == "/grupos@llubot") {
         sendMsg($update['chat']['id'], "<a href=\"http://grasia.fdi.ucm.es/librelab/web/?q=node/7\">Comisiones</a>\n\nGrupos: \n🔜 Hardware\n✔️ <a href=\"https://telegram.me/joinchat/Apxn5UERfUC5KK_ejIt3Ig\">Install Parties</a>\n✔️ <a href=\"https://telegram.me/CryptoParty\">CryptoParty</a>\n✔️ <a href=\"https://telegram.me/joinchat/Apxn5UF37NWlsMa-0gQ2_g\">Telegram Bots</a>\n✔️ <a href=\"https://telegram.me/joinchat/Ar4agkCACYELE5TZ5AWtAA\">Hacking</a>\n✔️ <a href=\"https://telegram.me/joinchat/AN341TyY2wfsr32vpSHcSg\">LLU</a>", false, $update['message_id'], true);
      }
      if ($update['text'] == "/github" || $update['text'] == "/github@llubot") {
         sendMsg($update['chat']['id'], "<a href=\"https://github.com/librelabucm\">Nuestro GitHub!</a>", false, $update['message_id']);
      }

   }

   if (!empty($update['text']) && false) {
      $update['text'] = strtolower($update['text']);
      if (strpos($update['text'], 'easter egg') !== false) {
         sendMsg($update['chat']['id'], "The egg hatched!", false, $update['message_id']);
      }
      if (strpos($update['text'], 'spam') !== false) {
         sendMsg($update['chat']['id'], "What? Who said spam?", false, $update['message_id']);
      }
      if (strpos($update['text'], 'es mi cumple') !== false) {
         sendMsg($update['chat']['id'], "felicidades entonces", false, $update['message_id']);
      }
      if (strpos($update['text'], 'es su cumple') !== false || strpos($update['text'], 'es el cumple de') !== false) {
         sendMsg($update['chat']['id'], "felicidades al cumpleañero!", false, $update['message_id']);
      }
      if (strpos($update['text'], 'felicidades') !== false) {
         sendMsg($update['chat']['id'], "felicidades!!! (Espera... me he perdido. A quien hay q felicitar?)", false, $update['message_id']);
      }
      if (strpos($update['text'], strtolower($update['from']['first_name'])) !== false) {
         sendMsg($update['chat']['id'], "Narcisita....", false, $update['message_id']);
      } else if (!empty($update['from']['username']) && strpos($update['text'], strtolower($update['from']['username'])) !== false) {
         sendMsg($update['chat']['id'], "....atsisicraN@", false, $update['message_id']);
      }
      if (strpos($update['text'], 'votemos') !== false) {
         sendMsg($update['chat']['id'], "Voto en contra. No me parece buena idea.", false, $update['message_id']);
      }
      if (strpos($update['text'], 'software privativo') !== false) {
         sendMsg($update['chat']['id'], "Perdone, pero no le he entendido bien.\n<em>software privativo</em> no está registrado en mi diccionario.", false, $update['message_id']);
      }
      if (strpos($update['text'], '.doc') !== false) {
         sendMsg($update['chat']['id'], ".odt *", false, $update['message_id']);
      }
      if (strpos($update['text'], 'microsoft') !== false) {
         sendMsg($update['chat']['id'], "microsoft :(", false, $update['message_id']);
      }
      if (strpos($update['text'], '#web') !== false) {
         sendMsg($update['chat']['id'], "http://www.librelabucm.org/", false, $update['message_id']);
      }
      if (strpos($update['text'], 'semanainformatica') !== false || strpos($update['text'], 'semana de la informatica') !== false) {
         sendMsg($update['chat']['id'], "http://informatica.ucm.es/ii-semana-de-la-informatica-2016", false, $update['message_id']);
      }
      if (strpos($update['text'], '#corewars') !== false) {
         sendMsg($update['chat']['id'], "http://www.dsa-research.org/doku.php?id=people:poletti:corewarfdi", false, $update['message_id']);
      }
      if (strpos($update['text'], '#github') !== false) {
         sendMsg($update['chat']['id'], "https://github.com/LibreLabUCM", false, $update['message_id']);
      }
      if (strpos($update['text'], '@llubot, dime q si') !== false) {
         sendMsg($update['chat']['id'], "Pos claro q si!", false, $update['message_id']);
         exit();
      }
      if (strpos($update['text'], 'a q si @llubot ?') !== false) {
         sendMsg($update['chat']['id'], "Estoy a favor", false, $update['message_id']);
         exit();
      }
      if (strpos($update['text'], '@llubot') !== false) {
         sendMsg($update['chat']['id'], "Dime. (Quizá no te escucho)", false, $update['message_id']);
      }
   }
   
   exit;
}

function forwardMsg($chat_id, $from_chat_id, $message_id, $disable_notification = true) {
   $r = sendApiRequest('forwardMessage',
         array(
            'chat_id' => $chat_id,
            'from_chat_id' => $from_chat_id,
            'disable_notification' => $disable_notification,
            'message_id' => $message_id
         )
      );
   return json_encode($r, true);
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
   if (!json_decode($r, true)['ok']) {
      global $_IN;
      //logToFile($_IN);
      //logToFile("R:-> " . $r);
   }
   
}

function sendApiRequest($method, $params = array()) {
   $curl = curl_init();
   curl_setopt_array($curl, array(
       CURLOPT_RETURNTRANSFER => 1,
       CURLOPT_URL => 'https://api.telegram.org/bot'. TOKEN . '/' . $method . '?' . http_build_query($params),
       CURLOPT_SSL_VERIFYPEER => false
   ));
   return curl_exec($curl);
}


function logToFile($text) {
   // Debug
}
