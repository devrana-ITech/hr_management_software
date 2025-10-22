@extends('layouts.app')

@section('content')
<div class="row">
	<div class="col-xl-3 col-md-6">
		<div class="card mb-4 dashboard-card">
			<div class="card-body">
				<div class="d-flex">
					<div class="flex-grow-1">
						<h5>{{ _lang('Active Employees') }}</h5>
						<h4 class="pt-1 mb-0"><b>{{ $active_employees }}</b></h4>
					</div>
					<div class="ml-2 text-center">
						<i class="fas fa-users bg-success text-white"></i>
					</div>
				</div>
			</div>
		</div>
	</div>

	<div class="col-xl-3 col-md-6">
		<a href="{{ route('leaves.index') }}">
			<div class="card mb-4 dashboard-card">
				<div class="card-body">
					<div class="d-flex">
						<div class="flex-grow-1">
							<h5>{{ _lang('Leave Application') }}</h5>
							<h4 class="pt-1 mb-0"><b>{{ request_count('leave_application') }}</b></h4>
						</div>
						<div class="ml-2 text-center">
							<i class="fas fa-calendar-alt bg-info text-white"></i>
						</div>
					</div>
				</div>
			</div>
		</a>
	</div>

	<div class="col-xl-3 col-md-6">
		<a href="{{ route('employee_loans.index') }}">
			<div class="card mb-4 dashboard-card">
				<div class="card-body">
					<div class="d-flex">
						<div class="flex-grow-1">
							<h5>{{ _lang('Loan Application') }}</h5>
							<h4 class="pt-1 mb-0"><b>{{ request_count('loan_application') }}</b></h4>
						</div>
						<div class="ml-2 text-center">
							<i class="fas fa-coins bg-primary text-white"></i>
						</div>
					</div>
				</div>
			</div>
		</a>
	</div>

	<div class="col-xl-3 col-md-6">
		<a href="{{ route('employee_expenses.index') }}">
			<div class="card mb-4 dashboard-card">
				<div class="card-body">
					<div class="d-flex">
						<div class="flex-grow-1">
							<h5>{{ _lang('Expenses Requests') }}</h5>
							<h4 class="pt-1 mb-0"><b>{{ request_count('pending_expenses') }}</b></h4>
						</div>
						<div class="ml-2 text-center">
							<i class="fas fa-dollar-sign bg-danger text-white"></i>
						</div>
					</div>
				</div>
			</div>
		</a>
	</div>
</div>

<div class="row">
	<div class="col-lg-12">
		<div class="card mb-4">
			<div class="card-header d-flex justify-content-between align-items-center">
				{{ _lang('Profit & Loss').' - '._lang('Year of').' '.date('Y')  }}
				<a href="{{ route('reports.profitAndLoss') }}" class="btn btn-outline-primary btn-xs"><i class="far fa-chart-bar mr-1"></i>{{ _lang('View Report') }}</a>
			</div>
			<div class="card-body">
				<h5 class="text-center loading-chart"><i class="fas fa-spinner fa-spin"></i> {{ _lang('Loading Chart') }}</h5>
				<canvas id="transactionAnalysis" style="height: 350px"></canvas>
			</div>
		</div>
	</div>
</div>

<div class="row">
	<div class="col-lg-12">
		<div class="card mb-4">
			<div class="card-header">
				{{ _lang('Recent Transactions') }}
			</div>
			<div class="card-body p-0">
				<table class="table">
					<thead class="bg-light">
						<tr>
							<th class="text-dark pl-4">{{ _lang('Date') }}</th>
						    <th class="text-dark">{{ _lang('Description') }}</th>
						    <th class="text-dark">{{ _lang('Amount') }}</th>
                            <th class="text-dark text-center">{{ _lang('Action') }}</th>
						</tr>
					</thead>
					<tbody>
						@foreach($transactions as $transaction)
					    <tr data-id="row_{{ $transaction->id }}">
							<td class='created_at pl-4'>{{ $transaction->transaction_date }}</td>
							<td class='description'>{{ $transaction->description }}</td>	
							<td class='amount'>{{ decimalPlace($transaction->entries()->where('type', 'debit')->sum('amount'), currency_symbol()) }}</td>	
							<td class="text-center">
								<span class="dropdown">
								  <button class="btn btn-outline-primary dropdown-toggle btn-xs" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
								  {{ _lang('Action') }}
								  </button>
								  <form action="{{ route('transactions.destroy', $transaction['id']) }}" method="post">
									{{ csrf_field() }}
									<input name="_method" type="hidden" value="DELETE">

									<div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
										<a href="{{ route('transactions.show', $transaction['id']) }}" class="dropdown-item dropdown-edit"><i class="fas fa-eye"></i> {{ _lang('Details') }}</a>
										<a href="{{ route('transactions.edit', $transaction['id']) }}" class="dropdown-item dropdown-edit"><i class="fas fa-pencil-alt"></i> {{ _lang('Edit') }}</a>
										<button class="btn-remove dropdown-item" type="submit"><i class="fas fa-trash-alt"></i> {{ _lang('Delete') }}</button>
									</div>
								  </form>
								</span>
							</td>
					    </tr>
					    @endforeach
					</tbody>
				</table>
			</div>
		</div>
	</div>
</div>
@endsection

@section('js-script')
<script src="{{ asset('public/backend/plugins/chartJs/chart.min.js') }}"></script>
<script src="{{ asset('public/backend/assets/js/dashboard.js?v=1.1') }}"></script>
@endsection
