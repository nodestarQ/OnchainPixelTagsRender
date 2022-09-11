<?php
// ------------------------- config for passwords
// SESSIONS for everything
session_start();
// ---------------------------------------------------------------------------------------------------------------------
$GLOBALS["config"] = readconfig();
$useConf = "production";
// ---------------------------------------------------------------------------------------------------------------------
if(isset($_REQUEST["action"])) {
    if($_REQUEST["action"]=="logout") {
        $_SESSION["protected"]=false;
        session_destroy();
    }
}

// SITE VARS
$baseref=$GLOBALS["config"]["production"]["baseref"];
$cdnref=$baseref;
$prodenv = true;
$useConf = "production";

// my local development settings
// override stuff, if local...
if(substr_count($_SERVER["REQUEST_URI"],"/envoverse/")>0) {
    $baseref=$GLOBALS["config"]["dev"]["baseref"];
    $cdnref=$baseref;
    $prodenv = false;
    $useConf = "dev";
}

// ----------------------------------- unique group turing
// helps to keep the spammers away
// -------------------------------------------------------
if(!isset($_SESSION["TUR"])) {
    $_SESSION["TUR"]= "C_".strtoupper($_SERVER['HTTP_CF_IPCOUNTRY'])."_".date("YmdHis")."_". (dechex(rand(1,100000)));
}

// DB connect
// require_once(__DIR__ . "/_db.php");

// load all the composer stuff
// require_once(__DIR__ . "/../vendor/autoload.php");

//------------------------------------------------------------------------
function readConfig() {
    $configFile = file_get_contents(__DIR__ ."/../../projectConfig.json");
    $configArr = json_decode($configFile, true);
    return $configArr;
}
//
function givePolicycountries() {
    $VATrateArr = Array();
    $VATrateArr["AT"] = "Austria;20;0;0";
    $VATrateArr["BE"] = "Belgium;21;0;0";
    $VATrateArr["BG"] = "Bulgaria;20;0;0";
    $VATrateArr["HR"] = "Croatia;25;0;0";
    $VATrateArr["CY"] = "Cyprus;19;0;0";
    $VATrateArr["CZ"] = "Czech Republic;21;0;0";
    $VATrateArr["DK"] = "Denmark;25;0;0";
    $VATrateArr["EE"] = "Estonia;20;0;0";
    $VATrateArr["FI"] = "Finland;24;0;0";
    $VATrateArr["FR"] = "France;20;0;0";
    $VATrateArr["DE"] = "Germany;19;16;20210101";
    $VATrateArr["EL"] = "Greece;24;0;0";
    $VATrateArr["HU"] = "Hungary;27;0;0";
    $VATrateArr["IE"] = "Ireland;23;0;0";
    $VATrateArr["IT"] = "Italy;22;0;0";
    $VATrateArr["LV"] = "Latvia;21;0;0";
    $VATrateArr["LT"] = "Lithuania;21;0;0";
    $VATrateArr["LU"] = "Luxembourg;17;0;0";
    $VATrateArr["MT"] = "Malta;18;0;0";
    $VATrateArr["NL"] = "Netherlands;21;0;0";
    $VATrateArr["PL"] = "Poland;23;0;0";
    $VATrateArr["PT"] = "Portugal;23;0;0";
    $VATrateArr["RO"] = "Romania;19;0;0";
    $VATrateArr["SK"] = "Slovakia;20;0;0";
    $VATrateArr["SI"] = "Slovenia;22;0;0";
    $VATrateArr["ES"] = "Spain;21;0;0";
    $VATrateArr["SE"] = "Sweden;25;0;0";
    $VATrateArr["UK"] = "United Kingdom;20;0;0";
    $VATrateArr["US"] = "United States;0;0;0";
    return $VATrateArr;
}

function triggerCookiePolicy() {
    $callArr = Array();
    $callArr["COUNTRY"] = strtoupper($_SERVER['HTTP_CF_IPCOUNTRY']);
    $callArr["REMOTEIP"] = $_SERVER['HTTP_CF_CONNECTING_IP'];
    $countryArr = givePolicycountries();

    if(key_exists($callArr["COUNTRY"], $countryArr) OR $callArr["COUNTRY"]=="") {
        return 1;
    } else {
        return 0;
    }
}

