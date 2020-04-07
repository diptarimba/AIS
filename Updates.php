<?php
/**
 * Telegram Bot Example whitout WebHook.
 * It uses getUpdates Telegram's API.
 *
 * @author Gabriele Grillo <gabry.grillo@alice.it>
 */
 
/*
//Tadinya gini
Include 'Telegram.php';
Include 'User.php';
Include 'AIS.php';
$bot_token = '913704368:AAFHeUGRc4o6GcNVF1cJfRjPtSjV-6z4QJk';
$telegram = new Telegram($bot_token); 
$ais = new AISPENJUALAN();
$userku = new userku();
*/

 
include 'AIS.php';
$ais = new AISPENJUALAN();
$req = $telegram->getUpdates();
for ($i = 0; $i < $telegram->UpdateCount(); $i++) {
	
    // You NEED to call serveUpdate before accessing the values of message in Telegram Class
    $telegram->serveUpdate($i);
    $text = $telegram->Text();
	$chat_id = $telegram->ChatID();
	
	$ais->checkperintah($text,$ais);
	
}

