<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Country;

class CountryController extends Controller
{
    public function index() {
        return view('country.index', [
            'countries' => Country::orderBy('name', 'asc')->get()
        ]);
    }
}
