<?php
//ini_set('display_errors', 1);
//ini_set('display_startup_errors', 1);
//error_reporting(E_ALL);
// *********************************************************************

require_once("inc/tools.php");

$countSQL = "select count(*) ANZ from HODLKEYS where EGROUP='HODLERSTATUS'";
$countRES = dbQuery($countSQL);
$countARR = dbFetchArr($countRES);

if(0+$countARR["ANZ"] != 8818) {
    echo "creating!<BR>\n";
    for($i=0; $i < 8818; $i++) {
        updateStoreValue("ID_".($i+1),"0","HODLERSTATUS");
    }
} else {
    echo "exists!<BR>\n";
}
echo "done";