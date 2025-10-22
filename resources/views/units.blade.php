@extends('layouts.app')

@section('title', 'Available Units | SubdiRent')

@section('content')


<section class="py-5">
  <div class="container">
    <h2 class="fw-bold mb-4 text-primary">🏠 Available Units</h2>

    <div class="mb-4">
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

    <div id="units-container" class="row g-4">
      <p class="text-muted">Loading available units...</p>
    </div>
  </div>
</section>


<div id="reserveModal" class="modal-overlay">
  <div class="modal-content">
    <h4 class="text-center mb-4" id="reserveModalLabel">Reserve Unit</h4>

    <form id="reserveForm">
      <input type="hidden" id="unit_id" name="unit_id">

      <div class="row">
        <div class="col-md-6 mb-3">
          <label class="form-label">First Name</label>
          <input type="text" class="form-control" name="first_name" required>
        </div>
        <div class="col-md-6 mb-3">
          <label class="form-label">Middle Name</label>
          <input type="text" class="form-control" name="middle_name">
        </div>
        <div class="col-md-6 mb-3">
          <label class="form-label">Last Name</label>
          <input type="text" class="form-control" name="last_name" required>
        </div>
        <div class="col-md-6 mb-3">
          <label class="form-label">Email</label>
          <input type="email" class="form-control" name="email" required>
        </div>
        <div class="col-md-6 mb-3">
          <label class="form-label">Contact Number</label>
          <input type="text" class="form-control" name="contact_num" required>
        </div>
        <div class="col-md-6 mb-3">
          <label class="form-label">Date</label>
          <input type="date" class="form-control" name="date" required>
        </div>
        <div class="col-md-6 mb-3">
          <label class="form-label">Booking Time</label>
          <input type="time" class="form-control" name="booking_time" required>
        </div>
      </div>

      <div class="text-center mt-4">
        <button type="submit" class="btn btn-success px-4">Reserve</button>
        <button type="button" class="btn btn-outline-danger px-4 closeModalBtn">Cancel</button>
      </div>
    </form>
  </div>
</div>

<div id="applyModal" class="modal-overlay">
  <div class="modal-content">
    <h4 class="text-center mb-4" id="applyModalLabel">Apply for Unit</h4>

    <form id="applyForm">
      <input type="hidden" id="apply_unit_id" name="unit_id">

      <div class="row">
        <div class="col-md-6 mb-3">
          <label class="form-label">First Name</label>
          <input type="text" class="form-control" name="first_name" required>
        </div>
        <div class="col-md-6 mb-3">
          <label class="form-label">Middle Name</label>
          <input type="text" class="form-control" name="middle_name">
        </div>
        <div class="col-md-6 mb-3">
          <label class="form-label">Last Name</label>
          <input type="text" class="form-control" name="last_name" required>
        </div>
        <div class="col-md-6 mb-3">
          <label class="form-label">Email</label>
          <input type="email" class="form-control" name="email" required>
        </div>
        <div class="col-md-12 mb-3">
          <label class="form-label">Contact Number</label>
          <input type="text" class="form-control" name="contact_num" required>
        </div>
      </div>

      <div class="text-center mt-4">
        <button type="submit" class="btn btn-success px-4">Submit Application</button>
        <button type="button" class="btn btn-outline-danger px-4 closeModalBtn">Cancel</button>
      </div>
    </form>
  </div>
</div>


<style>
  .search-filter-container {
  display: flex;
  flex-direction: column;
  gap: 10px;
}

.search-box {
  border-radius: 100px;
  padding: 10px 15px;
  border: 1px solid #3a0b8dff;
  font-size: 16px;
}

.filter-buttons {
  display: flex;
  flex-wrap: wrap;
  gap: 8px;
}

.filter-btn {
  border: none;
  background: #ffffff19;
  padding: 8px 16px;
  border-radius: 20px;
  font-weight: 500;
  cursor: pointer;
  transition: all 0.2s ease;
}

.filter-btn:hover {
  background-color: var(--blue-200);
}

.filter-btn.active {
      background: var(--gradient-diagonal);
  color: white;
}

@media (max-width: 768px) {
  .search-box {
    font-size: 14px;
  }
  .filter-buttons {
    justify-content: center;
  }
  .filter-btn {
    padding: 6px 12px;
    font-size: 14px;
  }
}

.modal-overlay {
  display: none;
  position: fixed;
  inset: 0;
  background-color: rgba(255, 255, 255, 0.9);
  justify-content: center;
  align-items: flex-start;
  padding-top: 5vh;
  z-index: 1050;
  overflow-y: auto;
}

.modal-content {
  background: white;
  border-radius: 1px;
  padding: 30px;
  width: 600px;
  max-width: 90%;
  box-shadow: 0 0 15px rgba(32, 3, 3, 0.85);
  animation: fadeInUp 0.3s ease;
  position: relative;
}

@keyframes fadeInUp {
  from { opacity: 0; transform: translateY(30px); }
  to { opacity: 1; transform: translateY(0); }
}

.modal-content h4 {
  color: #2084d6ff;
  font-weight: 600;
  border-bottom: 2px solid #0ab2beff;
  padding-bottom: 10px;
  
}

.modal-overlay.show {
  display: flex;
}

@media (max-width: 768px) {
  .modal-content {
    width: 95%;
    padding: 20px;
    border-radius: 10px;
  }
}
</style>

