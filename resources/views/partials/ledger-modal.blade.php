<!-- tenant/partials/ledger-modal.blade.php -->
<div class="modal fade" id="ledgerModal" tabindex="-1" aria-labelledby="ledgerModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
    <div class="modal-content">
      <div class="modal-header bg-primary text-white">
        <h5 class="modal-title fw-bold" id="ledgerModalLabel">Account Ledger</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>

      <div class="modal-body">
        @if($payments->isEmpty())
          <div class="text-center py-5 text-muted">
            <i class="bi bi-receipt fs-1 mb-2"></i>
            <p>No transactions yet.</p>
          </div>
        @else
          <div class="table-responsive">
            <table class="table table-hover align-middle">
              <thead class="table-light">
                <tr>
                  <th>Date</th>
                  <th>For Month</th>
                  <th>Description</th>
                  <th>Method</th>
                  <th class="text-end">Amount</th>
                </tr>
              </thead>
              <tbody>
                @foreach($payments as $p)
                  <tr>
                    <td>{{ \Carbon\Carbon::parse($p->payment_date)->format('M d, Y') }}</td>
                    <td>{{ \Carbon\Carbon::parse($p->for_month)->format('F Y') }}</td>
                    <td>{{ $p->remarks ?? 'Rent Payment' }}</td>
                    <td>{{ ucfirst($p->payment_method) }}</td>
                    <td class="text-end">₱{{ number_format($p->amount, 2) }}</td>
                    
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
