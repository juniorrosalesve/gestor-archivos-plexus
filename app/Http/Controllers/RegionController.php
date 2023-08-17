<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\User;
use App\Models\Region;
use App\Models\Country;

class RegionController extends Controller
{
    public function index() {
        return view('regions.index', [
            'regions' => Region::orderBy('id', 'asc')->get()
        ]);
    }
    public function create() {
        $users  =   User::where('access', 'd')->get();
        if(sizeof($users) < 1)
            return '<script>alert("Debes tener al menos 1 director añadido.");location.href="'.route('create-user').'";</script>';

        return view('regions.create', [
            'users' => $users
        ]);
    }

    public function store(Request $r) {
        $countries  =   explode(",", $r->countries);
        $store  =   $r->except(['_token', 'countries']);
        $store  =   Region::create($store);
        for($i = 0; $i < sizeof($countries); $i++) 
            Country::create([
                'name' => $countries[$i],
                'regionId' => $store->id
            ]);
        return '<script>alert("Region creada correctamente!");location.href="'.route('regions').'";</script>';
    }
}
