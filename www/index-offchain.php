<?php
    // replace this with JS logic and smart contract call later to get random warrior
    // tools have DB and eth network disabled!
    require_once("inc/tools.php"); // <= needed to read and parse the config!

    // needs to be put into JS logic, when the contract has the function "giveRandomWarrior()"
    $activeTarget = rand(1,11);
    $activeTargetStatus = 0;
?>
<html>
<head>
    <title>KEYWORD from SEARCH</title>
    <meta content="Clean description" name="description">
    <meta content="NFTs, nft art, nft drop, best nft, degenmint, NFT" name="keywords">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>
<body>
<H1>Battle Royale Framework</H1>
Mint warriors and play with those. All in one contract.
<HR>
<div id="walletCall">
    <button onclick="checkWallet();">Connect Wallet to Mint OR Fight!</button>
</div>
<HR>
<H2>Free Mint</H2>
    <input type="hidden" id="mintAmount" value="1">
    <button onclick="startMint(1);">Mint 1</button>
    <button onclick="startMint(2);">Mint 2</button>
<HR>
<H2>Game</H2>
<div id="fightCall">
    <button id="rm-mint">-</button>
    <input type="text" id="mint-counter" style="font-size: 1.3em; padding: 3px 12px; background-color: #FFFFFF; border: 1px dotted #AAAAAA; text-align: center" value="1" size="3">
    <button type="button" id="add-mint">+</button>
    <BR>
    <BR>
    <button id="minusButton" onclick="throwMinus();">
        <B>Send Minus</B>
    </button>
    <button id="plusButton" onclick="throwPlus();">
        <B>Send Plus</B>
    </button>
    <br><br>
</div>

<HR>

<div id="gamePlane">GAMEPLANE</div>
<BR><BR>
<div id="walletStatus">WALLETSTATUS</div>
<BR><BR>
<div id="jackpotDiv">JACKPOT</div>
<BR><BR>
<div>
    <div id="healthCount">Health: <?=$activeTargetStatus?></div>
    <BR>
    <img src="assets/loading.gif" id="targetHead" width="250">
    <BR>
    <input type="text" id="targetID" name="targetID" value="<?=$activeTarget?>"
       onblur="initTargetHead(); return false;" onkeyup="keyHandle(event)">
</div>

<div id="modalBody"></div>

<?php /* JAVASCRIPT */ ?>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js" id="jquery"></script>
<script src="https://unpkg.com/@walletconnect/web3-provider@1.7.8/dist/umd/index.min.js"></script>
<script src="js/web3.min.js"></script>
<script src="js/minttool_game.js?v=1"></script>
<script src="js/battle.js?v=1"></script>
<script>
    // ------------------------ DEFAULTS
    var web3 = false;
    var activeWallet = "";
    var myContract = "";
    var contractInstance = null;

    var contractAddr = "<?=$GLOBALS["config"][$useConf]["ethnet"]["contract"]?>";
    var myABI = <?=json_encode($GLOBALS["config"]["contractDetails"]["abi"])?>;
    var publicInfuraID = "<?=$GLOBALS["config"][$useConf]["ethnet"]["infuraid"]?>";

    var activeGas = "";
    var chainId = "";
    var myProof = [];
    var tokenCounter = 0;
    var transactionStatus = "";
    var currentId = 1;
    var mintValue = 5000000000000000; // 0.005 in WEI
    var activePrice = 0;
    var BCtime = 0;
    var ethNetworkNeeded = <?=$GLOBALS["config"][$useConf]["networkneeded"]?>;
    var transactionHashArr = [];

    // ------------------------ GAME START DEFAULTS
    var battleStarted = false;
    var freeMintStarted = false;
    var activeTarget = <?=$activeTarget?>;
    var activeTargetStatus = <?=$activeTargetStatus?>;
    var getMaterialAmount = 1;
    var showMiniPot = false;
    // ------------------------ START GAME
    $(document).ready(function() {
        if(activeTarget < 1) {
            alert("Game finished!");
        } else {
            initTargetHead();
        }
    });
</script>

</body>
</html>
