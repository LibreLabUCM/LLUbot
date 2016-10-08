<?php

if (empty($_GET['key']) || $_GET['key'] != 'SECRETKEY') { // @llubot
   exit();
}
define('TOKEN', 'SECRETTOKEN');



$botHook = 'https://SECRETURL/llubot/?key='.$_GET['key'];
