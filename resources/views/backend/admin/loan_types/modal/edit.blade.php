<form method="post" class="ajax-screen-submit" autocomplete="off" action="{{ route('employee_loan_types.update', $id) }}" enctype="multipart/form-data">
	{{ csrf_field()}}
	<input name="_method" type="hidden" value="PATCH">
	<div class="row px-2">
		<div class="col-md-12">
			<div class="form-group">
				<label class="control-label">{{ _lang('Name') }}</label>						
				<input type="text" class="form-control" name="name" value="{{ $loantype->name }}" required>
			</div>
		</div>

		<div class="col-md-12">
			<div class="form-group">
				<label class="control-label">{{ _lang('Minimum Amount') }}</label>						
				<input type="text" class="form-control float-field" name="minimum_amount" value="{{ $loantype->minimum_amount }}" required>
			</div>
		</div>

		<div class="col-md-12">
			<div class="form-group">
				<label class="control-label">{{ _lang('Maximum Amount') }}</label>						
				<input type="text" class="form-control float-field" name="maximum_amount" value="{{ $loantype->maximum_amount }}" required>
			</div>
		</div>

		<div class="col-md-12">
			<div class="form-group">
				<label class="control-label">{{ _lang('Interest Rate') }}(%)</label>						
				<input type="text" class="form-control float-field" name="interest_rate" value="{{ $loantype->interest_rate }}" required>
			</div>
		</div>

		<div class="col-md-12">
			<div class="form-group">
				<label class="control-label">{{ _lang('Interest Type') }}</label>						
				<select class="form-control auto-select" data-selected="{{ $loantype->interest_type }}" name="interest_type"  required>
					<option value="fixed">{{ _lang('Fixed') }}</option>
					<option value="declining">{{ _lang('Declining') }}</option>	
				</select>
			</div>
		</div>

		<div class="col-md-12">
			<div class="form-group">
				<label class="control-label">{{ _lang('Term') }}</label>						
				<input type="number" class="form-control" name="term" value="{{ $loantype->term }}" required>
			</div>
		</div>

		<div class="col-md-12 mt-2">
			<div class="form-group">
			    <button type="submit" class="btn btn-primary"><i class="ti-check-box mr-2"></i> {{ _lang('Update') }}</button>
		    </div>
		</div>
	</div>
</form>

