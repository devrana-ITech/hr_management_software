<form method="post" class="validate" autocomplete="off" action="{{ route('employee_loans.approve', $loan->id) }}">
    {{ csrf_field() }}
    <div class="row px-2">
        <div class="col-lg-12">
            <div class="form-group">
                <label class="control-label">{{ _lang('Loan ID') }}</label>						
                <input type="text" class="form-control" name="loan_id" value="{{ $loan->loan_id }}" required>
            </div>
        </div>

        <div class="col-lg-12">
            <div class="form-group">
                <label class="control-label">{{ _lang('Due Date') }}</label>						
                <input type="text" class="form-control datepicker" name="loan_due_at" value="{{ old('loan_due_at', $dueDate) }}" required>
            </div>
        </div>
        
        <div class="col-lg-12">
            <div class="form-group">
                <label for="transaction_date" class="form-label">{{ _lang('Cash/Bank Account') }}</label>
                <select class="form-control select2 auto-select" name="account_id" data-selected="{{ old('account_id') }}" required>
                    <option value="">{{ _lang('Select One') }}</option>
                    @foreach($accounts as $account)
                        <option value="{{ $account->id }}">
                            {{ $account->account_id.' - '.$account->name }}
                        </option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="col-lg-12 mt-2">
            <div class="form-group">
                <button type="submit" class="btn btn-primary"><i class="ti-check-box mr-2"></i>{{ _lang('Submit') }}</button>
            </div>
        </div>
    </div>
</form>


