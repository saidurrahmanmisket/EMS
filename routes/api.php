<?php

use App\Http\Controllers\DealerOrShowroomController;
use App\Http\Controllers\VehicleController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\UserAuthController;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\BranchController;
use App\Http\Controllers\Authorization\AuthorizationController;
use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\DesignationController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\RentalInformationController;
use App\Http\Controllers\ManageBankController;
use App\Http\Controllers\ManageMerchantMobileBankingNameController;
use App\Http\Controllers\ManageMobileBankingOperatorController;
use App\Http\Controllers\DriverController;
use App\Http\Controllers\UnitController;
use App\Http\Controllers\ShopController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ExpenseCategoryController;
use App\Http\Controllers\ExpenseSectorController;
use App\Http\Controllers\ManagePurchaserController;
use App\Http\Controllers\VehicleSellerController;

use App\Http\Controllers\EmployeeExpenseCategoryController;
use App\Http\Controllers\EmployeeExpenseTypeController;
use App\Http\Controllers\ManageBillingTypeController;
use App\Http\Controllers\ManageMerchantMobileBankingNumberController;
use App\Http\Controllers\ManageUtilitySectorController;
use App\Http\Controllers\SubCategoryController;
use App\Http\Controllers\SubSubCategoryController;
use App\Http\Controllers\GiverController;
use App\Http\Controllers\ReceiverController;
use App\Http\Controllers\ClientCompanyController;
use App\Http\Controllers\ManageUtilityBillingSectorInformationController;
use App\Http\Controllers\PaymentClientGiverController;
use App\Http\Controllers\PaymentReceiverController;
use App\Http\Controllers\ReceiveMethodController;
use App\Http\Controllers\TransactionTypeController;
use App\Http\Controllers\PaymentMethodController;
use App\Http\Controllers\DeductionReasonController;
use App\Http\Controllers\ExpenseController;
use App\Http\Controllers\ExpenseRentalController;
use App\Http\Controllers\ExpenseUtilityController;
use App\Http\Controllers\BankAccountsController;

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
//Sudipta Route Start
//Public Routes for User Auth
Route::controller(UserAuthController::class)->group(function () {
    Route::prefix('user/auth')->group(function () {
        Route::post('login', 'login');
    });
});

// Protected Routes for User Auth
Route::middleware(['auth:user'])->group(function () {
    Route::controller(UserAuthController::class)->group(function () {
        Route::prefix('user/auth')->group(function () {
            Route::post('logout', 'logout');
        });
    });
});


//Routes for role permission
Route::controller(AuthorizationController::class)->group(function () {
    Route::prefix('authorization')->group(function () {
        Route::middleware(['auth:user'])->group(function () {
            Route::get('get_unique_module_list', 'get_unique_module_list');
            Route::post('create_role_and_create_permission', 'create_role_and_create_permission');
            Route::get('get_role_and_permission_list', 'get_role_and_permission_list');
            Route::post('delete_role', 'delete_role');
            Route::post('assign_role_to_user', 'assign_role_to_user');
            Route::get('get_permission_list_of_authenticate_user', 'get_permission_list_of_authenticate_user');
            Route::get('get_role_and_permission_list_for_edit', 'get_role_and_permission_list_for_edit');
            Route::post('update_role_and_update_permission', 'update_role_and_update_permission');
            Route::get('get_role_list', 'get_role_list');
            Route::get('get_users_with_roles', 'get_users_with_roles');
            Route::post('remove_role_from_user', 'remove_role_from_user');
            //User Defined Functions for Testing Purposes
            //Route::post('create_role_driver_and_assign_that_role_driver_to_the_created_driver', 'create_role_driver_and_assign_that_role_driver_to_the_created_driver');
        });
    });
});

//Sudipta Route End

//Jenia Route Start

