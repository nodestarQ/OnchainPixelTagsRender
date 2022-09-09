// SPDX-License-Identifier: MIT
// This is no CC0
// www.PixelRoyal.xyz
pragma solidity 0.8.15;
import "@openzeppelin/contracts/access/Ownable.sol";
import "erc721a/contracts/ERC721A.sol";
import "@openzeppelin/contracts/utils/Strings.sol"; 
import {Base64} from "base64-sol/base64.sol";
import './PixelTag.sol';

contract PixelRoyale is ERC721A, Ownable {
    //---------- Addies ----------//
    address public contractCreator;
    address public lastPlayer;
    address public mostAttacks;
    address public randomPlayer;
    address public randomTag;
    address public abashoCollective; // ---> Needs to be set
    address public pixelTagContract; // ---> Interface for Tags
    address private nullAddress;
    //---------- Mint Vars ----------//
    bool public started;
    bool public claimed;
    uint256 public constant MAXPIXELS = 4444;
    uint256 public constant WALLETLIMIT = 2;
    uint256 public constant CREATORCLAIMAMOUNT = 111;
    mapping(address => uint) public addressClaimed; // ---> keeps track of wallet limit
    // - will be replaced by on-chain art
    string private baseURI;
    //---------- Metadata Snippets ----------//
    string private comb1 = '","description": "4444 On-Chain PixelRoyale warriors are fighting for honor and glory on the Ethereum Blockchain! Well, for that and for an Eth prize pool! Mint, attack and survive to emerge victorious and win the jackpot!","external_url": "http://pixelroyale.xyz/","attributes": [{"trait_type": "Background","value": "';
    string private comb2 = '"},{"trait_type": "Model Number","value": "';
    string private comb3 = '"},{"trait_type": "Soul","value": "';
    string private comb4 = '"},{"trait_type": "Hair","value": "';
    string private comb5 = '"},{"trait_type": "Shirt","value": "';
    string private comb6 = '"},{"trait_type": "Backpack","value": "';
    string private comb7 = '"},{"trait_type": "Helmet","value": "';
    string private comb8 = '"},{"trait_type": "Weapon","value": "';
    string private comb9 = '"},{"trait_type": "Status","value": "'; // ALive or Missing in action
    string private sComb9 = '"},{"trait_type": "Killer","value": "';
    string private comb10 = '"}],"image": "data:image/svg+xml;base64,';
    string private comb11 = '"}';
    //---------- PixelRoyale Vars ----------//
    bool public pixelWarStarted;
    bool public pixelWarConcluded;
    uint256 public timeLimit = 1671667200; // Thursday, 22. December 2022 00:00:00 GMT
    uint constant ITEM_PRICE = 0.005 ether;
    mapping(address => uint) public walletHighscore; // ---> keeps track of each wallet highscore 
    uint256 public currentHighscore;
    string private salt;
    bool public payout;
    //---------- Mini Jackpot Vars ----------//
    uint256[] public jackpot;
    //---------- Player Vars ----------//
    struct Pixel {
        uint32 seed; //only for on-chain art gen
        int256 health;
        bool status;
    }
    mapping(uint256 => Pixel) public pixelList; // ---> maps ID to a Player Struct

    //---------- Events ----------//
    event ItemBought(address from,address currentMA,uint tokenId,int256 amount);
    event DropOut(address from,uint tokenId);
    event MiniJackpotWin(address winner, uint256 jackpotAmount);
    event MiniJackpotAmount(uint256 jackpotAmount);
    
    //---------- Construct ERC721A TOKEN ----------//
    constructor() ERC721A("PixelRoyale BATTLE GAME", "PRBG") {
        contractCreator = msg.sender;
    }

    function _startTokenId() internal view virtual override returns (uint256) {
        return 1;
    }
    
    //---------------------------------------------------------------------------------------------
    //---------- MINT FUNCTIONS ----------//
    //---------- Start Minting -----------//
    function startMint() external onlyOwner {
        require(!started, "mint has already started");
        started = true;
    }

    //---------- Free Mint Function ----------//
    function mint(uint256 _amount) external {
        uint256 total = totalSupply();
        if(_msgSender() != contractCreator) {
            require(started, "Mint did not start yet");
            require(addressClaimed[_msgSender()] + _amount <= WALLETLIMIT, "Wallet limit reached, don't be greedy");
        }
        require(_amount > 0, "You need to mint at least 1");
        require(total + _amount <= MAXPIXELS, "Not that many NFTs left, try to mint less");
        require(total <= MAXPIXELS, "Mint out");
        
        // create structs for minted amount
        for (uint j; j < _amount; j++) { 
                Pixel memory newPixel = Pixel(
                    uint32(bytes4(keccak256(abi.encodePacked(block.timestamp, j+1, msg.sender)))),0,true);
                pixelList[total+j+1] = newPixel;
        }
        addressClaimed[_msgSender()] += _amount;
        _safeMint(_msgSender(), _amount);

        // immediately starts PixelRoyale GAME on mint out
        if(totalSupply() >= MAXPIXELS){
            pixelWarStarted = true;
        }
    }

    //---------- Team Claim ----------//
    function teamClaim() external onlyOwner {
        uint256 total = totalSupply();
        require(!claimed, "already claimed");
        for (uint j; j < CREATORCLAIMAMOUNT; j++) {
            // struct creation for mint amount 
                Pixel memory newPixel = Pixel(
                uint32(bytes4(keccak256(abi.encodePacked(block.timestamp, j+1, msg.sender)))),0,true);
            pixelList[total+j+1] = newPixel;
        }
        _safeMint(contractCreator, CREATORCLAIMAMOUNT);
        claimed = true;
    }

    //---------------------------------------------------------------------------------------------
    //---------- BATTLE ROYALE GAME ----------//
    //---------- Manually Start PixelRoyale ----------//
    function startPixelWar() external onlyOwner {
        require(!pixelWarStarted, "The war has already been started");
        pixelWarStarted = true;
    }

    //---------- Calculate Amount Of "Alive" Players ----------//
    function getPopulation() public view returns(uint256 _population) {
        for (uint j; j < totalSupply(); j++) {
            if(isAlive(j)){
                _population++;
            }
        }
    }

    //---------- Returns Last Player ID ----------//
    function checkLastSurvivor() public view returns(uint256 _winner) {
        for (uint j; j <= totalSupply(); j++) {
            if(pixelList[j].health > -1){
                _winner = j;
            }
        }
    }

    //---------- Checks If Specified TokenID Is "Alive" ----------//
    function isAlive(uint256 _tokenId) public view returns(bool _alive) {
       pixelList[_tokenId].health < 0 ? _alive = false : _alive = true;
    }

    //---------- Returns Random "Alive" TokenID ----------//
    function returnRandomId() public view returns(uint256 _tokenId) {
        for (uint256 j = pseudoRandom(totalSupply(),"Q"); j <= totalSupply() + 1; j++) {
            if(pixelList[j].health > -1) {
                return j;
            } 
            if(j == totalSupply()) {
                j = 0;
            }   
        }
    }

    //---------- Pseudo Random Number Generator From Range ----------//
    function pseudoRandom(uint256 _number, string memory _specialSalt) public view returns(uint number) {
        number = uint(keccak256(abi.encodePacked(block.timestamp,block.difficulty,msg.sender,salt,_specialSalt))) % _number;
        number == 0 ? number++: number;
    }

    //---------- Change Salt Value Pseudo Randomness ----------//
    function changeSalt(string memory _newSalt) public onlyOwner {
        salt = _newSalt;
    }

    //---------- Set HP For Players | Protect/Attack ----------//
    function setHP(uint256 _tokenId, int256 _amount) external payable {
        require(!pixelWarConcluded, "PixelRoyale has concluded!");
        require(pixelWarStarted, "PixelRoyale hasn't started!");
        require(getPopulation() > 1, "We already have a winner!");
        require(_amount != 0, "Value needs to be > or < than 0");
        require(pixelList[_tokenId].health > -1, "Player already out of the Game");

        uint priceMod = 10; // ---> 0%
        uint256 amount;

        // turn _amount into a positive amount value
        _amount < 0 ? amount = uint256(_amount*-1) : amount = uint256(_amount);

        // bulk pricing:
        if(amount>6) {
            priceMod = 8; // ---> 20%
            if(amount>12) {
                priceMod = 7; // ---> 30%
                if(amount>18) {
                    priceMod = 6; // ---> 40%
                    if(amount>24) { priceMod = 5; } // ---> 50%
                }  
            }
        }

        // calculate purchase 
        uint256 currentPrice = ITEM_PRICE / 10 * priceMod * amount;
        require((currentPrice) <= msg.value, "Not enough ETH");
        
        // checks on attack purchase 
        if(_amount < 0) {
            require(pixelList[_tokenId].health+_amount>-2,"Try less attacks - warrior overkill");
            walletHighscore[_msgSender()] += amount;
            if(walletHighscore[_msgSender()] > currentHighscore) {
                currentHighscore = walletHighscore[_msgSender()];
                mostAttacks = _msgSender();
            }
        }

        // change health value in player struct
        (pixelList[_tokenId].health+_amount) < 0 ? pixelList[_tokenId].health = -1 : pixelList[_tokenId].health = pixelList[_tokenId].health + _amount;

        //emit event for item buy
        emit ItemBought(_msgSender(),mostAttacks,_tokenId,_amount); // ---> buyer, current Highscore Leader, Interacted token, amount of protections/attacks
        
        // add to mini jackpot array
        addToPot(currentPrice);

        // try jackpot
        if(jackpot.length>0){
            tryJackpot();
        }

        // check if token is alive | Check if player has dropped out of Game
        InterfacePixelTags pixelTag = InterfacePixelTags(pixelTagContract); // ---> Interface to Tags NFT
        if ( !isAlive(_tokenId) ) {
            pixelTag.mintPixelTag(_msgSender()); // ---> MINT DogTag FROM ERC721A 
            pixelList[_tokenId].status = false;
            //emit DropOut event
            emit DropOut(_msgSender(),_tokenId); // ---> Killer, Killed Token
        }
        // check if population is smaller than 2 | check if PixelRoyale has concluded
        if ( getPopulation() < 2 ) {
            pixelWarConcluded = true;
            lastPlayer = ownerOf(checkLastSurvivor());
            randomPlayer = ownerOf(pseudoRandom(MAXPIXELS,"Warrior"));
            randomTag = pixelTag.ownerOf(pseudoRandom(MAXPIXELS-1,"Tag"));
        }
    }

    //---------------------------------------------------------------------------------------------
    //---------- BATTLE ROYALE GAME ----------//
    //---------- Add 49% Of Bet To Mini Jackpot ----------//
    function addToPot(uint256 _amount) internal {
        jackpot.push(_amount/100*49);
    }

    //---------- Calculate Current Mini Jackpot Size ----------//
    function currentPot() internal view returns(uint256 _result) {
        for (uint j; j < jackpot.length; j++) {
            _result += jackpot[j];
        }
    }

    //---------- Win Mini Jackpot Function ----------//
    function tryJackpot() internal {
        if(pseudoRandom(8,"") == 4) { // ---> 12,5% winning chance
            payable(_msgSender()).transfer(currentPot());
            emit MiniJackpotWin(_msgSender(), currentPot()); // ---> emits jackpot amount and winner when hit
            delete jackpot; // ---> purge mini jackpot array after it has been paid out
        }
        else {
            emit MiniJackpotAmount(currentPot()); // ---> emits jackpot amount when not hit
        }
    }

    //---------- Set PixelTag Contract Address For Interactions/Interface ----------//
    function setTagContract(address _addr) external onlyOwner {
        pixelTagContract = _addr;
    }
    //---------------------------------------------------------------------------------------------
    //---------- WITHDRAW FUNCTIONS ----------//

    //---------- Distribute Balance if Game Has Not Concluded Prior To Time Limit ----------//
    function withdraw() public {
        require(block.timestamp >= timeLimit, "Play fair, wait until the time limit runs out");
        require(contractCreator == _msgSender(), "Only Owner can withdraw after time limit runs out");
        uint256 balance = address(this).balance;
        payable(abashoCollective).transfer(balance/100*15);
        payable(contractCreator).transfer(address(this).balance);
    }

    //---------- Distribute Balance if Game Has Concluded Prior To Time Limit ----------//
    function distributeToWinners() public {
        require(pixelWarConcluded, "The game has not concluded yet!");
        require(!payout, "The prize pool has already been paid out!");
        uint256 balance = address(this).balance;
        // 25% to Last player and most attacks
        payable(lastPlayer).transfer(balance/100*25);
        payable(mostAttacks).transfer(balance/100*25);
        // 15% to random holder of Player and Dog Tag NFTs
        payable(randomPlayer).transfer(balance/100*10);
        payable(randomTag).transfer(balance/100*10);
        // 15% to abasho collective and remainder to Contract Creator
        payable(abashoCollective).transfer(balance/100*15);
        payable(contractCreator).transfer(address(this).balance);
        payout = true;
    }
    
    //---------------------------------------------------------------------------------------------
    //---------- METADATA & BASEURI ----

    function tokenURI(uint256 _tokenId) public view virtual override returns (string memory) {
        require(_exists(_tokenId), "There is no token with that ID");
        //Start JSON and SVG Generation by creating file headers
        bytes memory json = abi.encodePacked('{"name": "Pixel Tag #',Strings.toString(_tokenId)); // --> JSON HEADER
        bytes memory img = abi.encodePacked('<svg xmlns="http://www.w3.org/2000/svg" witdh="640" height="640" viewBox="0 0 16 16">'); // --> SVG HEADER
        uint32 seed = pixelList[_tokenId].seed;
        //Init Trait Strings
        string memory t0 = Strings.toString((((seed%36)*0)+180)%360); //background -> helmet
        string memory t1 = Strings.toString((seed%360)); //model -> hair
        string memory t2 = Strings.toString((seed%72)*5); //soul + shirt -> backpack
        string memory t3 = Strings.toString(((seed%360)+180)%360); //hair
        string memory t4 = Strings.toString((((seed%72)*5)+180)%360); //backpack
        string memory t5 = Strings.toString(((seed%36)*0)); // helmet
        string[4] memory tA6 = ["Pew Pew", "Dual Wield", "Stabby", "Say Hello To My Little Friend"];
        string memory t6;
        string memory t7;
        seed%100<=75 ? t6 = tA6[seed%4] : t6 = "Bare Handed"; //weapon
        pixelList[_tokenId].status ? t7 = "Alive" : t7 = "Missing In Action";//Status

        // ----- JSON ASSEMBLY ------//
        json = abi.encodePacked(json,comb1,t0); //bg
        json = abi.encodePacked(json,comb2,t1); //model
        json = abi.encodePacked(json,comb3,t2); //soul
        json = abi.encodePacked(json,comb4,t3); //hair
        json = abi.encodePacked(json,comb5,t2); //shirt
        json = abi.encodePacked(json,comb6,t4); //backpack
        json = abi.encodePacked(json,comb7,t5); //helmet
        json = abi.encodePacked(json,comb8,t6); //weapon
        json = abi.encodePacked(json,comb9,t7); //status

        // ----- SVG ASSEMBLY ------//
        //BACKGROUND//
        img = abi.encodePacked(img, '<rect x="0" y="0" width="16" height="16" fill="hsl(',t0,',100%,90%)"/>');
        //SHIRT
        img = abi.encodePacked(img, '<rect x="5" y="13" width="6" height="3" fill="hsl(',t2,',90%,50%)"/><rect x="5" y="13" width="1" height="3" fill="hsl(',t2,',90%,40%)"/>');
        //MODEL// 
        img = abi.encodePacked(img, '<polygon points="5,13 5,12 4,12 4,11 3,11 3,7 13,7 13,11 12,11 12,12 13,12 12,12 11,12 11,13 " fill="hsl(',t1,',50%,80%)"/><polygon points="5,13 5,12 4,12 4,11 3,11 3,7 4,7 4,11 5,11 5,12 6,12 6,13" fill="hsl(',t1,',50%,70%)"/><polygon points="7,15 12,15 12,16 11,16 11,15 7,15 7,16 6,16 6,15" fill="hsl(',t1,',50%,80%)"/>');
        //HAIR
        img = abi.encodePacked(img, '<polygon points="4,7 13,7 13,8 11,8 11,7 9,7 9,8 8,8 8,7 7,7 7,8 6,8 6,7 4,7 4,8 3,8 3,7" fill="hsl(',t3,',80%,60%)"/>');
        //HELMET
        img = abi.encodePacked(img, '<polygon points="1,7 1,5 2,5 2,3 3,3 3,2 4,2 4,1 12,1 12,2 13,2 13,3 14,3 14,5 15,5 15,7 14,7 14,11 13,11 13,12 12,12 12,11 13,11 13,7 3,7 3,11 4,11 4,12 3,12 3,11 2,11 2,7" fill="hsl(',t5,',80%,40%"/><polygon points="2,6 2,5 3,5 3,3 4,3 4,3 4,3 4,2 12,2 12,3 13,3 13,3 13,3 13,5 14,5 14,6" fill="hsl(',t5,',70%,50%)"/><polygon points="2,6 2,5 3,5 3,4 4,4 4,5 5,5 5,6 " fill="hsl(',t5,',80%,80%)"/><polygon points="6,6 6,5 7,5 7,4 8,4 8,6" fill="hsl(',t5,',80%,80%)"/><polygon points="9,6 9,5 11,5 11,6" fill="hsl(',t5,',80%,80%)"/><polygon points="13,5 12,5 12,3 13,3 13,5 14,5 14,6 13,6 " fill="hsl(',t5,',80%,80%)"/>');
        //BACKPACK
        img = abi.encodePacked(img, '<polygon points="3,16 3,14 4,14 4,13 5,13 5,16" fill="hsl(',t4,',70%,30%)"/>');
        //EYES
        img = abi.encodePacked(img, '<polygon points="5,8 11,8 11,10 9,10 9,8 7,8 7,10 5,10 " fill="hsl(0,0%,0%)"/><polygon points="5,8 6,8 7,8 10,8 11,8 10,8 10,9 9,9 9,8 6,8 6,9 5,9" fill="hsl(0,100%,100%)"/>');
        //MOUTH
        img = abi.encodePacked(img, '<rect x="8" y="11" width="1" height="1" fill="hsl(0,0%,0%)"/>');
        //DIRT
        if(pixelList[_tokenId].status) {
            img = abi.encodePacked(img, '<rect x="4" y="7" width="1" height="1" fill="hsl(50,30%,30%)"/><rect x="3" y="8" width="1" height="1" fill="hsl(50,30%,30%)"/><rect x="7" y="8" width="1" height="1" fill="hsl(50,30%,30%)"/><rect x="12" y="8" width="1" height="1" fill="hsl(50,30%,30%)"/><rect x="11" y="10" width="1" height="1" fill="hsl(50,30%,30%)"/><rect x="10" y="12" width="1" height="1" fill="hsl(50,30%,30%)"/><rect x="5" y="12" width="1" height="1" fill="hsl(50,30%,30%)"/><rect x="4" y="11" width="1" height="1" fill="hsl(50,30%,30%)"/>');
        }
        //WEAPON
        if (keccak256(abi.encodePacked(t6)) == keccak256(abi.encodePacked("Bare Handed"))) {
                img = abi.encodePacked(img, '');
        }
        else if (keccak256(abi.encodePacked(t6)) == keccak256(abi.encodePacked(tA6[0]))) {
                img = abi.encodePacked(img, '<polygon points="12,16 12,14 15,14 15,15 13,15 13,16" fill="hsl(0,0%,0%)"/>');
        }
        else if (keccak256(abi.encodePacked(t6)) == keccak256(abi.encodePacked(tA6[1]))) {
                img = abi.encodePacked(img, '<polygon points="12,16 12,14 15,14 15,15 13,15 13,16" fill="hsl(0,0%,0%)"/><polygon points="7,16 7,14 10,14 10,15 8,15 8,16" fill="hsl(0,0%,0%)"/>');
        }
        else if (keccak256(abi.encodePacked(t6)) == keccak256(abi.encodePacked(tA6[2]))) {
                img = abi.encodePacked(img, '<rect x="5" y="14" width="3" height="1" fill="hsl(0,0%,0%)"/><rect x="6" y="12" width="1" height="2" fill="hsl(0,0%,75%)"/>');
        }
        else if (keccak256(abi.encodePacked(t6)) == keccak256(abi.encodePacked(tA6[3]))) {
                img = abi.encodePacked(img, '<polygon points="5,13 6,13 11,13 11,12 12,12 12,13 14,13 14,14 12,14 12,15 11,15 8,15 8,16 7,16 7,15 6,15 6,14 5,14" fill="hsl(0,0%,0%)"/><rect x="9" y="14" width="2" height="1" fill="hsl(5,80%,30%)"/>');
        }
        // ----- CLOSE OFF SVG AND JSON ASSEMBLY ------//
        img = abi.encodePacked(img, '</svg>');
        json = abi.encodePacked(json,comb7,Base64.encode(img),comb11);
        // ----- RETURN BASE64 ENCODED METADATA ------//
        return string(abi.encodePacked('data:application/json;base64,', Base64.encode(json)));
    }
}