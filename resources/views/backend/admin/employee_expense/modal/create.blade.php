<form method="post" class="ajax-submit" autocomplete="off" action="{{ route('employee_expenses.store') }}" enctype="multipart/form-data">
    @csrf
    <div class="row p-2">
        <div class="col-lg-6">
            <div class="form-group">
                <label class="control-label">{{ _lang('Trans Date') }}</label>						
                <input type="text" class="form-control datetimepicker" name="trans_date" value="{{ old('trans_date', now()) }}" required>
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
                <label class="control-label">{{ _lang('Bill No') }}</label>						
                <input type="text" class="form-control" name="bill_no" value="{{ old('bill_no') }}">
            </div>
        </div>


        <div class="col-lg-6">
            <div class="form-group">
                <label class="control-label">{{ _lang('Amount') }}</label>						
                <input type="text" class="form-control float-field" name="amount" value="{{ old('amount') }}" required>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="form-group">
                <label class="control-label">{{ _lang('Category') }}</label>						
                <select class="form-control select2-ajax" name="expense_category_id" data-table="employee_expense_categories"
                data-value="id" data-display="name" data-title="{{ _lang('Add Category') }}" data-href="{{ route('employee_expense_categories.create') }}" required>
                <option value="">{{ _lang('Select One') }}</option>
                </select>
            </div>
        </div>

        <div class="col-lg-6">
			<div class="form-group">
				<label class="control-label">{{ _lang('Status') }}</label>						
				<select class="form-control auto-select" data-selected="{{ old('status', 1) }}" name="status"  required>
					<option value="1">{{ _lang('Completed') }}</option>
					<option value="0">{{ _lang('Pending') }}</option>
				</select>
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
                <label class="control-label">{{ _lang('Attachment') }}</label>						
                <input type="file" class="dropify" name="attachment" >
            </div>
        </div>

        <div class="col-lg-12 mt-2">
            <div class="form-group">
                <button type="submit" class="btn btn-primary"><i class="ti-check-box mr-2"></i> {{ _lang('Submit') }}</button>
            </div>
        </div>
    </div>
</form>