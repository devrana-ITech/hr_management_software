@extends('layouts.app')

@section('content')
<div class="row">
	<div class="{{ $alert_col }}">
		<div class="card">
		    <div class="card-header">
				<span class="panel-title">{{ _lang('Notice Details') }}</span>
			</div>
			
			<div class="card-body">
				<h4 class="mb-2">{{ $notice->title }}</h4>
				<div>{!! xss_clean($notice->details) !!}</div>

			    <table class="table table-bordered mt-4">
					@if($notice->attachment != null)
					<tr>
						<td>{{ _lang('Attachment') }}</td>
						<td><a href="{{ asset('public/uploads/media/'. $notice->attachment) }}" target="_blank"><i class="fas fa-paperclip mr-1"></i>{{ _lang('Download') }}</a></td>
					</tr>
					@endif
					<tr>
						<td>{{ _lang('Status') }}</td>
						<td>{!! $notice->status == 0 ? xss_clean(show_status(_lang('Draft'), 'warning'))  : xss_clean(show_status(_lang('Published'), 'success')) !!}</td>
					</tr>
					<tr><td>{{ _lang('Created') }}</td><td>{{ $notice->created_by->name }}</td></tr>
					<tr><td>{{ _lang('Created At') }}</td><td>{{ $notice->created_at }}</td></tr>
			    </table>
			</div>
	    </div>
	</div>
</div>
@endsection


