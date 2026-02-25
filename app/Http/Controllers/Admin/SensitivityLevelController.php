<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SensitivityLevel;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SensitivityLevelController extends Controller
{
    public function __construct()
    {
        $this->middleware('can:manage-sensitivity-levels');
    }

    public function index(): View
    {
        $levels = SensitivityLevel::orderBy('sort_order')->get();

        return view('admin.sensitivity-levels.index', compact('levels'));
    }

    public function create(): View
    {
        return view('admin.sensitivity-levels.form', ['level' => new SensitivityLevel()]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'code' => 'required|string|max:50|unique:sensitivity_levels,code',
            'sort_order' => 'required|integer|min:0',
        ]);

        SensitivityLevel::create($validated);

        return redirect()->route('admin.sensitivity-levels.index')->with('success', 'Sensitivity level created.');
    }

    public function edit(SensitivityLevel $sensitivityLevel): View
    {
        return view('admin.sensitivity-levels.form', ['level' => $sensitivityLevel]);
    }

    public function update(Request $request, SensitivityLevel $sensitivityLevel): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'code' => 'required|string|max:50|unique:sensitivity_levels,code,' . $sensitivityLevel->id,
            'sort_order' => 'required|integer|min:0',
        ]);

        $sensitivityLevel->update($validated);

        return redirect()->route('admin.sensitivity-levels.index')->with('success', 'Sensitivity level updated.');
    }

    public function destroy(SensitivityLevel $sensitivityLevel): RedirectResponse
    {
        if ($sensitivityLevel->documents()->exists()) {
            return back()->with('error', 'Cannot delete: sensitivity level is in use.');
        }
        $sensitivityLevel->delete();
        return redirect()->route('admin.sensitivity-levels.index')->with('success', 'Sensitivity level deleted.');
    }
}
