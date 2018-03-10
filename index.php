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
				<p><a href="#" class="menu-a current">Events</a>  <a href="message.php" class="menu-a">Group Chat</a></p>
				<img src="images/cmpevents.png" style="height: 100px;"/>
				<table class="events">
					<tr>
						<th>Event</th>
						<th>When?</th>
						<th><img src="images/food.png" style="height: 25px;"/></th>
						<th><img src="images/snack.png" style="height: 25px;"/></th>
						<th><img src="images/drink.png" style="height: 25px;"/></th>
						<th><img src="images/fun.png" style="height: 25px;"/></th>
						<th>Who</th>
						<th>Going?</th>
					</tr>					
					<?php	
						$m = new MongoDB\Driver\Manager("mongodb://username:password@host:port/table");
						$query = new MongoDB\Driver\Query([], []);
						$rows = $m->executeQuery('main.cmpevents', $query);
						foreach ($rows as $r) {
							echo("<tr>");
								echo("<td>".$r->name."</td>");
								echo("<td>".getThisAsDay($r->dati)."</td>");
								echo("<td><img src='images/".$r->images[0].".png'/></td>");
								echo("<td><img src='images/".$r->images[1].".png'/></td>");
								echo("<td><img src='images/".$r->images[2].".png'/></td>");
								echo("<td><img src='images/".$r->images[3].".png'/></td>");
								echo("<td>");
									foreach($r->going as $g){
										echo("<div class='img-icon-small' style='background: url(\"images/" . getUserImage($g) . ".jpg\");'></div>");
									}
								echo("</td>");
								echo("<td><div class='going-area'><div class='going-button' onclick='setYesEvent(\"" . $r->_id . "\", \"" . $name . "\")'>Yes</div><div class='going-button' onclick='setNoEvent(\"" . $r->_id . "\", \"" . $name . "\")'>No</div></div></td>");
							echo("</tr>");
						}
					?>
				</table>
				<div class="copyright-text">
					&copy; 2018 Norves. This website is owned and operated by <a href="https://norv.es">Norves UK</a>. Image by nicola j. patron under CC BY-SA 3.0 licence
				</div>
			</div>
		</div>
	</body>
	<link href="css/dash.css" type="text/css" rel="stylesheet"/>
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.8/jquery.min.js"></script>
	<script>
		function setYesEvent(eventid, name){
			var xmlhttp = new XMLHttpRequest();
			xmlhttp.onreadystatechange = function() {
				if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
					window.location.href='index.php';
				}
			};
			xmlhttp.open("GET", "yestoevent.php?event=" + eventid + "&name=" + name , true);
			xmlhttp.send();
		}
		
		function setNoEvent(eventid, name){
			var xmlhttp = new XMLHttpRequest();
			xmlhttp.onreadystatechange = function() {
				if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
					window.location.href='index.php';
				}
			};
			xmlhttp.open("GET", "notoevent.php?event=" + eventid + "&name=" + name , true);
			xmlhttp.send();
		}
	</script>
</html>