function updateStoreValue($key, $value, $group="") {
    $walletSQL = "delete from " . ($GLOBALS["config"]["keytable"]) . " where EGROUP = '".dbEscString($group)."' AND EKEY='".$key."'";
    $wRes = dbQuery($walletSQL);
    $walletSQL = "insert into " . ($GLOBALS["config"]["keytable"]) . " (EID, ELASTDATE, EGROUP, EKEY, EVALUE) values ";
    $walletSQL .= "(DEFAULT, '".date("Y-m-d H:i:s")."', '".$group."','".$key."','".$value."')";
    $wRes = dbQuery($walletSQL);
}
// ------------------------------------------------- other base tools
function sysString($inStr) {
    $outStr = preg_replace('([^\w\s\d\-\_])', '', $inStr);
    return $outStr;
}
// -------------------
// ------------------------------------ my mail sender
function _mymail($strFrom, $strTo,$subject,$htmltext,$plaintext,$strReplyTo='',$strFileAttach='')
{
    // available SMTP providers
    $arrSmtpSrvs = array(
        'runbox'    => array('h' => 'mail.runbox.com',
            'auth' => true, 'po'=>587, 'u' => "puzzler",
            'p' => "ma5ter99", 'f' => "puzzler@runbox.com",
            'fn' => 'ROAYALE Delivery')
    );

    // create the mail
    $mail = new PHPMailer\PHPMailer\PHPMailer;
    $mail->isSMTP();

    if (($p=strpos($strTo, ";")) !== false) {
        list($strTo, $strToName) = explode(";", $strTo);
        $mail->addAddress($strTo, $strToName);
    } else {
        $mail->addAddress($strTo, "ROYALE Mail");
    }

    // - reply
    if ($strReplyTo) {
        $mail->addReplyTo($strReplyTo);
    }
    elseif(substr_count($strFrom,";")>0) {
        $fromparts=explode(";",$strFrom);
        $mail->addReplyTo($fromparts[0], $fromparts[1]);
    }
    else {
        $mail->addReplyTo($strFrom, "ROYALE System Mail");
    }

    if(strlen($strFileAttach)>5)
    {
        if(file_exists($strFileAttach))
            $mail->addAttachment($strFileAttach);
    }

    $mail->WordWrap = 70;
    $mail->isHTML(true);

    // WHICH provider for mails do we use?
    $strUseProvider = 'runbox';

    // create settings list
    list($mail->Host, $mail->SMTPAuth, $mail->Port, $mail->Username, $mail->Password, $mail->From, $mail->FromName) = array_values($arrSmtpSrvs[$strUseProvider]);
    $mail->SMTPSecure = "tls";
    //$mail->SMTPDebug = 3;

    $mail->Subject = $subject;
    $mail->Body    = $htmltext;

    $plaintext = $plaintext."\n\n"."admin@envolabs.io";
    $mail->AltBody = $plaintext;

    $fSend = $mail->Send();

    if ($fSend) {
        return true;
        //print "SEND XXXXXXXXXX";
    }
    else {
        //print_r($arrSmtpSrvs[$strUseProvider]);
        //print $mail->ErrorInfo;
        // error, fall back on SENDMAIL on the Linux OS
        mail("admin@envolabs.io","ROYALE Mailing Error",date("YmdHis").": Could not send to ".$strTo." via ".$strUseProvider."\n\n".$mail->ErrorInfo."\n\n".$plaintext);
        mail($strTo,"Plain: ".$subject,$plaintext);
    }
}
// ------------------------------------------------------------------------------
function giveLatestId() {
    $latestSQL = "select EVALUE from " . ($GLOBALS["config"]["keytable"]) . "  where EGROUP = 'HODLERS' AND EKEY like 'LASTMINT' ORDER BY EID desc limit 1";
    $latestRES = dbQuery($latestSQL);
    $latestARR = dbFetchArr($latestRES);

    return 0+intval($latestARR["EVALUE"]);
}

// ------------------------------------------------------------------------------
function giveRndTarget() {
    $countSQL = "select count(*) ANZ from " . ($GLOBALS["config"]["keytable"]) . " where EGROUP='" . ($GLOBALS["config"]["keygroup"]) . "' AND EVALUE > '-1'";
    $countRES = dbQuery($countSQL);
    $countARR = dbFetchArr($countRES);

    $latestSQL = "select * from " . ($GLOBALS["config"]["keytable"]) . "  where EGROUP = '" . ($GLOBALS["config"]["grouplastmint"]) . "' AND EKEY like 'LASTMINT'";
    $latestRES = dbQuery($latestSQL);
    $latestARR = dbFetchArr($latestRES);

    $latestId = 1 + intval(giveLatestId());

    $targetARR = Array();
    $targetARR["ID"] = -1;
    $targetARR["STATUS"] = -1;

    if($countARR["ANZ"]>0) {
        $targetSQL = "SELECT *, CONVERT(REPLACE(EKEY,'ID_',''), INTEGER) MYID FROM " . ($GLOBALS["config"]["keytable"]) . " WHERE EKEY like 'ID_%' AND CONVERT(REPLACE(EKEY,'ID_',''), INTEGER) < ".($latestId)." ORDER BY RAND() limit 1";
        $targetRES = dbQuery($targetSQL);
        $targetVARs = dbFetchArr($targetRES);
        $targetARR["ID"] = 0 + str_replace("ID_", "", $targetVARs["EKEY"]);
        $targetARR["STATUS"] = 0+$targetVARs["EVALUE"];
    }
    return $targetARR;
}

