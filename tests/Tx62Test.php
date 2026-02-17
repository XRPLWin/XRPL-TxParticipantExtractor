<?php declare(strict_types=1);

namespace XRPLWin\XRPLTxParticipantExtractor\Tests;

use PHPUnit\Framework\TestCase;
use XRPLWin\XRPLTxParticipantExtractor\TxParticipantExtractor;

/***
 * LoanSet
 * @see 2EE6E56970D231971027B9A050A5C2386EFBE941C7488CB74B4CCFD90592EACB xrpl devnet
 */
final class Tx62Test extends TestCase
{
    public function testLoanSet()
    {
        $transaction = file_get_contents(__DIR__.'/fixtures/tx62.json');
        $transaction = \json_decode($transaction);
        $TxParticipantExtractor = new TxParticipantExtractor($transaction->result);

        $parsedTransaction = $TxParticipantExtractor->result();
        
        $this->assertIsArray($parsedTransaction);
        
        $accounts = $TxParticipantExtractor->accounts();
        
        $this->assertCount(5, $accounts);
        
        //Initiator
        $this->assertEquals([
            'INITIATOR',
            'TXSIGNER',
            'VAULT_OWNER',
            'LOANBROKER_OWNER',
            'ACCOUNTROOT_ACCOUNT'
        ], $accounts['rLqFqimsVC6NDzrXd1U2DJ4HpePmY6bNKE']);

        //Broker account
        $this->assertEquals([
            'DIRECTORYNODE_OWNER',
            'LOANBROKER_ACCOUNT',
        ], $accounts['r3Tqo462LWafQrvgYxUA7RsZnZezzZbUmp']);

        $this->assertEquals([
            'LOAN_BORROWER',
            'DIRECTORYNODE_OWNER',
            'RIPPLESTATE_HIGHLIMIT_ISSUER',
            'ACCOUNTROOT_ACCOUNT'
        ], $accounts['r4YvsyQWzLhnTtazA1wpFMBHcfjktw5tsQ']);

        $this->assertEquals([
            'VAULT_ACCOUNT',
            'RIPPLESTATE_HIGHLIMIT_ISSUER',
        ], $accounts['rEChfXTCNbDrqyvMUCETLyjvf8umtdXuRL']);

        $this->assertEquals([
            'VAULT_ASSET_ISSUER',
            'RIPPLESTATE_LOWLIMIT_ISSUER',
        ], $accounts['r3cqktRFGSDsBZZkmo3phWaT278XQ19ySS']);

    }
}