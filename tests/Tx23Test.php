<?php declare(strict_types=1);

namespace XRPLWin\XRPLTxParticipantExtractor\Tests;

use PHPUnit\Framework\TestCase;
use XRPLWin\XRPLTxParticipantExtractor\TxParticipantExtractor;

/***
 * HookState
 */
final class Tx23Test extends TestCase
{
    public function testHookState()
    {
        $transaction = file_get_contents(__DIR__.'/fixtures/tx23.json');
        $transaction = \json_decode($transaction);
        $TxParticipantExtractor = new TxParticipantExtractor($transaction->result);

        $parsedTransaction = $TxParticipantExtractor->result();

        $this->assertIsArray($parsedTransaction);

        $this->assertEquals([
            'rDsNaTF6wGo6Rb2q8PBowRao5rkwjjUnwt',
            'rQUhXd7sopuga3taru3jfvc1BgVbscrb1X'
        ], $parsedTransaction);
        
    }
}