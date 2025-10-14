<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class LockScreenController extends Controller
{
    public function __construct()
    {
        if (!config('features.lock_screen', false)) {
            abort(404);
        }
    }
    
    public function show()
    {
        return view('lock-screen');
    }

    public function lock(Request $request)
    {
        session(['screen_locked' => true]);
        
        if ($request->expectsJson()) {
            return response()->json(['success' => true]);
        }
        
        return redirect()->route('lock.screen');
    }

    public function unlock(Request $request)
    {
        $request->validate([
            'pin' => 'required|digits:4'
        ]);

        $user = auth()->user();
        
        // Verify PIN
        if (!Hash::check($request->pin, $user->lock_pin)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid PIN'
            ], 401);
        }

        // Unlock screen and reset activity timestamp
        session([
            'screen_locked' => false,
            'last_activity' => now()->timestamp
        ]);

        return response()->json(['success' => true]);
    }

    public function checkStatus()
    {
        $lastActivity = session('last_activity', now()->timestamp);
        $idleTime = now()->timestamp - $lastActivity;
        $isLocked = session('screen_locked', false);

        // Auto-lock after 30 seconds
        if (!$isLocked && $idleTime > 30) {
            session(['screen_locked' => true]);
            $isLocked = true;
        }

        return response()->json([
            'locked' => $isLocked,
            'idle_time' => $idleTime
        ]);
    }

    public function setPin(Request $request)
    {
        $request->validate([
            'pin' => 'required|digits:4|confirmed'
        ]);

        $user = auth()->user();
        $user->lock_pin = Hash::make($request->pin);
        $user->save();

        return response()->json([
            'success' => true,
            'message' => 'PIN set successfully'
        ]);
    }

    public function updateActivity()
    {
        session(['last_activity' => now()->timestamp]);
        return response()->json(['success' => true]);
    }
}