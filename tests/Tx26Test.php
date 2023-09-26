<?php declare(strict_types=1);

namespace XRPLWin\XRPLTxParticipantExtractor\Tests;

use PHPUnit\Framework\TestCase;
use XRPLWin\XRPLTxParticipantExtractor\TxParticipantExtractor;

/***
 * Xahau UNLReport
 */
final class Tx26Test extends TestCase
{
    public function testHookDefinition()
    {
        $transaction = file_get_contents(__DIR__.'/fixtures/tx26.json');
        $transaction = \json_decode($transaction);
        $TxParticipantExtractor = new TxParticipantExtractor($transaction->result);

        $parsedTransaction = $TxParticipantExtractor->result();
        
        $this->assertIsArray($parsedTransaction);

        $this->assertEquals([
            'rGhk2uLd8ShzX2Zrcgn8sQk1LWBG4jjEwf',
            'rnr4kwS1VkJhvjVRuq2fbWZtEdN2HbpVVu',
            'rJupFrPPYgUNFBdoSqhMEJ22hiHKiZSHXQ',
            'roUo3ygV92bdhfE1v9LGpPETXvJv2kQv5',
            'rGsa7f4arJ8JE9ok9LCht6jCu5xBKUKVMq',
            'r3htgPchiR2r8kMGzPK3Wfv3WTrpaRKjtU',
            'rfQtB8m51sdbWgcmddRX2mMjMpSxzX1AGr',
        ], $parsedTransaction);

        $parsedTransactionWithoutEmitted = $TxParticipantExtractor->resultWithoutEmitted();
        //dd( $parsedTransactionWithoutEmitted);
        $this->assertEquals([
            'rGhk2uLd8ShzX2Zrcgn8sQk1LWBG4jjEwf',
            'rnr4kwS1VkJhvjVRuq2fbWZtEdN2HbpVVu',
            'rJupFrPPYgUNFBdoSqhMEJ22hiHKiZSHXQ',
            'roUo3ygV92bdhfE1v9LGpPETXvJv2kQv5',
            'rGsa7f4arJ8JE9ok9LCht6jCu5xBKUKVMq',
            'r3htgPchiR2r8kMGzPK3Wfv3WTrpaRKjtU',
            'rfQtB8m51sdbWgcmddRX2mMjMpSxzX1AGr',
        ], $parsedTransactionWithoutEmitted);


        $parsedTransactionWithRoles = $TxParticipantExtractor->accounts();

        $this->assertEquals([
            'rGhk2uLd8ShzX2Zrcgn8sQk1LWBG4jjEwf' => ['UNLREPORT_OLD_VALIDATOR'],
            'rnr4kwS1VkJhvjVRuq2fbWZtEdN2HbpVVu' => ['UNLREPORT_OLD_VALIDATOR'],
            'rJupFrPPYgUNFBdoSqhMEJ22hiHKiZSHXQ' => ['UNLREPORT_OLD_VALIDATOR'],
            'roUo3ygV92bdhfE1v9LGpPETXvJv2kQv5' => ['UNLREPORT_OLD_VALIDATOR'],
            'rGsa7f4arJ8JE9ok9LCht6jCu5xBKUKVMq' => ['UNLREPORT_ACTIVE_VALIDATOR'],
            'r3htgPchiR2r8kMGzPK3Wfv3WTrpaRKjtU' => ['UNLREPORT_OLD_VALIDATOR'],
            'rfQtB8m51sdbWgcmddRX2mMjMpSxzX1AGr' => ['UNLREPORT_OLD_VALIDATOR'],
        ], $parsedTransactionWithRoles);
        
    }
}