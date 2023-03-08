<?php declare(strict_types=1);

namespace XRPLWin\XRPLTxParticipantExtractor;

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
    
    //TODO AuthAccounts,BidMax,Amount,Amount2,Asset,Asset2 https://xrpl.org/ammbid.html

    //TakerGets TakerPays? - eg. https://xrpl.org/offercreate.html

    //TODO issuer of token from Amount - eg. https://xrpl.org/payment.html

    //TODO RegularSigner

    //Add RegularKey - eg. https://xrpl.org/setregularkey.html
    if(isset($this->tx->RegularKey))
      $this->addAccount($this->tx->RegularKey, 'REGULARKEY');

    //TODO Add SignerEntries - eg. https://xrpl.org/signerlistset.html

    //TODO Add LimitAmount issuer - eg. https://xrpl.org/trustset.html
     
    //Extract all other participants from meta
    $this->extractAccountsFromMeta();

    $this->removeSpecialAccounts();

    $this->result = \array_keys($this->accounts);
    //dd($this->accounts);
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
      //dd($n);
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
  private function extract_NFTokenPage(\stdClass $data)
  {
    //no affected accounts
  }

  /**
   * @see https://xrpl.org/ledger-object-types.html
   * @todo TakerGetsIssuer TakerPaysIssuer - check this
   * @return void
   */
  private function extract_DirectoryNode(\stdClass $data)
  {
    # Owner
    if(isset($data->Owner)) {
      $this->addAccount($data->Owner, 'DIRECTORYNODE_OWNER');
    }
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

  public function accounts(): array
  {
    return $this->accounts;
  }
}