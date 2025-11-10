@extends('layouts.app')

@section('title', 'Available Units | SubdiRent')

@section('content')

{{-- Google Fonts: Kinuha mula sa home.blade.php --}}

<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&family=Salsa&display=swap" rel="stylesheet">
<link rel="stylesheet" href="{{ asset('css/units.css') }}">

<section class="py-5" style="padding-top: 5rem !important;">
    <div class="container">
        <h2 class="section-heading animate-on-scroll">Available Units</h2>

        <div class="mb-4 animate-on-scroll" data-delay="1">
            <div class="search-filter-container">
                <input type="text" id="searchInput" class="form-control search-box" placeholder="Search Unit Name">

                <div class="filter-buttons mt-3">
                    <button class="filter-btn active" data-phase="all">All Phase</button>
                    <button class="filter-btn" data-phase="Phase I">Phase I</button>
                    <button class="filter-btn" data-phase="Phase II">Phase II</button>
                    <button class="filter-btn" data-phase="Phase III">Phase III</button>
                    <button class="filter-btn" data-phase="Phase IV">Phase IV</button>
                    <button class="filter-btn" data-phase="Phase V">Phase V</button>
                </div>
            </div>
        </div>
        
        <div class="properties-section">
            <div id="units-container" class="row g-4">
                <p class="text-muted text-center">Loading available units...</p>
            </div>
        </div>
    </div>
</section>


{{-- ============================================= --}}
{{-- ==== MODAL HTML (KINUHA SA FIRST CODE) ==== --}}
{{-- ============================================= --}}
{{-- In-update ko ang classes para sa styling --}}

{{-- ==== Reserve Unit Modal ==== --}}
<div id="reserveModal" class="custom-modal">
    <div class="custom-modal-header">
        Reserve Unit
        <span style="float: right; cursor: pointer;" onclick="closeModal('reserveModal')">&times;</span>
    </div>
    <div class="custom-modal-body">
        <form id="reserveForm">
            <input type="hidden" id="reserveUnitId" name="unit_id">
            
            <label for="reserveUnitName" class="form-label"><b>Unit Name</b></label>
            <input type="text" id="reserveUnitName" name="unit_name" readonly class="form-control mb-3">

            <label for="first_name" class="form-label">First Name</label>
            <input type="text" id="first_name" name="first_name" required class="form-control mb-3">

            <label for="first_name" class="form-label">Middle Name</label>
            <input type="text" name="middle_name"  class="form-control mb-3">

            <label for="last_name" class="form-label">Last Name</label>
            <input type="text" id="last_name" name="last_name" required class="form-control mb-3">

            <label for="email" class="form-label">Email</label>
            <input type="email" id="email" name="email" required class="form-control mb-3">

            <label for="contact_num" class="form-label">Contact Number</label>
            <input type="tel" id="contact_num" name="contact_num" maxlength="11" oninput="this.value = this.value.replace(/[^0-9]/g, '');" required 
       class="form-control mb-3">

            {{-- BINAGO MULA "move_in_date" PATUNGONG "date" --}}
            <label for="move_in_date" class="form-label">Preferred Date</label>
            <input type="date" id="move_in_date" name="date" required class="form-control mb-3"> 

            {{-- ITO YUNG MAHALAGANG DAGDAG --}}
            <label for="booking_time" class="form-label">Preferred Time</label>
<input 
    type="time" 
    id="booking_time" 
    name="booking_time" 
    required 
    class="form-control mb-3"
    min="08:00" 
    max="17:00">
</form>
        </form>
    </div>
    <div class="custom-modal-footer">
        <button class="btn btn-details" type="submit" form="reserveForm">Submit Reservation</button>
        <button type="button" class="btn btn-outline-danger" onclick="closeModal('reserveModal')">Cancel</button>
    </div>
</div>

{{-- ==== Apply Now Modal ==== --}}
<div id="applyModal" class="custom-modal">
    <div class="custom-modal-header">
        Apply Now
        <span style="float: right; cursor: pointer;" onclick="closeModal('applyModal')">&times;</span>
    </div>
    <div class="custom-modal-body">
        <form id="applyForm">
            <input type="hidden" id="applyUnitId" name="unit_id">
            
            <label for="applyUnitName" class="form-label"> <b>Unit Name</b></label>
            <input type="text" id="applyUnitName" name="unit_name" readonly class="form-control mb-3">

            <label for="apply_first_name" class="form-label">First Name</label>
            <input type="text" id="apply_first_name" name="first_name" required class="form-control mb-3">

            <label for="apply_first_name" class="form-label">Middle Name</label>
            <input type="text" name="middle_name" class="form-control mb-3">

            <label for="apply_last_name" class="form-label">Last Name</label>
            <input type="text" id="apply_last_name" name="last_name" required class="form-control mb-3">

            <label for="apply_email" class="form-label">Email</label>
            <input type="email" id="apply_email" name="email" required class="form-control mb-3">

            <label for="apply_contact_num" class="form-label">Contact Number</label>
        <input type="tel" id="apply_contact_num" name="contact_num" required class="form-control mb-3"maxlength="11"oninput="this.value = this.value.replace(/[^0-9]/g, '');">

            <label for="message" class="form-label">Message / Remarks</label>
            <textarea id="message" name="remarks" rows="3" class="form-control mb-3"></textarea>
        </form>
    </div>
    <div class="custom-modal-footer">
        <button class="btn btn-details" type="submit" form="applyForm">Submit Application</button>
        <button type="button" class="btn btn-outline-danger" onclick="closeModal('applyModal')">Cancel</button>
    </div>
</div>

{{-- ==== Overlay (KINUHA SA FIRST CODE) ==== --}}
<div id="modalOverlay" class="modal-overlay" onclick="closeAllModals()"></div>

{{-- ==== View Details Modal ==== --}}
<div id="viewDetailsModal" class="custom-modal">
    <div class="custom-modal-header">
        <span id="modalUnitNameHeader">Unit Details</span>
        <span style="float: right; cursor: pointer;" onclick="closeModal('viewDetailsModal')">&times;</span>
    </div>
    <div class="custom-modal-body">
        
        <div class="modal-image-container">
    <img id="modalUnitImage" class="modal-unit-image" src="" alt="Unit Image">

    <!-- Navigation buttons -->
    <button class="modal-nav-btn prev" id="prevImageBtn">&#10094;</button>
    <button class="modal-nav-btn next" id="nextImageBtn">&#10095;</button>
</div>

        <div class="modal-details-grid">
            <div class="modal-detail-item">
                <strong>Phase:</strong>
                <span id="modalUnitPhase"></span>
            </div>
            <div class="modal-detail-item">
                <strong>Code:</strong>
                <span id="modalUnitCode"></span>
            </div>
            <div class="modal-detail-item">
                <strong>Floor Area:</strong>
                <span id="modalUnitFloorArea"></span>
            </div>
            <div class="modal-detail-item">
                <strong>Bedroom:</strong>
                <span id="modalUnitBedroom"></span>
            </div>
            <div class="modal-detail-item">
                <strong>Bathroom:</strong>
                <span id="modalUnitBathroom"></span>
            </div>
            <div class="modal-detail-item">
                <strong>Price:</strong>
                <span id="modalUnitPrice"></span>
            </div>
        </div>

        <div class="modal-description">
            <strong>Description:</strong>
            <p id="modalUnitDescription"></p>
        </div>

    </div>
    <div class="custom-modal-footer">
        <button type="button" class="btn btn-outline-danger" onclick="closeModal('viewDetailsModal')">Close</button>
    </div>
</div>

<script src="{{ asset('js/units.js') }}">
    
</script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

@endsection