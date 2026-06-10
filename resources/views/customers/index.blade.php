@extends('layouts.app')

@section('content')
<section class="page-header customer-page-header">
    <h1>Manajemen Pelanggan</h1>
    <p>Kelola data pelanggan laundry Anda</p>
</section>

@if(session('success'))
    <div class="alert-success">{{ session('success') }}</div>
@endif

<div class="customer-toolbar">
    <form method="GET" action="{{ route('customers.index') }}" class="customer-search-form">
        <div class="customer-search-box">
            <span class="search-icon">⌕</span>
            <input
                type="text"
                name="search"
                value="{{ request('search') }}"
                placeholder="Cari pelanggan (nama, telepon, ID)..."
            >
        </div>
    </form>

    <button type="button" class="customer-add-btn" id="openCreateCustomerModal">
        <span>+</span>
        Tambah Pelanggan Baru
    </button>
</div>

<div class="customer-table-card">
    <table class="customer-table">
        <thead>
            <tr>
                <th>ID Pelanggan</th>
                <th>Nama Lengkap</th>
                <th>Nomor Telepon</th>
                <th>Alamat</th>
                <th>Terdaftar Sejak</th>
                <th>Aksi</th>
            </tr>
        </thead>

        <tbody>
            @forelse($customers as $customer)
                <tr>
                    <td>C{{ str_pad($customer->id, 3, '0', STR_PAD_LEFT) }}</td>
                    <td>{{ $customer->user->name ?? '-' }}</td>
                    <td>{{ $customer->phone ?? '-' }}</td>
                    <td>{{ $customer->address ?? '-' }}</td>
                    <td>{{ $customer->created_at ? $customer->created_at->format('d M Y') : '-' }}</td>
                    <td>
                        <div class="customer-action">
                            <button
                                type="button"
                                class="edit-action open-edit-modal"
                                title="Edit"
                                data-id="{{ $customer->id }}"
                                data-name="{{ $customer->user->name ?? '' }}"
                                data-phone="{{ $customer->phone ?? '' }}"
                                data-address="{{ $customer->address ?? '' }}"
                            >
                                ✎
                            </button>

                            <form
                                method="POST"
                                action="{{ route('customers.destroy', $customer) }}"
                                onsubmit="return confirm('Yakin ingin menghapus pelanggan ini?')"
                            >
                                @csrf
                                @method('DELETE')

                                <button type="submit" class="delete-action" title="Hapus">
                                    🗑
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="empty-row">
                        Belum ada data pelanggan.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

{{-- MODAL TAMBAH --}}
<div class="modal-overlay" id="createCustomerModal">
    <div class="customer-modal-card">
        <div class="customer-modal-header">
            <h3>Tambah Pelanggan Baru</h3>
            <button type="button" class="modal-close-btn" data-close-modal>&times;</button>
        </div>

        <form method="POST" action="{{ route('customers.store') }}" class="customer-modal-form">
            @csrf
            <input type="hidden" name="_mode" value="create">

            <div class="modal-form-group">
                <label>Nama Lengkap</label>
                <input
                    type="text"
                    name="name"
                    placeholder="Masukkan nama"
                    value="{{ old('_mode') === 'create' ? old('name') : '' }}"
                >
                @if(old('_mode') === 'create')
                    @error('name') <small class="error-text">{{ $message }}</small> @enderror
                @endif
            </div>

            <div class="modal-form-group">
                <label>Nomor Telepon</label>
                <input
                    type="text"
                    name="phone"
                    placeholder="Masukkan nomor"
                    value="{{ old('_mode') === 'create' ? old('phone') : '' }}"
                >
                @if(old('_mode') === 'create')
                    @error('phone') <small class="error-text">{{ $message }}</small> @enderror
                @endif
            </div>

            <div class="modal-form-group">
                <label>Alamat</label>
                <textarea
                    name="address"
                    placeholder="Masukkan alamat"
                >{{ old('_mode') === 'create' ? old('address') : '' }}</textarea>
                @if(old('_mode') === 'create')
                    @error('address') <small class="error-text">{{ $message }}</small> @enderror
                @endif
            </div>

            <div class="customer-modal-actions">
                <button type="button" class="modal-cancel-btn" data-close-modal>Batal</button>
                <button type="submit" class="modal-submit-btn">Tambah Pelanggan</button>
            </div>
        </form>
    </div>