<script>
document.addEventListener("DOMContentLoaded", () => {
  const reserveModal = document.getElementById("reserveModal");
  const applyModal = document.getElementById("applyModal");
  const reserveForm = document.getElementById("reserveForm");
  const applyForm = document.getElementById("applyForm");

  const container = document.getElementById("units-container");
  const searchInput = document.getElementById("searchInput");
  const filterButtons = document.querySelectorAll(".filter-btn");


  let allUnits = [];
  let currentPhase = "all";

  // 🟢 Fetch and render units
  fetch("/units")
    .then(res => res.json())
    .then(units => {
      allUnits = units;
      renderUnits(allUnits);
    })
    .catch(err => {
      console.error("Error fetching units:", err);
      container.innerHTML = `<p class="text-danger">Failed to load units.</p>`;
    });

  // 🟢 Render units dynamically
  function renderUnits(units) {
    container.innerHTML = "";

    if (!units.length) {
      container.innerHTML = `<p class="text-muted">No available units found.</p>`;
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
              <p class="text-muted small mb-2">📍 ${unit.location || 'N/A'}</p>
              <p class="text-primary fw-semibold mb-2">₱${unit.monthly_rent?.toLocaleString() || 0} / month</p>
              <p class="card-text mb-3 flex-grow-1">${unit.description?.substring(0, 80) || ''}...</p>

              <div class="d-grid gap-2">
                <button class="btn btn-primary reserve-btn" data-id="${unit.id}" data-title="${unit.title}">
                  <i class="bi bi-calendar-check"></i> Reserve Unit
                </button>
                <button class="btn btn-outline-primary apply-btn" data-id="${unit.id}" data-title="${unit.title}">
                  <i class="bi bi-file-earmark-text"></i> Apply Now
                </button>
              </div>
            </div>
          </div>
        </div>`;
    });

    attachButtonEvents();
  }

  // 🟢 Search and Filter logic
  searchInput.addEventListener("input", applyFilters);
  filterButtons.forEach(btn => {
    btn.addEventListener("click", () => {
      filterButtons.forEach(b => b.classList.remove("active"));
      btn.classList.add("active");
      currentPhase = btn.dataset.phase;
      applyFilters();
    });
  });

  function applyFilters() {
  const term = searchInput.value.toLowerCase();
  const filtered = allUnits.filter(unit => {
    const matchesSearch = unit.title.toLowerCase().includes(term);

    // Normalize both filter and unit location text
    const normalize = str => str
      ?.toLowerCase()
      .replace(/phase\s+/i, "")
      .replace(/\bii\b/g, "2")
      .replace(/\biii\b/g, "3")
      .replace(/\biv\b/g, "4")
      .replace(/\bv\b/g, "5")
      .replace(/\bvi\b/g, "6")
      .replace(/\bvii\b/g, "7")
      .replace(/\bviii\b/g, "8")
      .replace(/\bix\b/g, "9")
      .replace(/\bx\b/g, "10")
      .trim();

    const unitPhase = normalize(unit.location || "");
    const filterPhase = normalize(currentPhase);

    const matchesPhase =
      currentPhase === "all" || unitPhase === filterPhase;

    return matchesSearch && matchesPhase;
  });

  renderUnits(filtered);
}

  // 🟢 Attach modal button actions
  function attachButtonEvents() {
    document.querySelectorAll(".reserve-btn").forEach(btn => {
      btn.addEventListener("click", e => {
        const { id, title } = e.currentTarget.dataset;
        document.getElementById("unit_id").value = id;
        document.getElementById("reserveModalLabel").textContent = `Reserve Unit - ${title}`;
        reserveModal.classList.add("show");
      });
    });

    document.querySelectorAll(".apply-btn").forEach(btn => {
      btn.addEventListener("click", e => {
        const { id, title } = e.currentTarget.dataset;
        document.getElementById("apply_unit_id").value = id;
        document.getElementById("applyModalLabel").textContent = `Apply for Unit - ${title}`;
        applyModal.classList.add("show");
      });
    });
  }

  // 🟢 Close Modals
  document.querySelectorAll(".closeModalBtn").forEach(btn => {
    btn.addEventListener("click", () => {
      reserveModal.classList.remove("show");
      applyModal.classList.remove("show");
    });
  });
  [reserveModal, applyModal].forEach(modal => {
    modal.addEventListener("click", e => {
      if (e.target === modal) modal.classList.remove("show");
    });
  });

  // 🟢 Submit Reserve Form
  reserveForm.addEventListener("submit", async e => {
    e.preventDefault();
    const formData = new FormData(reserveForm);
    try {
      const res = await fetch("/api/bookings", { method: "POST", body: formData });
      if (!res.ok) throw new Error("Reservation failed");
      alert("✅ Reservation successful!");
      reserveForm.reset();
      reserveModal.classList.remove("show");
    } catch (err) {
      alert("❌ Failed to reserve unit.");
    }
  });

  // 🟢 Submit Apply Form
  applyForm.addEventListener("submit", async e => {
    e.preventDefault();
    const formData = new FormData(applyForm);
    try {
      const res = await fetch("/api/applications", { method: "POST", body: formData });
      if (!res.ok) throw new Error("Application failed");
      alert("✅ Application submitted successfully!");
      applyForm.reset();
      applyModal.classList.remove("show");
    } catch (err) {
      alert("❌ Failed to submit application.");
    }
  });
});
</script>

@endsection