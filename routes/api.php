<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

/** Routes without auth go here */
Route::post('login', 'AuthController@logIn')->middleware(['dynamic_db', 'cors', 'throttle_api:1000,5']);//
Route::post('customer-login', 'AuthController@customerPortalLogIn')->middleware(['dynamic_db', 'cors', 'throttle_api:1000,5']);//

/** Guest Access */
Route::group(['namespace' => 'API', 'middleware' => ['dynamic_db', 'cors', 'throttle_api:200000,1']], function () {

    Route::post('send-mail', 'MailController@index');
    Route::post('send-sms', 'SmsController@index');

    Route::get('404', 'ErrorController@_404');
    Route::get('error-object', 'ErrorController@_404');
    Route::post('error-object', 'ErrorController@_404');
    Route::put('error-object', 'ErrorController@_404');
    Route::delete('error-object', 'ErrorController@_404');

    Route::get("branch", "CompanyController@publicCompany");
    Route::get("branch-user", "UsersController@getPublishUser");

    //Picklist
    Route::get("pl-timezone", "PicklistController@getTimezone");
    Route::get("pl-industry", "PicklistController@getIndustry");

    Route::get('public-categories', 'CategoriesController@publicIndex');
    Route::get('public-products', 'ProductsController@publicProduct');
    Route::get('public-recordtype', 'RecordTypeController@index');
    Route::get('public-photos', 'PhotoController@index');
    Route::get('public-documents', 'DocumentController@publicIndex');


    Route::get('public-articles', 'PostsController@publicIndex');
    Route::get('public-search-articles', 'PostsController@publicSearch');
    Route::get('public-people', 'PersonAccountController@publicIndex');
    Route::post('public-people', 'PersonAccountController@publicStore');
    Route::put('public-people', 'PersonAccountController@publicUpdates');
    Route::get('public-image-gallery', 'PhotoController@publicImageGallery');
    Route::get('public-company', 'CompanyController@publicCompany');
    Route::get('public-stores', 'CompanyController@publicStore');
    Route::get('public-contact', 'ContactController@index');
    Route::get('public-cash-accs', 'CashAccountController@publicPaymentMethod');

    Route::get('public-properties', 'PropertyController@index');
    Route::get('public-product-properties', 'ProductPropertyController@index');

    // Reservation 
    Route::post("guest-reservation", "ReservationController@guestRequestReservation");
    Route::post('public-reservation', 'ReservationController@publicStore');
    Route::get('public-reserved-rooms', 'ReservedRoomController@index');

    Route::get('public-project', 'ProjectController@index');

    Route::get('theme-page', 'SitePageController@publicIndex');
    Route::get('theme-section', 'SectionsController@index');
    Route::get('theme-menu', 'SiteNavController@publicIndex');

    /** GlobalPicklist Route */
    Route::get("guest-global-picklist", "GlobalPicklistController@index");
    Route::get("guest-location-area", "LocationAreaController@index");


    //Route for create sale order
    Route::post('guest-order', 'SaleOrderController@publicStore');
    Route::post('guest-order-detail', 'SaleOrderDetailController@publicStore');

    //route for get meta info
    Route::get('page-meta-info', 'MetaWebsiteController@getPageMeta');
    Route::get('post-meta-info', 'MetaWebsiteController@getArticleMeta');
    Route::get('prod-meta-info', 'MetaWebsiteController@getProductMeta');
    Route::get('project-meta-info', 'MetaWebsiteController@getProjectMeta');

    Route::get('test', 'SiteNavController@testing');

    Route::get('testing-send-mail', 'MailController@index');

});

//route for guest
Route::group(['middleware' => ['dynamic_db', 'auth:api', 'cors', 'user_profile']], function () {
    Route::group(['namespace' => 'API'], function () {
        Route::get('testing-customer', 'UsersController@index');
    });
});


Route::group(['middleware' => ['dynamic_db', 'auth:api', 'throttle_api:200,1', 'cors']], function () {
    Route::post('logout', 'AuthController@logOut');
});

