@extends('layouts.app')

@section('content')
<div class="row">
	<div class="col-lg-8 offset-lg-2">
		<div class="card">
			<div class="card-header d-sm-flex align-items-center justify-content-between">
				<span class="panel-title">{{ _lang('Balance Sheet') }}</span>
				<button class="btn btn-outline-primary btn-xs print" data-print="report" type="button" id="report-print-btn"><i class="fas fa-print mr-1"></i>{{ _lang('Print Report') }}</button>
			</div>

			<div class="card-body">
                <div class="report-params">
                    <!-- Date Range Form -->
                    <form method="GET" class="validate" action="{{ route('reports.balanceSheet') }}" class="mb-4">
                        <div class="row">
                            <div class="col-md-5">
                                <label for="to_date">{{ _lang('End Date') }}</label>
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
						<p>{{ _lang('Balance Sheet') }}</p>
						<p>{{ isset($toDate) ? date($date_format, strtotime($toDate)) : '' }}</p>
					</div>

                    <div class="table-responsive mt-2">
                        <table class="table table-bordered">
                            <tbody>
                                <tr class="bg-light">
                                    <td><b>{{ _lang('Assets') }}</b></td>
                                    <td class="text-right"><b>{{ _lang('Amount') }}</b></td>
                                </tr>
                                @foreach($assetsData as $asset)
                                    <tr>
                                        <td>{{ $asset['account_name'] }}</td>
                                        <td class="text-right">{{ decimalPlace($asset['balance']) }}</td>
                                    </tr>
                                @endforeach
                                <tr>
                                    <td><b>{{ _lang('Total Assets') }}</b></td>
                                    <td class="text-right"><b>{{ decimalPlace(array_sum(array_column($assetsData, 'balance')), $currency_symbol) }}</b></td>
                                </tr>

                                <tr>
                                    <td colspan="2" class="border-none border-0">&nbsp;</td>
                                </tr>

                                <!--Liabilities-->
                                <tr class="bg-light">
                                    <td class="text-dark"><b>{{ _lang('Liabilities') }}</b></td>
                                    <td class="text-right"><b>{{ _lang('Amount') }}</b></td>
                                </tr>
                                @foreach($liabilitiesData as $liability)
                                    <tr>
                                        <td>{{ $liability['account_name'] }}</td>
                                        <td class="text-right">{{ decimalPlace($liability['balance']) }}</td>
                                    </tr>
                                @endforeach
                                <tr>
                                    <td><b>{{ _lang('Total Liabilities') }}</b></td>
                                    <td class="text-right"><b>{{ decimalPlace(array_sum(array_column($liabilitiesData, 'balance')), $currency_symbol) }}</b></td>
                                </tr>

                                <tr>
                                    <td colspan="2" class="border-none border-0">&nbsp;</td>
                                </tr>

                                <!--Equity-->
                                <tr class="bg-light">
                                    <td class="text-dark"><b>{{ _lang('Equity') }}</b></td>
                                    <td class="text-right"><b>{{ _lang('Amount') }}</b></td>
                                </tr>
                                @foreach($equityData as $equity)
                                    <tr>
                                        <td>{{ $equity['account_name'] }}</td>
                                        <td class="text-right">{{ decimalPlace($equity['balance']) }}</td>
                                    </tr>
                                @endforeach
                                <tr>
                                    <td>{{ $netProfitOrLoss >= 0 ? _lang('Net Profit') : _lang('Net Loss') }}</td>
                                    <td class="text-right">{{ decimalPlace($netProfitOrLoss) }}</td>
                                </tr>
                                <tr>
                                    <td><b>{{ _lang('Total Equity') }}<b></td>
                                    <td class="text-right"><b>{{ decimalPlace(array_sum(array_column($equityData, 'balance')) + $netProfitOrLoss, $currency_symbol) }}</b></td>
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
