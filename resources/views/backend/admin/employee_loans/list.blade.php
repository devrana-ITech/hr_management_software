@extends('layouts.app')

@section('content')
<div class="row">
	<div class="col-lg-12">
		<div class="card">
		    <div class="card-header d-flex align-items-center">
				<span class="panel-title">{{ _lang('Employee Loans') }}</span>
				<a class="btn btn-primary btn-xs ml-auto" data-title="{{ _lang('Add New Loan') }}" href="{{ route('employee_loans.create') }}"><i class="ti-plus"></i> {{ _lang('Add New') }}</a>
			</div>
			<div class="card-body">
				<table id="employee_loans_table" class="table">
					<thead>
					    <tr>
						    <th>{{ _lang('Date') }}</th>
						    <th>{{ _lang('Loan ID') }}</th>
							<th>{{ _lang('Employee') }}</th>
							<th>{{ _lang('Loan Amount') }}</th>
							<th>{{ _lang('Remaining Balance') }}</th>
							<th>{{ _lang('Interest Rate') }}</th>
							<th>{{ _lang('Monthly Installment') }}</th>
							<th>{{ _lang('Status') }}</th>
							<th class="text-center">{{ _lang('Action') }}</th>
					    </tr>
					</thead>
					<tbody>
					</tbody>
				</table>
			</div>
		</div>
	</div>
</div>
@endsection

@section('js-script')
<script>
(function ($) {
	"use strict";

	$('#employee_loans_table').DataTable({
		processing: true,
		serverSide: true,
		ajax: _url + '/admin/employee_loans/get_table_data/{{ $status }}',
		"columns" : [
			{ data : 'application_date', name : 'application_date' },
			{ data : 'loan_id', name : 'loan_id' },
			{ data : 'employee.first_name', name : 'employee.first_name' },
			{ data : 'loan_amount', name : 'loan_amount' },
			{ data : 'remaining_balance', name : 'remaining_balance' },
			{ data : 'interest_rate', name : 'interest_rate' },
			{ data : 'monthly_installment', name : 'monthly_installment' },
			{ data : 'status', name : 'status' },
			{ data : "action", name : "action" },
		],
		responsive: true,
		"bStateSave": true,
		"bAutoWidth":false,
		"ordering": false,
		"language": {
		   "decimal":        "",
		   "emptyTable":     "{{ _lang('No Data Found') }}",
		   "info":           "{{ _lang('Showing') }} _START_ {{ _lang('to') }} _END_ {{ _lang('of') }} _TOTAL_ {{ _lang('Entries') }}",
		   "infoEmpty":      "{{ _lang('Showing 0 To 0 Of 0 Entries') }}",
		   "infoFiltered":   "(filtered from _MAX_ total entries)",
		   "infoPostFix":    "",
		   "thousands":      ",",
		   "lengthMenu":     "{{ _lang('Show') }} _MENU_ {{ _lang('Entries') }}",
		   "loadingRecords": "{{ _lang('Loading...') }}",
		   "processing":     "{{ _lang('Processing...') }}",
		   "search":         "{{ _lang('Search') }}",
		   "zeroRecords":    "{{ _lang('No matching records found') }}",
		   "paginate": {
			  "first":      "{{ _lang('First') }}",
			  "last":       "{{ _lang('Last') }}",
			  "previous":   "<i class='fas fa-angle-left'></i>",
			  "next":       "<i class='fas fa-angle-right'></i>"
		  }
		}
	});
})(jQuery);
</script>
@endsection