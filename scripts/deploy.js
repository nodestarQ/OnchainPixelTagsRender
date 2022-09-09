const hre = require("hardhat");


async function main() {

  const contract = await hre.ethers.getContractFactory("PixelTags");
  const render = await contract.deploy();

  await render.deployed();

  mintPixelTag("0x8626f6940E2eb28930eFb4CeF49B2d1F2C9C1199");

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