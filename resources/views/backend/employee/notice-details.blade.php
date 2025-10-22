@extends('layouts.app')

@section('content')
<div class="row">
	<div class="col-lg-8 offset-lg-2">
		<div class="card">
		    <div class="card-header">
				<span class="panel-title">{{ _lang('Notice Details') }}</span>
			</div>
			
			<div class="card-body">
				<h4>{{ $notice->title }}</h4>
                <p class="mb-3 font-italic">{{ _lang('Created by').' '.$notice->created_by->name.', '._lang('Posted on').' '.$notice->created_at }}</p>

				<div>{!! xss_clean($notice->details) !!}</div>

                @if($notice->attachment != null)
                <a class="btn btn-outline-primary btn-xs mt-4" href="{{ asset('public/uploads/media/'. $notice->attachment) }}" target="_blank"><i class="fas fa-paperclip mr-1"></i>{{ _lang('Download Attachment') }}</a></td>
                @endif
			</div>
	    </div>
	</div>
</div>
@endsection