<?php

namespace App\Http\Controllers;

use App\Models\Accreditor;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

class AccreditorAccountController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:accreditors',
            'expires_at' => 'required|date',
            'college_id' => 'required|exists:colleges,id',
            'program_id' => 'required|exists:programs,id',
            'level' => 'nullable|string',
        ]);

        $password = Str::random(10);

        $accreditor = Accreditor::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($password),
            'expires_at' => $request->expires_at,
            'college_id' => $request->college_id,
            'program_id' => $request->program_id,
            'level' => $request->level,
            'created_by' => auth()->id(),
        ]);

        // In a real app we'd dispatch an email here.
        // Mail::to($accreditor->email)->send(new AccreditorCredentialsMail($accreditor, $password));

        return response()->json([
            'message' => 'Accreditor created successfully',
            'password' => $password, // Return mostly for dev/admin visibility
            'accreditor' => $accreditor
        ], 201);
    }
}
