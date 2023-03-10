<?php declare(strict_types=1);

namespace XRPLWin\XRPLTxParticipantExtractor;

use XRPL_PHP\Core\RippleBinaryCodec\Types\AccountId;

/**
 * Transaction Participant Extractor
 */
class TxParticipantExtractor
{
  /**
   * Special addresses
   * @see https://xrpl.org/accounts.html#special-addresses
   */
  const ACCOUNT_ZERO      = 'rrrrrrrrrrrrrrrrrrrrrhoLvTp';
  const ACCOUNT_ONE       = 'rrrrrrrrrrrrrrrrrrrrBZbvji';
  const ACCOUNT_GENESIS   = 'rHb9CJAWyB4rj91VRWn96DkukG4bwdtyTh';
  const ACCOUNT_BLACKHOLE = 'rrrrrrrrrrrrrrrrrNAMEtxvNvQ';
  const ACCOUNT_NAN       = 'rrrrrrrrrrrrrrrrrrrn5RM1rHd';

  private readonly \stdClass $tx;
  private array $result = [];
  private array $accounts = [];



  public function __construct(\stdClass $tx)
  {
    $this->tx = $tx;

    //Add Account (if exists)
    if(isset($this->tx->Account))
      $this->addAccount($this->tx->Account, 'INITIATOR');

    //Add Issuer (if exists)
    if(isset($this->tx->Issuer))
      $this->addAccount($this->tx->Issuer, 'ISSUER');

    //Add Destination (if exists)
    if(isset($this->tx->Destination))
      $this->addAccount($this->tx->Destination, 'DESTINATION');

    //Add Authorize (if exists) - eg. https://xrpl.org/depositpreauth.html
    if(isset($this->tx->Authorize))
      $this->addAccount($this->tx->Authorize, 'AUTHORIZE');
    
    //Add Owner (if exists) - eg. https://xrpl.org/escrowcancel.html; https://xrpl.org/nftokenburn.html
    if(isset($this->tx->Owner))
      $this->addAccount($this->tx->Owner, 'OWNER');

    //Add TakerGets,TakerPays issuer - eg. https://xrpl.org/offercreate.html
    if(isset($this->tx->TakerGets->issuer))
      $this->addAccount($this->tx->TakerGets->issuer, 'TAKERGETS_ISSUER');
    if(isset($this->tx->TakerPays->issuer))
      $this->addAccount($this->tx->TakerPays->issuer, 'TAKERPAYS_ISSUER');

    # Issuer of token from Amount - eg. https://xrpl.org/payment.html
    if(isset($this->tx->Amount->issuer)) {
      $this->addAccount($this->tx->Amount->issuer, 'AMOUNT_ISSUER');
    }
    
    //Add RegularKey - eg. https://xrpl.org/setregularkey.html
    if(isset($this->tx->RegularKey))
      $this->addAccount($this->tx->RegularKey, 'REGULARKEY');

    //Add LimitAmount issuer - eg. https://xrpl.org/trustset.html
    if(isset($this->tx->LimitAmount->issuer))
      $this->addAccount($this->tx->LimitAmount->issuer, 'LIMITAMOUNT_ISSUER');

    //Add SignerEntries - eg. https://xrpl.org/signerlistset.html
    if(isset($this->tx->SignerEntries) && \is_array($this->tx->SignerEntries)) {
      
      foreach($this->tx->SignerEntries as $s) {
        if(isset($s->SignerEntry->Account))
          $this->addAccount($s->SignerEntry->Account, 'SIGNERENTRIES_SIGNERENTRY_ACCOUNT');
      }
      unset($s);
    }

    //TODO AuthAccounts,BidMax,Amount,Amount2,Asset,Asset2 https://xrpl.org/ammbid.html
     
    //Extract all other participants from meta
    $this->extractAccountsFromMeta();
    $this->removeSpecialAccounts();
    $this->result = \array_keys($this->accounts);
    //foreach($this->result as $r) {echo "'".$r."',".PHP_EOL;}exit;
  }

  /**
   * Extracts all accounts from metadata.
   * 
   * @return void
   */
  private function extractAccountsFromMeta(): void
  {
    if(!isset($this->tx->meta->AffectedNodes))
      return;
    
    foreach($this->tx->meta->AffectedNodes as $n)
    {
      if(isset($n->CreatedNode))
      {
        if(isset($n->CreatedNode->NewFields))
          $this->extract($n->CreatedNode->NewFields, $n->CreatedNode->LedgerEntryType);
      }

      if(isset($n->ModifiedNode))
      {
        if(isset($n->ModifiedNode->PreviousFields))
          $this->extract($n->ModifiedNode->PreviousFields, $n->ModifiedNode->LedgerEntryType);

        if(isset($n->ModifiedNode->FinalFields))
          $this->extract($n->ModifiedNode->FinalFields, $n->ModifiedNode->LedgerEntryType);
      }

      if(isset($n->DeletedNode))
      {
        if(isset($n->DeletedNode->FinalFields))
          $this->extract($n->DeletedNode->FinalFields, $n->DeletedNode->LedgerEntryType);
      }
    }
  }

