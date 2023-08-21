<?php declare(strict_types=1);

namespace XRPLWin\XRPLTxParticipantExtractor\Tests;

use PHPUnit\Framework\TestCase;
use XRPLWin\XRPLTxParticipantExtractor\TxParticipantExtractor;

/***
 * HookState
 */
final class Tx24Test extends TestCase
{
    public function testEmittedTxn()
    {
        $transaction = file_get_contents(__DIR__.'/fixtures/tx24.json');
        $transaction = \json_decode($transaction);
        $TxParticipantExtractor = new TxParticipantExtractor($transaction->result);

        $parsedTransaction = $TxParticipantExtractor->result();

        $this->assertIsArray($parsedTransaction);

        $this->assertEquals([
            'rKo8nG3i6qmyhcVYV1njnC5JhEYWnwB51w',
            'rssM7AC37o7Qw8DkuWYeqnnSDJvaC6CJDP'
        ], $parsedTransaction);

        $parsedTransactionWithoutEmitted = $TxParticipantExtractor->resultWithoutEmitted();

        $this->assertEquals([
            'rKo8nG3i6qmyhcVYV1njnC5JhEYWnwB51w',
            'rssM7AC37o7Qw8DkuWYeqnnSDJvaC6CJDP'
        ], $parsedTransactionWithoutEmitted);
        
    }
}