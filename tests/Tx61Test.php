<?php declare(strict_types=1);

namespace XRPLWin\XRPLTxParticipantExtractor\Tests;

use PHPUnit\Framework\TestCase;
use XRPLWin\XRPLTxParticipantExtractor\TxParticipantExtractor;

/***
 * LoanBrokerSet
 * @see 2EE6E56970D231971027B9A050A5C2386EFBE941C7488CB74B4CCFD90592EACB xrpl devnet
 */
final class Tx61Test extends TestCase
{
    public function testLoanBrokerSet()
    {
        $transaction = file_get_contents(__DIR__.'/fixtures/tx61.json');
        $transaction = \json_decode($transaction);
        $TxParticipantExtractor = new TxParticipantExtractor($transaction->result);

        $parsedTransaction = $TxParticipantExtractor->result();
        
        $this->assertIsArray($parsedTransaction);
        
        $accounts = $TxParticipantExtractor->accounts();
        dump($accounts);
        $this->assertCount(4, $accounts);
        
        //Initiator
        $this->assertEquals([
            'INITIATOR',
            'TXSIGNER',
            'DIRECTORYNODE_OWNER',
            'ACCOUNTROOT_ACCOUNT',
            'LOANBROKER_OWNER'
        ], $accounts['rwietsevLFg8XSmG3bEZzFein1g8RBqWDZ']);

        //Broker account
        $this->assertEquals([
            'RIPPLESTATE_HIGHLIMIT_ISSUER',
            'ACCOUNTROOT_ACCOUNT',
            'DIRECTORYNODE_OWNER',
            'LOANBROKER_ACCOUNT',
        ], $accounts['rG8XfcWXyjg88zciEuvbUR5BGs5KkcCz24']);

    }
}