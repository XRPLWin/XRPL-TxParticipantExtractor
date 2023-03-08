<?php declare(strict_types=1);

namespace XRPLWin\XRPLTxParticipantExtractor\Tests;

use PHPUnit\Framework\TestCase;
use XRPLWin\XRPLTxParticipantExtractor\TxParticipantExtractor;

/***
 * NFTokenCancelOffer
 */
final class Tx09Test extends TestCase
{
    public function testNFTokenCancelOffer()
    {
        $transaction = file_get_contents(__DIR__.'/fixtures/tx09.json');
        $transaction = \json_decode($transaction);
        $TxParticipantExtractor = new TxParticipantExtractor($transaction->result);
        $parsedTransaction = $TxParticipantExtractor->result();

        $this->assertIsArray($parsedTransaction);

        $this->assertEquals([
            'rnkmrTjpPTnVHkWCkVHLLVpspLKhyBCPm5',
        ], $parsedTransaction);
        
    }
}