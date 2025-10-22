@extends('layouts.app')

@section('content')
<div class="row">
	<div class="col-lg-8 offset-lg-2">
		<div class="card">
			<div class="card-header d-sm-flex align-items-center justify-content-between">
				<span class="panel-title">{{ _lang('Trial Balance') }}</span>
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
						<p>{{ _lang('Trial Balance') }}</p>
						<p>{{ isset($fromDate) ? date($date_format, strtotime($fromDate)).' '._lang('to').' '.date($date_format, strtotime($toDate)) : '----------  '._lang('to').'  ----------' }}</p>
					</div>
					
					<div class="table-responsive">
                        <table class="table table-bordered">
                            <thead class="bg-light">
                                <tr>
                                    <th class="text-dark">{{ _lang('Account') }}</th>
                                    <th class="text-dark text-right">{{ _lang('Debit') }}</th>
                                    <th class="text-dark text-right">{{ _lang('Credit') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($trialBalance as $balance)
                                    <tr>
                                        <td>{{ $balance['account_name'] }}</td>
                                        <td class="text-right">{{ decimalPlace($balance['debit']) }}</td>
                                        <td class="text-right">{{ decimalPlace($balance['credit']) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th>{{ _lang('Total') }}</th>
                                    <th class="text-right">{{ decimalPlace($totalDebits, $currency_symbol) }}</th>
                                    <th class="text-right">{{ decimalPlace($totalCredits, $currency_symbol) }}</th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
