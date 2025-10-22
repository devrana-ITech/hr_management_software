@php
$permissions = permission_list();
$inbox = request_count('messages');
$pending_expenses = request_count('pending_expenses');
$leave_application = request_count('leave_application');
$loan_application = request_count('loan_application');
@endphp

<li><a href="{{ route('dashboard.index') }}"><i class="fas fa-th-large"></i><span>{{ _lang('Dashboard') }}</span></a></li>

<li>
	<a href="javascript: void(0);"><i class="fas fa-user-friends"></i><span>{{ _lang('Employees') }}</span><span class="menu-arrow"><i class="mdi mdi-chevron-right"></i></span></a>
	<ul class="nav-second-level" aria-expanded="false">
		@if(in_array('employees.index', $permissions))
		<li class="nav-item"><a class="nav-link" href="{{ route('employees.index') }}">{{ _lang('Manage Employees') }}</a></li>
		@endif

		@if(in_array('departments.index', $permissions))
		<li class="nav-item"><a class="nav-link" href="{{ route('departments.index') }}">{{ _lang('Departments') }}</a></li>
		@endif

		@if(in_array('designations.index', $permissions))
		<li class="nav-item"><a class="nav-link" href="{{ route('designations.index') }}">{{ _lang('Designations') }}</a></li>
		@endif

		@if(in_array('employees.create', $permissions))
		<li class="nav-item"><a class="nav-link" href="{{ route('employees.create') }}">{{ _lang('Add Employee') }}</a></li>
		@endif
	</ul>
</li>

<li>
	<a href="javascript: void(0);"><i class="far fa-clock"></i><span>{{ _lang('Manage Work Hours') }}</span><span class="menu-arrow"><i class="mdi mdi-chevron-right"></i></span></a>
	<ul class="nav-second-level" aria-expanded="false">
		@if(in_array('working_hours.create', $permissions))
		<li class="nav-item"><a class="nav-link" href="{{ route('working_hours.create') }}">{{ _lang('Manage Work Hours') }}</a></li>
		@endif

		@if(in_array('working_hours.index', $permissions))
		<li class="nav-item"><a class="nav-link" href="{{ route('working_hours.index') }}">{{ _lang('Work Hours History') }}</a></li>
		@endif
	</ul>
</li>

<li>
	<a href="javascript: void(0);"><i class="fas fa-user-clock"></i><span>{{ _lang('Attendance') }}</span><span class="menu-arrow"><i class="mdi mdi-chevron-right"></i></span></a>
	<ul class="nav-second-level" aria-expanded="false">
		@if(in_array('attendance.create', $permissions))
		<li class="nav-item"><a class="nav-link" href="{{ route('attendance.create') }}">{{ _lang('Manage Attendance') }}</a></li>
		@endif

		@if(in_array('attendance.index', $permissions))
		<li class="nav-item"><a class="nav-link" href="{{ route('attendance.index') }}">{{ _lang('Attendance History') }}</a></li>
		@endif
	</ul>
</li>

<li>
	<a href="javascript: void(0);"><i class="fas fa-money-check-alt"></i><span>{{ _lang('Payslips') }}</span><span class="menu-arrow"><i class="mdi mdi-chevron-right"></i></span></a>
	<ul class="nav-second-level" aria-expanded="false">
		@if(in_array('payslips.index', $permissions))
		<li class="nav-item"><a class="nav-link" href="{{ route('payslips.index') }}">{{ _lang('Manage Payslip') }}</a></li>
		@endif

		@if(in_array('payslips.make_payment', $permissions))
		<li class="nav-item"><a class="nav-link" href="{{ route('payslips.make_payment') }}">{{ _lang('Make Payment') }}</a></li>
		@endif
	</ul>
</li>

