#Bytecoin Faucet Installation

This faucet runs on a linux environment with PHP and MYSQL, and it was tested on Ubuntu 15.04 with PHP 5.6.4 and MariaDB 5.5.

Faucet is set to work on the same server as bytecoin wallet and bytecoin daemon.

First of all you need to create a new database and create this table on it for the faucet to save all requests:
```
CREATE TABLE IF NOT EXISTS `payouts` (
`id` bigint(20) unsigned NOT NULL,
  `ip_address` varchar(45) NOT NULL,
  `payout_amount` double NOT NULL,
  `payout_address` varchar(100) NOT NULL,
  `payment_id` varchar(75) NULL,
  `transaction_id` varchar(75) NULL,
  `timestamp` datetime NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=latin1;
```

After you create database, copy config.php.sample to config.php and edit config.php with all your custom parameters and also database information.


Now for faucet to communicate with bytecoin wallet you need to run simplewallet as this:

```bash
./simplewallet --wallet-file=wallet.bin --pass=password --rpc-bind-port=8070 --rpc-bind-ip=127.0.0.1
```

Note: Run this command after you already created a wallet with simplewallet commands.

* wallet.bin needs to be the wallet file name that you enter when you created your wallet.
* password needs to be the password to open your wallet
* rpc-bind-port and rpc-bind-ip can be changed if so, you need to edit index.php and request.php (Please don't edit, as you may end opening the wallet rpc to the public)


And bytecoin daemon as this:

```bash
./bytecoind --rpc-bind-ip=127.0.0.1
```

To keep bytecoind and simplewallet on background you can use screen command.

Advertisements can be edited on the index.php they are between this lines for an easy location:

           <!-- ADS ADS ADS ADS ADS ADS ADS ADS ADS -->
           <!-- ADS ADS ADS ADS ADS ADS ADS ADS ADS -->


After all this steps you should be ready to go ;)
