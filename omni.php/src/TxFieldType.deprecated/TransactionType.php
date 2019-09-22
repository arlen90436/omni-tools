<?php
namespace OmniTool\TxFieldType;

/*
Field: Transaction type
Description: the MSC Protocol function to be performed

Size: 16-bit unsigned integer, 2 bytes

Inter-dependencies: Transaction version

Current Valid values:

0: Simple Send
3: Send To Owners
20: Sell Coins for Bitcoins (currency trade offer)
21: Offer/Accept Omni Protocol Coins for Another Omni Protocol Currency (currency trade offer)
22: Purchase Coins with Bitcoins (accept currency trade offer)
50: Create a Property with fixed number of tokens
51: Create a Property via Crowdsale with Variable number of Tokens
52: Promote a Property
53: Close a Crowdsale Manually
54: Create a Managed Property with Grants and Revocations
55: Grant Property Tokens
56: Revoke Property Tokens
70: Change Property Issuer on Record
To be added in future releases:

2: Restricted Send
10: Mark an Address as Savings
11: Mark a Savings Address as Compromised
12: Mark an Address as Rate-Limited
14: Remove a Rate Limitation
30: Register a Data Stream
31: Publish Data
32: Create a List of Addresses
33: Removing Addresses from a List
40: Offer/Accept a Bet
60: List Something for Sale
61: Initiate a Purchase from a Listing
62: Respond to a Buyer Offer
63: Release Funds and Leave Feedback
100: Create a New Child Currency
*/

class TransactionType extends TypeBase{
  public function __construct($v){
    $this->pushUint16($v);
  }
}

