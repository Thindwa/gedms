<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Ministry;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MinistryController extends Controller
{
    public function __construct()
    {
        $this->middleware('can:manage-ministry');
    }

    public function index(): View
    {
        $ministries = Ministry::withCount(['departments', 'users'])->orderBy('name')->get();
        return view('admin.ministries.index', compact('ministries'));
    }

    public function create(): View
    {
        return view('admin.ministries.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'nullable|string|max:50',
            'description' => 'nullable|string|max:500',
        ]);
        Ministry::create([
            'name' => $request->name,
            'code' => $request->code,
            'description' => $request->description,
            'is_active' => true,
        ]);
        return redirect()->route('admin.ministries.index')->with('success', 'Ministry created successfully.');
    }
}
