<?php declare(strict_types=1);

namespace XRPLWin\XRPLTxParticipantExtractor\Tests;

use PHPUnit\Framework\TestCase;
use XRPLWin\XRPLTxParticipantExtractor\TxParticipantExtractor;

/***
 * XLS-35 URITokenBurn
 */
final class Tx28Test extends TestCase
{
    public function testUriTokenBurn()
    {
        $transaction = file_get_contents(__DIR__.'/fixtures/tx28.json');
        $transaction = \json_decode($transaction);
        $TxParticipantExtractor = new TxParticipantExtractor($transaction->result);

        $parsedTransaction = $TxParticipantExtractor->result();
        //dd($TxParticipantExtractor->accounts());
        $this->assertIsArray($parsedTransaction);

        $this->assertEquals([
            'rswHs4bzLBSyfd2fWtjuzUxAqudfrzRDtT',
            'rJNTKV22U8n9uBkCsdc8W9ABaiVs1AVwR4'
        ], $parsedTransaction);
    }
}