<?php 
	$db = $m = new MongoDB\Driver\Managermysqli_connect("host", "username", "password", "database");
	$id = mysqli_real_escape_string($db, $_POST['user']);
	$pass = mysqli_real_escape_string($db, $_POST['pass']);
	$id = str_replace('%','', $id);
	require('detect.php');
	
	function strToHex($string){
		$hex = '';
		for ($i=0; $i<strlen($string); $i++){
			$ord = ord($string[$i]);
			$hexCode = dechex($ord);
			$hex .= substr('0'.$hexCode, -2);
		}
		return strToUpper($hex);
	}
	
    $m = new MongoDB\Driver\Manager("mongodb://username:password@host:port/table");
    $query = new MongoDB\Driver\Query(['username' => $id], []);
    $rows = $m->executeQuery('main.cmpusers', $query);
    $countrows = count($rows);
	$i = 0;
    if($countrows == 0){
        echo("0");
		$i++;
    }elseif($countrows == 1){
        foreach ($rows as $r) {
            $correct = $r->password;
            $userid = $r->_id;
			if ($pass == $correct) {
				$token = strToHex($userid . time());
				$ip = Detect::ip();
				$device = Detect::deviceType() . " (". Detect::brand() . ")";
				$location = Detect::ipCountry();
				$useros = Detect::os();
				$browser = Detect::browser();
				$bulk = new MongoDB\Driver\BulkWrite();
				$bulk->insert(['token' => $token, 'ipaddress' => $ip, 'country' => $location, 'device' => $device, 'os' => $useros, 'browser' => $browser, 'user' => $userid]);
				$rows = $m->executeBulkWrite('main.cmptokens', $bulk);
				echo($token);
				$i++;
			}
        }
    }else{
		echo("0");
		$i++;
	}
	
	if($i == 0){
		echo("0");
		$i++;
	}
?>