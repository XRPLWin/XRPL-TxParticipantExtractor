<?php declare(strict_types=1);

namespace XRPLWin\XRPLTxParticipantExtractor\Tests;

use PHPUnit\Framework\TestCase;
use XRPLWin\XRPLTxParticipantExtractor\TxParticipantExtractor;

/***
 * Firewall
 * @see https://gist.github.com/dangell7/4cdc6109a50a3bb27362e5586b2b25b9
 */
final class Tx55Test extends TestCase
{
    public function testCredentialAccept()
    {
        $transaction = file_get_contents(__DIR__.'/fixtures/tx55.json');
        $transaction = \json_decode($transaction);
        $TxParticipantExtractor = new TxParticipantExtractor($transaction->result);

        $parsedTransaction = $TxParticipantExtractor->result();
        $this->assertIsArray($parsedTransaction);
        $accounts = $TxParticipantExtractor->accounts();
    
        //Initiator
        $this->assertEquals([
            'INITIATOR',
            'TXSIGNER',
            'ACCOUNTROOT_ACCOUNT',
        ], $accounts['ragQU6nhrXFmVbeUSY6JqXabU9GYc3bN6F']);

        $this->assertEquals([
            'ISSUER',
            'CREDENTIAL_ISSUER',
            'CREDENTIAL_SUBJECT',
            'ACCOUNTROOT_ACCOUNT'
        ], $accounts['raJ3p9hK9iQpZhMPayrV259Z9M129RnDvH']); 

    }
}