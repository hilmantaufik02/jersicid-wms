<x-admin-layout title="Tambah Supplier">
    <x-slot name="pageTitle">Tambah Supplier Baru</x-slot>
    <div class="max-w-2xl mx-auto">
        <form action="{{ route('suppliers.store') }}" method="POST" class="glass-panel p-6">
            @csrf
            <div class="mb-4">
                <label class="form-label">Nama Supplier</label>
                <input type="text" name="name" required class="form-input" value="{{ old('name') }}">
            </div>
            <div class="mb-4">
                <label class="form-label">Kontak Person</label>
                <input type="text" name="contact_person" class="form-input" value="{{ old('contact_person') }}">
            </div>
            <div class="mb-4">
                <label class="form-label">Email</label>
                <input type="email" name="email" class="form-input" value="{{ old('email') }}">
            </div>
            <div class="mb-4">
                <label class="form-label">No. Telepon</label>
                <input type="text" name="phone" class="form-input" value="{{ old('phone') }}">
            </div>
            <div class="mb-4">
                <label class="form-label">Alamat</label>
                <textarea name="address" class="form-input" rows="3">{{ old('address') }}</textarea>
            </div>
            <div class="mb-4">
                <label class="form-label">Kota</label>
                <input type="text" name="city" class="form-input" value="{{ old('city') }}">
            </div>
            <div class="mb-4">
                <label class="form-label">Lead Time (Hari)</label>
                <input type="number" name="lead_time_days" class="form-input" value="{{ old('lead_time_days') }}">
            </div>
            <div class="flex justify-end gap-3">
                <a href="{{ route('suppliers.index') }}" class="px-4 py-2 bg-slate-800 hover:bg-slate-700 text-white text-sm font-semibold rounded-xl transition-all flex items-center gap-2 border border-slate-700">Batal</a>
                <button type="submit" class="px-4 py-2 bg-slate-800 hover:bg-slate-700 text-white text-sm font-semibold rounded-xl transition-all flex items-center gap-2 border border-slate-700">Simpan</button>
            </div>
        </form>
    </div>
</x-admin-layout>