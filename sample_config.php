<?php

/* INSTRUCTIONS:
 *   Copy sample_config.php to config.php (cp sample_config.php config.php)
 *   Edit SECRETKEY, SECRETTOKEN and SECRETURL with appropiate values
 *   Run https://SECRETURL/?key=SECRETKEY&setHook in browser to set the webhook
 */
if (empty($_GET['key']) || $_GET['key'] != 'SECRETKEY') { // @llubot
  exit();
}
define('TOKEN', 'SECRETTOKEN');


$botHook = 'https://SECRETURL/llubot/?key='.$_GET['key'];
