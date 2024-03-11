<?php declare(strict_types=1);

namespace XRPLWin\XRPLTxParticipantExtractor\Tests;

use PHPUnit\Framework\TestCase;
use XRPLWin\XRPLTxParticipantExtractor\TxParticipantExtractor;

/***
 * AMMWithdraw
 */
final class Tx38Test extends TestCase
{
    public function testAMMWithdraw()
    {
        $transaction = file_get_contents(__DIR__.'/fixtures/tx38.json');
        $transaction = \json_decode($transaction);
        $TxParticipantExtractor = new TxParticipantExtractor($transaction->result);

        $parsedTransaction = $TxParticipantExtractor->result();
        $this->assertIsArray($parsedTransaction);

        $this->assertEquals([
            'rMQQUwGFXB1xKEqzeXaLpRJn2fk3T88Efg', //initiator
            'rhjVJF4ccwbnSCU3gVvpyi6KgqUz8bLmcy',
            'rPsLc5urbzLd5S39MWDo8GfkukqTTvdxvt',
            'rpSVjvfXqPtfX5VQU3rKmBbbF2dYeiCc6Q',
            //vote entries:
            'rU4W6ZvbTW9HAxxT685jqGqDNn1egrHfGC',
            'rpWFofBE6nuTaKjYLMHS7o3pNV9UJuVtJc',
            'rNNnGdeXukFLk2KBAadffET5wLNqoRTcVB',
            'rBsCCWiouB8vXGJS7Vw2QBysM5uiczcS2W',
            'rP32yzQE7xX94YcSedw18QquqnN6Tw4zS3',
            'rKtkrtWyD4mNiEYxtKnH9LM7aSAjJGC88e',
            'r4EVW2tiEkTRmxCuyRd35txXPwUBUKG269',
            
        ], $parsedTransaction);

        $accounts = $TxParticipantExtractor->accounts();

        //This regular key is transaction signer
        $this->assertEquals([
            'INITIATOR',
            'TXSIGNER',
            'RIPPLESTATE_HIGHLIMIT_ISSUER',
            'DIRECTORYNODE_OWNER',
            'ACCOUNTROOT_ACCOUNT',
            'RIPPLESTATE_LOWLIMIT_ISSUER'
        ], $accounts['rMQQUwGFXB1xKEqzeXaLpRJn2fk3T88Efg']);

        $this->assertEquals([
            'RIPPLESTATE_LOWLIMIT_ISSUER',
            'AMM_LPTOKENBALANCE_ISSUER',
            'AMM_AUCTIONSLOT_PRICE_ISSUER',
            'DIRECTORYNODE_OWNER',
            'ACCOUNTROOT_ACCOUNT'
        ], $accounts['rhjVJF4ccwbnSCU3gVvpyi6KgqUz8bLmcy']);

        $this->assertEquals([
            'AMM_ASSET2_ISSUER',
            'RIPPLESTATE_HIGHLIMIT_ISSUER',
        ], $accounts['rPsLc5urbzLd5S39MWDo8GfkukqTTvdxvt']);

        $this->assertEquals([
            'AMM_AUCTIONSLOT_ACCOUNT',
            'AMM_VOTEENTRY_ACCOUNT',
        ], $accounts['rpSVjvfXqPtfX5VQU3rKmBbbF2dYeiCc6Q']);

        $this->assertEquals([
            'AMM_VOTEENTRY_ACCOUNT',
        ], $accounts['rBsCCWiouB8vXGJS7Vw2QBysM5uiczcS2W']);
       
    }
}