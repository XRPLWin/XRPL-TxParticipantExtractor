<?php declare(strict_types=1);

namespace XRPLWin\XRPLTxParticipantExtractor\Tests;

use PHPUnit\Framework\TestCase;
use XRPLWin\XRPLTxParticipantExtractor\TxParticipantExtractor;

/***
 * AMMCreate
 */
final class Tx37Test extends TestCase
{
    public function testAMMCreate()
    {
        $transaction = file_get_contents(__DIR__.'/fixtures/tx37.json');
        $transaction = \json_decode($transaction);
        $TxParticipantExtractor = new TxParticipantExtractor($transaction->result);

        $parsedTransaction = $TxParticipantExtractor->result();
        $this->assertIsArray($parsedTransaction);

        $this->assertEquals([
            'r8ZUGctKnnKETutuTSVVU1tmMicG2Dsp5', //initiator
            'rP9jPyP5kyvFRb6ZiRghAGw5u8SGAmU4bd', //Amount1 issuer
            'raihScAvmDTQNZUhxv9qrQbdbR7kwgTZSD', //Amount2 issuer
            'raonGnW61wAAjphXRiRh4Lva3nK6Qxbbiy',
            
        ], $parsedTransaction);

        $accounts = $TxParticipantExtractor->accounts();

        //This regular key is transaction signer
        $this->assertEquals([
            'INITIATOR',
            'TXSIGNER',
            'DIRECTORYNODE_OWNER',
            'ACCOUNTROOT_ACCOUNT',
            'RIPPLESTATE_LOWLIMIT_ISSUER',
            'AMM_AUCTIONSLOT_ACCOUNT',
            'AMM_VOTEENTRY_ACCOUNT'
        ], $accounts['r8ZUGctKnnKETutuTSVVU1tmMicG2Dsp5']);
        
        $this->assertEquals([
            'AMOUNT2_ISSUER',
            'DIRECTORYNODE_OWNER',
            'RIPPLESTATE_HIGHLIMIT_ISSUER',
            'AMM_ASSET1_ISSUER'
        ], $accounts['raihScAvmDTQNZUhxv9qrQbdbR7kwgTZSD']); //Amount2, Asset 1

        $this->assertEquals([
            'AMOUNT_ISSUER',
            'RIPPLESTATE_HIGHLIMIT_ISSUER',
            'DIRECTORYNODE_OWNER',
            'AMM_ASSET2_ISSUER',
        ], $accounts['rP9jPyP5kyvFRb6ZiRghAGw5u8SGAmU4bd']); //Amount2, Asset 2

        $this->assertEquals([
            'RIPPLESTATE_LOWLIMIT_ISSUER',
            'DIRECTORYNODE_OWNER',
            'ACCOUNTROOT_ACCOUNT',
            'RIPPLESTATE_HIGHLIMIT_ISSUER',
            'AMM_AUCTIONSLOT_PRICE_ISSUER',
            'AMM_LPTOKENBALANCE_ISSUER',
        ], $accounts['raonGnW61wAAjphXRiRh4Lva3nK6Qxbbiy']);
       
    }
}