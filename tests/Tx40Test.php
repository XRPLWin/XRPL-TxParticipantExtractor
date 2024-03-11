<?php declare(strict_types=1);

namespace XRPLWin\XRPLTxParticipantExtractor\Tests;

use PHPUnit\Framework\TestCase;
use XRPLWin\XRPLTxParticipantExtractor\TxParticipantExtractor;

/***
 * AMMVote
 */
final class Tx40Test extends TestCase
{
    public function testAMMVote()
    {
        $transaction = file_get_contents(__DIR__.'/fixtures/tx40.json');
        $transaction = \json_decode($transaction);
        $TxParticipantExtractor = new TxParticipantExtractor($transaction->result);

        $parsedTransaction = $TxParticipantExtractor->result();
        $this->assertIsArray($parsedTransaction);

        $this->assertEquals([
            'rMFYxTLxTDpj2YMSovi898vuXeYg91UU1v',
            'rpSVjvfXqPtfX5VQU3rKmBbbF2dYeiCc6Q',
            'rhjVJF4ccwbnSCU3gVvpyi6KgqUz8bLmcy',
            'rU4W6ZvbTW9HAxxT685jqGqDNn1egrHfGC',
            'rpWFofBE6nuTaKjYLMHS7o3pNV9UJuVtJc',
            'rNNnGdeXukFLk2KBAadffET5wLNqoRTcVB',
            'rBsCCWiouB8vXGJS7Vw2QBysM5uiczcS2W',
            'rP32yzQE7xX94YcSedw18QquqnN6Tw4zS3',
            'rKtkrtWyD4mNiEYxtKnH9LM7aSAjJGC88e',
            'r4EVW2tiEkTRmxCuyRd35txXPwUBUKG269',
            'rPsLc5urbzLd5S39MWDo8GfkukqTTvdxvt',
            
        ], $parsedTransaction);

        $accounts = $TxParticipantExtractor->accounts();

        //This regular key is transaction signer
        $this->assertEquals([
            'INITIATOR',
            'TXSIGNER',
            'ACCOUNTROOT_ACCOUNT'
        ], $accounts['rMFYxTLxTDpj2YMSovi898vuXeYg91UU1v']);

        $this->assertEquals([
            'AMM_AUCTIONSLOT_ACCOUNT',
            'AMM_VOTEENTRY_ACCOUNT',
        ], $accounts['rpSVjvfXqPtfX5VQU3rKmBbbF2dYeiCc6Q']);

        $this->assertEquals([
            'AMM_AUCTIONSLOT_PRICE_ISSUER',
            'AMM_ACCOUNT',
            'AMM_LPTOKENBALANCE_ISSUER',
        ], $accounts['rhjVJF4ccwbnSCU3gVvpyi6KgqUz8bLmcy']);

        $this->assertEquals([
            'AMM_VOTEENTRY_ACCOUNT',
        ], $accounts['rU4W6ZvbTW9HAxxT685jqGqDNn1egrHfGC']);

        $this->assertEquals([
            'AMM_VOTEENTRY_ACCOUNT',
        ], $accounts['rKtkrtWyD4mNiEYxtKnH9LM7aSAjJGC88e']);

        $this->assertEquals([
            'AMM_VOTEENTRY_ACCOUNT',
        ], $accounts['r4EVW2tiEkTRmxCuyRd35txXPwUBUKG269']);

        $this->assertEquals([
            'AMM_ASSET2_ISSUER',
        ], $accounts['rPsLc5urbzLd5S39MWDo8GfkukqTTvdxvt']);

        
    }
}