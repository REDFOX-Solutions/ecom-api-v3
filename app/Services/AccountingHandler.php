<?php

namespace App\Services;
// namespace App\Services\MailHandler;
use App\Http\Controllers\API\AccountingBookController;
use App\Http\Controllers\API\AccountingClassController;
use App\Http\Controllers\API\ChartOfAccountController;
use App\Http\Controllers\API\GeneralLedgerController;
use App\Http\Controllers\API\GeneralLedgerDetailsController;
use App\Model\AccountingBook;
use App\Model\AccountingClass;
use App\Model\ChartOfAccount;
use App\Model\GLAccMapping;
use App\Model\MetaDataConfig;
use App\Model\Receipts;
use App\Model\SaleOrder; 
use App\Model\Shipment;
use App\Model\StaffShifts;
use Carbon\Carbon;

class AccountingHandler
{

    /**
     * Method to create default Accounting and map product with chart of account
     */
    public static function createDefaultAccounting(){
        AccountingBook::where("id", "<>", null)->delete();
        AccountingClass::where("id", "<>", null)->delete();
        ChartOfAccount::where("id", "<>", null)->delete();
        GLAccMapping::where("id", "<>", null)->delete();

        // Accounting Book (Ledger) will also create auto in code
        $lstAccBooks = [];
        $lstAccBooks[] = ['name' => 'budget', 'code' => '001', 'ledger_type' => 'budget'];
        $lstAccBooks[] = ['name' => 'actual', 'code' => '002', 'ledger_type' => 'actual'];

        $accBookCtrl = new AccountingBookController();
        $lstAccBooksCreated = $accBookCtrl->createLocal($lstAccBooks);

        // Create Accounting Class
        $lstAccClasses = [];
        $lstAccClasses[] = ['name' => 'Cash and Cash equivalents', 'class_type' => 'asset', 'is_active' => 1, 'code' => "cashasset"];
        $lstAccClasses[] = ['name' => 'Inventories', 'class_type' => 'asset', 'is_active' => 1, 'code' => "inventory"];
        $lstAccClasses[] = ['name' => 'Account Receivables', 'class_type' => 'asset', 'is_active' => 1, 'code' => "ar"];

        $lstAccClasses[] = ['name' => 'Account Payable', 'class_type' => 'liability', 'is_active' => 1, 'code' => "ap"];
        
        $lstAccClasses[] = ['name' => 'Income - Sales', 'class_type' => 'income', 'is_active' => 1, 'code' => 'sale'];
        $lstAccClasses[] = ['name' => 'Other Income', 'class_type' => 'income', 'is_active' => 1, 'code' => 'otherincome'];

        $lstAccClasses[] = ['name' => 'Cost of goods sold', 'class_type' => 'expense', 'is_active' => 1, 'code' => 'cogs'];
        $lstAccClasses[] = ['name' => 'Operating Expense', 'class_type' => 'expense', 'is_active' => 1, 'code' => 'opex'];

        $lstAccClasses[] = ['name' => 'Tax Payable', 'class_type' => 'liability', 'is_active' => 1, 'code' => 'taxpays'];

        $accClassCtrl = new AccountingClassController();
        $lstCreatedAccClasses = $accClassCtrl->createLocal($lstAccClasses);
 
        $mapAccClassByCode = [];
        
        foreach ($lstCreatedAccClasses as $index => $accClass) {
            $mapAccClassByCode[$accClass["code"]] = $accClass;
        }
        // create chart of account 
        $lstCOAs = [];
        $lstCOAs[] = ["code" => "10000000", "accounting_name" => "cash", "accounting_class_id" => $mapAccClassByCode["cashasset"]["id"], "is_active"=>1];
        $lstCOAs[] = ["code" => "10001000", "accounting_name" => "bank account", "accounting_class_id" => $mapAccClassByCode["cashasset"]["id"], "is_active"=>1];
        $lstCOAs[] = ["code" => "12000000", "accounting_name" => "Account Receivable", "accounting_class_id" => $mapAccClassByCode["ar"]["id"], "is_active"=>1];
        $lstCOAs[] = ["code" => "13001000", "accounting_name" => "Inventory Beverage", "accounting_class_id" => $mapAccClassByCode["inventory"]["id"], "is_active"=>1];
        $lstCOAs[] = ["code" => "13002000", "accounting_name" => "Inventory Food", "accounting_class_id" => $mapAccClassByCode["inventory"]["id"], "is_active"=>1];
        $lstCOAs[] = ["code" => "13003000", "accounting_name" => "Inventory Bakery", "accounting_class_id" => $mapAccClassByCode["inventory"]["id"], "is_active"=>1];
        $lstCOAs[] = ["code" => "13004000", "accounting_name" => "Inventory Wine", "accounting_class_id" => $mapAccClassByCode["inventory"]["id"], "is_active"=>1];
        $lstCOAs[] = ["code" => "21000000", "accounting_name" => "Account Payable", "accounting_class_id" => $mapAccClassByCode["ap"]["id"], "is_active"=>1];
        $lstCOAs[] = ["code" => "20001000", "accounting_name" => "Vat Output", "accounting_class_id" => $mapAccClassByCode["taxpays"]["id"], "is_active"=>1];
        $lstCOAs[] = ["code" => "40001000", "accounting_name" => "Sales Beverage", "accounting_class_id" => $mapAccClassByCode["sale"]["id"], "is_active"=>1];
        $lstCOAs[] = ["code" => "40002000", "accounting_name" => "Sales Food", "accounting_class_id" => $mapAccClassByCode["sale"]["id"], "is_active"=>1];
        $lstCOAs[] = ["code" => "40003000", "accounting_name" => "Sales Bakery", "accounting_class_id" => $mapAccClassByCode["sale"]["id"], "is_active"=>1];
        $lstCOAs[] = ["code" => "40004000", "accounting_name" => "Sales Wine", "accounting_class_id" => $mapAccClassByCode["sale"]["id"], "is_active"=>1];
        $lstCOAs[] = ["code" => "40005000", "accounting_name" => "Service Charge", "accounting_class_id" => $mapAccClassByCode["otherincome"]["id"], "is_active"=>1];
        $lstCOAs[] = ["code" => "50001000", "accounting_name" => "COGS Beverage", "accounting_class_id" => $mapAccClassByCode["cogs"]["id"], "is_active"=>1];
        $lstCOAs[] = ["code" => "50002000", "accounting_name" => "COGS Food", "accounting_class_id" => $mapAccClassByCode["cogs"]["id"], "is_active"=>1];
        $lstCOAs[] = ["code" => "50003000", "accounting_name" => "COGS Bakery", "accounting_class_id" => $mapAccClassByCode["cogs"]["id"], "is_active"=>1];
        $lstCOAs[] = ["code" => "50004000", "accounting_name" => "COGS Wine", "accounting_class_id" => $mapAccClassByCode["cogs"]["id"], "is_active"=>1];
        $lstCOAs[] = ["code" => "50005000", "accounting_name" => "Discount Given", "accounting_class_id" => $mapAccClassByCode["cogs"]["id"], "is_active"=>1];
        $lstCOAs[] = ["code" => "51000000", "accounting_name" => "Salary Expense", "accounting_class_id" => $mapAccClassByCode["opex"]["id"], "is_active"=>1];
        $lstCOAs[] = ["code" => "51001000", "accounting_name" => "Rental Expense", "accounting_class_id" => $mapAccClassByCode["opex"]["id"], "is_active"=>1];
        $lstCOAs[] = ["code" => "51002000", "accounting_name" => "Utilities Expense", "accounting_class_id" => $mapAccClassByCode["opex"]["id"], "is_active"=>1];
        $lstCOAs[] = ["code" => "51003000", "accounting_name" => "Delivery Expense", "accounting_class_id" => $mapAccClassByCode["opex"]["id"], "is_active"=>1];
        $coaCtrol = new ChartOfAccountController();
        $lstCreatedCOAs = $coaCtrol->createLocal($lstCOAs);

        $mapCOAs = [];
        foreach ($lstCreatedCOAs as $index => $coa) {
            $mapCOAs[$coa["code"]] = $coa;
        }


        //create mapping chart of account with product
        // acc_type: inventory, inventory_sub, sale, sale_sub, cogs, cogs_sub, std_cost_var, std_cost_var_sub, std_cost_rev, std_cost_rev_sub, po_accr, po_accr_sub, deferral, deferral_sub

        // $lstProducts = Products::get()->toArray();
        // $lstGLMapProd = [];
        // foreach ($lstProducts as $index => $product) {
        //     $lstGLMapProd[] = ["object_name" => "products", "obj_record_id" => $product["id"], "chart_of_acc_id" => $mapCOAs["001"]["id"], "acc_type" => "inventory"];
        //     $lstGLMapProd[] = ["object_name" => "products", "obj_record_id" => $product["id"], "chart_of_acc_id" => $mapCOAs["001"]["id"], "acc_type" => "sale"];
        //     $lstGLMapProd[] = ["object_name" => "products", "obj_record_id" => $product["id"], "chart_of_acc_id" => $mapCOAs["001"]["id"], "acc_type" => "cogs"];
        //     // $lstGLMapProd[] = ["object_name" => "products", "obj_record_id" => $product["id"], "chart_of_acc_id" => $mapCOAs["001"]["id"], "acc_type" => ""];
        // }

        // if(count($lstGLMapProd) > 0){
        //     $glAccMappingCtrl = new GLAccMappingController();
        //     $glAccMappingCtrl->createLocal($lstGLMapProd);
        // }
    }

