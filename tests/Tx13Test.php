<?php declare(strict_types=1);

namespace XRPLWin\XRPLTxParticipantExtractor\Tests;

use PHPUnit\Framework\TestCase;
use XRPLWin\XRPLTxParticipantExtractor\TxParticipantExtractor;

/***
 * Payment
 */
final class Tx13Test extends TestCase
{
    public function testPayment()
    {
        $transaction = file_get_contents(__DIR__.'/fixtures/tx13.json');
        $transaction = \json_decode($transaction);
        $TxParticipantExtractor = new TxParticipantExtractor($transaction->result);

        $parsedTransaction = $TxParticipantExtractor->result();

        $this->assertIsArray($parsedTransaction);

        $this->assertEquals([
            'rogue5HnPRSszD9CWGSUz8UGHMVwSSKF6',  //account
            'rB1CbvwR8Ld6zdTJG96nFRnxF8HvDQooe6',
            'r38UeRHhNLnprf1CjJ3ts4y1TuGCSSY3hL',
            'rCSCManTZ8ME9EoLrSHHYKW8PPwWMgkwr',  //CSC
            'rKiCet8SdvWxPXnAgYarFUXMh1zCPz432Y', //balance change issuer CNY
            'rvYAfWj5gh67oV6fW32ZzP3Aw4Eubs59B',  //USD
            'r4L6ZLHkTytPqDR81H1ysCr6qGv9oJJAKi',
            'rnhxcjE1PPCMdiHY9MvAZ13cQnrQh7yCsC',
        ], $parsedTransaction);
        
    }
}