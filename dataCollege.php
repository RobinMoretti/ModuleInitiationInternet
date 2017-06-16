<?php
$GLOBALS['plot_db'] = db_connect();

$feed = isset($_REQUEST['feed']);
$click = isset($_REQUEST['click']);
$dump = isset($_REQUEST['dump']);
$Sendreponse= isset($_REQUEST['reponse']);

$offset = $_REQUEST['offset'];
$limit = $_REQUEST['limit'];
$ip = $_REQUEST['ip'];
$last = $_REQUEST['last'];

if ($ip==true) {
	die($_SERVER['SERVER_ADDR']);
}


if ($feed == true) {
  	$json = array();
  	
  	$object = json_decode(file_get_contents('php://input'));
  	//echo file_get_contents('php://input');
  	$click = $object->{'click'};
  	$ip = $object->{'ip'};
	
  	$request = "INSERT INTO dataCollege ('click', 'ip') VALUES ($click, \"$ip\");";

  	error_log($request);
	echo $request;

  	if ($GLOBALS['plot_db']->exec($request)) {
    	$json['error'] = "ok";
  	} else {
      	$json['error'] = "error";
  	}

   	die(json_encode($json));
}

if ($Sendreponse == true) {

  	$json = array();
  	
  	$object = json_decode(file_get_contents('php://input'));
  	//echo file_get_contents('php://input');
  	$reponse = $object->{'reponse'};
  	$id = $object->{'id'};
	
  	$request = 'UPDATE dataCollege SET reponse=" '.$reponse.'" WHERE id='.$id.';';

  	error_log($request);
	echo $request;

  	if ($GLOBALS['plot_db']->exec($request)) {
    	$json['error'] = "ok";
  	} else {
      	$json['error'] = "error";
  	}

   	die(json_encode($json));
}

if ($dump == true) {
	
	// construction de la requette sql
	$request = "SELECT * FROM dataCollege";
	
	if ($limit != null) {
		$request .= " LIMIT ".$limit;
		
		if ($offset != null) {
			$request .= " OFFSET ".$offset;
		}
	} else if ($last != null) {
		$request .= "  ORDER BY id DESC LIMIT 1";
	}
	
  	$results = $GLOBALS['plot_db']->query($request);
	$dataCollege = array();

 	while($res = $results->fetchArray(SQLITE3_ASSOC)){
		$datas = array();

		$datas['id'] = 				$res['id'];
		$datas['click'] = 				$res['click'];
		$datas['ip'] = 				$res['ip'];
		$datas['reponse'] = 				$res['reponse'];
		
		$dataCollege["data"][] = $datas;
	}
  	$dataCollege['error'] = "ok";

	$GLOBALS['plot_db']->close();
	die(json_encode($dataCollege));
}

function db_connect() {

  class DB extends SQLite3 {
    function __construct( $file ) {
      $this->open( $file,SQLITE3_OPEN_READWRITE | SQLITE3_OPEN_CREATE);
    }
  }

  $adb = new DB('dataCollege.db');

  if ($adb->lastErrorMsg() != 'not an error') {
    error_log("Database Error: " . $adb->lastErrorMsg()."\n",3);
  }
  return $adb;
}

?>