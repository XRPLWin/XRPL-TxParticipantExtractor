<?php declare(strict_types=1);

namespace XRPLWin\XRPLTxParticipantExtractor\Tests;

use PHPUnit\Framework\TestCase;
use XRPLWin\XRPLTxParticipantExtractor\TxParticipantExtractor;

/***
 * EscrowCancel
 */
final class Tx15Test extends TestCase
{
    public function testEscrowCancel()
    {
        $transaction = file_get_contents(__DIR__.'/fixtures/tx15.json');
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