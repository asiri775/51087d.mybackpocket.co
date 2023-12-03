<?php
use App\Http\Controllers\BankController;
use Illuminate\Support\Facades\Route;

// Route::get('imap', 'ImapController@index');

Route::get('/', 'Auth\LoginController@showLoginForm');

Route::get('parse', 'ParseEmailController@index');
Route::get('reset', 'ResetCronController@index');
Route::get('reset-database', 'ResetCronController@resetDatabase')->name('ResetDatabase');

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');
Route::get('/admin/dashboard', 'DashboardController@index')->name('admin.dashboard');
Route::middleware(['auth', 'admin'])->group(function () {
Route::group(['prefix' => 'admin', 'namespace' => 'Transactions', 'name' => 'admin'], function () {
    Route::get('transactions', 'TransactionsController@index')->name('transactions.list');
    Route::post('transactions-datatable', 'TransactionsTableController')->name('transactions.datatable');
    Route::get('archived', 'TransactionsController@archiveList')->name('ArchicvedTransactions.list');
    Route::get('transactions/visible', 'TransactionsController@visible')->name('transactions.makeItVisible');
    Route::post('archived-transactions-datatable', 'ArchivedTransactionsTableController')->name('ArchivedTransactions.datatable');
    Route::get('transactions/{transaction}', 'TransactionsController@show')->name('transactions.detail');
    Route::get('transactions/pdf/{transaction}', 'TransactionsController@pdf')->name('transactions.pdf');
    Route::get('transactions/mpdf/{transaction}', 'TransactionsController@mpdf')->name('transactions.mpdf');
    Route::post('transactions/notify', 'TransactionsController@notify')->name('transactions.notify');
    Route::post('transactions/notify-list', 'TransactionsController@notifyList')->name('transactions.notify.list');
    Route::post('transactions/add-to-envelope/{transaction}', 'TransactionsController@AddToEnvelope')->name('transactions.AddToEnvelope');
    Route::post('transactions/add-to-budgets/{transaction}', 'TransactionsController@AddToBudgetTransacation')->name('transactions.AddToBudgetTransacation');
    Route::post('archive/transactions', 'TransactionsController@archive')->name('transactions.archive');
    Route::post('hide/transactions', 'TransactionsController@hide')->name('transactions.hide');
    Route::post('hide-all/transactions', 'TransactionsController@hideAll')->name('transactions.hideAll');
    Route::post('transactions/envelope/autocomplete', 'TransactionsController@envelopeAutoComplete')->name('transactions.envAuto');
    Route::post('transactions/budget/autocomplete', 'TransactionsController@budgetAutoComplete')->name('transactions.BudgetAuto');
    Route::post('transactions/print-all', 'TransactionsController@printAll')->name('transactions.printAll');
    Route::post('transactions/print-list', 'TransactionsController@savePDF')->name('transactions.printList');
    Route::post('transactions/export-all', 'TransactionsController@exportAll')->name('transactions.exportAll');

});

Route::group(['prefix' => 'admin', 'namespace' => 'Vendors', 'name' => 'admin'], function () {
  Route::get('vendors', 'VendorsController@index')->name('vendors.list');
  Route::get('vendors/add', 'VendorsController@add')->name('add.vendor');
  Route::post('vendors/store', 'VendorsController@storeVendor')->name('store.vendor');
  Route::get('vendors/visible', 'VendorsController@visible')->name('vendors.makeItVisible');
  Route::post('vendors/search', 'VendorsController@search');
  Route::get('vendors/new-this-week', 'VendorsController@week')->name('vendors.week');
  Route::get('vendors/new-this-month', 'VendorsController@month')->name('vendors.month');
  Route::get('vendors/recent', 'VendorsController@recentVendors')->name('vendors.recent');
  Route::post('vendors-datatable', 'VendorsTableController')->name('vendors.datatable');
  Route::get('vendors/{vendor}', 'VendorsController@show')->name('vendors.detail');
  Route::post('vendors/select-date', 'VendorsController@selectDate')->name('vendors.selectDate');
  Route::post('hide/vendors', 'VendorsController@hide')->name('vendors.hide');
  Route::post('hide-all/vendors', 'VendorsController@hideAll')->name('vendors.hideAll');
  Route::get('vendors/edit-vendor/{vendor}', 'VendorsController@editVendor')->name('edit.vendor');
  Route::post('vendors/delete-vendor', 'VendorsController@deleteVendor')->name('delete.vednor');
  Route::post('vendors/edit-vendor/{vendor}', 'VendorsController@editVendorPost')->name('edit.vendor.post');
  Route::get('get-all-vendors', 'VendorsController@getAllVendors')->name('get.all.vendors');

});

Route::group(['prefix' => 'admin', 'namespace' => 'Products', 'name' => 'admin'], function () {
    Route::get('products', 'ProductsController@index')->name('products.list');
    Route::post('products-datatable', 'ProductsTableController')->name('products.datatable');
    Route::get('products/{product}', 'ProductsController@show')->name('products.detail');
    Route::post('hide/products', 'ProductsController@hide')->name('products.hide');
    Route::get('products-visible', 'ProductsController@productVisible')->name('products.Visible');
    Route::post('hide-all/products', 'ProductsController@hideAll')->name('products.hideAll');
});

Route::group(['prefix' => 'admin', 'namespace' => 'Sales', 'name' => 'admin'], function () {
    Route::get('sales', 'SalesController@index')->name('sales.list');
    Route::get('sales/top', 'SalesController@topSales')->name('sales.top');
    Route::post('sales-datatable', 'SalesTableController')->name('sales.datatable');
});


Route::group(['prefix' => 'admin', 'namespace' => 'Envelope', 'name' => 'admin'], function () {
    Route::get('envelopes', 'EnvelopeController@index')->name('envelope.list');
    Route::post('envelopes/create-envelope', 'EnvelopeController@addEnvelope')->name('create-envelope');
    Route::get('envelopes/preview/{id}', 'EnvelopeController@previewEnvelope')->name('preview-envelope');
    Route::get('envelopes/print/{id}', 'EnvelopeController@printPdf')->name('print-pdf');
    Route::get('envelopes/previewEnvelope/delete/{id}', 'EnvelopeController@deleteExisting')->name('delete-existing');
    Route::get('envelopes/delete/{id}', 'EnvelopeController@deleteEnvelope')->name('delete-envelope');
    Route::get('envelopes/add-existing-envelope', 'EnvelopeController@previewExistingEnvelope')->name('preview-exsisting-envelope');
    Route::post('envelopes/add-to-existing-envelope', 'EnvelopeController@addToExistingEnvelope')->name('add-to-exsisting-envelope');
    Route::post('envelopes/previewEnvelope/delete', 'EnvelopeController@deletePreview')->name('delete-preview');
    Route::post('envelopes/edit-envelope/{id}', 'EnvelopeController@editEnvelope')->name('edit-envelope');
    Route::post('envelopes-datatable', 'EnvelopesTableController')->name('envelopes.datatable');
    Route::get('envelopes/select-existing-envelope', 'EnvelopeController@selectExistingEnvelope')->name('select-existing-envelope');
    Route::get('envelopes/addEnvelope', 'EnvelopeController@preview')->name('add-envelope');
    Route::post('envelopes/storeEnvelope', 'EnvelopeController@store')->name('envelope-store');
    Route::post('envelopes/bulkComplete', 'EnvelopeController@bulkComplete')->name('envelope-bulkComplete');
    Route::post('envelopes/Complete/{id}', 'EnvelopeController@completeEnvelope')->name('envelope-complete');
    Route::post('envelopes/bulk-download', 'EnvelopeController@bulkDownload')->name('bulk-download');
    Route::post('envelopes/bulk-delete/{id}', 'EnvelopeController@bulkDelete')->name('bulk-delete');
    Route::get('envelopes/envelope-data', 'EnvelopeController@EnvelopeData')->name('envelope-data');
    Route::get('envelopes/items/delete/{id}', 'EnvelopeController@deleteEnvelopeItem')->name('delete-envelope-item');
    Route::post('envelopes/session/add-existing-envelope', 'EnvelopeController@addExistingIdSession')->name('existingEnvelope.addSession');
    Route::post('envelopes/session/remove-existing-envelope', 'EnvelopeController@removeExistingIdSession')->name('existingEnvelope.removeSession');
    Route::post('envelopes/email/{id}', 'EnvelopeController@email')->name('envelope.email');
    Route::get('envelopes/AddReceipts/{id}', 'AddReceiptController@index')->name('AddReceipts.list');
    Route::post('envelopes/AddReceipts-datatable', 'AddReceiptTableController')->name('AddReceipts.datatable');
    Route::get('envelopes/AddReceipts/{transaction}', 'AddReceiptController@show')->name('AddReceipts.detail');
    Route::get('envelopes/AddReceipts/pdf/{transaction}', 'AddReceiptController@pdf')->name('AddReceipts.pdf');
    Route::get('envelopes/AddReceipts/mpdf/{transaction}', 'AddReceiptController@mpdf')->name('AddReceipts.mpdf');
    Route::post('envelopes/AddReceipts/session/bulkEnvelope', 'AddReceiptController@bulkSession')->name('AddReceipts.bulkSession');
    Route::post('envelopes/AddReceipts/session/clearInvoice', 'AddReceiptController@clearSession')->name('AddReceipts.clearSession');
    Route::post('envelopes/AddReceipts/session/addInvoice', 'AddReceiptController@addSession')->name('AddReceipts.addSession');
    Route::post('envelopes/AddReceipts/session/removeInvoice', 'AddReceiptController@removeSession')->name('AddReceipts.removeSession');
    Route::post('envelopes/notify', 'EnvelopeController@notify')->name('envelopes.notify');
    Route::get('get-all-envelopes', 'EnvelopeController@getAllEnvelopes')->name('get.all.envelopes');
});

Route::group(['prefix' => 'admin', 'namespace' => 'Reports', 'name' => 'admin'], function () {
    Route::get('reports', 'EnvelopeReportController@index')->name('reports.list');
    Route::post('reports-datatable', 'EnvelopesReportTableController')->name('envelopesReports.datatable');
    Route::get('reports/users/{id}', 'EnvelopeReportController@usersList')->name('reports.users');
    Route::get('reports/preview/{id}', 'PreviewReportController@previewReport')->name('preview.reports');
    Route::get('reports/print/{id}', 'PreviewReportController@printReportPdf')->name('report-pdf');
    Route::get('reports/download/{id}', 'PreviewReportController@reportDownload')->name('report-pdf');
    Route::post('reports/notifyReport', 'PreviewReportController@notifyReport')->name('preview.notify.report');
    Route::get('reports/printUserReportPdf/{id}', 'PreviewReportController@printUserReportPdf')->name('preview.user.report.pdf');
    Route::get('reports/printUserReportPdfDownload/{id}', 'PreviewReportController@printUserReportPdfDownload')->name('preview.user.report.pdf.download');
    //Route::post('reports/notifyList', 'PreviewReportController@notifyList')->name('preview.notify.report.list');
  // Route::get('reports/share-report/generater{id}', 'PreviewReportController@shereLink')->name('share.generater');
});

Route::group(['prefix' => 'admin', 'namespace' => 'Budgets', 'name' => 'admin'], function () {
    Route::get('budgets', 'BudgetController@index')->name('budgets.list');
    Route::post('budgets/create-budget', 'BudgetController@addBudget')->name('create-budget');
    Route::get('budgets/delete/{id}', 'BudgetController@deleteBudget')->name('delete-budget');
    Route::get('budgets/preview/{id}', 'BudgetController@previewBudget')->name('preview-budget');
    Route::post('budgets/edit-budget/{id}', 'BudgetController@editBudget')->name('edit-budget');
    Route::get('budgets/items/delete/{id}', 'BudgetController@deleteBudgetItem')->name('delete-budget-item');
    Route::post('budgets/bulk-delete/{id}', 'BudgetController@bulkDelete')->name('budget-bulk-delete');
    Route::get('budgets/AddReceipts/{id}', 'AddBudgetReceiptController@index')->name('AddBudgetReceipts.list');
    Route::get('budgets/add-receipts', 'AddBudgetReceiptController@previewExistingBudget')->name('budgets.add-receipts');
    Route::post('budgets/AddReceipts-datatable', 'AddBudgetReceiptTableController')->name('AddBudgetReceipts.datatable');
    Route::post('budgets/AddReceipts/session/bulkBudgets', 'AddBudgetReceiptController@bulkSession')->name('AddBudgetReceipts.bulkSession');
    Route::post('budgets/AddReceipts/session/clearBudgets', 'AddBudgetReceiptController@clearSession')->name('AddBudgetReceipts.clearSession');
    Route::post('budgets/AddReceipts/session/addBudgets', 'AddBudgetReceiptController@addSession')->name('AddBudgetReceipts.addSession');
    Route::post('budgets/AddReceipts/session/removeBudgets', 'AddBudgetReceiptController@removeSession')->name('AddBudgetReceipts.removeSession');
    Route::get('budgets/AddReceipts/delete-receipt/{id}', 'AddBudgetReceiptController@deleteReceipt')->name('delete-receipt');
    Route::post('budgets/add-to-budget', 'AddBudgetReceiptController@addToBudget')->name('add-to-budget');
    Route::get('budgets/reports', 'BudgetReportController@index')->name('budget-reports.list');
    Route::post('budgets/reports-datatable', 'BudgetReportTableController')->name('BudgetReports.datatable');
    Route::get('budgets/reports/users/{id}', 'BudgetReportController@usersList')->name('BudgetReports.users');
    Route::get('get-all-budgets', 'BudgetController@getAllBudgets')->name('get.all.budgets');
});


Route::group(['prefix' => 'admin', 'namespace' => 'Categories', 'name' => 'admin'], function () {
    Route::get('categories', 'CategoryController@index')->name('category.list');
    Route::post('categories/create-main-category', 'CategoryController@addMainCategory')->name('main-category');
    Route::post('categories/update-main-category', 'CategoryController@editMainCategory')->name('edit-main-category');
    Route::post('categories/create-sub-category', 'CategoryController@addSubCategory')->name('sub-category');
    Route::post('categories/update-sub-category', 'CategoryController@editSubCategory')->name('edit-sub-category');
    Route::post('categories/create-child-category', 'CategoryController@addChildCategory')->name('child-category');
    Route::post('categories/update-child-category', 'CategoryController@editChildCategory')->name('edit-child-category');
    Route::post('categories/delete', 'CategoryController@deleteCategory')->name('delete-category');
    Route::get('categories/delete-categories', 'CategoryController@deleteCategories')->name('delete.categories');
    Route::get('categories/delete/{id}', 'CategoryController@deleteCategory')->name('delete-category');
    Route::post('categories/add-session', 'CategoryController@addCategorySession')->name('Cat.addSession');
    Route::post('categories/remove-session', 'CategoryController@removeCategorySession')->name('Cat.removeSession');
    Route::post('categories/main-category-datatable', 'MainCategoryTableController')->name('mainCat.datatable');
    Route::post('categories/sub-category-datatable', 'SubCategoryTableController')->name('subCat.datatable');
    Route::post('categories/child-category-datatable', 'ChildCategoryTableController')->name('childCat.datatable');
    Route::get('categories/update-category/{category}', 'CategoryController@updateCategory')->name('update.category');
    Route::post('categories/update-category-post/{category}', 'CategoryController@updateCategoryPost')->name('update.category.post');
  });


Route::group(['prefix' => 'admin', 'name' => 'admin'], function () {
    Route::get('bank-statements', 'BankStatementController@list')->name('bankStatements.list');
    Route::post('bank-statements/new-statement', 'BankStatementController@addNewStatement')->name('bankStatements.addNewStatement');
    Route::post('bank-statements/edit-statement', 'BankStatementController@editStatement')->name('bankStatements.editStatement');
    Route::post('bank-statements/bulk-update-statement', 'BankStatementController@bulkUpdateStatement')->name('bankStatements.bulkUpdateStatement');
    Route::post('bank-statements/bulk-update-statements', 'BankStatementController@bulkUpdateStatements')->name('bankStatements.bulkUpdateStatements');
    Route::post('bank-statements/update-invoice-category', 'BankStatementController@updateInvoiceCategory')->name('bankStatements.updateInvoiceCategory');
    Route::get('bank-statements/transactions/{id}', 'BankStatementController@showTransactions')->name('bankStatements.showTransactions');
    Route::get('bank-statements/list-transactions/{id}', 'BankStatementController@listTransactions')->name('bankStatements.listTransactions');
    Route::get('bank-statements/delete-statement/{id}', 'BankStatementController@deleteStatement')->name('bankStatements.deleteStatement');
    Route::get('bank-statements/delete-statements/{id}', 'BankStatementController@deleteStatements')->name('bankStatements.deleteStatements');
    Route::get('bank-statements/all-transactions/', 'BankStatementController@allTransactions')->name('bankStatements.allTransactions');
    Route::get('bank-statements/all-transactions/export', 'BankStatementController@allTransactionsExport')->name('bankStatements.allTransactions.export');
	Route::get('bank-statements/all-transactions/export-by-year', 'BankStatementController@allTransactionsExportByYear')->name('bankStatements.allTransactions.export.byYear');
    Route::post('bank-statements/transactions-data-table/{id}', 'BankStatementController@transactionDataTable')->name('bankStatements.transactionDataTable');
    Route::post('bank-statements/statements-data-table', 'BankStatementController@statementsDataTable')->name('bankStatements.statementsDataTable');
    Route::resource('banks', 'BankController');
    Route::resource('bank-accounts', 'BankAccountController');

});

Route::group(['prefix' => 'admin', 'namespace' => 'UserManagement', 'name' => 'admin'], function () {
  Route::get('users', 'UserController@index')->name('admin.users');
  Route::get('add-users', 'UserController@addUsers')->name('admin.users.addUsers');
  Route::post('users/store', 'UserController@store')->name('admin.users.store');
  Route::post('users-datatable', 'UserTableController')->name('admin.users.datatable');
  Route::get('user/edit-user/{user}', 'UserController@editUsers')->name('admin.users.edit');
  Route::post('user/edit-user/{user}', 'UserController@update')->name('admin.users.update');
  Route::post('user/delete/{user}', 'UserController@delete')->name('admin.users.delete');
  Route::get('user/show/{user}', 'UserController@show')->name('admin.users.show');
});

Route::group(['prefix' => 'admin', 'namespace' => 'Roles', 'name' => 'admin'], function () {	
  Route::get('roles', 'RoleController@index')->name('admin.roles');	
  Route::post('roles/store', 'RoleController@store')->name('admin.roles.store');	
  Route::post('roles/update', 'RoleController@update')->name('admin.roles.update');	
  Route::get('roles/delete/{id}', 'RoleController@delete')->name('admin.roles.delete');	
});

Route::group(['prefix' => 'admin/settings', 'name' => 'admin'], function () {
  Route::get('/', 'SettingsController@index')->name('admin.settings.index');
  Route::post('edit', 'SettingsController@edit')->name('admin.settings.edit');
});

Route::get('reports/share-report/{id}', 'ShareLinksController@shereReport')->name('share.reports');
Route::get('email/share-email/{id}', 'ShareLinksController@shareEmail')->name('share.email');
});