//'throttle1' maximum number of requests that can be made in a given number of minutes.
//'throttle:60,1' 60 times per minute
Route::group(['middleware' => ['dynamic_db', 'auth:api', 'throttle_api:200000,1', 'cors', 'user_profile']], function () {


    Route::group(['namespace' => 'API'], function () {

        /** Block Permission  */
        /** Application */
        Route::apiResource("applications", "ApplicationController", ["only" => ['index', 'store']]);
        Route::put('applications', 'ApplicationController@updates');//mass update

        /** Application Permission */
        Route::apiResource("app-permissions", "AppPermissionController", ["only" => ['index', 'store']]);
        Route::put('app-permissions', 'AppPermissionController@updates');//mass update


        Route::apiResource("object-permissions", "ObjectPermissionController", ["only" => ['index', 'store']]);
        Route::put('object-permissions', 'ObjectPermissionController@updates');//mass update

        Route::apiResource("permissions", "PermissionController", ["only" => ['index', 'store']]);
        Route::put('permissions', 'PermissionController@updates');//mass update

        /** Block Company Model Route */
        /** Settings */
        Route::post('setup-default-acc', 'ConfigController@setupDefaultAccounting');
        Route::apiResource("init-setup", "SetupStepsController", ["only" => ['store', 'index']]);
        Route::put('init-setup', 'SetupStepsController@updates');//mass update
        Route::delete('init-setup', 'SetupStepsController@destroys');//mass delete

        // Metadata
        Route::apiResource("metadatas", "MetadataConfigController", ["only" => ['store', 'index']]);
        Route::put('metadatas', 'MetadataConfigController@updates');//mass update
        Route::delete('metadatas', 'MetadataConfigController@destroys');//mass delete

        /** Channel route */
        Route::apiResource("channels", "ChannelController", ["only" => ['store', 'index']]);
        Route::put('channels', 'ChannelController@updates');//mass update
        Route::delete('channels', 'ChannelController@destroys');//mass delete

        /** Document route */
        Route::apiResource("documents", "DocumentController", ["only" => ['store', 'index']]);
        Route::put('documents', 'DocumentController@updates');//mass update
        Route::delete('documents', 'DocumentController@destroys');//mass delete

        // File upload
        Route::post('upload-photo', 'FileUploadController@uploadImage');
        Route::post('upload-pdf', 'FileUploadController@uploadPDF');
        Route::post('upload-video', 'FileUploadController@uploadVideo');

        /** GlobalPicklist Route */
        Route::apiResource("global-picklists", "GlobalPicklistController", ["only" => ['store', 'index']]);
        Route::put('global-picklists', 'GlobalPicklistController@updates');//mass update
        Route::delete('global-picklists', 'GlobalPicklistController@destroys');//mass delete

        Route::apiResource("currency-picklists", "CurrencyPicklistController", ["only" => ['store', 'index']]);
        Route::put('currency-picklists', 'CurrencyPicklistController@updates');//mass update
        Route::delete('currency-picklists', 'CurrencyPicklistController@destroys');//mass delete

        /** Company currency Route */
        Route::apiResource("currencies", "ShopCurrencyController", ["only" => ['store', 'index']]);
        Route::put('currencies', 'ShopCurrencyController@updates');//mass update
        Route::delete('currencies', 'ShopCurrencyController@destroys');//mass delete

        /** Company currency sheets Route */
        Route::apiResource("currency-sheets", "CurrencySheetsController", ["only" => ['store', 'index']]);
        Route::put('currency-sheets', 'CurrencySheetsController@updates');//mass update
        Route::delete('currency-sheets', 'CurrencySheetsController@destroys');//mass delete

        Route::apiResource("currency-exchanges", "CurrencyExchangeRateController", ["only" => ['store', 'index']]);
        Route::put('currency-exchanges', 'CurrencyExchangeRateController@updates');//mass update
        Route::delete('currency-exchanges', 'CurrencyExchangeRateController@destroys');//mass delete

        Route::apiResource("currency-exchange-history", "CurrencyExchangeRateHisController", ["only" => ['store', 'index']]);
        Route::put('currency-exchange-history', 'CurrencyExchangeRateHisController@updates');//mass update
        Route::delete('currency-exchange-history', 'CurrencyExchangeRateHisController@destroys');//mass delete


        /** Company Route */
        Route::get("company-profile", "CompanyController@getInfo");
        Route::get("company-setting", "CompanyController@getAllConfig");
        Route::apiResource('companies', 'CompanyController', ['only' => ['index', 'store']]);
        Route::put('companies', 'CompanyController@updates');//mass update

        Route::apiResource('contacts', 'ContactController', ['only' => ['index', 'store']]);
        Route::put('contacts', 'ContactController@updates');//mass update
        Route::delete('contacts', 'ContactController@destroys');//mass delete

        /** Profile Route */
        Route::apiResource('profiles', 'ProfileController', ['only' => ['index', 'store']]);
        Route::put('profiles', 'ProfileController@updates');//mass update

        Route::apiResource('profile-permissions', 'ProfilePermissionController', ['only' => ['index', 'store']]);
        Route::put('profile-permissionss', 'ProfilePermissionController@updates');//mass update
        Route::delete('profile-permissions', 'ProfilePermissionController@destroys');//mass delete


        /** Related User Route */
        Route::apiResource('users', 'UsersController', ['only' => ['index', 'store']]);
        Route::put('users', 'UsersController@updates');//mass update
        Route::get("user-profile", 'UsersController@userProfile');
        Route::post("change-password", "UsersController@changePassword");
        Route::apiResource("login-histories", "LoginHistoryController", ["only" => ['index']]);

        // UserRole
        Route::apiResource("user-roles", "UserRoleController", ["only" => ['store', 'index']]);
        Route::put('user-roles', 'UserRoleController@updates');//mass update
        Route::delete('user-roles', 'UserRoleController@destroys');//mass delete

        //System Info Route
        Route::get('app-profiles', "SystemInfoController@getRelatedProfileApp");
        Route::get('app-users', "SystemInfoController@getRelatedUserApp");

        /** Person Accoun Route  */
        Route::apiResource("people", "PersonAccountController", ["only" => ['store', 'index']]);
        Route::put('people', 'PersonAccountController@updates');//mass update
        Route::delete('people', 'PersonAccountController@destroys');//mass delete

        /** Photos */
        Route::apiResource("photos", "PhotoController", ["only" => ['store', 'index']]);
        Route::put('photos', 'PhotoController@updates');//mass update
        Route::delete('photos', 'PhotoController@destroys');//mass delete
        Route::post('photo-upload', 'PhotoController@uploadImage');


        /** Location Area Route */
        Route::apiResource("location-areas", "LocationAreaController", ["only" => ['store', 'index']]);
        Route::put('location-areas', 'LocationAreaController@updates');//mass update
        Route::delete('location-areas', 'LocationAreaController@destroys');//mass delete

        //recordtype
        Route::apiResource("recordtypes", "RecordTypeController", ["only" => ['index']]);
        Route::put('recordtypes', 'RecordTypeController@updates');//mass update
        Route::delete('recordtypes', 'RecordTypeController@destroys');//mass delete

        /** Block Product Master */

        /** Product options */

        /** Products Route  */
        Route::apiResource("products", "ProductsController", ["only" => ['store', 'index']]);
        Route::put('products', 'ProductsController@updates');//mass update
        Route::delete('products', 'ProductsController@destroys');//mass delete
        Route::post('upsert-products', 'ProductsController@apiUpsert');//mass upsert

        /** Product Properties Route */
        Route::apiResource("product-properties", "ProductPropertyController", ["only" => ['store', 'index']]);
        Route::put('product-properties', 'ProductPropertyController@updates');//mass update
        Route::delete('product-properties', 'ProductPropertyController@destroys');//mass delete

        /** Properties Route */
        Route::apiResource("properties", "PropertyController", ["only" => ['store', 'index']]);
        Route::put('properties', 'PropertyController@updates');//mass update
        Route::delete('properties', 'PropertyController@destroys');//mass delete
        Route::post('upsert-properties', 'PropertyController@apiUpsert');//mass upsert

        /** Category route */
        Route::apiResource("categories", "CategoriesController", ["only" => ['store', 'index']]);
        Route::put('categories', 'CategoriesController@updates');//mass update
        Route::delete('categories', 'CategoriesController@destroys');//mass delete

        /** Tag route */
        Route::apiResource("tags", "TagsController", ["only" => ["store", "index"]]);
        Route::put('tags', 'TagsController@updates');//mass update
        Route::delete('tags', 'TagsController@destroys');//mass delete

        // ProductUom
        Route::apiResource("prod-uoms", "ProductUOMController", ["only" => ["store", "index"]]);
        Route::put('prod-uoms', 'ProductUOMController@updates');//mass update
        Route::delete('prod-uoms', 'ProductUOMController@destroys');//mass delete


        /** Price and Cost */
        /** payment method Route */
        Route::apiResource("payment-methods", "PaymentMethodController", ["only" => ['store', 'index']]);
        Route::put('payment-methods', 'PaymentMethodController@updates');//mass update
        Route::delete('payment-methods', 'PaymentMethodController@destroys');//mass delete

        /** Pricebook */
        Route::apiResource("pricebooks", "PricebookController", ["only" => ["store", "index"]]);
        Route::put('pricebooks', 'PricebookController@updates');//mass update
        Route::delete('pricebooks', 'PricebookController@destroys');//mass delete

        /** Pricebook entry */
        Route::apiResource("pricebook-entries", "PricebookEntryController", ["only" => ["store", "index"]]);
        Route::put('pricebook-entries', 'PricebookEntryController@updates');//mass update
        Route::delete('pricebook-entries', 'PricebookEntryController@destroys');//mass delete

        /** Pricebook entry Planner */
        Route::apiResource("pbe-planer", "PricebookEntryPlanerController", ["only" => ["store", "index"]]);
        Route::put('pbe-planer', 'PricebookEntryPlanerController@updates');//mass update
        Route::delete('pbe-planer', 'PricebookEntryPlanerController@destroys');//mass delete

        /** Product Standard Cost Route  */
        Route::apiResource("product-std-costs", "ProductStandardCostController", ["only" => ['store', 'index']]);
        Route::put('product-std-costs', 'ProductStandardCostController@updates');//mass update
        Route::delete('product-std-costs', 'ProductStandardCostController@destroys');//mass delete

        // Cost History
        Route::apiResource("cost-histories", "CostHistoryController", ["only" => ['store', 'index']]);
        Route::put('cost-histories', 'CostHistoryController@updates');//mass update
        Route::delete('cost-histories', 'CostHistoryController@destroys');//mass delete

        // End Block Product Master

        // Block Inventory
        // InvAdjustment TBC
        Route::apiResource("inv-adjs", "InvAdjustmentController", ["only" => ['store', 'index']]);
        Route::put('inv-adjs', 'InvAdjustmentController@updates');//mass update
        Route::delete('inv-adjs', 'InvAdjustmentController@destroys');//mass delete

        // InvAdjDetail TBC
        Route::apiResource("inv-adj-details", "InvAdjDetailController", ["only" => ['store', 'index']]);
        Route::put('inv-adj-details', 'InvAdjDetailController@updates');//mass update
        Route::delete('inv-adj-details', 'InvAdjDetailController@destroys');//mass delete

        // IssueProduct TBC
        Route::apiResource("issue-prods", "IssueProductController", ["only" => ['store', 'index']]);
        Route::put('issue-prods', 'IssueProductController@updates');//mass update
        Route::delete('issue-prods', 'IssueProductController@destroys');//mass delete

        // IssueProductDetail TBC
        Route::apiResource("issue-prod-details", "IssueProductDetailController", ["only" => ['store', 'index']]);
        Route::put('issue-prod-details', 'IssueProductDetailController@updates');//mass update
        Route::delete('issue-prod-details', 'IssueProductDetailController@destroys');//mass delete

        // KitAssemblyDetail TBC
        Route::apiResource("kit-assembly-details", "KitAssemblyDetailController", ["only" => ['store', 'index']]);
        Route::put('kit-assembly-details', 'KitAssemblyDetailController@updates');//mass update
        Route::delete('kit-assembly-details', 'KitAssemblyDetailController@destroys');//mass delete

        // KitAssembly TBC
        Route::apiResource("kit-assemblies", "KitAssemblyController", ["only" => ['store', 'index']]);
        Route::put('kit-assemblies', 'KitAssemblyController@updates');//mass update
        Route::delete('kit-assemblies', 'KitAssemblyController@destroys');//mass delete

        // KitSpec
        Route::apiResource("kit-specs", "KitSpecificationController", ["only" => ['store', 'index']]);
        Route::put('kit-specs', 'KitSpecificationController@updates');//mass update
        Route::delete('kit-specs', 'KitSpecificationController@destroys');//mass delete

        // KitSpecDetail
        Route::apiResource("kit-spec-details", "KitSpecDetailController", ["only" => ['store', 'index']]);
        Route::put('kit-spec-details', 'KitSpecDetailController@updates');//mass update
        Route::delete('kit-spec-details', 'KitSpecDetailController@destroys');//mass delete

        // PhysicalCount TBC
        Route::apiResource("physical-counts", "PhysicalCountController", ["only" => ['store', 'index']]);
        Route::put('physical-counts', 'PhysicalCountController@updates');//mass update
        Route::delete('physical-counts', 'PhysicalCountController@destroys');//mass delete

        // PhysicalCountDetail TBC
        Route::apiResource("physical-count-details", "PhysicalCountDetailController", ["only" => ['store', 'index']]);
        Route::put('physical-count-details', 'PhysicalCountDetailController@updates');//mass update
        Route::delete('physical-count-details', 'PhysicalCountDetailController@destroys');//mass delete

        // ProductWarehouseDetail TBC
        Route::apiResource("prod-warehouse-details", "ProdWareHouseDetailController", ["only" => ['store', 'index']]);
        Route::put('prod-warehouse-details', 'ProdWareHouseDetailController@updates');//mass update
        Route::delete('prod-warehouse-details', 'ProdWareHouseDetailController@destroys');//mass delete

        // ReceiptProductDetail TBC
        Route::apiResource("receipt-prod-details", "ReceiptProductDetailController", ["only" => ['store', 'index']]);
        Route::put('receipt-prod-details', 'ReceiptProductDetailController@updates');//mass update
        Route::delete('receipt-prod-details', 'ReceiptProductDetailController@destroys');//mass delete

        // ReceiptProduct TBC
        Route::apiResource("receipt-prods", "ReceiptProductController", ["only" => ['store', 'index']]);
        Route::put('receipt-prods', 'ReceiptProductController@updates');//mass update
        Route::delete('receipt-prods', 'ReceiptProductController@destroys');//mass delete

        // TransferProdDetail TBC
        Route::apiResource("transfer-prod-details", "TransferProductDetailController", ["only" => ['store', 'index']]);
        Route::put('transfer-prod-details', 'TransferProductDetailController@updates');//mass update
        Route::delete('transfer-prod-details', 'TransferProductDetailController@destroys');//mass delete

        // TransferProduct TBC
        Route::apiResource("transfer-prods", "TransferProductController", ["only" => ['store', 'index']]);
        Route::put('transfer-prods', 'TransferProductController@updates');//mass update
        Route::delete('transfer-prods', 'TransferProductController@destroys');//mass delete

        // ReasonCode TBC
        Route::apiResource("reason-code", "ReasonCodeController", ["only" => ['store', 'index']]);
        Route::put('reason-code', 'ReasonCodeController@updates');//mass update
        Route::delete('reason-code', 'ReasonCodeController@destroys');//mass delete

        // WarehouseLocation
        Route::apiResource("warehouse-locations", "WarehouseLocationsController", ["only" => ['store', 'index']]);
        Route::put('warehouse-locations', 'WarehouseLocationsController@updates');//mass update
        Route::delete('warehouse-locations', 'WarehouseLocationsController@destroys');//mass delete

        /** Warehouse Route */
        Route::apiResource("warehouses", "WarehouseController", ["only" => ['store', 'index']]);
        Route::put('warehouses', 'WarehouseController@updates');//mass update
        Route::delete('warehouses', 'WarehouseController@destroys');//mass delete

        /** UOM Route */
        Route::apiResource("uoms", "UOMController", ["only" => ['store', 'index']]);
        Route::put('uoms', 'UOMController@updates');//mass update
        Route::delete('uoms', 'UOMController@destroys');//mass delete

        // End Block Inventory
        /** ------------ SALE ORDER ROUTES ------------------ */

        //baskets
        Route::apiResource("baskets", "BasketController", ["only" => ['store', 'index']]);
        Route::put('baskets', 'BasketController@updates');//mass update
        Route::delete('baskets', 'BasketController@destroys');//mass delete


        /** Sale Order */
        Route::apiResource("sale-orders", "SaleOrderController", ["only" => ['store', 'index']]);
        Route::put('sale-orders', 'SaleOrderController@updates');//mass update
        Route::delete('sale-orders', 'SaleOrderController@destroys');//mass delete

        /** Sale Order Detail */
        Route::apiResource("sale-order-details", "SaleOrderDetailController", ["only" => ['store', 'index']]);
        Route::put('sale-order-details', 'SaleOrderDetailController@updates');//mass update
        Route::delete('sale-order-details', 'SaleOrderDetailController@destroys');//mass delete
        Route::post('upsert-sale-order-details', 'SaleOrderDetailController@apiUpsert');//mass upsert

        /** Floor Table Route  */
        Route::apiResource("floor-tables", "FloorTableController", ["only" => ['store', 'index']]);
        Route::put('floor-tables', 'FloorTableController@updates');//mass update
        Route::delete('floor-tables', 'FloorTableController@destroys');//mass delete

        // reserve table
        Route::apiResource("reserve-tables", "CustomerReservationController", ["only" => ['store', 'index']]);
        Route::put("reserve-tables", "CustomerReservationController@updates");
        Route::delete("reserve-tables", "CustomerReservationController@destroys");


        /** staff end day route */
        Route::apiResource("staff-end-days", "StaffEndDayController", ["only" => ['store', 'index']]);
        Route::put('staff-end-days', 'StaffEndDayController@updates');//mass update
        Route::delete('staff-end-days', 'StaffEndDayController@destroys');//mass delete

        /** staff shift route */
        Route::apiResource("staff-shifts", "StaffShiftController", ["only" => ['store', 'index']]);
        Route::put('staff-shifts', 'StaffShiftController@updates');//mass update
        Route::delete('staff-shifts', 'StaffShiftController@destroys');//mass delete
        Route::post('end-shift', 'StaffShiftController@endShift');// request for end shift

        /** theory collection route */
        Route::apiResource("theory-collections", "TheoryCollectionController", ["only" => ['store', 'index']]);
        Route::put('theory-collections', 'TheoryCollectionController@updates');//mass update
        Route::delete('theory-collections', 'TheoryCollectionController@destroys');//mass delete

        /** cash note route */
        Route::apiResource("cash-notes", "CashNoteController", ["only" => ['store', 'index']]);
        Route::put('cash-notes', 'CashNoteController@updates');//mass update
        Route::delete('cash-notes', 'CashNoteController@destroys');//mass delete

        Route::apiResource("cash-note-details", "CashNoteDetailController", ["only" => ['store', 'index']]);
        Route::put('cash-note-details', 'CashNoteDetailController@updates');//mass update
        Route::delete('cash-note-details', 'CashNoteDetailController@destroys');//mass delete


        /** sale transaction count */
        Route::apiResource("sale-transaction-counts", "SaleTransactionCountController", ["only" => ['store', 'index']]);
        Route::put('sale-transaction-counts', 'SaleTransactionCountController@updates');//mass update
        Route::delete('sale-transaction-counts', 'SaleTransactionCountController@destroys');//mass delete

        /** Invoice Route */
        Route::apiResource("invoices", "InvoiceController", ["only" => ['store', 'index']]);
        Route::put('invoices', 'InvoiceController@updates');//mass update
        Route::delete('invoices', 'InvoiceController@destroys');//mass delete
        Route::get("invoice-sale-order", "InvoiceController@getInvoiceBySaleOrder");

        /** Invoice Detail Route */
        Route::apiResource("invoice-details", "InvoiceDetailController", ["only" => ['store', 'index']]);
        Route::put('invoice-details', 'InvoiceDetailController@updates');//mass update

        /** Receipt Route */
        Route::apiResource("receipts", "ReceiptController", ["only" => ['store', 'index']]);
        Route::put('receipts', 'ReceiptController@updates');//mass update
        Route::delete('receipts', 'ReceiptController@destroys');//mass delete


        /** Invoice Receipt Route */
        Route::apiResource("invoices-receipts", "InvoiceReceiptController", ["only" => ['store', 'index']]);
        Route::put('invoices-receipts', 'InvoiceReceiptController@updates');//mass update


        /** Printed Invoice History Route */
        Route::apiResource("printed-invoices", "PrintedInvoiceHistoryController", ["only" => ['store', 'index']]);
        Route::put('printed-invoices', 'PrintedInvoiceHistoryController@updates');//mass update

        /** Shipment Route */
        Route::apiResource("shipments", "ShipmentController", ["only" => ['store', 'index']]);
        Route::put('shipments', 'ShipmentController@updates');//mass update
        Route::delete('shipments', 'ShipmentController@destroys');//mass delete

        /** Shipment Detail Route */
        Route::apiResource("shipment-details", "ShipmentDetailController", ["only" => ['store', 'index']]);
        Route::put('shipment-details', 'ShipmentDetailController@updates');//mass update
        Route::delete('shipment-details', 'ShipmentDetailController@destroys');//mass delete


        /** Documents */
        // Route::apiResource("document", "DocumentController", ["only" => ['show', 'update', 'destroy']]);
        // Route::apiResource("documents", "DocumentController", ["only" => ['store', 'index']]);
        // Route::put('documents', 'DocumentController@updates');//mass update
        // Route::delete('documents', 'DocumentController@destroys');//mass delete

        //reports
        // Route::get('report-sale-detail', "ReportController@reportSaleDetail");
        // Route::post('report-sale-summary', "ReportController@reportSaleSummary");
        Route::get('report-sale-summary', "ReportSaleSummaryController@index");
        Route::get('report-sale-by-item', "ReportSaleByItemController@index");
        Route::get('report-sale-by-channel', "ReportSaleByChannelController@index");


        /** Accounting Route **/

        //Accounting Book Route
        Route::apiResource("acc-books", "AccountingBookController", ["only" => ['store', 'index']]);
        Route::put('acc-books', 'AccountingBookController@updates');//mass update
        Route::delete('acc-books', 'AccountingBookController@destroys');//mass delete

        //Accounting Class Route //NOTE: Accounting Class cannot delete
        Route::apiResource("acc-classes", "AccountingClassController", ["only" => ['store', 'index']]);
        Route::put('acc-classes', 'AccountingClassController@updates');//mass update
        // Route::delete('acc-classes', 'AccountingClassController@destroys');//mass delete

        //Cash Account
        Route::apiResource("cash-accs", "CashAccountController", ["only" => ['store', 'index']]);
        Route::put('cash-accs', 'CashAccountController@updates');//mass update
        Route::delete("cash-accs", "CashAccountController@destroys");//mass delete

        //cash transfer
        Route::apiResource("cash-transfers", "CashTransferController", ["only" => ['store', 'index']]);
        Route::put('cash-transfers', 'CashTransferController@updates');//mass update
        Route::delete('cash-transfers', 'CashTransferController@destroys');//mass delete

        //cash transfer detail
        Route::apiResource("cash-transfers-detail", "CashTransferDetailController", ["only" => ['store', 'index']]);
        Route::put('cash-transfers-detail', 'CashTransferDetailController@updates');//mass update
        Route::delete('cash-transfers-detail', 'CashTransferDetailController@destroys');//mass delete


        //Chart of Account Route //NOTE: Chart of account not allow to delete, update code and accounting class
        Route::apiResource("chart-of-accs", "ChartOfAccountController", ["only" => ['store', 'index']]);
        Route::put('chart-of-accs', 'ChartOfAccountController@updates');//mass update
        Route::delete('chart-of-accs', 'ChartOfAccountController@destroys');//mass delete

        //General Ledger Route
        Route::apiResource("gls", "GeneralLedgerController", ["only" => ['store', 'index']]);
        Route::put('gls', 'GeneralLedgerController@updates');//mass update
        Route::delete('gls', 'GeneralLedgerController@destroys');//mass delete

        //General Ledger Details Route
        Route::apiResource("gl-details", "GeneralLedgerDetailsController", ["only" => ['store', 'index']]);
        Route::put('gl-details', 'GeneralLedgerDetailsController@updates');//mass update
        Route::delete('gl-details', 'GeneralLedgerDetailsController@destroys');//mass delete

        //gl account Mapping Route
        Route::apiResource("gl-acc-mappings", "GLAccMappingController", ["only" => ['store', 'index']]);
        Route::put('gl-acc-mappings', 'GLAccMappingController@updates');//mass update
        Route::delete('gl-acc-mappings', 'GLAccMappingController@destroys');//mass delete

        //Reports
        Route::apiResource("coa-trans-summary", "COATransactionSummaryController", ["only" => ['index']]);
        Route::apiResource("coa-trans-detail", "COATransactionDetailController", ["only" => ['index']]);


        /** End Accounting Route **/

        /** ------------ PURCHASE ORDER ROUTES ------------------ */

        /** Purchase Order  route */
        Route::apiResource("purchase-orders", "PurchaseOrderController", ["only" => ['store', 'index']]);
        Route::put('purchase-orders', 'PurchaseOrderController@updates');//mass update
        Route::delete('purchase-orders', 'PurchaseOrderController@destroys');//mass delete

        /** Purchase Order detail route  */
        Route::apiResource("purchase-order-detail", "PurchaseOrderDetailController", ["only" => ['store', 'index']]);
        Route::put('purchase-order-detail', 'PurchaseOrderDetailController@updates');//mass update
        Route::delete('purchase-order-detail', 'PurchaseOrderDetailController@destroys');//mass delete
        Route::post('upsert-purchase-order-detail', 'PurchaseOrderDetailController@apiUpsert');//mass upsert

        /** Purchase Receipt route */
        Route::apiResource("purchase-receipt", "PurchaseReceiptController", ["only" => ['store', 'index']]);
        Route::put('purchase-receipt', 'PurchaseReceiptController@updates');//mass update
        Route::delete('purchase-receipt', 'PurchaseReceiptController@destroys');//mass delete

        /** Purchase Receipt detail route */
        Route::apiResource("purchase-receipt-details", "PurchaseReceiptDetailController", ["only" => ['store', 'index']]);
        Route::put('purchase-receipt-details', 'PurchaseReceiptDetailController@updates');//mass update
        Route::delete('purchase-receipt-details', 'PurchaseReceiptDetailController@destroys');//mass delete
        Route::post('upsert-purchase-receipt-details', 'PurchaseReceiptDetailController@apiUpsert');//mass upsert

        /** Purchase Bill route */
        Route::apiResource("purchase-bill", "PurchaseBillController", ["only" => ['store', 'index']]);
        Route::put('purchase-bill', 'PurchaseBillController@updates');//mass update
        Route::delete('purchase-bill', 'PurchaseBillController@destroys');//mass delete

        /** Purchase Payment route */
        Route::apiResource("purchase-payment", "PurchasePaymentController", ["only" => ['store', 'index']]);
        Route::put('purchase-payment', 'PurchasePaymentController@updates');//mass update
        Route::delete('purchase-payment', 'PurchasePaymentController@destroys');//mass delete

        /** Purchase Bill Payment route */
        Route::apiResource("purchase-bill-payment", "PurchaseBillPaymentController", ["only" => ['store', 'index']]);
        Route::put('purchase-bill-payment', 'PurchaseBillPaymentController@updates');//mass update
        Route::delete('purchase-bill-payment', 'PurchaseBillPaymentController@destroys');//mass delete

        /** Purchase Bill Detail route */
        Route::apiResource("purchase-bill-detail", "PurchaseBillDetailController", ["only" => ['store', 'index']]);
        Route::put('purchase-bill-detail', 'PurchaseBillDetailController@updates');//mass update
        Route::delete('purchase-bill-detail', 'PurchaseBillDetailController@destroys');//mass delete

        /** ------------ END PURCHASE ORDER ROUTES ------------------ */


        /** Website theme */

        /** Site nav */
        Route::apiResource("site-nav", "SiteNavController", ["only" => ['show', 'update', 'destroy']]);
        Route::apiResource("site-navs", "SiteNavController", ["only" => ['store', "index"]]);
        Route::put('site-navs', 'SiteNavController@updates');//mass update

        /** Site Page */
        Route::apiResource("site-page", "SitePageController", ["only" => ['show', 'update', 'destroy']]);
        Route::apiResource("site-pages", "SitePageController", ["only" => ['store', "index"]]);
        Route::put('site-pages', 'SitePageController@updates');//mass update

        /** Section */
        Route::apiResource("section", "SectionsController", ["only" => ['show', 'update', 'destroy']]);
        Route::apiResource("sections", "SectionsController", ["only" => ['store', "index"]]);
        Route::put('sections', 'SectionsController@updates');//mass update
        Route::delete('sections', 'SectionsController@destroys');//mass delete

        /** asset */
        Route::apiResource("asset", "AssetsController", ["only" => ["show", 'update', 'destroy']]);
        Route::apiResource("assets", "AssetsController", ["only" => ['store', 'index']]);
        Route::put('assets', 'AssetsController@updates');//mass update
        Route::delete('assets', 'AssetsController@destroys');//mass delete

        /** Project Route  */
        Route::apiResource("project", "ProjectController", ["only" => ['show', 'update', 'destroy']]);
        Route::apiResource("projects", "ProjectController", ["only" => ['store', 'index']]);
        Route::put('projects', 'ProjectController@updates');//mass update
        Route::delete('projects', 'ProjectController@destroys');//mass delete

        /** Project property Route  */
        Route::apiResource("project-property", "ProjectPropertyController", ["only" => ['show', 'update', 'destroy']]);
        Route::apiResource("project-properties", "ProjectPropertyController", ["only" => ['store', 'index']]);
        Route::put('project-properties', 'ProjectPropertyController@updates');//mass update
        Route::delete('project-properties', 'ProjectPropertyController@destroys');//mass delete

        /** posts Route  */
        Route::apiResource("post", "PostsController", ["only" => ['show', 'update', 'destroy']]);
        Route::apiResource("posts", "PostsController", ["only" => ['index', 'store']]);
        Route::put('posts', 'PostsController@updates');//mass update
        Route::delete('posts', 'PostsController@destroys');//mass delete

        /** Reservation route */
        Route::apiResource("reservation", "ReservationController", ["only" => ['store', 'index']]);
        Route::put('reservation', 'ReservationController@updates');//mass update
        Route::delete('reservation', 'ReservationController@destroys');//mass delete

        /** Reserved Room route */
        Route::apiResource("reserved-rooms", "ReservedRoomController", ["only" => ['store', 'index']]);
        Route::put('reserved-rooms', 'ReservedRoomController@updates');//mass update
        Route::delete('reserved-rooms', 'ReservedRoomController@destroys');//mass delete
        Route::post('upsert-reserved-rooms', 'ReservedRoomController@apiUpsert');//mass upsert 

        /** Reserved room price */
        Route::apiResource("reserved-room-prices", "ReservedRoomPriceController", ["only" => ['store', 'index']]);
        Route::put('reserved-room-prices', 'ReservedRoomPriceController@updates');//mass update
        Route::delete('reserved-room-prices', 'ReservedRoomPriceController@destroys');//mass delete

        /** Room Status route */
        Route::apiResource("room-status", "RoomStatusController", ["only" => ['store', 'index']]);
        Route::put('room-status', 'RoomStatusController@updates');//mass update
        Route::delete('room-status', 'RoomStatusController@destroys');//mass delete


        /** Hotel / Reservation */
        Route::apiResource("room-amenities", "RoomAmenitiesController", ["only" => ['store', 'index']]);
        Route::put("room-amenities", "RoomAmenitiesController@updates");
        Route::delete("room-amenities", "RoomAmenitiesController@destroys");
        Route::post("upsert-room-amenities", "RoomAmenitiesController@apiUpsert");

        Route::apiResource("hotel-amenities", "HotelAmenitiesController", ["only" => ['store', 'index']]);
        Route::put("hotel-amenities", "HotelAmenitiesController@updates");
        Route::delete("hotel-amenities", "HotelAmenitiesController@destroys");
        Route::post("upsert-hotel-amenities", "HotelAmenitiesController@apiUpsert");

        /** Reserved Room route */
        Route::apiResource("reserved-rooms", "ReservedRoomController", ["only" => ['store', 'index']]);
        Route::put('reserved-rooms', 'ReservedRoomController@updates');//mass update
        Route::delete('reserved-rooms', 'ReservedRoomController@destroys');//mass delete
        Route::post('upsert-reserved-rooms', 'ReservedRoomController@apiUpsert');//mass upsert 

        /** Reserved room price */
        Route::apiResource("reserved-room-prices", "ReservedRoomPriceController", ["only" => ['store', 'index']]);
        Route::put('reserved-room-prices', 'ReservedRoomPriceController@updates');//mass update
        Route::delete('reserved-room-prices', 'ReservedRoomPriceController@destroys');//mass delete


        /** Room Status route */
        Route::apiResource("room-status", "RoomStatusController", ["only" => ['store', 'index']]);
        Route::put('room-status', 'RoomStatusController@updates');//mass update
        Route::delete('room-status', 'RoomStatusController@destroys');//mass delete

    });

});

Route::group(['middleware' => []], function () {

    Route::group(['namespace' => 'API'], function () {

        Route::apiResource("aggregators", "AggregatorController", ["only" => ['store', 'index', 'show']]);

    });

});


