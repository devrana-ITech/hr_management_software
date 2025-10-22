@extends('layouts.app')

@section('content')
<div class="row">
	<div class="col-lg-8 offset-lg-2">
		<div class="card">
			<div class="card-header d-sm-flex align-items-center justify-content-between">
				<span class="panel-title">{{ _lang('General Ledger') }}</span>
				<button class="btn btn-outline-primary btn-xs print" data-print="report" type="button" id="report-print-btn"><i class="fas fa-print mr-1"></i>{{ _lang('Print Report') }}</button>
			</div>

			<div class="card-body">
                <div class="report-params">
                    <!-- Date Range Form -->
                    <form method="GET" class="validate" action="{{ route('reports.generalLedger') }}" class="mb-4">
                        <div class="row">
                            <div class="col-md-3">
                                <label for="from_date">{{ _lang('From Date') }}</label>
                                <input type="text" name="from_date" class="form-control datepicker" value="{{ $fromDate }}" readonly>
                            </div>

                            <div class="col-md-3">
                                <label for="to_date">{{ _lang('To Date') }}</label>
                                <input type="text" name="to_date" class="form-control datepicker" value="{{ $toDate }}" readonly>
                            </div>

                            <div class="col-md-4">
                                <label for="account_id">{{ _lang('Select Account') }}</label>
                                <select name="account_id" id="account_id" class="form-control select2">
                                    <option value="">{{ _lang('All Accounts') }}</option>
                                    @foreach($allAccounts as $account)
                                        <option value="{{ $account->id }}" {{ $accountId == $account->id ? 'selected' : '' }}>
                                            {{ $account->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-2 d-flex align-items-end">
                                <button type="submit" class="btn btn-light btn-xs btn-block"><i class="ti-filter mr-1"></i>{{ _lang('Filter') }}</button>
                            </div>
                        </div>
                    </form>
                </div>

                @php $date_format = get_date_format(); @endphp

                <div id="report">
					<div class="report-header">
						<h4>{{ get_option('company_name') }}</h4>
						<p>{{ _lang('General Ledger') }}</p>
						<p>{{ isset($fromDate) ? date($date_format, strtotime($fromDate)).' '._lang('to').' '.date($date_format, strtotime($toDate)) : '----------  '._lang('to').'  ----------' }}</p>
					</div>

                    @foreach($generalLedger as $ledger)
                    <div class="table-responsive {{ !$loop->last ? 'mb-4' : '' }}">
                        <table class="table table-bordered">
                            <thead class="bg-light">
                                <tr>
                                    <th class="text-dark" colspan="5">{{ $ledger['account_name'] }}</th>
                                </tr>
                                <tr>
                                    <th class="text-dark">{{ _lang('Date') }}</th>
                                    <th class="text-dark">{{ _lang('Description') }}</th>
                                    <th class="text-dark text-right">{{ _lang('Debit') }}</th>
                                    <th class="text-dark text-right">{{ _lang('Credit') }}</th>
                                    <th class="text-dark text-right">{{ _lang('Balance') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td colspan="4"><strong>{{ _lang('Opening Balance') }}</strong></td>
                                    <td class="text-right">{{ decimalPlace($ledger['opening_balance']) }}</td>
                                </tr>
                                @foreach($ledger['transactions'] as $transaction)
                                    <tr>
                                        <td>{{ $transaction['date'] }}</td>
                                        <td>{{ $transaction['description'] }}</td>
                                        <td class="text-right">{{ decimalPlace($transaction['debit']) }}</td>
                                        <td class="text-right">{{ decimalPlace($transaction['credit']) }}</td>
                                        <td class="text-right">{{ decimalPlace($transaction['balance']) }}</td>
                                    </tr>
                                @endforeach
                                <tr>
                                    <td colspan="4"><strong>{{ _lang('Closing Balance') }}</strong></td>
                                    <td class="text-right">{{ decimalPlace($ledger['closing_balance'], $currency_symbol) }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
