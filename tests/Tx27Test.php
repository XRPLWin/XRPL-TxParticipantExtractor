<?php declare(strict_types=1);

namespace XRPLWin\XRPLTxParticipantExtractor\Tests;

use PHPUnit\Framework\TestCase;
use XRPLWin\XRPLTxParticipantExtractor\TxParticipantExtractor;

/***
 * XLS-35 URITokenMint
 */
final class Tx27Test extends TestCase
{
    public function testUriTokenMint()
    {
        $transaction = file_get_contents(__DIR__.'/fixtures/tx27.json');
        $transaction = \json_decode($transaction);
        $TxParticipantExtractor = new TxParticipantExtractor($transaction->result);

        $parsedTransaction = $TxParticipantExtractor->result();
        
        $this->assertIsArray($parsedTransaction);

        $this->assertEquals([
            'rswHs4bzLBSyfd2fWtjuzUxAqudfrzRDtT',
        ], $parsedTransaction);
    }
}