    /**
     * Method to create GL and GL Detail after successfull POS end day
     * this method will invoke after created staff_end_day record
     * @param $staffEndDay      staff_end_day record
     */
    public static function createGLDetailsPOSEndDay($staffEndDay)
    {

        $endDate = new Carbon($staffEndDay["end_date"]); 

        //create GL header
        //get Accounting Book to create GL
        $lstAccBook = AccountingBook::where('ledger_type', "actual")
            ->get()
            ->toArray();

        if (empty($lstAccBook)) {
            $newAccBook = [
                "ledger_type" => "actual",
                "name" => "actual"
            ];

            $accBookCtrl = new AccountingBookController();
            $lstAccBook = $accBookCtrl->createLocal([$newAccBook]);
        }

        $now = $endDate->format(GlobalStaticValue::$FORMAT_DATETIME);
        $finPeriod = $endDate->format('Y-m');

        //create GL
        $newGL = [
            "module" => "pos",
            "batch_number" => "",
            "fin_period" => $finPeriod,
            "transaction_date" => $now,
            "status" => "hold",
            "ledger_id" => $lstAccBook[0]["id"]
        ];

        $glController = new GeneralLedgerController();
        $lstNewGLs = $glController->createLocal([$newGL]);

        $newGLId = $lstNewGLs[0]["id"];

        
        $lstTaxPercentages = MetaDataConfig::where("name", "tax_percentage")->get()->toArray();//get Tax percentage to calc tax
        
        $taxRate = !empty($lstTaxPercentages) && isset($lstTaxPercentages[0]["value"]) ? ((int) $lstTaxPercentages[0]["value"])/100 : 0;
        $lstTaxAccounts = MetaDataConfig::where("name", "tax_chart_of_account")->get()->toArray();//get Tax chart of acc to create GL Details

        //get all staff end shift for TODAY and assign staff end day to it
        $lstStaffShift = StaffShifts::where("staff_end_day_id", $staffEndDay["id"])
            ->get()
            ->toArray();

        $lstStaffShiftIds = [];
        foreach ($lstStaffShift as $index => &$staffShift) {
            $lstStaffShiftIds[] = $staffShift["id"];
        }

        //get completed order and order detail to create GL account detail
        $lstOrders = SaleOrder::where("status", "completed")
            ->whereIn("staff_shifts_id", $lstStaffShiftIds)
            ->with("orderDetails")
            ->get()
            ->toArray();

        $mapProdTotalPriceCost = [];
        $totalTax = 0;
        $lstProdIds = [];
        $orderIds = []; //to get shipment for Accounting

        //To sum all product price and cost in current order
        foreach ($lstOrders as $index => $order) {
            $lstOrderDetails = isset($order["order_details"]) ? $order["order_details"] : [];
            $orderIds[] = $order["id"];
            $totalTax += (isset($order["order_total"]) ? $order["order_total"] * $taxRate : 0);

            if (count($lstOrderDetails) > 1) {

                foreach ($lstOrderDetails as $index => $orderDetail) {

                    $prodId = $orderDetail["products_id"];

                    //sum product price and cost
                    if (!isset($mapProdTotalPriceCost[$prodId])) {
                        $mapProdTotalPriceCost[$prodId] = [
                            "cost" => 0,
                            "price" => 0
                        ];
                    }

                    $qty = isset($orderDetail["quantity"]) ? $orderDetail["quantity"] : 0;
                    // $unitPrice = isset($orderDetail["unit_price"]) ? $orderDetail["unit_price"] : 0;
                    $amount = isset($orderDetail["amount"]) ? $orderDetail["amount"] : 0;
                    $cost = isset($orderDetail["product"]) && isset($orderDetail["product"]["cost"]) ? $orderDetail["product"]["cost"] : 0;

                    $lstProdIds[] = $prodId;
                    $mapProdTotalPriceCost[$prodId]["cost"] += $qty * $cost;
                    $mapProdTotalPriceCost[$prodId]["price"] += $amount;
                }
            }
        }

        //get gl acc mapping for each item to get chart of acc
        $mapProdGlAcc = GLAccMapping::whereIn("obj_record_id", $lstProdIds)
            ->where("object_name", "products")
            ->get()
            ->groupBy("obj_record_id")
            ->toArray();

        $mapGlAccDetails = [];

        //TO sum chart of account for inventory, sale, cogs
        foreach ($lstProdIds as $index => $prodId) {

            //if the item doesnt have GL Account, skip it
            if (!isset($mapProdGlAcc[$prodId]) || empty($mapProdGlAcc[$prodId])) continue;

            $lstProdGLAccs = $mapProdGlAcc[$prodId];

            //we will get 3 chart of account (Inventory, Sale, COGS) to create GL Detail
            $invAccId  = "";
            $cogsAccId = "";
            $saleAccId = "";
            foreach ($lstProdGLAccs as $key => $glAcc) {
                $invAccId = $glAcc["type"] == 'inventory' ? $glAcc['chart_of_acc_id'] : $invAccId;
                $cogsAccId = $glAcc["type"] == 'cogs' ? $glAcc['chart_of_acc_id'] : $cogsAccId;
                $saleAccId = $glAcc["type"] == 'sale' ? $glAcc['chart_of_acc_id'] : $saleAccId;
            }

            $prodSumAmt = isset($mapProdTotalPriceCost[$prodId]) ? $mapProdTotalPriceCost[$prodId] : [];

            //to sum inventory account
            if ($invAccId != "") {
                if (!isset($mapGlAccDetails[$invAccId])) {
                    $newGlDetail = [
                        "coa_id" => $invAccId,
                        "credit_amount" => 0,
                        "debit_amount" => 0,
                        "transaction_type" => "pos",
                        "general_ledger_id" => $newGLId
                    ];
                    $mapGlAccDetails[$invAccId] = $newGlDetail;
                }
                $mapGlAccDetails[$invAccId]["credit_amount"] += $prodSumAmt["cost"];
            }

            //to sum COGS account
            if ($cogsAccId != "") {
                if (!isset($mapGlAccDetails[$cogsAccId])) {
                    $newGlDetail = [
                        "coa_id" => $cogsAccId,
                        "credit_amount" => 0,
                        "debit_amount" => 0,
                        "transaction_type" => "pos",
                        "general_ledger_id" => $newGLId
                    ];
                    $mapGlAccDetails[$cogsAccId] = $newGlDetail;
                }
                $mapGlAccDetails[$cogsAccId]["debit_amount"] += $prodSumAmt["cost"];
            }

            //to sum sale account
            if ($saleAccId != "") {
                if (!isset($mapGlAccDetails[$saleAccId])) {
                    $newGlDetail = [
                        "coa_id" => $saleAccId,
                        "credit_amount" => 0,
                        "debit_amount" => 0,
                        "transaction_type" => "pos",
                        "general_ledger_id" => $newGLId
                    ];
                    $mapGlAccDetails[$saleAccId] = $newGlDetail;
                }
                $mapGlAccDetails[$saleAccId]["credit_amount"] += $prodSumAmt["price"];
            }
        }

        //We need to create more GL Detail from (Payment Method, tax)
        //Create GL Detail from payment Method
        $lstGLDetailPaymentAccs = self::generateGLDetailFromPaymentMethodBySO($orderIds, $lstNewGLs[0], 'pos');

        
        //create general ledger detail
        $lst2CreateGLDetails = array_merge(array_values($mapGlAccDetails),  (array)$lstGLDetailPaymentAccs);
        
        //create GL Detail for TAX
        if(!empty($lstTaxAccounts) && isset($lstTaxAccounts[0]["value"])){
            $taxGLDetail = [
                "coa_id" => $lstTaxAccounts[0]["value"],
                "credit_amount" => $totalTax,
                "debit_amount" => 0,
                "transaction_type" => "pos",
                "general_ledger_id" => $newGLId
            ];
            $lst2CreateGLDetails[] = $taxGLDetail; 
        }

        if (count($lst2CreateGLDetails) > 0) {
            $glDetailCtrl = new GeneralLedgerDetailsController();
            $glDetailCtrl->createLocal($lst2CreateGLDetails);
        }

        //TODO: Send email to customer
        //get all records from notification where match current company 
        //send email to that notification
        // $mailConfig = new stdClass();
        // $mailConfig->to_email ="from_email@gmail.com";
        // $mailConfig->subject ="subject";
        // $mailConfig->body ="say something..";
        // $mailConfig->to_email ="to_email@gmail.com";
        // $MailHandler::sendEmail($mailConfig);

    }

