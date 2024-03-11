<?php declare(strict_types=1);

namespace XRPLWin\XRPLTxParticipantExtractor\Tests;

use PHPUnit\Framework\TestCase;
use XRPLWin\XRPLTxParticipantExtractor\TxParticipantExtractor;

/***
 * AMMDeposit
 */
final class Tx39Test extends TestCase
{
    public function testAMMDeposit()
    {
        $transaction = file_get_contents(__DIR__.'/fixtures/tx39.json');
        $transaction = \json_decode($transaction);
        $TxParticipantExtractor = new TxParticipantExtractor($transaction->result);

        $parsedTransaction = $TxParticipantExtractor->result();
        $this->assertIsArray($parsedTransaction);

        $this->assertEquals([
            'rBsnqu7dx1ZETaTwZpTNKfmxaSHveJbAWr', //initiator
            'rPsLc5urbzLd5S39MWDo8GfkukqTTvdxvt',
            'rhjVJF4ccwbnSCU3gVvpyi6KgqUz8bLmcy',
            'rpSVjvfXqPtfX5VQU3rKmBbbF2dYeiCc6Q',
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
            'RIPPLESTATE_LOWLIMIT_ISSUER',
            'ACCOUNTROOT_ACCOUNT',
            'RIPPLESTATE_HIGHLIMIT_ISSUER',
            'DIRECTORYNODE_OWNER'
        ], $accounts['rBsnqu7dx1ZETaTwZpTNKfmxaSHveJbAWr']);

        $this->assertEquals([
            'AMOUNT2_ISSUER',
            'AMM_ASSET2_ISSUER',
            'RIPPLESTATE_HIGHLIMIT_ISSUER',
        ], $accounts['rPsLc5urbzLd5S39MWDo8GfkukqTTvdxvt']);

        $this->assertEquals([
            'AMM_LPTOKENBALANCE_ISSUER',
            'AMM_AUCTIONSLOT_PRICE_ISSUER',
            'RIPPLESTATE_LOWLIMIT_ISSUER',
            'DIRECTORYNODE_OWNER',
            'ACCOUNTROOT_ACCOUNT',
        ], $accounts['rhjVJF4ccwbnSCU3gVvpyi6KgqUz8bLmcy']);

        
    }
}