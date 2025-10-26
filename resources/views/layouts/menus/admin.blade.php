@php
$inbox = request_count('messages');
$pending_expenses = request_count('pending_expenses');
$leave_application = request_count('leave_application');
$loan_application = request_count('loan_application');
@endphp

<li>
	<a href="{{ route('dashboard.index') }}"><i class="fas fa-th-large"></i><span>{{ _lang('Dashboard') }}</span></a>
</li>

{{-- <li>
	<a href="javascript: void(0);"><i class="fas fa-user-friends"></i><span>{{ _lang('Employees') }}</span><span class="menu-arrow"><i class="mdi mdi-chevron-right"></i></span></a>
	<ul class="nav-second-level" aria-expanded="false">
		<li class="nav-item"><a class="nav-link" href="{{ route('employees.index') }}">{{ _lang('Manage Employees') }}</a></li>
		<li class="nav-item"><a class="nav-link" href="{{ route('departments.index') }}">{{ _lang('Departments') }}</a></li>
		<li class="nav-item"><a class="nav-link" href="{{ route('designations.index') }}">{{ _lang('Designations') }}</a></li>
		<li class="nav-item"><a class="nav-link" href="{{ route('employees.create') }}">{{ _lang('Add Employee') }}</a></li>
	</ul>
</li> --}}
<li>

		<li class="nav-item"><a class="nav-link" href="{{ route('employees.index') }}">{{ _lang('Manage Employees') }}</a></li>
		<li class="nav-item"><a class="nav-link" href="{{ route('departments.index') }}">{{ _lang('Departments') }}</a></li>
		<li class="nav-item"><a class="nav-link" href="{{ route('designations.index') }}">{{ _lang('Designations') }}</a></li>
		<li class="nav-item"><a class="nav-link" href="{{ route('employees.create') }}">{{ _lang('Add Employee') }}</a></li>
		<li class="nav-item"><a class="nav-link" href="{{ route('units.index') }}">{{ _lang('Units') }}</a></li>
		<li class="nav-item"><a class="nav-link" href="avascript: void(0);">{{ _lang('Employees Report') }}</a></li>

</li>

