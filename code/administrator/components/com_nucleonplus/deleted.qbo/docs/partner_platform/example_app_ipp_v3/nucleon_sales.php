<?php

require_once dirname(__FILE__) . '/config.php';

//require_once dirname(__FILE__) . '/views/header.tpl.php';

?>

<pre>

<?php

// Item
$ItemService = new QuickBooks_IPP_Service_Term();

$items = $ItemService->query($Context, $realm, "SELECT * FROM Item WHERE Id = '21' ");
$qty   = 1;

if (count($items) == 0) {
    throw new Exception('Invalid item');
}
else $item = $items[0];

$oldQty = $item->getQtyOnHand();
$newQty = $item->getQtyOnHand() - $qty;

$expenseAccount                = QuickBooks_IPP_IDS::usableIDType($item->getExpenseAccountRef());
$assetAccount                  = QuickBooks_IPP_IDS::usableIDType($item->getAssetAccountRef());
$rebatesAccount                = 141;
$referralBonusAccount          = 142;
$systemFeeAccount              = 138;
$contingencyFundAccount        = 139;
$operatingExpenseBudgetAccount = 140;
$salesAccount                  = 96;
$undepositedFundsAccount       = 92;
$salesOfProductAccount         = 124;

$packagePrice     = 1500 * $qty;
$rebates          = 1000 * $qty;
$referralBonus    = 150 * $qty;
$systemFee        = 10 * $qty;
$contingencyFund  = 50 * $qty;
$operatingExpense = 60 * $qty;

$totalUnitPrice    = $item->getUnitPrice() * $qty;
$totalPurchaseCost = $item->getPurchaseCost() * $qty;

$allocations = ($systemFee + $contingencyFund + $operatingExpense + $rebates + $referralBonus);
$profit      = ($packagePrice - ($totalUnitPrice + $allocations));
$sales       = ($profit + $allocations);

/*
 * Journal entry
 */
$JournalEntryService = new QuickBooks_IPP_Service_JournalEntry();

// Main journal entry object
$JournalEntry = new QuickBooks_IPP_Object_JournalEntry();
$JournalEntry->setDocNumber('8910');
$JournalEntry->setTxnDate(date('Y-m-d'));

/*
 * 1st Set
 */
// Cost of Goods Sold
$Line = new QuickBooks_IPP_Object_Line();
$Line->setDescription('Cost of goods sold');
$Line->setAmount($totalPurchaseCost);
$Line->setDetailType('JournalEntryLineDetail');
$Details = new QuickBooks_IPP_Object_JournalEntryLineDetail();
$Details->setPostingType('Debit');
$Details->setAccountRef($expenseAccount); // Cost of goods sold
$Line->addJournalEntryLineDetail($Details);
$JournalEntry->addLine($Line);

// Inventory Asset
$Line = new QuickBooks_IPP_Object_Line();
$Line->setDescription('Inventory asset');
$Line->setAmount($totalPurchaseCost);
$Line->setDetailType('JournalEntryLineDetail');
$Detail = new QuickBooks_IPP_Object_JournalEntryLineDetail();
$Detail->setPostingType('Credit');
$Detail->setAccountRef($assetAccount); // Inventory asset
$Line->addJournalEntryLineDetail($Detail);
$JournalEntry->addLine($Line);

/*
 * 2nd Set
 */
// Undeposited Funds
$Line = new QuickBooks_IPP_Object_Line();
$Line->setDescription('Sale of Product Package');
$Line->setAmount($totalUnitPrice);
$Line->setDetailType('JournalEntryLineDetail');
$Detail = new QuickBooks_IPP_Object_JournalEntryLineDetail();
$Detail->setPostingType('Debit');
$Detail->setAccountRef($undepositedFundsAccount);
$Line->addJournalEntryLineDetail($Detail);
$JournalEntry->addLine($Line);

// Sales of Product Income
$Line = new QuickBooks_IPP_Object_Line();
$Line->setDescription('Sales of product income');
$Line->setAmount($totalUnitPrice);
$Line->setDetailType('JournalEntryLineDetail');
$Detail = new QuickBooks_IPP_Object_JournalEntryLineDetail();
$Detail->setPostingType('Credit');
$Detail->setAccountRef($salesOfProductAccount);
$Line->addJournalEntryLineDetail($Detail);
$JournalEntry->addLine($Line);

/*
 * 3rd Set
 */
