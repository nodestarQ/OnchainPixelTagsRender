// SPDX-License-Identifier: UNLICENSED
pragma solidity ^0.8.15;

import "@openzeppelin/contracts/access/Ownable.sol";
import "@openzeppelin/contracts/utils/Strings.sol"; 
import {Base64} from "base64-sol/base64.sol";
import {TraitAssembly} from "./TraitAssembly.sol";

contract OnchainPixelTagsRender is Ownable {

    uint16 private pixelIndex = 1;
    mapping(uint16 => uint32) private pixelTags;

    
    //Metadata Parts
    string private comb1 = '","description": "4443 On-Chain PixelTags given out for confirmed kills in the PixelRoyale BATTLE GAME. Collect the PixelTags for a chance to win 10% of the PixelRoyale prize pool!","external_url": "http://pixelroyale.xyz/","attributes": [{"trait_type": "Background","value": "';
    string private comb2 = '"},{"trait_type": "Base","value": "';
    string private comb3 = '"},{"trait_type": "Soul","value": "';
    string private comb4 = '"},{"trait_type": "Accessoire","value": "';
    string private comb5 = '"},{"trait_type": "Mouth","value": "';
    string private comb6 = '"},{"trait_type": "Eyes","value": "';
    string private comb7 = '"}],"image": "data:image/svg+xml;base64,';
    string private comb8 = '"}';
    //traits 
    string[4] maTrait = ["Ag", "Au", "Pt", "Rn"];
    string[13] aTrait = ["Flower Crown", "Night Vision", "Trauma", "Sleek Curl", "Twin Tails", "Red Rag", "Blue Rag", "Snapback", "Crown", "One Peace", "Red Oni", "Blue Oni", "Clown"];
    string[18] moTrait = ["Smile", "Rabbit", "Frown", "Jeez", "Deez", "Grin", "Hungry", "Hillbilly", "Yikes", "Dumber", "Cigarette", "Puke", "Raw", "Tongue", "Surprised", "Stunned", "Chew", "Respirator"]; 
    string[17] eTrait = ["Passive", "Sane", "Wary", "Fine", "Shut", "Glee", "Cool", "Tough", "Archaic", "Sly", "Sharp", "Sad", "Indifferent", "Focused", "Gloomy", "Abnormal", "Gem"];
    

    function tokenURI(uint16 _index) public view virtual returns (string memory) {
        bytes memory json = abi.encodePacked('{"name": "Pixel Tag #',Strings.toString(_index)); // --> JSON HEADER
        bytes memory img = abi.encodePacked('<svg xmlns="http://www.w3.org/2000/svg" witdh="640" height="640" viewBox="0 0 16 16">'); // --> SVG HEADER
        uint32 seed = pixelTags[_index];
        
        string memory trait1;
        string memory trait2;
        string memory trait3;
        string memory trait4;
        
        //colors 
        string memory basePrimeCol;
        string memory baseSecondCol;

        // ------ BASE TRAIT ----- //
        if(seed%99==0) {
            trait1 = maTrait[3];
            basePrimeCol ="179,24%,61%";
            baseSecondCol = "179,100%,86%";
        }
        else if(seed%99>=1 && seed%99<=5){
            trait1 = maTrait[2];
            basePrimeCol ="180,6%,57%";
            baseSecondCol = "178,53%,88%";
        }
        else if(seed%99>=6 && seed%99<=20){
            trait1 = maTrait[1];
            basePrimeCol ="46,67%,48%";
            baseSecondCol = "46,100%,70%";
        }
        else {
            trait1 = maTrait[0];
            basePrimeCol ="180,2%,40%";
            baseSecondCol = "180,2%,80%";
        }
        // ------ ACCESSORY TRAIT ----- //
        if(seed%99>=75) {
            trait2 = "None";
        }
        else {
            trait2 = aTrait[seed%12];
        }
        // ------ MOUTH TRAIT ----- //
        trait3 = moTrait[seed%17];
        // ------ EYE TRAIT ----- //
        trait4 = eTrait[seed%16];

        string memory backgroundColor = Strings.toString((seed%36)*10); 
        string memory soulColor =  Strings.toString((seed%72)*5);

        // ----- JSON ASSEMBLY ------//
        json = abi.encodePacked(json,comb1,backgroundColor,comb2,trait1,comb3,soulColor,comb4,trait2,comb5,trait3);
        json = abi.encodePacked(json,comb6,trait4);

        //BACKGROUND//
        img = abi.encodePacked(img, '<rect x="0" y="0" width="16" height="16" fill="hsl(',backgroundColor,',100%,90%)"/>'); // --> Add Background
        //BASE// 
        img = abi.encodePacked(img, '<polygon points="5,1 5,2 4,2 4,3 3,3 3,4 3,13 4,13 4,14 5,14 5,15 11,15 11,14 12,14 12,13 13,13 13,3 12,3 12,2 11,2 11,1" fill="hsl(',basePrimeCol,')"/>'); 
        img = abi.encodePacked(img, '<polygon points="5,2 5,3 4,3 4,3 4,3 4,4 4,13 5,13 5,14 6,14 6,14 11,14 11,13 11,13 12,13 12,3 11,3 11,2 11,2" fill="hsl(',baseSecondCol,')"/>');
        //ACC
        img = abi.encodePacked(img, TraitAssembly.choseAcc(trait2, seed));
        //MOUTH
        img = abi.encodePacked(img, TraitAssembly.choseMo(trait3));
        //EYES
        img = abi.encodePacked(img, TraitAssembly.choseE(trait4, seed));
        

        img = abi.encodePacked(img, '</svg>');
        json = abi.encodePacked(json,comb7,Base64.encode(img),comb8);
        return string(abi.encodePacked('data:application/json;base64,', Base64.encode(json)));
    }

    function addPT() external {
        pixelTags[pixelIndex] = uint32(bytes4(keccak256(abi.encodePacked(block.timestamp, pixelIndex, msg.sender))));
        pixelIndex++;
    }
    
}