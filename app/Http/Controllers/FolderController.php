<?php

namespace App\Http\Controllers;

use App\Models\Folder;
use App\Models\StorageSpace;
use App\Models\User;
use App\Services\FileService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FolderController extends Controller
{
    public function __construct(
        protected FileService $fileService
    ) {}

    public function lock(Folder $folder): RedirectResponse
    {
        $this->fileService->lockFolder($folder, Auth::user());
        return back()->with('success', 'Folder locked.');
    }

    public function unlock(Folder $folder): RedirectResponse
    {
        $this->fileService->unlockFolder($folder, Auth::user());
        return back()->with('success', 'Folder unlocked.');
    }

    public function share(Request $request, Folder $folder): RedirectResponse
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'permission' => 'required|in:view,edit',
        ]);
        $shareWith = User::findOrFail($request->user_id);
        $this->fileService->shareFolder($folder, $shareWith, $request->permission, Auth::user());
        return back()->with('success', 'Folder shared with contents.');
    }

    public function createOrGetShareLink(Request $request, Folder $folder): \Illuminate\Http\JsonResponse
    {
        $request->validate(['permission' => 'nullable|in:view,edit']);
        $permission = $request->get('permission', 'view');
        $link = $this->fileService->createOrGetFolderShareLink($folder, $permission, Auth::user());
        $url = route('shared.access', ['token' => $link->token], true);
        return response()->json(['url' => $url, 'token' => $link->token]);
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'space_id' => 'required|exists:storage_spaces,id',
            'parent_id' => 'nullable|exists:folders,id',
            'name' => 'required|string|max:255',
        ]);

        $space = StorageSpace::findOrFail($request->space_id);
        $parent = $request->parent_id ? Folder::findOrFail($request->parent_id) : null;
        $this->fileService->createFolder($space, $parent, $request->name, Auth::user());

        return back()->with('success', 'Folder created.');
    }

    public function update(Request $request, Folder $folder): RedirectResponse
    {
        $request->validate([
            'action' => 'required|in:rename,move',
            'name' => 'required_if:action,rename|nullable|string|max:255',
            'parent_id' => 'required_if:action,move|nullable|exists:folders,id',
        ]);

        match ($request->action) {
            'rename' => $this->fileService->renameFolder($folder, $request->name ?? $folder->name, Auth::user()),
            'move' => $this->fileService->moveFolder($folder, $request->parent_id ? Folder::find($request->parent_id) : null, Auth::user()),
        };

        return back()->with('success', 'Folder updated.');
    }

    public function destroy(Folder $folder): RedirectResponse
    {
        $this->fileService->deleteFolder($folder, Auth::user());
        return back()->with('success', 'Folder deleted.');
    }
}
