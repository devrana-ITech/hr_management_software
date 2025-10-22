@extends('layouts.app')

@section('content')
<div class="row">
	<div class="{{ $alert_col }}">
		<div class="card">
			<div class="card-header">
				<span class="panel-title">{{ _lang('Post New Notice') }}</span>
			</div>
			<div class="card-body">
			    <form method="post" class="validate" autocomplete="off" action="{{ route('notices.store') }}" enctype="multipart/form-data">
					{{ csrf_field() }}
					<div class="row">
						<div class="col-md-12">
							<div class="form-group">
								<label class="control-label">{{ _lang('Title') }}</label>						
								<input type="text" class="form-control" name="title" value="{{ old('title') }}" required>
							</div>
						</div>

						<div class="col-md-12">
							<div class="form-group">
								<label class="control-label">{{ _lang('Details') }}</label>						
								<textarea class="form-control summernote" name="details">{{ old('details') }}</textarea>
							</div>
						</div>

						<div class="col-md-12">
							<div class="form-group">
								<label class="control-label">{{ _lang('Attachment') }}</label>						
								<input type="file" class="form-control file-uploader" name="attachment" >
							</div>
						</div>

						<div class="col-md-12">
							<div class="form-group">
								<label class="control-label">{{ _lang('Status') }}</label>						
								<select class="form-control auto-select" data-selected="{{ old('status', 1) }}" name="status" required>
									<option value="1">{{ _lang('Published') }}</option>
									<option value="0">{{ _lang('Draft') }}</option>
								</select>
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
</div>
@endsection


