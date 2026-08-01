<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Department;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DepartmentController extends Controller
{
    /**
     * List departments. Add/Edit are done through modals on this same page
     * (see resources/views/admin/departments/index.blade.php), so there
     * are no separate create()/edit() view methods.
     */
    public function index(): View
    {
        $departments = Department::withCount('complaints')->orderBy('name')->paginate(10);

        return view('admin.departments.index', compact('departments'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:departments,name'],
            'description' => ['nullable', 'string', 'max:1000'],
        ]);

        Department::create($validated);

        return redirect()->route('admin.departments.index')->with('success', 'Department added.');
    }

    public function update(Request $request, Department $department): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:departments,name,'.$department->id],
            'description' => ['nullable', 'string', 'max:1000'],
        ]);

        $department->update($validated);

        return redirect()->route('admin.departments.index')->with('success', 'Department updated.');
    }

    public function destroy(Department $department): RedirectResponse
    {
        $department->delete();

        return redirect()->route('admin.departments.index')->with('success', 'Department removed.');
    }
}
