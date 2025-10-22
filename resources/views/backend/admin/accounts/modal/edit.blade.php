<form method="post" class="validate" autocomplete="off" action="{{ route('accounts.update', $id) }}" enctype="multipart/form-data">
    @csrf
    <input name="_method" type="hidden" value="PATCH">
    <div class="row px-2">
        @if($account->is_default)
        <div class="col-md-12">
            <div class="alert alert-warning">
                <span><i class="fas fa-exclamation-circle mr-1"></i>{{ _lang("You can't change account type of system defined accounts") }}</span>
            </div>
        </div>
        @endif

        <div class="col-md-12">
            <div class="form-group">
                <label class="control-label">{{ _lang('Account ID') }}</label>						
                <input type="number" class="form-control" name="account_id" value="{{ $account->account_id }}" required>
            </div>
        </div>

        <div class="col-md-12">
            <div class="form-group">
                <label class="control-label">{{ _lang('Name') }}</label>						
                <input type="text" class="form-control" name="name" value="{{ $account->name }}" required>
            </div>
        </div>

        <div class="col-md-12">
            <div class="form-group">
                <label class="control-label">{{ _lang('Account Type') }}</label>						
                <select name="type" class="form-control {{ $account->is_default ? '' : 'select2' }} auto-select" data-selected="{{ $account->type }}" {{ $account->is_default ? 'disabled' : 'required' }}>
                    <option value="asset">{{ _lang('Asset') }}</option>
                    <option value="liability">{{ _lang('Liability') }}</option>
                    <option value="equity">{{ _lang('Equity') }}</option>
                    <option value="revenue">{{ _lang('Revenue') }}</option>
                    <option value="expense">{{ _lang('Expense') }}</option>
                </select>
            </div>
        </div>

        <div class="col-md-12 {{ $account->type != 'asset' ? 'd-none' : '' }}" id="bank-cash-field">
            <div class="form-group">
                <label class="control-label">{{ _lang('Bank/Cash Account') }}?</label>	
                <select class="form-control" name="is_bank" {{ $account->is_default ? 'disabled' : '' }}>	
                    <option value="0" {{ $account->is_bank == 0 ? 'selected' : '' }}>{{ _lang('No') }}</option>
                    <option value="1" {{ $account->is_bank == 1 ? 'selected' : '' }}>{{ _lang('Yes') }}</option>
                </select>
            </div>
        </div>
            
        <div class="col-md-12 mt-2">
            <div class="form-group">
                <button type="submit" class="btn btn-primary"><i class="ti-check-box mr-2"></i> {{ _lang('Update') }}</button>
            </div>
        </div>
    </div>
</form>

<script>
$(document).on('change', '#type', function(){
    $(this).val() == 'asset' ? $("#bank-cash-field").removeClass('d-none') : $("#bank-cash-field").addClass('d-none');
});
</script>