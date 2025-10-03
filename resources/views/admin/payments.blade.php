@extends('admin.dashboard')

@section('content')
<div class="container-fluid py-4">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold" style="color:#333;">Payments</h2>
        <button class="btn btn-success">+ Add Payment</button>
    </div>

    <div class="table-responsive">
        <table class="table table-bordered align-middle">
            <thead>
                <tr>
                    <th style="background-color:#FFF3C2;">ID</th>
                    <th style="background-color:#FFF3C2;">Tenant Name</th>
                    <th style="background-color:#FFF3C2;">Amount</th>
                    <th style="background-color:#FFF3C2;">Date</th>
                    <th style="background-color:#FFF3C2;">Status</th>
                    <th style="background-color:#FFF3C2;">Actions</th>
                </tr>
            </thead>
            <tbody>
                {{-- Example row --}}
                <tr>
                    <td>1</td>
                    <td>John Doe</td>
                    <td>₱8,000.00</td>
                    <td>2025-10-03</td>
                    <td><span class="badge bg-success">Paid</span></td>
                    <td class="d-flex gap-2">
                        <button class="btn btn-sm btn-primary">Edit</button>
                        <button class="btn btn-sm btn-warning">Archive</button>
                    </td>
                </tr>
                <tr>
                         <td>3</td>
                         <td>Carlos Reyes</td>
                         <td>₱4,200</td>
                         <td>2025-09-30</td>
                         <td><span class="badge bg-danger">Unpaid</span></td>
                            <td class="d-flex gap-2">
                        <button class="btn btn-sm btn-primary">Edit</button>
                        <button class="btn btn-sm btn-warning">Archive</button>
                        </td>
                     </tr>
            </tbody>
        </table>
    </div>
@endsection
