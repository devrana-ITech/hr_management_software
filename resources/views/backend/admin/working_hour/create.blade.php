@extends('layouts.app')

@section('content')
<div class="row">
	@if(!isset($employees))
	<div class="{{ $alert_col }}">
		<div class="card">
			<div class="card-header">
				<span class="panel-title">{{ _lang('Manage Working Hour') }}</span>
			</div>
			<div class="card-body">
			    <form method="get" class="validate" autocomplete="off" action="{{ route('working_hours.create') }}">
					<div class="row">
						<div class="col-md-12">
							<div class="form-group">
								<label class="control-label">{{ _lang('Date') }}</label>
								<input type="text" class="form-control datepicker no-msg" name="date" value="{{ old('date') }}" required>
							</div>
						</div>

						<div class="col-md-12 mt-2">
							<div class="form-group">
								<button type="submit" class="btn btn-primary btn-block">{{ _lang('Next') }}</button>
							</div>
						</div>
					</div>
			    </form>
			</div>
		</div>
    </div>
	@else
	<div class="{{ $alert_col }}">
		@if($message != null)
		<div class="alert alert-danger">
			<strong>{{ $message }}</strong>
		</div>
		@endif
		<div class="card">
			<div class="card-header text-center">
				<span class="panel-title">{{ _lang('Manage Working Hour') }}</span>
			</div>
			<div class="card-body">
			    <form method="post" class="validate" autocomplete="off" action="{{ route('working_hours.store') }}" enctype="multipart/form-data">
					@csrf
					<input type="hidden" name="date" value="{{ $date }}">
					<div class="row">
						<div class="col-md-12">
							<div class="table-responsive">
								<table class="table table-bordered">
									<thead>
										<th>{{ _lang('Employee ID') }}</th>
										<th>{{ _lang('Name') }}</th>
										<th>{{ _lang('Clock In') }}</th>
										<th>{{ _lang('Clock Out') }}</th>
										<th>{{ _lang('Hour Deduct') }}</th>
										<th>{{ _lang('Leave') }}</th>
										<th>{{ _lang('Remarks') }}</th>
									</thead>
									<tbody>
										@foreach($employees as $employee)
										<tr>
											<td>{{ $employee->employee_id }}</td>
											<td>{{ $employee->name }}</td>
											<td>
												<input type="hidden" name="employee_id[]" value="{{ $employee->id }}">
												<input type="text" name="clock_in[]" class="form-control timepicker clock_in" value="{{ old('clock_in.'.$loop->index, $employee->clock_in) }}" required>	
											</td>
											<td><input type="text" name="clock_out[]" class="form-control timepicker clock_out @error('clock_out.'.$loop->index) is-invalid @enderror" value="{{ old('clock_out.'.$loop->index, $employee->clock_out) }}" required></td>
											<td><input type="text" name="hour_deduct[]" class="form-control float-field" value="{{ old('hour_deduct.'.$loop->index, $employee->hour_deduct ?? 0) }}" required></td>
											<td>
												{{ $employee->leave_duration != null ? ucwords(str_replace('_',' ',$employee->leave_duration)) : _lang('N/A') }}
											</td>
											<td>
												<textarea name="remarks[]" class="form-control">{{ old('hour_deduct.'.$loop->index, $employee->remarks) }}</textarea>
											</td>
										</tr>
										@endforeach
									</tbody>
								</table>
							</div>
						</div>

						<div class="col-md-12 mt-2">
							<div class="form-group">
								<button type="submit" class="btn btn-primary"><i class="ti-check-box mr-2"></i> {{ _lang('Submit') }}</button>
							</div>
						</div>
					</div>
			    </form>
			</div>
		</div>
    </div>
	@endif
</div>
@endsection