function countPop($healthStat=0) {
    if($healthStat>-1) {
        $aliveDead = ">";
    } else {
        $aliveDead = "=";
    }

    $countSQL = "select count(*) ANZ from " . ($GLOBALS["config"]["keytable"]) . " where EGROUP='HODLERSTATUS' AND EVALUE ". ($aliveDead) ." '-1'";
    $countRES = dbQuery($countSQL);
    $countARR = dbFetchArr($countRES);
    //print $countSQL;
    return $countARR;
}

function giveHodler($myId) {
    $targetARR = Array();
    $targetARR["ID"] = -1;
    $targetARR["STATUS"] = -1;
    // check ID
    $myId = 0 + $myId;
    $targetSQL = "select * from " . ($GLOBALS["config"]["keytable"]) . " where  EGROUP='" . ($GLOBALS["config"]["keygroup"]) . "' AND EKEY = 'ID_".($myId)."'";
    $targetRES = dbQuery($targetSQL);
    $targetVARs = dbFetchArr($targetRES);
    $targetARR["ID"] = 0 + str_replace("ID_", "", $targetVARs["EKEY"]);
    $targetARR["STATUS"] = 0+$targetVARs["EVALUE"];

    $latestId = intval(giveLatestId());

    if($targetARR["STATUS"] < 0 OR $myId>$latestId) {
        $targetARR = giveRndHodler();
    }
    return $targetARR;
}

function addHeroName($myPath, $myId, $relPath, $newName = "") {
    $sourceFile = $myPath . "adolfs/" . ($myId) . ".png";
    $tagName = "";
    $fileCreated = "";
    if(file_exists($myPath . "adolfs/bak_" . ($myId) . ".png")) {
        $sourceFile = $myPath . "adolfs/bak_" . ($myId) . ".png";
    }
    if(strlen($newName)<3) {
        $checkSQL = "select EVALUE from " . ($GLOBALS["config"]["keytable"]) . " where EGROUP='DOGTAG_".($myId)."'";
        $checkRES = dbQuery($checkSQL);
        $checkARR = dbFetchArr($checkRES);
        $tagName = $checkARR["EVALUE"];
    } else {
        $updateSQL = "update " . ($GLOBALS["config"]["keytable"]) . " set EVALUE = '".$newName."' where EGROUP = 'DOGTAG_".($myId)."' AND EKEY = '".dbEscString($_COOKIE["activeWallet"])."'";
        $updateRES = dbQuery($updateSQL);
        $tagName = $newName;
    }
    $checkSQL = "select EVALUE from " . ($GLOBALS["config"]["keytable"]) . " where EGROUP='HODLERSTATUS' AND EKEY = 'ID_" . ($myId) . "'";
    $checkRES = dbQuery($checkSQL);
    $checkARR = dbFetchArr($checkRES);

    if ($checkARR["EVALUE"] == -1 and strlen($tagName)>2) {
        $dest = imagecreatefrompng($sourceFile);
        $src = imagecreatefrompng($relPath."assets/death" . (rand(1, 5) . ".png"));

        imagealphablending($src, true);
        imagesavealpha($src, true);

        imagecopy($dest, $src, 0, 0, 0, 0, 1024, 1024);

        $black = imagecolorallocate($dest, 0, 0, 0);
        $white = ImageColorAllocate($dest, 255, 255, 255);

        imagefilledpolygon($dest, array(512, 0, 784, 0, 1024, 240, 1024, 512), 4, $black);

        putenv('GDFONTPATH=' . realpath($relPath.'.'));
        imagettftext($dest, 32, 315, 710, 38, $white, 'cascadia', "A.HODLer #" . ($myId));
        imagettftext($dest, 32, 315, 602, 28, $white, 'cascadia', "killed by " . $tagName);


        if (!file_exists($myPath . "adolfs/bak_" . ($myId) . ".png")) {
            copy($myPath . "adolfs/" . ($myId) . ".png", $myPath . "adolfs/bak_" . ($myId) . ".png");
        }
        imagepng($dest, $myPath . "adolfs/" . ($myId) . ".png");
        $fileCreated = $myPath . "adolfs/" . ($myId) . ".png";
    }
    return $fileCreated;
}