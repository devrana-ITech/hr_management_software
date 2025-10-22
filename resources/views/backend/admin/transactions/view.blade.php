@extends('layouts.app')

@section('content')
<div class="row">
	<div class="{{ $alert_col }}">
		<div class="card">
		    <div class="card-header d-sm-flex align-items-center justify-content-between">
				<div class="panel-title">{{ _lang('Transaction Details') }}</div>
                <a href="{{ route('transactions.index') }}" class="btn btn-secondary btn-xs"><i class="fas fa-undo mr-1"></i>{{ _lang('Back to Transactions') }}</a>
			</div>
			<div class="card-body">
                <p><strong>{{ _lang('Date') }}:</strong> {{ $transaction->transaction_date }}</p>
                <p><strong>{{ _lang('Description') }}:</strong> {{ $transaction->description }}</p>
                <p><strong>{{ _lang('Total Amount') }}:</strong> {{ decimalPlace($transaction->entries()->where('type', 'debit')->sum('amount'), currency_symbol()) }}</p>
                <p><strong>{{ _lang('Created') }}:</strong> {{ $transaction->created_by->name }} ({{ $transaction->created_at }})</p>
                
                @if($transaction->updated_user_id != null)
                <p><strong>{{ _lang('Updated') }}:</strong> {{ $transaction->updated_by->name }} ({{ $transaction->updated_at }})</p>
                @endif

                <table class="table table-bordered mt-4">
                    <thead class="bg-light">
                        <th class="text-dark">{{ _lang('Account') }}</th>
                        <th class="text-dark text-right">{{ _lang('Debit') }}</th>
                        <th class="text-dark text-right">{{ _lang('Credit') }}</th>
                    </thead>
                    <tbody>
                        @foreach($transaction->entries as $entry)
                        <tr>
                            <td>{{ $entry->account->name }}</td>
                            <td class="text-right">{{ $entry->type == 'debit' ? decimalPlace($entry->amount, currency_symbol()) : '' }}</td>
                            <td class="text-right">{{ $entry->type == 'credit' ? decimalPlace($entry->amount, currency_symbol()) : '' }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                @if($transaction->attachment != '')
                <a class="btn btn-primary btn-xs" target="_blank" href="{{ asset('storage/app/public/'. $transaction->attachment) }}"><i class="fas fa-paperclip mr-1"></i>{{ _lang('View Attachment') }}</a>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
