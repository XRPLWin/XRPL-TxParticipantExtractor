<?php declare(strict_types=1);

namespace XRPLWin\XRPLTxParticipantExtractor\Tests;

use PHPUnit\Framework\TestCase;
use XRPLWin\XRPLTxParticipantExtractor\TxParticipantExtractor;

/***
 * AMMWithdraw
 * Happens when liquidity is withdrawn from an AMM pool and there is other pool involvement.
 * @see 2859C63BBFFEB25EE0D116284DC903A2EE3AE8548275D79BD5D33BB5B25C10A9
 */
final class Tx58Test extends TestCase
{
    public function testAMMWithdrawTodo()
    {
        $transaction = file_get_contents(__DIR__.'/fixtures/tx58.json');
        $transaction = \json_decode($transaction);
        $TxParticipantExtractor = new TxParticipantExtractor($transaction->result);

        $parsedTransaction = $TxParticipantExtractor->result();
        $this->assertIsArray($parsedTransaction);
        $accounts = $TxParticipantExtractor->accounts();

        //AMM account
        $this->assertEquals([
            'RIPPLESTATE_HIGHLIMIT_ISSUER',
            'RIPPLESTATE_LOWLIMIT_ISSUER',
            'DIRECTORYNODE_OWNER',
            'AMM_ACCOUNT'
        ], $accounts['rnuESaWHkTUjU9hys6UP5MVsDjrmWRYVk5']);

        //NOT amm account in this case
        $this->assertEquals([
            'DIRECTORYNODE_OWNER',
            'RIPPLESTATE_HIGHLIMIT_ISSUER',
        ], $accounts['r4UPddYeGeZgDhSGPkooURsQtmGda4oYQW']); //not AMM
        $this->assertTrue(true);

    }
}