<li>
	<a href="javascript: void(0);"><i class="fas fa-minus-circle"></i><span>{{ _lang('Employee Expenses') }}</span> {!! $pending_expenses > 0 ? xss_clean('<div class="circle-animation"></div>') : '' !!}<span class="menu-arrow"><i class="mdi mdi-chevron-right"></i></span></a>
	<ul class="nav-second-level" aria-expanded="false">
		@if(in_array('employee_expenses.index', $permissions))
		<li class="nav-item"><a class="nav-link" href="{{ route('employee_expenses.index') }}">{{ _lang('Expenses') }}</a></li>
		@endif

		@if(in_array('employee_expense_categories.index', $permissions))
		<li class="nav-item"><a class="nav-link" href="{{ route('employee_expense_categories.index') }}">{{ _lang('Categories') }}</a></li>
		@endif
	</ul>
</li>

@if(in_array('holidays.index', $permissions))
<li>
	<a href="{{ route('holidays.index') }}"><i class="fas fa-snowman"></i><span>{{ _lang('Holiday Management') }}</span></a>
</li>
@endif

<li>
	<a href="javascript: void(0);"><i class="fas fa-calendar-alt"></i><span>{{ _lang('Leave Management') }}</span> {!! $leave_application > 0 ? xss_clean('<div class="circle-animation"></div>') : '' !!}<span class="menu-arrow"><i class="mdi mdi-chevron-right"></i></span></a>
	<ul class="nav-second-level" aria-expanded="false">
		@if(in_array('leaves.index', $permissions))
		<li class="nav-item"><a class="nav-link" href="{{ route('leaves.index') }}">{{ _lang('Leave Applications') }}</a></li>
		@endif

		@if(in_array('leave_types.index', $permissions))
		<li class="nav-item"><a class="nav-link" href="{{ route('leave_types.index') }}">{{ _lang('Leave Types') }}</a></li>
		@endif
	</ul>
</li>

<li>
	<a href="javascript: void(0);"><i class="fas fa-coins"></i><span>{{ _lang('Loan Management') }}</span> {!! $loan_application > 0 ? xss_clean('<div class="circle-animation"></div>') : '' !!}<span class="menu-arrow"><i class="mdi mdi-chevron-right"></i></span></a>
	<ul class="nav-second-level" aria-expanded="false">
		@if(in_array('employee_loans.index', $permissions))
		<li class="nav-item"><a class="nav-link" href="{{ route('employee_loans.index') }}?status=pending">{{ _lang('Pending Loans') }}</a></li>
		<li class="nav-item"><a class="nav-link" href="{{ route('employee_loans.index') }}">{{ _lang('Manage Loans') }}</a></li>
		@endif

		@if(in_array('employee_loans.repayments', $permissions))
		<li class="nav-item"><a class="nav-link" href="{{ route('employee_loans.repayments') }}">{{ _lang('Repayments') }}</a></li>
		@endif

		@if(in_array('employee_loan_types.index', $permissions))
		<li class="nav-item"><a class="nav-link" href="{{ route('employee_loan_types.index') }}">{{ _lang('Loan Types') }}</a></li>
		@endif
	</ul>
</li>

<li>
	<a href="javascript: void(0);"><i class="fas fa-landmark"></i><span>{{ _lang('Accounting') }}</span><span class="menu-arrow"><i class="mdi mdi-chevron-right"></i></span></a>
	<ul class="nav-second-level" aria-expanded="false">
		@if(in_array('accounts.index', $permissions))
		<li class="nav-item"><a class="nav-link" href="{{ route('accounts.index') }}">{{ _lang('Chart Of Accounts') }}</a></li>
		@endif

		@if(in_array('transactions.index', $permissions))
		<li class="nav-item"><a class="nav-link" href="{{ route('transactions.index') }}">{{ _lang('Transactions') }}</a></li>
		@endif

		@if(in_array('transactions.create', $permissions))
		<li class="nav-item"><a class="nav-link" href="{{ route('transactions.create') }}">{{ _lang('Journal Entry') }}</a></li>
		@endif

		@if(in_array('transactions.add_income', $permissions))
		<li class="nav-item"><a class="nav-link" href="{{ route('transactions.add_income') }}">{{ _lang('New Income') }}</a></li>
		@endif

		@if(in_array('transactions.add_expense', $permissions))
		<li class="nav-item"><a class="nav-link" href="{{ route('transactions.add_expense') }}">{{ _lang('New Expense') }}</a></li>
		@endif
	</ul>
