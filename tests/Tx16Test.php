<?php declare(strict_types=1);

namespace XRPLWin\XRPLTxParticipantExtractor\Tests;

use PHPUnit\Framework\TestCase;
use XRPLWin\XRPLTxParticipantExtractor\TxParticipantExtractor;

/***
 * EscrowFinish
 */
final class Tx16Test extends TestCase
{
    public function testEscrowFinish()
    {
        $transaction = file_get_contents(__DIR__.'/fixtures/tx16.json');
        $transaction = \json_decode($transaction);
        $TxParticipantExtractor = new TxParticipantExtractor($transaction->result);

        $parsedTransaction = $TxParticipantExtractor->result();

        $this->assertIsArray($parsedTransaction);

        $this->assertEquals([
            'rf1BiGeXwwQoi8Z2ueFYTEXSwuJYfV2Jpn',
            'rKDvgGUsNPZxsgmoemfrgXPS2Not4co2op'
        ], $parsedTransaction);
        
    }
}