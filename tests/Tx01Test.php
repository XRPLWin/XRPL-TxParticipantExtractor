<?php declare(strict_types=1);

namespace XRPLWin\XRPLTxParticipantExtractor\Tests;

use PHPUnit\Framework\TestCase;
use XRPLWin\XRPLTxParticipantExtractor\TxParticipantExtractor;

/***
 * OfferCreate
 */
final class Tx01Test extends TestCase
{
    public function testOfferCreate()
    {
        $transaction = file_get_contents(__DIR__.'/fixtures/tx01.json');
        $transaction = \json_decode($transaction);
        $TxParticipantExtractor = new TxParticipantExtractor($transaction->result);
        $parsedTransaction = $TxParticipantExtractor->result();

        $this->assertIsArray($parsedTransaction);

        $this->assertEquals([
            "rJWSJ8b2DxpvbhJjTA3ZRiEK2xsxZNHaLP",
            "rhub8VRN55s94qWKDv6jmDy1pUykJzF3wq",
            "rETx8GBiH6fxhTcfHM9fGeyShqxozyD3xe",
            "rJU5puBVXYYtxEVj2yQrTb2ySwA8wcqcev",
            "rEcBRYhzi2uRrwACVpTjDPTY853bYnwtKf",
            "ra5J6KL9fbt6EeNt6c1eea3J7BsQJBPApi",
            "rMFDmJA7vtLhFtE23AvseAjJpsDinAHuA8",
            "rBXy89tHLhdYWWUuY6pJPaDWYQ2t7pz5zD",
            "r4UbhViHeao6vWkQkrZmwgcr1Y5DCKea68",
            "r3rhWeE31Jt5sWmi4QiGLMZnY3ENgqw96W",
            "rBLuzcfyq8y9WadyfB451LCnMyvdjBZi5n",
            "rwietsevLFg8XSmG3bEZzFein1g8RBqWDZ",
            "rEKgHxPWWPxb4naHAttqaj3K4Xc6mWC9ZH",
            "r9ezMLUoErY6FdSKnwgoJH77yFGAibysLJ",
            "rUoQzm8rG5jUtvpzRwAdmLoKjjVeWqckTy",
            "rhS2H7ETM3wBkFETvYycoUm9FEDYi44Pg4",
            "rKkrpnUB7smd5Su7eiqTbQWJGLc8bnagRH",
            "rB7HzBnEki8NgjYjBosN4rpDZ3yMiBdeDg",
            "r39rBggWHTUN95x31mAdxPCC7XnhuHRHor",
            "rETSmijMPXT9fnDbLADZnecxgkoJJ6iKUA",
            "rKNRP7Cim1ekmrVQp4NDN9gTh6EVVY1M3F",
        ], $parsedTransaction);

        $accounts = $TxParticipantExtractor->accounts();

        //check who signed (initiator signed)
        $this->assertEquals([
            'INITIATOR',
            'TXSIGNER',
            'RIPPLESTATE_HIGHLIMIT_ISSUER',
            'ACCOUNTROOT_ACCOUNT'
        ], $accounts['rJWSJ8b2DxpvbhJjTA3ZRiEK2xsxZNHaLP']);

        
        //dd($accounts);
        
    }
}