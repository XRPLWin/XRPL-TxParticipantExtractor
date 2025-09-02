<?php declare(strict_types=1);

namespace XRPLWin\XRPLTxParticipantExtractor\Tests;

use PHPUnit\Framework\TestCase;
use XRPLWin\XRPLTxParticipantExtractor\TxParticipantExtractor;

/***
 * Firewall
 * @see https://gist.github.com/dangell7/4cdc6109a50a3bb27362e5586b2b25b9
 */
final class Tx57Test extends TestCase
{
    public function testCredentialCreate()
    {
        $transaction = file_get_contents(__DIR__.'/fixtures/tx57.json');
        $transaction = \json_decode($transaction);
        $TxParticipantExtractor = new TxParticipantExtractor($transaction->result);

        $parsedTransaction = $TxParticipantExtractor->result();
        $this->assertIsArray($parsedTransaction);
        $accounts = $TxParticipantExtractor->accounts();

        //Initiator
        $this->assertEquals([
            'INITIATOR',
            'TXSIGNER',
            'DIRECTORYNODE_OWNER',
            'CREDENTIAL_ISSUER',
            'CREDENTIAL_SUBJECT',
            'ACCOUNTROOT_ACCOUNT'
        ], $accounts['rUq5E9JiSJZ939VWeTtXAu5MLYD7PuPY32']);

        $this->assertEquals([
            'SUBJECT',
            'DIRECTORYNODE_OWNER',
        ], $accounts['rLWg5A14rg5uDGwUVBYYncQXeHor82ZaFJ']); 

    }
}