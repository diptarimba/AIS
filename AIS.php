<?php
include 'Telegram.php';
Include 'User.php';

class AISPENJUALAN
{
	public function __construct(){
		$userku = new userku();
		$bot_token = '913704368:AAFHeUGRc4o6GcNVF1cJfRjPtSjV-6z4QJk';
		$telegram = new Telegram($bot_token);
	}

	function checkperintah($text){
		if($text = '/start' || $text = 'home'){
			$this->home();
		}elseif($text = 'kat_tambah'){
			$this->katalog_tambah();
		}elseif($text = 'kat_edit' ){
			$this->katalog_edit();
		}elseif($text = 'kat_hapus'){
			$this->katalog_hapus();
		}else{
			checklastact($text);
		}
	}
	
	function checklastact($text){
		$last_activity = $userku->checklastuser($chat_id);
		$lastactivity = $last_activity['last_activity'];
		$lastmessage = $last_activity['last_message'];
		$keyword = explode("_", $lastactivity);
		$keyword = $keyword[0]."_".$keyword[1];
		if($keyword == 'kat_tambah'){
			if($lastactivity == 'kat_tambah_selesai'){
				$this->katalog($lastmessage,$lastactivity);
			}else{
				$this->katalog_tambah($text,$lastmessage,$lastactivity);
			}
		}elseif($keyword == 'kat_hapus'){
			$this->katalog_hapus();
		}
	
	}
	
	public function home(){
		
		//$user->checkuser($useridtele);
		$option = array(
		$telegram->buildInlineKeyboardButton("Jual", $url='', $callback_data='home_jual'),
		$telegram->buildInlineKeyboardButton("Katalog", $url='', $callback_data='home_katalog'),
		$telegram->buildInlineKeyboardButton("Transaksi", $url='', $callback_data='home_transaksi'));
		$keyb = $telegram->buildInlineKeyboard($option);
		$text = "[AIS Video Challenge]Selamat Datang, pelanggan apakah yang hadir pada hari ini? semoga tetap memberikan cuan maksimal";
		$content = ['chat_id' => $chat_id, 'reply_markup' => $keyb, $reply = $rep_text];
		return $telegram->sendMeesage($content);
	}
	
	public function jual(){
		$rep_text = "Kita saat ini memiliki beberapa barang yang bisa dijual, silahkan pilih barang dibawah ini:";
		$option = array(
		array($telegram->buildInlineKeyboardButton("Tambah Barang", $url='', $callback_data="jual_add")),
		array($telegram->buildInlineKeyboardButton("Hapus Barrang", $url='', $callback_data="jual_del")),
		array($telegram->buildInlineKeyboardButton("Jual", $url='', $callback_data="jual_exe"),$telegram->buildInlineKeyboardButton("Home", $url='', $callback_data="home"))
		);
		
		$keyb = $telegram->buildInlineKeyboard($option);
		$content = ['chat_id' => $chat_id, 'reply_markup' => $keyb, 'text' => $rep_text];
		return $content;
	}
	
	public function transaksi(){
		$rep_text = "Terimakasih telah bekerjssama bersama saya dalam bertransaksi, saya memiliki daftar riwayat transaksi dengan anda, apakah anda ingin melihat dalam bentuk harian/bulanan?";
		$option = array(
		array($telegram->buildInlineKeyboardButton("Trx Harian", $url='', $callback_data="trans_harian"),$telegram->buildInlineKeyboardButton("Trx Bulanan", $url='', $callback_data="trans_bulanan")),
		array($telegram->buildInlineKeyboardButton("home", $url='', $callback_data="home"))
			);
		$keyb = $telegram->buildInlineKeyboard($option);
		$content = ['chat_id' => $chat_id, 'reply_markup' => $keyb, 'text' => $rep_text];
		return $content;
	}
	