// System Fee
$Line = new QuickBooks_IPP_Object_Line();
$Line->setDescription('System Fee');
$Line->setAmount($systemFee);
$Line->setDetailType('JournalEntryLineDetail');
$Details = new QuickBooks_IPP_Object_JournalEntryLineDetail();
$Details->setPostingType('Debit');
$Details->setAccountRef($systemFeeAccount);
$Line->addJournalEntryLineDetail($Details);
$JournalEntry->addLine($Line);

// Contingency Fund
$Line = new QuickBooks_IPP_Object_Line();
$Line->setDescription('Contingency Fund');
$Line->setAmount($contingencyFund);
$Line->setDetailType('JournalEntryLineDetail');
$Details = new QuickBooks_IPP_Object_JournalEntryLineDetail();
$Details->setPostingType('Debit');
$Details->setAccountRef($contingencyFundAccount);
$Line->addJournalEntryLineDetail($Details);
$JournalEntry->addLine($Line);

// Operating Expense
$Line = new QuickBooks_IPP_Object_Line();
$Line->setDescription('Operating Expense');
$Line->setAmount($operatingExpense);
$Line->setDetailType('JournalEntryLineDetail');
$Details = new QuickBooks_IPP_Object_JournalEntryLineDetail();
$Details->setPostingType('Debit');
$Details->setAccountRef($operatingExpenseBudgetAccount);
$Line->addJournalEntryLineDetail($Details);
$JournalEntry->addLine($Line);

// Rebates
$Line = new QuickBooks_IPP_Object_Line();
$Line->setDescription('Rebates for Members');
$Line->setAmount($rebates);
$Line->setDetailType('JournalEntryLineDetail');
$Detail = new QuickBooks_IPP_Object_JournalEntryLineDetail();
$Detail->setPostingType('Debit');
$Detail->setAccountRef($rebatesAccount);
$Line->addJournalEntryLineDetail($Detail);
$JournalEntry->addLine($Line);

// Referral Bonus
$Line = new QuickBooks_IPP_Object_Line();
$Line->setDescription('Referral Bonus for Members');
$Line->setAmount($referralBonus);
$Line->setDetailType('JournalEntryLineDetail');
$Detail = new QuickBooks_IPP_Object_JournalEntryLineDetail();
$Detail->setPostingType('Debit');
$Detail->setAccountRef($referralBonusAccount);
$Line->addJournalEntryLineDetail($Detail);
$JournalEntry->addLine($Line);

// Profit
$Line = new QuickBooks_IPP_Object_Line();
$Line->setDescription('Revenue from Sale of Product Package');
$Line->setAmount($profit);
$Line->setDetailType('JournalEntryLineDetail');
$Detail = new QuickBooks_IPP_Object_JournalEntryLineDetail();
$Detail->setPostingType('Debit');
$Detail->setAccountRef($undepositedFundsAccount);
$Line->addJournalEntryLineDetail($Detail);
$JournalEntry->addLine($Line);

// Sales
$Line = new QuickBooks_IPP_Object_Line();
$Line->setDescription('Sales');
$Line->setAmount($sales);
$Line->setDetailType('JournalEntryLineDetail');
$Detail = new QuickBooks_IPP_Object_JournalEntryLineDetail();
$Detail->setPostingType('Credit');
$Detail->setAccountRef($salesAccount);
$Line->addJournalEntryLineDetail($Detail);
$JournalEntry->addLine($Line);

if ($resp = $JournalEntryService->add($Context, $realm, $JournalEntry))
{
    print('Our new journal entry ID is: [' . $resp . ']');

    // Update the item's quantity
    $item->setQtyOnHand($newQty);

    $ItemService = new QuickBooks_IPP_Service_Item();

    if ($resp = $ItemService->update($Context, $realm, $item->getId(), $item))
    {
        echo '<br />';
        print("Updated the item '{$item->getName()}' quantity from {$oldQty} to {$item->getQtyOnHand()}");
    }
    else
    {
        echo '<br />';
        print('ERROR!');
        print($ItemService->lastError($Context));
    }
}
else print($JournalEntryService->lastError($Context));

/*
print('<br><br><br><br>');
print("\n\n\n\n\n\n\n\n");
print('Request [' . $IPP->lastRequest() . ']');
print("\n\n\n\n");
print('Response [' . $IPP->lastResponse() . ']');
print("\n\n\n\n\n\n\n\n\n");
*/

?>

</pre>

<?php

//require_once dirname(__FILE__) . '/views/footer.tpl.php';