</div>

{{-- MODAL EDIT --}}
<div class="modal-overlay" id="editCustomerModal">
    <div class="customer-modal-card">
        <div class="customer-modal-header">
            <h3>Edit Pelanggan</h3>
            <button type="button" class="modal-close-btn" data-close-modal>&times;</button>
        </div>

        <form method="POST" action="#" id="editCustomerForm" class="customer-modal-form">
            @csrf
            @method('PUT')
            <input type="hidden" name="_mode" value="edit">
            <input type="hidden" name="customer_id" id="edit_customer_id" value="">

            <div class="modal-form-group">
                <label>Nama Lengkap</label>
                <input type="text" name="name" id="edit_name" placeholder="Masukkan nama">
                @if(old('_mode') === 'edit')
                    @error('name') <small class="error-text">{{ $message }}</small> @enderror
                @endif
            </div>

            <div class="modal-form-group">
                <label>Nomor Telepon</label>
                <input type="text" name="phone" id="edit_phone" placeholder="Masukkan nomor">
                @if(old('_mode') === 'edit')
                    @error('phone') <small class="error-text">{{ $message }}</small> @enderror
                @endif
            </div>

            <div class="modal-form-group">
                <label>Alamat</label>
                <textarea name="address" id="edit_address" placeholder="Masukkan alamat"></textarea>
                @if(old('_mode') === 'edit')
                    @error('address') <small class="error-text">{{ $message }}</small> @enderror
                @endif
            </div>

            <div class="customer-modal-actions">
                <button type="button" class="modal-cancel-btn" data-close-modal>Batal</button>
                <button type="submit" class="modal-submit-btn">Simpan Perubahan</button>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const createModal = document.getElementById('createCustomerModal');
    const editModal = document.getElementById('editCustomerModal');
    const openCreateBtn = document.getElementById('openCreateCustomerModal');
    const closeButtons = document.querySelectorAll('[data-close-modal]');
    const editButtons = document.querySelectorAll('.open-edit-modal');
    const editForm = document.getElementById('editCustomerForm');
    const customerBaseUrl = "{{ url('customers') }}";

    const editId = document.getElementById('edit_customer_id');
    const editName = document.getElementById('edit_name');
    const editPhone = document.getElementById('edit_phone');
    const editAddress = document.getElementById('edit_address');

    function openModal(modal) {
        modal.classList.add('show');
        document.body.classList.add('modal-open');
    }

    function closeModal(modal) {
        modal.classList.remove('show');
        document.body.classList.remove('modal-open');
    }

    openCreateBtn.addEventListener('click', function () {
        openModal(createModal);
    });

    editButtons.forEach(button => {
        button.addEventListener('click', function () {
            const id = this.dataset.id;
            const name = this.dataset.name;
            const phone = this.dataset.phone;
            const address = this.dataset.address;

            editForm.action = `${customerBaseUrl}/${id}`;
            editId.value = id;
            editName.value = name;
            editPhone.value = phone;
            editAddress.value = address;

            openModal(editModal);
        });
    });

    closeButtons.forEach(button => {
        button.addEventListener('click', function () {
            closeModal(createModal);
            closeModal(editModal);
        });
    });

    [createModal, editModal].forEach(modal => {
        modal.addEventListener('click', function (e) {
            if (e.target === modal) {
                closeModal(modal);
            }
        });
    });

    @if($errors->any())
        const oldMode = @json(old('_mode'));

        if (oldMode === 'create') {
            openModal(createModal);
        }

        if (oldMode === 'edit') {
            const oldCustomerId = @json(old('customer_id'));
            editForm.action = `${customerBaseUrl}/${oldCustomerId}`;
            editId.value = oldCustomerId;
            editName.value = @json(old('name'));
            editPhone.value = @json(old('phone'));
            editAddress.value = @json(old('address'));
            openModal(editModal);
        }
    @endif
});
</script>
@endsection
