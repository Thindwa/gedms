<?php

namespace App\Http\Controllers;

use App\Models\Memo;
use App\Models\User;
use App\Services\MemoService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MemoController extends Controller
{
    public function __construct(protected MemoService $memoService) {}

    public function index(Request $request): View
    {
        $user = auth()->user();
        $direction = $request->get('direction', 'upward');

        $memos = Memo::where('from_user_id', $user->id)
            ->when($direction !== 'all', fn ($q) => $q->where('direction', $direction))
            ->with(['fromUser', 'toUser', 'ministry', 'department'])
            ->latest()
            ->get();

        return view('memos.index', [
            'memos' => $memos,
            'direction' => $direction,
            'users' => User::where('ministry_id', $user->ministry_id)->orderBy('name')->get(['id', 'name', 'email']),
        ]);
    }

    public function create(): View
    {
        $user = auth()->user();
        return view('memos.create', [
            'users' => User::where('ministry_id', $user->ministry_id)->orderBy('name')->get(['id', 'name', 'email']),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'direction' => 'required|in:upward,downward,personal',
            'title' => 'required|string|max:255',
            'body' => 'nullable|string',
            'to_user_id' => 'required_if:direction,upward,downward|nullable|exists:users,id',
            'requires_approval' => 'boolean',
        ]);

        $validated['requires_approval'] = $request->boolean('requires_approval');

        $memo = $this->memoService->create($validated, $request->user());
        return redirect()->route('memos.index')->with('success', 'Memo created.');
    }

    public function show(Memo $memo): View
    {
        $this->authorize('view', $memo);
        $memo->load(['fromUser', 'toUser', 'ministry', 'department', 'file']);
        return view('memos.show', ['memo' => $memo]);
    }

    public function send(Memo $memo)
    {
        $this->memoService->send($memo, auth()->user());
        return redirect()->route('memos.show', $memo)->with('success', 'Memo sent.');
    }

    public function acknowledge(Memo $memo)
    {
        $this->memoService->acknowledge($memo, auth()->user());
        return redirect()->route('memos.show', $memo)->with('success', 'Memo acknowledged.');
    }
}