Route::prefix('company')->group(function () {
    Route::get('/list', [CompanyController::class, 'list'])->name('list');
    Route::post('/add', [CompanyController::class, 'add'])->name('add');
    Route::get('/edit', [CompanyController::class, 'edit'])->name('edit');
    Route::post('/update', [CompanyController::class, 'update'])->name('update');
    Route::get('/getCompany', [CompanyController::class, 'getCompany'])->name('getCompany');
    Route::get('/getCompanyDocument', [CompanyController::class, 'getCompanyDocument']);
    Route::post('/addOthersDocument', [CompanyController::class, 'addOthersDocument']);
    Route::get('/deleteDocument', [CompanyController::class, 'deleteDocument']);
    Route::get('/deleteOthersDocument', [CompanyController::class, 'deleteOthersDocument']);
});
Route::prefix('branch')->group(function () {
    Route::get('/list', [BranchController::class, 'list'])->name('list');
    Route::post('/add', [BranchController::class, 'add'])->name('add');
    Route::get('/edit', [BranchController::class, 'edit'])->name('edit');
    Route::post('/update', [BranchController::class, 'update'])->name('update');
    Route::get('/getBranch', [BranchController::class, 'getBranch'])->name('getBranch');
});
Route::prefix('department')->group(function () {
    Route::get('/list', [DepartmentController::class, 'list'])->name('list');
    Route::post('/add', [DepartmentController::class, 'add'])->name('add');
    Route::get('/edit', [DepartmentController::class, 'edit'])->name('edit');
    Route::post('/update', [DepartmentController::class, 'update'])->name('update');
    Route::get('/getDepartment', [DepartmentController::class, 'getDepartment'])->name('getDepartment');
});
Route::prefix('designation')->group(function () {
    Route::get('/list', [DesignationController::class, 'list'])->name('list');
    Route::post('/add', [DesignationController::class, 'add'])->name('add');
    Route::post('/update', [DesignationController::class, 'update'])->name('update');
    Route::get('/edit', [DesignationController::class, 'edit'])->name('edit');
    Route::get('/getDesignation', [DesignationController::class, 'getDesignation'])->name('getDesignation');
});
Route::prefix('employee')->group(function () {
    Route::get('/list', [EmployeeController::class, 'list']);
    Route::post('/add', [EmployeeController::class, 'add']);
    Route::get('/edit', [EmployeeController::class, 'edit']);
    Route::post('/update', [EmployeeController::class, 'update']);
    Route::get('/getEmployeeListByBranch', [EmployeeController::class, 'getEmployeeListByBranch']);
    Route::get('/getEmployeeDetailsToAssignRole', [EmployeeController::class, 'getEmployeeDetailsToAssignRole']);
    Route::get('/getSingleEmployeeDetailsByUser', [EmployeeController::class, 'getSingleEmployeeDetailsByUser']);
    Route::get('/getEmployeeDocument', [EmployeeController::class, 'getEmployeeDocument']);
    Route::post('/addOthersDocument', [EmployeeController::class, 'addOthersDocument']);
    Route::get('/deleteOthersDocument', [EmployeeController::class, 'deleteOthersDocument']);
    Route::get('/deletePersonalDetailsDocument', [EmployeeController::class, 'deletePersonalDetailsDocument']);
    Route::get('/deleteEducationDocument', [EmployeeController::class, 'deleteEducationDocument']);
    Route::get('/deleteExperienceDocument', [EmployeeController::class, 'deleteExperienceDocument']);
    Route::get('/deleteTrainingDocument', [EmployeeController::class, 'deleteTrainingDocument']);
    Route::get('/deletePromotionDocument', [EmployeeController::class, 'deletePromotionDocument']);
});
Route::prefix('driver')->group(function () {
    Route::get('/list', [DriverController::class, 'list']);
    Route::post('/add', [DriverController::class, 'add']);
    Route::get('/edit', [DriverController::class, 'edit']);
    Route::post('/update', [DriverController::class, 'update']);
    Route::get('/getDriverDocument', [DriverController::class, 'getDriverDocument']);
    Route::post('/addOthersDocument', [DriverController::class, 'addOthersDocument']);
    Route::get('/deleteOthersDocument', [DriverController::class, 'deleteOthersDocument']);
    Route::get('/deletePersonalDetailsDocument', [DriverController::class, 'deletePersonalDetailsDocument']);
    Route::get('/deleteEducationDocument', [DriverController::class, 'deleteEducationDocument']);
    Route::get('/deleteExperienceDocument', [DriverController::class, 'deleteExperienceDocument']);
    Route::get('/deleteIncrementDocument', [DriverController::class, 'deleteIncrementDocument']);
});
Route::prefix('unit')->group(function () {
    Route::get('/list', [UnitController::class, 'list']);
    Route::post('/add', [UnitController::class, 'add']);
    Route::get('/edit', [UnitController::class, 'edit']);
    Route::post('/update', [UnitController::class, 'update']);
    Route::get('/getUnitList', [UnitController::class, 'getUnitList']);
});
Route::prefix('shop')->group(function () {
    Route::get('/list', [ShopController::class, 'list']);
    Route::post('/add', [ShopController::class, 'add']);
    Route::get('/edit', [ShopController::class, 'edit']);
    Route::post('/update', [ShopController::class, 'update']);
    Route::get('/getShopByCompany', [ShopController::class, 'getShopByCompany']);
});
Route::prefix('product')->group(function () {
    Route::get('/list', [ProductController::class, 'list']);
    Route::post('/add', [ProductController::class, 'add']);
    Route::get('/edit', [ProductController::class, 'edit']);
    Route::post('/update', [ProductController::class, 'update']);
    Route::get('/getProductByCompany', [ProductController::class, 'getProductByCompany']);
});
Route::prefix('category')->group(function () {
    Route::get('/list', [CategoryController::class, 'list']);
    Route::post('/add', [CategoryController::class, 'add']);
    Route::get('/edit', [CategoryController::class, 'edit']);
    Route::post('/update', [CategoryController::class, 'update']);
    Route::get('/getCategoryList', [CategoryController::class, 'getCategoryList']);
});
Route::prefix('sub_category')->group(function () {
    Route::get('/list', [SubCategoryController::class, 'list']);
    Route::post('/add', [SubCategoryController::class, 'add']);
    Route::get('/edit', [SubCategoryController::class, 'edit']);
    Route::post('/update', [SubCategoryController::class, 'update']);
    Route::get('/getSubCategoryListByCategory', [SubCategoryController::class, 'getSubCategoryListByCategory']);
});
Route::prefix('sub_sub_category')->group(function () {
    Route::get('/list', [SubSubCategoryController::class, 'list']);
    Route::post('/add', [SubSubCategoryController::class, 'add']);
    Route::get('/edit', [SubSubCategoryController::class, 'edit']);
    Route::post('/update', [SubSubCategoryController::class, 'update']);
});
Route::prefix('expense_category')->group(function () {
    Route::get('/list', [ExpenseCategoryController::class, 'list']);
    Route::post('/add', [ExpenseCategoryController::class, 'add']);
    Route::get('/edit', [ExpenseCategoryController::class, 'edit']);
    Route::post('/update', [ExpenseCategoryController::class, 'update']);
    Route::get('/getExpenseCategoryListByCompanyAndCategory', [ExpenseCategoryController::class, 'getExpenseCategoryListByCompanyAndCategory']);
    Route::get('/getExpenseCategoryListByCompany', [ExpenseCategoryController::class, 'getExpenseCategoryListByCompany']);
    Route::get('/getExpenseCategoryListByArray', [ExpenseCategoryController::class, 'getExpenseCategoryListByArray']);
});
Route::prefix('expense_sector')->group(function () {
    Route::get('/list', [ExpenseSectorController::class, 'list']);
    Route::post('/add', [ExpenseSectorController::class, 'add']);
    Route::get('/edit', [ExpenseSectorController::class, 'edit']);
    Route::post('/update', [ExpenseSectorController::class, 'update']);
    Route::get('/getExpenseSectorListByExpenseCategory', [ExpenseSectorController::class, 'getExpenseSectorListByExpenseCategory']);
    Route::get('/getExpenseSectorByArray', [ExpenseSectorController::class, 'getExpenseSectorByArray']);
});
Route::prefix('employee_expense_category')->group(function () {
    Route::get('/list', [EmployeeExpenseCategoryController::class, 'list']);
    Route::post('/add', [EmployeeExpenseCategoryController::class, 'add']);
    Route::get('/edit', [EmployeeExpenseCategoryController::class, 'edit']);
    Route::post('/update', [EmployeeExpenseCategoryController::class, 'update']);
    Route::get('/getEmployeeExpenseCategoryListByExpenseSector', [EmployeeExpenseCategoryController::class, 'getEmployeeExpenseCategoryListByExpenseSector']);
    Route::get('/getEmployeeExpenseCategoryListByArray', [EmployeeExpenseCategoryController::class, 'getEmployeeExpenseCategoryListByArray']);
});
Route::prefix('employee_expense_type')->group(function () {
    Route::get('/list', [EmployeeExpenseTypeController::class, 'list']);
    Route::post('/add', [EmployeeExpenseTypeController::class, 'add']);
    Route::get('/edit', [EmployeeExpenseTypeController::class, 'edit']);
    Route::post('/update', [EmployeeExpenseTypeController::class, 'update']);
    Route::get('/getEmployeeExpenseTypeListByArray', [EmployeeExpenseTypeController::class, 'getEmployeeExpenseTypeListByArray']);
});
Route::prefix('giver')->group(function () {
    Route::get('/list', [GiverController::class, 'list']);
    Route::post('/add', [GiverController::class, 'add']);
    Route::get('/edit', [GiverController::class, 'edit']);
    Route::post('/update', [GiverController::class, 'update']);
});
Route::prefix('receiver')->group(function () {
    Route::get('/list', [ReceiverController::class, 'list']);
    Route::post('/add', [ReceiverController::class, 'add']);
    Route::get('/edit', [ReceiverController::class, 'edit']);
    Route::post('/update', [ReceiverController::class, 'update']);
});
Route::prefix('client_company')->group(function () {
    Route::get('/list', [ClientCompanyController::class, 'list']);
    Route::post('/add', [ClientCompanyController::class, 'add']);
    Route::get('/edit', [ClientCompanyController::class, 'edit']);
    Route::post('/update', [ClientCompanyController::class, 'update']);
});
Route::prefix('payment_client_giver')->group(function () {
    Route::get('/list', [PaymentClientGiverController::class, 'list']);
    Route::post('/add', [PaymentClientGiverController::class, 'add']);
    Route::get('/edit', [PaymentClientGiverController::class, 'edit']);
    Route::post('/update', [PaymentClientGiverController::class, 'update']);
    Route::get('/payment-client-giver-list', [PaymentClientGiverController::class, 'getPaymentClientGiverList']);

});
Route::prefix('payment_receiver')->group(function () {
    Route::get('/list', [PaymentReceiverController::class, 'list']);
    Route::post('/add', [PaymentReceiverController::class, 'add']);
    Route::get('/edit', [PaymentReceiverController::class, 'edit']);
    Route::post('/update', [PaymentReceiverController::class, 'update']);
    Route::get('/payment-receiver-list', [PaymentReceiverController::class, 'paymentReceiverList']);
});
Route::prefix('receive_method')->group(function () {
    Route::get('/list', [ReceiveMethodController::class, 'list']);
    Route::post('/add', [ReceiveMethodController::class, 'add']);
    Route::get('/edit', [ReceiveMethodController::class, 'edit']);
    Route::post('/update', [ReceiveMethodController::class, 'update']);
    Route::get('/getReceiveMethodList', [ReceiveMethodController::class, 'getReceiveMethodList']);
});
Route::prefix('transaction_type')->group(function () {
    Route::get('/list', [TransactionTypeController::class, 'list']);
    Route::post('/add', [TransactionTypeController::class, 'add']);
    Route::get('/edit', [TransactionTypeController::class, 'edit']);
    Route::post('/update', [TransactionTypeController::class, 'update']);
});
Route::prefix('payment_method')->group(function () {
    Route::get('/list', [PaymentMethodController::class, 'list']);
    Route::post('/add', [PaymentMethodController::class, 'add']);
    Route::get('/edit', [PaymentMethodController::class, 'edit']);
    Route::post('/update', [PaymentMethodController::class, 'update']);
});
Route::prefix('deduction_reason')->group(function () {
    Route::get('/list', [DeductionReasonController::class, 'list']);
    Route::post('/add', [DeductionReasonController::class, 'add']);
    Route::get('/edit', [DeductionReasonController::class, 'edit']);
    Route::post('/update', [DeductionReasonController::class, 'update']);
    Route::get('/getDeductionReasonListByArray', [DeductionReasonController::class, 'getDeductionReasonListByArray']);
});

