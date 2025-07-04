<?php declare(strict_types=1);

namespace XRPLWin\XRPLTxParticipantExtractor;

use Hardcastle\XRPL_PHP\Core\RippleBinaryCodec\Types\AccountId;
use Hardcastle\XRPL_PHP\Core\CoreUtilities as XRPLPHPUtilities;

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

    # Issuer of token from Amount2 - AMM
    if(isset($this->tx->Amount2->issuer)) {
      $this->addAccount($this->tx->Amount2->issuer, 'AMOUNT2_ISSUER');
    }

    # AMM Bid (BidMax)
    if(isset($this->tx->BidMax) && !\is_string($this->tx->BidMax) && isset($this->tx->BidMax->issuer)) {
      $this->addAccount($this->tx->BidMax->issuer, 'AMM_BIDMAX_ISSUER');
    }
    # AMM Bid (BidMin)
    if(isset($this->tx->BidMin) && !\is_string($this->tx->BidMin) && isset($this->tx->BidMin->issuer)) {
      $this->addAccount($this->tx->BidMin->issuer, 'AMM_BIDMIN_ISSUER');
    }

    # AMM (AuthAccounts) - eg. In Bid
    if(isset($this->tx->AuthAccounts) && \is_array($this->tx->AuthAccounts)) {
      foreach($this->tx->AuthAccounts as $_aa) {
        $this->addAccount($_aa->AuthAccount->Account, 'AUTH_ACCOUNT');
      }
    }
    
    //Add RegularKey - eg. https://xrpl.org/setregularkey.html
    if(isset($this->tx->RegularKey))
      $this->addAccount($this->tx->RegularKey, 'REGULARKEY');

    //Add transaction signer (derived from SigningPubKey field)
    if(isset($this->tx->SigningPubKey) && \is_string($this->tx->SigningPubKey) && $this->tx->SigningPubKey !== '') {
      $signer = $this->pubkeyToAccount($this->tx->SigningPubKey);
      $this->addAccount($signer, 'TXSIGNER');
    }

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

    //Batch
    if($this->tx->TransactionType == 'Batch') {
      //Add BatchSigners
      if(isset($this->tx->BatchSigners) && \is_array($this->tx->BatchSigners)) {
        foreach($this->tx->BatchSigners as $bs) {
          if(isset($bs->BatchSigner->Account))
            $this->addAccount($bs->BatchSigner->Account, 'BATCHSIGNERS_BATCHSIGNER_ACCOUNT');
        }
      }
    }

    //TODO AuthAccounts,BidMax,Amount,Amount2,Asset,Asset2 https://xrpl.org/ammbid.html
  
    //Extract all other participants from meta
    $this->extractAccountsFromMeta();
    $this->normalizeAccounts();
    //Logic handlers
    $this->logic_detectAMMWithdraw();

    if(isset($options['allowSpecialAccounts']) && $options['allowSpecialAccounts']) {
      //do not remove special accounts
    } else {
      $this->removeSpecialAccounts();
    }
      
    $this->result = \array_keys($this->accounts);
    //foreach($this->result as $r) {echo "'".$r."',".PHP_EOL;}exit;
  }

  /**
   * Logic handler for AMMWithdraw
   * This method will detect AMM_ACCOUNT by comparing existing detected accounts.
   * @return void
   */
  private function logic_detectAMMWithdraw()
  {
    if($this->tx->TransactionType != 'AMMWithdraw')
      return;
    $accounts = $this->accounts;
    //Check if AMM_ACCOUNT role does not exist
    foreach($this->accounts as $acc => $roles) {
      if(\in_array('AMM_ACCOUNT',$roles))
        return;
    }
    unset($acc);
    unset($roles);

    //This is AMMWithdraw but AMM_ACCOUNT was not detected
    
    unset($accounts[self::ACCOUNT_ZERO]);
    unset($accounts[self::ACCOUNT_ONE]);
    unset($accounts[self::ACCOUNT_GENESIS]);
    unset($accounts[self::ACCOUNT_BLACKHOLE]);
    unset($accounts[self::ACCOUNT_NAN]);

    if(count($accounts) == 1) {
      throw new \Exception('Unhandled: unable to detect AMM_ACCOUNT in logic_detectAMMWithdraw - single account detected without obvious AMM account (txn: '.$this->tx->hash.')');
      //return;
    }

    //If there is more than two non special accounts present, one is transaction initiator other is AMM Account - see test 45
    if(count($accounts) > 2) {

      //Deep search for AMM Account in withdrawed:
      //Eliminate all the things AMM account can ever not be:
      //1. Eliminate INITIATOR
      //2. Eliminate ACCOUNTROOT_REGULARKEY
      //3. Eliminate ACCOUNTROOT_NFTOKENMINTER
      //4. Eliminate AMOUNT_ISSUER
      foreach($accounts as $_a => $roles) {
        if(\in_array('INITIATOR',$roles)) {
          unset($accounts[$_a]);
          continue;
        }
        if(\in_array('ACCOUNTROOT_REGULARKEY',$roles)) {
          unset($accounts[$_a]);
          continue;
        }
        if(\in_array('ACCOUNTROOT_NFTOKENMINTER',$roles)) {
          unset($accounts[$_a]);
          continue;
        }
        if(\in_array('AMOUNT_ISSUER',$roles)) {
          unset($accounts[$_a]);
          continue;
        }
      }
      //Case when there is only token issuer and amm account left, in that case RIPPLESTATE_HIGHLIMIT_ISSUER is AMM account
      //@see EAE26C7364232AC1D7C3B03EC4ABD0258B0C392A3F2FF5B7394F3826DA1D5A1A
      
      if(count($accounts) == 2 || count($accounts) == 3) {
       
        if(count($accounts) == 2) {
          
          $isValidSpecificCase = true;
          foreach($accounts as $_a => $roles) {
            if(count($roles) != 2) {
              $isValidSpecificCase = false;
              break;
            }
            
            //Roles has to contain (DIRECTORYNODE_OWNER and RIPPLESTATE_HIGHLIMIT_ISSUER) OR (DIRECTORYNODE_OWNER and RIPPLESTATE_LOWLIMIT_ISSUER)
            if(\in_array('DIRECTORYNODE_OWNER',$roles) && \in_array('RIPPLESTATE_HIGHLIMIT_ISSUER',$roles)) {
              //OK
            } elseif(\in_array('DIRECTORYNODE_OWNER',$roles) && \in_array('RIPPLESTATE_LOWLIMIT_ISSUER',$roles)) {
              //OK
            } else {
              $isValidSpecificCase = false;
            }
          }

          if($isValidSpecificCase) {
          //Remove account with RIPPLESTATE_LOWLIMIT_ISSUER
          foreach($accounts as $_a => $roles) {
            if(\in_array('RIPPLESTATE_LOWLIMIT_ISSUER',$roles)) {
              unset($accounts[$_a]);
              break;
            }
          }
          
        }


        } else { //3
          $isValidSpecificCase = false;
          foreach($accounts as $_a => $roles) {
            
            //Roles has to contain (DIRECTORYNODE_OWNER and RIPPLESTATE_LOWLIMIT_ISSUER)
            if(\in_array('DIRECTORYNODE_OWNER',$roles) && \in_array('RIPPLESTATE_LOWLIMIT_ISSUER',$roles)) {
              $isValidSpecificCase = true;
            } else {
              //$isValidSpecificCase = false;
            }
          }

          if($isValidSpecificCase) {
            //Remove account without DIRECTORYNODE_OWNER
            
            foreach($accounts as $_a => $roles) {
              if(!\in_array('DIRECTORYNODE_OWNER',$roles)) {
                unset($accounts[$_a]);
              }
            }
          }
        }
       
      }

      if(count($accounts) == 2) {
        //Handle case when LPToken issuer and non LPToken issuer are left (see AA32D2BFA786383BE8AA96CB9ACA59C4E3DEE2410879E6B52B8928DF1CE544B9)
        
        //Remove account with RIPPLESTATE_HIGHLIMIT_ISSUER
          foreach($accounts as $_a => $roles) {
            if(\in_array('RIPPLESTATE_HIGHLIMIT_ISSUER',$roles)) {
              unset($accounts[$_a]);
              break;
            }
          }
      }
      if(count($accounts) > 1) {
        throw new \Exception('Unhandled: unable to detect AMM_ACCOUNT in logic_detectAMMWithdraw - more than one account detected without obvious AMM account (txn: '.$this->tx->hash.')');
        //return;
      }

      if(count($accounts) == 0) {
        throw new \Exception('Unhandled: unable to detect AMM_ACCOUNT in logic_detectAMMWithdraw - no account detected (txn: '.$this->tx->hash.')');
      }

      //Only one remains
      $this->addAccount(\array_keys($accounts)[0], 'AMM_ACCOUNT');
      return;
    }
    foreach($this->accounts as $acc => $roles) {
      if(!\in_array('INITIATOR',$roles)) {
        $this->addAccount($acc, 'AMM_ACCOUNT');
      }
    }
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
   * DID
   * @see https://xrpl.org/docs/references/protocol/ledger-data/ledger-entry-types/did/
   * @return void
   */
  private function extract_DID(\stdClass $data, ?string $context = null)
  {
    if(isset($data->Account)) {
      $this->addAccount($data->Account, 'DID_ACCOUNT'); //Account that controls DID
    }
  }

  /**
   * Oracle (OracleSet, ...)
   * @return void
   */
  private function extract_Oracle(\stdClass $data, ?string $context = null)
  {
    if(isset($data->Owner)) {
      $this->addAccount($data->Owner, 'ORACLE_OWNER');
    }
  }

  /**
   * @see https://xrpl.org/ledger-object-types.html
   * @return void
   */
  private function extract_AMM(\stdClass $data, ?string $context = null)
  {
    //AMM Account ID
    if(isset($data->Account)) {
      $this->addAccount($data->Account, 'AMM_ACCOUNT');
    }

    //Asset 1
    if(isset($data->Asset) && !\is_string($data->Asset) && isset($data->Asset->issuer)) {
      $this->addAccount($data->Asset->issuer, 'AMM_ASSET1_ISSUER');
    }

    //Asset 2
    if(isset($data->Asset2) && !\is_string($data->Asset2) && isset($data->Asset2->issuer)) {
      $this->addAccount($data->Asset2->issuer, 'AMM_ASSET2_ISSUER');
    }

    //AuctionSlot
    if(isset($data->AuctionSlot)) {
      if(isset($data->AuctionSlot->Account))
        $this->addAccount($data->AuctionSlot->Account, 'AMM_AUCTIONSLOT_ACCOUNT');

      if(isset($data->AuctionSlot->Price) && !\is_string($data->AuctionSlot->Price) && isset($data->AuctionSlot->Price->issuer))
        $this->addAccount($data->AuctionSlot->Price->issuer, 'AMM_AUCTIONSLOT_PRICE_ISSUER');
    }

    //VoteSlots
    if(isset($data->VoteSlots) && \is_array($data->VoteSlots)) {
      foreach($data->VoteSlots as $vs) {
        
        if(isset($vs->VoteEntry) && isset($vs->VoteEntry->Account)) {
          $this->addAccount($vs->VoteEntry->Account, 'AMM_VOTEENTRY_ACCOUNT');
        }
      }
    }

    //LPTokenBalance
    if(isset($data->LPTokenBalance) && isset($data->LPTokenBalance->issuer)) {
      $this->addAccount($data->LPTokenBalance->issuer, 'AMM_LPTOKENBALANCE_ISSUER');
    }

  }

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
      $this->addAccount($data->EmittedTxn->Issuer, 'EMITTED_ISSUER');

    //Add Destination (if exists)
    if(isset($data->EmittedTxn->Destination))
      $this->addAccount($data->EmittedTxn->Destination, 'EMITTED_DESTINATION');

    //Add Authorize (if exists) - eg. https://xrpl.org/depositpreauth.html
    if(isset($data->EmittedTxn->tx->Authorize))
      $this->addAccount($data->EmittedTxn->tx->Authorize, 'EMITTED_AUTHORIZE');
    
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

  private function extract_ImportVLSequence(\stdClass $data, ?string $context = null)
  {
    //no affected accounts
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
   * XLS-33 Multi Purpose Tokens
   */
  private function extract_MPTokenIssuance(\stdClass $data, ?string $context = null)
  {
    if(isset($data->Issuer)) {
      $this->addAccount($data->Issuer, 'MPTOKEN_ISSUER');
    }
  }

  private function extract_MPToken(\stdClass $data, ?string $context = null)
  {
    if(isset($data->Account)) {
      $this->addAccount($data->Account, 'MPTOKEN_ACCOUNT');
    }
    //no affected accounts (?)
  }

  private function pubkeyToAccount(string $SigningPubKey): string
  {
    return XRPLPHPUtilities::deriveAddress($SigningPubKey);
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