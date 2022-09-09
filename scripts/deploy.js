const hre = require("hardhat");


async function main() {

  const contract = await hre.ethers.getContractFactory("OnchainPixelTagsRender");
  const render = await contract.deploy();

  await render.deployed();

  await render.addPT();

  tokenURI = await render.tokenURI(1);
  console.log(tokenURI);
  console.log("\n");
}

// We recommend this pattern to be able to use async/await everywhere
// and properly handle errors.
main()
  .then(() => process.exit(0))
  .catch((error) => {
    console.error(error);
    process.exit(1);
  });