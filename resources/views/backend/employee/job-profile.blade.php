@extends('layouts.app')

@section('content')
<div class="row">
	<div class="col-lg-10 offset-lg-1">
		<div class="card">
		    <div class="card-header text-center">
				<span class="panel-title">{{ _lang('Personal Information') }}</span>
			</div>
			
			<div class="card-body">
			    <table class="table table-bordered">
				    <tr><td colspan="2" class="bg-light"><b>{{ _lang('Personal Details') }}</b></td></tr>
					<tr>
						<td colspan="2" class="text-center"><img class="thumb-xl rounded" src="{{ profile_picture($employee->image) }}"></td>
					</tr>
				    <tr><td>{{ _lang('Employee ID') }}</td><td>{{ $employee->employee_id }}</td></tr>
					<tr><td>{{ _lang('First Name') }}</td><td>{{ $employee->first_name }}</td></tr>
					<tr><td>{{ _lang('Last Name') }}</td><td>{{ $employee->last_name }}</td></tr>
					<tr><td>{{ _lang('Fathers Name') }}</td><td>{{ $employee->fathers_name }}</td></tr>
					<tr><td>{{ _lang('Mothers Name') }}</td><td>{{ $employee->mothers_name }}</td></tr>
					<tr><td>{{ _lang('Date Of Birth') }}</td><td>{{ $employee->date_of_birth }}</td></tr>
					<tr><td>{{ _lang('Email') }}</td><td>{{ $employee->email }}</td></tr>
					<tr><td>{{ _lang('Phone') }}</td><td>{{ $employee->phone }}</td></tr>
					<tr><td>{{ _lang('City') }}</td><td>{{ $employee->city }}</td></tr>
					<tr><td>{{ _lang('State') }}</td><td>{{ $employee->state }}</td></tr>
					<tr><td>{{ _lang('Zip') }}</td><td>{{ $employee->zip }}</td></tr>
					<tr><td>{{ _lang('Country') }}</td><td>{{ $employee->country }}</td></tr>
					
					<tr><td colspan="2" class="bg-light"><b>{{ _lang('Company Details') }}</b></td></tr>
					<tr><td>{{ _lang('Department') }}</td><td>{{ $employee->department->name }}</td></tr>
					<tr><td>{{ _lang('Designation') }}</td><td>{{ $employee->designation->name }}</td></tr>
					<tr><td>{{ _lang('Salary Type') }}</td><td>{{ ucwords($employee->salary_type) }}</td></tr>
					<tr><td>{{ _lang('Basic Salary') }}</td><td>{{ decimalPlace($employee->basic_salary, currency_symbol(currency())) }}</td></tr>
					<tr><td>{{ _lang('Full Day Absence Fine') }}</td><td>{{ decimalPlace($employee->full_day_absence_fine, currency_symbol(currency())) }}</td></tr>
					<tr><td>{{ _lang('Half Day Absence Fine') }}</td><td>{{ decimalPlace($employee->half_day_absence_fine, currency_symbol(currency())) }}</td></tr>
					<tr><td>{{ _lang('Joining Date') }}</td><td>{{ $employee->joining_date }}</td></tr>
					@if($employee->end_date != null)
					<tr><td>{{ _lang('End Date') }}</td><td>{{ $employee->end_date }}</td></tr>
					@endif
					
					<tr><td colspan="2" class="bg-light"><b>{{ _lang('Bank Details') }}</b></td></tr>
					<tr><td>{{ _lang('Bank Name') }}</td><td>{{ $employee->bank_name }}</td></tr>
					<tr><td>{{ _lang('Branch Name') }}</td><td>{{ $employee->branch_name }}</td></tr>
					<tr><td>{{ _lang('Account Name') }}</td><td>{{ $employee->account_name }}</td></tr>
					<tr><td>{{ _lang('Account Number') }}</td><td>{{ $employee->account_number }}</td></tr>
					<tr><td>{{ _lang('Swift Code') }}</td><td>{{ $employee->swift_code }}</td></tr>
					<tr><td>{{ _lang('Remarks') }}</td><td>{{ $employee->remarks }}</td></tr>

					@if($employee->documents->count() > 0)
					<tr><td colspan="2" class="bg-light"><b>{{ _lang('Documents') }}</b></td></tr>
					@endif
					@foreach($employee->documents as $document)
					<tr>
						<td>{{ $document->name }}</td>
						<td><a href="{{ asset('public/uploads/documents/'.$document->document) }}" class="btn btn-xs btn-light"><i class="fas fa-download mr-2"></i>{{ _lang('Download') }}</a></td>
					</tr>
					@endforeach
			    </table>
			</div>
	    </div>
	</div>
</div>

<div class="row">
	<div class="col-lg-5 offset-lg-1">
		<div class="card">
			<div class="card-header">
				<span class="panel-title text-success font-weight-bold">{{ _lang('Allowances') }}</span>
			</div>
			<div class="card-body">
				<table class="table table-bordered" id="allowances">
					<thead class="bg-white">
						<th class="text-dark">{{ _lang('Name') }}</th>
						<th class="text-dark text-right">{{ _lang('Amount') }}</th>
					</thead>
					<tbody>
						@foreach($employee->benefit_deductions()->where('type','add')->get() as $allowances)
						<tr>
							<td>{{ $allowances->name }}</td>
							<td class="text-right">
								{{ decimalPlace($allowances->amount, currency_symbol(currency())) }}
								{{ $allowances->amount_type == 'percent' ? '%' : '' }}
							</td>
						</tr>
						@endforeach
					</tbody>
				</table>
			</div>
		</div>
	</div>

	<div class="col-lg-5">
		<div class="card">
			<div class="card-header d-flex justify-content-between">
				<span class="panel-title text-danger font-weight-bold">{{ _lang('Deductions') }}</span>
			</div>
			<div class="card-body">
				<table class="table table-bordered" id="deductions">
					<thead class="bg-white">
						<th class="text-dark">{{ _lang('Name') }}</th>
						<th class="text-dark text-right">{{ _lang('Amount') }}</th>
					</thead>
					<tbody>
						@foreach($employee->benefit_deductions()->where('type','deduct')->get() as $deductions)
						<tr>
							<td>{{ $deductions->name }}</td>
							<td class="text-right">
								{{ decimalPlace($deductions->amount, currency_symbol(currency())) }}
								{{ $deductions->amount_type == 'percent' ? '%' : '' }}
							</td>
						</tr>
						@endforeach
					</tbody>
				</table>
			</div>
		</div>
	</div>
</div>
@endsection