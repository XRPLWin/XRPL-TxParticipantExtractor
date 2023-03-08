<?php declare(strict_types=1);

namespace XRPLWin\XRPLTxParticipantExtractor\Tests;

use PHPUnit\Framework\TestCase;
use XRPLWin\XRPLTxParticipantExtractor\TxParticipantExtractor;

/***
 * NFTokenMint
 */
final class Tx05Test extends TestCase
{
    public function testNFTokenMint()
    {
        $transaction = file_get_contents(__DIR__.'/fixtures/tx05.json');
        $transaction = \json_decode($transaction);
        $TxParticipantExtractor = new TxParticipantExtractor($transaction->result);
        $parsedTransaction = $TxParticipantExtractor->result();

        $this->assertIsArray($parsedTransaction);

        $this->assertEquals([
            'rKC3EJn1qMqkH1TwDLQ1pLaw4cLDsm9Rz7'
        ], $parsedTransaction);
        
    }
}