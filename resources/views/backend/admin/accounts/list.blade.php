@extends('layouts.app')

@section('content')
<div class="row">
	<div class="col-lg-12">
		<div class="card">
		    <div class="card-header d-flex align-items-center">
				<span class="panel-title">{{ _lang('Chart Of Accounts') }}</span>
				<a class="btn btn-primary btn-xs ml-auto ajax-modal-2" data-title="{{ _lang('Add Account') }}" href="{{ route('accounts.create') }}"><i class="ti-plus"></i> {{ _lang('Add New') }}</a>
			</div>
			<div class="card-body">
				<table id="accounts_table" class="table data-table">
					<thead>
					    <tr>
						    <th>{{ _lang('Account ID') }}</th>
						    <th>{{ _lang('Name') }}</th>
                            <th>{{ _lang('Type') }}</th>
                            <th class="text-center">{{ _lang('Action') }}</th>
					    </tr>
					</thead>
					<tbody>
					    @foreach($accounts as $account)
					    <tr data-id="row_{{ $account->id }}">
							<td class='account_id'>{{ $account->account_id }}</td>
							<td class='name'>
								<span class="fw-500">{{ $account->name }}</span>
								<small class="text-black-50 font-italic"><br>{{ str_replace('_', ' ', $account->slug) }}</small>
							</td>
							<td class='type'>{{ ucfirst($account->type) }}</td>
							
							<td class="text-center">
								<span class="dropdown">
								  <button class="btn btn-outline-primary dropdown-toggle btn-xs" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
								  {{ _lang('Action') }}
								  </button>
								  @if($account->is_default == 0)
								  <form action="{{ route('accounts.destroy', $account['id']) }}" method="post">
									{{ csrf_field() }}
									<input name="_method" type="hidden" value="DELETE">

									<div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
										<a href="{{ route('accounts.edit', $account['id']) }}" data-title="{{ _lang('Update Account') }}" class="dropdown-item dropdown-edit ajax-modal"><i class="fas fa-pencil-alt"></i> {{ _lang('Edit') }}</a>
										<button class="dropdown-item" type="submit"><i class="fas fa-trash-alt"></i> {{ _lang('Delete') }}</button>
									</div>
								  </form>
								  @else
								  	<div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
										<a href="{{ route('accounts.edit', $account['id']) }}" data-title="{{ _lang('Update Account') }}" class="dropdown-item dropdown-edit ajax-modal"><i class="fas fa-pencil-alt"></i> {{ _lang('Edit') }}</a>
										<button class="dropdown-item" type="submit" disabled><i class="fas fa-trash-alt"></i> {{ _lang('Delete') }}</button>
									</div>
								  @endif
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