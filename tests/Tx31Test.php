<?php declare(strict_types=1);

namespace XRPLWin\XRPLTxParticipantExtractor\Tests;

use PHPUnit\Framework\TestCase;
use XRPLWin\XRPLTxParticipantExtractor\TxParticipantExtractor;

/***
 * UNLReport (first)
 */
final class Tx31Test extends TestCase
{
    public function testUriTokenBuy()
    {
        $transaction = file_get_contents(__DIR__.'/fixtures/tx31.json');
        $transaction = \json_decode($transaction);
        $TxParticipantExtractor = new TxParticipantExtractor($transaction->result);

        $parsedTransaction = $TxParticipantExtractor->result();

        $this->assertIsArray($parsedTransaction);

        $this->assertEquals([
            'rGhk2uLd8ShzX2Zrcgn8sQk1LWBG4jjEwf',
            'rJupFrPPYgUNFBdoSqhMEJ22hiHKiZSHXQ',
            'rGsa7f4arJ8JE9ok9LCht6jCu5xBKUKVMq',
            'r3htgPchiR2r8kMGzPK3Wfv3WTrpaRKjtU',
            'rfQtB8m51sdbWgcmddRX2mMjMpSxzX1AGr'

        ], $parsedTransaction);
    }
}