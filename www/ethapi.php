<?php
/*
 * Values in HODLKEYS:
 * EGROUP: HODLERSTATUS / EKEY: ID_hodlerid / EVALUE: health
 * EGROUP: GRENADES / EKEY: wallet / EVALUE: amount
 * EGROUP: SHIELDS / EKEY: wallet / EVALUE: amount
 * EGROUP: DOGTAG_ID / EKEY: wallet / EVALUE: 12charstring
 * EGROUP: WALLETLOC / EKEY: wallet / EVALUE: 2char COUNTRYCODE
 * EGROUP: POPULATION / EKEY: AMOUNT / EVALUE: value
 * EGROUP: WALLETNAMES / EKEY: wallet / EVALUE: 12 chars
 */
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$expire = 'expires: ' . date('D, d M Y H:i:s', strtotime('-1 day')) . ' GMT';
header($expire);
header("cache-control: no-cache");
header("pragma: no-cache");
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST');
header("Access-Control-Allow-Headers: X-Requested-With");

require_once(__DIR__ . "/inc/tools.php");
require __DIR__ . '/vendor/autoload.php';

use Web3\Contract;

$abi = $GLOBALS["config"]["contractDetails"]["abi"];
$contractAddr = $GLOBALS["config"][$useConf]["ethnet"]["contract"];

// ---------------------------------------------------------------------------------------------------------
// callback functions to return contract details via JSON

// POP
$cbPop = function($err, $resArr) {
    $subInt = intval($resArr["_population"]->value);
    updateStoreValue("AMOUNT", (0+$subInt), "POPULATION");
    header("Content-type: application/json; charset=utf-8");
    print '{"POPULATION": '. ( 0 + $subInt ) .'}';
};

// HODLer
$cbTargets = function($err, $resArr) {
    $userCountry = strtoupper($_SERVER['HTTP_CF_IPCOUNTRY']);
    $verifyValue = intval(dbEscString($_REQUEST["v"]));
    $userWallet = dbEscString($_REQUEST["w"]);
    $subInt = intval($resArr[0]->value);

    //print "Here:";
    //print_r($resArr);

    if($subInt == 9223372036854775807) {
        $subInt = -1;
    }
    // transaction took place
    if($verifyValue > -1 AND $subInt < $verifyValue) {
        $checkSQL = "select EVALUE from " . ($GLOBALS["config"]["keytable"]) . " where EGROUP='" . ($GLOBALS["config"]["keygroup"]) . "' AND 'EKEY' = 'ID_".(dbEscString(intval($_REQUEST["p"])))."'";
        $checkRES = dbQuery($checkSQL);
        $checkARR = dbFetchArr($checkRES);
        if(0+intval($checkARR["EVALUE"]) <> $subInt) {
            $EGROUP = "";

            if(0+intval($checkARR["EVALUE"]) > $subInt) {
                $EGROUP = $GLOBALS["config"]["keyminus"]."_".(dbEscString($_REQUEST["p"]));
                $EDELTA = (0+intval($checkARR["EVALUE"])) - $subInt;
            }
            if(0+$checkARR["EVALUE"] < $subInt) {
                $EGROUP = $GLOBALS["config"]["keyplus"]."_".(dbEscString($_REQUEST["p"]));
                $EDELTA = $subInt - (0+intval($checkARR["EVALUE"]));
            }

            if(strlen($EGROUP)>0 AND strlen($userWallet)>10) {
                $prevARR = Array();
                $prevARR["EVALUE"] = 0;

                $prevSQL = "select EVALUE from " . ($GLOBALS["config"]["keytable"]) . " where EGROUP='".$EGROUP."' AND 'EKEY' = '".($userWallet)."'";
                $prevRES = dbQuery($prevSQL);
                $prevARR = dbFetchArr($prevRES);
                // update the Diff
                $NEWVAL = $prevARR["EVALUE"] + $EDELTA;
                updateStoreValue($userWallet, $NEWVAL, $EGROUP);
            }
            // note a kill!
            if($subInt == -1) {
                updateStoreValue($userWallet, dbEscString(substr($userWallet,0,5) . ".." .substr($userWallet,-5)), "DOGTAG_".(0 + $_REQUEST["p"]));
            }
        }
    }
    updateStoreValue("ID_".(dbEscString(intval($_REQUEST["p"]))), (0+$subInt), $GLOBALS["config"]["keygroup"]);
    header("Content-type: application/json; charset=utf-8");
    print '{"HEALTH": '.(0+$subInt).', "ID": '.strip_tags($_REQUEST["p"]).'}';
};


// ---------------------------------------------------------------------------------------------------------
// Command execution
// call contract function
// pass command in ?c= wallet in &w= and additional params in &p=
$availableCommands = ["population","targets","getnew"];
$commandStr = strip_tags($_REQUEST["c"]);
$usrWallet = strip_tags($_REQUEST["w"]);
$commandPara = strip_tags($_REQUEST["p"]);
$verifyValue = strip_tags($_REQUEST["v"]);
//-
if(in_array($commandStr, $availableCommands)) {
    // -
    if($commandStr == "population" OR $commandStr == "targets") {
        $contract = new Contract($GLOBALS["config"][$useConf]["ethnet"]["infurahttps"], $abi);
        // -
        if($commandStr=="population") {
            $contract->at($contractAddr)->call("getHodlerPopulation", "", $cbPop);
        }
        // -
        if($commandStr=="targets") {
            // print $contractAddr." AND " . (intval($commandPara));
            // print_r($contract);
            $contract->at($contractAddr)->call($GLOBALS["config"]["contractDetails"]["arrayTargets"], (0 + intval($commandPara)), $cbTargets);
        }
    }
    // -
    if($commandStr=="getnew") {
        $targetArr = giveRndTarget();
        $activeTarget = $targetArr["ID"];
        $activeTargetStatus = $targetArr["STATUS"];
        print '{"HEALTH": '.(0+$activeTargetStatus).', "ID": '.($activeTarget).'}';
    }
}
