<div class="row p-2">
	<div class="col-lg-12">
        <table class="table table-bordered">
            <tr><td>{{ _lang('Trans Date') }}</td><td>{{ $employeeexpense->trans_date }}</td></tr>
            <tr><td>{{ _lang('Employee') }}</td><td>{{ $employeeexpense->employee->name }}</td></tr>
            <tr><td>{{ _lang('Bill No') }}</td><td>{{ $employeeexpense->bill_no }}</td></tr>
            <tr><td>{{ _lang('Category') }}</td><td>{{ $employeeexpense->category->name }}</td></tr>
            <tr><td>{{ _lang('Amount') }}</td><td>{{ decimalPlace($employeeexpense->amount, currency_symbol(currency())) }}</td></tr>
            <tr><td>{{ _lang('Description') }}</td><td>{{ $employeeexpense->description }}</td></tr>
            <tr>
                <td>{{ _lang('Attachment') }}</td>
                <td>
                    @if($employeeexpense->attachment != '')
                    <a href="{{ asset('public/uploads/media/'.$employeeexpense->attachment) }}" target="_blank">{{ $employeeexpense->attachment }}</a>
                    @endif
                </td>
            </tr>
            <tr>
                <td>{{ _lang('Status') }}</td>
                <td>
                    @if($employeeexpense->status == 0)
                    {!! xss_clean(show_status(_lang('Pending'), 'warning')) !!}
                    @else
                    {!! xss_clean(show_status(_lang('Completed'), 'success')) !!}
                    @endif
                </td>
            </tr>
        </table>
    </div>
</div>