<div class="card">
    <div class="card-header text-center">
        <span class="panel-title">{{ _lang('Loans') }}</span>
    </div>
    
    <div class="card-body">
        <div class="table-responsive">
            <table id="payslips_table" class="table table-bordered">
                <thead>
                    <tr>
                        <th>{{ _lang('Date') }}</th>
                        <th>{{ _lang('Loan ID') }}</th>
                        <th>{{ _lang('Employee') }}</th>
                        <th>{{ _lang('Loan Amount') }}</th>
                        <th>{{ _lang('Remaining Balance') }}</th>
                        <th>{{ _lang('Interest Rate') }}</th>
                        <th>{{ _lang('Monthly Installment') }}</th>
                        <th class="text-center">{{ _lang('Status') }}</th>
                        <th class="text-center">{{ _lang('Action') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($loans as $loan)
                    <tr>
                        <td>{{ $loan->application_date }}</td>
                        <td>{{ $employee->employee_id }}</td>
                        <td>{{ $employee->name }}</td>
                        <td>{{ decimalPlace($loan->loan_amount, currency_symbol()) }}</td>
                        <td>{{ decimalPlace($loan->remaining_balance, currency_symbol()) }}</td>
                        <td>{{ $loan->interest_rate }}%</td>
                        <td>{{ decimalPlace($loan->monthly_installment, currency_symbol()) }}</td>
                        <td class="text-center">
                            @if ($loan->status == 'pending')
                                <span class="badge badge-warning">{{ ucwords($loan->status) }}<span>
                            @elseif ($loan->status == 'approved')
                                <span class="badge badge-success">{{ ucwords($loan->status) }}<span>
                            @elseif ($loan->status == 'rejected')
                                <span class="badge badge-danger">{{ ucwords($loan->status) }}<span>
                            @elseif ($loan->status == 'repaid')
                                <span class="badge badge-primary">{{ ucwords($loan->status) }}<span>
                            @endif
                        </td>
                        <td class="text-center">
                            <a class="btn btn-outline-primary btn-xs" href="{{ route('employee_loans.show', $loan['id']) }}"><i class="fas fa-eye mr-1"></i> {{ _lang('Details') }}</a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="float-right">
            {{ $loans->links() }}
        </div>
    </div>
</div>