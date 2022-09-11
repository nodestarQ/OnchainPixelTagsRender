// -----------------------------------------------------------------------------------------------------
// call freeMint
function startMint(myAmount) {
    if(web3 === false) {
        $("#walletStatus").html("No wallet connected, please connect a wallet!");
        connectProvider();
        return;
    }
    if(contractInstance === undefined || contractInstance === null) {
        $("#walletStatus").html("No wallet connected, please connect a wallet!");
        connectProvider();
        return;
    }
    $("#mintAmount").val(myAmount);
    //alert($("#mintAmount").val());

    var sendPrice = (parseInt(activePrice) * parseInt($("#mintAmount").val())).toString();
    tokenCounter = 0;
    contractInstance.methods.mint(parseInt($("#mintAmount").val())).send({from: activeWallet, value: 0, gasPrice: parseInt(activeGas * 1.25)})
        .on('transactionHash', function(hash){
            console.log("Hash: ");
            console.log(hash);
            transactionStatus="Transaction <B>placed</B> on the chain, waiting for update: " + hash +"<BR>\n" + "<B>IF YOU ARE CONNECTING A MOBILE WALLET, PLEASE WATCH THAT SCREEN!</B>";
            transactionPlaced();
            $("#walletStatus").html('<img src="assets/loading.gif" height="80">'+"<BR>\n"+"SENDING TO BLOCKCHAIN - Please wait!");

        })
        .on('receipt', function(receipt){
            console.log("Receipt: ");
            console.log(receipt);
            transactionStatus="Transaction <B>received</B> by chain, waiting for update." + "<BR>\n" + "<B>IF YOU ARE CONNECTING A MOBILE WALLET, PLEASE WATCH THAT SCREEN!</B>";
            transactionPlaced();
            $("#walletStatus").html('<img src="assets/loading.gif" height="80">'+"<BR>\n"+"BLOCKCHAIN PROCESSING - Please wait!");
        })
        .on('confirmation', function(confirmationNumber, receipt){
            transactionStatus="";
            $("#walletStatus").html("Transaction <B>confirmed</B> by chain: <a href='https://etherscan.io/tx/" + receipt.transactionHash + "'>Show on Etherscan</a>");
            if(!isMobile) {
                if(tokenCounter<1) {
                    alert("Free mint successful!")
                    tokenCounter++;
                }
                transactionStatus="";
            }
            if(transactionStatus == "") {
                console.log("Confirmation: / Receipt: ");
                console.log(confirmationNumber);
                console.log(receipt);
            }
            calcMintValue();
            balanceof();
            totalsupply();
            mintSuccess(receipt);
        })
        .on('error', function(error, receipt) {
            console.log("Error: / Receipt: ");
            console.log(error);
            console.log(receipt);
            transactionStatus="";
            $("#walletStatus").html("ERROR: Something went wrong!");
            $("#walletStatus").append("<BR>STOPPED - Not enough funds or cancelled?");
        });
}
// -----------------------------------------------------------------------------------------------------
// -----------------------------------------------------------------------------------------------------
// call fight
function startFight(myTarget, myKind) {
    if(web3 === false) {
        $("#walletStatus").html("No wallet connected, please connect a wallet!");
        connectProvider();
        return;
    }
    if(contractInstance === undefined || contractInstance === null) {
        $("#walletStatus").html("No wallet connected, please connect a wallet!");
        connectProvider();
        return;
    }

    var sendPrice = updatePrice(myTarget,myKind).toString();
    var totalHealth = myKind * getMaterialAmount;
    showMiniPot = true;

    //alert(myTarget +","+ totalHealth +","+ activeGas);
    // GAS Param = gasPrice: parseInt(mintValue * 0.0000008,10).toString()
    transactionStatus = "";
    contractInstance.methods.setHP(myTarget, totalHealth).send({from: activeWallet, value: sendPrice})
        .on('transactionHash', function(hash){
            console.log("Hash: ");
            console.log(hash);
            transactionPlaced();
            $("#walletStatus").html("<B>SENDING TO BLOCKCHAIN - Please wait!</B>");
        })
        .on('receipt', function(receipt){
            console.log("Receipt: ");
            console.log(receipt);
            transactionPlaced();
            if(transactionStatus.length < 3) {
                $("#walletStatus").html("<B>BLOCKCHAIN PROCESSING - Please wait!</B>");
            }
        })
        .on('confirmation', function(confirmationNumber, receipt) {
            if(!transactionHashArr.includes(receipt.transactionHash)) {
                transactionHashArr.push(receipt.transactionHash);
                if (myKind == -1) {
                    if (transactionStatus == "") {
                        moveMinus();
                    }
                    transactionStatus = "RUNNING";
                }
                if (myKind == 1) {
                    if (transactionStatus == "") {
                        movePlus();
                        console.log("Confirmation: / Receipt: ");
                        console.log(confirmationNumber);
                        console.log(receipt);
                    }
                    transactionStatus = "RUNNING";
                }
                $("#walletStatus").html("Transaction <B>confirmed</B> by chain: <a href='https://etherscan.io/tx/" + receipt.transactionHash + "' target='_blank'>Show on Etherscan</a>");
            }
        })
        .on('error', function(error, receipt) {
            console.log("Error: / Receipt: ");
            console.log(error);
            console.log(receipt);
            transactionStatus="";
            $("#walletStatus").html("ERROR: Something went wrong!");
            $("#walletStatus").append("<BR>STOPPED - Not enough funds or cancelled?");
            if(myKind == 1) {
                $("#plusAnim").remove();
                $("#plusButton").prop('disabled', false);
            } else {
                grenadeSpeed = 1;
                $("#minusAnim").remove();
                $("#minusButton").prop('disabled', false);
            }
        });
}
// -----------------------------------------------------------------------------------------------------
// -----------------------------------------------------------------------------------------------------
// get the complete value of a mint (amount * value of 1)
function calcMintValue() {
    var myAmount = getMaterialAmount;
    if(myAmount < 1) {
        getMaterialAmount=1;
        return;
    }
    var currVal = web3.utils.fromWei((myAmount * activePrice).toString(), 'ether');
    currVal = roundUp(currVal, 5);
    console.log(currVal + " ETH + gas fees");
}

