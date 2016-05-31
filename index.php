<?php
header('Access-Control-Allow-Origin: *');


require 'vendor/autoload.php';


$app=new \Slim\Slim();

//database
function getConnection() {
	$dbhost="";
	$dbuser="";
	$dbpass="";
	$dbname="";
	$dbh = new PDO("mysql:host=$dbhost;dbname=$dbname;charset=utf8", $dbuser, $dbpass);
	$dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	return $dbh;
}

$app->get('/session',function() use ($app){
    $req = $app->request();

	//basic informations
	$dev=;//enter your developer id
	$auth="";// your api auth code
	$date=gmdate('YmdHis');
	$signature=md5($dev."createsession".$auth.gmdate('YmdHis'));

	//testsession
	$test="http://localhost/smite_slim_api/testsession";
	$ch2 = curl_init();
	curl_setopt($ch2, CURLOPT_URL, $test);
	curl_setopt($ch2, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch2, CURLOPT_TIMEOUT, 10);
	$resp2 = curl_exec($ch2);
	$findme="sessionup";
	$pos = strpos($resp2, $findme);
	
	//check response if there have active session or not
	if($pos=="sessionup"){
		echo "You have session current so not need open new session";
	}else{
		$url="http://api.smitegame.com/smiteapi.svc/createsessionJSON/".$dev."/".$signature."/".$date;
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_TIMEOUT, 10);
		$resp = curl_exec($ch);
		$array=json_decode($resp, true);
		$session_id=$array["session_id"];
		curl_close($ch);
		echo $session_id;
		$query="UPDATE sessions SET session_id='$session_id' WHERE id='3'";
		try {
		  $db=getConnection();
		  $workit=$db->query($query);
		  $db = null;
		   } catch(PDOException $e) {
				 echo '{"error":{"text":'. $e->getMessage() .'}}';
		   }
	}

})->name('session');

$app->get('/testsession',function() use ($app){
    $req = $app->request();
	$dev=;//enter your developer id
	$auth="";// your api auth code
	$query="select * from sessions where id='3'";
	$date=gmdate('YmdHis');
    try {

		$db=getConnection();
		$workit=$db->query($query);
		$getit = $workit->fetchAll(PDO::FETCH_OBJ);
		if($getit){
			foreach ($getit as $key) {
				$session_id=$key -> session_id;
				$signature=md5($dev."testsession".$auth.gmdate('YmdHis'));
				$url="http://api.smitegame.com/smiteapi.svc/testsessionJSON/".$dev."/".$signature."/".$session_id."/".$date;
				$ch = curl_init();
				curl_setopt($ch, CURLOPT_URL, $url);
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
				curl_setopt($ch, CURLOPT_TIMEOUT, 10);
				$resp = curl_exec($ch);
				$array=json_decode($resp, true);
				$findme="Invalid session id.";
				$pos = strpos($array, $findme);
				if($pos=="Invalid session id."){
					echo "thereisnosession";
				}else{
					echo "sessionup";
				}
				curl_close($ch);
			}
		}
	  $db = null;
	   } catch(PDOException $e) {
		     echo '{"error":{"text":'. $e->getMessage() .'}}';
	   }
})->name('testsession');

$app->get('/getgods',function() use ($app){
    $req = $app->request();
	$dev=;//enter your developer id
	$auth="";// your api auth code
	$query="select * from sessions where id='3'";
	$date=gmdate('YmdHis');
    try {

		$db=getConnection();
		$workit=$db->query($query);
		$getit = $workit->fetchAll(PDO::FETCH_OBJ);
		if($getit){
			foreach ($getit as $key) {
				$session_id=$key -> session_id;
				$signature=md5($dev."getgods".$auth.gmdate('YmdHis'));
				$url="http://api.smitegame.com/smiteapi.svc/getgodsJSON/".$dev."/".$signature."/".$session_id."/".$date."/1";
				$ch = curl_init();
				curl_setopt($ch, CURLOPT_URL, $url);
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
				curl_setopt($ch, CURLOPT_TIMEOUT, 10);
				$resp = curl_exec($ch);
				$array=json_decode($resp,true);
				$countarray=count($array);


				$file=fopen('gods.json','w');
				fwrite($file,$resp);
				fclose($file);

				var_dump(json_decode($resp, true));
				curl_close($ch);
			}
		}
	  $db = null;
	   } catch(PDOException $e) {
		     echo '{"error":{"text":'. $e->getMessage() .'}}';
	   }

})->name('getgods');

