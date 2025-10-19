@extends('admin.dashboard')

@section('page-title', 'Units')

@section('content')
<div class="container-fluid py-4">
    {{-- ✅ Page Header --}}
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4">
        <h2 class="fw-bold text-blue-900 mb-0">🏠 Room Listings</h2>

        {{-- ✅ Search + Add Button --}}
        <div class="d-flex align-items-center gap-2">
            <input type="text" id="unitSearch" class="form-control me-2" placeholder="Search Units..." style="width: 250px;">
            <a href="{{ route('admin.addroom') }}" 
               class="btn btn-action fw-bold px-4 rounded-pill">
                + Add Room
            </a>
        </div>
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

{{-- ✅ AJAX Search Script --}}
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(document).ready(function() {
    $('#unitSearch').on('keyup', function() {
        let query = $(this).val().trim();

        if (query.length === 0) {
            $('#searchResults').html('');
            $('#unitGrid').show();
            return;
        }

        $.ajax({
            url: "{{ route('admin.units.search') }}",
            type: 'GET',
            data: { query: query },
            success: function(units) {
                $('#searchResults').html('');
                $('#unitGrid').hide();

                if (units.length === 0) {
                    $('#searchResults').html('<p class="text-muted text-center mt-3">No units found.</p>');
                    return;
                }

                let html = '';
                units.forEach(unit => {
                    let image = unit.image_path ? `/storage/${unit.image_path}` : '/images/no-image.png';

                    html += `
                        <div class="col-md-3">
                            <div class="card shadow-sm border-0">
                                <img src="${image}" class="card-img-top" alt="${unit.title}" style="height: 180px; object-fit: cover;">
                                <div class="card-body">
                                    <h5 class="card-title fw-bold">${unit.title}</h5>
                                    <p class="text-muted mb-1"><i class="bi bi-geo-alt-fill"></i> ${unit.location}</p>
                                    <p class="mb-0"><strong>Phase:</strong> ${unit.phase || 'N/A'}</p>
                                    <p class="mb-0"><strong>Floor Area:</strong> ${unit.floor_area || 'N/A'} sqm</p>
                                    <p class="mb-0"><strong>Rent:</strong> ₱${Number(unit.rent).toLocaleString()}</p>
                                </div>
                            </div>
                        </div>
                    `;
                });

                $('#searchResults').html(html);
            },
            error: function(xhr) {
                console.error('Error:', xhr.responseText);
            }
        });
    });
});
</script>
@endsection
