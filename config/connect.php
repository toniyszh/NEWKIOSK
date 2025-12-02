<?php
	// $main_serverName = "117.120.7.147"; // LIVE SERVER
	// $serverUserName = "sa";
	// $serverPassword = "pscP@ssw0rd123";

	$main_serverName = "192.168.100.136"; // LIVE SERVER
	$serverUserName = "sa";
	$serverPassword = "psc";
	
	$_SESSION['main_ipaddress'] = $main_serverName;
	$_SESSION['showdepartment'] = 'no';
	//$clientcode = trim($_SESSION['clientcode']);
	$clientcode = "RWPOS";

	$serverDatabase = "WeeDoo";

	$connectionInfo = array( "Database"=>"$serverDatabase", "UID"=>$serverUserName, "PWD"=>$serverPassword);
	$main_conn = sqlsrv_connect( $main_serverName, $connectionInfo);
	
	$pos_serverName = "192.168.100.136"; // POS SERVER
	$pos_database = "SAMGYUPMALATE";
	$pos_serverUserName = "sa";
	$pos_serverPassword = "psc";

	$pos_connectionInfo = array( "Database"=>$pos_database, "UID"=>$pos_serverUserName, "PWD"=>$pos_serverPassword);
	$pos_conn = sqlsrv_connect( $pos_serverName, $pos_connectionInfo);
	

	if( trim($clientcode) == "" ) {
		
		ini_set('display_errors', 1);
		ini_set('display_startup_errors', 1);
		error_reporting(E_ALL);
		//echo '<script>window.location="http://weedoo.ph"</script>';
		exit();
	}

	$_SESSION['serverdatabase'] = $serverDatabase;

	$query = "SELECT * from merchant where code = '" . $clientcode ."'";
	$result = sqlsrv_query($main_conn,$query);

	while( $row = sqlsrv_fetch_array( $result, SQLSRV_FETCH_ASSOC)) {

		$serverDatabase = trim($row['databasename']);
		$serverName = trim($row['ipaddress']);
		$restoname = trim($row['merchantname']);
		$companyid = $row['companyid'];

	}

	$_SESSION['databasename'] = $serverDatabase;
	$_SESSION['storename'] = trim($restoname);
	$_SESSION['companyid'] = $companyid
	;

	$connectionInfo = array( "Database"=>$serverDatabase , "UID"=>$serverUserName, "PWD"=>$serverPassword);
	$conn = sqlsrv_connect( $serverName, $connectionInfo);

	if( $conn === false ) {
		 die( print_r( sqlsrv_errors(), true));

  }


	if( $conn ) {
		//echo "Connection established.<br />";
		echo "<br />";
		 $connstatus = "Connected";
	}else{
		 echo "Connection could not be established.<br />";
		 $connstatus = "Not Connected";
		 die( print_r( sqlsrv_errors(), true));
	}

	$_SESSION['conn_status'] = $connstatus;

	// for reference please watch this video on how to connecto SQL SERVER by configuring Php.ini
	//https://www.youtube.com/watch?v=upvALf8zJXg

?>
