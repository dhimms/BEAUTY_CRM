<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UserRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    public function index(Request $request)
    {   
        // query ini digunakan untuk mengambil data user
        $users = User::query()
            // method when digunakan untuk mengecek apakah request memiliki parameter search
            // jika request memiliki parameter search maka akan dilakukan query
            // fn() adalah method closure yang digunakan untuk mengecek apakah request memiliki parameter search
            // query ini akan mencari user berdasarkan nama, email, atau telepon
            // variabel $v adalah value dari parameter search
            // variabel $q adalah query builder yang isinya dari query awal User::query()
            // jika tidak ada parameter search maka user yang akan dikeluarkan semua kecuali user yang tidak aktif
            ->when($request->search, fn($q, $v) => $q->where(function ($q) use ($v) {
                $q->where('name', 'like', "%$v%")
                  ->orWhere('email', 'like', "%$v%")
                  ->orWhere('phone', 'like', "%$v%");
            })) 
            ->when($request->role, fn($q, $v) => $q->role($v))
            ->when($request->status !== null && $request->status !== '', fn($q) => $q->where('is_active', $request->status))
            // with('roles') ini digunakan untuk mengatasi n+1 query agar pengambilannya sekali jalan saja dan tidak perlu 2x query
            ->with('roles')
            ->latest()
            ->paginate(15)
            ->withQueryString();

        $roles = Role::all();

        return view('admin.users.index', compact('users', 'roles'));
    }

    public function create()
    {
        $roles = Role::all();
        return view('admin.users.create', compact('roles'));
    }

    public function store(UserRequest $request)
    {
        $data = $request->validated();

        if ($request->hasFile('avatar')) {
            $data['avatar'] = $request->file('avatar')->store('avatars', 'public');
        }
        // melakukan hassing password agar tidak tersimpan plain text
        $data['password'] = Hash::make($data['password']);
        // mengubah nilai is_active menjadi boolean
        // jika tidak ada isinya maka nilainya akan false secara default
        $data['is_active'] = $request->boolean('is_active', false);
        // unset ini digunakan untuk menghapus key 'role' dan 'password_confirmation' dari array $data
        // karena kedua key ini tidak ada di databased
        // jadi tidak perlu disimpan ke database
        unset($data['role'], $data['password_confirmation']);

        $user = User::create($data);
        // assignRole ini digunakan untuk memberikan role ke user
        // karena kita menggunakan spatie maka kita tinggal pakai fungsi assignRole
        $user->assignRole($request->role); 

        return redirect()->route('admin.users.index')
            ->with('success', "User {$user->name} berhasil dibuat.");
    }

    public function show(User $user)
    {
        $user->load('roles');
        $leadCount  = $user->assignedLeads()->count();
        $dealCount  = $user->assignedDeals()->count();
        $wonDeals   = $user->assignedDeals()->won()->count();

        return view('admin.users.show', compact('user', 'leadCount', 'dealCount', 'wonDeals'));
    }

    public function edit(User $user)
    {
        // Manager hanya bisa di-toggle aktif/nonaktif, tidak bisa diedit
        if ($user->hasRole('Manager') && $user->id !== auth()->id()) {
            return redirect()->route('admin.users.index')
                ->with('error', 'User dengan role Manager tidak dapat diedit. Anda hanya dapat mengaktifkan atau menonaktifkan akun ini.');
        }

        $roles = Role::all();
        $user->load('roles');
        return view('admin.users.edit', compact('user', 'roles'));
    }

    public function update(UserRequest $request, User $user)
    {
        // Manager hanya bisa di-toggle aktif/nonaktif, tidak bisa diedit
        if ($user->hasRole('Manager') && $user->id !== auth()->id()) {
            return redirect()->route('admin.users.index')
                ->with('error', 'User dengan role Manager tidak dapat diedit.');
        }

        $data = $request->validated();

        if ($request->hasFile('avatar')) {
            if ($user->avatar) {
                Storage::disk('public')->delete($user->avatar);
            }
            $data['avatar'] = $request->file('avatar')->store('avatars', 'public');
        }
        
        // cek apakah password kosong atau tidak
        // jika tidak maka dilakukan proses hassing pada passwordnya
        if (!empty($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        } else {
            unset($data['password']);
        }

        $data['is_active'] = $request->boolean('is_active', false);
        unset($data['role'], $data['password_confirmation']);

        $user->update($data);
        // syncRoles ini digunakan untuk update role user
        // karena kita menggunakan spatie maka kita tinggal pakai fungsi syncRoles
        $user->syncRoles([$request->role]);  

        return redirect()->route('admin.users.index')
            ->with('success', "User {$user->name} berhasil diperbarui.");
    }

    public function destroy(User $user)
    {   // mengecek apakah user yang akan dihapus adalah user yang sedang login  
        if ($user->id === auth()->id()) {
            return back()->with('error', 'Anda tidak bisa menghapus akun sendiri.');
        }

        // Manager tidak bisa dihapus, hanya bisa di-toggle
        if ($user->hasRole('Manager')) {
            return back()->with('error', 'User dengan role Manager tidak dapat dihapus. Anda hanya dapat mengaktifkan atau menonaktifkan akun ini.');
        }

        $user->delete();
        return redirect()->route('admin.users.index')
            ->with('success', "User {$user->name} berhasil dihapus.");
    }

    public function toggle(User $user)
    {
        if ($user->id === auth()->id()) {
            return response()->json(['error' => 'Tidak bisa menonaktifkan akun sendiri.'], 422);
        }
        $user->update(['is_active' => !$user->is_active]);
        return response()->json([
            'success'   => true,
            'is_active' => $user->is_active,
            'message'   => $user->is_active ? 'User diaktifkan.' : 'User dinonaktifkan.',
        ]);
    }
}

