@extends('admin.dashboard')

@section('page-title', 'Units')

@section('content')
<div class="container-fluid py-4">

    {{-- ✅ Page Header --}}
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4">
        <h2 class="fw-bold text-blue-900 mb-0">🏠 Room Listings</h2>

        {{-- ✅ Search + Add --}}
        <div class="d-flex align-items-center gap-2">
            <input type="text" id="unitSearch" class="form-control me-2" placeholder="Search Units..." style="width: 250px;">
            <a href="{{ route('admin.addroom') }}" class="btn btn-action fw-bold px-4 rounded-pill">
                + Add Room
            </a>
        </div>
    </div>

    {{-- ✅ Units Grid --}}
    <div class="row g-4" id="unitsGrid">
        <div class="col-12 text-center text-muted">Loading units...</div>
    </div>
</div>

{{-- ✅ Pass API token safely --}}
<script>
    sessionStorage.setItem('admin_api_token', '{{ session('admin_api_token') }}');
</script>

{{-- ✅ Script --}}
<script src="{{ asset('fetch_js/showUnit.js') }}"></script>

<style>
.archived-card {
    filter: grayscale(100%) brightness(0.85);
    opacity: 0.8;
}
</style>
@endsection
