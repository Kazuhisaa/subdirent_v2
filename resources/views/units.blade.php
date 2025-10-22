@extends('layouts.app')

@section('title', 'Available Units | SubdiRent')

@section('content')
<section class="py-5">
    <div class="container">
        <h2 class="fw-bold mb-4 text-primary">🏠 Available Units</h2>

        <div id="units-container" class="row g-4">
            <p class="text-muted">Loading available units...</p>
        </div>
    </div>
</section>

<script>
document.addEventListener("DOMContentLoaded", () => {
    fetch("/units") // ✅ API endpoint
        .then(res => res.json())
        .then(units => {
            const container = document.getElementById("units-container");
            container.innerHTML = ""; // clear loading text

            if (!units.length) {
                container.innerHTML = `<p class="text-muted">No available units at the moment.</p>`;
                return;
            }

            units.forEach(unit => {
                const imageUrl = unit.files?.length ? unit.files[0] : '/uploads/default-room.jpg';
                container.innerHTML += `
                    <div class="col-md-4">
                        <div class="card shadow-sm h-100">
                            <img src="${imageUrl}" class="card-img-top" alt="${unit.title}">
                            <div class="card-body">
                                <h5 class="card-title fw-bold">${unit.title}</h5>
                                <p class="text-muted small mb-2">${unit.location}</p>
                                <p class="text-primary fw-semibold">₱${unit.monthly_rent.toLocaleString()} / month</p>
                                <p class="card-text">${unit.description?.substring(0, 80) || ''}...</p>
                                <a href="#" class="btn btn-outline-primary w-100">View Details</a>
                            </div>
                        </div>
                    </div>
                `;
            });
        })
        .catch(error => {
            console.error("Error fetching units:", error);
            document.getElementById("units-container").innerHTML = `<p class="text-danger">Failed to load units.</p>`;
        });
});
</script>
@endsection
