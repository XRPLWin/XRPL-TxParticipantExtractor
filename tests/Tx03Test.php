<?php declare(strict_types=1);

namespace XRPLWin\XRPLTxParticipantExtractor\Tests;

use PHPUnit\Framework\TestCase;
use XRPLWin\XRPLTxParticipantExtractor\TxParticipantExtractor;

/***
 * NFTokenMint
 */
final class Tx03Test extends TestCase
{
    public function testNFTokenMint()
    {
        $transaction = file_get_contents(__DIR__.'/fixtures/tx03.json');
        $transaction = \json_decode($transaction);
        $TxParticipantExtractor = new TxParticipantExtractor($transaction->result);
        $parsedTransaction = $TxParticipantExtractor->result();

        $this->assertIsArray($parsedTransaction);

        $this->assertEquals([
            'rU2T6qNSab9N4SQZAEutwWnkzA7vUGWcfQ',
        ], $parsedTransaction);
        
    }
}