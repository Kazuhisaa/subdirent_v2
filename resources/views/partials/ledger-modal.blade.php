<div class="modal fade" id="ledgerModal" tabindex="-1" aria-labelledby="ledgerModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
    <div class="modal-content">
      <div class="modal-header bg-dark text-white p-4">
        <h4 class="modal-title fw-bold mb-0" id="ledgerModalLabel">
            <i class="bi bi-file-earmark-spreadsheet me-2"></i> Full Account Ledger
        </h4>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>

      <div class="modal-body p-4">
        @if($payments->isEmpty())
          <div class="text-center py-5 text-muted">
            <i class="bi bi-receipt fs-1 mb-2"></i>
            <p class="lead">No transactions recorded yet.</p>
          </div>
        @else
          <div class="table-responsive">
            <table class="table table-hover align-middle">
              <thead class="table-light">
                <tr>
                  <th>Date Paid</th>
                  <th>For Billing Month</th>
                  <th>Reference No.</th>
                  <th>Type</th>
                  <th>Description</th>
                  <th>Method</th>
                  <th class="text-end">Amount Paid</th>
                  <th>Status</th>
                </tr>
              </thead>
              <tbody>
                @foreach($payments->sortByDesc('payment_date') as $p) {{-- Sort by date for ledger view --}}
                  <tr>
                    <td>
                        {{ \Carbon\Carbon::parse($p->payment_date)->format('M d, Y') }}
                        <div class="small text-muted">{{ \Carbon\Carbon::parse($p->payment_date)->format('H:i A') }}</div>
                    </td>
                    <td><span class="fw-semibold">{{ \Carbon\Carbon::parse($p->for_month)->format('F Y') }}</span></td>
                    <td class="small">{{ $p->reference_no ?? 'N/A' }}</td>
                    <td>
                        @if(stripos($p->remarks, 'Downpayment') !== false)
                            <span class="badge bg-dark">Deposit</span>
                        @elseif(stripos($p->remarks, 'Rent Payment') !== false)
                            <span class="badge bg-primary">Rent</span>
                        @else
                            <span class="badge bg-secondary">Other</span>
                        @endif
                    </td>
                    <td>{{ $p->remarks }}</td>
                    <td>{{ ucfirst($p->payment_method) }}</td>
                    <td class="text-end fw-bold text-success">₱{{ number_format($p->amount, 2) }}</td>
                    <td>
                        @if($p->payment_status === 'paid')
                            <span class="badge bg-success">Paid</span>
                        @elseif($p->payment_status === 'partial')
                            <span class="badge bg-warning text-dark">Partial</span>
                        @else
                            <span class="badge bg-danger">Failed</span>
                        @endif
                    </td>
                  </tr>
                @endforeach
              </tbody>
            </table>
          </div>
        @endif
      </div>
    </div>
  </div>
</div>