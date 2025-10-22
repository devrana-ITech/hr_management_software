<div class="card">
    <div class="card-header text-center">
        <span class="panel-title">{{ _lang('Awards') }}</span>
    </div>
    
    <div class="card-body">
        <div class="table-responsive">
            <table id="payslips_table" class="table table-bordered">
                <thead>
                    <tr>
                        <th>{{ _lang('Employee ID') }}</th>
                        <th>{{ _lang('Award Date') }}</th>
                        <th>{{ _lang('Award Name') }}</th>
                        <th>{{ _lang('Award') }}</th>
                        <th class="text-center">{{ _lang('Action') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($awards as $award)
                    <tr>
                        <td>{{ $employee->employee_id }}</td>
                        <td>{{ $award->award_date }}</td>
                        <td>{{ $award->name }}</td>
                        <td>{{ $award->award }}</td>
                        <td class="text-center">
                            <div class="dropdown text-center">
                                <button class="btn btn-outline-primary btn-xs dropdown-toggle" type="button" data-toggle="dropdown">{{ _lang('Action') }}</button>
                                <div class="dropdown-menu">
                                    <a class="dropdown-item ajax-modal" data-title="{{ _lang('Update Award') }}" href="{{ route('awards.edit', $award['id']) }}"><i class="fas fa-pencil-alt mr-1"></i>{{ _lang('Edit') }}</a>
                                    <a class="dropdown-item ajax-modal" data-title="{{ _lang('Award Details') }}" href="{{ route('awards.show', $award['id']) }}"><i class="fas fa-eye mr-1"></i> {{ _lang('Details') }}</a>
                                    <form action="{{ route('awards.destroy', $award['id']) }}" method="post">
                                        @csrf
                                        <input name="_method" type="hidden" value="DELETE">
                                        <button class="dropdown-item btn-remove" type="submit"><i class="fas fa-trash-alt mr-1"></i> {{ _lang('Delete') }}</button>
                                    </form>
                                </div>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="float-right">
            {{ $awards->links() }}
        </div>
    </div>
</div>