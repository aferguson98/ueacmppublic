<?php
	$eventid = $_GET['event'];
	$name = $_GET['name'];
	
	$m = new MongoDB\Driver\Manager("mongodb://username:password@host:port/table");
    $bulk = new MongoDB\Driver\BulkWrite();
	$bulk->update(['_id' => new MongoDB\BSON\ObjectID($eventid)], ['$pull' => ["going" => $name]], ['upsert' => false, 'multi' => false]);
	$rows = $m->executeBulkWrite('main.cmpevents', $bulk);
?>