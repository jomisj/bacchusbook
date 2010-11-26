<?php

/* ---------------------

------------------------ */

require_once("/Users/jomis_usr/Documents/Adobe Flash Builder 4/Wine_Desktop/php/XmlSerializer.class.php");

$conn = mysql_connect("localhost", "root", "root");
if (!$conn) {
	exit("Unable to connect to DB: " . mysql_error());
}

if (!mysql_select_db("wines")) {
	exit("Unable to select mydbname: " . mysql_error());
}

switch (@$_REQUEST["method"]) {
	case "FindAll":
		$ret = FindAllWines();
		// the XmlSerializer uses a PEAR xml parser to generate an xml response.
		$serializer = new XmlSerializer();
		echo $serializer->serialize($ret);
		break;
        case "FindAllbyYear":
		$ret = FindAllWines2();
		$serializer = new XmlSerializer();
		echo $serializer->serialize($ret);
                break;
	case "FindAll2":
		$ret = FindAllWines2();
		// the XmlSerializer uses a PEAR xml parser to generate an xml response.
		$serializer = new XmlSerializer();
		echo $serializer->serialize($ret);
		break;
	case "InsertWine":
		$ret = InsertWine();
		echo $ret;
		break;
        case "chartGroupbyYear":
                $ret = chartGroupbyYear();
		$serializer = new XmlSerializer();
		echo $serializer->serialize($ret);
                break;
	default:
		$ret = FindAllWines();
		// the XmlSerializer uses a PEAR xml parser to generate an xml response.
		$serializer = new XmlSerializer();
		echo $serializer->serialize($ret);
		break;
}


die();

function GetSQLValueString($theValue, $theType, $theDefinedValue = "", $theNotDefinedValue = "")
{
  $theValue = get_magic_quotes_gpc() ? stripslashes($theValue) : $theValue;

  $theValue = function_exists("mysql_real_escape_string") ? mysql_real_escape_string($theValue) : mysql_escape_string($theValue);

  switch ($theType) {
    case "text":
      $theValue = ($theValue != "") ? '"' . $theValue . '"' : "NULL";
      break;
    case "long":
    case "int":
      $theValue = ($theValue != "") ? intval($theValue) : "NULL";
      break;
    case "double":
      $theValue = ($theValue != "") ? '"' . doubleval($theValue) . '"' : "NULL";
      break;
    case "date":
      $theValue = ($theValue != "") ? '"' . $theValue . '"' : "NULL";
      break;
    case "defined":
      $theValue = ($theValue != "") ? $theDefinedValue : $theNotDefinedValue;
      break;
  }
  return $theValue;
}

function FindAllWines() {
	global $conn;

	$query_recordset = "SELECT wineID, wine_name, region, grape_type, appelation, classification, color, year, grading, comments FROM characteristics";
	$recordset = mysql_query($query_recordset, $conn);
	if (!$recordset) {
		echo "Could not successfully run query ($query_recordset) from DB: " . mysql_error();
		exit;
	}
	$toret = array();
	while ($row_recordset = mysql_fetch_assoc($recordset)) {
		array_push($toret, $row_recordset);

	}

	return array("data" => $toret);
}

function FindAllWines2() {
	global $conn;
        if (intval($_REQUEST["YearParm"])==0)
        {
            $query_recordset = "SELECT * FROM characteristics, bottles WHERE characteristics.wineID = bottles.wineID";
        }else {
            $query_recordset = sprintf("SELECT * FROM characteristics, bottles WHERE characteristics.wineID = bottles.wineID and characteristics.year=%d",
                                GetSQLValueString($_REQUEST["YearParm"], "int"));
        }
	$recordset = mysql_query($query_recordset, $conn);
	if (!$recordset) {
		echo "Could not successfully run query ($query_recordset) from DB: " . mysql_error();
		exit;
	}
	$toret = array();
	while ($row_recordset = mysql_fetch_assoc($recordset)) {
		array_push($toret, $row_recordset);

	}

	return array("data" => $toret);
}

function InsertWine() {
	global $conn;

	$query_insert = sprintf("INSERT INTO characteristics (wine_name, region, grape_type, appelation, classification, color, year, grading, comments) VALUES (%s, %s, %s, %s, %s, %s, %s, %u, %s)",
		GetSQLValueString($_REQUEST["wine_name"], "text"),
		GetSQLValueString($_REQUEST["region"], "text"),
		GetSQLValueString($_REQUEST["grape_type"], "text"),
		GetSQLValueString($_REQUEST["appelation"], "text"),
		GetSQLValueString($_REQUEST["classification"], "text"),
		GetSQLValueString($_REQUEST["color"], "text"),
		GetSQLValueString($_REQUEST["year"], "date"),
		GetSQLValueString($_REQUEST["grading"], "int"),
		GetSQLValueString($_REQUEST["comments"], "text")
	);

	$ok = mysql_query($query_insert,$conn);

	if (!$ok) {
		return 1;
		//exit("Unable to insert to DB: " . mysql_error());
	}
	else {
		return 0;
	}

}

function chartGroupbyYear() {
global $conn;

	$query_recordset = "SELECT COUNT(*) as Num, A.year FROM (SELECT B.*, C.year FROM bottles B, characteristics C WHERE B.wineID = C.wineID) A GROUP BY A.year";
	$recordset = mysql_query($query_recordset, $conn);
	if (!$recordset) {
		echo "Could not successfully run query ($query_recordset) from DB: " . mysql_error();
		exit;
	}
	$toret = array();
	while ($row_recordset = mysql_fetch_assoc($recordset)) {
		array_push($toret, $row_recordset);

	}

	return array("data" => $toret);
}

?>