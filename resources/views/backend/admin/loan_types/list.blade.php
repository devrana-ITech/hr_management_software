@extends('layouts.app')

@section('content')

<div class="row">
	<div class="col-lg-12">
		<div class="card">
		    <div class="card-header d-flex align-items-center">
				<span class="panel-title">{{ _lang('Loan Types') }}</span>
				<a class="btn btn-primary btn-xs ml-auto ajax-modal" data-title="{{ _lang('Add Loan Type') }}" href="{{ route('employee_loan_types.create') }}"><i class="ti-plus"></i> {{ _lang('Add New') }}</a>
			</div>
			<div class="card-body">
				<table id="employee_loan_types_table" class="table data-table">
					<thead>
					    <tr>
						    <th>{{ _lang('Name') }}</th>
							<th>{{ _lang('Minimum Amount') }}</th>
							<th>{{ _lang('Maximum Amount') }}</th>
							<th>{{ _lang('Interest Rate') }}</th>
							<th>{{ _lang('Interest Type') }}</th>
							<th>{{ _lang('Term') }}</th>
							<th class="text-center">{{ _lang('Action') }}</th>
					    </tr>
					</thead>
					<tbody>
					    @foreach($loantypes as $loantype)
					    <tr data-id="row_{{ $loantype->id }}">
							<td class='name'>{{ $loantype->name }}</td>
							<td class='minimum_amount'>{{ decimalPlace($loantype->minimum_amount, currency_symbol()) }}</td>
							<td class='maximum_amount'>{{ decimalPlace($loantype->maximum_amount, currency_symbol()) }}</td>
							<td class='interest_rate'>{{ $loantype->interest_rate }}%</td>
							<td class='interest_type'>{{ ucwords($loantype->interest_type) }}</td>
							<td class='term'>{{ $loantype->term }}</td>
							
							<td class="text-center">
								<span class="dropdown">
								  <button class="btn btn-primary dropdown-toggle btn-xs" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
								  {{ _lang('Action') }}
								  </button>
								  <form action="{{ route('employee_loan_types.destroy', $loantype['id']) }}" method="post">
									{{ csrf_field() }}
									<input name="_method" type="hidden" value="DELETE">

									<div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
										<a href="{{ route('employee_loan_types.edit', $loantype['id']) }}" data-title="{{ _lang('Update Loan Type') }}" class="dropdown-item dropdown-edit ajax-modal"><i class="fas fa-pencil-alt"></i> {{ _lang('Edit') }}</a>
										<a href="{{ route('employee_loan_types.show', $loantype['id']) }}" data-title="{{ _lang('Loan Type Details') }}" class="dropdown-item dropdown-view ajax-modal"><i class="fas fa-eye"></i> {{ _lang('View') }}</a>
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