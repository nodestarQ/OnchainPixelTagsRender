// count up
$("#add-mint").click(function() {
    getMaterialAmount++;
    $("#mint-counter").val(getMaterialAmount.toString());
    if(activeWallet.length > 10) {
        var hintHTML = web3.utils.fromWei(updatePrice(1,1).toString(), 'ether') + " ETH";
        $("#walletStatus").html( hintHTML );
    }
    $("#rm-mint").removeClass("disabled");
});
// count down
$("#rm-mint").click(function() {
    getMaterialAmount--;
    if (getMaterialAmount <= 1) {
        getMaterialAmount=1;
        $("#rm-mint").addClass("disabled");
    }
    $("#mint-counter").val(getMaterialAmount.toString());
    if(activeWallet.length > 10) {
        var hintHTML = web3.utils.fromWei(updatePrice(1,1).toString(), 'ether') + " ETH";
        $("#walletStatus").html( hintHTML );
    }
    $("#add-mint").removeClass("disabled");
});

// --------------------------------------------------------------------------------
function keyHandle(e){
    if(e.keyCode === 13){
        e.preventDefault(); // Ensure it is only this code that runs
        initTargetHead();
    }
}

function updateJackpot() {
    console.log("Checking Pot!");
    web3.eth.getBalance(contractAddr).then((cBalance) => {
        var potVal = parseInt(cBalance / 100 * 25);
        var currVal = web3.utils.fromWei((potVal).toString(), 'ether');
        currVal = roundUp(currVal, 4);
        $("#jackpotDiv").html("Jackpot: " + currVal + " ETH");
    });
}

function updatePrice() {
    var factor = 1;
    if(getMaterialAmount>6) {
        factor = 0.8;
        if(getMaterialAmount>12) {
            factor = 0.7;
            if(getMaterialAmount>18) {
                factor = 0.6;
                if(getMaterialAmount>24) {
                    factor = 0.5;
                }
            }
        }
    }
    var newPrice = parseInt(mintValue * factor) * getMaterialAmount;
    return newPrice;
}

function updateWarriorHead() {
    var myTargetID = parseInt($("#targetID").val());
    // get new warrior logic goes here
}

function updatePopCount() {
    // update population count
}

function initTargetHead() {
    var myTargetID = parseInt($("#targetID").val());
    // new head display

    if(contractInstance === undefined || contractInstance === null) {
        $("#walletStatus").html("No wallet connected, please connect a wallet!");
        return;
    }
    contractInstance.methods.pixelList(parseInt($("#targetID").val())).call({from: activeWallet}, function(err, result) {
        if (!err) {
            activeTarget = parseInt($("#targetID").val());
            activeTargetStatus = result.health;
            $("#healthCount").html("Health: " + activeTargetStatus.toString());
            if (activeTargetStatus < 0) {
                $("#walletStatus").html("Warrior is dead, pick another one!");
                // updateWarriorHead() here!
            }
        }
    });
}

function moveMinus() {
    $("#minusAnim").attr("src","assets/explosion1.gif");
    initTargetHead();
    window.setTimeout(function() {
        $("#minusButton").prop('disabled', false);
        $("#gamePlane").html("GAMEPLANE");
    }, 2000);
}

function throwMinus() {
    if(activeTargetStatus<0) {
        alert("Target is gone or not minted, please select a new one!");
        return;
    }
    $("#gamePlane").append("<img id='minusAnim' src='assets/fightanim1.gif' height='200' />");
    $("#minusButton").prop('disabled', true);

    var myTargetId = parseInt($("#targetID").val());
    startFight(myTargetId, -1);
}
// -------------------------------------------------------------------------
function movePlus() {
    $("#plusAnim").attr("src","assets/defend1.gif");
    initTargetHead();
    window.setTimeout(function() {
        $("#plusButton").prop('disabled', false);
        $("#gamePlane").html("GAMEPLANE")
    }, 2500);
}

function throwPlus() {
    if(activeTargetStatus<0) {
        alert("Target is dead or not minted, please select a new one!");
        return;
    }
    $("#gamePlane").append("<img id='plusAnim' src='assets/fightanim1.gif' height='200' />");
    $("#plusButton").prop('disabled', true);

    var myTargetId = parseInt($("#targetID").val());
    startFight(myTargetId, 1);
}