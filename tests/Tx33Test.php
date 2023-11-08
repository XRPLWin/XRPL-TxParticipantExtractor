<?php declare(strict_types=1);

namespace XRPLWin\XRPLTxParticipantExtractor\Tests;

use PHPUnit\Framework\TestCase;
use XRPLWin\XRPLTxParticipantExtractor\TxParticipantExtractor;

/***
 * Import (Xahau B2M - mint)
 */
final class Tx33Test extends TestCase
{
    public function testImport()
    {
        $transaction = file_get_contents(__DIR__.'/fixtures/tx33.json');
        $transaction = \json_decode($transaction);
        $TxParticipantExtractor = new TxParticipantExtractor($transaction->result);

        $parsedTransaction = $TxParticipantExtractor->result();

        $this->assertIsArray($parsedTransaction);

        $this->assertEquals([
            'rwvzHb1Qh5wS3FKiuFDJFspKj8YcZ8KM12',
        ], $parsedTransaction);
    }

    public function testImportWithSpecialAccounts()
    {
        $transaction = file_get_contents(__DIR__.'/fixtures/tx33.json');
        $transaction = \json_decode($transaction);
        $TxParticipantExtractor = new TxParticipantExtractor($transaction->result,['allowSpecialAccounts' => true]);

        $parsedTransaction = $TxParticipantExtractor->result();

        $this->assertIsArray($parsedTransaction);

        $expectedList = [
            'rwvzHb1Qh5wS3FKiuFDJFspKj8YcZ8KM12',
        ];
        sort($expectedList,SORT_REGULAR);
        sort($parsedTransaction,SORT_REGULAR);
        
        $this->assertEquals(\array_values($expectedList), \array_values($parsedTransaction));
    }
}