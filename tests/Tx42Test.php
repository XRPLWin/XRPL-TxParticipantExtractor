<?php declare(strict_types=1);

namespace XRPLWin\XRPLTxParticipantExtractor\Tests;

use PHPUnit\Framework\TestCase;
use XRPLWin\XRPLTxParticipantExtractor\TxParticipantExtractor;

/***
 * AMMBid (with AuthAccounts)
 * @see https://github.com/ripple/explorer/blob/eb1dcb31d97657d6b8a03ab425d7231453ea605e/src/containers/shared/components/Transaction/AMMBid/test/mock_data/amm_bid.json
 */
final class Tx42Test extends TestCase
{
    public function testAMMBidWithAuthAccounts()
    {
        $transaction = file_get_contents(__DIR__.'/fixtures/tx42.json');
        $transaction = \json_decode($transaction);
        $TxParticipantExtractor = new TxParticipantExtractor($transaction->result);

        $parsedTransaction = $TxParticipantExtractor->result();
        $this->assertIsArray($parsedTransaction);

        $this->assertEquals([
            'rUwaiErsYE5kibUUtaPczXZVVd73VNy4R9',
            'rMEdVzU8mtEArzjrN9avm3kA675GX7ez8W',
            'ra8uHq2Qme5j19TqvPzTE2nqT12Zc3xJmK',
            'rU6o2YguZi847RaiH2QGTkL4eZWZjbxZvk',
            'rhpHaFggC92ELty3n3yDEtuFgWxXWkUFET',
            
            
        ], $parsedTransaction);
       
        $accounts = $TxParticipantExtractor->accounts();

        //This regular key is transaction signer
        $this->assertEquals([
            'INITIATOR',
            'TXSIGNER',
            'RIPPLESTATE_LOWLIMIT_ISSUER',
            'ACCOUNTROOT_ACCOUNT',
            'AMM_AUCTIONSLOT_ACCOUNT'
        ], $accounts['rUwaiErsYE5kibUUtaPczXZVVd73VNy4R9']);
        
        $this->assertEquals([
            'AMM_BIDMAX_ISSUER',
            'AMM_BIDMIN_ISSUER',
            'RIPPLESTATE_HIGHLIMIT_ISSUER',
            'AMM_LPTOKENBALANCE_ISSUER',
            'AMM_ACCOUNT',
            'AMM_AUCTIONSLOT_PRICE_ISSUER'
        ], $accounts['rMEdVzU8mtEArzjrN9avm3kA675GX7ez8W']);
        
        $this->assertEquals([
            'AUTH_ACCOUNT',
        ], $accounts['ra8uHq2Qme5j19TqvPzTE2nqT12Zc3xJmK']);

        $this->assertEquals([
            'AUTH_ACCOUNT',
        ], $accounts['rU6o2YguZi847RaiH2QGTkL4eZWZjbxZvk']);
        
        $this->assertEquals([
            'AMM_ASSET2_ISSUER',
        ], $accounts['rhpHaFggC92ELty3n3yDEtuFgWxXWkUFET']);
    }
}