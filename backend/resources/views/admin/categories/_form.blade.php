<div class="card">
    <form method="POST" action="{{ isset($category) ? route('admin.categories.update', $category) : route('admin.categories.store') }}">
        @csrf
        @if (isset($category)) @method('PUT') @endif
        <label>Nama kategori</label>
        <input name="name" value="{{ old('name', $category->name ?? '') }}" required>
        <label>Deskripsi</label>
        <textarea name="description" rows="3">{{ old('description', $category->description ?? '') }}</textarea>
        <label style="margin-top:12px"><input type="checkbox" name="is_active" value="1" @checked(old('is_active', isset($category) ? $category->is_active : true))> Aktif</label>
        <div style="margin-top:16px; display:flex; gap:10px;">
            <button class="btn" type="submit">Simpan</button>
            <a class="btn gray" href="{{ route('admin.categories.index') }}">Batal</a>
        </div>
    </form>
</div>
