@extends('layouts.app')

@section('content')
<section class="page-header payment-page-header">
    <h1>Pembayaran</h1>
    <p>Kelola pembayaran dan invoice pelanggan</p>
</section>

@if(session('success'))
    <div class="alert-success">{{ session('success') }}</div>
@endif

<div class="payment-table-card">
    <table class="payment-table">
        <thead>
            <tr>
                <th>No. Invoice</th>
                <th>Nama Pelanggan</th>
                <th>Total Biaya</th>
                <th>Tanggal Jatuh Tempo</th>
                <th>Status Pembayaran</th>
                <th>Aksi</th>
            </tr>
        </thead>

        <tbody>
            @forelse($invoices as $invoice)
                @php
                    $order = $invoice->laundryOrder;
                    $customer = $order?->customer;
                    $user = $customer?->user;
                    $service = $order?->service;

                    $dueDate = \Carbon\Carbon::parse($invoice->issued_at ?? $invoice->created_at)
                        ->addDays(3)
                        ->format('d M Y');

                    $baseTotal = ($invoice->subtotal ?? 0) + ($invoice->delivery_fee ?? 0);
                    $statusLabel = $invoice->status === 'paid' ? 'Lunas' : 'Belum Lunas';
                @endphp

                <tr>
                    <td>{{ $invoice->invoice_code }}</td>
                    <td>{{ $user->name ?? '-' }}</td>
                    <td>Rp {{ number_format($invoice->total_amount, 0, ',', '.') }}</td>
                    <td>{{ $dueDate }}</td>
                    <td>
                        <span class="payment-status {{ $invoice->status === 'paid' ? 'paid' : 'unpaid' }}">
                            {{ $statusLabel }}
                        </span>
                    </td>
                    <td>
                        <button
                            type="button"
                            class="payment-detail-btn open-payment-modal"
                            data-id="{{ $invoice->id }}"
                            data-invoice="{{ $invoice->invoice_code }}"
                            data-customer="{{ $user->name ?? '-' }}"
                            data-service="{{ $service->name ?? '-' }}"
                            data-weight="{{ $order->weight ?? 0 }}"
                            data-quantity="{{ $order->quantity ?? 0 }}"
                            data-subtotal="{{ $invoice->subtotal }}"
                            data-delivery="{{ $invoice->delivery_fee }}"
                            data-total="{{ $baseTotal }}"
                            data-points="{{ $customer->points_balance ?? 0 }}"
                            data-status="{{ $invoice->status }}"
                            data-action="{{ route('payments.process', $invoice) }}"
                        >
                            Rincian & Bayar
                        </button>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="empty-row">
                        Belum ada invoice pembayaran.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

{{-- MODAL RINCIAN PEMBAYARAN --}}
<div class="modal-overlay" id="paymentModal">
    <div class="payment-modal-card">
        <div class="payment-modal-header">
            <h3>Rincian Invoice</h3>
            <button type="button" class="modal-close-btn" data-close-payment-modal>&times;</button>
        </div>

        <form method="POST" action="#" id="paymentForm">
            @csrf

            <div class="payment-detail-row">
                <span>Invoice</span>
                <strong id="modalInvoiceCode">-</strong>
            </div>

            <div class="payment-detail-row">
                <span>Pelanggan</span>
                <strong id="modalCustomerName">-</strong>
            </div>

            <div class="payment-form-group">
                <label>Metode Pembayaran</label>
                <select name="method" id="paymentMethod" required>
                    <option value="">-- Pilih Metode --</option>
                    <option value="cash">Cash</option>
                    <option value="qris">QRIS</option>
                    <option value="transfer">Transfer</option>
                </select>
            </div>

            <div class="payment-form-group">
                <label>Poin Diskon</label>
                <input
                    type="number"
                    name="points_used"
                    id="pointsUsed"
                    min="0"
                    value="0"
                    placeholder="Masukkan poin"
                >
                <small id="availablePointsText">Poin tersedia: 0</small>
            </div>

            <div class="payment-service-box">
                <h4>Detail Layanan</h4>

                <div class="payment-detail-row">
                    <span id="modalServiceName">-</span>
                    <strong id="modalSubtotal">Rp 0</strong>
                </div>

                <div class="payment-detail-row">
                    <span>Biaya Antar</span>
                    <strong id="modalDeliveryFee">Rp 0</strong>
                </div>

                <div class="payment-detail-row">
                    <span>Diskon Poin</span>
                    <strong id="modalPointDiscount">Rp 0</strong>
                </div>

                <div class="payment-detail-row payment-total-row">
                    <span>Total</span>
                    <strong id="modalTotal">Rp 0</strong>
                </div>
            </div>

            <div class="payment-modal-actions">
                <button type="button" class="modal-cancel-btn" data-close-payment-modal>Tutup</button>
                <button type="submit" class="modal-submit-btn" id="processPaymentBtn">
                    Proses Pembayaran
                </button>
            </div>
        </form>
    </div>
