<?php declare(strict_types=1);

namespace XRPLWin\XRPLTxParticipantExtractor\Tests;

use PHPUnit\Framework\TestCase;
use XRPLWin\XRPLTxParticipantExtractor\TxParticipantExtractor;

/***
 * AMMWithdraw
 * AMM account is: rUUxn5hxQivA2XyeY2JefmrYF6rZ81277B
 * Case when there is only token issuer and amm account left, in that case RIPPLESTATE_HIGHLIMIT_ISSUER is AMM account
 * Mainnet.
 */
final class Tx48Test extends TestCase
{
    public function testAMMWithdrawLogicDetectIssuerAndAMMAccount()
    {
        $transaction = file_get_contents(__DIR__.'/fixtures/tx48.json');
        $transaction = \json_decode($transaction);
        $TxParticipantExtractor = new TxParticipantExtractor($transaction->result);

        $parsedTransaction = $TxParticipantExtractor->result();
        $this->assertIsArray($parsedTransaction);
        $accounts = $TxParticipantExtractor->accounts();
        
        //This is AMM AccountID
        $this->assertEquals([
            'DIRECTORYNODE_OWNER',
            'RIPPLESTATE_HIGHLIMIT_ISSUER',
            'AMM_ACCOUNT'
            
        ], $accounts['rUUxn5hxQivA2XyeY2JefmrYF6rZ81277B']);

        //Issuer
        $this->assertEquals([
            'DIRECTORYNODE_OWNER',
            'RIPPLESTATE_LOWLIMIT_ISSUER'
            
        ], $accounts['rZapJ1PZ297QAEXRGu3SZkAiwXbA7BNoe']);

        //Initiator
        $this->assertEquals([
            'INITIATOR',
            'TXSIGNER',
            'DIRECTORYNODE_OWNER',
            'ACCOUNTROOT_ACCOUNT',
            'RIPPLESTATE_HIGHLIMIT_ISSUER',
            'RIPPLESTATE_LOWLIMIT_ISSUER'
        ], $accounts['rBaoU3uujvsiTCBbbJYwgXnHnsbVyKMXDS']);

    }
}