$app->get('/getgodskins/:id',function($id) use ($app){
    $req = $app->request();
	$dev=;//enter your developer id
	$auth="";// your api auth code
	$query="select * from sessions where id='3'";
	$date=gmdate('YmdHis');
    try {

		$db=getConnection();
		$workit=$db->query($query);
		$getit = $workit->fetchAll(PDO::FETCH_OBJ);
		if($getit){
			foreach ($getit as $key) {
				$session_id=$key -> session_id;
				$signature=md5($dev."getgodskins".$auth.gmdate('YmdHis'));
				$url="http://api.smitegame.com/smiteapi.svc/getgodskinsJSON/".$dev."/".$signature."/".$session_id."/".$date."/".$id."/1";
				$ch = curl_init();
				curl_setopt($ch, CURLOPT_URL, $url);
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
				curl_setopt($ch, CURLOPT_TIMEOUT, 10);
				$resp = curl_exec($ch);
				$file=fopen($id.'.json','w');
				fwrite($file,$resp);
				fclose($file);

				var_dump(json_decode($resp, true));
				curl_close($ch);
			}
		}
	  $db = null;
	   } catch(PDOException $e) {
		     echo '{"error":{"text":'. $e->getMessage() .'}}';
	   }

})->name('getgodskins');

$app->get('/getgodrecommendeditems/:id',function($id) use ($app){
    $req = $app->request();
	$dev=;//enter your developer id
	$auth="";// your api auth code
	$query="select * from sessions where id='3'";
	$date=gmdate('YmdHis');
    try {

		$db=getConnection();
		$workit=$db->query($query);
		$getit = $workit->fetchAll(PDO::FETCH_OBJ);
		if($getit){
			foreach ($getit as $key) {
				$session_id=$key -> session_id;
				$signature=md5($dev."getgodrecommendeditems".$auth.gmdate('YmdHis'));
				$url="http://api.smitegame.com/smiteapi.svc/getgodrecommendeditemsJSON/".$dev."/".$signature."/".$session_id."/".$date."/".$id."/1";
				$ch = curl_init();
				curl_setopt($ch, CURLOPT_URL, $url);
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
				curl_setopt($ch, CURLOPT_TIMEOUT, 10);
				$resp = curl_exec($ch);
				var_dump(json_decode($resp, true));
				curl_close($ch);
			}
		}
	  $db = null;
	   } catch(PDOException $e) {
		     echo '{"error":{"text":'. $e->getMessage() .'}}';
	   }

})->name('getgodrecommendeditems');

$app->get('/getitems',function() use ($app){
    $req = $app->request();
	$dev=;//enter your developer id
	$auth="";// your api auth code
	$query="select * from sessions where id='3'";
	$date=gmdate('YmdHis');
    try {

		$db=getConnection();
		$workit=$db->query($query);
		$getit = $workit->fetchAll(PDO::FETCH_OBJ);
		if($getit){
			foreach ($getit as $key) {
				$session_id=$key -> session_id;
				$signature=md5($dev."getitems".$auth.gmdate('YmdHis'));
				$url="http://api.smitegame.com/smiteapi.svc/getitemsJSON/".$dev."/".$signature."/".$session_id."/".$date."/1";
				$ch = curl_init();
				curl_setopt($ch, CURLOPT_URL, $url);
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
				curl_setopt($ch, CURLOPT_TIMEOUT, 10);
				$resp = curl_exec($ch);

				$file=fopen('items.json','w');
				fwrite($file,$resp);
				fclose($file);

				var_dump(json_decode($resp, true));
				curl_close($ch);
			}
		}
	  $db = null;
	   } catch(PDOException $e) {
		     echo '{"error":{"text":'. $e->getMessage() .'}}';
	   }

})->name('getitems');

