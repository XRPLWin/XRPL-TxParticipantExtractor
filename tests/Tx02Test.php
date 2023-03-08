<?php declare(strict_types=1);

namespace XRPLWin\XRPLTxParticipantExtractor\Tests;

use PHPUnit\Framework\TestCase;
use XRPLWin\XRPLTxParticipantExtractor\TxParticipantExtractor;

/***
 * NFTokenMint
 */
final class Tx02Test extends TestCase
{
    public function testNFTokenMint()
    {
        $transaction = file_get_contents(__DIR__.'/fixtures/tx02.json');
        $transaction = \json_decode($transaction);
        $TxParticipantExtractor = new TxParticipantExtractor($transaction->result);
        $parsedTransaction = $TxParticipantExtractor->result();

        $this->assertIsArray($parsedTransaction);

        $this->assertEquals([
            'rfx2mVhTZzc6bLXKeYyFKtpha2LHrkNZFT',
            'rHeRoYtbiMSKhtXm4k7tff1PrcwYnCePR3',
        ], $parsedTransaction);
        
    }
}