    /**
     * Method to generate GL Detail for Payment Method Accounting from Sale Order
     * @param $orderIds     list Sales order ids
     * @param $gl           array object general Ledger
     * @param $transTyp     String transaction type
     * @return list GL Detail records
     */
    public static function generateGLDetailFromPaymentMethodBySO($orderIds, $gl, $transTyp)
    {
        //TO get Payment method -> Sale Order -> Shipment -> Invoice -> Invoice Receipt -> receipt -> payment method
        $lstShipments = Shipment::whereIn("sales_order_id", $orderIds)
                                ->where("status", "completed")
                                ->with("invoice")->get()->toArray();

        $mapReceiptAmount = [];

        foreach ($lstShipments as $key => $shipment) {
            $invoice = isset($shipment["invoice"]) ? $shipment["invoice"] : null;

            if (!is_null($invoice)) {
                $lstInvReceipts = isset($invoice["invoice_receipts"]) ? $invoice["invoice_receipts"] : [];

                foreach ($lstInvReceipts as $key => $invRecp) {

                    if (!isset($mapReceiptAmount[$invRecp["receipts_id"]])) {
                        $mapReceiptAmount[$invRecp["receipts_id"]] = 0;
                    }
                    $mapReceiptAmount[$invRecp["receipts_id"]] += $invRecp["amount"];
                }
            }
        }

        $receiptIds = array_keys($mapReceiptAmount);
        //get receipt and payment method to create GL Detail for payment method
        $lstReceipts = Receipts::whereIn("id", $receiptIds)->with("paymentMethod")->get()->toArray();
        $mapSumPaymentAccId = [];
        foreach ($lstReceipts as $key => $receipt) {

            if (isset($receipt["payment_method"])) {
                $paymentMethod = $receipt["payment_method"];

                if (!isset($mapSumPaymentAccId[$paymentMethod["chart_of_acc_id"]])) {
                    $mapSumPaymentAccId[$paymentMethod["chart_of_acc_id"]] = 0;
                }

                $mapSumPaymentAccId[$paymentMethod["chart_of_acc_id"]] += isset($mapReceiptAmount[$receipt["id"]]) ? $mapReceiptAmount[$receipt["id"]] : 0;
            }
        }

        $lstGLDetails = [];
        foreach ($mapSumPaymentAccId as $accId => $amount) {
            $newGlDetail = [
                "coa_id" => $accId,
                "credit_amount" => 0,
                "debit_amount" => $amount,
                "transaction_type" => $transTyp,
                "general_ledger_id" => $gl["id"]
            ];
            $lstGLDetails[] = $newGlDetail;
        }

        return $lstGLDetails;
    }


