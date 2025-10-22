<?php

namespace App\Http\Controllers;
use App\Models\Award;
use App\Models\Employee;
use App\Models\EmployeeBenefitDeduction;
use App\Models\EmployeeLoan;
use App\Models\Leave;
use App\Models\Payroll;
use App\Models\PayrollBenefit;
use App\Models\User;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Validator;

class EmployeeController extends Controller {

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index() {
        $assets = ['datatable'];
        $employees = Employee::paginate(2);
        return view('backend.admin.employee.list', compact('assets','employees'));
    }

    public function get_table_data() {
        $employees = Employee::with('department', 'designation')
            ->select('employees.*');

        return Datatables::eloquent($employees)
            ->editColumn('basic_salary', function ($employee) {
                if ($employee->salary_type == 'fixed') {
                    return decimalPlace($employee->basic_salary, currency_symbol(currency()));
                } else {
                    return decimalPlace($employee->basic_salary, currency_symbol(currency())) . '/' . _lang('Hourly');
                }
            })
            ->addColumn('action', function ($employee) {
                return '<div class="dropdown text-center">'
                . '<button class="btn btn-outline-primary btn-xs dropdown-toggle" type="button" data-toggle="dropdown">' . _lang('Action')
                . '</button>'
                . '<div class="dropdown-menu">'
                . '<a class="dropdown-item" href="' . route('employees.show', $employee['id']) . '"><i class="fas fa-eye"></i> ' . _lang('Details') . '</a>'
                . '<a class="dropdown-item" href="' . route('employee_documents.index', $employee['id']) . '"><i class="fas fa-folder-open"></i> ' . _lang('Documents') . '</a>'
                . '<a class="dropdown-item" href="' . route('employees.login_access', $employee['id']) . '"><i class="fas fa-lock"></i> ' . _lang('Login Access') . '</a>'
                . '<a class="dropdown-item" href="' . route('employees.edit', $employee['id']) . '"><i class="fas fa-pencil-alt"></i> ' . _lang('Edit') . '</a>'
                . '<form action="' . route('employees.destroy', $employee['id']) . '" method="post">'
                . csrf_field()
                . '<input name="_method" type="hidden" value="DELETE">'
                . '<button class="dropdown-item btn-remove" type="submit"><i class="fas fa-trash-alt"></i> ' . _lang('Delete') . '</button>'
                    . '</form>'
                    . '</div>'
                    . '</div>';
            })
            ->setRowId(function ($employee) {
                return "row_" . $employee->id;
            })
            ->rawColumns(['action'])
            ->make(true);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request) {
        $alert_col = 'col-lg-10 offset-lg-1';
        return view('backend.admin.employee.create', compact('alert_col'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request) {
        $validator = Validator::make($request->all(), [
            'employee_id'           => 'required|unique:employees',
            'first_name'            => 'required|max:50',
            'last_name'             => 'required|max:50',
            'date_of_birth'         => 'required',
            'email'                 => 'nullable|email|unique:employees|max:191',
            'phone'                 => 'nullable|max:30',
            'department_id'         => 'required',
            'designation_id'        => 'required',
            'joining_date'          => 'required',
            'salary_type'           => 'required',
            'basic_salary'          => 'required|numeric',
            'full_day_absence_fine' => 'required|numeric',
            'half_day_absence_fine' => 'required|numeric',
            'yearly_leave_limit'    => 'required|integer',
            'image'                 => 'nullable|image|max:4096',
        ]);

        if ($validator->fails()) {
            if ($request->ajax()) {
                return response()->json(['result' => 'error', 'message' => $validator->errors()->all()]);
            } else {
                return redirect()->route('employees.create')
                    ->withErrors($validator)
                    ->withInput();
            }
        }

        $image = 'default.png';
        if ($request->hasfile('image')) {
            $file  = $request->file('image');
            $image = rand() . time() . $file->getClientOriginalName();
            $file->move(public_path() . "/uploads/profile/", $image);
        }

        DB::beginTransaction();

        $employee                = new Employee();
        $employee->employee_id   = $request->input('employee_id');
        $employee->first_name    = $request->input('first_name');
        $employee->last_name     = $request->input('last_name');
        $employee->fathers_name  = $request->input('fathers_name');
        $employee->mothers_name  = $request->input('mothers_name');
        $employee->date_of_birth = $request->input('date_of_birth');
        $employee->email         = $request->input('email');
        $employee->phone         = $request->input('phone');
        $employee->city          = $request->input('city');
        $employee->state         = $request->input('state');
        $employee->zip           = $request->input('zip');
        $employee->country       = $request->input('country');
        $employee->image         = $request->input('image');

        $employee->department_id         = $request->input('department_id');
        $employee->designation_id        = $request->input('designation_id');
        $employee->joining_date          = $request->input('joining_date');
        $employee->end_date              = $request->input('end_date');
        $employee->salary_type           = $request->salary_type;
        $employee->basic_salary          = $request->basic_salary;
        $employee->full_day_absence_fine = $request->full_day_absence_fine;
        $employee->half_day_absence_fine = $request->half_day_absence_fine;
        $employee->yearly_leave_limit    = $request->yearly_leave_limit;

        $employee->bank_name      = $request->input('bank_name');
        $employee->branch_name    = $request->input('branch_name');
        $employee->account_name   = $request->input('account_name');
        $employee->account_number = $request->input('account_number');
        $employee->swift_code     = $request->input('swift_code');
        $employee->remarks        = $request->input('remarks');

        $employee->save();

        if (isset($request->allowances)) {
            for ($i = 0; $i < count($request->allowances['name']); $i++) {
                $employee->benefit_deductions()->save(new EmployeeBenefitDeduction([
                    'name'        => $request->allowances['name'][$i],
                    'amount'      => $request->allowances['amount'][$i],
                    'amount_type' => $request->allowances['amount_type'][$i],
                    'type'        => 'add',
                ]));
            }
        }

        if (isset($request->deductions)) {
            for ($i = 0; $i < count($request->deductions['name']); $i++) {
                $employee->benefit_deductions()->save(new EmployeeBenefitDeduction([
                    'name'        => $request->deductions['name'][$i],
                    'amount'      => $request->deductions['amount'][$i],
                    'amount_type' => $request->deductions['amount_type'][$i],
                    'type'        => 'deduct',
                ]));
            }
        }

        DB::commit();

        if (!$request->ajax()) {
            return redirect()->route('employees.index')->with('success', _lang('Saved Successfully'));
        } else {
            return response()->json(['result' => 'success', 'action' => 'store', 'message' => _lang('Saved Successfully'), 'data' => $employee, 'table' => '#employees_table']);
        }

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, $id) {
        $data              = array();
        $data['alert_col'] = 'col-lg-10 offset-lg-1';
        $data['employee']  = Employee::with('department', 'designation')->find($id);

        if (isset($_GET['tab']) && $_GET['tab'] == 'payroll') {
            $data['benefits_deductions'] = PayrollBenefit::whereHas('payroll', function (Builder $query) use ($id) {
                $query->where('employee_id', $id)
                    ->where('status', 1);
            })
                ->selectRaw('name, SUM(amount) as amount, type')
                ->groupBy('name', 'type')
                ->get();

            $data['payrolls'] = Payroll::with('payroll_benefits')
                ->select('payslips.*')
                ->where('payslips.employee_id', $id)
                ->orderBy('id', 'desc')
                ->paginate(10)
                ->withPath('?tab=' . $_GET['tab']);
        }

        if (isset($_GET['tab']) && $_GET['tab'] == 'leaves') {
            $data['leaves'] = Leave::select('leaves.*')
                ->where('leaves.employee_id', $id)
                ->orderBy('leaves.id', 'desc')
                ->paginate(10)
                ->withPath('?tab=' . $_GET['tab']);
        }

        if (isset($_GET['tab']) && $_GET['tab'] == 'loans') {
            $data['loans'] = EmployeeLoan::select('employee_loans.*')
                ->where('employee_loans.employee_id', $id)
                ->orderBy("employee_loans.id", "desc")
                ->paginate(10)
                ->withPath('?tab=' . $_GET['tab']);
        }

        if (isset($_GET['tab']) && $_GET['tab'] == 'awards') {
            $data['awards'] = Award::select('awards.*')
                ->where('awards.employee_id', $id)
                ->orderBy('id', 'desc')
                ->paginate(10)
                ->withPath('?tab=' . $_GET['tab']);
        }

        return view('backend.admin.employee.view', $data);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request, $id) {
        $alert_col = 'col-lg-10 offset-lg-1';
        $employee  = Employee::find($id);
        return view('backend.admin.employee.edit', compact('employee', 'id', 'alert_col'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id) {
        $validator = Validator::make($request->all(), [
            'first_name'            => 'required|max:50',
            'last_name'             => 'required|max:50',
            'date_of_birth'         => 'required',
            'email'                 => [
                'nullable',
                'email',
                Rule::unique('employees')->ignore($id),
            ],
            'phone'                 => 'nullable|max:30',
            'employee_id'           => [
                'required',
                Rule::unique('employees')->ignore($id),
            ],
            'department_id'         => 'required',
            'designation_id'        => 'required',
            'joining_date'          => 'required',
            'salary_type'           => 'required',
            'basic_salary'          => 'required|numeric',
            'full_day_absence_fine' => 'required|numeric',
            'half_day_absence_fine' => 'required|numeric',
            'yearly_leave_limit'    => 'required|integer',
            'image'                 => 'nullable|image|max:4096',
        ]);

        if ($validator->fails()) {
            if ($request->ajax()) {
                return response()->json(['result' => 'error', 'message' => $validator->errors()->all()]);
            } else {
                return redirect()->route('employees.edit', $id)
                    ->withErrors($validator)
                    ->withInput();
            }
        }

        if ($request->hasfile('image')) {
            $file  = $request->file('image');
            $image = rand() . time() . $file->getClientOriginalName();
            $file->move(public_path() . "/uploads/profile/", $image);
        }

        DB::beginTransaction();

        $employee                = Employee::find($id);
        $employee->first_name    = $request->input('first_name');
        $employee->last_name     = $request->input('last_name');
        $employee->fathers_name  = $request->input('fathers_name');
        $employee->mothers_name  = $request->input('mothers_name');
        $employee->date_of_birth = $request->input('date_of_birth');
        $employee->email         = $request->input('email');
        $employee->phone         = $request->input('phone');
        $employee->city          = $request->input('city');
        $employee->state         = $request->input('state');
        $employee->zip           = $request->input('zip');
        $employee->country       = $request->input('country');
        if ($request->hasfile('image')) {
            $employee->image = $image;
        }
        $employee->employee_id           = $request->input('employee_id');
        $employee->department_id         = $request->input('department_id');
        $employee->designation_id        = $request->input('designation_id');
        $employee->joining_date          = $request->input('joining_date');
        $employee->end_date              = $request->input('end_date');
        $employee->salary_type           = $request->salary_type;
        $employee->basic_salary          = $request->basic_salary;
        $employee->full_day_absence_fine = $request->full_day_absence_fine;
        $employee->half_day_absence_fine = $request->half_day_absence_fine;
        $employee->yearly_leave_limit    = $request->yearly_leave_limit;

        $employee->bank_name      = $request->input('bank_name');
        $employee->branch_name    = $request->input('branch_name');
        $employee->account_name   = $request->input('account_name');
        $employee->account_number = $request->input('account_number');
        $employee->swift_code     = $request->input('swift_code');
        $employee->remarks        = $request->input('remarks');

        $employee->save();

        $employee->benefit_deductions()->whereNotIn('id', isset($request->allowances['salary_benefit_id']) ? $request->allowances['salary_benefit_id'] : [])->delete();
        $employee->benefit_deductions()->whereNotIn('id', isset($request->deductions['salary_benefit_id']) ? $request->deductions['salary_benefit_id'] : [])->delete();

        if (isset($request->allowances)) {
            for ($i = 0; $i < count($request->allowances['name']); $i++) {
                $employee->benefit_deductions()->save(EmployeeBenefitDeduction::firstOrNew([
                    'id'          => isset($request->allowances['salary_benefit_id'][$i]) ? $request->allowances['salary_benefit_id'][$i] : null,
                    'employee_id' => $employee->id,
                    'type'        => 'add',
                ], [
                    'name'        => $request->allowances['name'][$i],
                    'amount'      => $request->allowances['amount'][$i],
                    'amount_type' => $request->allowances['amount_type'][$i],
                ]));
            }
        }

        if (isset($request->deductions)) {
            for ($i = 0; $i < count($request->deductions['name']); $i++) {
                $employee->benefit_deductions()->save(EmployeeBenefitDeduction::firstOrNew([
                    'id'          => isset($request->deductions['salary_benefit_id'][$i]) ? $request->deductions['salary_benefit_id'][$i] : null,
                    'employee_id' => $employee->id,
                    'type'        => 'deduct',
                ], [
                    'name'        => $request->deductions['name'][$i],
                    'amount'      => $request->deductions['amount'][$i],
                    'amount_type' => $request->deductions['amount_type'][$i],
                ]));
            }
        }

        DB::commit();

        if (!$request->ajax()) {
            return redirect()->route('employees.index')->with('success', _lang('Updated Successfully'));
        } else {
            return response()->json(['result' => 'success', 'action' => 'update', 'message' => _lang('Updated Successfully'), 'data' => $employee, 'table' => '#employees_table']);
        }
    }

    public function login_access(Request $request, $id) {
        if ($request->isMethod('get')) {
            $alert_col = 'col-lg-8 offset-lg-2';
            $employee  = Employee::find($id);
            return view('backend.admin.employee.login_access', compact('employee', 'id', 'alert_col'));
        } else {
            $employee = Employee::find($id);

            $validator = Validator::make($request->all(), [
                'name'            => 'required|max:191',
                'email'           => [
                    'required',
                    'email',
                    $employee->user_id != null ? Rule::unique('users')->ignore($employee->user_id) : 'unique:users',
                ],
                'status'          => 'required',
                'profile_picture' => 'nullable|image',
                'password'        => $employee->user_id != null ? 'nullable|min:6' : 'required|min:6',
            ]);

            if ($validator->fails()) {
                if ($request->ajax()) {
                    return response()->json(['result' => 'error', 'message' => $validator->errors()->all()]);
                } else {
                    return back()->withErrors($validator)->withInput();
                }
            }

            if ($request->hasfile('profile_picture')) {
                $file            = $request->file('profile_picture');
                $profile_picture = time() . $file->getClientOriginalName();
                $file->move(public_path() . "/uploads/profile/", $profile_picture);
            }

            DB::beginTransaction();

            $user            = $employee->user_id != null ? User::find($employee->user_id) : new User();
            $user->name      = $request->input('name');
            $user->email     = $request->input('email');
            $user->user_type = 'employee';
            $user->status    = $request->input('status');

            if ($request->hasfile('profile_picture')) {
                $user->profile_picture = $profile_picture;
            }

            if ($request->password) {
                $user->password = Hash::make($request->password);
            }

            $user->phone   = $request->input('phone');
            $user->city    = $request->input('city');
            $user->state   = $request->input('state');
            $user->zip     = $request->input('zip');
            $user->address = $request->input('address');

            $user->save();

            $employee->user_id = $user->id;
            $employee->save();

            DB::commit();

            return back()->with('success', _lang('Updated Sucessfully'));
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id) {
        $employee = Employee::find($id);
        $employee->delete();
        return redirect()->route('employees.index')->with('success', _lang('Deleted Successfully'));
    }
}
