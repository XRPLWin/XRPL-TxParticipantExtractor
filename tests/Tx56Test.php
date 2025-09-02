<?php declare(strict_types=1);

namespace XRPLWin\XRPLTxParticipantExtractor\Tests;

use PHPUnit\Framework\TestCase;
use XRPLWin\XRPLTxParticipantExtractor\TxParticipantExtractor;

/***
 * Firewall
 * @see https://gist.github.com/dangell7/4cdc6109a50a3bb27362e5586b2b25b9
 */
final class Tx56Test extends TestCase
{
    public function testPermissionedDomainSet()
    {
        $transaction = file_get_contents(__DIR__.'/fixtures/tx56.json');
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
            'DIRECTORYNODE_OWNER'
        ], $accounts['r43sjcv7u6A4pqVdPY2rqcy4G54gedVbUf']);

        $this->assertEquals([
            'CREDENTIAL_ISSUER',
        ], $accounts['r9GrSn8m1KyW6VYFrY8mnJSahWp92eztzq']); 

    }
}