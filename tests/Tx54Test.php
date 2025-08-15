<?php declare(strict_types=1);

namespace XRPLWin\XRPLTxParticipantExtractor\Tests;

use PHPUnit\Framework\TestCase;
use XRPLWin\XRPLTxParticipantExtractor\TxParticipantExtractor;

/***
 * Firewall
 * @see https://gist.github.com/dangell7/4cdc6109a50a3bb27362e5586b2b25b9
 */
final class Tx54Test extends TestCase
{
    public function testFirewall()
    {
        $transaction = file_get_contents(__DIR__.'/fixtures/tx54.json');
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
            'DIRECTORYNODE_OWNER',
            'FIREWALL_OWNER',
            'WITHDRAWPREAUTH_ACCOUNT',
        ], $accounts['rfeZgd8GgQyZ5Pdh5XsLddEo7EjQiQPZD3']);

        $this->assertEquals([
            'COUNTERPARTY',
            'FIREWALL_COUNTERPARTY',
        ], $accounts['rf5cfYRf2EKVgTa4i3wp6JBzC1jsiBZdJe']);

        $this->assertEquals([
            'BACKUP',
            'WITHDRAWPREAUTH_AUTHORIZE',
        ], $accounts['rEULJ6VeKU85xCS3Nk2VjbZzfB3YgYspur']);

    }
}