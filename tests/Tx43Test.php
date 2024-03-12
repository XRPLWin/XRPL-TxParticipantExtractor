<?php declare(strict_types=1);

namespace XRPLWin\XRPLTxParticipantExtractor\Tests;

use PHPUnit\Framework\TestCase;
use XRPLWin\XRPLTxParticipantExtractor\TxParticipantExtractor;

/***
 * DIDSet
 * Devnet.
 */
final class Tx43Test extends TestCase
{
    public function testDIDSet()
    {
        $transaction = file_get_contents(__DIR__.'/fixtures/tx43.json');
        $transaction = \json_decode($transaction);
        $TxParticipantExtractor = new TxParticipantExtractor($transaction->result);

        $parsedTransaction = $TxParticipantExtractor->result();
        $this->assertIsArray($parsedTransaction);

        $this->assertEquals([
            'rfbXNzwdcXDJfrTzS7vNBj7NA3beAGf7bv'
        ], $parsedTransaction);
       
        $accounts = $TxParticipantExtractor->accounts();

        //This regular key is transaction signer
        $this->assertEquals([
            'INITIATOR',
            'TXSIGNER',
            'ACCOUNTROOT_ACCOUNT',
            'DIRECTORYNODE_OWNER',
            'DID_ACCOUNT'
        ], $accounts['rfbXNzwdcXDJfrTzS7vNBj7NA3beAGf7bv']);
        
    }
}