</li>

@if(in_array('awards.index', $permissions))
<li>
	<a href="{{ route('awards.index') }}"><i class="fas fa-award"></i><span>{{ _lang('Awards') }}</span></a>
</li>
@endif

<li>
	<a href="javascript: void(0);"><i class="far fa-bell"></i><span>{{ _lang('Notices') }}</span><span class="menu-arrow"><i class="mdi mdi-chevron-right"></i></span></a>
	<ul class="nav-second-level" aria-expanded="false">
		@if(in_array('notices.create', $permissions))
		<li class="nav-item"><a class="nav-link" href="{{ route('notices.create') }}">{{ _lang('Post Notice') }}</a></li>
		@endif

		@if(in_array('notices.index', $permissions))
		<li class="nav-item"><a class="nav-link" href="{{ route('notices.index') }}">{{ _lang('Notice List') }}</a></li>
		@endif
	</ul>
</li>

<li>
	<a href="javascript: void(0);"><i class="fas fa-envelope"></i><span>{{ _lang('Messages') }}</span> {!! $inbox > 0 ? xss_clean('<div class="circle-animation"></div>') : '' !!}<span class="menu-arrow"><i class="mdi mdi-chevron-right"></i></span></a>
	<ul class="nav-second-level" aria-expanded="false">
		<li class="nav-item"><a class="nav-link" href="{{ route('messages.compose') }}">{{ _lang('New Message') }}</a></li>
		<li class="nav-item"><a class="nav-link" href="{{ route('messages.inbox') }}">{{ _lang('Inbox Items') }}</a></li>
		<li class="nav-item"><a class="nav-link" href="{{ route('messages.sent') }}">{{ _lang('Sent Items') }}</a></li>
	</ul>
</li>

<li>
	<a href="javascript: void(0);"><i class="far fa-chart-bar"></i><span>{{ _lang('Reports') }}</span><span class="menu-arrow"><i class="mdi mdi-chevron-right"></i></span></a>
	<ul class="nav-second-level" aria-expanded="false">
		@if(in_array('reports.attendance_report', $permissions))
		<li class="nav-item"><a class="nav-link" href="{{ route('reports.attendance_report') }}">{{ _lang('Attendance Report') }}</a></li>
		@endif

		@if(in_array('reports.payroll_report', $permissions))
		<li class="nav-item"><a class="nav-link" href="{{ route('reports.payroll_report') }}">{{ _lang('Payroll Report') }}</a></li>
		@endif

		@if(in_array('reports.trialBalance', $permissions))
		<li class="nav-item"><a class="nav-link" href="{{ route('reports.trialBalance') }}">{{ _lang('Trial Balance') }}</a></li>
		@endif

		@if(in_array('reports.generalLedger', $permissions))
		<li class="nav-item"><a class="nav-link" href="{{ route('reports.generalLedger') }}">{{ _lang('General Ledger') }}</a></li>
		@endif

		@if(in_array('reports.profitAndLoss', $permissions))
		<li class="nav-item"><a class="nav-link" href="{{ route('reports.profitAndLoss') }}">{{ _lang('Profit & Loss') }}</a></li>
		@endif

		@if(in_array('reports.balanceSheet', $permissions))
		<li class="nav-item"><a class="nav-link" href="{{ route('reports.balanceSheet') }}">{{ _lang('Balance Sheet') }}</a></li>
		@endif
	</ul>
</li>