// -----------------------------------------------------------------------------------------------------
// functions for status updates
function transactionPlaced() {
    var d = new Date();
    var myTime = d.toLocaleTimeString();
    if(transactionStatus.length > 12) {
        $("#WalletStatus").html(myTime + " - " + transactionStatus);
        window.setTimeout(transactionPlaced, 1000);
    }
}
// -----------------------------------------------------------------------------------------------------
// functions for status updates
function callMintCounterUpdate(latestId) {
    console.log("added " + latestId.toString());
}
// -----------------------------------------------------------------------------------------------------
// functions for status updates
function mintSuccess(resultJson) {
    var myHtml = "";
    var e = 0 ;
    var tokids = "",

    myHtml = "<h2>Success - Thank you!</h2>\n";1

    $("#walletStatus").html(myHtml);
}
// --------------------------------------------------------------------------------
//
async function connectMM() {
    if (typeof window.ethereum !== 'undefined') {
        console.log("yes, wallet there");
        ethereum.request({method: 'eth_requestAccounts'});
        web3 = new Web3(window.ethereum);
    }
}

async function connectProvider() {
    if (typeof window.ethereum !== 'undefined') {
        await connectMM();
        checkWallet();
    } else {
        // metamask not available, lets get another provider
        console.log("No provider :-(");
        var provider = new WalletConnectProvider.default({
            infuraId: publicInfuraID,
        });
        provider.enable().then(function(res){
            web3 = new Web3(provider);
            checkWallet();
        });
    }
    return;
}
// -----------------------------------------------------------------------------------------------------
// check and connect wallet
async function checkWallet() {
    if(web3 === false) {
        await connectProvider();
    }

    const accounts = await web3.eth.getAccounts();

    if (accounts.length > 0) {
        activeWallet = accounts[0];
    }
    //console.log(accounts[0]);
    if(typeof window.ethereum !== 'undefined') {
        if (ethereum.isConnected()) {
            var isMM = ethereum.isMetaMask;
            if (isMM) {
                console.log("yes - MM installed");
            } else {
                console.log("no - MM missing");
            }
            console.log("yes");
            console.log("Wallet Found");
            // web3 = new Web3(window.ethereum);
        }
    }
    if(activeWallet.length > 12) {
        $("#walletCall").hide();
        $("#fightCall").show();

        console.log(activeWallet);
        console.log("<B>Status Wallet:</B> Wallet connected. Make sure the network is right!");

        chainId = await web3.eth.getChainId();
        console.log("NETWORK CHECK:",chainId);
        if(chainId == ethNetworkNeeded) {
            console.log("Status: Wallet connected, you can mint right away!");
            updateJackpot();
            initTargetHead();
        }
    } else {
        $("#walletCall").show();
        $("#fightCall").hide();
        $("#walletStatus").html("<B>CAN NOT READ YOUR WALLET ID, something is wrong!</B><BR>\n<B style='color: #AA0000;'>Please connect your Metamask-compatible wallet</B>");
    }


    // metamask is there!
    if(typeof window.ethereum !== 'undefined') {
        window.ethereum.on('accountsChanged', function (accounts) {
            activeWallet = accounts[0];
            $("#walletStatus").html("Wallet account changed - Account: " + activeWallet);
            location.reload();
        });
        window.ethereum.on('chainChanged', function (networkId) {
            chainId = ethereum.request({method: 'eth_chainId'});
        });
    }
    // found a wallet
    if(activeWallet.length > 5) {
        document.cookie = "activeWallet="+activeWallet;
        $("#walletStatus").html(activeWallet);
        $("#walletStatus").html("<B>Status:</B> Wallet connected. Make sure the network is the ETH mainnet!");

        await showNetwork(chainId);

        if(chainId == ethNetworkNeeded) {
            readContract();

            $("#walletStatus").html("<B>Status:</B> Wallet connected, you can free mint right away!");
            $("#walletStatus").html($("#walletStatus").html() + "<BR>\n" + activeWallet.substring(0,5)+"..."+activeWallet.substring(activeWallet.length - 5));
        } else {
            $("#walletStatus").html("<B>Status:</B> WRONG NETWORK, PLEASE SWITCH TO ETH MAINNET!");
        }
    } else {
        $("#walletStatus").html("NO WALLET FOUND!");
    }
    //
    // $("#walletStatus").html(contractAddr);
}
// -----------------------------------------------------------------------------------------------------
// Set and log network ID - 4 = rinkeby, 1 = mainnet
async function showNetwork() {
    /*
    0x1	1	Ethereum Main Network (Mainnet)
    0x3	3	Ropsten Test Network
    0x4	4	Rinkeby Test Network
    0x5	5	Goerli Test Network
    0x2a	42	Kovan Test Network
     */
    chainId = await web3.eth.getChainId();
    if(chainId == 0x1) { console.log("Ethereum Main"); }
    if(chainId == 0x3) { console.log("Ropsten Testnetwork"); }
    if(chainId == 0x4) { console.log("Rinkeby Testnetwork"); }
    if(chainId == 0x5) { console.log("Goerli Testnetwork"); }
    if(chainId == 0x2a) { console.log("Kovan Testnetwork"); }
    if(chainId == 588) { console.log("Metis Stardust Testnetwork"); }
    // 1088 Andromeda
    if(chainId == 1088) { console.log("Metis Andromeda network"); }

    if(chainId != ethNetworkNeeded) {
        console.log("Hint: YOU ARE NOT on the right network! Network ID: " + chainId);
    }
    return chainId;
}
// -----------------------------------------------------------------------------------------------------
// prepare contract interaction with wallet: mint!
async function readContract() {
    contractInstance = new web3.eth.Contract(myABI, contractAddr);
    // subscriptions
    let options = {
        reconnect: {
            auto: true,
            delay: 6000, // ms
            maxAttempts: 5,
            onTimeout: false
        },
        // fromBlock: 0,
        address: [contractAddr],    //Only get events from specific addresses
        topics: ['0xe046c7220b7585b547aeeb53022a609f95e11e82fa9beb690b0ecbe602ca3fe0']
        //What topics to subscribe to: MiniJackpotWin
    };

    let subscription = web3.eth.subscribe('logs', options,(err,event) => {
        if (!err)
            console.log("EVENT", event);
    });

    subscription.on('data', event => {
        console.log("EVENT DATA ", event);
        /*
        var miniPotVal = parseInt(event.data.jackpotAmount);
        var thisWinnerWallet = event.data.winner;
        var ethMiniPotVal = web3.utils.fromWei((miniPotVal).toString(), 'ether');
        */
        if(showMiniPot) {
            console.log("You WON!");
            showMiniPot = false;
        }
    });

    subscription.on('changed', changed => console.log("EVENT CHANGED ",changed));
    subscription.on('error', err => { throw err });
    subscription.on('connected', nr => console.log("EVENT CONNECTED ",nr));
    // -----------------------------------------------------------
    contractInstance.methods.started().call({from: activeWallet}, function(err, result) {
        if(! err) {
            freeMintStarted = result;
            if(!freeMintStarted) {
                console.log("Free mint has not started yet!");
            }
            console.log("Free mint started: ", freeMintStarted);
            contractInstance.methods.pixelWarStarted().call({from: activeWallet}, function(err, result) {
                if(! err) {
                    battleStarted = result;
                    console.log("Battle status: ", battleStarted);
                    if(battleStarted) {
                        activePrice = mintValue;
                        console.log("Active - value: " + activePrice);

                        web3.eth.getGasPrice().then((result) => {
                            activeGas = result.toString();
                            console.log("Gas estimate: " + activeGas);
                        });
                        calcMintValue();
                    } else {
                        console.log("Battle has not started yet!");
                    }
                } else {
                    console.log("Error in retrieving value from contract");
                    console.log(err);
                }

            });
            console.log("Balance: ", web3.eth.getBalance(contractAddr));
        } else {
            console.log("Error in retrieving value from contract");
            console.log(err);
        }

    });
}
// prepare contract interaction with wallet: mint!
// -----------------------------------------------------------------------------------------------------
function roundUp(num, precision) {
    precision = Math.pow(10, precision)
    return Math.ceil(num * precision) / precision
}