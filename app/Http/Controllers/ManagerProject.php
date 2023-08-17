<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use App\Models\User;
use App\Models\Project;
use App\Models\Region;
use App\Models\Country;
use App\Models\Directory;

class ManagerProject extends Controller
{
    public function index() {
        $userId     =   Auth::user()->id;
        return view('project.manager.index', [
            'projects' => Project::where('managerId', $userId)->get()
        ]);
    }
    public function project($regionId, $countryId, $projectId) {
        return view('project.navigate', [
            'region' => Region::find($regionId),
            'country' => Country::find($countryId),
            'project' => Project::find($projectId),
            'dirs' => Directory::where('projectId', $projectId)->where('route', 0)->get()
        ]);
    }
}
