<?php declare(strict_types=1);

namespace XRPLWin\XRPLTxParticipantExtractor\Tests;

use PHPUnit\Framework\TestCase;
use XRPLWin\XRPLTxParticipantExtractor\TxParticipantExtractor;

/***
 * NFTokenAcceptOffer
 */
final class Tx10Test extends TestCase
{
    public function testNFTokenAcceptOffer()
    {
        $transaction = file_get_contents(__DIR__.'/fixtures/tx10.json');
        $transaction = \json_decode($transaction);
        $TxParticipantExtractor = new TxParticipantExtractor($transaction->result);

        $parsedTransaction = $TxParticipantExtractor->result();

        $this->assertIsArray($parsedTransaction);

        $this->assertEquals([
            'rDuck4z5jdAJLDaRMwpc2xZhsCKqqTMRsr',
            'rsa614fckHaBjDpCcZNQqfvVFVPYZzPvE2',
            'rpAETGuhJW5ZYfg3PdsCTELv4ho147AoE9',
        ], $parsedTransaction);
        
    }
}