<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    public function index()
    {
        $users = User::orderBy('created_at', 'desc')->get();
        return view('admin.users.index', compact('users'));
    }

    public function create()
    {
        return view('admin.users.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'password' => 'required|string|min:8|confirmed',
            'role' => ['required', Rule::in(['admin', 'peasant'])],
        ]);

        $validated['password'] = Hash::make($validated['password']);
        $validated['active'] = $request->has('active') ? true : false;

        User::create($validated);

        return redirect()->route('admin.users.index')
            ->with('success', 'Usuario creado exitosamente.');
    }

    public function show(User $user)
    {
        return view('admin.users.show', compact('user'));
    }

    public function edit(User $user)
    {
        return view('admin.users.edit', compact('user'));
    }

    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'password' => 'nullable|string|min:8|confirmed',
            'role' => ['required', Rule::in(['admin', 'peasant'])],
        ]);

        if ($request->filled('password')) {
            $validated['password'] = Hash::make($validated['password']);
        } else {
            unset($validated['password']);
        }

        $validated['active'] = $request->has('active') ? true : false;

        $user->update($validated);

        return redirect()->route('admin.users.index')
            ->with('info', 'Usuario actualizado exitosamente.');
    }

    public function destroy(User $user)
    {
        // Don't delete, just deactivate
        $user->deactivate();

        return redirect()->route('admin.users.index')
            ->with('error', 'Usuario desactivado exitosamente.');
    }

    public function activate(User $user)
    {
        $user->activate();

        return redirect()->route('admin.users.index')
            ->with('success', 'Usuario activado exitosamente.');
    }
}

