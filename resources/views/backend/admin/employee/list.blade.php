@extends('layouts.app')

@section('content')

<div class="row">
	<div class="col-lg-12">
		<div class="card">
		    <div class="card-header d-flex align-items-center">
				<span class="panel-title">{{ _lang('Employees') }}</span>
				<a class="btn btn-primary btn-xs ml-auto" href="{{ route('employees.create') }}"><i class="ti-plus"></i> {{ _lang('Add New') }}</a>
			</div>

{{--
<div class="row align-items-center mb-3">
  <div class="col-md-6 col-sm-12">
    <div class="dataTables_length" id="DataTables_Table_0_length">
      <label class="d-flex align-items-center">
        <span class="me-2 ml-3 mt-3 mr-2">Show</span>
        <select name="DataTables_Table_0_length" aria-controls="DataTables_Table_0"
          class="form-select form-select-lg w-auto mt-3">
          <option value="10">10</option>
          <option value="25">25</option>
          <option value="50">50</option>
          <option value="100">100</option>
        </select>
        <span class="ms-2 mt-3 mr-2 ml-2">entries</span>
      </label>
    </div>
  </div> --}}


    <x-search
        :perPage="$perPage"
        :value="$search ?? ''"
        :placeholder="'Search by name...'"
        :action="route('employees.index')" />


  {{-- <div class="col-md-6 col-sm-12 text-md-end mt-2 mt-md-0">
    <label class="d-flex align-items-center justify-content-md-end">
      <span class="me-2 mt-3 mr-1">Search:</span>
      <input type="search" class="form-control form-control-sm w-auto mt-3 mr-3"
        placeholder="Type to search..." aria-controls="DataTables_Table_0">
    </label>
  </div>
</div> --}}

{{-- <x-search/> --}}


			<div class="card-body">
				<table id="employees_table" class="table tableBody">
					<thead>
					    <tr>
						    <th>{{ _lang('Employee ID') }}</th>
							<th>{{ _lang('First Name') }}</th>
							<th>{{ _lang('Last Name') }}</th>
							<th>{{ _lang('Department') }}</th>
							<th>{{ _lang('Designation') }}</th>
							<th>{{ _lang('Joining Date') }}</th>
							<th>{{ _lang('End Date') }}</th>
							<th class="text-center">{{ _lang('Action') }}</th>
					    </tr>
					</thead>
					<tbody>
					    @foreach($employees as $employee)
					    <tr data-id="row_{{ $employee->id }}">
							<td class='name'>{{ $employee->employee_id }}</td>
							<td class='name'>{{ $employee->first_name }}</td>
							<td class='descriptions'>{{ $employee->last_name }}</td>
							<td class='descriptions'>{{ $employee->department->name }}</td>
							<td class='descriptions'>{{ $employee->designation->name }}</td>
							<td class='descriptions'>{{ $employee->joining_date }}</td>
							<td class='descriptions'>{{ $employee->end_date }}</td>
							<td class="text-center">
								<span class="dropdown">
								  <button class="btn btn-outline-primary dropdown-toggle btn-xs" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
								  {{ _lang('Action') }}
								  </button>
								  <form action="{{ route('employees.destroy', $employee['id']) }}" method="post">
									{{ csrf_field() }}
									<input name="_method" type="hidden" value="DELETE">

									<div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
										<a href="{{ route('employees.edit', $employee['id']) }}" data-title="{{ _lang('Update Department') }}" class="dropdown-item dropdown-edit ajax-modal"><i class="fas fa-pencil-alt"></i> {{ _lang('Edit') }}</a>
										<a href="{{ route('employees.show', $employee['id']) }}" data-title="{{ _lang('Department Details') }}" class="dropdown-item dropdown-view ajax-modal"><i class="fas fa-eye"></i> {{ _lang('Details') }}</a>
										<button class="btn-remove dropdown-item" type="submit"><i class="fas fa-trash-alt"></i> {{ _lang('Delete') }}</button>
									</div>
								  </form>
								</span>
							</td>
					    </tr>
					    @endforeach
					</tbody>
				</table>
                <div class="d-flex justify-content-end">{{ $employees->links() }}</div>

			</div>
		</div>
	</div>
</div>

@endsection

@section('js-script')
<script>
(function ($) {
	"use strict";
	$('#employees_table').DataTable({
		processing: true,
		serverSide: true,
		ajax: "{{ route('employees.get_table_data') }}",
		"columns" : [
			{ data : 'employee_id', name : 'employee_id' },
			{ data : 'first_name', name : 'first_name' },
			{ data : 'last_name', name : 'last_name' },
			{ data : 'department.name', name : 'department.name' },
			{ data : 'designation.name', name : 'designation.name' },
			{ data : 'basic_salary', name : 'basic_salary' },
			{ data : "action", name : "action" },
		],
		responsive: true,
		"bStateSave": true,
		"bAutoWidth":false,
		"ordering": true,
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
