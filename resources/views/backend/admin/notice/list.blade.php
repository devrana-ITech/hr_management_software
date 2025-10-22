@extends('layouts.app')

@section('content')
<div class="row">
	<div class="col-lg-12">
		<div class="card">
		    <div class="card-header d-flex align-items-center">
				<span class="panel-title">{{ _lang('Notice List') }}</span>
				<a class="btn btn-primary btn-xs ml-auto" href="{{ route('notices.create') }}"><i class="ti-plus"></i> {{ _lang('Add New') }}</a>
			</div>
			<div class="card-body">
				<table id="notices_table" class="table">
					<thead>
					    <tr>
						    <th>{{ _lang('Title') }}</th>
							<th>{{ _lang('Status') }}</th>
							<th>{{ _lang('Created') }}</th>
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

	$('#notices_table').DataTable({
		processing: true,
		serverSide: true,
		ajax: _url + '/admin/notices/get_table_data',
		"columns" : [
			{ data : 'title', name : 'title' },
			{ data : 'status', name : 'status' },
			{ data : 'created_by.name', name : 'created_by.name' },
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
			  "next":       "{{ _lang('Next') }}",
			  "previous":   "{{ _lang('Previous') }}"
		  }
		}
	});
})(jQuery);
</script>
@endsection