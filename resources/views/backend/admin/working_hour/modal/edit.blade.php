<form method="post" class="ajax-submit" autocomplete="off" action="{{ route('working_hours.update', $id) }}">
    {{ csrf_field()}}
    <input name="_method" type="hidden" value="PATCH">
    <div class="row px-2">		
        <div class="col-lg-6">
            <div class="form-group">
                <label class="control-label">{{ _lang('Employee ID') }}</label>						
                <input type="text" class="form-control" value="{{ $workinghour->staff->employee_id }}" readonly>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="form-group">
                <label class="control-label">{{ _lang('Employee Name') }}</label>						
                <input type="text" class="form-control" value="{{ $workinghour->staff->name }}" readonly>
            </div>
        </div>

        <div class="col-lg-12">
            <div class="form-group">
                <label class="control-label">{{ _lang('Date') }}</label>						
                <input type="text" class="form-control datepicker" name="date" value="{{ $workinghour->getRawOriginal('date') }}">
            </div>
        </div>

        <div class="col-lg-12">
            <div class="form-group">
                <label class="control-label">{{ _lang('Clock In') }}</label>						
                <input type="text" class="form-control timepicker" name="clock_in" value="{{ $workinghour->clock_in }}">
            </div>
        </div>

        <div class="col-lg-12">
            <div class="form-group">
                <label class="control-label">{{ _lang('Clock Out') }}</label>						
                <input type="text" class="form-control timepicker" name="clock_out" value="{{ $workinghour->clock_out }}">
            </div>
        </div>

        <div class="col-lg-12">
            <div class="form-group">
                <label class="control-label">{{ _lang('Hour Deduct') }}</label>						
                <input type="text" class="form-control" name="hour_deduct" value="{{ $workinghour->hour_deduct }}">
            </div>
        </div>

        <div class="col-lg-12">
            <div class="form-group">
                <label class="control-label">{{ _lang('Remarks') }}</label>						
                <textarea class="form-control" name="remarks">{{ $workinghour->remarks }}</textarea>
            </div>
        </div>
            
        <div class="col-lg-12 mt-2">
            <div class="form-group">
                <button type="submit" class="btn btn-primary"><i class="ti-check-box mr-2"></i> {{ _lang('Update') }}</button>
            </div>
        </div>
    </div>
</form>