<?php 

function powerOf2($n, $name) {
  if ($n != 0 && ($n & ($n - 1)) == 0) {
    $power = log($n,2);
    $MESSAGE_POWER_OF_2 = "🎊🎊<strong>¡¡Enhorabuena $name eres el miembro 2^$power!!</strong> 🎉🎉\nTienes un regalo muy especial a recoger en la guarida de LibreLab 🎁";
    return $MESSAGE_POWER_OF_2;
  }
  return false;
}

function multipleOf10($n, $name) {
  if ($n % 10 == 0)
    return "<strong>¡Enhorabuena $name, eres el miembro número $n!</strong>\n 🍾 ¡¡Fiesta para todos!! 🍻";
  return false;
}

function registerInDatabase($number, $name) {
  global $db, $pdo;
  $query = "SELECT * FROM MEMBERS_COUNT WHERE number=$number;";
  $result = $db->querySingle($query);

  if ($result) {
    error_log("Number $number already awarded");
    return false;
  }
  if ($result === NULL) {
    $comment = "'Miembro $number en chat de LLU'";
    $query = $pdo->prepare('INSERT INTO MEMBERS_COUNT (number, winner, comment, delivered) VALUES ( :number, :name, :comment, :delivered )');
    $query->execute(array('number' => $number, 'name' => $pdo->quote($name), 'comment' => $comment, 'delivered' => 0));
    error_log("Award for no $number member \"$name\"!! ");
    return true;
  }
  // Error in database
  error_log('Error in database executing: ' . $query);
  return false;
}

function checkReward($number, $name, $chat_id) {
  if (!is_int($number)) return;
  if ( ($msgPower = powerOf2($number, $name)) && registerInDatabase($number, $name) )
    sendMsg($chat_id, $msgPower);
  if ( ($msgMultiple = multipleOf10($number, $name)) && registerInDatabase($number, $name) )
    sendMsg($chat_id, $msgMultiple);
}
