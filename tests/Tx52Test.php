<?php declare(strict_types=1);

namespace XRPLWin\XRPLTxParticipantExtractor\Tests;

use PHPUnit\Framework\TestCase;
use XRPLWin\XRPLTxParticipantExtractor\TxParticipantExtractor;

/***
 * AMMWithdraw
 * AMM account is: rnuESaWHkTUjU9hys6UP5MVsDjrmWRYVk5
 * Case when 3 accounts left, amm acc, issuer 1 and issuer 2
 * Mainnet.
 */
final class Tx52Test extends TestCase
{
    public function testAMMWithdraw()
    {
        $transaction = file_get_contents(__DIR__.'/fixtures/tx52.json');
        $transaction = \json_decode($transaction);
        $TxParticipantExtractor = new TxParticipantExtractor($transaction->result);

        $parsedTransaction = $TxParticipantExtractor->result();
        $this->assertIsArray($parsedTransaction);
        $accounts = $TxParticipantExtractor->accounts();
        //AMM account
        $this->assertEquals([
            'RIPPLESTATE_LOWLIMIT_ISSUER',
            'DIRECTORYNODE_OWNER',
            'AMM_ACCOUNT'
        ], $accounts['rnuESaWHkTUjU9hys6UP5MVsDjrmWRYVk5']);

    }
}