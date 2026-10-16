<?php

namespace App\Http\Controllers;

use App\Models\Department;
use App\Traits\IndonesianValidationMessages;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DepartmentController extends Controller
{
    use IndonesianValidationMessages;

    public function index(): View
    {
        $departments = Department::withCount(['tickets', 'users'])->orderBy('name')->get();

        return view('departments.index', compact('departments'));
    }

    public function create(): View
    {
        return view('departments.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:50', 'unique:departments,name'],
            'description' => ['nullable', 'string', 'max:255'],
        ], $this->validationMessages());

        Department::create($validated);

        return redirect()->route('departments.index')->with('success', 'Departemen berhasil ditambahkan.');
    }

    public function edit(Department $department): View
    {
        return view('departments.edit', compact('department'));
    }

    public function update(Request $request, Department $department): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:50', 'unique:departments,name,'.$department->id],
            'description' => ['nullable', 'string', 'max:255'],
        ], $this->validationMessages());

        $department->update($validated);

        return redirect()->route('departments.index')->with('success', 'Departemen berhasil diperbarui.');
    }

    public function destroy(Department $department): RedirectResponse
    {
        if ($department->tickets()->count() > 0 || $department->users()->count() > 0 || $department->categories()->count() > 0) {
            return back()->with('error', 'Departemen masih memiliki tiket, pengguna, atau kategori dan tidak dapat dihapus.');
        }

        $department->delete();

        return redirect()->route('departments.index')->with('success', 'Departemen berhasil dihapus.');
    }
}
