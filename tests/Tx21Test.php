<?php declare(strict_types=1);

namespace XRPLWin\XRPLTxParticipantExtractor\Tests;

use PHPUnit\Framework\TestCase;
use XRPLWin\XRPLTxParticipantExtractor\TxParticipantExtractor;

/***
 * TicketCreate
 */
final class Tx21Test extends TestCase
{
    public function testTicketCreate()
    {
        $transaction = file_get_contents(__DIR__.'/fixtures/tx21.json');
        $transaction = \json_decode($transaction);
        $TxParticipantExtractor = new TxParticipantExtractor($transaction->result);

        $parsedTransaction = $TxParticipantExtractor->result();

        $this->assertIsArray($parsedTransaction);

        $this->assertEquals([
            'rGoZQPxErbUtYMQefhRvXLN2epnAmWGZ5Y', //Ticket owner

        ], $parsedTransaction);
        
    }
}