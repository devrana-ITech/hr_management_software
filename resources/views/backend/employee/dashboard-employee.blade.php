@extends('layouts.app')

@section('content')
<div class="row">
	<div class="col-lg-3 col-md-6">
		<div class="card mb-4 dashboard-card">
			<div class="card-body">
				<div class="d-flex">
					<div class="flex-grow-1">
						<h5>{{ _lang('Available Leaves') }}</h5>
						<h4 class="pt-1 mb-0"><b>{{ $available_leave }}</b></h4>
					</div>
					<div class="ml-2 text-center">
						<i class="fas fa-calendar-alt bg-success text-white"></i>
					</div>
				</div>
			</div>
		</div>
	</div>

	<div class="col-lg-3 col-md-6">
		<div class="card mb-4 dashboard-card">
			<div class="card-body">
				<div class="d-flex">
					<div class="flex-grow-1">
						<h5>{{ _lang('Leave Taken') }}</h5>
						<h4 class="pt-1 mb-0"><b>{{ $leave_taken }}</b></h4>
					</div>
					<div class="ml-2 text-center">
						<i class="fas fa-calendar-check bg-primary text-white"></i>
					</div>
				</div>
			</div>
		</div>
	</div>

	<div class="col-lg-3 col-md-6">
		<div class="card mb-4 dashboard-card">
			<div class="card-body">
				<div class="d-flex">
					<div class="flex-grow-1">
						<h5>{{ _lang('Absent') }}</h5>
						<h4 class="pt-1 mb-0"><b>{{ $absent }}</b></h4>
					</div>
					<div class="ml-2 text-center">
						<i class="fas fa-calendar-times bg-danger text-white"></i>
					</div>
				</div>
			</div>
		</div>
	</div>

	<div class="col-lg-3 col-md-6">
		<div class="card mb-4 dashboard-card">
			<div class="card-body">
				<div class="d-flex">
					<div class="flex-grow-1">
						<h5>{{ _lang('Loan Balance') }}</h5>
						<h4 class="pt-1 mb-0"><b>{{ decimalPlace($loan_balance, currency_symbol()) }}</b></h4>
					</div>
					<div class="ml-2 text-center">
						<i class="fas fa-coins bg-warning text-white"></i>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<div class="row">
	<div class="col-lg-6">
		<div class="card">
			<div class="card-header">
				{{ _lang('Upcoming Holidays') }}
			</div>
			<div class="card-body px-0 pt-0">
				<table class="table">
					<thead class="bg-light">
						<tr>
							<th class="text-dark pl-4">{{ _lang('Title') }}</th>
							<th class="text-dark">{{ _lang('Date') }}</th>
						</tr>
					</thead>
					<tbody>
						@if($holidays->count() == 0)
						<tr>
							<td colspan="2" class="text-center">{{ _lang('No holiday available') }}</td>
						</tr>
						@endif
						@foreach($holidays as $holiday)
						<tr>
							<td class="pl-4">{{ $holiday->title }}</td>
							<td>{{ $holiday->date }}</td>
						</tr>
						@endforeach
					</tbody>
				</table>
			</div>
		</div>
	</div>

	<div class="col-lg-6">
		<div class="card">
			<div class="card-header">
				{{ _lang('Notice Board') }}
			</div>
			<div class="card-body px-0 pt-0">
				<table class="table">
					<thead class="bg-light">
						<tr>
							<th class="text-dark pl-4">{{ _lang('Title') }}</th>
							<th class="text-dark">{{ _lang('Date') }}</th>
							<th class="text-dark"></th>
						</tr>
					</thead>
					<tbody>
					@if($notices->count() == 0)
						<tr>
							<td colspan="2" class="text-center">{{ _lang('No notice available') }}</td>
						</tr>
					@endif
					@foreach($notices as $notice)
						<tr>
							<td class="pl-4">{{ $notice->title }}</td>
							<td>{{ $notice->created_at }}</td>
							<td class="text-center"><a href="{{ route('notices.details', $notice->id) }}"><i class="fas fa-long-arrow-alt-right mr-1"></i>{{ _lang('View') }}</a></td>
						</tr>
					@endforeach
					</tbody>
				</table>
				<div class="pl-3">{{ $notices->links() }}</div>
			</div>
		</div>
	</div>
</div>
@endsection
