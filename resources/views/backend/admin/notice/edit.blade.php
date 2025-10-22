@extends('layouts.app')

@section('content')
<div class="row">
	<div class="{{ $alert_col }}">
		<div class="card">
			<div class="card-header">
				<span class="panel-title">{{ _lang('Update Notice') }}</span>
			</div>
			<div class="card-body">
				<form method="post" class="validate" autocomplete="off" action="{{ route('notices.update', $id) }}" enctype="multipart/form-data">
					{{ csrf_field()}}
					<input name="_method" type="hidden" value="PATCH">
					<div class="row">
						<div class="col-md-12">
							<div class="form-group">
								<label class="control-label">{{ _lang('Title') }}</label>						
								<input type="text" class="form-control" name="title" value="{{ $notice->title }}" required>
							</div>
						</div>

						<div class="col-md-12">
							<div class="form-group">
								<label class="control-label">{{ _lang('Details') }}</label>						
								<textarea class="form-control summernote" name="details">{{ $notice->details }}</textarea>
							</div>
						</div>

						<div class="col-md-12">
							<div class="form-group">
								<label class="control-label">{{ _lang('Attachment') }}</label>						
								<input type="file" class="form-control file-uploader" name="attachment" data-default-file="{{ $notice->attachment != '' ? asset('public/uploads/media/'.$notice->attachment) : '' }}">
							</div>
						</div>

						<div class="col-md-12">
							<div class="form-group">
								<label class="control-label">{{ _lang('Status') }}</label>						
								<select class="form-control auto-select" data-selected="{{ $notice->status }}" name="status" required>
									<option value="1">{{ _lang('Published') }}</option>
									<option value="0">{{ _lang('Draft') }}</option>
								</select>
							</div>
						</div>

						<div class="col-md-12 mt-2">
							<div class="form-group">
								<button type="submit" class="btn btn-primary"><i class="ti-check-box mr-2"></i> {{ _lang('Update') }}</button>
							</div>
						</div>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>
@endsection


