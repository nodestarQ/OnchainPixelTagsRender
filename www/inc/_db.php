<?php
// DB SETTINGS
// ------------------------ DB
$GLOBALS["dbcon"] = mysqli_connect($GLOBALS["config"][$useConf]["db"]["host"], $GLOBALS["config"][$useConf]["db"]["user"],$GLOBALS["config"][$useConf]["db"]["pw"], $GLOBALS["config"][$useConf]["db"]["dbname"]) or die("Error: Could not connect to database!");
mysqli_set_charset($GLOBALS["dbcon"], "utf8");


// ------------------------------------------------- DB tools
function dbQuery($strSQL) {
    //if($link=="") $link = $GLOBALS["dbcon"];
    $result = mysqli_query($GLOBALS["dbcon"],$strSQL)
    or _mymail("rs@metadist.de","rs@metadist.de","ENVO - query failed in SQL... ",
        __FILE__." ".__LINE__."\n<BR>".$strSQL."<BR>\n".dbError()."<BR>\n<pre>".print_r($_SERVER,true)."</pre>",
        $strSQL."<BR>\n".dbError());

    // myDebug(substr(strip_tags($strSQL),0,800),__FUNCTION__,__LINE__);
    if(!$result) {
        return false;
    }
    else {
        return $result;
    }
}

function dbFetchArr($res,$dummy="") {
    return mysqli_fetch_array($res, MYSQLI_ASSOC);
}

function dbCountRows($res) {
    return mysqli_num_rows($res);
}

function dbAffectedRows() {
    return mysqli_affected_rows($GLOBALS["dbcon"]);
}

function dbEscString($str) {
    $newStr = mysqli_real_escape_string($GLOBALS["dbcon"],$str);
    $newStr = str_replace("\\/","/", $newStr);
    return $newStr;
}

function dbLastId() {
    return mysqli_insert_id($GLOBALS["dbcon"]);
}

function dbError() {
    return mysqli_error($GLOBALS["dbcon"]);
}