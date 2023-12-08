<?php declare(strict_types=1);

namespace XRPLWin\XRPLTxParticipantExtractor\Tests;

use PHPUnit\Framework\TestCase;
use XRPLWin\XRPLTxParticipantExtractor\TxParticipantExtractor;

/***
 * Payment from xahau testnet - missing issuer field
 */
final class Tx36Test extends TestCase
{
    public function testEscrowFinishSignedWithRegularKey()
    {
        $transaction = file_get_contents(__DIR__.'/fixtures/tx36.json');
        $transaction = \json_decode($transaction);
        $TxParticipantExtractor = new TxParticipantExtractor($transaction->result);

        $parsedTransaction = $TxParticipantExtractor->result();

        $this->assertIsArray($parsedTransaction);

        $this->assertEquals([
            'rGTHeSRizAcAQkTvnGuoz3N4YzcDrxH8b9',
            'rKDjeYvaDKhsBaaNDeo41JgPhkgSaSqMoR',
        ], $parsedTransaction);

        $accounts = $TxParticipantExtractor->accounts();

        //This regular key is transaction signer
        $this->assertEquals([
            'INITIATOR',
            'TXSIGNER',
            'EMITTED_ISSUER',
            'ACCOUNTROOT_ACCOUNT'
        ], $accounts['rGTHeSRizAcAQkTvnGuoz3N4YzcDrxH8b9']);
       
        $this->assertEquals([
            'DESTINATION',
            'EMITTED_INITIATOR',
            'ACCOUNTROOT_ACCOUNT',
            //'HOOKEXECUTION_HOOKACCOUNT'
        ], $accounts['rKDjeYvaDKhsBaaNDeo41JgPhkgSaSqMoR']);
    }
}