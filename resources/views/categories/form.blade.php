{{-- Partial form kategori: menerima $category (null untuk buat baru), $departments (null jika bukan admin global) --}}
<div class="space-y-6">
    @if (auth()->user()->isAdmin())
        <div>
            <label for="department_id" class="block font-medium text-sm text-gray-700">Departemen <span class="text-red-500">*</span></label>
            <select id="department_id" name="department_id" required
                    class="mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 @error('department_id') border-red-500 @enderror">
                <option value="">— Pilih departemen —</option>
                @foreach ($departments as $department)
                    <option value="{{ $department->id }}" {{ old('department_id', $category->department_id ?? '') == $department->id ? 'selected' : '' }}>{{ $department->name }}</option>
                @endforeach
            </select>
            @error('department_id') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
        </div>
    @else
        <div>
            <span class="block font-medium text-sm text-gray-700">Departemen</span>
            <p class="mt-1 inline-flex items-center rounded-md bg-violet-50 px-2 py-1 text-xs font-medium text-violet-700 ring-1 ring-inset ring-violet-600/20">
                {{ auth()->user()->department?->name ?? '-' }}
            </p>
            <p class="mt-1 text-xs text-gray-500">Kategori otomatis masuk ke departemen Anda.</p>
        </div>
    @endif

    <div>
        <label for="name" class="block font-medium text-sm text-gray-700">Nama Kategori <span class="text-red-500">*</span></label>
        <input id="name" name="name" type="text" required maxlength="50"
               value="{{ old('name', $category->name ?? '') }}"
               placeholder="Contoh: Hardware"
               class="mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 @error('name') border-red-500 @enderror" />
        @error('name') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
    </div>

    <div>
        <label for="description" class="block font-medium text-sm text-gray-700">Deskripsi</label>
        <input id="description" name="description" type="text" maxlength="255"
               value="{{ old('description', $category->description ?? '') }}"
               placeholder="Penjelasan singkat kategori (opsional)"
               class="mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 @error('description') border-red-500 @enderror" />
        @error('description') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
    </div>
</div>
