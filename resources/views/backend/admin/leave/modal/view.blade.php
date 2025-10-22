<div class="row p-2">
	<div class="col-lg-12">
		<table class="table table-bordered">
			<tr><td>{{ _lang('Employee ID') }}</td><td>{{ $leave->staff->employee_id }}</td></tr>
			<tr><td>{{ _lang('Leave Type') }}</td><td>{{ $leave->leave_type }}</td></tr>
			<tr>
				<td class="text-nowrap">{{ _lang('Leave Duration') }}</td>
				<td>{{ $leave->leave_duration == 'full_day' ? _lang('Full Day') : _lang('Half Day') }}</td>
			</tr>
			<tr><td>{{ _lang('Start Date') }}</td><td>{{ $leave->start_date }}</td></tr>
			<tr><td>{{ _lang('End Date') }}</td><td>{{ $leave->end_date }}</td></tr>
			<tr><td>{{ _lang('Total Days') }}</td><td>{{ $leave->total_days }}</td></tr>
			<tr><td class="text-nowrap">{{ _lang('Leave Details') }}</td><td>{{ $leave->description }}</td></tr>
			<tr><td>{{ _lang('Status') }}</td><td>{!! xss_clean(leave_status($leave->status)) !!}</td></tr>
		</table>

		<div class="d-flex justify-content-between">
			<a href="{{ route('leaves.approve', $leave['id']) }}" class="btn btn-primary"><i class="fas fa-check-circle"></i> {{ _lang('Approve') }}</a>
			<a href="{{ route('leaves.reject', $leave['id']) }}" class="btn btn-danger btn-remove-2" data-message="{{ _lang("You will not be able to approve once you reject the application") }}"><i class="fas fa-times-circle"></i> {{ _lang('Reject') }}</a>
		</td>
	</div>
</div>