Route::middleware(['auth:user'])->group(function () {
    Route::prefix('expense')->group(function () {
        Route::post('/addExpense', [ExpenseController::class, 'addExpense']);
        Route::post('/totalAdvanceAmount', [ExpenseController::class, 'totalAdvanceAmount']);
        Route::get('/months_list', [ExpenseController::class, 'months_list']);
        Route::get('/getMonthlySalaryOfEmployee', [ExpenseController::class, 'getMonthlySalaryOfEmployee']);

        Route::post('/list', [ExpenseController::class, 'expenseList']);
        Route::post('/list/view', [ExpenseController::class, 'expenseListView']);
        //Expense Rental
        Route::post('/addRentalExpense', [ExpenseController::class, 'addRentalExpense']);
        Route::post('/totalAdvance', [ExpenseController::class, 'totalAdvance']);
        Route::get('/months_list', [ExpenseController::class, 'months']);
        //Expense Utility
        Route::post('/addExpenseUtility', [ExpenseController::class, 'addExpenseUtility']);

      Route::get('/allExpense', [ExpenseController::class, 'allExpenseList']);
    });
});

//Route::get('/index', [App\Http\Controllers\CompaniesController::class, 'index'])->name('index');

//Jenia Route End


//Sujon Route Start

Route::prefix('rentalInformation')->group(function () {
    Route::get('/list', [RentalInformationController::class, 'index']);
    Route::post('/add', [RentalInformationController::class, 'store']);
    Route::get('/edit', [RentalInformationController::class, 'edit']);
    Route::post('/update', [RentalInformationController::class, 'update']);
    Route::get('/getrentalspacename', [RentalInformationController::class, 'getRentalSpaceName']);

    Route::get('/rentalspacecitylist', [RentalInformationController::class, 'citylist']);
    Route::get('/rentalspacethanapoziplist', [RentalInformationController::class, 'rentalSpaceThanaPozip']);
    Route::get('/getRentalSpaceDocument', [RentalInformationController::class, 'getRentalSpaceDocument']);
    Route::post('/addOthersDocument', [RentalInformationController::class, 'addOthersDocument']);
    Route::get('/deleteOthersDocument', [RentalInformationController::class, 'deleteOthersDocument']);
    Route::get('/deleteRentalSpaceDocument', [RentalInformationController::class, 'deleteRentalSpaceDocument']);
    Route::get('/deleteOwnerDocument', [RentalInformationController::class, 'deleteOwnerDocument']);
    Route::get('/deleteTenantDocument', [RentalInformationController::class, 'deleteTenantDocument']);
    Route::get('/deleteFeeDocument', [RentalInformationController::class, 'deleteFeeDocument']);
    Route::get('/deleteParkingFeeDocument', [RentalInformationController::class, 'deleteParkingFeeDocument']);
});
Route::prefix('managepurchaser')->group(function () {
    Route::get('/list', [ManagePurchaserController::class, 'index']);
    Route::post('/add', [ManagePurchaserController::class, 'store']);
    Route::get('/edit', [ManagePurchaserController::class, 'edit']);
    Route::post('/update', [ManagePurchaserController::class, 'update']);
    Route::get('/getPurchaserByCompanyAndBranch', [ManagePurchaserController::class, 'getPurchaserByCompanyAndBranch']);
});
Route::prefix('managebank')->group(function () {
    Route::get('/list', [ManageBankController::class, 'index']);
    Route::post('/add', [ManageBankController::class, 'store']);
    Route::get('/edit', [ManageBankController::class, 'edit']);
    Route::get('/getBanks', [ManageBankController::class, 'getBanks']);
    Route::post('/update', [ManageBankController::class, 'update']);
});
Route::prefix('managemobilebankingoperator')->group(function () {
    Route::get('/list', [ManageMobileBankingOperatorController::class, 'index']);
    Route::get('/get-list', [ManageMobileBankingOperatorController::class, 'getList']);
    Route::post('/add', [ManageMobileBankingOperatorController::class, 'store']);
    Route::get('/edit', [ManageMobileBankingOperatorController::class, 'edit']);
    Route::post('/update', [ManageMobileBankingOperatorController::class, 'update']);
});
Route::prefix('managemerchantmobilebankingname')->group(function () {
    Route::get('/list', [ManageMerchantMobileBankingNameController::class, 'index']);
    Route::get('/get-list', [ManageMerchantMobileBankingNameController::class, 'getList']);
    Route::post('/add', [ManageMerchantMobileBankingNameController::class, 'store']);
    Route::get('/edit', [ManageMerchantMobileBankingNameController::class, 'edit']);
    Route::post('/update', [ManageMerchantMobileBankingNameController::class, 'update']);
});
Route::prefix('managemerchantmobilebankingnumber')->group(function () {
    Route::get('/list', [ManageMerchantMobileBankingNumberController::class, 'index']);
    Route::get('/get-list', [ManageMerchantMobileBankingNumberController::class, 'getList']);
    Route::post('/add', [ManageMerchantMobileBankingNumberController::class, 'store']);
    Route::get('/edit', [ManageMerchantMobileBankingNumberController::class, 'edit']);
    Route::post('/update', [ManageMerchantMobileBankingNumberController::class, 'update']);

    Route::get('/merchantList', [ManageMerchantMobileBankingNumberController::class, 'merchantlist']);
});
Route::prefix('manageutilitysector')->group(function () {
    Route::get('/list', [ManageUtilitySectorController::class, 'index']);
    Route::post('/add', [ManageUtilitySectorController::class, 'store']);
    Route::get('/edit', [ManageUtilitySectorController::class, 'edit']);
    Route::post('/update', [ManageUtilitySectorController::class, 'update']);
});
Route::prefix('managebillingtype')->group(function () {
    Route::get('/list', [ManageBillingTypeController::class, 'index']);
    Route::post('/add', [ManageBillingTypeController::class, 'store']);
    Route::get('/edit', [ManageBillingTypeController::class, 'edit']);
    Route::post('/update', [ManageBillingTypeController::class, 'update']);

    Route::get('/utilitysectorlist', [ManageBillingTypeController::class, 'utilitySectorList']);
});
Route::prefix('managebillingsectorinformation')->group(function () {
    Route::get('/list', [ManageUtilityBillingSectorInformationController::class, 'index']);
    Route::post('/add', [ManageUtilityBillingSectorInformationController::class, 'store']);
    Route::get('/edit', [ManageUtilityBillingSectorInformationController::class, 'edit']);
    Route::post('/update', [ManageUtilityBillingSectorInformationController::class, 'update']);

    Route::get('/utilitybillingtypelist', [ManageUtilityBillingSectorInformationController::class, 'utilityBillingTypeList']);
    Route::post('/getExpenseUtility', [ManageUtilityBillingSectorInformationController::class, 'getExpenseUtility']);

});


