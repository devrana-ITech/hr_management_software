@extends('layouts.app')

@section('content')
<div class="row">
	<div class="col-lg-8 offset-lg-2">
		<div class="card">
			<div class="card-header d-sm-flex align-items-center justify-content-between">
				<span class="panel-title">{{ _lang('Profit & Loss Report') }}</span>
				<button class="btn btn-outline-primary btn-xs print" data-print="report" type="button" id="report-print-btn"><i class="fas fa-print mr-1"></i>{{ _lang('Print Report') }}</button>
			</div>

			<div class="card-body">
                <div class="report-params">
                    <!-- Date Range Form -->
                    <form method="GET" class="validate" action="{{ route('reports.trialBalance') }}" class="mb-4">
                        <div class="row">
                            <div class="col-md-5">
                                <label for="from_date">{{ _lang('From Date') }}</label>
                                <input type="text" name="from_date" class="form-control datepicker" value="{{ $fromDate }}" readonly>
                            </div>
                            <div class="col-md-5">
                                <label for="to_date">{{ _lang('To Date') }}</label>
                                <input type="text" name="to_date" class="form-control datepicker" value="{{ $toDate }}" readonly>
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
						<p>{{ _lang('Profit & Loss Report') }}</p>
						<p>{{ isset($fromDate) ? date($date_format, strtotime($fromDate)).' '._lang('to').' '.date($date_format, strtotime($toDate)) : '----------  '._lang('to').'  ----------' }}</p>
					</div>

                    <div class="table-responsive mt-2">
                        <table class="table table-bordered">
                            <tbody>
                                <tr class="bg-light">
                                    <td colspan="2"><b>{{ _lang('Revenues') }}</b></td>
                                </tr>
                                @foreach($revenueData as $revenue)
                                    <tr>
                                        <td>{{ $revenue['account_name'] }}</td>
                                        <td class="text-right">{{ decimalPlace($revenue['total']) }}</td>
                                    </tr>
                                @endforeach
                                <tr>
                                    <td><b>{{ _lang('Total Revenue') }}</b></td>
                                    <td class="text-right"><b>{{ decimalPlace($totalRevenue, $currency_symbol) }}</b></td>
                                </tr>

                                <tr>
                                    <td colspan="2" class="border-none border-0">&nbsp;</td>
                                </tr>

                                <!--Expenses-->
                                <tr class="bg-light">
                                    <td colspan="2" class="text-dark"><b>{{ _lang('Expenses') }}</b></td>
                                </tr>

                                @foreach($expenseData as $expense)
                                    <tr>
                                        <td>{{ $expense['account_name'] }}</td>
                                        <td class="text-right">{{ decimalPlace($expense['total']) }}</td>
                                    </tr>
                                @endforeach
                                <tr>
                                    <td><b>{{ _lang('Total Expenses') }}</b></td>
                                    <td class="text-right"><b>{{ decimalPlace($totalExpenses, $currency_symbol) }}</b></td>
                                </tr>
                                <tr>
                                    <td><b>{{ $netProfitOrLoss >= 0 ? _lang('Net Profit') : _lang('Net Loss') }}</b></td>
                                    <td class="text-right"><b>{{ decimalPlace($netProfitOrLoss, $currency_symbol) }}</b></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                </div>

            </div>
        </div>
    </div>
</div>
@endsection
