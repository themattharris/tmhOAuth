<?php
$user = 'xxxxxxxx'; //@～～の「～～」の部分をのせる

require_once("./tmhOAuth.php");
 
//Access Tokenの設定 apps.twitter.com でご確認下さい。
//API keyの値を格納
$sConsumerKey = "xxxxxxxxxxxxxxxxxxxxxxxxx";
//API secretの値を格納
$sConsumerSecret = "xxxxxxxxxxxxxxxxxxxxxxxxxxxx";
//Access Tokenの値を格納
$sAccessToken = "xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx";
//Access Token Secretの値を格納
$sAccessTokenSecret = "xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx";
 
//OAuthオブジェクトを生成する
$toa = new tmhOauth(
	array(
		"consumer_key" => 		$sConsumerKey,
		"consumer_secret" => 	$sConsumerSecret,
		"token" => 				$sAccessToken,
		"secret" => 			$sAccessTokenSecret,
		"curl_ssl_verifypeer" => false,
	)
);


$cursor = -1; //Initialize
$sleep_counter = 1; //skip one to be used to get no of user's followers
$loop_counter = 0; //Total loops in the while block
//get the number of a users followers
$user_details = $toa->request( 'GET', "https://api.twitter.com/1.1/users/show.json",array('screen_name'=>$user));
$user_data = json_decode($toa->response["response"]);



//$user_data = json_decode($user_details);
while($cursor != 0){	
	//Get followers
	$followers = $toa->request( 'GET', "https://api.twitter.com/1.1/followers/ids.json",array("cursor" => $cursor, "screen_name"=>$user));
	//$friends = $toa->get('friends/ids', array('cursor' => -1));
	//Convert JSON returned to array

	$data = json_decode($toa->response["response"], false, 512, JSON_BIGINT_AS_STRING);
	$i=0;
	foreach($data->ids as $id){
		$followers_id[$i] = $id;
		$i++;
	}
	
	$roop_count = floor((count($followers_id)-1)/100);//100の位の数字

	$followers_id = array_chunk($followers_id,100);

	for($k=0; $k<=$roop_count; $k++){
		$followers_id_sep[$k] = implode(",",$followers_id[$k]);
		$followers_details[$k] = $toa->request('GET',"https://api.twitter.com/1.1/users/lookup.json",array('user_id'=>$followers_id_sep[$k]));
		$followers_details[$k] = json_decode($toa->response["response"]);
	}

	if($roop_count == 0){
		$all_followers_details = $followers_details[0];
	}

	for($i=1; $i<=$roop_count; $i++){
		if($roop_count>=1){
		$followers_details[0] = array_merge($followers_details[0],$followers_details[$i]);
		}
	$all_followers_details = $followers_details[0];
	}

	$display=0;//表示人数
	for($i=0; $i<count($all_followers_details); $i++){
		echo "@".$all_followers_details[$i]->screen_name."<br>"; 
		$display++;
	}

	echo "<br>";
	//echo "<br>".$display."<br>";//表示回数

	
	//Get next cursor pointer
	$cursor = $data->next_cursor_str;
	
	//Sleep counter
	if($sleep_counter >= 14){
		sleep(16*60); //Pause execution for 16mins
		$sleep_counter = 0; //Reset counter
	}else{
		$sleep_counter++;
	}
	
	//loop counter
	$loop_counter++;
}
//echo output
$followers = $user_data->followers_count;
$loop_multiplier = $loop_counter * 5000;
//How many were really stored
if($loop_multiplier > $followers){
	$dumped = $followers;
}else{
	$dumped = $loop_multiplier;
}
echo '<br><strong>' . $dumped 
	. '</strong> of <strong>' . $followers 
	. '</strong> followers of <strong>' . $user . '</strong> (' 
	. $user_data->name.')';
?>
