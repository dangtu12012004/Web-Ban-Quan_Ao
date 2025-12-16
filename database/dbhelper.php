<?php
require_once(__DIR__ . '/config.php');

function execute($sql)
{
	//save data into table
	// open connection to database
	$con = mysqli_connect(HOST, USERNAME, PASSWORD, DATABASE);
    mysqli_set_charset($con, 'UTF8');
	//insert, update, delete
	mysqli_query($con, $sql);

	//close connection
	mysqli_close($con);
}

function executeResult($sql)
{
	//save data into table
	// open connection to database
	$con = mysqli_connect(HOST, USERNAME, PASSWORD, DATABASE);
    mysqli_set_charset($con, 'UTF8');
	//insert, update, delete
	$result = mysqli_query($con, $sql);
	$data   = [];
	if ($result) {
		while ($row = mysqli_fetch_array($result, 1)) {
			$data[] = $row;
		}
	}
	mysqli_close($con);
	return $data;
}

function executeSingleResult($sql)
{
    $con = mysqli_connect(HOST, USERNAME, PASSWORD, DATABASE);
    mysqli_set_charset($con, 'UTF8');

    $result = mysqli_query($con, $sql);
    if (!$result) {
        mysqli_close($con);
        return null;
    }

    $row = mysqli_fetch_array($result, MYSQLI_ASSOC);
    mysqli_close($con);
    return $row;
}
