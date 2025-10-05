@extends('admin.dashboard')

@section('content')
<div class="container-fluid py-4">

    {{-- Header --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="fw-bold text-blue-900 mb-0">PAYMENTS</h3>
        <button class="btn btn-action">+ Add Payment</button>
    </div>

    {{-- Payments Table --}}
    <div class="card border-0 shadow-sm">
        <div class="card-header" style="background-color: var(--blue-100); border-bottom: 1px solid var(--blue-200);">
            <h6 class="mb-0 fw-semibold text-blue-800">PAYMENT RECORDS</h6>
        </div>

        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table mb-0 text-center booking-table align-middle">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>TENANT NAME</th>
                            <th>AMOUNT</th>
                            <th>DATE</th>
                            <th>STATUS</th>
                            <th>ACTIONS</th>
                        </tr>
                    </thead>
                    <tbody>
                        {{-- Dummy Data --}}
                        <tr>
                            <td>1</td>
                            <td>John Doe</td>
                            <td>₱8,000.00</td>
                            <td>2025-10-03</td>
                            <td><span class="badge bg-success">Paid</span></td>
                            <td class="d-flex justify-content-center gap-2">
                                <button class="btn btn-sm btn-primary">Edit</button>
                                <button class="btn btn-sm btn-outline-warning">Archive</button>
                            </td>
                        </tr>
                        <tr>
                            <td>2</td>
                            <td>Carlos Reyes</td>
                            <td>₱4,200</td>
                            <td>2025-09-30</td>
                            <td><span class="badge bg-danger">Unpaid</span></td>
                            <td class="d-flex justify-content-center gap-2">
                                <button class="btn btn-sm btn-primary">Edit</button>
                                <button class="btn btn-sm btn-outline-warning">Archive</button>
                            </td>
                        </tr>
                        {{-- End Dummy Data --}}
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
