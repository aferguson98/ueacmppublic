<?php
	if(!isset($_COOKIE['token'])){
		header("Location: login.html");
	}
	
	$m = new MongoDB\Driver\Manager("mongodb://username:password@host:port/table");
    $query = new MongoDB\Driver\Query(['token' => $_COOKIE['token']], []);
    $rows = $m->executeQuery('main.cmptokens', $query);
    foreach ($rows as $r) {
		$userid = $r->user;
	}
	
	$m = new MongoDB\Driver\Manager("mongodb://username:password@host:port/table");
	$query = new MongoDB\Driver\Query(['_id' => new MongoDB\BSON\ObjectID($userid)], []);
	$rows = $m->executeQuery('main.cmpusers', $query);
	foreach ($rows as $r) {
		$name = $r->username;
	}
	
	function getUserImage($name){
		$name = str_replace(' ','', $name);
		return strtolower($name);
	}
	
	function getThisAsDay($date){
		$printableDate = date_create_from_format('Y-m-d H:i:s', $date);
		$newDate = strtotime($date);
		$sevendays = strtotime('+7 days');
		if($newDate < $sevendays) {
			return date_format($printableDate,'D') . " at " . date_format($printableDate,'g:ia');
		}else{
			return date_format($printableDate,'d/m') . " at " . date_format($printableDate,'g:ia');
		}
	}
?>
<!DOCTYPE html>
<html>
	<head>
		<link href="images/favicon.png" type="image/png" rel="icon"/>
		<title>CMP Premium Plus - Home</title>
	</head>
	<body>
		<div class="dash-area">
			<nav>
				<img src="images/favicon.png" style="height: 100px;"/>
				<div class="menu">
					<div class="menu-item">Logged in as <?php echo($name);?></div>
					<div class="menu-item"><div class="img-icon"style="background: url('images/<?php echo(getUserImage($name));?>.jpg');"></div></div>
				</div>
			</nav>
			<div class="dash-container">
				<p><a href="#" class="menu-a">Events</a>  <a href="message.php" class="menu-a current">Group Chat</a></p>
				<img src="images/cmpchat.png" style="height: 50px;"/>
				<div class="scroll-box-chat"></div>
				<div class="chat-area"><textarea class="chat-holder" id="chat-start" onkeydown="checkSubmission(event);" placeholder="Press enter to send and shift+enter for a new line"></textarea></div>
				<div class="copyright-text">
					&copy; 2018 Norves. This website is owned and operated by <a href="https://norv.es">Norves UK</a>. Image by nicola j. patron under CC BY-SA 3.0 licence
				</div>
			</div>
		</div>
	</body>
	<link href="css/dash.css" type="text/css" rel="stylesheet"/>
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.8/jquery.min.js"></script>
	<script>
		$(document).ready(function(){
			setInterval(reloadPage(), 1000);
		});
	
		function checkSubmission(e){
			if (e.keyCode == 13 && !e.shiftKey){
				$('#chat-start').attr("disabled"); 
				sendMessage();
			}
		}
		
		function sendMessage(){
			var message = $('#chat-start');
			var xmlhttp = new XMLHttpRequest();
			xmlhttp.onreadystatechange = function() {
				if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
					reloadPage();
					$('#chat-start').val(""); 
					$('#chat-start').removeAttr("disabled"); 
				}
			};
			xmlhttp.open("GET", "sendmessage.php?msg=" + message , true);
			xmlhttp.send();
		}
		
		function reloadPage(){
			var xmlhttp = new XMLHttpRequest();
			xmlhttp.onreadystatechange = function() {
				if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
					$('.scroll-box-chat').append(xmlhttp.responseText);
				    var height = $('.scroll-box-chat')[0].scrollHeight;
				    $('.scroll-box-chat').scrollTop(height);
				}
			};
			xmlhttp.open("GET", "getmessage.php" , true);
			xmlhttp.send();
		}
	</script>
</html>