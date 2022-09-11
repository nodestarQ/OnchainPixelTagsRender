<?php
require_once("inc/tools.php");

if(substr_count($_SERVER["REQUEST_URI"],"/envoverse/")<1) {
    $myPath = "/wwwroot/centralweb/";
} else {
    $myPath = "/wwwroot/";
}

$pathSel["json"] = "/";
$pathSel["glb"] = "/";
$pathSel["png"] = "/";

$uriParts = explode(".php/", $_SERVER["REQUEST_URI"]);
$paramArr = explode("/",$uriParts[1]);

$myFOME = $paramArr[0];
$myTHUMB = $paramArr[1];

$myFile = basename($myFOME);

$myFile = preg_replace("/[^a-zA-Z0-9\-\_\.]+/", "", $myFile);

$fileParts = explode(".", $myFile);
$myKind = strtolower($fileParts[1]);

$myId = 0 + intval(str_replace("ahodler","", $fileParts[0]));

// get last active minted ENVO
// $mintSQL = "select EVALUE from ENVOKEYS where EGROUP='FOMEMINT' AND EKEY='LATESTMINT'";
// $mintRes = dbQuery($mintSQL);
// $mintArr = dbFetchArr($mintRes);

// $myHighEnvo = 0 + intval($mintArr["EVALUE"]);


//print $myCommand.": ".$myPath.$pathSel["png"].($myDir)."/".$myFile;
//exit;

//echo "test now:".$myKind." and ".($myId);

// -----------------------------------------------------------------
if($myId > 0 AND $myId < 8819) {
    $hodlSQL = "select * from HODLKEYS where  EGROUP='HODLERSTATUS' AND EKEY = 'ID_".($myId)."'";
    $hodlRES = dbQuery($hodlSQL);
    $hodlVARs = dbFetchArr($hodlRES);

    // ------------------------------------- JSON
    if($myKind=="json") {
        $jsonFile = file_get_contents($myPath . "adolfjson/".($myId).".json");
if($_REQUEST["debug"]=="true") {
    print $jsonFile;
}
        $jsonFile = str_replace("/kkk/","/ahodler.world/token.php/", $jsonFile);
        $jsonFile = str_replace("XXKEY2XX",$myId, $jsonFile);

        $jsonArr = json_decode($jsonFile, true);

        if((0+intval($hodlVARs["EKEY"])) <0) {
            $hodlerAttr = ["trait_type" => "Dead", "value" => "yes"];
        } else {
            $hodlerAttr = ["trait_type" => "Shields", "value" => 0 + intval($hodlVARs["EKEY"])];
        }

        $jsonArr["attributes"][] = $hodlerAttr;

        $jsonArr["description"] = "Attack Adolf HODLer in an epic BATTLE ROYALE and win real ETH. Fair and open BATTLE contract allows you to attack: [BATTLE RULES](https://ahodler.world/rules.php). HODLers are limited to 8,818 fighters.";


        header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
        header("Cache-Control: post-check=0, pre-check=0", false);
        header("Pragma: no-cache");
        header("Content-type: application/json; charset=utf-8");
        print json_encode($jsonArr);
        exit;
    }
    if($myTHUMB != "thumb") {
        // ------------------------------------- PNG
        if($myKind == "jpg") {
            header('Access-Control-Allow-Origin: *');
            header('Access-Control-Allow-Methods: GET, POST');
            header("Access-Control-Allow-Headers: X-Requested-With");
            header("Content-Type: image/jpg");
            //$fp = fopen($myPath."/".($myId).".jpg", 'rb');
            $fp = fopen("./raw-hodler.jpg", 'rb');
            fpassthru($fp);
            exit;
        }
        // ------------------------------------- PNG
        if($myKind == "png" and $myId < 8819) {
            header('Access-Control-Allow-Origin: *');
            header('Access-Control-Allow-Methods: GET, POST');
            header("Access-Control-Allow-Headers: X-Requested-With");
            header("Content-Type: image/png");
            if(0+$hodlVARs["EVALUE"]<0) {
                if (!file_exists($myPath . "adolfs/bak_" . ($myId) . ".png")) {
                    addHeroName($myPath, $myId, "", "");
                }
                $fp = fopen($myPath . "adolfs/" . ($myId) . ".png", 'rb');
                fpassthru($fp);
            } else {
                $bang = imagecreatefrompng("img/bang" . (rand(1, 7) . ".png"));
                imagealphablending($bang, true);
                imagesavealpha($bang, true);

                $src = imagecreatefrompng($myPath."adolfs/".($myId).".png");
                imagecopy($src, $bang, 545+rand(1,25), 15+rand(1,25), 0, 0, imagesx($bang), imagesy($bang));

                $red = imagecolorallocate($src, rand(190,210), 0, 0);
                $white = imagecolorallocate($src, 255, 255, 255);
                imagefilledpolygon($src, array(0, 512, 0, 240, 240, 0, 512, 0), 4, $red);
                putenv('GDFONTPATH=' . realpath('.'));
                imagettftext($src, 42, 45, 60, 296, $white, 'cascadia', "kill him on");
                imagettftext($src, 32, 45, 40, 420, $white, 'cascadia', "https://ahodler.world/");
                imagepng($src);
            }
            exit;
        }
    } else {
        // ------------------------------------- PNG
        if($myKind == "png") {
            header('Access-Control-Allow-Origin: *');
            header('Access-Control-Allow-Methods: GET, POST');
            header("Access-Control-Allow-Headers: X-Requested-With");
            header("Content-Type: image/png");
            $fp = fopen($myPath . "hodler_thumbs/" . ($myId) . ".png", 'rb');
            fpassthru($fp);
            exit;
        }
    }
}
