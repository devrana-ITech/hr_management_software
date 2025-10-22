<div class="card">
    <div class="card-header text-center">
        <span class="panel-title">{{ _lang('Payroll Details') }}</span>
    </div>
    
    <div class="card-body">
        <div class="row mb-4">
            <div class="col-lg-6">        
                <h5 class="mb-2">{{ _lang('Benefits Overview') }}</h5>
                <table class="payslip-table w-100" border="1">
                    <thead class="bg-light">
                        <th>{{ _lang('Name') }}</th>
                        <th class="text-right">{{ _lang('Amount') }}</th>
                    </thead>
                    <tbody>
                        @foreach($benefits_deductions->where('type', 'add') as $benefit)
                        <tr>
                            <td>{{ $benefit->name }}</td>
                            <td class="text-right">{{ decimalPlace($benefit->amount, currency_symbol()) }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="col-lg-6">
                <h5 class="mb-2">{{ _lang('Deductions Overview') }}</h5>
                <table class="payslip-table w-100" border="1">
                    <thead class="bg-light">
                        <th>{{ _lang('Name') }}</th>
                        <th class="text-right">{{ _lang('Amount') }}</th>
                    </thead>
                    <tbody>
                        @foreach($benefits_deductions->where('type', 'deduct') as $deduction)
                        <tr>
                            <td>{{ $deduction->name }}</td>
                            <td class="text-right">{{ decimalPlace($deduction->amount, currency_symbol()) }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <div class="table-responsive">
            <table id="payslips_table" class="table table-bordered">
                <thead>
                    <tr>
                        <th>{{ _lang('Employee ID') }}</th>
                        <th>{{ _lang('Name') }}</th>
                        <th>{{ _lang('Year') }}</th>
                        <th>{{ _lang('Month') }}</th>
                        <th class="text-right">{{ _lang('Net Salary') }}</th>
                        <th class="text-center">{{ _lang('Status') }}</th>
                        <th class="text-center">{{ _lang('Action') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($payrolls as $payroll)
                    <tr>
                        <td>{{ $employee->employee_id }}</td>
                        <td>{{ $employee->name }}</td>
                        <td>{{ $payroll->year }}</td>
                        <td>{{ date('F', mktime(0, 0, 0, $payroll->month, 10)) }}</td>
                        <td class="text-right">{{ decimalPlace($payroll->net_salary, currency_symbol()) }}</td>
                        <td class="text-center">{!! xss_clean(payroll_status($payroll->status)) !!}</td>
                        <td class="text-center">
                            @if ($payroll->status == 0)
                                <div class="dropdown text-center">
                                    <button class="btn btn-outline-primary btn-xs dropdown-toggle" type="button" data-toggle="dropdown">{{ _lang('Action') }}</button>
                                    <div class="dropdown-menu">
                                        <a class="dropdown-item" href="{{ route('payslips.edit', $payroll['id']) }}"><i class="fas fa-pencil-alt mr-1"></i>{{ _lang('Edit') }}</a>
                                        <a class="dropdown-item" href="{{ route('payslips.show', $payroll['id']) }}"><i class="fas fa-eye mr-1"></i> {{ _lang('Details') }}</a>
                                        <form action="{{ route('payslips.destroy', $payroll['id']) }}" method="post">
                                            @csrf
                                            <input name="_method" type="hidden" value="DELETE">
                                            <button class="dropdown-item btn-remove" type="submit"><i class="fas fa-trash-alt mr-1"></i> {{ _lang('Delete') }}</button>
                                        </form>
                                    </div>
                                </div>
                            @else
                                <div class="dropdown text-center">
                                    <button class="btn btn-outline-primary btn-xs dropdown-toggle" type="button" data-toggle="dropdown">{{ _lang('Action') }}</button>
                                    <div class="dropdown-menu">
                                        <a class="dropdown-item" href="{{ route('payslips.show', $payroll['id']) }}"><i class="fas fa-eye mr-1"></i> {{ _lang('Details') }}</a>
                                    </div>
                                </div>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="float-right">
            {{ $payrolls->links() }}
        </div>
    </div>
</div>