    /**
     * Method to update COA fields in shipment detail after shipment completed
     * @param $shipmentId       String shipment id released 
     * @author Sopha Pum | 11-06-2021
     */
    public static function populateCOAFieldsAfterReleasedShipment($shipmentId){
 
        //get existed shipment detail to populate COA fields
        $lstShipmentDetails = ShipmentDetail::where("shipments_id", $shipmentId)
                                        ->with("product")
                                        ->get()
                                        ->toArray();

        foreach ($lstShipmentDetails as $index => &$shipDetail) {
            $product = $shipDetail["product"];

            //COA fields that need to populate in shipment detail
            $shipDetail["cogs_coa_id"] = isset($product["cogs_coa_id"]) ? $product["cogs_coa_id"] : null;
            $shipDetail["inventory_coa_id"] = isset($product["inventory_coa_id"]) ? $product["inventory_coa_id"]: null;
        }

        if(!empty($lstShipmentDetails)){
            $shipDetailController = new ShipmentDetailController();
            $shipDetailController->updateLocal($lstShipmentDetails);
        } 
    }

    /**
     * Method to update COA fields in PR Detail after PR completed
     * @param $receiptId       String receipt id released
     * @author Sopha Pum | 11-06-2021
     */
    public static function populateCOAFieldsAfterReleasedPR($receiptId){
        //get existed PR Detail to populate COA fields
        $lstPRDetails = PurchaseReceiptDetail::where("purchase_receipts_id", $receiptId)
                                                    ->with("product")
                                                    ->get()
                                                    ->toArray();

        foreach ($lstPRDetails as $index => &$prDetail) {
            $product = $prDetail["product"];

            //COA fields that need to populate in shipment detail
            $prDetail["accrual_coa_id"] = isset($product["po_accrual_coa_id"]) ? $product["po_accrual_coa_id"] : null;
            $prDetail["inventory_coa_id"] = isset($product["inventory_coa_id"]) ? $product["inventory_coa_id"]: null;
        }

        if(!empty($lstPRDetails)){
            $prDetailController = new PurchaseReceiptDetailController();
            $prDetailController->updateLocal($lstPRDetails);
        } 
    }
}