	public function katalog($lastmessage,$lastactivity){
		$katalog = $userku->kataloguser('check', $content = '', $chat_id);
		if($katalog == ''){
			$rep_text = "Anda belum memiliki barang untuk dijual";
		}else{
			$katalog_parse = json_decode($katalog, true);
			$rep_text = "Barang anda sebagai berikut:\n";
			foreach ($katalog_parse as $key => $inti){
				$rep_text = ($key+1).$inti['nama']."\n";
			}
			$rep_text = $rep_text."Apakah perlu ada pembaruan?";
		}
		$option = array(array($telegram->buildInlineKeyboardButton("Tambah", $url='', $callback_data="kat_tambah"),
		$telegram->buildInlineKeyboardButton("Edit", $url='', $callback_data="kat_edit"),
		$telegram->buildInlineKeyboardButton("Hapus", $url='', $callback_data="kat_hapus")),
		array($telegram->buildInlineKeyboardButton("Home", $url='', $callback_data="home")));
		$keyb = $telegram->buildInlineKeyboard($option);
		$content = ['chat_id' => $chat_id, 'message_id' => $lastmessage, 'reply_markup' => $keyb, 'text' => $rep_text];
		
		$kirim = $telegram->editMessageText($content);
		
		$userku->adlastact('home_katalog',$kirim['result']['message_id']);
	}
	
	public function katalog_tambah($text,$lastmessage,$lastactivity){
		if($lastactivity == 'home_katalog'){
			$rep_text = "Masukan nama untuk barang baru";
			$content = ['chat_id' => $chat_id, 'message_id' => $lastmessage, 'text' => $rep_text];
			$kirim = $telegram->editMessageText($content);
			$userku->adlastact('kat_tambah_0',$kirim['result']['message_id']);
			
		}elseif($lastactivity == 'kat_tambah_0'){
			$tambah_kat['Nama Barang'] = $text;
			$userku->tempaction("post",$tambah_kat,$chat_id);
			$rep_text = "Masukan harga per unit (ex 20000)";
			$content = ['chat_id' => $chat_id, 'message_id' => $lastmessage];
			$kirim = $telegram->deleteMessage($content);
			$content = ['chat_id' => $chat_id, 'reply' => $rep_text];
			$kirim = $telegram->sendMeesage($content);
			$userku->adlastact('kat_tambah1',$kirim['result']['message_id']);
			
		}elseif($lastactivity == 'kat_tambah_1'){
			$tambah_kat = $userku->tempaction("get",$content,$chat_id);
			$tambah_kat['Harga Barang'] = $text;
			$userku->tempaction("post",$tambah_kat,$chat_id);
			$rep_text = "Masukan jumlah unit yang tersedia";
			$content = ['chat_id' => $chat_id, 'message_id' => $lastmessage];
			$kirim = $telegram->deleteMessage($content);
			$content = ['chat_id' => $chat_id, 'reply' => $rep_text];
			$kirim = $telegram->sendMeesage($content);
			$userku->adlastact('kat_tambah_2',$kirim['result']['message_id']);
			
		}elseif($lastactivity == 'kat_tambah_2'){
			$tambah_kat = $userku->tempaction("get",$content,$chat_id);
			$tambah_kat['Jumlah Unit'] = $text;
			$katalog = $userku->kataloguser("check",$content = '', $chat_id);
			if($katalog == ''){
				$userku->tempaction("post",$tambah_kat,$chat_id);
			}else{
				$katalog_parse = json_decode($katalog, true);
				$katalog_parse[] = $tambah_kat;
				$katalog_encode = json_encode($katalog_parse, true);
				$userku->kataloguser("post", $katalog_encode, $chat_id);
			}
			$userku->tempaction("post",'',$chat_id);
			$rep_text = "Penambahan barang baru selesai";
			$content = ['chat_id' => $chat_id, 'message_id' => $lastmessage];
			$kirim = $telegram->deleteMessage($content);
			$content = ['chat_id' => $chat_id, 'reply' => $rep_text];
			$kirim = $telegram->sendMeesage($content);
			$userku->adlastact('kat_tambah_selesai',$kirim['result']['message_id']);
			checkperintah();
		}
	}
}