  /**
   * @param \stdClass $data - metadata changed fields
   * @param string $LedgerEntryType
   * @see https://xrpl.org/ledger-object-types.html
   * @return void
   */
  private function extract(\stdClass $data, string $LedgerEntryType): void
  {
    $subMethod = 'extract_'.$LedgerEntryType;
    $this->$subMethod($data);
    
  }

  /**
   * @see https://xrpl.org/ledger-object-types.html
   * @return void
   */
  private function extract_AccountRoot(\stdClass $data)
  {
    # Account
    if(isset($data->Account)) {
      $this->addAccount($data->Account, 'ACCOUNTROOT_ACCOUNT');
    }

    #NFTokenMinter
    if(isset($data->NFTokenMinter)) {
      $this->addAccount($data->NFTokenMinter, 'ACCOUNTROOT_NFTOKENMINTER');
    }

    #RegularKey
    if(isset($data->RegularKey)) {
      $this->addAccount($data->RegularKey, 'ACCOUNTROOT_REGULARKEY');
    }
  }

  /**
   * @see https://xrpl.org/ledger-object-types.html
   * @return void
   */
  private function extract_Amendments(\stdClass $data)
  {
    //no affected accounts
  }

  /**
   * @see https://xrpl.org/ledger-object-types.html
   * @return void
   */
  private function extract_AMM(\stdClass $data)
  {
    dd('TODO extract_AMM');
  }

  /**
   * @see https://xrpl.org/ledger-object-types.html
   * @return void
   */
  private function extract_Check(\stdClass $data)
  {
    # Account
    if(isset($data->Account)) {
      $this->addAccount($data->Account, 'CHECK_ACCOUNT');
    }

    # Destination
    if(isset($data->Destination)) {
      $this->addAccount($data->Destination, 'CHECK_DESTINATION');
    }

    # SendMax
    if(isset($data->SendMax->issuer)) {
      $this->addAccount($data->SendMax->issuer, 'CHECK_SENDMAX_ISSUER');
    }
  }

  /**
   * @see https://xrpl.org/ledger-object-types.html
   * @return void
   */
  private function extract_DepositPreauth(\stdClass $data)
  {
    # Account
    if(isset($data->Account)) {
      $this->addAccount($data->Account, 'DEPOSITPREAUTH_ACCOUNT');
    }

    # Authorize
    if(isset($data->Authorize)) {
      $this->addAccount($data->Authorize, 'DEPOSITPREAUTH_AUTHORIZE');
    }
  }

   /**
   * @see https://xrpl.org/ledger-object-types.html
   * @return void
   */
  private function extract_DirectoryNode(\stdClass $data)
  {
    # Owner
    if(isset($data->Owner)) {
      $this->addAccount($data->Owner, 'DIRECTORYNODE_OWNER');
    }

    # TakerGetsIssuer
    if(isset($data->TakerGetsIssuer)) {
      $resolved = AccountId::fromHex($data->TakerGetsIssuer)->toJson();
      if(\is_string($resolved))
        $this->addAccount($resolved, 'DIRECTORYNODE_TAKERGETS_ISSUER');
      unset($resolved);
    }

    # TakerPaysIssuer
    if(isset($data->TakerPaysIssuer)) {
      $resolved = AccountId::fromHex($data->TakerPaysIssuer)->toJson();
      if(\is_string($resolved))
        $this->addAccount($resolved, 'DIRECTORYNODE_TAKERPAYS_ISSUER');
      unset($resolved);
    }
  }

  /**
   * @see https://xrpl.org/ledger-object-types.html
   * @return void
   */
  private function extract_Escrow(\stdClass $data)
  {
    # Account
    if(isset($data->Account)) {
      $this->addAccount($data->Account, 'ESCROW_ACCOUNT');
    }

    # Destination
    if(isset($data->Destination)) {
      $this->addAccount($data->Destination, 'ESCROW_DESTINATION');
    }
  }

  /**
   * @see https://xrpl.org/ledger-object-types.html
   * @return void
   */
  private function extract_FeeSettings(\stdClass $data)
  {
    //no affected accounts
  }

  /**
   * @see https://xrpl.org/ledger-object-types.html
   * @return void
   */
  private function extract_LedgerHashes(\stdClass $data)
  {
    //no affected accounts
  }

