@php
$inbox = request_count('messages');
@endphp

<li><a href="{{ route('dashboard.index') }}"><i class="fas fa-th-large"></i><span>{{ _lang('Dashboard') }}</span></a></li>
<li><a href="{{ route('my_payslips.index') }}"><i class="fas fa-money-check-alt"></i><span>{{ _lang('Payslips') }}</span></a></li>
<li><a href="{{ route('my_leaves.index') }}"><i class="fas fa-calendar-alt"></i><span>{{ _lang('Leave Applications') }}</span></a></li>
<li><a href="{{ route('my_expenses.index') }}"><i class="fas fa-minus-circle"></i><span>{{ _lang('Expenses') }}</span></a></li>
<li>
	<a href="javascript: void(0);"><i class="fas fa-coins"></i><span>{{ _lang('Loans') }}</span><span class="menu-arrow"><i class="mdi mdi-chevron-right"></i></span></a>
	<ul class="nav-second-level" aria-expanded="false">
		<li class="nav-item"><a class="nav-link" href="{{ route('my_loans.create') }}">{{ _lang('Apply New Loan') }}</a></li>
		<li class="nav-item"><a class="nav-link" href="{{ route('my_loans.index') }}">{{ _lang('Manage Loans') }}</a></li>
		<li class="nav-item"><a class="nav-link" href="{{ route('my_loans.repayments') }}">{{ _lang('Repayments') }}</a></li>
	</ul>
</li>
<li><a href="{{ route('my_awards.index') }}"><i class="fas fa-award"></i><span>{{ _lang('Awards') }}</span></a></li>
<li><a href="{{ route('my_reports.attendance_report') }}"><i class="fas fa-user-clock"></i><span>{{ _lang('Attendance Report') }}</span></a></li>
@if(auth()->user()->employee->salary_type == 'hourly')
<li><a href="{{ route('my_reports.work_hour_report') }}"><i class="far fa-clock"></i><span>{{ _lang('Work Hour Report') }}</span></a></li>
@endif
<li>
	<a href="javascript: void(0);"><i class="fas fa-envelope"></i><span>{{ _lang('Messages') }}</span> {!! $inbox > 0 ? xss_clean('<div class="circle-animation"></div>') : '' !!}<span class="menu-arrow"><i class="mdi mdi-chevron-right"></i></span></a>
	<ul class="nav-second-level" aria-expanded="false">
		<li class="nav-item"><a class="nav-link" href="{{ route('messages.compose') }}">{{ _lang('New Message') }}</a></li>
		<li class="nav-item"><a class="nav-link" href="{{ route('messages.inbox') }}">{{ _lang('Inbox Items') }}</a></li>
		<li class="nav-item"><a class="nav-link" href="{{ route('messages.sent') }}">{{ _lang('Sent Items') }}</a></li>
	</ul>
</li>
<li><a href="{{ route('profile.job_profile') }}"><i class="fas fa-id-card"></i><span>{{ _lang('Personal Information') }}</span></a></li>