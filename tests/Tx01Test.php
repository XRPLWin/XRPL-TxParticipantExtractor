<?php declare(strict_types=1);

namespace XRPLWin\XRPLTxParticipantExtractor\Tests;

use PHPUnit\Framework\TestCase;
use XRPLWin\XRPLTxParticipantExtractor\TxParticipantExtractor;

/***
 * NFTokenPage is modified, token is minted on behalf of issuer.
 * @see https://hash.xrp.fans/D904ADB2D6DD9644B7ACC14E351536B8570F8451AAB01E946ADB47B1E381399F/json
 */
final class Tx01Test extends TestCase
{
    public function testNFTokenMintListByIssuer()
    {
        $transaction = file_get_contents(__DIR__.'/fixtures/tx01.json');
        $transaction = \json_decode($transaction);
        $TxParticipantExtractor = new TxParticipantExtractor($transaction->result);
        $parsedTransaction = $TxParticipantExtractor->result();

        $this->assertIsArray($parsedTransaction);
        
        /*$this->assertArrayHasKey('nftokenid',$parsedTransaction);
        $this->assertArrayHasKey('direction',$parsedTransaction);
        $this->assertEquals('00082710B6961B76BA53FED0D85EF7267A4DBD6152FF1C06C11C4978000001DE',$parsedTransaction['nftokenid']);
        $this->assertEquals('IN',$parsedTransaction['direction']);
        $this->assertEquals(['OWNER'],$parsedTransaction['roles']);*/
    }
}