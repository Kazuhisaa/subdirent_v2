@extends('admin.dashboard')

@section('page-title', 'Units')

@section('content')
<div class="container-fluid py-4">
    {{-- ✅ Page Header --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold text-blue-900 mb-0">🏠 Room Listings</h2>
        <a href="{{ route('admin.addroom') }}" class="btn btn-action fw-bold px-4 rounded-pill">
            + Add Room
        </a>
    </div>

    {{-- ✅ Units Grid --}}
    <div class="row g-4" id="unitsGrid">
        <div class="col-12 text-center text-muted">Loading units...</div>
    </div>
</div>

{{-- Pass token safely from session --}}
<script>
    window.apiToken = "{{ session('admin_api_token') }}";
</script>

{{-- JS Script --}}
<script src="{{ asset('fetch_js/showUnit.js') }}"></script>

{{-- Optional Style --}}
<style>
.archived-card {
    filter: grayscale(100%) brightness(0.85);
    opacity: 0.8;
}
</style>
@endsection
