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
  const ACCOUNT_GENESIS   = 'rHb9CJAWyB4rj91VRWn96DkukG4bwdtyTh'; //XRPL Genesis
  const ACCOUNT_BLACKHOLE = 'rrrrrrrrrrrrrrrrrNAMEtxvNvQ';
  const ACCOUNT_NAN       = 'rrrrrrrrrrrrrrrrrrrn5RM1rHd';

  private readonly \stdClass $tx;
  private array $result = [];
  private array $accounts = [];

  public function __construct(\stdClass $tx, array $options = [])
  {
    $this->tx = $tx;

    //Add Account (if exists)
    if(isset($this->tx->Account) && $this->tx->Account)
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
    $this->normalizeAccounts();
    if(isset($options['allowSpecialAccounts']) && $options['allowSpecialAccounts']) {
      //do not remove special accounts
    } else {
      $this->removeSpecialAccounts();
    }
      
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
    $meta = isset($this->tx->meta) ? $this->tx->meta : $this->tx->metaData;

    if(!isset($meta->AffectedNodes))
      return;
      
    foreach($meta->AffectedNodes as $n)
    {
      if(isset($n->CreatedNode))
      {
        
        if(isset($n->CreatedNode->NewFields))
          $this->extract($n->CreatedNode->NewFields, $n->CreatedNode->LedgerEntryType, 'new');
      }

      if(isset($n->ModifiedNode))
      {
        if(isset($n->ModifiedNode->PreviousFields))
          $this->extract($n->ModifiedNode->PreviousFields, $n->ModifiedNode->LedgerEntryType, 'prev');

        if(isset($n->ModifiedNode->FinalFields))
          $this->extract($n->ModifiedNode->FinalFields, $n->ModifiedNode->LedgerEntryType, 'final');
      }

      if(isset($n->DeletedNode))
      {
        if(isset($n->DeletedNode->FinalFields))
          $this->extract($n->DeletedNode->FinalFields, $n->DeletedNode->LedgerEntryType, 'deleted');
      }
    }
  }

  /**
   * @param \stdClass $data - metadata changed fields
   * @param string $LedgerEntryType
   * @see https://xrpl.org/ledger-object-types.html
   * @return void
   */
  private function extract(\stdClass $data, string $LedgerEntryType, ?string $context = null): void
  {
    $subMethod = 'extract_'.$LedgerEntryType;
    $this->$subMethod($data,$context);
  }

  /**
   * @see https://xrpl.org/ledger-object-types.html
   * @return void
   */
  private function extract_AccountRoot(\stdClass $data, ?string $context = null)
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
  private function extract_Amendments(\stdClass $data, ?string $context = null)
  {
    //no affected accounts
  }

  /**
   * @see https://xrpl.org/ledger-object-types.html
   * @return void
   */
  //private function extract_AMM(\stdClass $data, ?string $context = null)
  //{
  //  dd('TODO extract_AMM');
  //}

  /**
   * @see https://xrpl.org/ledger-object-types.html
   * @return void
   */
  private function extract_Check(\stdClass $data, ?string $context = null)
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
  private function extract_DepositPreauth(\stdClass $data, ?string $context = null)
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
  private function extract_DirectoryNode(\stdClass $data, ?string $context = null)
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
  private function extract_Escrow(\stdClass $data, ?string $context = null)
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
  private function extract_FeeSettings(\stdClass $data, ?string $context = null)
  {
    //no affected accounts
  }

  /**
   * @see https://xrpl.org/ledger-object-types.html
   * @return void
   */
  private function extract_LedgerHashes(\stdClass $data, ?string $context = null)
  {
    //no affected accounts
  }

  /**
   * @see https://xrpl.org/ledger-object-types.html
   * @return void
   */
  private function extract_NegativeUNL(\stdClass $data, ?string $context = null)
  {
    //no affected accounts
  }

  /**
   * @see https://xrpl.org/ledger-object-types.html
   * @return void
   */
  private function extract_NFTokenOffer(\stdClass $data, ?string $context = null)
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
  private function extract_NFTokenPage(\stdClass $data, ?string $context = null)
  {
    //no affected accounts
  }

  /**
   * @see https://xrpl.org/ledger-object-types.html
   * @return void
   */
  private function extract_Offer(\stdClass $data, ?string $context = null)
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
  private function extract_PayChannel(\stdClass $data, ?string $context = null)
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
  private function extract_RippleState(\stdClass $data, ?string $context = null)
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
  private function extract_SignerList(\stdClass $data, ?string $context = null)
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
  private function extract_Ticket(\stdClass $data, ?string $context = null)
  {
    # Account
    if(isset($data->Account)) {
      $this->addAccount($data->Account, 'TICKET_ACCOUNT'); //owner of ticket
    }
  }

  
  # HOOKS START
  private function extract_HookState(\stdClass $data, ?string $context = null)
  {
    //no affected accounts
  }

  private function extract_Hook(\stdClass $data, ?string $context = null)
  {
    //no affected accounts
  }

  private function extract_HookDefinition(\stdClass $data, ?string $context = null)
  {
    //no affected accounts
  }

  private function extract_EmittedTxn(\stdClass $data, ?string $context = null)
  {
    //Add Account (if exists)
    if(isset($data->EmittedTxn->Account))
      $this->addAccount($data->EmittedTxn->Account, 'EMITTED_INITIATOR');
 
    //Add Issuer (if exists)
    if(isset($data->EmittedTxn->Issuer))
      $this->addAccount($this->tx->Issuer, 'EMITTED_ISSUER');

    //Add Destination (if exists)
    if(isset($data->EmittedTxn->Destination))
      $this->addAccount($data->EmittedTxn->Destination, 'EMITTED_DESTINATION');

    //Add Authorize (if exists) - eg. https://xrpl.org/depositpreauth.html
    if(isset($data->EmittedTxn->tx->Authorize))
      $this->addAccount($this->tx->Authorize, 'EMITTED_AUTHORIZE');
    
    //Add Owner (if exists) - eg. https://xrpl.org/escrowcancel.html; https://xrpl.org/nftokenburn.html
    if(isset($data->EmittedTxn->Owner))
      $this->addAccount($data->EmittedTxn->Owner, 'EMITTED_OWNER');

    //Add TakerGets,TakerPays issuer - eg. https://xrpl.org/offercreate.html
    if(isset($data->EmittedTxn->TakerGets->issuer))
      $this->addAccount($data->EmittedTxn->TakerGets->issuer, 'EMITTED_TAKERGETS_ISSUER');
    if(isset($data->EmittedTxn->TakerPays->issuer))
      $this->addAccount($data->EmittedTxn->TakerPays->issuer, 'EMITTED_TAKERPAYS_ISSUER');

    # Issuer of token from Amount - eg. https://xrpl.org/payment.html
    if(isset($data->EmittedTxn->Amount->issuer)) {
      $this->addAccount($data->EmittedTxn->Amount->issuer, 'EMITTED_AMOUNT_ISSUER');
    }
    
    //Add RegularKey - eg. https://xrpl.org/setregularkey.html
    if(isset($data->EmittedTxn->RegularKey))
      $this->addAccount($data->EmittedTxn->RegularKey, 'EMITTED_REGULARKEY');

    //Add LimitAmount issuer - eg. https://xrpl.org/trustset.html
    if(isset($data->EmittedTxn->LimitAmount->issuer))
      $this->addAccount($data->EmittedTxn->LimitAmount->issuer, 'EMITTED_LIMITAMOUNT_ISSUER');
  }

  /**
   * Introducted 25.09.2023 in Xahau
   */
  private function extract_UNLReport(\stdClass $data, ?string $context = null)
  {
    $role = 'UNLREPORT_ACTIVE_VALIDATOR';
    if($context == 'prev' || $context == 'deleted')
      $role = 'UNLREPORT_OLD_VALIDATOR';

      
    //if(!isset($data->ActiveValidators)) {
    //  throw new \Exception('extract_UNLReport ActiveValidators does not exist in medatadata');
    //}
    if(isset($data->ActiveValidators)) {
      foreach($data->ActiveValidators as $av) {
        if(isset($av->ActiveValidator->Account) && \trim((string)$av->ActiveValidator->Account) !== '') {
          $this->addAccount($av->ActiveValidator->Account, $role);
        }
      }
    }
  }

  /**
   * XLS-35
   * https://github.com/XRPLF/XRPL-Standards/discussions/89
   */
  private function extract_URIToken(\stdClass $data, ?string $context = null)
  {
    if(isset($data->Issuer)) {
      $this->addAccount($data->Issuer, 'ACCOUNTROOT_NFTOKENMINTER'); //backwards compatibility with XLS-20
      $this->addAccount($data->Issuer, 'ACCOUNTROOT_URITOKENMINTER');
    }
    if(isset($data->Owner)) {
      $this->addAccount($data->Owner, 'ACCOUNTROOT_NFTOKENOWNER'); //backwards compatibility with XLS-20
      $this->addAccount($data->Owner, 'ACCOUNTROOT_URITOKENOWNER');
    }
  }

  # HOOKS END

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

  private function normalizeAccounts()
  {
    if($this->tx->TransactionType == 'UNLReport') {

      /*
      "rGsa7f4arJ8JE9ok9LCht6jCu5xBKUKVMq" => array:2 [
        0 => "UNLREPORT_OLD_VALIDATOR"
        1 => "UNLREPORT_ACTIVE_VALIDATOR"
      ]
      to 
      "rGsa7f4arJ8JE9ok9LCht6jCu5xBKUKVMq" => array:2 [
        1 => "UNLREPORT_ACTIVE_VALIDATOR"
      ]
      */
      foreach($this->accounts as $acc => $roles) {
        if(count($roles) == 2) {
          $this->accounts[$acc] = ['UNLREPORT_ACTIVE_VALIDATOR'];
        }
      }
    }
    
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
   * Returns final result but skips EMITTED_ accounts where they are only emitted.
   * @return array
   */
  public function resultWithoutEmitted(): array
  {
    $r = [];
    foreach($this->accounts as $account => $roles) {
      $include = false;
      foreach($roles as $role) {
        if(!\str_starts_with($role,'EMITTED_'))
          $include = true;
      }
      if($include)
        $r[] = $account;
    }
    return $r;
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