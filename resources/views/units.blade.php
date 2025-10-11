<div class="row g-4">
                @foreach ($units as $unit)
            <div class="col-md-6 col-lg-4">
                <div class="card unit-card">
                    {{-- Gagamit ng image_url column. Fallback sa Picsum kung walang URL. --}}
                    <img src="{{ $unit->image_url ?? 'https://picsum.photos/600/400?random=' . $loop->iteration }}" class="unit-img" alt="{{ $unit->name }} Image">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <div>
                                <h5 class="card-title">{{ $unit->name }}</h5>
                                <p class="text-muted small mb-0">{{ $unit->address ?? 'Phase Unknown' }}</p>
                            </div>
                            <span class="badge badge-status">{{ $unit->status }}</span>
                        </div>
                        <p class="price mb-2">₱{{ number_format($unit->price, 2) }}/month</p>
                        <p class="text-muted small mb-3">
                            {{ $unit->description }}
                        </p>
                        <div class="d-flex gap-2">
                            <button class="btn btn-outline-primary w-50">Reserve Unit</button>
                            <button class="btn btn-primary w-50">Apply</button>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
      </div>