// User routes
Route::middleware(['auth', 'user'])->group(function () {
    Route::get('/user/dashboard', 'DashboardController@userIndex')->name('user.dashboard');
    Route::post('user-dashboard-transactions-datatable', 'User\DashboardReceiptsTableController')->name('user.dashboard.transactions.datatable');
    Route::post('user-dashboard-envelope-datatable', 'User\DashboardEnvelopesTableController')->name('user.dashboard.envelope.datatable');
    Route::group(['prefix' => 'user', 'namespace' => 'User\Transactions', 'name' => 'user'], function () {
      Route::get('transactions', 'TransactionController@index')->name('user.transactions.index');
      Route::post('user-transactions-datatable', 'TransactionTableController')->name('user.transactions.datatable');
      Route::get('user-transactions/{transaction}', 'TransactionController@show')->name('user.transactions.show');
      Route::post('print-all', 'TransactionController@printAll')->name('user.transactions.printAll');
      Route::post('print-list', 'TransactionController@savePDF')->name('user.transactions.printList');
      Route::post('export-all', 'TransactionController@exportAll')->name('user.transactions.exportAll');
      Route::post('add-to-envelope/{transaction}', 'TransactionController@AddToEnvelope')->name('user.transactions.AddToEnvelope');
      Route::post('add-to-budgets/{transaction}', 'TransactionController@AddToBudgetTransacation')->name('user.transactions.AddToBudgetTransacation');
     
    });
  

    Route::group(['prefix' => 'user', 'namespace' => 'User\Stores', 'name' => 'user'], function () {
      Route::get('stores', 'StoresController@index')->name('user.stores.index');
      Route::post('/user-stores-datatable', 'StoresTableController')->name('user.stores.datatable');
    });
  
    Route::group(['prefix' => 'user/envelopes', 'namespace' => 'User\Envelopes', 'name' => 'user'], function () {
      Route::get('/', 'EnvelopeController@index')->name('user.envelopes.index');
      Route::post('/create', 'EnvelopeController@create')->name('user.envelopes.create');
      Route::post('/user-envelope-datatable', 'EnvelopeTableController')->name('user.envelope.datatable');
      Route::get('add-reciepts/{Envelope}', 'EnvelopeReceiptController@index')->name('user.envelope.add.reciepts');
      Route::post('add-receipt-data-table', 'EnvelopeAddReceiptTableController')->name('user.envelope.add.receipt.datatable');
      Route::post('add-receipts/session/addInvoice', 'EnvelopeReceiptController@addSession')->name('user.add.receipt.addSession');
      Route::post('add-receipts/session/removeInvoice', 'EnvelopeReceiptController@removeSession')->name('user.add.receipt.removeSession');
      Route::get('add-receipt', 'EnvelopeReceiptController@addReceipt')->name('user.add.receipt.add');
      Route::post('add-to-envelope', 'EnvelopeReceiptController@addToEnvelope')->name('user.add.receipt.addToEnvelope');
      Route::get('add-receipt/delete/{id}', 'EnvelopeReceiptController@deleteReceipt')->name('user.receipt.delete');
      Route::get('preview/{Envelope}', 'EnvelopeReceiptController@previewUserEnvelope')->name('user.preview.envelope');
      Route::get('delete/{Envelope}', 'EnvelopeController@deleteUserEnvelope')->name('user.delete.envelope');
      Route::post('add-receipt/session/bulk-receipts', 'EnvelopeController@bulkUserSession')->name('user.bulkSession.envelope');
      Route::post('add-receipt/session/clear-receipts', 'EnvelopeController@clearUserSession')->name('user.clearSession.envelope');
      Route::post('edit-envelope/{id}', 'EnvelopeController@editUserEnvelope')->name('user.envelope.edit');
      Route::get('items/delete/{envelope}/{id}', 'EnvelopeController@deleteEnvelopeItem')->name('user-delete-envelope-item');
    });

    
  
    Route::group(['prefix' => 'user/budget-manager', 'namespace' => 'User\Budget_Manager', 'name' => 'user'], function () {
      Route::get('/', 'BudgetManagerController@index')->name('user.budget.manager.index');
      Route::post('/create', 'BudgetManagerController@create')->name('user.budget.manager.create');
      Route::post('/user-budget-datatable', 'BudgetManagerTableController')->name('user.budget.datatable');
      Route::post('edit-user-budget/{id}', 'BudgetManagerController@editUserBudget')->name('user.budget.manager.edit');
      Route::get('delete/{id}', 'BudgetManagerController@deleteUserBudget')->name('user.budget.manager.delete');
      Route::get('add-reciepts/{Budget}', 'BudgetManagerController@budgetAddReceipt')->name('user.budget.add.reciepts');
      Route::post('add-receipts-datatable', 'BudgetAddReceiptTableController')->name('user.budget.add.receipt.datatable');
      Route::post('add-receipts/session/bulk-budgets', 'BudgetManagerController@bulkSession')->name('user.budget.add.receipt.bulkSession');
      Route::post('add-receipts/session/clear-session', 'BudgetManagerController@clearSession')->name('user.budget.add.receipt.clear.session');
      Route::post('add-receipts/session/add-budgets', 'BudgetManagerController@addSession')->name('user.budget.add.receipt.add.session');
      Route::post('add-receipts/session/remove-budgets', 'BudgetManagerController@removeSession')->name('user.budget.add.receipt.remove.session');
      Route::get('add-receipts-preview', 'BudgetManagerController@previewExistingBudget')->name('user.budget.add.receipt.preview');
      Route::post('add-receipt-to-budget', 'BudgetManagerController@addToBudget')->name('user.budget.add.receipt.store');
      Route::get('preview/{id}', 'BudgetManagerController@previewBudget')->name('user.budget.preview');
      Route::get('receipt/delete/{id}', 'BudgetManagerController@deleteBudgetTransaction')->name('user.budget.delete.receipt');
      Route::get('envelope/delete/{id}', 'BudgetManagerController@deleteBudgetEnvelope')->name('user.budget.delete.envelope');
      Route::get('add-receipts/delete-receipt/{id}', 'BudgetManagerController@deleteReceipt')->name('user.budget.add.receipt.delete');
  
      Route::get('add-envelopes/{Budget}', 'BudgetManagerController@budgetAddEnvelope')->name('user.budget.add.envelopes');
      Route::post('add-envelopes-datatable', 'BudgetAddEnvelopeTableController')->name('user.budget.add.envelope.datatable');
      Route::post('add-envelopes/session/bulk-budgets', 'BudgetManagerController@bulkEnvelopeSession')->name('user.budget.add.envelope.bulkSession');
      Route::post('add-envelopes/session/clear-session', 'BudgetManagerController@clearEnvelopeSession')->name('user.budget.add.envelope.clear.session');
      Route::post('add-envelopes/session/add-budgets', 'BudgetManagerController@addEnvelopeSession')->name('user.budget.add.envelope.add.session');
      Route::post('add-envelopes/session/remove-budgets', 'BudgetManagerController@removeEnvelopeSession')->name('user.budget.add.envelope.remove.session');
      Route::get('add-envelopes-preview', 'BudgetManagerController@previewExistingEnvelopeBudget')->name('user.budget.add.envelope.preview');
      Route::get('add-envelopes/delete-envelope/{id}', 'BudgetManagerController@deleteEnvelope')->name('user.budget.add.envelope.delete');
      Route::post('add-envelope-to-budget', 'BudgetManagerController@addEnvelopeToBudget')->name('user.budget.add.envelope.store');
    });
  
    Route::group(['prefix' => 'user/reports', 'namespace' => 'User\Reports', 'name' => 'user'], function () {
      Route::get('/', 'UserReportController@index')->name('user.reports.index');
      Route::get('purchase-by-vendor/{time}', 'UserReportController@purchaseByVendor')->name('user.reports.purchaseByVendor');
      Route::get('vendor-detail/{vendor}', 'UserReportController@showStore')->name('user.reports.show.store');
      Route::get('purchase-by-month/{time}', 'UserReportController@purchaseByMonth')->name('user.reports.purchasesByMonth');
      Route::get('my-envelopes/{time}', 'UserReportController@myEnvelopesReports')->name('user.reports.myEnvelopesReports');
      Route::get('my-budgets/{time}', 'UserReportController@myBudgetsReports')->name('user.reports.myBudgetsReports');
      Route::post('purchase-by-vendor-datatable', 'PurchasesByVendortableController')->name('user.reports.purchaseByVendor.datatable');
      Route::post('purchase-by-vendor-detail-datatable', 'VendorDetailTableController')->name('user.reports.vendor.detail.datatable');
      Route::post('vendor-add-to-favourites', 'UserReportController@addToFavourites')->name('user.reports.vendor.add.to.favourites');
      Route::post('my-envelopes-datatable', 'MyEnvelopesReportTableController')->name('user.reports.my.envelopes.datatable');
      Route::post('my-budget-datatable', 'MyBudgetReportTableController')->name('user.reports.my.budgets.datatable');
      Route::get('purchase-by-category/{time}', 'UserReportController@purchaseByCategory')->name('user.reports.purchaseByCategory');
      Route::post('purchase-by-category-datatable', 'PurchasesByCategoryTableController')->name('user.reports.purchaseByCategory.datatable');
    });
  
    Route::group(['prefix' => 'user/settings','namespace' => 'User', 'name' => 'admin'], function () {
      Route::get('/', 'UserSettingsController@index')->name('user.settings.index');
      Route::post('edit', 'UserSettingsController@edit')->name('user.settings.edit');
    });
  });
  
################### OTHERS ##################
Route::get('/run-cmd', function () {
    /*Artisan::call('migrate');
    Artisan::call('make:auth');
    Artisan::call('make:controller', ['name' => 'DemoController']);
    dd(Artisan::output());*/
	exec('composer dump-autoload');
	Artisan::call('config:clear');
	Artisan::call('cache:clear');
	echo('success');
});
