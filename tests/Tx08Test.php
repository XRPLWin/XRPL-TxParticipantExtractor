<?php declare(strict_types=1);

namespace XRPLWin\XRPLTxParticipantExtractor\Tests;

use PHPUnit\Framework\TestCase;
use XRPLWin\XRPLTxParticipantExtractor\TxParticipantExtractor;

/***
 * NFTokenCreateOffer
 */
final class Tx08Test extends TestCase
{
    public function testNFTokenCreateOffer()
    {
        $transaction = file_get_contents(__DIR__.'/fixtures/tx08.json');
        $transaction = \json_decode($transaction);
        $TxParticipantExtractor = new TxParticipantExtractor($transaction->result);
        $parsedTransaction = $TxParticipantExtractor->result();

        $this->assertIsArray($parsedTransaction);

        $this->assertEquals([
            'rPmjAYZJ6WgxoVcpnteZWYUSXfh8RaGnD2',
            'raAMrBFaAqf7cv8U7gZssZptfaKGvZjiga',
        ], $parsedTransaction);
        
    }
}