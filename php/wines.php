<?php

/* ---------------------

------------------------ */

require_once("include.php");

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
	case "removeBottle":
		$ret = removeBottle();
		echo $ret;
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
        case "addAnotherBottle":
		$ret = addAnotherBottle();
		echo $ret;
		break;
        case "loadWineList":
                $ret = loadWineList();
                $serializer = new XmlSerializer();
		echo $serializer->serialize($ret);
                break;
        case "addNewBottle":
		$ret = addNewBottle();
		echo $ret;
		break;
	default:
		$ret = FindAllWines2();
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

/**************************************************************************************
                            SELECT 
                            B.bottleID, B.price, B. bottle_size, B.purchase_date, 
                            B.removal_date, B.eventID, B.drink_start, B.drink_end, 
                            B.best_start, B.best_end, B.comment, 
                            A.wineID, A.wine_name, A.region, A.grape_type, 
                            A.appelation, A.classification, A.color, 
                            A.year, A.grading, A.comments, B.locationID,
                            B.rackID, B.row, B.col, B.rack_name,
                            B.max_row, B.max_col
                            FROM 
                            (SELECT Y.*, E.comment
                            FROM
                            (SELECT Z.*, S.locationID,
                            S.rackID, S.row, S.col, S.rack_name,
                            S.max_row, S.max_col
                            FROM bottles Z
                            LEFT OUTER JOIN
                            (SELECT L.*, R.rack_name,
                            R.max_row, R.max_col
                            FROM location L
                            LEFT OUTER JOIN 
                            racks R
                            ON L.rackID = R.rackID) S
                            ON S.bottleID = Z.bottleID) Y
                            LEFT OUTER JOIN 
                            event E
                            ON E.eventID = Y.eventID ) B
                            LEFT OUTER JOIN  
                            characteristics A 
                            ON B.wineID = A.wineID
**************************************************************************************/
function FindAllWines2() {
	global $conn;

        /*$query_recordset = "SELECT 
                           B.bottleID, B.price, B. bottle_size, B.purchase_date, 
                           B.removal_date, B.eventID, B.drink_start, B.drink_end, 
                           B.best_start, B.best_end, B.comment, 
                           A.wineID, A.wine_name, A.region, A.grape_type, 
                           A.appelation, A.classification, A.color, 
                           A.year, A.grading, A.comments 
                           FROM 
                           (SELECT bottles.*, event.comment FROM bottles 
                           LEFT OUTER JOIN event 
                           ON bottles.eventID = event.eventID) B 
                           LEFT OUTER JOIN  
                           characteristics A 
                           ON B.wineID = A.wineID";*/

        $query_recordset = "SELECT 
                            B.bottleID, B.price, B. bottle_size, B.purchase_date, 
                            B.removal_date, B.eventID, B.drink_start, B.drink_end, 
                            B.best_start, B.best_end, B.comment, 
                            A.wineID, A.wine_name, A.region, A.grape_type, 
                            A.appelation, A.classification, A.color, 
                            A.year, A.grading, A.comments, B.locationID,
                            B.rackID, B.row, B.col, B.rack_name,
                            B.max_row, B.max_col
                            FROM 
                            (SELECT Y.*, E.comment
                            FROM
                            (SELECT Z.*, S.locationID,
                            S.rackID, S.row, S.col, S.rack_name,
                            S.max_row, S.max_col
                            FROM bottles Z
                            LEFT OUTER JOIN
                            (SELECT L.*, R.rack_name,
                            R.max_row, R.max_col
                            FROM location L
                            LEFT OUTER JOIN 
                            racks R
                            ON L.rackID = R.rackID) S
                            ON S.bottleID = Z.bottleID) Y
                            LEFT OUTER JOIN 
                            event E
                            ON E.eventID = Y.eventID ) B
                            LEFT OUTER JOIN  
                            characteristics A 
                            ON B.wineID = A.wineID";

        if (intval($_REQUEST["YearParm"])==0)
        {
             $query_recordset = $query_recordset . " WHERE B.removal_date IS NULL ORDER BY B.bottleID";

        }else {
            $query_recordset = sprintf("%s WHERE A.year=%d AND B.removal_date IS NULL ORDER BY B.bottleID",
                                $query_recordset,
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

function loadWineList() {
	global $conn;

        $query_recordset = "SELECT * FROM characteristics A ORDER BY A.wine_name";

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

        echo "Event ID..........".GetSQLValueString($_REQUEST["eventID"],"int");
        if (((GetSQLValueString($_REQUEST["eventID"], "int")) > 0))
        {
                $query_update_ev = sprintf("UPDATE event SET comment = %s  WHERE eventID=%u",
                        GetSQLValueString($_REQUEST["event_comment"], "text"),
                        GetSQLValueString($_REQUEST["eventID"], "int")
                        );
            echo $query_update_ev;
            $ok = mysql_query($query_update_ev,$conn);
            if (!$ok) {
                    return 1;
            }
        } else if ((GetSQLValueString($_REQUEST["event_comment"], "text")) != "NULL") {
                    $query_event_max = "SELECT MAX(eventID) as MNUM FROM event";
                    $recordset = mysql_query($query_event_max,$conn);
                    $rs = mysql_fetch_assoc($recordset);
                    $maxnum = intval($rs['MNUM']);
                    $maxnum++;
                    $query_update_ev = sprintf("INSERT INTO event (eventID, comment) VALUES (%u, %s)",
                            $maxnum,
                            GetSQLValueString($_REQUEST["event_comment"], "text")
                            );
                    $_REQUEST["eventID"] = $maxnum;
                    echo $query_update_ev;
                    $ok = mysql_query($query_update_ev,$conn);
                    if (!$ok) {
                            return 1;
                    }
        }
        
        echo "Event ID......".$_REQUEST["eventID"];
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
        //echo $query_update_ch;
        /*$query_update_bt = sprintf("UPDATE bottles SET price = %f, bottle_size=%s, purchase_date=%s, removal_date=%s, eventID=%u, drink_start=%u, drink_end=%u, best_start=%u, best_end=%u WHERE bottleID = %u",
		GetSQLValueString($_REQUEST["price"], "test"),
		GetSQLValueString($_REQUEST["bottle_size"], "text"),
		GetSQLValueString($_REQUEST["purchase_date"], "date"),
		GetSQLValueString($_REQUEST["removal_date"], "date"),
                GetSQLValueString($_REQUEST["eventID"], "int"),
		GetSQLValueString($_REQUEST["drink_start"], "int"),
		GetSQLValueString($_REQUEST["drink_end"], "int"),
		GetSQLValueString($_REQUEST["best_start"], "int"),
		GetSQLValueString($_REQUEST["best_end"], "int"),
		GetSQLValueString($_REQUEST["bottleID"], "int")
	);*/

        $query_update_bt = sprintf("UPDATE bottles SET price = %f, bottle_size=%s, purchase_date=%s, removal_date=%s, ",
            	GetSQLValueString($_REQUEST["price"], "test"),
		GetSQLValueString($_REQUEST["bottle_size"], "text"),
		GetSQLValueString($_REQUEST["purchase_date"], "date"),
		GetSQLValueString($_REQUEST["removal_date"], "date")
                );


        if ($maxnum > 0){
        $query_update_bt = $query_update_bt . sprintf("eventID=%u, ",
                GetSQLValueString($_REQUEST["eventID"], "int")  
                );
        }

        $query_update_bt = $query_update_bt . sprintf("drink_start=%u, drink_end=%u, best_start=%u, best_end=%u WHERE bottleID = %u",
		GetSQLValueString($_REQUEST["drink_start"], "int"),
		GetSQLValueString($_REQUEST["drink_end"], "int"),
		GetSQLValueString($_REQUEST["best_start"], "int"),
		GetSQLValueString($_REQUEST["best_end"], "int"),
		GetSQLValueString($_REQUEST["bottleID"], "int")
	);

    /*$query_update_bt = sprintf("UPDATE bottles SET price = %f, bottle_size=%s, purchase_date=%s, removal_date=%s, eventID=%u, drink_start=%u, drink_end=%u, best_start=%u, best_end=%u WHERE bottleID = %u",
		GetSQLValueString($_REQUEST["price"], "test"),
		GetSQLValueString($_REQUEST["bottle_size"], "text"),
		GetSQLValueString($_REQUEST["purchase_date"], "date"),
		GetSQLValueString($_REQUEST["removal_date"], "date"),
                GetSQLValueString($_REQUEST["eventID"], "int"),
		GetSQLValueString($_REQUEST["drink_start"], "int"),
		GetSQLValueString($_REQUEST["drink_end"], "int"),
		GetSQLValueString($_REQUEST["best_start"], "int"),
		GetSQLValueString($_REQUEST["best_end"], "int"),
		GetSQLValueString($_REQUEST["bottleID"], "int")
	);*/


        echo $query_update_bt;

	$ok = mysql_query($query_update_ch,$conn);

	if (!$ok) {
		return 1;
	}
	else {
		$ok = mysql_query($query_update_bt,$conn);
                if (!$ok) {
                        return 1;
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
	}
	else {
		return 0;
	}

}

//Line added after svn install. Testing the changes
function chartGroupbyYear() {
global $conn;

	$query_recordset = "SELECT COUNT(*) as Num, A.year FROM (SELECT B.*, C.year FROM bottles B, characteristics C WHERE B.wineID = C.wineID and B.removal_date IS NULL) A GROUP BY A.year";
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

function removeBottle() {
global $conn;
    $today = getdate();
    $rem_date = sprintf("%u-%u-%u",$today[year],$today[mon],$today[mday]);

    $query_update_bt = sprintf("UPDATE bottles SET removal_date='%s' WHERE bottleID = %u",
		$rem_date,
		GetSQLValueString($_REQUEST["bottleID"], "int")
	);
    $query_update_ch = sprintf("UPDATE characteristics SET comments = %s WHERE wineID=%u",
                GetSQLValueString($_REQUEST["comments"], "text"),
                GetSQLValueString($_REQUEST["wineID"], "int")
        );

    //echo $query_update_bt;
    $ok = mysql_query($query_update_bt,$conn);

	if (!$ok) {
		return 1;
	}
	else {
            $ok = mysql_query($query_update_ch,$conn);        
            if (!$ok) {
                    return 1;
            }
            else {            
		return 0;
            }
	}
}

function addAnotherBottle() {
	global $conn;

        if ((GetSQLValueString($_REQUEST["event_comment"], "text")) != "NULL")
        {
            $query_event_max = "SELECT MAX(eventID) as MNUM FROM event";
            $recordset = mysql_query($query_event_max,$conn);
            $rs = mysql_fetch_assoc($recordset);
            $maxnum = intval($rs['MNUM']);
            $maxnum++;
            $query_insert_ev = sprintf("INSERT INTO event (eventID, comment) VALUES (%u, %s)",
                    $maxnum,
                    GetSQLValueString($_REQUEST["event_comment"], "text")
                    );

            echo $query_insert_ev;

            $ok = mysql_query($query_insert_ev,$conn);
            if (!$ok) {
                    return 1;
            }

            $_REQUEST["eventID"] = $maxnum;
        }else{
            $_REQUEST["eventID"] = "NULL";
        }

	/*$query_update_ch = sprintf("UPDATE characteristics SET wine_name = %s, region=%s, grape_type=%s, appelation=%s, classification=%s, color=%s, year=%s, grading=%u, comments=%s WHERE wineID=%u",
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
	);*/
        
        $query_insert_bt = "INSERT INTO bottles (wineID, price, bottle_size, purchase_date, ";
        if ((GetSQLValueString($_REQUEST["event_comment"], "text")) != "NULL"){
            $query_insert_bt = $query_insert_bt . "eventID, ";
        }
        $query_insert_bt = $query_insert_bt."drink_start, drink_end, best_start, best_end) VALUES (";

        $query_insert_bt= $query_insert_bt.sprintf("%u, %f, %s, %s, ",
	        GetSQLValueString($_REQUEST["wineID"], "int"),
        	GetSQLValueString($_REQUEST["price"], "test"),
		GetSQLValueString($_REQUEST["bottle_size"], "text"),
		GetSQLValueString($_REQUEST["purchase_date"], "date"));

        if ((GetSQLValueString($_REQUEST["event_comment"], "text")) != "NULL"){
            $query_insert_bt = $query_insert_bt.$maxnum.", ";
        }

        $query_insert_bt= $query_insert_bt.sprintf("%s, %s, %s, %s)",
		GetSQLValueString($_REQUEST["drink_start"], "int"),
		GetSQLValueString($_REQUEST["drink_end"], "int"),
		GetSQLValueString($_REQUEST["best_start"], "int"),
		GetSQLValueString($_REQUEST["best_end"], "int")
	);
        echo $query_insert_bt;

	$ok = mysql_query($query_insert_bt,$conn);

	if (!$ok) {
		return 1;
	}else{
                return 0;
	}
}

function addNewBottle(){
	global $conn;

        //echo "chUpdate Flag is:".$_REQUEST["chUpdate"]."......";
        if ($_REQUEST["chUpdate"] == "true") {


            $query_ch_max = "SELECT MAX(wineID) as MNUM FROM characteristics";
            $recordset = mysql_query($query_ch_max,$conn);
            $rs = mysql_fetch_assoc($recordset);
            $maxnum = intval($rs['MNUM']);
            $maxnum++;
            //echo "MaxNum is.....".$maxnum.".......";

            $query_insert_ch = sprintf("INSERT INTO characteristics (wineID, wine_name, region, grape_type, appelation, classification, color, year, grading, comments) VALUES (%u, %s, %s, %s, %s, %s, %s, %s, %u, %s)",
                    $maxnum,
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

            //echo $query_insert_ch;
            $ok = mysql_query($query_insert_ch,$conn);

            if (!$ok) {
                    return 1;
            }
            $_REQUEST["wineID"] = $maxnum;

        }

        $query_insert_bt = sprintf("INSERT INTO bottles (wineID, price, bottle_size, purchase_date, drink_start, drink_end, best_start, best_end) VALUES (%u,%f,%s,%s,%s,%s,%s,%s)",
                GetSQLValueString($_REQUEST["wineID"], "int"),
		GetSQLValueString($_REQUEST["price"], "test"),
		GetSQLValueString($_REQUEST["bottle_size"], "text"),
		GetSQLValueString($_REQUEST["purchase_date"], "date"),
		GetSQLValueString($_REQUEST["drink_start"], "int"),
		GetSQLValueString($_REQUEST["drink_end"], "int"),
		GetSQLValueString($_REQUEST["best_start"], "int"),
		GetSQLValueString($_REQUEST["best_end"], "int")
                );
        
        //echo $query_insert_bt;
        $ok = mysql_query($query_insert_bt,$conn);

        if (!$ok) {
                return 1;
        }
        else{
            return 0;
        }
}

?>