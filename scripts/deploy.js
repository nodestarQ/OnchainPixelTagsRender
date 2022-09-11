const hre = require("hardhat");


async function main() {

  //10 warriors 3 for team 7 up for grabs
  //9 pixel tags 

  const abashoAdd ="";
  const [owner, player1, player2, player3, player4 ] = await ethers.getSigners();
  const provider = ethers.provider;

  //Library deployment
  const library = await hre.ethers.getContractFactory("TraitAssembly");
  const deploayedLib = await library.deploy();

  //battle royale deployment
  const contractRoyale = await hre.ethers.getContractFactory("PixelRoyale");
  const deployedRoyale = await contractRoyale.deploy();

  //Pixel Tags Deployment
  const contractTags = await hre.ethers.getContractFactory("PixelTags", {
    libraries: {
      TraitAssembly: deploayedLib.address,
    },
  });
  const deployedTags = await contractTags.deploy();
  //Contract Set Up
  console.log(deployedRoyale.address);
  console.log(deployedTags.address);
  console.log("\n");

  await deployedRoyale.setBaseURI("moin/");
  await deployedRoyale.setTagContract(deployedTags.address);
  await deployedTags.setMintContract(deployedRoyale.address);
  await deployedRoyale.teamClaim();
  console.log("Team Claimed: "+await deployedRoyale.totalSupply());
  console.log("\n");
  await deployedRoyale.startMint();
  console.log("Mint Started");
  console.log("\n");
  await deployedRoyale.connect(player1).mint(2);
  console.log(player1.address+" minted 2 tokens");
  console.log("total tokens minted : "+await deployedRoyale.totalSupply());
  console.log("\n");
  await deployedRoyale.connect(player2).mint(2);
  console.log(player2.address+" minted 2 tokens");
  console.log("total tokens minted : "+await deployedRoyale.totalSupply());
  console.log("\n");
  await deployedRoyale.connect(player3).mint(2);
  console.log(player3.address+" minted 2 tokens");
  console.log("total tokens minted : "+await deployedRoyale.totalSupply());
  console.log("\n");
  await deployedRoyale.connect(player4).mint(1);
  console.log(player1.address+" minted 1 token");
  console.log("total tokens minted : "+await deployedRoyale.totalSupply());
  console.log("\n");
  console.log("\n");
  console.log("\n");
  console.log("BR STARTS"); 
  console.log("royale POPULATION: " + await deployedRoyale.getPopulation());
  console.log("TAGS SUPPLY: " +await deployedTags.totalSupply());
  console.log("\n");
  console.log("FIRST KILL");
  let tx = await deployedRoyale.connect(player1).setHP(deployedRoyale.returnRandomId(),-1,{value: ethers.utils.parseEther("1.0")}); 
  console.log("royale POPULATION: " + await deployedRoyale.getPopulation());
  console.log("TAGS SUPPLY: " +await deployedTags.totalSupply());
  let receipt = await tx.wait();
  for (const event of receipt.events) {
    console.log(`Event ${event.event} with args ${event.args}`);
  }
  console.log("\n");
  console.log("SECOND KILL");
  tx = await deployedRoyale.connect(player1).setHP(deployedRoyale.returnRandomId(),-1,{value: ethers.utils.parseEther("1.0")}); 
  console.log("royale POPULATION: " + await deployedRoyale.getPopulation());
  console.log("TAGS SUPPLY: " +await deployedTags.totalSupply());
  receipt = await tx.wait();
  for (const event of receipt.events) {
    console.log(`Event ${event.event} with args ${event.args}`);
  }
  console.log("\n");
  console.log("Third KILL");
  tx = await deployedRoyale.connect(player1).setHP(deployedRoyale.returnRandomId(),-1,{value: ethers.utils.parseEther("1.0")}); 
  console.log("royale POPULATION: " + await deployedRoyale.getPopulation());
  console.log("TAGS SUPPLY: " +await deployedTags.totalSupply());
  receipt = await tx.wait();
  for (const event of receipt.events) {
    console.log(`Event ${event.event} with args ${event.args}`);
  }
  console.log("\n");
  console.log("FOURTH KILL");
  tx = await deployedRoyale.connect(player1).setHP(deployedRoyale.returnRandomId(),-1,{value: ethers.utils.parseEther("1.0")}); 
  console.log("royale POPULATION: " + await deployedRoyale.getPopulation());
  console.log("TAGS SUPPLY: " +await deployedTags.totalSupply());
  receipt = await tx.wait();
  for (const event of receipt.events) {
    console.log(`Event ${event.event} with args ${event.args}`);
  }
  console.log("\n");
  console.log("FIFTH KILL");
  tx = await deployedRoyale.connect(player2).setHP(deployedRoyale.returnRandomId(),-1,{value: ethers.utils.parseEther("1.0")}); 
  console.log("royale POPULATION: " + await deployedRoyale.getPopulation());
  console.log("TAGS SUPPLY: " +await deployedTags.totalSupply());
  receipt = await tx.wait();
  for (const event of receipt.events) {
    console.log(`Event ${event.event} with args ${event.args}`);
  }
  console.log("\n");
  console.log("SIXTH KILL");
  tx = await deployedRoyale.connect(player3).setHP(deployedRoyale.returnRandomId(),-1,{value: ethers.utils.parseEther("1.0")}); 
  console.log("royale POPULATION: " + await deployedRoyale.getPopulation());
  console.log("TAGS SUPPLY: " +await deployedTags.totalSupply());
  receipt = await tx.wait();
  for (const event of receipt.events) {
    console.log(`Event ${event.event} with args ${event.args}`);
  }
  console.log("\n");
  console.log("SEVENTH KILL");
  tx = await deployedRoyale.connect(player4).setHP(deployedRoyale.returnRandomId(),-1,{value: ethers.utils.parseEther("1.0")}); 
  console.log("royale POPULATION: " + await deployedRoyale.getPopulation());
  console.log("TAGS SUPPLY: " +await deployedTags.totalSupply());
  receipt = await tx.wait();
  for (const event of receipt.events) {
    console.log(`Event ${event.event} with args ${event.args}`);
  }
  console.log("\n");
  console.log("EIGHT KILL");
  tx = await deployedRoyale.connect(player4).setHP(deployedRoyale.returnRandomId(),-1,{value: ethers.utils.parseEther("1.0")}); 
  console.log("royale POPULATION: " + await deployedRoyale.getPopulation());
  console.log("TAGS SUPPLY: " +await deployedTags.totalSupply());
  receipt = await tx.wait();
  for (const event of receipt.events) {
    console.log(`Event ${event.event} with args ${event.args}`);
  }
  console.log("\n");
  console.log("NINTH KILL");
  tx = await deployedRoyale.connect(player4).setHP(deployedRoyale.returnRandomId(),-1,{value: ethers.utils.parseEther("1.0")}); 
  console.log("royale POPULATION: " + await deployedRoyale.getPopulation());
  console.log("TAGS SUPPLY: " +await deployedTags.totalSupply());
  receipt = await tx.wait();
  for (const event of receipt.events) {
    console.log(`Event ${event.event} with args ${event.args}`);
  }
  console.log("\n");
  /* REVERT BECAUSE PIXEL ROYALE ALREADY CONCLUDED
  console.log("10TH");
  tx = await deployedRoyale.connect(player4).setHP(deployedRoyale.returnRandomId(),-1,{value: ethers.utils.parseEther("1.0")}); 
  console.log("royale POPULATION: " + await deployedRoyale.getPopulation());
  console.log("TAGS SUPPLY: " +await deployedTags.totalSupply());
  */
  console.log("GAME HAS CONCLUDED");
  console.log("royale POPULATION: " + await deployedRoyale.getPopulation());
  console.log("royale SUPPLY: " +await deployedRoyale.totalSupply());
  console.log(await deployedRoyale.checkLastSurvivor());
  console.log(await deployedRoyale.ownerOf(await deployedRoyale.checkLastSurvivor()));
  //SHOW WINNER WALLETS
  const lastPlayer = await deployedRoyale.lastPlayer();
  const mostAttacks = await deployedRoyale.mostAttacks();
  const randomPlayer = await deployedRoyale.randomPlayer();
  const randomrTags = await deployedRoyale.randomTag(); 
  console.log("lastPlayer: "+ lastPlayer);
  console.log("mostAttacks: "+mostAttacks);
  console.log("randomPlayer: "+randomPlayer);
  console.log("randomTag: "+randomrTags);
  //balances
  console.log("\n");
  const bal1 = ethers.utils.formatEther(await provider.getBalance(deployedRoyale.address));
  const bal2 = ethers.utils.formatEther(await provider.getBalance(owner.address));
  const bal3 = ethers.utils.formatEther(await provider.getBalance(lastPlayer));
  const bal4 = ethers.utils.formatEther(await provider.getBalance(mostAttacks));
  const bal5 = ethers.utils.formatEther(await provider.getBalance(randomPlayer));
  const bal6 = ethers.utils.formatEther(await provider.getBalance(randomrTags));
  
  console.log("PRE WIRTHDRAW "+bal1);
  console.log("BALANCE OWNER: "+bal2);
  console.log("BALANCE LP : "+bal3);
  console.log("BALANCE MA: "+bal4);
  console.log("LP BALANCE RP: "+bal5);
  console.log("LP BALANCE RT: "+bal6);
  console.log("\n");
  await deployedRoyale.distributeToWinners();
  console.log("POST WITHDRAW "+(ethers.utils.formatEther(await provider.getBalance(deployedRoyale.address))-bal1));
  console.log("BALANCE OWNER: "+(ethers.utils.formatEther(await provider.getBalance(owner.address))-bal2));
  console.log("BALANCE LP : "+(ethers.utils.formatEther(await provider.getBalance(lastPlayer))-bal3));
  console.log("BALANCE MA: "+(ethers.utils.formatEther(await provider.getBalance(mostAttacks))-bal4));
  console.log("LP BALANCE RP: "+(ethers.utils.formatEther(await provider.getBalance(randomPlayer))-bal5));
  console.log("LP BALANCE RT: "+(ethers.utils.formatEther(await provider.getBalance(randomrTags))-bal6));
  console.log(await deployedTags.tokenURI(1));
  
  /*
  await render.mintPixelTag();

  tokenURI = await render.tokenURI(1);
  console.log(tokenURI);
  console.log("\n");
  */
}


// We recommend this pattern to be able to use async/await everywhere
// and properly handle errors.
main()
  .then(() => process.exit(0))
  .catch((error) => {
    console.error(error);
    process.exit(1);
  });