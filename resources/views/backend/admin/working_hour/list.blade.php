@extends('layouts.app')

@section('content')

<div class="row">
	<div class="col-lg-12">
		<div class="card">
		    <div class="card-header d-flex align-items-center">
				<span class="panel-title">{{ _lang('Work Hour History') }}</span>
				<a class="btn btn-primary btn-xs ml-auto" href="{{ route('working_hours.create') }}"><i class="ti-plus"></i> {{ _lang('Add New') }}</a>
			</div>
			<div class="card-body">
				<table id="working_hours_table" class="table">
					<thead>
					    <tr>
							<th>{{ _lang('Date') }}</th>
						    <th>{{ _lang('Employee ID') }}</th>
						    <th>{{ _lang('Name') }}</th>
							<th>{{ _lang('Clock In') }}</th>
							<th>{{ _lang('Clock Out') }}</th>
							<th>{{ _lang('Work Hour') }}</th>
							<th>{{ _lang('Hour Deduct') }}</th>
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

	$('#working_hours_table').DataTable({
		processing: true,
		serverSide: true,
		ajax: _url + '/admin/working_hours/get_table_data',
		"columns" : [
			{ data : 'date', name : 'date' },
			{ data : 'staff.employee_id', name : 'staff.employee_id' },
			{ data : 'staff.first_name', name : 'staff.first_name' },
			{ data : 'clock_in', name : 'clock_in' },
			{ data : 'clock_out', name : 'clock_out' },
			{ data : 'work_hour', name : 'work_hour' },
			{ data : 'hour_deduct', name : 'hour_deduct' },
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