</div>

{{-- MODAL PEMBAYARAN BERHASIL --}}
@if($paidInvoice)
    @php
        $paidOrder = $paidInvoice->laundryOrder;
        $paidCustomer = $paidOrder?->customer?->user;
        $paidPayment = $paidInvoice->payment;
    @endphp

    <div class="modal-overlay show" id="successPaymentModal">
        <div class="payment-success-card">
            <h2>Pembayaran Berhasil!</h2>
            <p>Pembayaran untuk invoice {{ $paidInvoice->invoice_code }} telah dikonfirmasi.</p>

            <div class="receipt-box" id="receiptArea">
                <h3>Nota Pembayaran</h3>

                <div class="payment-detail-row">
                    <span>Invoice</span>
                    <strong>{{ $paidInvoice->invoice_code }}</strong>
                </div>

                <div class="payment-detail-row">
                    <span>Pelanggan</span>
                    <strong>{{ $paidCustomer->name ?? '-' }}</strong>
                </div>

                <div class="payment-detail-row">
                    <span>Total Dibayar</span>
                    <strong>Rp {{ number_format($paidPayment->amount_paid ?? $paidInvoice->total_amount, 0, ',', '.') }}</strong>
                </div>

                <div class="payment-detail-row">
                    <span>Tanggal</span>
                    <strong>{{ now()->format('d M Y') }}</strong>
                </div>
            </div>

            <div class="payment-modal-actions">
                <a href="{{ route('payments.index') }}" class="modal-cancel-btn success-link">Selesai</a>
                <button type="button" class="modal-submit-btn" onclick="window.print()">Unduh Nota</button>
            </div>
        </div>
    </div>
@endif

<script>
document.addEventListener('DOMContentLoaded', function () {
    const modal = document.getElementById('paymentModal');
    const openButtons = document.querySelectorAll('.open-payment-modal');
    const closeButtons = document.querySelectorAll('[data-close-payment-modal]');

    const paymentForm = document.getElementById('paymentForm');
    const processPaymentBtn = document.getElementById('processPaymentBtn');

    const invoiceCode = document.getElementById('modalInvoiceCode');
    const customerName = document.getElementById('modalCustomerName');
    const serviceName = document.getElementById('modalServiceName');
    const subtotalText = document.getElementById('modalSubtotal');
    const deliveryText = document.getElementById('modalDeliveryFee');
    const pointDiscountText = document.getElementById('modalPointDiscount');
    const totalText = document.getElementById('modalTotal');
    const pointsInput = document.getElementById('pointsUsed');
    const availablePointsText = document.getElementById('availablePointsText');

    let currentBaseTotal = 0;
    let currentAvailablePoints = 0;
    const pointValue = 100;

    function rupiah(number) {
        return new Intl.NumberFormat('id-ID', {
            style: 'currency',
            currency: 'IDR',
            maximumFractionDigits: 0
        }).format(number);
    }

    function openModal() {
        modal.classList.add('show');
        document.body.classList.add('modal-open');
    }

    function closeModal() {
        modal.classList.remove('show');
        document.body.classList.remove('modal-open');
    }

    function updateTotal() {
        const pointsUsed = Math.min(
            Number(pointsInput.value || 0),
            currentAvailablePoints
        );

        const discount = pointsUsed * pointValue;
        const finalTotal = Math.max(0, currentBaseTotal - discount);

        pointDiscountText.innerText = rupiah(discount);
        totalText.innerText = rupiah(finalTotal);
    }

    openButtons.forEach(button => {
        button.addEventListener('click', function () {
            const status = this.dataset.status;

            paymentForm.action = this.dataset.action;

            invoiceCode.innerText = this.dataset.invoice;
            customerName.innerText = this.dataset.customer;
            serviceName.innerText = this.dataset.service;

            const subtotal = Number(this.dataset.subtotal || 0);
            const delivery = Number(this.dataset.delivery || 0);
            currentBaseTotal = Number(this.dataset.total || 0);
            currentAvailablePoints = Number(this.dataset.points || 0);

            subtotalText.innerText = rupiah(subtotal);
            deliveryText.innerText = rupiah(delivery);
            pointsInput.value = 0;
            pointsInput.max = currentAvailablePoints;
            availablePointsText.innerText = `Poin tersedia: ${currentAvailablePoints}`;

            processPaymentBtn.disabled = status === 'paid';
            processPaymentBtn.innerText = status === 'paid' ? 'Sudah Lunas' : 'Proses Pembayaran';

            updateTotal();
            openModal();
        });
    });

    pointsInput.addEventListener('input', updateTotal);

    closeButtons.forEach(button => {
        button.addEventListener('click', closeModal);
    });

    modal.addEventListener('click', function (event) {
        if (event.target === modal) {
            closeModal();
        }
    });
});
</script>
@endsection
