<?php declare(strict_types=1);

namespace XRPLWin\XRPLTxParticipantExtractor\Tests;

use PHPUnit\Framework\TestCase;
use XRPLWin\XRPLTxParticipantExtractor\TxParticipantExtractor;

/***
 * NFTokenAcceptOffer
 */
final class Tx11Test extends TestCase
{
    public function testNFTokenAcceptOffer()
    {
        $transaction = file_get_contents(__DIR__.'/fixtures/tx11.json');
        $transaction = \json_decode($transaction);
        $TxParticipantExtractor = new TxParticipantExtractor($transaction->result);

        $parsedTransaction = $TxParticipantExtractor->result();

        $this->assertIsArray($parsedTransaction);

        $this->assertEquals([
            'rJezoKZH1nKavjZfjQAAZwvNHR6jabDfek',
            'rMJAXYsbNzhwp7FfYnAsYP5ty3R9XnurPo',
            'rBNkqEPXXzPtJs25yi1Q8SkLrBChdwsjrN',
        ], $parsedTransaction);
        
    }
}