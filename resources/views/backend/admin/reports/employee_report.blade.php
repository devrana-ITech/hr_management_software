@extends('layouts.app')

@section('content')
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Employee Report by Unit</title>

    <style>
        @media print {
            .no-print { display: none !important; }
            body { background: white; }
            table { font-size: 12px; }
        }
        body { background: #f9f9f9; }
        .report-container {
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 6px rgba(0,0,0,0.1);
        }
    </style>
</head>
<body>

<div class="container mt-4 report-container">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h3>Employee Report</h3>
        <button onclick="window.print()" class="btn btn-success no-print">
            🖨️ Print Report
        </button>
    </div>

    <form method="GET" action="{{ route('employee.report') }}" class="no-print mb-3">
        <div class="row">
            <div class="col-md-4">
                <select name="unit_id" class="form-control auto-select select2">
                    <option value="">-- Select Unit --</option>
                    @foreach($units as $unit)
                        <option value="{{ $unit->id }}" {{ (isset($unit_id) && $unit_id == $unit->id) ? 'selected' : '' }}>
                            {{ $unit->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-primary">Filter</button>
            </div>
        </div>
    </form>

    @if($employees->count() > 0)
    <table class="table table-bordered table-striped">
        <thead class="table-light">
            <tr>
                <th>#</th>
                <th>Employee ID</th>
                <th>Name</th>
                <th>Email</th>
                <th>Phone</th>
                <th>Unit</th>
                <th>Department</th>
                <th>Designation</th>
                <th>Joining Date</th>
                <th>End Date</th>
            </tr>
        </thead>
        <tbody>
            @foreach($employees as $key => $emp)
                <tr>
                    <td>{{ $key + 1 }}</td>
                    <td>{{ $emp->employee_id }}</td>
                    <td>{{ $emp->first_name }} {{ $emp->last_name }}</td>
                    <td>{{ $emp->email }}</td>
                    <td>{{ $emp->phone }}</td>
                    <td>{{ $emp->unit?->name }}</td>
                    <td>{{ $emp->department?->name }}</td>
                    <td>{{ $emp->designation?->name }}</td>
                    <td>{{ $emp->joining_date }}</td>
                    <td>{{ $emp->end_date }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
    @else
        <p class="text-muted">No employees found for the selected unit.</p>
    @endif
</div>

</body>
</html>

@endsection
