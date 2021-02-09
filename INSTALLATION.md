#WSB Faucet Installation

This faucet runs on a linux environment with PHP and MYSQL, and it was tested on Ubuntu 18.04 with PHP 7.2.24 and mysql 14.4.

Faucet is set to work on the same server as wsb wallet-api and wallstreetbets daemon.

First of all you need to create a new database and create this table on it for the faucet to save all requests:
```
CREATE TABLE IF NOT EXISTS `payouts` (
`id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `ip_address` varchar(45) NOT NULL,
  `payout_amount` double NOT NULL,
  `payout_address` varchar(100) NOT NULL,
  `payment_id` varchar(75) NULL,
  `transaction_id` varchar(75) NULL,
  `timestamp` datetime NOT NULL,
  PRIMARY KEY (id)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1;
```

After you create database, copy config.php.sample to config.php and edit config.php with all your custom parameters and also database information.

And wallstreetsbets daemon as this:

```bash
./wallstreetbetsd --rpc-bind-ip=127.0.0.1
```


Now for faucet to communicate with bytecoin wallet you need to run wallet-api as this:

```bash
./wallet-api -port=8070 --rpc-password=password
```

Create wallet or open wallet
```
curl -X POST "http://127.0.0.1:8070/wallet/create" -H "accept: application/json" -H "X-API-KEY: PASSWORD" -H "Content-Type: application/json" -d "{ \"daemonHost\": \"127.0.0.1\", \"daemonPort\": 19993, \"filename\": \"WALLETNAME.wallet\", \"password\": \"WALLETPASSWORD\"}"
```
Open a already created wallet
```
curl -X POST "http://127.0.0.1:8070/wallet/open" -H "accept: application/json" -H "X-API-KEY: PASSWORD" -H "Content-Type: application/json" -d "{ \"daemonHost\": \"127.0.0.1\", \"daemonPort\": 19993, \"filename\": \"WALLETNAME.wallet\", \"password\": \"WALLETPASSWORD\"}"
```

Get Wallet Address
```
curl -X GET "http://127.0.0.1:8070/addresses" -H "accept: application/json" -H "X-API-KEY: PASSWORD"
```

Get Wallet Mnemonic key
```
curl -X GET "http://127.0.0.1:8070/keys/mnemonic/WALLET_ADDRESS" -H "accept: application/json" -H "X-API-KEY: PASSWORD"
```

Get Wallet Private key
```
curl -X GET "http://127.0.0.1:8070/keys" -H "accept: application/json" -H "X-API-KEY: PASSWORD"
```

* WALLETNAME.wallet needs to be the wallet file name that you enter when you created your wallet.
* WALLETPASSWORD needs to be the password to open your wallet
* rpc-bind-port and rpc-bind-ip can be changed if so, you need to edit index.php and request.php (Please don't edit, as you may end opening the wallet rpc to the public)


To keep wallet-api and wallstreetbetsd on background you can use screen command.  TODO: create a background script

Advertisements can be edited on the index.php they are between this lines for an easy location:

           <!-- ADS ADS ADS ADS ADS ADS ADS ADS ADS -->
           <!-- ADS ADS ADS ADS ADS ADS ADS ADS ADS -->
           
Documentation for wallet-api: https://turtlecoin.github.io/wallet-api-docs/

After all this steps you should be ready to go ;)
