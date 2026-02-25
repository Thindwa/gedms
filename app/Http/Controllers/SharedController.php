<?php

namespace App\Http\Controllers;

use App\Models\File;
use App\Models\FileShare;
use App\Models\Folder;
use App\Models\FolderShare;
use App\Models\ShareLink;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SharedController extends Controller
{
    /**
     * Handle visit via share link. Adds the user to shares and redirects to the file/folder.
     */
    public function access(string $token): RedirectResponse
    {
        $link = ShareLink::where('token', $token)->first();
        if (! $link) {
            abort(404, 'Share link not found or expired.');
        }

        if (! Auth::check()) {
            session(['url.intended' => route('shared.access', ['token' => $token])]);
            return redirect()->route('login');
        }

        $user = Auth::user();

        if ($link->shareable_type === File::class) {
            $file = File::find($link->shareable_id);
            if (! $file) {
                abort(404, 'File not found.');
            }
            FileShare::firstOrCreate(
                ['file_id' => $file->id, 'shared_with_user_id' => $user->id],
                ['shared_by_user_id' => $link->created_by_user_id, 'permission' => $link->permission]
            );
            return redirect()->route('files.index', ['view' => 'shared']);
        }

        if ($link->shareable_type === Folder::class) {
            $folder = Folder::find($link->shareable_id);
            if (! $folder) {
                abort(404, 'Folder not found.');
            }
            FolderShare::firstOrCreate(
                ['folder_id' => $folder->id, 'shared_with_user_id' => $user->id],
                ['shared_by_user_id' => $link->created_by_user_id, 'permission' => $link->permission]
            );
            return redirect()->route('files.index', ['space' => $folder->storage_space_id, 'folder' => $folder->id]);
        }

        abort(404);
    }
}
