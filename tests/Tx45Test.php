<?php declare(strict_types=1);

namespace XRPLWin\XRPLTxParticipantExtractor\Tests;

use PHPUnit\Framework\TestCase;
use XRPLWin\XRPLTxParticipantExtractor\TxParticipantExtractor;

/***
 * AMMWithdraw
 * Mainnet. AMMWithdraw with 0 XRP yielded small amount of LPToken returned. In return its harder to find AMMAccountID
 *   AMMAccountID is RIPPLESTATE_HIGHLIMIT_ISSUER, we need to detect it also as AMMAccountID as special logic rule detection.
 */
final class Tx45Test extends TestCase
{
    public function testZeroAMMWithdrawLogicDetect()
    {
        $transaction = file_get_contents(__DIR__.'/fixtures/tx45.json');
        $transaction = \json_decode($transaction);
        $TxParticipantExtractor = new TxParticipantExtractor($transaction->result);

        $parsedTransaction = $TxParticipantExtractor->result();
        $this->assertIsArray($parsedTransaction);

        $this->assertEquals([
            'ro1VomW8pj9U3o93bxSHXipaSqQ1siJ5t',
            'rB3nE2RcnhAqUj3xYczakzvF72uW6ML9UM'
        ], $parsedTransaction);
       
        $accounts = $TxParticipantExtractor->accounts();
        
        //This regular key is transaction signer
        $this->assertEquals([
            'INITIATOR',
            'TXSIGNER',
            'RIPPLESTATE_LOWLIMIT_ISSUER',
            'ACCOUNTROOT_ACCOUNT',
        ], $accounts['ro1VomW8pj9U3o93bxSHXipaSqQ1siJ5t']);

        //This is AMM AccountID
        $this->assertEquals([
            'RIPPLESTATE_HIGHLIMIT_ISSUER',
            'AMM_ACCOUNT',
        ], $accounts['rB3nE2RcnhAqUj3xYczakzvF72uW6ML9UM']);

    }
}