Route::prefix('vehicle')->group(function () {
    Route::post('/add_vehicle', [VehicleController::class, 'add_vehicle']);
    Route::get('/vehicle_single_view_or_edit', [VehicleController::class, 'vehicle_single_view_or_edit']);
    Route::get('/vehicle_list', [VehicleController::class, 'vehicle_list']);
    Route::post('/vehicle_update', [VehicleController::class, 'vehicle_update']);
});


Route::prefix('vehicle_seller')->group(function () {
    Route::post('/add', [VehicleSellerController::class, 'store']);
    Route::get('/list', [VehicleSellerController::class, 'list']);
    Route::get('/edit/or/view', [VehicleSellerController::class, 'edit_or_view']);
    Route::post('/update', [VehicleSellerController::class, 'update']);
    Route::get('/get_previous_owner_or_seller_name_list_for_select', [VehicleSellerController::class, 'get_previous_owner_or_seller_name_list_for_select']);
});

Route::prefix('dealer_or_showroom')->group(function () {
    Route::post('/add', [DealerOrShowroomController::class, 'store']);
    Route::get('/list', [DealerOrShowroomController::class, 'list']);
    Route::get('/edit/or/view', [DealerOrShowroomController::class, 'edit_or_view']);
    Route::post('/update', [DealerOrShowroomController::class, 'update']);
    Route::get('/get_dealer_or_showroom_list_for_select', [DealerOrShowroomController::class, 'get_dealer_or_showroom_list_for_select']);
});

Route::prefix('payment')->group(function () {
    Route::get('/', [\App\Http\Controllers\PaymentController::class, 'index']);
    Route::get('create/{expense_sector}/{expense_id}', [\App\Http\Controllers\PaymentController::class, 'create']);
    Route::post('store', [\App\Http\Controllers\PaymentController::class, 'store']);
    Route::post('list', [\App\Http\Controllers\PaymentController::class, 'list']);
});

Route::prefix('bank_accounts')->group(function () {
    Route::get('/list', [BankAccountsController::class, 'index']);
    Route::post('/create', [BankAccountsController::class, 'create']);
    Route::get('/edit', [BankAccountsController::class, 'edit']);
    Route::post('/update', [BankAccountsController::class, 'update']);
    Route::post('/destroy', [BankAccountsController::class, 'destroy'])->middleware('auth:user');
});
