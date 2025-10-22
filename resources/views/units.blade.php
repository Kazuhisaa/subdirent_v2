@extends('layouts.app')

@section('title', 'Available Units | SubdiRent')

@section('content')
<style>
/* 🧩 Fix overlay issues */
.modal-backdrop.show {
  opacity: 0.5 !important;
  z-index: 1040 !important;
}

.modal-backdrop {
  z-index: 1040 !important;
}

.modal {
  z-index: 1055 !important;
  pointer-events: auto !important;
}

.modal-dialog-centered {
  display: flex;
  align-items: center;
  min-height: 100vh;
}

body.modal-open {
  overflow: hidden; /* prevent background scroll */
}
</style>

<section class="py-5">
  <div class="container">
    <h2 class="fw-bold mb-4 text-primary">🏠 Available Units</h2>

    <div id="units-container" class="row g-4">
      <p class="text-muted">Loading available units...</p>
    </div>
  </div>
</section>

<!-- 🟢 Reserve Unit Modal -->
<div class="modal fade" id="reserveModal" tabindex="-1" aria-labelledby="reserveModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-lg">
    <div class="modal-content border-0 shadow-lg">
      <div class="modal-header bg-primary text-white">
        <h5 class="modal-title" id="reserveModalLabel">Reserve Unit</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>

      <form id="reserveForm">
        <div class="modal-body">
          <input type="hidden" id="unit_id" name="unit_id">

          <div class="row g-3">
            <div class="col-md-4">
              <label class="form-label">First Name</label>
              <input type="text" class="form-control" name="first_name" required>
            </div>
            <div class="col-md-4">
              <label class="form-label">Middle Name</label>
              <input type="text" class="form-control" name="middle_name">
            </div>
            <div class="col-md-4">
              <label class="form-label">Last Name</label>
              <input type="text" class="form-control" name="last_name" required>
            </div>
            <div class="col-md-6">
              <label class="form-label">Email</label>
              <input type="email" class="form-control" name="email" required>
            </div>
            <div class="col-md-6">
              <label class="form-label">Contact Number</label>
              <input type="text" class="form-control" name="contact_num" required>
            </div>
            <div class="col-md-6">
              <label class="form-label">Date</label>
              <input type="date" class="form-control" name="date" required>
            </div>
            <div class="col-md-6">
              <label class="form-label">Booking Time</label>
              <input type="time" class="form-control" name="booking_time" required>
            </div>
          </div>
        </div>

        <div class="modal-footer bg-light">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-primary">Confirm Reservation</button>
        </div>
      </form>
    </div>
  </div>
</div>

<script>
document.addEventListener("DOMContentLoaded", () => {
  fetch("/units")
    .then(res => res.json())
    .then(units => {
      const container = document.getElementById("units-container");
      container.innerHTML = "";

      if (!units.length) {
        container.innerHTML = `<p class="text-muted">No available units at the moment.</p>`;
        return;
      }

      units.forEach(unit => {
        const imageUrl = unit.files?.length ? `/${unit.files[0]}` : '/uploads/default-room.jpg';

        container.innerHTML += `
          <div class="col-md-4">
            <div class="card shadow-sm h-100">
              <img src="${imageUrl}" class="card-img-top" alt="${unit.title}">
              <div class="card-body d-flex flex-column">
                <h5 class="card-title fw-bold mb-1">${unit.title}</h5>
                <p class="text-muted small mb-2">📍 ${unit.location}</p>
                <p class="text-primary fw-semibold mb-2">₱${unit.monthly_rent.toLocaleString()} / month</p>
                <p class="card-text mb-3 flex-grow-1">${unit.description?.substring(0, 80) || ''}...</p>

                <div class="d-grid gap-2">
                  <button class="btn btn-primary reserve-btn" data-id="${unit.id}" data-title="${unit.title}">
                    <i class="bi bi-calendar-check"></i> Reserve Unit
                  </button>
                  <a href="/application/${unit.id}" class="btn btn-outline-primary">
                    <i class="bi bi-file-earmark-text"></i> Apply Now
                  </a>
                </div>
              </div>
            </div>
          </div>
        `;
      });

      // ✅ Show modal when "Reserve Unit" is clicked
      document.querySelectorAll('.reserve-btn').forEach(btn => {
        btn.addEventListener('click', e => {
          const button = e.currentTarget;
          const id = button.dataset.id;
          const title = button.dataset.title;

          document.getElementById('unit_id').value = id;
          document.getElementById('reserveModalLabel').textContent = `Reserve Unit - ${title}`;

          const modal = new bootstrap.Modal(document.getElementById('reserveModal'));
          modal.show();
        });
      });
    })
    .catch(error => {
      console.error("Error fetching units:", error);
      document.getElementById("units-container").innerHTML = `<p class="text-danger">Failed to load units.</p>`;
    });

  // ✅ Handle reservation form
  document.getElementById('reserveForm').addEventListener('submit', async (e) => {
    e.preventDefault();
    const formData = new FormData(e.target);

    try {
      const res = await fetch('/api/bookings', {
        method: 'POST',
        body: formData
      });

      if (!res.ok) throw new Error('Reservation failed');
      const data = await res.json();

      alert('✅ Reservation successful!');
      bootstrap.Modal.getInstance(document.getElementById('reserveModal')).hide();
      e.target.reset();
    } catch (err) {
      console.error(err);
      alert('❌ Failed to reserve unit.');
    }
  });
});
</script>
@endsection
