<form method="post" class="validate" autocomplete="off" action="{{ route('accounts.store') }}" enctype="multipart/form-data">
    @csrf
    <div class="row px-2">
        <div class="col-md-12">
            <div class="form-group">
                <label class="control-label">{{ _lang('Account ID') }}</label>						
                <input type="number" class="form-control" name="account_id" value="{{ old('account_id') }}" required>
            </div>
        </div>

        <div class="col-md-12">
            <div class="form-group">
                <label class="control-label">{{ _lang('Name') }}</label>						
                <input type="text" class="form-control" name="name" value="{{ old('name') }}" required>
            </div>
        </div>

        <div class="col-md-12">
            <div class="form-group">
                <label class="control-label">{{ _lang('Account Type') }}</label>						
                <select name="type" class="form-control select2 auto-select" id="type" data-selected="{{ old('type') }}" required>
                    <option value="">{{ _lang('Select One') }}</option>
                    <option value="asset">{{ _lang('Asset') }}</option>
                    <option value="liability">{{ _lang('Liability') }}</option>
                    <option value="equity">{{ _lang('Equity') }}</option>
                    <option value="revenue">{{ _lang('Revenue') }}</option>
                    <option value="expense">{{ _lang('Expense') }}</option>
                </select>
            </div>
        </div>

        <div class="col-md-12 {{ old('type') != 'asset' ? 'd-none' : '' }}" id="bank-cash-field">
            <div class="form-group">
                <label class="control-label">{{ _lang('Bank/Cash Account') }}?</label>	
                <select class="form-control" name="is_bank">	
                    <option value="0">{{ _lang('No') }}</option>
                    <option value="1">{{ _lang('Yes') }}</option>
                </select>
            </div>
        </div>
     
        <div class="col-md-12 mt-2">
            <div class="form-group">
                <button type="submit" class="btn btn-primary"><i class="ti-check-box mr-2"></i> {{ _lang('Save') }}</button>
            </div>
        </div>
    </div>
</form>

<script>
$(document).on('change', '#type', function(){
    $(this).val() == 'asset' ? $("#bank-cash-field").removeClass('d-none') : $("#bank-cash-field").addClass('d-none');
});
</script>