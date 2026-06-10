@extends('layouts.app')

@section('content')
<section class="page-header">
    <h1>Order Tracking</h1>
    <p>Pantau dan perbarui status pengerjaan cucian pelanggan</p>
</section>

@if(session('success'))
    <div class="alert-success">{{ session('success') }}</div>
@endif

<div class="toolbar-card">
    <form method="GET" action="{{ route('tracking.index') }}" class="filter-form">
        <select name="status" class="form-control filter-select">
            <option value="">Semua Status</option>
            @foreach($statuses as $status)
                <option value="{{ $status }}" {{ request('status') === $status ? 'selected' : '' }}>
                    {{ ucwords(str_replace('_', ' ', $status)) }}
                </option>
            @endforeach
        </select>

        <button type="submit" class="btn-internal-primary">Filter</button>

        <a href="{{ route('tracking.index') }}" class="btn-cancel">Reset</a>
    </form>
</div>

<div class="tracking-list">
    @forelse($orders as $order)
        <div class="tracking-card">
            <div class="tracking-card-header">
                <div>
                    <h3>{{ $order->order_code }}</h3>
                    <p>{{ $order->customer->user->name ?? '-' }} • {{ $order->service->name ?? '-' }}</p>
                </div>

                <span class="badge {{ $order->status === 'selesai' ? 'badge-green' : 'badge-blue' }}">
                    {{ ucwords(str_replace('_', ' ', $order->status)) }}
                </span>
            </div>

            <div class="tracking-steps">
                @php
                    $flow = ['diterima', 'dicuci', 'dijemur', 'disetrika', 'siap_diambil', 'selesai'];
                    $currentIndex = array_search($order->status, $flow);
                @endphp

                @foreach($flow as $index => $step)
                    <div class="tracking-step {{ $currentIndex !== false && $index <= $currentIndex ? 'active' : '' }}">
                        <div class="step-circle">{{ $index + 1 }}</div>
                        <span>{{ ucwords(str_replace('_', ' ', $step)) }}</span>
                    </div>
                @endforeach
            </div>

            <form method="POST" action="{{ route('tracking.update', $order) }}" class="tracking-update-form">
                @csrf
                @method('PATCH')

                <div class="form-row">
                    <div class="form-group">
                        <label>Ubah Status</label>
                        <select name="status" class="form-control">
                            @foreach($statuses as $status)
                                <option value="{{ $status }}" {{ $order->status === $status ? 'selected' : '' }}>
                                    {{ ucwords(str_replace('_', ' ', $status)) }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Catatan</label>
                        <input type="text" name="note" class="form-control" placeholder="Contoh: Cucian sedang diproses">
                    </div>
                </div>

                <div class="tracking-actions">
                    <a href="{{ route('orders.show', $order) }}" class="btn-cancel">Detail</a>
                    <button type="submit" class="btn-internal-primary">Update Status</button>
                </div>
            </form>
        </div>
    @empty
        <div class="empty-card">
            Belum ada order untuk ditampilkan.
        </div>
    @endforelse
</div>
@endsection
