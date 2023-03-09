<?php declare(strict_types=1);

namespace XRPLWin\XRPLTxParticipantExtractor\Tests;

use PHPUnit\Framework\TestCase;
use XRPLWin\XRPLTxParticipantExtractor\TxParticipantExtractor;

/***
 * SignerListSet
 */
final class Tx20Test extends TestCase
{
    public function testSignerListSet()
    {
        $transaction = file_get_contents(__DIR__.'/fixtures/tx20.json');
        $transaction = \json_decode($transaction);
        $TxParticipantExtractor = new TxParticipantExtractor($transaction->result);

        $parsedTransaction = $TxParticipantExtractor->result();

        $this->assertIsArray($parsedTransaction);

        $this->assertEquals([
            'rf1BiGeXwwQoi8Z2ueFYTEXSwuJYfV2Jpn', //initiator
            //signer entries:
            'ra5nK24KXen9AHvsdFTKHSANinZseWnPcX',
            'rsA2LpzuawewSBQXkiju3YQTMzW13pAAdW',

        ], $parsedTransaction);
        
    }
}