<?php


namespace App\Http\Controllers;

use App\Models\Platform;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PlatformController extends Controller
{
    // 1. List all available platforms
    public function index()
    {
        return Platform::all();
    }

    // 2. Toggle active platforms for the user (attach/detach)
    public function toggle(Request $request)
    {
        $request->validate([
            'platform_id' => 'required|exists:platforms,id',
        ]);

        $user = Auth::user();

        // Simulate toggling
        if ($user->platforms()->where('platform_id', $request->platform_id)->exists()) {
            $user->platforms()->detach($request->platform_id);
            $status = 'deactivated';
        } else {
            $user->platforms()->attach($request->platform_id);
            $status = 'activated';
        }

        return response()->json([
            'message' => "Platform {$status} successfully.",
            'platform_id' => $request->platform_id
        ]);
    }
}