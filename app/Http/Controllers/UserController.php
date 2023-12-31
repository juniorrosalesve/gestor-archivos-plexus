<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\User;

class UserController extends Controller
{
    public $ranks   =   [
        'a' => 'Auditores',
        'd' => 'Directores',
        'g' => 'Gerentes',
        'c' => 'Consultores'
    ];

    public function index(Request $r) {
        if($r->has('access')) 
            $users  =   User::where('access', $r->access)->get();
        else
            $users  =   User::where('access', 'a')->get(); 
        return view('users.index', [
            'users' => $users,
            'actualAccess' => ($r->has('access') ? $r->access : 'a'),
            'ranks' => $this->ranks
        ]);
    }
    public function create() {
        return view('users.create', [
            'ranks' => $this->ranks
        ]);
    }
    public function edit($userId) {
        return view('users.edit', [
            'user' => User::find($userId),
            'ranks' => $this->ranks
        ]);
    }

    public function store(Request $r) {
        User::create($r->except('_token'));
        return '<script>alert("Añadido correctamente");location.href="'.url('/users?access='.$r->access).'";</script>';
    }
    public function update(Request $r) {
        User::where('id', $r->userId)->update($r->except(['_token', 'userId']));
        return '<script>alert("Guardado correctamente");location.href="'.route('edit-user', ['userId' => $r->userId]).'";</script>';
    }
}
