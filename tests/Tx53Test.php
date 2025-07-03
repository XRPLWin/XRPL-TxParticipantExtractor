<?php declare(strict_types=1);

namespace XRPLWin\XRPLTxParticipantExtractor\Tests;

use PHPUnit\Framework\TestCase;
use XRPLWin\XRPLTxParticipantExtractor\TxParticipantExtractor;

/***
 * Batch
 * Devnet.
 */
final class Tx53Test extends TestCase
{
    public function testBatch()
    {
        $transaction = file_get_contents(__DIR__.'/fixtures/tx53.json');
        $transaction = \json_decode($transaction);
        $TxParticipantExtractor = new TxParticipantExtractor($transaction->result);

        $parsedTransaction = $TxParticipantExtractor->result();
        $this->assertIsArray($parsedTransaction);
        $accounts = $TxParticipantExtractor->accounts();
        //dd($accounts);
        //Batch initiator
        $this->assertEquals([
            'INITIATOR',
            'TXSIGNER',
            'ACCOUNTROOT_ACCOUNT'
        ], $accounts['raDfrqdioDzAWHVyBfsW5sWNWrnsFGTjr4']);

        $this->assertEquals([
            'BATCHSIGNERS_BATCHSIGNER_ACCOUNT',
        ], $accounts['rMcNpeUQjWeAPvBVUSZ85CzyJZTMcWoc7s']);

        $this->assertEquals([
            'BATCHSIGNERS_BATCHSIGNER_ACCOUNT',
        ], $accounts['r9Rxhnf6gHH3oebVDMCg7k1UgvD3o62mse']);

    }
}