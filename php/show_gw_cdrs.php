<?php 
$filename_db_gw = 'data/db_gw.sq3';

$conn = new PDO('sqlite:'.$filename_db_gw);

// check if table exists
$sql = "SELECT 1 FROM cdrs";
if ($conn->query($sql) === false){
	die(print_r($conn->errorInfo(),true)." in ".$filename_db_pbx);
}

echo("<pre>\r\n");

$sql ="PRAGMA table_info(cdrs);";
foreach($conn->query($sql) as $row){
	print $row['name'].";\t";
}
print "\r\n";

// fetch CDRs from DB
$sql = "SELECT * FROM cdrs";
$prepardStatement = $conn->prepare($sql);
$prepardStatement->execute();

foreach ($prepardStatement->fetchAll(PDO::FETCH_ASSOC) as $row) {
	foreach ($row as $value){
		print $value . ";\t";
	}
	print "\r\n";
}
echo("</pre>");

?>