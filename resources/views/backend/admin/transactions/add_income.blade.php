@extends('layouts.app')

@section('content')
<div class="row">
	<div class="{{ $alert_col }}">
		<div class="card">
		    <div class="card-header d-flex align-items-center">
				<span class="panel-title">{{ _lang('Add Income') }}</span>
			</div>
			<div class="card-body">
                <form class="validate" action="{{ route('transactions.add_income') }}" method="POST" enctype="multipart/form-data">
                    @csrf

                    <div class="row">
                        <div class="col-lg-6">
                            <div class="form-group">
                                <label for="transaction_date" class="form-label">{{ _lang('Transaction Date') }}</label>
                                <input type="text" class="form-control datepicker" id="transaction_date" name="transaction_date" value="{{ old('transaction_date', date('Y-m-d')) }}" required>
                            </div>
                        </div>

                        <div class="col-lg-6">
                            <div class="form-group">
                                <label for="transaction_date" class="form-label">{{ _lang('Account') }}</label>
                                <select class="form-control select2 auto-select" id="account_id" name="account_id" data-selected="{{ old('account_id') }}" required>
                                    <option value="">{{ _lang('Select One') }}</option>
                                    @foreach($accounts as $account)
                                        <option value="{{ $account->id }}">
                                            {{ $account->account_id.' - '.$account->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="col-lg-6">
                            <div class="form-group">
                                <label for="transaction_date" class="form-label">{{ _lang('Category') }}</label>
                                <select class="form-control select2 auto-select" id="category_id" name="category_id" data-selected="{{ old('category_id') }}" required>
                                    <option value="">{{ _lang('Select One') }}</option>
                                    @foreach($categories as $category)
                                        <option value="{{ $category->id }}">
                                            {{ $category->account_id.' - '.$category->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="col-lg-6">
                            <div class="form-group">
                                <label for="description" class="form-label">{{ _lang('Amount') }}</label>
                                <input type="text" class="form-control float-field" id="amount" name="amount" value="{{ old('amount') }}" required>
                            </div>
                        </div>

                        <div class="col-lg-12">
                            <div class="form-group">
                                <label for="description" class="form-label">{{ _lang('Description') }}</label>
                                <input type="text" class="form-control" id="description" name="description" value="{{ old('description') }}" required>
                            </div>
                        </div>

                        <div class="col-lg-12">
                            <div class="form-group">
                                <label for="attachment" class="form-label">{{ _lang('Attachment') }}</label>
                                <input type="file" class="dropify" id="attachment" name="attachment" value="{{ old('attachment') }}">
                            </div>
                        </div>

                        <div class="col-lg-12">
                            <button type="submit" class="btn btn-primary"><i class="fas fa-check-circle mr-1"></i>{{ _lang('Submit') }}</button>
                        </div>
                    </div>                 
                </form>

            </div>
        </div>
    </div>
</div>
@endsection
