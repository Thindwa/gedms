<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Unit;
use Illuminate\View\View;

class UnitController extends Controller
{
    public function __construct()
    {
        $this->middleware('can:manage-department');
    }

    public function index(): View
    {
        $units = Unit::with(['department.ministry'])->orderBy('name')->get();
        return view('admin.units.index', compact('units'));
    }
}
