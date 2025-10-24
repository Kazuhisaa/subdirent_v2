@extends('admin.dashboard')

@section('content')
<div class="container-fluid py-4">

    {{-- Header --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="fw-bold text-blue-900 mb-0">PAYMENTS</h3>
        {{-- Button moved to card header --}}
    </div>

    {{-- ✅ CHANGED: Wrapped the table in the standard theme card --}}
    <div class="card border-0 shadow-sm">

        {{-- ✅ ADDED: A gradient card-header to match the other pages --}}
        <div class="card-header fw-bold text-white d-flex justify-content-between align-items-center"
             style="background: linear-gradient(90deg, #007BFF, #0A2540); border-radius: .5rem;">
            <span>PAYMENT RECORDS</span>

            {{-- ✅ MOVED: The "Add Payment" button is now here and styled --}}
            <button class="btn btn-sm text-white fw-semibold"
                    style="background: linear-gradient(90deg, #2A9DF4, #0A2540); border:none; border-radius: 6px;">
                + Add Payment
            </button>
        </div>

        <div class="card-body p-0">
            <div class="table-responsive">
                {{-- ✅ ADDED: .booking-table class for consistent styling --}}
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
                            <td><span class="badge bg-success">Paid</span></td> {{-- bg-success is styled by your CSS --}}
                            <td>
                                {{-- ✅ CHANGED: Use a wrapper div for alignment --}}
                                <div class="d-flex justify-content-center align-items-center gap-2">
                                    {{-- ✅ CHANGED: Buttons use icons and theme classes --}}
                                    <button class="btn btn-sm btn-outline-blue" title="Edit">
                                        <i class="bi bi-pencil-square"></i>
                                    </button>
                                    <button class="btn btn-sm btn-outline-warning" title="Archive">
                                        <i class="bi bi-archive-fill"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td>2</td>
                            <td>Carlos Reyes</td>
                            <td>₱4,200</td>
                            <td>2025-09-30</td>
                            <td><span class="badge bg-danger">Unpaid</span></td>
                            <td>
                                {{-- ✅ CHANGED: Use a wrapper div for alignment --}}
                                <div class="d-flex justify-content-center align-items-center gap-2">
                                    {{-- ✅ CHANGED: Buttons use icons and theme classes --}}
                                    <button class="btn btn-sm btn-outline-blue" title="Edit">
                                        <i class="bi bi-pencil-square"></i>
                                    </button>
                                    <button class="btn btn-sm btn-outline-warning" title="Archive">
                                        <i class="bi bi-archive-fill"></i>
                                    </button>
                                </div>
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