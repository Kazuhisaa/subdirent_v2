@extends('admin.dashboard')

@section('content')
<div class="container-fluid py-4">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold" style="color:#333;">Tenant Management</h2>
        <button class="btn btn-success">+ Add Tenant</button>
    </div>

    <div class="table-responsive">
        <table class="table table-bordered align-middle">
            <thead>
                <tr>
                    <th class="text-center" style="background-color:#FFF3C2;">ID</th>
                    <th style="background-color:#FFF3C2;">Full Name</th>
                    <th style="background-color:#FFF3C2;">Email</th>
                    <th style="background-color:#FFF3C2;">Username</th>
                    <th style="background-color:#FFF3C2;">Contact</th>
                    <th style="background-color:#FFF3C2;">Status</th>
                    <th style="background-color:#FFF3C2;">Actions</th>
                </tr>
            </thead>
            <tbody>
                {{-- Example row --}}
                <tr>
                    <td class="text-center">1</td>
                    <td>John Doe</td>
                    <td>johndoe@example.com</td>
                    <td>john_doe</td>
                    <td>09123456789</td>
                    <td><span class="badge bg-success">Active</span></td>
                    <td class="d-flex gap-2">
                        <button class="btn btn-sm btn-primary">Edit</button>
                        <button class="btn btn-sm btn-warning">Archive</button>
                    </td>
                </tr>
                <tr>
                    <td class="text-center">1</td>
                    <td>Jane Smith</td>
                    <td>janesmith@example.com</td>
                    <td>jane_smith</td>
                    <td>09987654321</td>
                    <td><span class="badge bg-secondary">Archived</span></td>
                    <td class="d-flex gap-2">
                        <button class="btn btn-sm btn-primary">Edit</button>
                        {{-- Archived, no archive button --}}
                    </td>
                </tr>
            </tbody>
        </table>
    </div>

</div>
@endsection
