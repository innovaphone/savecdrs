<?php
$filename_pbx_cdrs = 'data/cdr_pbx.txt';
$filename_gw_cdrs = 'data/cdr_gw.txt';
$filename_db_pbx = 'data/db_pbx.sq3';
$filename_db_gw = 'data/db_gw.sq3';

// all possible CDR columns for prepared SQL statement
$gw_cdr_columns = array(
	'event',
	'time',
	'date',
	'ref',
	'xleg',
	'xref',
	'dir',
	'src_if',
	'dst_if',
	'src_cgpn',
	'src_dgpn',
	'src_cdpn',
	'src_name',
	'src_url',
	'div_url',
	'dst_cgpn',
	'dst_dgpn',
	'dst_cdpn',
	'dst_name',
	'dst_url',
	'src_reg_name',
	'dst_reg_name',
	'bcaps',
	'cause',
	'alert_time',
	'connect_time',
	'disc_time',
	'srv_id',
	'charge_units',
	'charge_amount',
	'charge_curr',
	'pr_called'
);

$pbx_cdr_columns = array(
	'time',
	'date',
	'from',
	'msg'
);

// all PBX CDRs are events of type syslog
if ($_GET['event'] === 'syslog'){
	// insert CDR into DB
	insert_data($filename_db_pbx, $pbx_cdr_columns);
	// write CDR to TXT file
	write_cdr_to_file($filename_pbx_cdrs,http_build_query($_GET)."\r\n");
	
// GW CDRs are of various types like 'A:Call', 'B:Rel' etc.
} else {
	// insert CDR into DB
	insert_data($filename_db_gw, $gw_cdr_columns);
	// write CDR to TXT file
	write_cdr_to_file($filename_gw_cdrs,http_build_query($_GET)."\r\n");
}

function insert_data($db, $array){
    $placeholders = array_fill(0, count($array), '?');

    $columns = $values = array();
    foreach($array as $k => $r){
		$columns[] = ($r == 'from') ? 'device' : $r;
		$values[] = empty($_GET[$r]) ? null : $_GET[$r];
    }
	
	// create an SQLite DB connection using PDO
	// make sure directory "data" exists and is writable
	// make sure folowing extensions are enabled in php.ini
	// extension=php_pdo.dll
	// extension=php_pdo_sqlite.dll
	// you can easily switch to another DB like Postgres using PDO
	$conn = new PDO('sqlite:'.$db);
	
	// create table, if not exists
	//$query_create = 'CREATE TABLE IF NOT EXISTS cdrs (id INTEGER AUTO_INCREMENT PRIMARY KEY, '.
	$query_create = 'CREATE TABLE IF NOT EXISTS cdrs ('.
			implode(' TEXT, ',$columns).
			')';

	$q = $conn->query($query_create);
	
	// insert CDR
    $query_insert = 'INSERT INTO cdrs '.
             '('.implode(', ', $columns).') VALUES '.
             '('.implode(', ', $placeholders).')';
	
	if (!$preparedStatement = $conn->prepare($query_insert)){
		var_dump($preparedStatement);
		var_dump($conn->errorInfo());
		die("Prepare SQL statement failed: ".$query_insert);
	}
	
	if (!$preparedStatement->execute($values)){
		var_dump($conn->errorInfo());
		die("Execute prepared statement failed: ".$statement." Values: ".print_r($values,true));
	}
}

function write_cdr_to_file($filename, $line){

	// try to create file, if not exists
	if (!file_exists($filename)){
		if (!touch($filename)) {
			die("Can not create $filename");
		}
	}
	// check if existing file is writable
	if (is_writable($filename)) {

		// open the file to append lines
		if (!$handle = fopen($filename, "a")) {
			 print "Can not open file $filename";
			 exit;
		}

		// write line to file
		if (!fwrite($handle, $line)) {
			print "Can not write to file $filename";
			exit;
		}

		fclose($handle);

	} else {
		print "The file $filename is not writable";
	}

}
?>