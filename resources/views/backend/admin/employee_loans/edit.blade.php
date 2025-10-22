@extends('layouts.app')

@section('content')
<div class="row">
	<div class="{{ $alert_col }}">
		<div class="card">
			<div class="card-header">
				<span class="panel-title">{{ _lang('Update Loan') }}</span>
			</div>
			<div class="card-body">
				<form method="post" class="validate" autocomplete="off" action="{{ route('employee_loans.update', $id) }}" enctype="multipart/form-data">
					{{ csrf_field()}}
					<input name="_method" type="hidden" value="PATCH">
					<div class="row">
						<div class="col-lg-6">
							<div class="form-group">
								<label class="control-label">{{ _lang('Loan ID') }}</label>						
								<input type="text" class="form-control" name="loan_id" value="{{ $employeeloan->loan_id }}" required>
							</div>
						</div>

						<div class="col-lg-6">
							<div class="form-group">
								<label class="control-label">{{ _lang('Loan Type') }}</label>						
								<select class="form-control auto-select" name="loan_type_id" data-selected="{{ $employeeloan->loan_type_id }}" required>
									<option value="">{{ _lang('Select One') }}</option>
									@foreach(\App\Models\LoanType::all() as $loan_type)
									<option value="{{ $loan_type->id }}">{{ $loan_type->name }}</option>
									@endforeach
								</select>
							</div>
						</div>

						<div class="col-lg-6">
							<div class="form-group">
								<label class="control-label">{{ _lang('Application Date') }}</label>						
								<input type="text" class="form-control datepicker" name="application_date" value="{{ $employeeloan->application_date }}" required>
							</div>
						</div>

						<div class="col-lg-6">
							<div class="form-group">
								<label class="control-label">{{ _lang('Employee') }}</label>						
								<select class="form-control auto-select select2" data-selected="{{ $employeeloan->employee_id }}" name="employee_id" required>
									<option value="">{{ _lang('Select One') }}</option>
									@foreach(\App\Models\Employee::active()->get() as $employee)
									<option value="{{ $employee->id }}">{{ $employee->employee_id }} ({{ $employee->name }})</option>
									@endforeach
								</select>
							</div>
						</div>

						<div class="col-lg-6">
							<div class="form-group">
								<label class="control-label">{{ _lang('Loan Amount') }}</label>						
								<input type="text" class="form-control float-field" name="loan_amount" id="loan_amount" value="{{ $employeeloan->loan_amount }}" required>
							</div>
						</div>

						<div class="col-lg-6">
							<div class="form-group">
								<label class="control-label">{{ _lang('Interest Rate') }} (%)</label>						
								<input type="text" class="form-control float-field" name="interest_rate" id="interest_rate" value="{{ $employeeloan->interest_rate }}" readonly>
							</div>
						</div>

						<div class="col-lg-6">
							<div class="form-group">
								<label class="control-label">{{ _lang('Interest Type') }}</label>						
								<select class="form-control auto-select" name="interest_type" id="interest_type" data-selected="{{ $employeeloan->interest_type }}" disabled>
									<option value="fixed">{{ _lang('Fixed') }}</option>
									<option value="declining">{{ _lang('Declining') }}</option>
								</select>
							</div>
						</div>

						<div class="col-lg-6">
							<div class="form-group">
								<label class="control-label">{{ _lang('Term') }}</label>						
								<input type="text" class="form-control float-field" name="term" value="{{ $employeeloan->loan_amount / $employeeloan->monthly_installment }}" readonly>
							</div>
						</div>

						<div class="col-lg-6">
							<div class="form-group">
								<label class="control-label">{{ _lang('Loan Due At') }}</label>						
								<input type="date" class="form-control" name="loan_due_at" value="{{ $employeeloan->loan_due_at }}">
							</div>
						</div>

						<div class="col-lg-6">
							<div class="form-group">
								<label class="control-label">{{ _lang('Loan Purpose') }}</label>						
								<input type="text" class="form-control" name="loan_purpose" value="{{ $employeeloan->loan_purpose }}" required>
							</div>
						</div>

						<div class="col-lg-12">
							<div class="form-group">
								<label class="control-label">{{ _lang('Attachment') }}</label>						
								<input type="file" class="dropify" name="attachment" data-defult-file-url="{{ $employeeloan->attachment != null ? asset('storage/app/public/'. $employeeloan->attachment) : '' }}">
							</div>
						</div>

						<div class="col-lg-12">
							<div class="form-group">
								<label class="control-label">{{ _lang('Description') }}</label>						
								<textarea class="form-control" name="description">{{ $employeeloan->description }}</textarea>
							</div>
						</div>

						<div class="col-lg-12">
							<div class="form-group">
								<label class="control-label">{{ _lang('Remarks') }}</label>						
								<textarea class="form-control" name="remarks">{{ $employeeloan->remarks }}</textarea>
							</div>
						</div>
				
						<div class="col-lg-12 mt-2">
							<div class="form-group">
								<button type="submit" class="btn btn-primary"><i class="ti-check-box mr-2"></i> {{ _lang('Update') }}</button>
							</div>
						</div>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>
@endsection

@section('js-script')
<script>
(function ($) {
	"use strict";

	$(document).on('change','#loan_type_id', function(){
		if($(this).val() != ''){
			$.get('/employee_loan_types/'+ $(this).val() +'/get_loan_type', function(data, status){
				var json = JSON.parse(JSON.stringify(data));
				$("#interest_rate").val(json['interest_rate']);
				$("#interest_type").val(json['interest_type']);
				$("#term").val(json['term']);
				$("#loan_amount").prop('placeholder',json['minimum_amount'] + ' - ' + json['maximum_amount']);
			});
		}else{
			$("#interest_rate").val('');
			$("#interest_type").val('');
			$("#term").val('');
			$("#loan_amount").prop('placeholder','');
		}
	});

})(jQuery);
</script>
@endsection


