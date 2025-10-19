@extends('admin.dashboard')

@section('page-title', 'Units')

@section('content')
<div class="container-fluid py-4">
    {{-- ✅ Page Header --}}
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4">
        <h2 class="fw-bold text-blue-900 mb-0">🏠 Room Listings</h2>

        <div class="d-flex align-items-center gap-3">
            {{-- 🔍 Search bar --}}
            <input type="text" id="unitSearch" 
                   class="form-control shadow-sm rounded-pill px-3"
                   placeholder="Search units..."
                   style="width: 250px;">

            <a href="{{ route('admin.addroom') }}" 
               class="btn btn-action fw-bold px-4 rounded-pill">
                + Add Room
            </a>
        </div>
    </div>

    {{-- ✅ Search Results --}}
    <div id="searchResults" class="row g-4 mb-4" style="display:none;"></div>

    {{-- ✅ Units Grid (default) --}}
    <div class="row g-4" id="unitsGrid">
        <div class="col-12 text-center text-muted">Loading units...</div>
    </div>
</div>

{{-- ✅ Pass API token safely --}}
<script>
    sessionStorage.setItem('admin_api_token', '{{ session('admin_api_token') }}');
</script>

{{-- ✅ Existing Fetch Script --}}
<script src="{{ asset('fetch_js/showUnit.js') }}"></script>

{{-- ✅ Search Script --}}
<script>
document.addEventListener('DOMContentLoaded', () => {
    const searchInput = document.getElementById('unitSearch');
    const unitsGrid = document.getElementById('unitsGrid');
    const searchResults = document.getElementById('searchResults');

    searchInput.addEventListener('keyup', async (e) => {
        const query = e.target.value.trim();

        if (!query) {
            // 🔄 Show default grid when search is cleared
            searchResults.style.display = 'none';
            searchResults.innerHTML = '';
            unitsGrid.style.display = 'flex';
            return;
        }

        try {
            const res = await fetch(`{{ route('admin.units.search') }}?query=${encodeURIComponent(query)}`);
            if (!res.ok) throw new Error(`HTTP ${res.status}`);
            const units = await res.json();

            // Hide original grid
            unitsGrid.style.display = 'none';
            searchResults.style.display = 'flex';
            searchResults.innerHTML = '';

            if (units.length === 0) {
                searchResults.innerHTML = `
                    <div class="col-12 text-center py-4 text-muted">
                        <img src="/images/empty-state.svg" alt="No results" width="100" class="mb-3">
                        <p>No units found for "<strong>${query}</strong>".</p>
                    </div>`;
                return;
            }

            units.forEach(unit => {
                const image = unit.image_path 
                    ? `/storage/${unit.image_path}` 
                    : `/images/no-image.png`;

                const card = `
                    <div class="col-lg-4 col-md-6">
                        <div class="card border-0 shadow-sm rounded-4 overflow-hidden h-100 room-card">
                            <div class="position-relative" style="height: 200px; background: #f5f9ff;">
                                <img src="${image}" alt="${unit.title}" 
                                     class="w-100 h-100 object-fit-cover">
                            </div>
                            <div class="card-body">
                                <h5 class="fw-bold text-blue-900 mb-1">${unit.title ?? 'Untitled'}</h5>
                                <p class="text-blue-700 small mb-2">
                                    <i class="bi bi-geo-alt-fill"></i> ${unit.location ?? 'Unknown'}
                                </p>
                                <div class="small text-blue-800 lh-sm mb-3">
                                    <p class="mb-1"><strong>Phase:</strong> ${unit.phase || 'N/A'}</p>
                                    <p class="mb-1"><strong>Floor Area:</strong> ${unit.floor_area || 'N/A'} sqm</p>
                                    <p class="mb-1"><strong>Bedroom:</strong> ${unit.bedroom || 'N/A'}</p>
                                    <p class="mb-1"><strong>Bathroom:</strong> ${unit.bathroom || 'N/A'}</p>
                                </div>
                                <div class="border-top pt-2">
                                    <p class="fw-semibold text-blue-800 mb-0">
  Price: ₱${parseFloat((unit.unit_price || '0').toString().replace(/[^\d.]/g, '')).toLocaleString()}
</p>

                                </div>
                            </div>
                        </div>
                    </div>`;
                searchResults.insertAdjacentHTML('beforeend', card);
            });

        } catch (err) {
            console.error('Search Error:', err);
            searchResults.innerHTML = `<div class="col-12 text-danger text-center">⚠ Error loading search results.</div>`;
        }
    });
});
</script>

{{-- ✅ Small styling --}}
<style>
#unitSearch {
    border: 1px solid #d0e3ff;
    transition: 0.2s;
}
#unitSearch:focus {
    border-color: #0056d2;
    box-shadow: 0 0 0 3px rgba(0,86,210,0.1);
}
</style>
@endsection
