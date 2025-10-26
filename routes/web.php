<?php

use App\Http\Controllers\AccountController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\AwardController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\DesignationController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\EmployeeDocumentController;
use App\Http\Controllers\EmployeeExpenseController;
use App\Http\Controllers\Employee\AwardController as EmployeeAwardController;
use App\Http\Controllers\Employee\EmployeeExpenseController as ExpenseController;
use App\Http\Controllers\Employee\LeaveController as EmployeeLeaveController;
use App\Http\Controllers\Employee\LoanController as EmployeeLoanController;
use App\Http\Controllers\Employee\PayrollController as EmployeePayrollController;
use App\Http\Controllers\Employee\ReportController as EmployeeReportController;
use App\Http\Controllers\ExpenseCategoryController;
use App\Http\Controllers\HolidayController;
use App\Http\Controllers\LanguageController;
use App\Http\Controllers\LeaveController;
use App\Http\Controllers\LeaveTypeController;
use App\Http\Controllers\LoanController;
use App\Http\Controllers\LoanTypeController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\NoticeController;
use App\Http\Controllers\NotificationTemplateController;
use App\Http\Controllers\PayrollController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\UnitController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\UtilityController;
use App\Http\Controllers\WorkingHourController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
 */

$ev = env('APP_INSTALLED', true) == true ? get_option('email_verification', 0) : 0;


    Route::get('/', function () {
        return redirect('login');
    });

    Auth::routes(['verify' => $ev == 1 ? true : false]);
    Route::get('/logout', 'Auth\LoginController@logout');

    $initialMiddleware = ['auth'];
    if ($ev == 1) {
        array_push($initialMiddleware, 'verified');
    }

    //  Route::get('units/create', [UnitController::class, 'index'])->name('units.create');
    //  Route::post('units', [UnitController::class, 'store'])->name('units.store');
    //  Route::resource('units', UnitController::class);

    Route::group(['middleware' => $initialMiddleware], function () {

        Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard.index');

        //Profile Controller
        Route::get('profile', [ProfileController::class, 'index'])->name('profile.index');
        Route::get('profile/edit', [ProfileController::class, 'edit'])->name('profile.edit');
        Route::post('profile/update', [ProfileController::class, 'update'])->name('profile.update');
        Route::get('profile/change_password', [ProfileController::class, 'change_password'])->name('profile.change_password');
        Route::post('profile/update_password', [ProfileController::class, 'update_password'])->name('profile.update_password');
        Route::get('profile/notification_mark_as_read/{id}', [ProfileController::class, 'notification_mark_as_read'])->name('profile.notification_mark_as_read');
        Route::get('profile/show_notification/{id}', [ProfileController::class, 'show_notification'])->name('profile.show_notification');

        //Message Controllers
        Route::get('/messages/compose', [MessageController::class, 'compose'])->name('messages.compose');
        Route::post('/messages/send', [MessageController::class, 'send'])->name('messages.send');
        Route::get('/messages/inbox', [MessageController::class, 'inbox'])->name('messages.inbox');
        Route::get('/messages/sent', [MessageController::class, 'sentItems'])->name('messages.sent');
        Route::get('/messages/{id}', [MessageController::class, 'show'])->name('messages.show');
        Route::get('/messages/reply/{id}', [MessageController::class, 'reply'])->name('messages.reply');
        Route::post('/messages/reply/{id}', [MessageController::class, 'sendReply'])->name('messages.sendReply');
        Route::get('/messages/{id}/download_attachment', [MessageController::class, 'download_attachment'])->name('messages.download_attachment');

        //Get Loan Type By ID
        Route::get('employee_loan_types/{id}/get_loan_type', [LoanTypeController::class, 'get_loan_type']);

        /** Admin Only Route **/
        Route::group(['middleware' => ['admin'], 'prefix' => 'admin'], function () {

            //User Management
            Route::get('users/get_table_data', [UserController::class, 'get_table_data']);
            Route::resource('users', UserController::class);

            //User Roles
            Route::resource('roles', 'RoleController');

            //Permission Controller
            Route::get('permission/access_control', 'PermissionController@index')->name('permission.index');
            Route::get('permission/access_control/{user_id?}', 'PermissionController@show')->name('permission.show');
            Route::post('permission/store', 'PermissionController@store')->name('permission.store');

            //Language Controller
            Route::resource('languages', LanguageController::class);

            //Utility Controller
            Route::match(['get', 'post'], 'administration/general_settings/{store?}', [UtilityController::class, 'settings'])->name('settings.update_settings');
            Route::post('administration/upload_logo', [UtilityController::class, 'upload_logo'])->name('settings.uplaod_logo');
            Route::get('administration/database_backup_list', [UtilityController::class, 'database_backup_list'])->name('database_backups.list');
            Route::get('administration/create_database_backup', [UtilityController::class, 'create_database_backup'])->name('database_backups.create');
            Route::delete('administration/destroy_database_backup/{id}', [UtilityController::class, 'destroy_database_backup'])->name('database_backups.destroy');
            Route::get('administration/download_database_backup/{id}', [UtilityController::class, 'download_database_backup'])->name('database_backups.download');
            Route::post('administration/remove_cache', [UtilityController::class, 'remove_cache'])->name('settings.remove_cache');
            Route::post('administration/send_test_email', [UtilityController::class, 'send_test_email'])->name('settings.send_test_email');

            //Notification Template
            Route::resource('notification_templates', NotificationTemplateController::class)->only([
                'index', 'edit', 'update',
            ]);

        });

        /** Dynamic Permission **/
        Route::group(['middleware' => ['permission'], 'prefix' => 'admin'], function () {
            //Dashboard Widget
            Route::get('dashboard/active_employee_widget', 'DashboardController@dashboard_widget')->name('dashboard.active_employee_widget');
            Route::get('dashboard/leave_application_widget', 'DashboardController@dashboard_widget')->name('dashboard.leave_application_widget');
            Route::get('dashboard/loan_application_widget', 'DashboardController@dashboard_widget')->name('dashboard.loan_application_widget');
            Route::get('dashboard/expense_requests_widget', 'DashboardController@dashboard_widget')->name('dashboard.expense_requests_widget');
            Route::get('dashboard/profit_and_loss_widget', 'DashboardController@dashboard_widget')->name('dashboard.profit_and_loss_widget');
            Route::get('dashboard/recent_transactions_widget', 'DashboardController@dashboard_widget')->name('dashboard.recent_transactions_widget');

            //Department Controller
            Route::resource('departments', DepartmentController::class);
            Route::get('designations/get_designations/{deaprtment_id}', [DesignationController::class, 'get_designations']);
            Route::resource('designations', DesignationController::class)->except('show');

            //Unit Controller
            // Route::get('units/create', [UnitController::class, 'index']);
            Route::resource('units', UnitController::class);

            //Staff Controller
            Route::match(['get', 'post'], 'employees/{id}/login_access', [EmployeeController::class, 'login_access'])->name('employees.login_access');
            Route::get('employees/get_table_data', [EmployeeController::class, 'get_table_data'])->name('employees.get_table_data');
            Route::resource('employees', EmployeeController::class);

            //Staff Documents
            Route::get('employee_documents/{employee_id}', [EmployeeDocumentController::class, 'index'])->name('employee_documents.index');
            Route::get('employee_documents/create/{employee_id}', [EmployeeDocumentController::class, 'create'])->name('employee_documents.create');
            Route::resource('employee_documents', EmployeeDocumentController::class)->except(['index', 'create', 'show']);

            //Holiday Controller
            Route::get('holidays/get_table_data', [HolidayController::class, 'get_table_data']);
            Route::match(['get', 'post'], 'holidays/weekends', [HolidayController::class, 'weekends'])->name('holidays.weekends');
            Route::resource('holidays', HolidayController::class)->except('show');

            //Leave Application
            Route::resource('leave_types', LeaveTypeController::class)->except('show');
            Route::get('leaves/{id}/reject', [LeaveController::class, 'reject'])->name('leaves.reject');
            Route::get('leaves/{id}/approve', [LeaveController::class, 'approve'])->name('leaves.approve');
            Route::get('leaves/get_table_data', [LeaveController::class, 'get_table_data']);
            Route::resource('leaves', LeaveController::class);

            //Working Hours
            Route::get('working_hours/get_table_data', [WorkingHourController::class, 'get_table_data']);
            Route::resource('working_hours', WorkingHourController::class)->except('show', 'destroy');

            //Attendance Controller
            Route::get('attendance/get_table_data', [AttendanceController::class, 'get_table_data']);
            Route::post('attendance/create', [AttendanceController::class, 'create'])->name('attendance.create');
            Route::resource('attendance', AttendanceController::class)->except('show', 'edit', 'update', 'destroy');

            //Expense Category
            Route::resource('employee_expense_categories', ExpenseCategoryController::class)->except('show');

            //Employee Expense Controller
            Route::get('employee_expenses/get_table_data', [EmployeeExpenseController::class, 'get_table_data']);
            Route::resource('employee_expenses', EmployeeExpenseController::class);

            //Award Controller
            Route::get('awards/get_table_data', [AwardController::class, 'get_table_data']);
            Route::resource('awards', AwardController::class);

            //Payslip Controller
            Route::post('payslips/store_payment', [PayrollController::class, 'store_payment'])->name('payslips.store_payment');
            Route::match(['get', 'post'], 'payslips/make_payment', [PayrollController::class, 'make_payment'])->name('payslips.make_payment');
            Route::get('payslips/get_table_data', [PayrollController::class, 'get_table_data']);
            Route::resource('payslips', PayrollController::class);

            //Chart Of Accounts
            Route::resource('accounts', AccountController::class)->except('show');

            //Transaction Controller
            Route::get('transactions/get_table_data', [TransactionController::class, 'get_table_data']);
            Route::match(['get', 'post'], 'transactions/add_income', [TransactionController::class, 'add_income'])->name('transactions.add_income');
            Route::match(['get', 'post'], 'transactions/add_expense', [TransactionController::class, 'add_expense'])->name('transactions.add_expense');
            Route::resource('transactions', TransactionController::class);

            //Loan Type Controller
            Route::resource('employee_loan_types', LoanTypeController::class);

            //Loan Controller
            Route::get('employee_loans/get_repayment_table_data', [LoanController::class, 'get_repayment_table_data']);
            Route::get('employee_loans/repayments', [LoanController::class, 'repayments'])->name('employee_loans.repayments');
            Route::get('employee_loans/{id}/reject', [LoanController::class, 'reject'])->name('employee_loans.reject');
            Route::match(['get', 'post'], 'employee_loans/{id}/approve', [LoanController::class, 'approve'])->name('employee_loans.approve');
            Route::get('employee_loans/get_table_data/{status?}', [LoanController::class, 'get_table_data']);
            Route::resource('employee_loans', LoanController::class);

            //Notice Controller
            Route::get('notices/get_table_data', [NoticeController::class, 'get_table_data']);
            Route::resource('notices', NoticeController::class);

            //Report Controller
            Route::match(['get', 'post'], 'reports/attendance_report', [ReportController::class, 'attendance_report'])->name('reports.attendance_report');
            Route::match(['get', 'post'], 'reports/payroll_report', [ReportController::class, 'payroll_report'])->name('reports.payroll_report');
            Route::get('/reports/general-ledger', [ReportController::class, 'generalLedger'])->name('reports.generalLedger');
            Route::get('/reports/trial-balance', [ReportController::class, 'trialBalance'])->name('reports.trialBalance');
            Route::get('/reports/profit-and-loss', [ReportController::class, 'profitAndLoss'])->name('reports.profitAndLoss');
            Route::get('/reports/balance-sheet', [ReportController::class, 'balanceSheet'])->name('reports.balanceSheet');

        });

        /** Employee Only Route **/
        Route::group(['middleware' => ['employee'], 'prefix' => 'employee'], function () {
            //Profile Details
            Route::get('profile/job_profile', [ProfileController::class, 'job_profile'])->name('profile.job_profile');

            //View Notice
            Route::get('notices/{id}/details', [DashboardController::class, 'notice_details'])->name('notices.details');


            //Payslip Application
            Route::get('my_payslips/get_table_data', [EmployeePayrollController::class, 'get_table_data']);
            Route::resource('my_payslips', EmployeePayrollController::class)->only('index', 'show');

            //Leave Application
            Route::get('my_leaves/get_table_data', [EmployeeLeaveController::class, 'get_table_data']);
            Route::resource('my_leaves', EmployeeLeaveController::class)->except('edit', 'update', 'destroy');

            //Employee Expense Controller
            Route::get('my_expenses/get_table_data', [ExpenseController::class, 'get_table_data']);
            Route::resource('my_expenses', ExpenseController::class)->except('edit', 'update', 'destroy');

            //Loan Controller
            Route::get('my_loans/get_repayment_table_data', [EmployeeLoanController::class, 'get_repayment_table_data']);
            Route::get('my_loans/repayments', [EmployeeLoanController::class, 'repayments'])->name('my_loans.repayments');
            Route::get('my_loans/get_table_data/{status?}', [EmployeeLoanController::class, 'get_table_data']);
            Route::resource('my_loans', EmployeeLoanController::class)->except('edit', 'update', 'destroy');

            //Award Controller
            Route::get('my_awards/get_table_data', [EmployeeAwardController::class, 'get_table_data']);
            Route::resource('my_awards', EmployeeAwardController::class)->only('index', 'show');

            //Report Controller
            Route::get('my_reports/attendance_report', [EmployeeReportController::class, 'attendance_report'])->name('my_reports.attendance_report');
            Route::get('my_reports/work_hour_report', [EmployeeReportController::class, 'work_hour_report'])->name('my_reports.work_hour_report');
        });

        //Ajax Select2 Controller
        Route::get('ajax/get_table_data', 'Select2Controller@get_table_data');

    });

    Route::get('switch_language/', function () {
        if (isset($_GET['language'])) {
            session(['language' => $_GET['language']]);
            return back();
        }
    })->name('switch_language');


Route::get('dashboard/json_profit_and_loss', 'DashboardController@json_profit_and_loss')->middleware('auth');

//Social Login
Route::get('/login/{provider}', 'Auth\SocialController@redirect');
Route::get('/login/{provider}/callback', 'Auth\SocialController@callback');

Route::get('/installation', 'Install\InstallController@index');
Route::get('install/database', 'Install\InstallController@database');
Route::post('install/process_install', 'Install\InstallController@process_install');
Route::get('install/create_user', 'Install\InstallController@create_user');
Route::post('install/store_user', 'Install\InstallController@store_user');
Route::get('install/system_settings', 'Install\InstallController@system_settings');
Route::post('install/finish', 'Install\InstallController@final_touch');

//Update System
Route::get('system/update/{action?}', 'Install\UpdateController@index');
Route::get('migration/update', 'Install\UpdateController@update_migration');
