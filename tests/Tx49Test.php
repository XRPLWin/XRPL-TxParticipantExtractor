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
final class Tx49Test extends TestCase
{
    public function testMPTokenPayment()
    {
        $transaction = file_get_contents(__DIR__.'/fixtures/tx49.json');
        $transaction = \json_decode($transaction);
        $TxParticipantExtractor = new TxParticipantExtractor($transaction->result);

        $parsedTransaction = $TxParticipantExtractor->result();
        $this->assertIsArray($parsedTransaction);
        $accounts = $TxParticipantExtractor->accounts();

        //Initiator
        $this->assertEquals([
            'INITIATOR',
            'TXSIGNER',
            'MPTOKEN_ISSUER',
            'ACCOUNTROOT_ACCOUNT',
            
        ], $accounts['rsHwwSufxZP4TFCd4PmaiCbpA996JVRp1q']);

        //Destination
        $this->assertEquals([
            'DESTINATION',
            'MPTOKEN_ACCOUNT',
            
        ], $accounts['rBhx2s21vR9ch4xRfZYzHkLU4ZfnbmNxSH']);

    }
}