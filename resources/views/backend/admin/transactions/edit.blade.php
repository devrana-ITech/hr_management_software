@extends('layouts.app')

@section('content')
<div class="row">
	<div class="{{ $alert_col }}">
		<div class="card">
		    <div class="card-header d-flex align-items-center">
				<span class="panel-title">{{ _lang('Update Journal entry') }}</span>
			</div>
			<div class="card-body">
                <form class="validate" action="{{ route('transactions.update', $transaction->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <div class="row">
                        <div class="col-lg-3">
                            <div class="form-group">
                                <label for="transaction_date" class="form-label">{{ _lang('Transaction Date') }}</label>
                                <input type="text" class="form-control datepicker" id="transaction_date" name="transaction_date" value="{{ old('transaction_date', $transaction->getRawOriginal('transaction_date')) }}" required>
                            </div>
                        </div>

                        <div class="col-lg-9">
                            <div class="form-group">
                                <label for="description" class="form-label">{{ _lang('Description') }}</label>
                                <input type="text" class="form-control" id="description" name="description" value="{{ old('description', $transaction->description) }}" required>
                            </div>
                        </div>
                    </div>

                    <table class="table table-bordered mt-4" id="entries-table">
                        <thead class="bg-light">
                            <tr>
                                <th class="text-dark pl-3">{{ _lang('Account') }}</th>
                                <th class="text-dark">{{ _lang('Type') }}</th>
                                <th class="text-dark">{{ _lang('Amount') }}</th>
                                <th class="text-dark text-center">{{ _lang('Action') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if($transaction->entries)
                                @foreach($transaction->entries as $index => $entry)
                                    <tr>
                                        <td class="pl-3">
                                            <select class="form-control" name="entries[{{ $index }}][account_id]" required>
                                                <option value="">{{ _lang('Select Account') }}</option>
                                                @foreach($accounts as $account)
                                                    <option value="{{ $account->id }}" {{ $entry->account_id == $account->id ? 'selected' : '' }}>
                                                        {{ $account->account_id.' - '.$account->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </td>
                                        <td>
                                            <select class="form-control" name="entries[{{ $index }}][type]" required>
                                                <option value="debit" {{ $entry->type == 'debit' ? 'selected' : '' }}>{{ _lang('Debit') }}</option>
                                                <option value="credit" {{ $entry->type == 'credit' ? 'selected' : '' }}>{{ _lang('Credit') }}</option>
                                            </select>
                                        </td>
                                        <td>
                                            <input type="text" class="form-control float-field @error('entries.'.$loop->index.'.amount') is-invalid @enderror" name="entries[{{ $index }}][amount]" value="{{ $entry->amount }}" required>
                                        </td>
                                        <td class="text-center">
                                            <button type="button" class="btn btn-danger btn-xs remove-entry"><i class="far fa-trash-alt mr-1"></i>{{ _lang('Remove') }}</button>
                                        </td>
                                    </tr>
                                @endforeach
                            @else
                                <tr>
                                    <td class="pl-3">
                                        <select class="form-control" name="entries[0][account_id]" required>
                                            <option value="">{{ _lang('Select Account') }}</option>
                                            @foreach($accounts as $account)
                                                <option value="{{ $account->id }}">{{ $account->account_id.' - '.$account->name }}</option>
                                            @endforeach
                                        </select>
                                    </td>
                                    <td>
                                        <select class="form-control" name="entries[0][type]" required>
                                            <option value="debit" selected>{{ _lang('Debit') }}</option>
                                            <option value="credit">{{ _lang('Credit') }}</option>
                                        </select>
                                    </td>
                                    <td>
                                        <input type="text" class="form-control float-field" name="entries[0][amount]" required>
                                    </td>
                                    <td class="text-center">
                                        <button type="button" class="btn btn-danger btn-xs remove-entry"><i class="far fa-trash-alt mr-1"></i>{{ _lang('Remove') }}</button>
                                    </td>
                                </tr>

                                <tr>
                                    <td class="pl-3">
                                        <select class="form-control" name="entries[1][account_id]" required>
                                            <option value="">{{ _lang('Select Account') }}</option>
                                            @foreach($accounts as $account)
                                                <option value="{{ $account->id }}">{{ $account->account_id.' - '.$account->name }}</option>
                                            @endforeach
                                        </select>
                                    </td>
                                    <td>
                                        <select class="form-control" name="entries[1][type]" required>
                                            <option value="debit">{{ _lang('Debit') }}</option>
                                            <option value="credit" selected>{{ _lang('Credit') }}</option>
                                        </select>
                                    </td>
                                    <td>
                                        <input type="text" class="form-control float-field" name="entries[1][amount]" required>
                                    </td>
                                    <td class="text-center">
                                        <button type="button" class="btn btn-danger btn-xs remove-entry"><i class="far fa-trash-alt mr-1"></i>{{ _lang('Remove') }}</button>
                                    </td>
                                </tr>
                            @endif
                        </tbody>
                    </table>

                    <div class="row">
                        <div class="col-lg-12">
                            <div class="form-group">
                                <label for="attachment" class="form-label">{{ _lang('Attachment') }}</label>
                                <input type="file" class="dropify" id="attachment" name="attachment" data-default-file="{{ $transaction->attachment != null ? asset('storage/app/public/'. $transaction->attachment) : '' }}">
                            </div>
                        </div>
                    </div>

                    <button type="button" id="add-entry" class="btn btn-secondary"><i class="fas fa-plus-circle mr-1"></i>{{ _lang('Add Another Entry') }}</button>
                    <button type="submit" class="btn btn-primary"><i class="fas fa-check-circle mr-1"></i>{{ _lang('Submit Transaction') }}</button>
                </form>

            </div>
        </div>
    </div>
</div>
@endsection

@section('js-script')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        let entryIndex = {{ old('entries') ? count(old('entries')) : 2 }};

        document.getElementById('add-entry').addEventListener('click', function () {
            const tableBody = document.querySelector('#entries-table tbody');
            const newRow = document.createElement('tr');

            newRow.innerHTML = `
                <td class="pl-3">
                    <select class="form-control" name="entries[${entryIndex}][account_id]" required>
                        <option value="">{{ _lang('Select Account') }}</option>
                        @foreach($accounts as $account)
                            <option value="{{ $account->id }}">{{ $account->account_id.' - '.$account->name }}</option>
                        @endforeach
                    </select>
                </td>
                <td>
                    <select class="form-control" name="entries[${entryIndex}][type]" required>
                        <option value="debit">{{ _lang('Debit') }}</option>
                        <option value="credit">{{ _lang('Credit') }}</option>
                    </select>
                </td>
                <td>
                    <input type="text" class="form-control float-field" name="entries[${entryIndex}][amount]" required>
                </td>
                <td class="text-center">
                    <button type="button" class="btn btn-danger btn-xs remove-entry"><i class="far fa-trash-alt mr-1"></i>{{ _lang('Remove') }}</button>
                </td>
            `;
            tableBody.appendChild(newRow);
            entryIndex++;
        });

        document.querySelector('#entries-table').addEventListener('click', function (e) {
            if (e.target && e.target.classList.contains('remove-entry')) {
                const row = e.target.closest('tr');
                row.remove();
            }
        });
    });
</script>
@endsection
