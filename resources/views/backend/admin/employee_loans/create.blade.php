@extends('layouts.app')

@section('content')
<div class="row">
	<div class="{{ $alert_col }}">
		<div class="card">
			<div class="card-header">
				<span class="panel-title">{{ _lang('New Loan Application') }}</span>
			</div>
			<div class="card-body">
			    <form method="post" class="validate" autocomplete="off" action="{{ route('employee_loans.store') }}" enctype="multipart/form-data">
					{{ csrf_field() }}
					<div class="row">
						<div class="col-lg-6">
							<div class="form-group">
								<label class="control-label">{{ _lang('Loan ID') }}</label>
								<input type="text" class="form-control" name="loan_id" value="{{ old('loan_id') }}" required>
							</div>
						</div>

						<div class="col-lg-6">
							<div class="form-group">
								<label class="control-label">{{ _lang('Loan Type') }}</label>
								<select class="form-control auto-select" name="loan_type_id" id="loan_type_id" data-selected="{{ old('loan_type_id') }}" required>
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
								<input type="text" class="form-control datepicker" name="application_date" value="{{ old('application_date') }}" required>
							</div>
						</div>

						<div class="col-lg-6">
							<div class="form-group">
								<label class="control-label">{{ _lang('Employee') }}</label>
								<select class="form-control auto-select select2" data-selected="{{ old('employee_id') }}" name="employee_id" required>
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
								<input type="text" class="form-control float-field" name="loan_amount" id="loan_amount" value="{{ old('loan_amount') }}" required>
							</div>
						</div>

						<div class="col-lg-6">
							<div class="form-group">
								<label class="control-label">{{ _lang('Interest Rate') }} (%)</label>
								<input type="text" class="form-control float-field" name="interest_rate" id="interest_rate" value="{{ old('interest_rate') }}" readonly>
							</div>
						</div>

						<div class="col-lg-6">
							<div class="form-group">
								<label class="control-label">{{ _lang('Interest Type') }}</label>
								<select class="form-control auto-select" name="interest_type" id="interest_type" data-selected="{{ old('interest_type', 'fixed') }}" disabled>
									<option value="fixed">{{ _lang('Fixed') }}</option>
									<option value="declining">{{ _lang('Declining') }}</option>
								</select>
							</div>
						</div>

						<div class="col-lg-6">
							<div class="form-group">
								<label class="control-label">{{ _lang('Term') }}</label>
								<input type="number" class="form-control float-field" name="term" id="term" value="{{ old('term') }}" readonly>
							</div>
						</div>

						<div class="col-lg-12">
							<div class="form-group">
								<label class="control-label">{{ _lang('Loan Purpose') }}</label>
								<input type="text" class="form-control" name="loan_purpose" value="{{ old('loan_purpose') }}" required>
							</div>
						</div>

						<div class="col-lg-12">
							<div class="form-group">
								<label class="control-label">{{ _lang('Attachment') }}</label>
								<input type="file" class="dropify" name="attachment">
							</div>
						</div>

						<div class="col-lg-12">
							<div class="form-group">
								<label class="control-label">{{ _lang('Description') }}</label>
								<textarea class="form-control" name="description">{{ old('description') }}</textarea>
							</div>
						</div>

						<div class="col-lg-12">
							<div class="form-group">
								<label class="control-label">{{ _lang('Remarks') }}</label>
								<textarea class="form-control" name="remarks">{{ old('remarks') }}</textarea>
							</div>
						</div>

						<div class="col-lg-12 mt-2">
							<div class="form-group">
								<button type="submit" class="btn btn-primary"><i class="ti-check-box mr-2"></i>{{ _lang('Submit') }}</button>
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


