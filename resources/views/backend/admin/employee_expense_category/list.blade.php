@extends('layouts.app')

@section('content')

<div class="row">
	<div class="col-lg-12">
		<div class="card">
		    <div class="card-header d-flex align-items-center">
				<span class="panel-title">{{ _lang('Expense Categories') }}</span>
				<a class="btn btn-primary btn-xs ml-auto ajax-modal" data-title="{{ _lang('Add Category') }}" href="{{ route('employee_expense_categories.create') }}"><i class="ti-plus"></i> {{ _lang('Add New') }}</a>
			</div>
			<div class="card-body">
				<table id="employee_expense_categories_table" class="table data-table">
					<thead>
					    <tr>
						    <th>{{ _lang('Name') }}</th>
							<th>{{ _lang('Description') }}</th>
							<th class="text-center">{{ _lang('Action') }}</th>
					    </tr>
					</thead>
					<tbody>
					    @foreach($expensecategorys as $expensecategory)
					    <tr data-id="row_{{ $expensecategory->id }}">
							<td class='name'>{{ $expensecategory->name }}</td>
							<td class='description'>{{ $expensecategory->description }}</td>
							
							<td class="text-center">
								<span class="dropdown">
								  <button class="btn btn-primary dropdown-toggle btn-xs" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
								  {{ _lang('Action') }}
								  </button>
								  <form action="{{ route('employee_expense_categories.destroy', $expensecategory['id']) }}" method="post">
									{{ csrf_field() }}
									<input name="_method" type="hidden" value="DELETE">

									<div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
										<a href="{{ route('employee_expense_categories.edit', $expensecategory['id']) }}" data-title="{{ _lang('Update Category') }}" class="dropdown-item dropdown-edit ajax-modal"><i class="fas fa-pencil-alt"></i> {{ _lang('Edit') }}</a>
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