{{-- <li>
	<a href="javascript: void(0);"><i class="far fa-clock"></i><span>{{ _lang('Manage Work Hours') }}</span><span class="menu-arrow"><i class="mdi mdi-chevron-right"></i></span></a>
	<ul class="nav-second-level" aria-expanded="false">
		<li class="nav-item"><a class="nav-link" href="{{ route('working_hours.create') }}">{{ _lang('Manage Work Hours') }}</a></li>
		<li class="nav-item"><a class="nav-link" href="{{ route('working_hours.index') }}">{{ _lang('Work Hours History') }}</a></li>
	</ul>
</li>

<li>
	<a href="javascript: void(0);"><i class="fas fa-user-clock"></i><span>{{ _lang('Attendance') }}</span><span class="menu-arrow"><i class="mdi mdi-chevron-right"></i></span></a>
	<ul class="nav-second-level" aria-expanded="false">
		<li class="nav-item"><a class="nav-link" href="{{ route('attendance.create') }}">{{ _lang('Manage Attendance') }}</a></li>
		<li class="nav-item"><a class="nav-link" href="{{ route('attendance.index') }}">{{ _lang('Attendance History') }}</a></li>
	</ul>
</li>

<li>
	<a href="javascript: void(0);"><i class="fas fa-money-check-alt"></i><span>{{ _lang('Payslips') }}</span><span class="menu-arrow"><i class="mdi mdi-chevron-right"></i></span></a>
	<ul class="nav-second-level" aria-expanded="false">
		<li class="nav-item"><a class="nav-link" href="{{ route('payslips.index') }}">{{ _lang('Manage Payslip') }}</a></li>
		<li class="nav-item"><a class="nav-link" href="{{ route('payslips.make_payment') }}">{{ _lang('Make Payment') }}</a></li>
	</ul>
</li>

<li>
	<a href="javascript: void(0);"><i class="fas fa-minus-circle"></i><span>{{ _lang('Employee Expenses') }}</span> {!! $pending_expenses > 0 ? xss_clean('<div class="circle-animation"></div>') : '' !!}<span class="menu-arrow"><i class="mdi mdi-chevron-right"></i></span></a>
	<ul class="nav-second-level" aria-expanded="false">
		<li class="nav-item"><a class="nav-link" href="{{ route('employee_expenses.index') }}">{{ _lang('Expenses') }}</a></li>
		<li class="nav-item"><a class="nav-link" href="{{ route('employee_expense_categories.index') }}">{{ _lang('Categories') }}</a></li>
	</ul>
</li>

<li>
	<a href="{{ route('holidays.index') }}"><i class="fas fa-snowman"></i><span>{{ _lang('Holiday Management') }}</span></a>
</li>

<li>
	<a href="javascript: void(0);"><i class="fas fa-calendar-alt"></i><span>{{ _lang('Leave Management') }}</span> {!! $leave_application > 0 ? xss_clean('<div class="circle-animation"></div>') : '' !!}<span class="menu-arrow"><i class="mdi mdi-chevron-right"></i></span></a>
	<ul class="nav-second-level" aria-expanded="false">
		<li class="nav-item"><a class="nav-link" href="{{ route('leaves.index') }}">{{ _lang('Leave Applications') }}</a></li>
		<li class="nav-item"><a class="nav-link" href="{{ route('leave_types.index') }}">{{ _lang('Leave Types') }}</a></li>
	</ul>
</li>

<li>
	<a href="javascript: void(0);"><i class="fas fa-coins"></i><span>{{ _lang('Loan Management') }}</span> {!! $loan_application > 0 ? xss_clean('<div class="circle-animation"></div>') : '' !!}<span class="menu-arrow"><i class="mdi mdi-chevron-right"></i></span></a>
	<ul class="nav-second-level" aria-expanded="false">
		<li class="nav-item"><a class="nav-link" href="{{ route('employee_loans.index') }}?status=pending">{{ _lang('Pending Loans') }}</a></li>
		<li class="nav-item"><a class="nav-link" href="{{ route('employee_loans.index') }}">{{ _lang('Manage Loans') }}</a></li>
		<li class="nav-item"><a class="nav-link" href="{{ route('employee_loans.repayments') }}">{{ _lang('Repayments') }}</a></li>
		<li class="nav-item"><a class="nav-link" href="{{ route('employee_loan_types.index') }}">{{ _lang('Loan Types') }}</a></li>
	</ul>
</li>

<li>
	<a href="javascript: void(0);"><i class="fas fa-landmark"></i><span>{{ _lang('Accounting') }}</span><span class="menu-arrow"><i class="mdi mdi-chevron-right"></i></span></a>
	<ul class="nav-second-level" aria-expanded="false">
		<li class="nav-item"><a class="nav-link" href="{{ route('accounts.index') }}">{{ _lang('Chart Of Accounts') }}</a></li>
		<li class="nav-item"><a class="nav-link" href="{{ route('transactions.index') }}">{{ _lang('Transactions') }}</a></li>
		<li class="nav-item"><a class="nav-link" href="{{ route('transactions.create') }}">{{ _lang('Journal Entry') }}</a></li>
		<li class="nav-item"><a class="nav-link" href="{{ route('transactions.add_income') }}">{{ _lang('New Income') }}</a></li>
		<li class="nav-item"><a class="nav-link" href="{{ route('transactions.add_expense') }}">{{ _lang('New Expense') }}</a></li>
	</ul>
</li>

<li>
	<a href="{{ route('awards.index') }}"><i class="fas fa-award"></i><span>{{ _lang('Awards') }}</span></a>
</li>

<li>
	<a href="javascript: void(0);"><i class="far fa-bell"></i><span>{{ _lang('Notices') }}</span><span class="menu-arrow"><i class="mdi mdi-chevron-right"></i></span></a>
	<ul class="nav-second-level" aria-expanded="false">
		<li class="nav-item"><a class="nav-link" href="{{ route('notices.create') }}">{{ _lang('Post Notice') }}</a></li>
		<li class="nav-item"><a class="nav-link" href="{{ route('notices.index') }}">{{ _lang('Notice List') }}</a></li>
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
		<li class="nav-item"><a class="nav-link" href="{{ route('reports.attendance_report') }}">{{ _lang('Attendance Report') }}</a></li>
		<li class="nav-item"><a class="nav-link" href="{{ route('reports.payroll_report') }}">{{ _lang('Payroll Report') }}</a></li>
		<li class="nav-item"><a class="nav-link" href="{{ route('reports.trialBalance') }}">{{ _lang('Trial Balance') }}</a></li>
		<li class="nav-item"><a class="nav-link" href="{{ route('reports.generalLedger') }}">{{ _lang('General Ledger') }}</a></li>
		<li class="nav-item"><a class="nav-link" href="{{ route('reports.profitAndLoss') }}">{{ _lang('Profit & Loss') }}</a></li>
		<li class="nav-item"><a class="nav-link" href="{{ route('reports.balanceSheet') }}">{{ _lang('Balance Sheet') }}</a></li>
	</ul>
</li>

<li>
	<a href="javascript: void(0);"><i class="fas fa-user-friends"></i><span>{{ _lang('System Users') }}</span><span class="menu-arrow"><i class="mdi mdi-chevron-right"></i></span></a>
	<ul class="nav-second-level" aria-expanded="false">
		<li class="nav-item"><a class="nav-link" href="{{ route('users.index') }}">{{ _lang('Manage Users') }}</a></li>
		<li class="nav-item"><a class="nav-link" href="{{ route('roles.index') }}">{{ _lang('Roles & Permission') }}</a></li>
	</ul>
</li>

<li>
	<a href="javascript: void(0);"><i class="fas fa-globe"></i><span>{{ _lang('Languages') }}</span><span class="menu-arrow"><i class="mdi mdi-chevron-right"></i></span></a>
	<ul class="nav-second-level" aria-expanded="false">
		<li class="nav-item"><a class="nav-link" href="{{ route('languages.index') }}">{{ _lang('All Language') }}</a></li>
		<li class="nav-item"><a class="nav-link" href="{{ route('languages.create') }}">{{ _lang('Add New') }}</a></li>
	</ul>
</li>

<li><a href="{{ route('settings.update_settings') }}"><i class="fas fa-cog"></i><span>{{ _lang('System Settings') }}</span></a></li>
<li><a href="{{ route('notification_templates.index') }}"><i class="fas fa-envelope-open-text"></i><span>{{ _lang('Notification Templates') }}</span></a></li>
<li><a href="{{ route('database_backups.list') }}"><i class="fas fa-server"></i><span>{{ _lang('Database Backup') }}</span></a></li> --}}
