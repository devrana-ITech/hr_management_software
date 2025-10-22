<table class="table table-bordered">
	<tr><td>{{ _lang('Name') }}</td><td>{{ $loantype->name }}</td></tr>
	<tr><td>{{ _lang('Minimum Amount') }}</td><td>{{ decimalPlace($loantype->minimum_amount, currency_symbol()) }}</td></tr>
	<tr><td>{{ _lang('Maximum Amount') }}</td><td>{{ decimalPlace($loantype->maximum_amount, currency_symbol()) }}</td></tr>
	<tr><td>{{ _lang('Interest Rate') }}</td><td>{{ $loantype->interest_rate }}%</td></tr>
	<tr><td>{{ _lang('Interest Type') }}</td><td>{{ ucwords($loantype->interest_type) }}</td></tr>
	<tr><td>{{ _lang('Term') }}</td><td>{{ $loantype->term }}</td></tr>
</table>

