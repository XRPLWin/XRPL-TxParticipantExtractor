[![CI workflow](https://github.com/XRPLWin/XRPL-TxParticipantExtractor/actions/workflows/main.yml/badge.svg)](https://github.com/XRPLWin/XRPL-TxParticipantExtractor/actions/workflows/main.yml)
[![GitHub license](https://img.shields.io/github/license/XRPLWin/XRPL-TxParticipantExtractor)](https://github.com/XRPLWin/XRPL-TxParticipantExtractor/blob/main/LICENSE)
[![Total Downloads](https://img.shields.io/packagist/dt/xrplwin/xrpl-txparticipantextractor.svg?style=flat)](https://packagist.org/packages/xrplwin/xrpl-txparticipantextractor)

# XRPL Transaction Participant Extractor

## Description

Parses any XRPL Transaction and extracts all participating accounts. This means initiators, balance change participants, signers, regularkey, token issuer accounts - everything.

### Note

This package is provided as is, please test it yourself first.  
Found a bug? [Report issue here](https://github.com/XRPLWin/XRPL-TxParticipantExtractor/issues/new)

## Requirements
- PHP 8.1 or higher
- [Composer](https://getcomposer.org/)

## Installation
To install run

```
composer require xrplwin/xrpl-txparticipantextractor
```

## Usage
```PHP
use XRPLWin\XRPLTxParticipantExtractor\TxParticipantExtractor;

$txResult = [
  "Account": "rJWSJ8b2DxpvbhJjTA3ZRiEK2xsxZNHaLP",
  "Fee": "15",
  //...
]; //see tests/fixtures/tx01.json

$extractor = new TxParticipantExtractor((object)$txResult);
//To allow special accounts use:
//$extractor = new TxParticipantExtractor((object)$txResult,['allowSpecialAccounts' => true]);
$participantList = $extractor->result();

/*
├ This test printed output for $participantList
├ Array (
├     [0] => rJWSJ8b2DxpvbhJjTA3ZRiEK2xsxZNHaLP 
├     [1] => rhub8VRN55s94qWKDv6jmDy1pUykJzF3wq 
├     [2] => rETx8GBiH6fxhTcfHM9fGeyShqxozyD3xe 
├     [3] => rJU5puBVXYYtxEVj2yQrTb2ySwA8wcqcev 
├     [4] => rEcBRYhzi2uRrwACVpTjDPTY853bYnwtKf 
├     [5] => ra5J6KL9fbt6EeNt6c1eea3J7BsQJBPApi 
├     [6] => rMFDmJA7vtLhFtE23AvseAjJpsDinAHuA8 
├     [7] => rBXy89tHLhdYWWUuY6pJPaDWYQ2t7pz5zD 
├     [8] => r4UbhViHeao6vWkQkrZmwgcr1Y5DCKea68 
├     [9] => r3rhWeE31Jt5sWmi4QiGLMZnY3ENgqw96W 
├     [10] => rBLuzcfyq8y9WadyfB451LCnMyvdjBZi5n
├     [11] => rwietsevLFg8XSmG3bEZzFein1g8RBqWDZ
├     [12] => rEKgHxPWWPxb4naHAttqaj3K4Xc6mWC9ZH
├     [13] => r9ezMLUoErY6FdSKnwgoJH77yFGAibysLJ
├     [14] => rUoQzm8rG5jUtvpzRwAdmLoKjjVeWqckTy
├     [15] => rhS2H7ETM3wBkFETvYycoUm9FEDYi44Pg4
├     [16] => rKkrpnUB7smd5Su7eiqTbQWJGLc8bnagRH
├     [17] => rB7HzBnEki8NgjYjBosN4rpDZ3yMiBdeDg
├     [18] => r39rBggWHTUN95x31mAdxPCC7XnhuHRHor
├     [19] => rETSmijMPXT9fnDbLADZnecxgkoJJ6iKUA
├     [20] => rKNRP7Cim1ekmrVQp4NDN9gTh6EVVY1M3F
├ )
┴
*/

$participantDetails = $extractor->accounts();

/*
┐
├ This test printed output for $participantDetails
├ Array (
├     [rJWSJ8b2DxpvbhJjTA3ZRiEK2xsxZNHaLP] => Array
├         (
├             [0] => INITIATOR
├             [1] => RIPPLESTATE_HIGHLIMIT_ISSUER  
├             [2] => ACCOUNTROOT_ACCOUNT
├         )
├
├     [rhub8VRN55s94qWKDv6jmDy1pUykJzF3wq] => Array
├         (
├             [0] => TAKERGETS_ISSUER
├             [1] => RIPPLESTATE_LOWLIMIT_ISSUER   
├             [2] => OFFER_TAKERPAYS_ISSUER        
├             [3] => RIPPLESTATE_HIGHLIMIT_ISSUER  
├             [4] => DIRECTORYNODE_TAKERPAYS_ISSUER
├         )
├
├     [rETx8GBiH6fxhTcfHM9fGeyShqxozyD3xe] => Array
├         (
├             [0] => RIPPLESTATE_HIGHLIMIT_ISSUER  
├             [1] => OFFER_ACCOUNT
├             [2] => DIRECTORYNODE_OWNER
├             [3] => ACCOUNTROOT_ACCOUNT
├         )
├
├     [rJU5puBVXYYtxEVj2yQrTb2ySwA8wcqcev] => Array
├         (
├             [0] => ACCOUNTROOT_ACCOUNT
├             [1] => RIPPLESTATE_HIGHLIMIT_ISSUER  
├             [2] => OFFER_ACCOUNT
├             [3] => DIRECTORYNODE_OWNER
├         )
├
├     [rEcBRYhzi2uRrwACVpTjDPTY853bYnwtKf] => Array
├         (
├             [0] => RIPPLESTATE_HIGHLIMIT_ISSUER  
├             [1] => ACCOUNTROOT_ACCOUNT
├             [2] => DIRECTORYNODE_OWNER
├             [3] => OFFER_ACCOUNT
├         )
├
├     [ra5J6KL9fbt6EeNt6c1eea3J7BsQJBPApi] => Array
├         (
├             [0] => DIRECTORYNODE_OWNER
├             [1] => OFFER_ACCOUNT
├             [2] => RIPPLESTATE_HIGHLIMIT_ISSUER  
├             [3] => ACCOUNTROOT_ACCOUNT
├         )
├
├     [rMFDmJA7vtLhFtE23AvseAjJpsDinAHuA8] => Array
├         (
├             [0] => OFFER_ACCOUNT
├             [1] => RIPPLESTATE_HIGHLIMIT_ISSUER  
├             [2] => ACCOUNTROOT_ACCOUNT
├             [3] => DIRECTORYNODE_OWNER
├         )
├
├     [rBXy89tHLhdYWWUuY6pJPaDWYQ2t7pz5zD] => Array
├         (
├             [0] => RIPPLESTATE_HIGHLIMIT_ISSUER  
├             [1] => ACCOUNTROOT_ACCOUNT
├             [2] => DIRECTORYNODE_OWNER
├             [3] => OFFER_ACCOUNT
├         )
├
├     [r4UbhViHeao6vWkQkrZmwgcr1Y5DCKea68] => Array
├         (
├             [0] => RIPPLESTATE_HIGHLIMIT_ISSUER  
├             [1] => DIRECTORYNODE_OWNER
├             [2] => ACCOUNTROOT_ACCOUNT
├             [3] => OFFER_ACCOUNT
├         )
├
├     [r3rhWeE31Jt5sWmi4QiGLMZnY3ENgqw96W] => Array
├         (
├             [0] => RIPPLESTATE_HIGHLIMIT_ISSUER  
├             [1] => OFFER_ACCOUNT
├             [2] => ACCOUNTROOT_ACCOUNT
├         )
├
├     [rBLuzcfyq8y9WadyfB451LCnMyvdjBZi5n] => Array
├         (
├             [0] => OFFER_ACCOUNT
├             [1] => RIPPLESTATE_HIGHLIMIT_ISSUER  
├             [2] => ACCOUNTROOT_ACCOUNT
├             [3] => DIRECTORYNODE_OWNER
├         )
├
├     [rwietsevLFg8XSmG3bEZzFein1g8RBqWDZ] => Array
├         (
├             [0] => RIPPLESTATE_HIGHLIMIT_ISSUER  
├             [1] => ACCOUNTROOT_ACCOUNT
├             [2] => OFFER_ACCOUNT
├             [3] => DIRECTORYNODE_OWNER
├         )
├
├     [rEKgHxPWWPxb4naHAttqaj3K4Xc6mWC9ZH] => Array
├         (
├             [0] => OFFER_ACCOUNT
├             [1] => ACCOUNTROOT_ACCOUNT
├             [2] => DIRECTORYNODE_OWNER
├             [3] => RIPPLESTATE_HIGHLIMIT_ISSUER  
├         )
├
├     [r9ezMLUoErY6FdSKnwgoJH77yFGAibysLJ] => Array
├         (
├             [0] => DIRECTORYNODE_OWNER
├             [1] => RIPPLESTATE_HIGHLIMIT_ISSUER  
├             [2] => ACCOUNTROOT_ACCOUNT
├             [3] => OFFER_ACCOUNT
├         )
├
├     [rUoQzm8rG5jUtvpzRwAdmLoKjjVeWqckTy] => Array
├         (
├             [0] => OFFER_ACCOUNT
├             [1] => DIRECTORYNODE_OWNER
├             [2] => RIPPLESTATE_HIGHLIMIT_ISSUER  
├             [3] => ACCOUNTROOT_ACCOUNT
├         )
├
├     [rhS2H7ETM3wBkFETvYycoUm9FEDYi44Pg4] => Array
├         (
├             [0] => DIRECTORYNODE_OWNER
├             [1] => RIPPLESTATE_LOWLIMIT_ISSUER   
├             [2] => OFFER_ACCOUNT
├             [3] => ACCOUNTROOT_ACCOUNT
├         )
├
├     [rKkrpnUB7smd5Su7eiqTbQWJGLc8bnagRH] => Array
├         (
├             [0] => RIPPLESTATE_HIGHLIMIT_ISSUER  
├             [1] => DIRECTORYNODE_OWNER
├             [2] => ACCOUNTROOT_ACCOUNT
├             [3] => OFFER_ACCOUNT
├         )
├
├     [rB7HzBnEki8NgjYjBosN4rpDZ3yMiBdeDg] => Array
├         (
├             [0] => OFFER_ACCOUNT
├             [1] => ACCOUNTROOT_ACCOUNT
├             [2] => RIPPLESTATE_HIGHLIMIT_ISSUER  
├             [3] => DIRECTORYNODE_OWNER
├         )
├
├     [r39rBggWHTUN95x31mAdxPCC7XnhuHRHor] => Array
├         (
├             [0] => OFFER_ACCOUNT
├             [1] => RIPPLESTATE_HIGHLIMIT_ISSUER  
├             [2] => ACCOUNTROOT_ACCOUNT
├             [3] => DIRECTORYNODE_OWNER
├         )
├
├     [rETSmijMPXT9fnDbLADZnecxgkoJJ6iKUA] => Array
├         (
├             [0] => OFFER_ACCOUNT
├             [1] => DIRECTORYNODE_OWNER
├             [2] => ACCOUNTROOT_ACCOUNT
├         )
├
├     [rKNRP7Cim1ekmrVQp4NDN9gTh6EVVY1M3F] => Array
├         (
├             [0] => ACCOUNTROOT_REGULARKEY        
├         )
├
├ )
┴
*/
```

## Running tests
Run all tests in "tests" directory.
```
composer test
```
or
```
./vendor/bin/phpunit --testdox
```