$app->get('/gettopmatches',function() use ($app){
    $req = $app->request();
	$dev=;//enter your developer id
	$auth="";// your api auth code
	$query="select * from sessions where id='3'";
	$date=gmdate('YmdHis');
    try {

		$db=getConnection();
		$workit=$db->query($query);
		$getit = $workit->fetchAll(PDO::FETCH_OBJ);
		if($getit){
			foreach ($getit as $key) {
				$session_id=$key -> session_id;
				$signature=md5($dev."gettopmatches".$auth.gmdate('YmdHis'));
				$url="http://api.smitegame.com/smiteapi.svc/gettopmatchesJSON/".$dev."/".$signature."/".$session_id."/".$date;
				$ch = curl_init();
				curl_setopt($ch, CURLOPT_URL, $url);
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
				curl_setopt($ch, CURLOPT_TIMEOUT, 10);
				$resp = curl_exec($ch);

				$file=fopen('topmatches.json','w');
				fwrite($file,$resp);
				fclose($file);

				var_dump(json_decode($resp, true));
				curl_close($ch);
			}
		}
	  $db = null;
	   } catch(PDOException $e) {
		     echo '{"error":{"text":'. $e->getMessage() .'}}';
	   }

})->name('gettopmatches');

$app->get('/getesportsproleaguedetails',function() use ($app){
    $req = $app->request();
	$dev=;//enter your developer id
	$auth="";// your api auth code
	$query="select * from sessions where id='3'";
	$date=gmdate('YmdHis');
    try {

		$db=getConnection();
		$workit=$db->query($query);
		$getit = $workit->fetchAll(PDO::FETCH_OBJ);
		if($getit){
			foreach ($getit as $key) {
				$session_id=$key -> session_id;
				$signature=md5($dev."getesportsproleaguedetails".$auth.gmdate('YmdHis'));
				$url="http://api.smitegame.com/smiteapi.svc/getesportsproleaguedetailsJSON/".$dev."/".$signature."/".$session_id."/".$date;
				$ch = curl_init();
				curl_setopt($ch, CURLOPT_URL, $url);
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
				curl_setopt($ch, CURLOPT_TIMEOUT, 10);
				$resp = curl_exec($ch);

				$file=fopen('esports.json','w');
				fwrite($file,$resp);
				fclose($file);

				var_dump(json_decode($resp, true));
				curl_close($ch);
			}
		}
	  $db = null;
	   } catch(PDOException $e) {
		     echo '{"error":{"text":'. $e->getMessage() .'}}';
	   }

})->name('getesportsproleaguedetails');

$app->get('/getdataused',function() use ($app){
    $req = $app->request();
	$dev=;//enter your developer id
	$auth="";// your api auth code
	$query="select * from sessions where id='3'";
	$date=gmdate('YmdHis');
    try {

		$db=getConnection();
		$workit=$db->query($query);
		$getit = $workit->fetchAll(PDO::FETCH_OBJ);
		if($getit){
			foreach ($getit as $key) {
				$session_id=$key -> session_id;
				$signature=md5($dev."getdataused".$auth.gmdate('YmdHis'));
				$url="http://api.smitegame.com/smiteapi.svc/getdatausedJSON/".$dev."/".$signature."/".$session_id."/".$date;
				$ch = curl_init();
				curl_setopt($ch, CURLOPT_URL, $url);
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
				curl_setopt($ch, CURLOPT_TIMEOUT, 10);
				$resp = curl_exec($ch);
				var_dump(json_decode($resp, true));
				curl_close($ch);
			}
		}
	  $db = null;
	   } catch(PDOException $e) {
		     echo '{"error":{"text":'. $e->getMessage() .'}}';
	   }

})->name('getdataused');

$app->run();

?>
