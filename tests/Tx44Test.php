<?php declare(strict_types=1);

namespace XRPLWin\XRPLTxParticipantExtractor\Tests;

use PHPUnit\Framework\TestCase;
use XRPLWin\XRPLTxParticipantExtractor\TxParticipantExtractor;

/***
 * OracleSet
 * Devnet.
 */
final class Tx44Test extends TestCase
{
    public function testOracleSet()
    {
        $transaction = file_get_contents(__DIR__.'/fixtures/tx44.json');
        $transaction = \json_decode($transaction);
        $TxParticipantExtractor = new TxParticipantExtractor($transaction->result);

        $parsedTransaction = $TxParticipantExtractor->result();
        $this->assertIsArray($parsedTransaction);

        $this->assertEquals([
            'rsG6ds2daE5Nk2ZMtTBd6L7YXjdapTFcmH'
        ], $parsedTransaction);
       
        $accounts = $TxParticipantExtractor->accounts();

        //This regular key is transaction signer
        $this->assertEquals([
            'INITIATOR',
            'TXSIGNER',
            'ACCOUNTROOT_ACCOUNT',
            'ORACLE_OWNER',
            'DIRECTORYNODE_OWNER',
        ], $accounts['rsG6ds2daE5Nk2ZMtTBd6L7YXjdapTFcmH']);
        
    }
}