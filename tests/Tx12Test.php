<?php declare(strict_types=1);

namespace XRPLWin\XRPLTxParticipantExtractor\Tests;

use PHPUnit\Framework\TestCase;
use XRPLWin\XRPLTxParticipantExtractor\TxParticipantExtractor;

/***
 * NFTokenAcceptOffer
 */
final class Tx12Test extends TestCase
{
    public function testNFTokenAcceptOffer()
    {
        $transaction = file_get_contents(__DIR__.'/fixtures/tx12.json');
        $transaction = \json_decode($transaction);
        $TxParticipantExtractor = new TxParticipantExtractor($transaction->result);

        $parsedTransaction = $TxParticipantExtractor->result();

        $this->assertIsArray($parsedTransaction);

        $this->assertEquals([
            'rPpDcLBcRFYhUqeU9Rmmr5hgJWSkrL4VxP',
            'rHjTJ9eWkPutj3X89sseaRe3kqeeLKMmbg',
            'rDVcd1qz8Vhc84H8ZnA7B1XDomjagLyDFB',
        ], $parsedTransaction);
        
    }
}