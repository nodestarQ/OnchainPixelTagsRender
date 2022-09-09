// SPDX-License-Identifier: MIT
// www.PixelRoyal.xyz
pragma solidity 0.8.15;

import "@openzeppelin/contracts/access/Ownable.sol";
import "erc721a/contracts/ERC721A.sol";

contract PixelTags is ERC721A, Ownable {
    //---------- Vars ----------//
    address public contractCreator;
    address public pixelRoyale;
    uint256 public constant MAXTAGS = 4443;
    string private baseURI;

    //---------- Construct ERC721A TOKEN ----------//
    constructor() ERC721A("PixelTags BATTLE GAME", "PTBG") {
      contractCreator = msg.sender;
    }

    function _startTokenId() internal view virtual override returns (uint256) {
      return 1;
    }

    //---------------------------------------------------------------------------------------------
    //---------- MINT FUNCTIONS ----------//
    //---------- Set Origin Contract ----------//
    function setMintContract(address _addr) external onlyOwner {
      pixelRoyale = _addr;
    }

    //---------- Mint PixelTag ----------//
    function mintPixelTag(address _receiver) external {
      require(msg.sender == pixelRoyale, "Only Contract can mint");
      uint256 total = totalSupply();
      require(total < MAXTAGS, "The GAME has most likely concluded");
      // Mint
     _safeMint(_receiver, 1);
    }

    //---------------------------------------------------------------------------------------------
    //---------- METADATA & BASEURI ----------//
    function setBaseURI(string memory baseURI_) external onlyOwner {
      baseURI = baseURI_;
    }

    function _baseURI() internal view virtual override returns (string memory) {
      return baseURI;
    }

    function tokenURI(uint256 _tokenId) public view virtual override returns (string memory) {
      require(_exists(_tokenId), 'There is no Token with that ID');
      //IDENTIFIER DNA
      string memory currentBaseURI = _baseURI();
      return bytes(currentBaseURI).length > 0 ? string(abi.encodePacked(currentBaseURI, _toString(_tokenId), '.json')) : '';
    }
}
//---------------------------------------------------------------------------------------------
//---------- LAY OUT INTERFACE ----------//
interface InterfacePixelTags {
    function mintPixelTag(address _receiver) external;
    function ownerOf(uint256 tokenId) external view returns (address);
}