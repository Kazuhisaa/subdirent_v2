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