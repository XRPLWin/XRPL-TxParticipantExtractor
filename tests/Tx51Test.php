<?php declare(strict_types=1);

namespace XRPLWin\XRPLTxParticipantExtractor\Tests;

use PHPUnit\Framework\TestCase;
use XRPLWin\XRPLTxParticipantExtractor\TxParticipantExtractor;

/***
 * AMMWithdraw
 * AMM account is: rpTpRsVYFop6RjAfm46tBwLmBg7xieeZZ4
 * Case when 3 accounts left, amm acc, issuer 1 and issuer 2
 * Mainnet.
 */
final class Tx51Test extends TestCase
{
    public function testAMMWithdraw()
    {
        $transaction = file_get_contents(__DIR__.'/fixtures/tx51.json');
        $transaction = \json_decode($transaction);
        $TxParticipantExtractor = new TxParticipantExtractor($transaction->result);

        $parsedTransaction = $TxParticipantExtractor->result();
        $this->assertIsArray($parsedTransaction);
        $accounts = $TxParticipantExtractor->accounts();
        //dd($accounts);
        //Initiator
        $this->assertEquals([
            'DIRECTORYNODE_OWNER',
            'RIPPLESTATE_LOWLIMIT_ISSUER',
            'AMM_ACCOUNT'
            
        ], $accounts['rpTpRsVYFop6RjAfm46tBwLmBg7xieeZZ4']);

    }
}