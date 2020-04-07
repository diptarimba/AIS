<?php
class userku
{
			Public function connectDB(){
				$koneksi = mysqli_connect("localhost","root","","polineswer");
				return $koneksi;
				if (mysqli_connect_errno()){
					echo "Koneksi database gagal : " . mysqli_connect_error();
				}
			}
			
			public function checkuser($useridtele){
				$checking_query = "Select * from user where user_id=('$useridtele')";
				$checking_exe = mysqli_query($this->connectDB(), $checking_query);
				
				if(mysqli_num_rows($checking_exe)>=1){
				}else{
					$insert = mysqli_query($this->connectDB(),"INSERT INTO user(user_id) VALUES('$useridtele')");
				}
			}
			
			public function adlastact($activity,$message,$useridtele){
				$option_adlastaction = [
					'last_activity' => $activity,
					'last_message' => $message,
				];
				
				$encoded_adlastaction = json_encode($option_adlastaction, true);
				
				$update_last_query = "Update 'ais_user' set last_action=('$encoded_adlastaction') where user_id=('$useridtele')";
				$update_last_exe = mysqli_query($this->connectDB(), $update_last_query);
				return $update_last_exe;
			}
			
			public function kataloguser($method,$content,$useridtele){
				if($method == 'check'){
					$check_katalog_query = "Select 'katalog' from 'ais_katalogku' where user_id=('$useridtele')";
					$check_katalog_exe = mysqli_query($this->connectDB(), $check_katalog_query);
					return $check_katalog_exe;
				}elseif($method == 'post'){
					$post_katalog_query = "Update 'katalog' set 'ais_katalogku'=('$content') where user_id=('$useridtele')";
					$post_katalog_exe = mysqli_query($this->connectDB(), $post_katalog_query);
					return $post_katalog_exe;
				}
				
			}
			
			public function checklastuser($useridtele){
				$check_lastaction_query = "Select 'last_action' from 'ais_user' where user_id='$useridtele'";
				$checklastuser = mysqli_query($this->connectDB(), $check_lastaction_query);
				
				$decoded_lastaction = json_decode($checklastuser, true);
				return $decoded_lastaction;
			}
			
			public function tempaction($method,$content,$useridtele){
				if($method == 'post'){
					$tempaction_encode = json_encode($content);
					$add_tempaction = mysqli_query($this->connectDB(), "Update 'ais_user' set 'temp_act'=('$tempaction_encode') where user_id=('$useridtele')");
					return $add_tempaction;
				}elseif($method == 'get'){
					$get_tempaction = mysqli_query($this->connectDB(), "Select 'temp_act' from 'ais_user' where user_id=('$useridtele')");
					$get_tempaction = json_decode($get_tempaction);
					return $get_tempaction;
				}
			}
}