<?php declare(strict_types=1);

namespace XRPLWin\XRPLTxParticipantExtractor\Tests;

use PHPUnit\Framework\TestCase;
use XRPLWin\XRPLTxParticipantExtractor\TxParticipantExtractor;

/***
 * AMMWithdraw
 * Mainnet. AMMWithdraw with 0 XRP yielded small amount of LPToken returned. In return its harder to find AMMAccountID
 *   AMMAccountID is RIPPLESTATE_HIGHLIMIT_ISSUER, we need to detect it also as AMMAccountID as special logic rule detection.
 */
final class Tx46Test extends TestCase
{
    public function testAMMWithdrawLogicDetect()
    {
        $transaction = file_get_contents(__DIR__.'/fixtures/tx46.json');
        $transaction = \json_decode($transaction);
        $TxParticipantExtractor = new TxParticipantExtractor($transaction->result);

        $parsedTransaction = $TxParticipantExtractor->result();
        $this->assertIsArray($parsedTransaction);
        $accounts = $TxParticipantExtractor->accounts();
        //This is AMM AccountID
        $this->assertEquals([
            'DIRECTORYNODE_OWNER',
            'RIPPLESTATE_LOWLIMIT_ISSUER',
            'AMM_ACCOUNT'
            
        ], $accounts['rfVqEPkjvSENLoGdmC5GrK9a3uYHBnoyBT']);

    }
}