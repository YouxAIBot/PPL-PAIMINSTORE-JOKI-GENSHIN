<div class="card">
    <form method="POST" action="{{ isset($package) ? route('admin.packages.update', $package) : route('admin.packages.store') }}">
        @csrf
        @if (isset($package)) @method('PUT') @endif
        <div class="row">
            <div>
                <label>Kategori</label>
                <select name="category_id" required>
                    @foreach ($categories as $c)
                        <option value="{{ $c->id }}" @selected(old('category_id', $package->category_id ?? null) == $c->id)>{{ $c->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label>Nama paket</label>
                <input name="name" value="{{ old('name', $package->name ?? '') }}" required>
            </div>
        </div>
        <label>Tier</label>
        <input name="tier" value="{{ old('tier', $package->tier ?? '') }}" placeholder="mis. Dasar, Menengah, Tinggi">
        <label>Deskripsi</label>
        <textarea name="description" rows="3">{{ old('description', $package->description ?? '') }}</textarea>
        <div class="row">
            <div>
                <label>Harga (Rp)</label>
                <input type="number" min="0" step="1000" name="price" value="{{ old('price', $package->price ?? '') }}" required>
            </div>
            <div>
                <label>Estimasi durasi (jam)</label>
                <input type="number" min="1" name="estimated_hours" value="{{ old('estimated_hours', $package->estimated_hours ?? '') }}" required>
            </div>
        </div>
        <label style="margin-top:12px"><input type="checkbox" name="is_active" value="1" @checked(old('is_active', isset($package) ? $package->is_active : true))> Aktif</label>
        <div style="margin-top:16px; display:flex; gap:10px;">
            <button class="btn" type="submit">Simpan</button>
            <a class="btn gray" href="{{ route('admin.packages.index') }}">Batal</a>
        </div>
    </form>
</div>
