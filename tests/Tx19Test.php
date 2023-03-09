<?php declare(strict_types=1);

namespace XRPLWin\XRPLTxParticipantExtractor\Tests;

use PHPUnit\Framework\TestCase;
use XRPLWin\XRPLTxParticipantExtractor\TxParticipantExtractor;

/***
 * PaymentChannelClaim
 */
final class Tx19Test extends TestCase
{
    public function testPaymentChannelFund()
    {
        $transaction = file_get_contents(__DIR__.'/fixtures/tx19.json');
        $transaction = \json_decode($transaction);
        $TxParticipantExtractor = new TxParticipantExtractor($transaction->result);

        $parsedTransaction = $TxParticipantExtractor->result();

        $this->assertIsArray($parsedTransaction);

        $this->assertEquals([
            'rJnQrhRTXutuSwtrwxYiTkHn4Dtp8sF2LM',
            'rDPGDcJWQrvSpf5F4PQqd1tGYBgPsAHPtr', //destination of paychannel
        ], $parsedTransaction);
        
    }
}