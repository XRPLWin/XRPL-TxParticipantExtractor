<?php declare(strict_types=1);

namespace XRPLWin\XRPLTxParticipantExtractor\Tests;

use PHPUnit\Framework\TestCase;
use XRPLWin\XRPLTxParticipantExtractor\TxParticipantExtractor;

/***
 * EnableAmendment (Hook genesis on Xahau)
 */
final class Tx32Test extends TestCase
{
    public function testEnableAmendment()
    {
        $transaction = file_get_contents(__DIR__.'/fixtures/tx32.json');
        $transaction = \json_decode($transaction);
        $TxParticipantExtractor = new TxParticipantExtractor($transaction->result);

        $parsedTransaction = $TxParticipantExtractor->result();

        $this->assertIsArray($parsedTransaction);

        $this->assertEquals([
            'rtequsfcSxEerbj18TdS6hUd89vTbaWec',
            'rB3egB3cm51DpFENKH2CameyQJf2fmvN72',
            'r42noEGvAqfwXnDBebeEPfYVswZVe6CKPo',
            'r6QZ6zfK37ZSec5hWiQDtbTxUaU2NWG3F',
            'rD74dUPRFNfgnY2NzrxxYRXN4BrfGSN6Mv',
            'rN7XCq12KBvBLKad3wWsVUwmb3dNx1fx3e',
            'ra7MQw7YoMjUw6thxmSGE6jpAEY3LTHxev',
            'rfMB6RCNdWSB6TJXYwCEU5HvDC2eArJp8h',
            'r9EzMEYr5zuRasrfVKdry9JWLbw9mBe6Jg',
            'r4vv7gFjtWAUxPWfj5puGNeW9U8FGSn7iu',
            'rJFxdrd1BuMeJshRAZBuHP3hex9DjH1nnr',
            'r4FRPZbLnyuVeGiSi1Ap6uaaPvPXYZh1XN',
            'rxah6E9kpp1Zw98MxYFMoWMQ1NHCZSmyx' ,
            'r47tpT8LUoymNgRWzfUq2LdkPRfo4UZSkB',
            'rscan6NzxxSFxEQST8qALrc5v9mpM8fU1j',
            'rwyypATD1dQxDbdQjMvrqnsHr2cQw5rjMh',
            'riLD4RiZcmFLijuBkBr1qLa5tXbojgNSN',
            'rHsh4MNWJKXN2YGtSf95aEzFYzMqwGiBve',
            'rwcL4h6ix5VrxjE6GXNq2svJjnj6H3ZJjv',
            'rpZuvdsDzCLxii1ag9TAyf11Wc43qg4QAG',
            'rfKsmLP6sTfVGDvga6rW6XbmSFUzc3G9f3',
            'rHrptekd18qAGCADzK1kg2QyREiRPuVpTJ',
            'rK2y8aaRdEFpr2GUrqs1iuTCUsWgS5mHHW',
            'rWiNRBZaEHFptxtkdohBbk36UxoHVwvEa',
            'rGshbE2xPc2Jw66iTAXuX5RjXmJW4ohbrY',
            'rpuQonHVeMk1qEz9bWMhDRBDSjvLu2gU1B',
            'r4FF5jjJMS2XqWDyTYStWrgARsj3FjaJ2J',
            'rHMtqVuvEESUhPrsgb8tSa5ghjyoQySfVC',
        ], $parsedTransaction);
    }
}