  /**
   * @see https://xrpl.org/ledger-object-types.html
   * @return void
   */
  private function extract_NegativeUNL(\stdClass $data)
  {
    //no affected accounts
  }

  /**
   * @see https://xrpl.org/ledger-object-types.html
   * @return void
   */
  private function extract_NFTokenOffer(\stdClass $data)
  {
    # Issuer of token from Amount
    if(isset($data->Amount->issuer)) {
      $this->addAccount($data->Amount->issuer, 'NFTOKENOFFER_AMOUNT_ISSUER');
    }

    # Destination
    if(isset($data->Destination)) {
      $this->addAccount($data->Destination, 'NFTOKENOFFER_DESTINATION');
    }

    # Owner
    if(isset($data->Owner)) {
      $this->addAccount($data->Owner, 'NFTOKENOFFER_OWNER');
    }
  }

  /**
   * @see https://xrpl.org/ledger-object-types.html
   * @return void
   */
  private function extract_NFTokenPage(\stdClass $data)
  {
    //no affected accounts
  }

  /**
   * @see https://xrpl.org/ledger-object-types.html
   * @return void
   */
  private function extract_Offer(\stdClass $data)
  {
    # Account
    if(isset($data->Account)) {
      $this->addAccount($data->Account, 'OFFER_ACCOUNT');
    }

    # TakerPays
    if(isset($data->TakerPays->issuer)) {
      $this->addAccount($data->TakerPays->issuer, 'OFFER_TAKERPAYS_ISSUER');
    }

    # TakerGets
    if(isset($data->TakerGets->issuer)) {
      $this->addAccount($data->TakerGets->issuer, 'OFFER_TAKERGETS_ISSUER');
    }
  }

  /**
   * @see https://xrpl.org/ledger-object-types.html
   * @return void
   */
  private function extract_PayChannel(\stdClass $data)
  {
    # Account
    if(isset($data->Account)) {
      $this->addAccount($data->Account, 'PAYCHANNEL_ACCOUNT');
    }

    # Destination
    if(isset($data->Destination)) {
      $this->addAccount($data->Destination, 'PAYCHANNEL_DESTINATION');
    }
  }

  /**
   * @see https://xrpl.org/ledger-object-types.html
   * @return void
   */
  private function extract_RippleState(\stdClass $data)
  {
    # Balance
    if(isset($data->Balance->issuer)) {
      $this->addAccount($data->Balance->issuer, 'RIPPLESTATE_BALANCE_ISSUER');
    }

    # HighLimit
    if(isset($data->HighLimit->issuer)) {
      $this->addAccount($data->HighLimit->issuer, 'RIPPLESTATE_HIGHLIMIT_ISSUER');
    }

    # LowLimit
    if(isset($data->LowLimit->issuer)) {
      $this->addAccount($data->LowLimit->issuer, 'RIPPLESTATE_LOWLIMIT_ISSUER');
    }
  }

  /**
   * @see https://xrpl.org/ledger-object-types.html
   * @return void
   */
  private function extract_SignerList(\stdClass $data)
  {
    if(!isset($data->SignerEntries))
      return;


    foreach($data->SignerEntries as $s) {
      if(isset($s->SignerEntry->Account))
        $this->addAccount($s->SignerEntry->Account, 'SIGNERLIST_SIGNERENTRY_ACCOUNT');
    }
  }

  /**
   * @see https://xrpl.org/ledger-object-types.html
   * @return void
   */
  private function extract_Ticket(\stdClass $data)
  {
    # Account
    if(isset($data->Account)) {
      $this->addAccount($data->Account, 'TICKET_ACCOUNT'); //owner of ticket
    }
  }

  /**
   * Adds new account to list, or if exists adds context if provided.
   * @return void
   */
  private function addAccount(string $account, ?string $context): void
  {
    if(!isset($this->accounts[$account]))
      $this->accounts[$account] = [];

    if($context) {
      if(!in_array($context,$this->accounts[$account]))
        $this->accounts[$account][] = $context;
    }
  }

  private function removeSpecialAccounts()
  {
    unset($this->accounts[self::ACCOUNT_ZERO]);
    unset($this->accounts[self::ACCOUNT_ONE]);
    unset($this->accounts[self::ACCOUNT_GENESIS]);
    unset($this->accounts[self::ACCOUNT_BLACKHOLE]);
    unset($this->accounts[self::ACCOUNT_NAN]);
  }

  /**
   * Returns final result.
   * @return array
   */
  public function result(): array
  {
    return $this->result;
  }

  /**
   * Returns list of accounts with information where in tx account was disovered.
   * @return array
   */
  public function accounts(): array
  {
    return $this->accounts;
  }
}