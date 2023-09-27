<?php declare(strict_types=1);

namespace XRPLWin\XRPLTxParticipantExtractor\Tests;

use PHPUnit\Framework\TestCase;
use XRPLWin\XRPLTxParticipantExtractor\TxParticipantExtractor;

/***
 * XLS-35 URITokenCreateSellOffer
 */
final class Tx29Test extends TestCase
{
    public function testUriTokenCreateSellOffer()
    {
        $transaction = file_get_contents(__DIR__.'/fixtures/tx29.json');
        $transaction = \json_decode($transaction);
        $TxParticipantExtractor = new TxParticipantExtractor($transaction->result);

        $parsedTransaction = $TxParticipantExtractor->result();
        //dd($TxParticipantExtractor->accounts());
        $this->assertIsArray($parsedTransaction);

        $this->assertEquals([
            'rswHs4bzLBSyfd2fWtjuzUxAqudfrzRDtT',
            'r9gYbjBfANRfA1JHfaCVfPPGfXYiqQvmhS'
        ], $parsedTransaction);
    }
}