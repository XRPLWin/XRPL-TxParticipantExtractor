<?php declare(strict_types=1);

namespace XRPLWin\XRPLTxParticipantExtractor\Tests;

use PHPUnit\Framework\TestCase;
use XRPLWin\XRPLTxParticipantExtractor\TxParticipantExtractor;

/***
 * EscrowFinish (signed with other account (regular key))
 */
final class Tx35Test extends TestCase
{
    public function testEscrowFinishSignedWithRegularKey()
    {
        $transaction = file_get_contents(__DIR__.'/fixtures/tx35.json');
        $transaction = \json_decode($transaction);
        $TxParticipantExtractor = new TxParticipantExtractor($transaction->result);

        $parsedTransaction = $TxParticipantExtractor->result();
        //dd($TxParticipantExtractor);
        $this->assertIsArray($parsedTransaction);

        $this->assertEquals([
            'rWinEUKtN3BmYdDoGU6HZ7tTG54BeCAiz',
            'rDL7Fiq6QKmKeJBNSD6AmaaSqEc7R93JPf',
            'rMaxn4x8jLh4i5oUJHzBfDesAigQAmLyTH',
        ], $parsedTransaction);

        $accounts = $TxParticipantExtractor->accounts();

        //This regular key is transaction signer
        $this->assertEquals([
            'TXSIGNER',
            'ACCOUNTROOT_REGULARKEY'
        ], $accounts['rMaxn4x8jLh4i5oUJHzBfDesAigQAmLyTH']);

        $this->assertEquals([
            'INITIATOR',
            'ESCROW_DESTINATION',
            'DIRECTORYNODE_OWNER',
            'ACCOUNTROOT_ACCOUNT'
        ], $accounts['rWinEUKtN3BmYdDoGU6HZ7tTG54BeCAiz']);

        $this->assertEquals([
            'OWNER',
            'DIRECTORYNODE_OWNER',
            'ACCOUNTROOT_ACCOUNT',
            'ESCROW_ACCOUNT'
        ], $accounts['rDL7Fiq6QKmKeJBNSD6AmaaSqEc7R93JPf']);

    }
}