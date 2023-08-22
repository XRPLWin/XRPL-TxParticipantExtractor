<?php declare(strict_types=1);

namespace XRPLWin\XRPLTxParticipantExtractor\Tests;

use PHPUnit\Framework\TestCase;
use XRPLWin\XRPLTxParticipantExtractor\TxParticipantExtractor;

/***
 * HookState
 */
final class Tx25Test extends TestCase
{
    public function testHookDefinition()
    {
        $transaction = file_get_contents(__DIR__.'/fixtures/tx25.json');
        $transaction = \json_decode($transaction);
        $TxParticipantExtractor = new TxParticipantExtractor($transaction->result);

        $parsedTransaction = $TxParticipantExtractor->result();

        $this->assertIsArray($parsedTransaction);

        $this->assertEquals([
            'rU32AbyoxZxCEYQAa4ZmcR18e4kius7zxU',
        ], $parsedTransaction);

        $parsedTransactionWithoutEmitted = $TxParticipantExtractor->resultWithoutEmitted();

        $this->assertEquals([
            'rU32AbyoxZxCEYQAa4ZmcR18e4kius7zxU',
        ], $parsedTransactionWithoutEmitted);
        
    }
}