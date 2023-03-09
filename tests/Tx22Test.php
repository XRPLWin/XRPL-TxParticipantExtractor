<?php declare(strict_types=1);

namespace XRPLWin\XRPLTxParticipantExtractor\Tests;

use PHPUnit\Framework\TestCase;
use XRPLWin\XRPLTxParticipantExtractor\TxParticipantExtractor;

/***
 * TrustSet
 */
final class Tx22Test extends TestCase
{
    public function testTrustSet()
    {
        $transaction = file_get_contents(__DIR__.'/fixtures/tx22.json');
        $transaction = \json_decode($transaction);
        $TxParticipantExtractor = new TxParticipantExtractor($transaction->result);

        $parsedTransaction = $TxParticipantExtractor->result();

        $this->assertIsArray($parsedTransaction);

        $this->assertEquals([
            'r9qheokwreSsfdFqvYzYerrTa6xGpD8N5r',
            'rf1BiGeXwwQoi8Z2ueFYTEXSwuJYfV2Jpn'
        ], $parsedTransaction);
        
    }
}