<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Department;
use App\Traits\IndonesianValidationMessages;
use Illuminate\Database\QueryException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class CategoryController extends Controller
{
    use IndonesianValidationMessages;

    public function index(Request $request): View
    {
        $user = $request->user();

        $query = Category::with('department')->withCount('tickets')->orderBy('name');
        if ($user->isAgent()) {
            // Admin departemen hanya melihat kategori departemennya
            $query->where('department_id', $user->department_id);
        }

        $categories = $query->get();

        return view('categories.index', compact('categories'));
    }

    public function create(Request $request): View
    {
        $departments = $request->user()->isAdmin()
            ? Department::orderBy('name')->get()
            : null;

        return view('categories.create', compact('departments'));
    }

    public function store(Request $request): RedirectResponse
    {
        $user = $request->user();
        $departmentId = $user->isAdmin() ? $request->input('department_id') : $user->department_id;

        $rules = [
            'name' => [
                'required', 'string', 'max:50',
                Rule::unique('categories', 'name')->where(
                    fn ($q) => $q->where('department_id', $departmentId)
                ),
            ],
            'description' => ['nullable', 'string', 'max:255'],
        ];

        if ($user->isAdmin()) {
            $rules['department_id'] = ['required', 'exists:departments,id'];
        }

        $validated = $request->validate($rules, $this->validationMessages());
        $validated['department_id'] = $departmentId;

        Category::create($validated);

        return redirect()->route('categories.index')->with('success', 'Kategori berhasil ditambahkan.');
    }

    public function edit(Request $request, Category $category): View
    {
        $this->authorizeManage($request->user(), $category);

        $departments = $request->user()->isAdmin()
            ? Department::orderBy('name')->get()
            : null;

        return view('categories.edit', compact('category', 'departments'));
    }

    public function update(Request $request, Category $category): RedirectResponse
    {
        $user = $request->user();
        $this->authorizeManage($user, $category);

        $departmentId = $user->isAdmin() ? $request->input('department_id') : $category->department_id;

        $rules = [
            'name' => [
                'required', 'string', 'max:50',
                Rule::unique('categories', 'name')
                    ->where(fn ($q) => $q->where('department_id', $departmentId))
                    ->ignore($category->id),
            ],
            'description' => ['nullable', 'string', 'max:255'],
        ];

        if ($user->isAdmin()) {
            $rules['department_id'] = ['required', 'exists:departments,id'];
        }

        $validated = $request->validate($rules, $this->validationMessages());
        $validated['department_id'] = $departmentId;

        $category->update($validated);

        return redirect()->route('categories.index')->with('success', 'Kategori berhasil diperbarui.');
    }

    public function destroy(Request $request, Category $category): RedirectResponse
    {
        $this->authorizeManage($request->user(), $category);

        try {
            $category->delete();
        } catch (QueryException) {
            return back()->with('error', 'Kategori masih digunakan oleh tiket dan tidak dapat dihapus.');
        }

        return redirect()->route('categories.index')->with('success', 'Kategori berhasil dihapus.');
    }

    private function authorizeManage($user, Category $category): void
    {
        $allowed = $user->isAdmin()
            || ($user->isAgent() && $user->department_id !== null && $user->department_id === $category->department_id);

        if (! $allowed) {
            abort(403, 'Kategori ini di luar departemen Anda.');
        }
    }
}
