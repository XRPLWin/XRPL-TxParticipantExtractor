<?php declare(strict_types=1);

namespace XRPLWin\XRPLTxParticipantExtractor\Tests;

use PHPUnit\Framework\TestCase;
use XRPLWin\XRPLTxParticipantExtractor\TxParticipantExtractor;

/***
 * AMMBid
 */
final class Tx41Test extends TestCase
{
    public function testAMMBid()
    {
        $transaction = file_get_contents(__DIR__.'/fixtures/tx41.json');
        $transaction = \json_decode($transaction);
        $TxParticipantExtractor = new TxParticipantExtractor($transaction->result);

        $parsedTransaction = $TxParticipantExtractor->result();
        $this->assertIsArray($parsedTransaction);

        $this->assertEquals([
            'rGSViLupvrRGP8hKusnqs66qqG3EqPZGDn',
            'rhjVJF4ccwbnSCU3gVvpyi6KgqUz8bLmcy',
            'rJFE9mHRN5cFqbnCYnFsiKyAHKFsnKcSMy',
            'rPsLc5urbzLd5S39MWDo8GfkukqTTvdxvt',
            'rU4W6ZvbTW9HAxxT685jqGqDNn1egrHfGC',
            'rpWFofBE6nuTaKjYLMHS7o3pNV9UJuVtJc',
            'rNNnGdeXukFLk2KBAadffET5wLNqoRTcVB',
            'rBsCCWiouB8vXGJS7Vw2QBysM5uiczcS2W',
            'rP32yzQE7xX94YcSedw18QquqnN6Tw4zS3',
            'rKtkrtWyD4mNiEYxtKnH9LM7aSAjJGC88e',
            'r4EVW2tiEkTRmxCuyRd35txXPwUBUKG269',
            'rpSVjvfXqPtfX5VQU3rKmBbbF2dYeiCc6Q',
            
        ], $parsedTransaction);
        
        $accounts = $TxParticipantExtractor->accounts();

        //This regular key is transaction signer
        $this->assertEquals([
            'INITIATOR',
            'TXSIGNER',
            'AMM_AUCTIONSLOT_ACCOUNT',
            'RIPPLESTATE_HIGHLIMIT_ISSUER',
            'ACCOUNTROOT_ACCOUNT'
        ], $accounts['rGSViLupvrRGP8hKusnqs66qqG3EqPZGDn']);

        $this->assertEquals([
            'AMM_BIDMAX_ISSUER',
            'AMM_BIDMIN_ISSUER',
            'AMM_AUCTIONSLOT_PRICE_ISSUER',
            'AMM_LPTOKENBALANCE_ISSUER',
            'AMM_ACCOUNT',
            'RIPPLESTATE_LOWLIMIT_ISSUER',
        ], $accounts['rhjVJF4ccwbnSCU3gVvpyi6KgqUz8bLmcy']);

        $this->assertEquals([
            'AMM_AUCTIONSLOT_ACCOUNT',
        ], $accounts['rJFE9mHRN5cFqbnCYnFsiKyAHKFsnKcSMy']);

        $this->assertEquals([
            'AMM_ASSET2_ISSUER',
        ], $accounts['rPsLc5urbzLd5S39MWDo8GfkukqTTvdxvt']);

        $this->assertEquals([
            'AMM_VOTEENTRY_ACCOUNT',
        ], $accounts['rU4W6ZvbTW9HAxxT685jqGqDNn1egrHfGC']);

        $this->assertEquals([
            'AMM_VOTEENTRY_ACCOUNT',
        ], $accounts['rpWFofBE6nuTaKjYLMHS7o3pNV9UJuVtJc']);

        $this->assertEquals([
            'AMM_VOTEENTRY_ACCOUNT',
        ], $accounts['rNNnGdeXukFLk2KBAadffET5wLNqoRTcVB']);

        $this->assertEquals([
            'AMM_VOTEENTRY_ACCOUNT',
        ], $accounts['rBsCCWiouB8vXGJS7Vw2QBysM5uiczcS2W']);

        $this->assertEquals([
            'AMM_VOTEENTRY_ACCOUNT',
        ], $accounts['rP32yzQE7xX94YcSedw18QquqnN6Tw4zS3']);

        $this->assertEquals([
            'AMM_VOTEENTRY_ACCOUNT',
        ], $accounts['rKtkrtWyD4mNiEYxtKnH9LM7aSAjJGC88e']);

        $this->assertEquals([
            'AMM_VOTEENTRY_ACCOUNT',
        ], $accounts['r4EVW2tiEkTRmxCuyRd35txXPwUBUKG269']);

        $this->assertEquals([
            'AMM_VOTEENTRY_ACCOUNT',
        ], $accounts['rpSVjvfXqPtfX5VQU3rKmBbbF2dYeiCc6Q']);

        
        
    }
}