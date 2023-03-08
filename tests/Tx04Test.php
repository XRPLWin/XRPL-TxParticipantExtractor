<?php declare(strict_types=1);

namespace XRPLWin\XRPLTxParticipantExtractor\Tests;

use PHPUnit\Framework\TestCase;
use XRPLWin\XRPLTxParticipantExtractor\TxParticipantExtractor;

/***
 * NFTokenMint
 */
final class Tx04Test extends TestCase
{
    public function testNFTokenMint()
    {
        $transaction = file_get_contents(__DIR__.'/fixtures/tx04.json');
        $transaction = \json_decode($transaction);
        $TxParticipantExtractor = new TxParticipantExtractor($transaction->result);
        $parsedTransaction = $TxParticipantExtractor->result();

        $this->assertIsArray($parsedTransaction);

        $this->assertEquals([
            'rfx2mVhTZzc6bLXKeYyFKtpha2LHrkNZFT',
            'rKgR5LMCU1opzENpP7Qz7bRsQB4MKPpJb4'
        ], $parsedTransaction);
        
    }
}