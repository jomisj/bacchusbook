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
        case "updateWine":
                $ret = updateWine();
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

function updateWine() {
	global $conn;

	$query_update_ch = sprintf("UPDATE characteristics SET wine_name = %s, region=%s, grape_type=%s, appelation=%s, classification=%s, color=%s, year=%s, grading=%u, comments=%s WHERE wineID=%u",
		GetSQLValueString($_REQUEST["wine_name"], "text"),
		GetSQLValueString($_REQUEST["region"], "text"),
		GetSQLValueString($_REQUEST["grape_type"], "text"),
		GetSQLValueString($_REQUEST["appelation"], "text"),
		GetSQLValueString($_REQUEST["classification"], "text"),
		GetSQLValueString($_REQUEST["color"], "text"),
		GetSQLValueString($_REQUEST["year"], "date"),
		GetSQLValueString($_REQUEST["grading"], "int"),
		GetSQLValueString($_REQUEST["comments"], "text"),
                GetSQLValueString($_REQUEST["wineID"], "int")
	);

        $query_update_bt = sprintf("UPDATE bottles SET price = %f, bottle_size=%s, purchase_date=%s, removal_date=%s, drink_start=%u, drink_end=%u, best_start=%u, best_end=%u WHERE bottleID = %u",
		GetSQLValueString($_REQUEST["price"], "test"),
		GetSQLValueString($_REQUEST["bottle_size"], "text"),
		GetSQLValueString($_REQUEST["purchase_date"], "date"),
		GetSQLValueString($_REQUEST["removal_date"], "date"),
		GetSQLValueString($_REQUEST["drink_start"], "int"),
		GetSQLValueString($_REQUEST["drink_end"], "int"),
		GetSQLValueString($_REQUEST["best_start"], "int"),
		GetSQLValueString($_REQUEST["best_end"], "int"),
		GetSQLValueString($_REQUEST["bottleID"], "int")
	);


	$ok = mysql_query($query_update_ch,$conn);

	if (!$ok) {
		return 1;
		//exit("Unable to insert to DB: " . mysql_error());
	}
	else {
		$ok = mysql_query($query_update_bt,$conn);
                if (!$ok) {
                        return 1;
                        //exit("Unable to insert to DB: " . mysql_error());
                }
                else {
                    return 0;
                }
	}
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

//Line added after svn install. Testing the changes
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