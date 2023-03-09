<?php declare(strict_types=1);

namespace XRPLWin\XRPLTxParticipantExtractor\Tests;

use PHPUnit\Framework\TestCase;
use XRPLWin\XRPLTxParticipantExtractor\TxParticipantExtractor;

/***
 * PaymentChannelClaim
 */
final class Tx18Test extends TestCase
{
    public function testPaymentChannelClaim()
    {
        $transaction = file_get_contents(__DIR__.'/fixtures/tx18.json');
        $transaction = \json_decode($transaction);
        $TxParticipantExtractor = new TxParticipantExtractor($transaction->result);

        $parsedTransaction = $TxParticipantExtractor->result();

        $this->assertIsArray($parsedTransaction);

        $this->assertEquals([
            'rN7n7otQDd6FczFgLdSqtcsAUxDkw6fzRH',
            'rf1BiGeXwwQoi8Z2ueFYTEXSwuJYfV2Jpn', //was destination
        ], $parsedTransaction);
        
    }
}