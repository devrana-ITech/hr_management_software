@extends('layouts.app')

@section('content')
<div class="row">
	<div class="{{ $alert_col }}">
		<div class="card">
		    <div class="card-header">
				<span class="panel-title">{{ _lang('Loan Details') }}</span>
			</div>
			
			<div class="card-body">
			    <table class="table table-bordered">
				    <tr><td>{{ _lang('Loan ID') }}</td><td>{{ $employeeloan->loan_id }}</td></tr>
				    <tr><td>{{ _lang('Application Date') }}</td><td>{{ $employeeloan->application_date }}</td></tr>
					<tr><td>{{ _lang('Employee') }}</td><td>{{ $employeeloan->employee->name }}</td></tr>
					<tr><td>{{ _lang('Loan Amount') }}</td><td>{{ decimalPlace($employeeloan->loan_amount, currency_symbol()) }}</td></tr>
					<tr><td>{{ _lang('Remaining Balance') }}</td><td>{{ decimalPlace($employeeloan->remaining_balance, currency_symbol()) }}</td></tr>
					<tr><td>{{ _lang('Interest Rate') }}</td><td>{{ $employeeloan->interest_rate }}%</td></tr>
					<tr><td>{{ _lang('Interest Type') }}</td><td>{{ ucwords($employeeloan->interest_type) }}</td></tr>
					<tr><td>{{ _lang('Monthly Installment') }}</td><td>{{ decimalPlace($employeeloan->monthly_installment, currency_symbol()) }}</td></tr>
					<tr>
						<td>{{ _lang('Next Interest Amount') }}</td>
						<td>{{ decimalPlace($interest, currency_symbol()) }}</td>
					</tr>
					<tr><td>{{ _lang('Loan Issued At') }}</td><td>{{ $employeeloan->loan_issued_at ?? _lang('N/A') }}</td></tr>
					<tr><td>{{ _lang('Loan Due At') }}</td><td>{{ $employeeloan->loan_due_at ?? _lang('N/A') }}</td></tr>
					<tr><td>{{ _lang('Loan Purpose') }}</td><td>{{ $employeeloan->loan_purpose }}</td></tr>
					@if($employeeloan->attachment != null)
					<tr>
						<td>{{ _lang('Attachment') }}</td>
						<td><a href="{{ asset('storage/app/public/'. $employeeloan->attachment) }}" target="_blank"><i class="fas fa-paperclip mr-1"></i>{{ _lang('Download') }}</a></td>
					</tr>
					@endif
					<tr><td>{{ _lang('Description') }}</td><td>{{ $employeeloan->description }}</td></tr>
					<tr><td>{{ _lang('Remarks') }}</td><td>{{ $employeeloan->remarks }}</td></tr>
					<tr>
						<td>{{ _lang('Status') }}</td>
						<td>
						@if ($employeeloan->status == 'pending')
							<span class="badge badge-warning">{{ ucwords($employeeloan->status) }}<span>
						@elseif ($employeeloan->status == 'approved')
							<span class="badge badge-success">{{ ucwords($employeeloan->status) }}<span>
						@elseif ($employeeloan->status == 'rejected')
							<span class="badge badge-danger">{{  ucwords($employeeloan->status) }}<span>
						@elseif ($employeeloan->status == 'repaid')
							<span class="badge badge-primary">{{  ucwords($employeeloan->status) }}<span>
						@endif
						</td>
					</tr>
					
					<tr><td>{{ _lang('Created By') }}</td><td>{{ $employeeloan->created_by->name }}</td></tr>
			    </table>
			</div>
	    </div>
	</div>
</div>
@endsection


