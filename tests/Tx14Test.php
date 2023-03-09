<?php declare(strict_types=1);

namespace XRPLWin\XRPLTxParticipantExtractor\Tests;

use PHPUnit\Framework\TestCase;
use XRPLWin\XRPLTxParticipantExtractor\TxParticipantExtractor;

/***
 * EscrowCreate
 */
final class Tx14Test extends TestCase
{
    public function testEscrowCreate()
    {
        $transaction = file_get_contents(__DIR__.'/fixtures/tx14.json');
        $transaction = \json_decode($transaction);
        $TxParticipantExtractor = new TxParticipantExtractor($transaction->result);

        $parsedTransaction = $TxParticipantExtractor->result();

        $this->assertIsArray($parsedTransaction);

        $this->assertEquals([
            'rf1BiGeXwwQoi8Z2ueFYTEXSwuJYfV2Jpn',
            'ra5nK24KXen9AHvsdFTKHSANinZseWnPcX',
        ], $parsedTransaction);
        
    }
}