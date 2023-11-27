<?php declare(strict_types=1);

namespace XRPLWin\XRPLTxParticipantExtractor\Tests;

use PHPUnit\Framework\TestCase;
use XRPLWin\XRPLTxParticipantExtractor\TxParticipantExtractor;

/***
 * Import (Xahau B2M - mint)
 */
final class Tx34Test extends TestCase
{
    public function testImport()
    {
        $transaction = file_get_contents(__DIR__.'/fixtures/tx34.json');
        $transaction = \json_decode($transaction);
        $TxParticipantExtractor = new TxParticipantExtractor($transaction->result);

        $parsedTransaction = $TxParticipantExtractor->result();
        //dd($TxParticipantExtractor);
        $this->assertIsArray($parsedTransaction);

        $this->assertEquals([
            'r97wuJSQ56xZpkjke6wFCen7nVi8XLWcXG',
            'rK6g2UYc4GpQH8DYdPG7wywyQbxkJpQTTN',
            'ravr52zHtsL6JZrWxz4aZe96rffg1ixwGT',
        ], $parsedTransaction);

        $accounts = $TxParticipantExtractor->accounts();

        $this->assertEquals([
            'INITIATOR',
            'TXSIGNER',
            'ACCOUNTROOT_ACCOUNT',
            'DIRECTORYNODE_OWNER',
            'PAYCHANNEL_ACCOUNT'
        ], $accounts['r97wuJSQ56xZpkjke6wFCen7nVi8XLWcXG']);

        $this->assertEquals([
            'DESTINATION',
            'PAYCHANNEL_DESTINATION',
        ], $accounts['rK6g2UYc4GpQH8DYdPG7wywyQbxkJpQTTN']);

        $this->assertEquals([
            'ACCOUNTROOT_REGULARKEY',
        ], $accounts['ravr52zHtsL6JZrWxz4aZe96rffg1ixwGT']);

        //dd($accounts);
    }
}