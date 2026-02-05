<?php declare(strict_types=1);

namespace XRPLWin\XRPLTxParticipantExtractor\Tests;

use PHPUnit\Framework\TestCase;
use XRPLWin\XRPLTxParticipantExtractor\TxParticipantExtractor;

/***
 * VaultDeposit
 * @see DAFE3CFEEC5E235965FC241CB59072167860B134F7D984E105B31425DFA8ECAE xrpl devnet
 */
final class Tx60Test extends TestCase
{
    public function testVaultDeposit()
    {
        $transaction = file_get_contents(__DIR__.'/fixtures/tx60.json');
        $transaction = \json_decode($transaction);
        $TxParticipantExtractor = new TxParticipantExtractor($transaction->result);

        $parsedTransaction = $TxParticipantExtractor->result();
        
        $this->assertIsArray($parsedTransaction);
        
        $accounts = $TxParticipantExtractor->accounts();

        $this->assertCount(2, $accounts);
        
        //Initiator
        $this->assertEquals([
            'INITIATOR',
            'TXSIGNER',
            'MPTOKEN_ACCOUNT',
            'VAULT_OWNER',
            'ACCOUNTROOT_ACCOUNT'
        ], $accounts['r4SwQCi1TPhtUeaXv4iVWiuzyLqK7jxUwN']);

        //MPT Issuer
        $this->assertEquals([
            'VAULT_ACCOUNT',
            'ACCOUNTROOT_ACCOUNT',
            'MPTOKEN_ISSUER'
        ], $accounts['rDbV87m4C8Ns6wqUDCPAUsNSNtHHXzgKPF']);
    }
}