<?php declare(strict_types=1);

namespace XRPLWin\XRPLTxParticipantExtractor\Tests;

use PHPUnit\Framework\TestCase;
use XRPLWin\XRPLTxParticipantExtractor\TxParticipantExtractor;

/***
 * XLS-35 URITokenBuy
 */
final class Tx30Test extends TestCase
{
    public function testUriTokenBuy()
    {
        $transaction = file_get_contents(__DIR__.'/fixtures/tx30.json');
        $transaction = \json_decode($transaction);
        $TxParticipantExtractor = new TxParticipantExtractor($transaction->result);

        $parsedTransaction = $TxParticipantExtractor->result();
        //dd($TxParticipantExtractor->accounts());
        $this->assertIsArray($parsedTransaction);

        $this->assertEquals([
            'rJNTKV22U8n9uBkCsdc8W9ABaiVs1AVwR4',
            'r9gYbjBfANRfA1JHfaCVfPPGfXYiqQvmhS',
            'rswHs4bzLBSyfd2fWtjuzUxAqudfrzRDtT'
        ], $parsedTransaction);
    }
}