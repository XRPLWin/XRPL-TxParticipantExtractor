<?php declare(strict_types=1);

namespace XRPLWin\XRPLTxParticipantExtractor\Tests;

use PHPUnit\Framework\TestCase;
use XRPLWin\XRPLTxParticipantExtractor\TxParticipantExtractor;

/***
 * CronSet
 * @see 9FDA78896A426B980EEEAE53E4BE551BBF52236064455E7CD99F40DCE04373E6 xahau testnet
 */
final class Tx59Test extends TestCase
{
    public function testCronSet()
    {
        $transaction = file_get_contents(__DIR__.'/fixtures/tx59.json');
        $transaction = \json_decode($transaction);
        $TxParticipantExtractor = new TxParticipantExtractor($transaction->result);

        $parsedTransaction = $TxParticipantExtractor->result();
        $this->assertIsArray($parsedTransaction);
        $accounts = $TxParticipantExtractor->accounts();

        //AMM account
        $this->assertEquals([
            'INITIATOR',
            'TXSIGNER',
            'ACCOUNTROOT_ACCOUNT',
            'DIRECTORYNODE_OWNER',
            'CRON_OWNER'
        ], $accounts['rsYxnKtb8JBzfG4hp6sVF3WiVNw2broUFo']);
    }
}