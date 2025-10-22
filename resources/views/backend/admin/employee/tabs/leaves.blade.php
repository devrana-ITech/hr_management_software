<div class="card">
    <div class="card-header text-center">
        <span class="panel-title">{{ _lang('Leaves') }}</span>
    </div>
    
    <div class="card-body">
        <div class="table-responsive">
            <table id="payslips_table" class="table table-bordered">
                <thead>
                    <tr>
                        <th>{{ _lang('Employee ID') }}</th>
                        <th>{{ _lang('Leave Type') }}</th>
                        <th>{{ _lang('Leave Duration') }}</th>
                        <th>{{ _lang('Start Date') }}</th>
                        <th>{{ _lang('End Date') }}</th>
                        <th>{{ _lang('Total') }}</th>
                        <th class="text-center">{{ _lang('Status') }}</th>
                        <th class="text-center">{{ _lang('Action') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($leaves as $leave)
                    <tr>
                        <td>{{ $employee->employee_id }}</td>
                        <td>{{ $leave->leave_type }}</td>
                        <td>{{ $leave->leave_duration == 'full_day' ? _lang('Full Day') : _lang('Half Day') }}</td>
                        <td>{{ $leave->start_date }}</td>
                        <td>{{ $leave->end_date }}</td>
                        <td>{{ $leave->total_days . ' ' . _lang('days') }}</td>
                        <td class="text-center">{!! xss_clean(leave_status($leave->status)) !!}</td>
                        <td class="text-center">
                            <div class="dropdown text-center">
                                <button class="btn btn-outline-primary btn-xs dropdown-toggle" type="button" data-toggle="dropdown">{{ _lang('Action') }}</button>
                                <div class="dropdown-menu">
                                    <a class="dropdown-item ajax-modal" data-title="{{ _lang('Update Leave') }}" href="{{ route('leaves.edit', $leave['id']) }}"><i class="fas fa-pencil-alt mr-1"></i>{{ _lang('Edit') }}</a>
                                    <a class="dropdown-item ajax-modal" data-title="{{ _lang('Leave Details') }}" href="{{ route('leaves.show', $leave['id']) }}"><i class="fas fa-eye mr-1"></i> {{ _lang('Details') }}</a>
                                    <form action="{{ route('leaves.destroy', $leave['id']) }}" method="post">
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
            {{ $leaves->links() }}
        </div>
    </div>
</div>