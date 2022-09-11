BLOCKCHAIN BATTLE ROYALE framework
*****************************************************************************************

Goal is to create a set of files to quickly launch an on-chain battle royale contract,
containing a raffle aspect and paying out funds, when the "game" ends.

Parts:

1.) Smart Contract with target NFTs and game rules (how much is paid to whom)

2.) Table in the MySQL database to keep all statuses and track sales of "weapons"

3.) WatchDog for ETH events on the contract and update the database

4.) Website with Javascript game logic, wallet connect and triggering payments

5.) api tool, that allows on-chain checks of the health status, so the game gives a real-time feeling.

=> as much possible will be put into a projectConfig.json, so that settings are
centralized and easier adjustable
*****************************************************************************************
