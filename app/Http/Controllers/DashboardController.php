<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Project;
use App\Models\Directory;
use App\Models\Region;
use App\Models\Country;
use App\Models\Cronograma;

class DashboardController extends Controller
{
    public function index(Request $r) {
        if(\Auth::user()->access != 'a')
            return redirect()->route('manager-list');
        $region         =   null;
        $country        =   null;
        $chartProject   =   0;
        if($r->has('projectId'))
            $chartProject   =   $r->projectId;
        if($r->has('region') && $r->has('country')) {
            if($r->region > 0)
                $region     =   Region::find($r->region);
            if($r->country > 0)
                $country    =   Country::find($r->country);
        }
        if($region != null)
            $countries  =   Country::where('regionId', $region->id)->get();
        else
            $countries  =   Country::all();

        if($region == null && $country == null)
            $projects   =   Project::all();
        if($region != null && $country == null)
            $projects   =   Project::where('regionId', $region->id)->get();
        if($region != null && $country != null)
            $projects   =   Project::where('regionId', $region->id)->where('countryId', $country->id)->get();
        return view('dashboard', [
            'financiera_chart' => $this->generateChart("Admin. / Financiera", $r->region, $r->country, $r->chartProject),
            'operativa_chart' => $this->generateChart("Operativa", $r->region, $r->country, $r->chartProject),
            'estrategica_tactica_chart' => $this->generateChart("Estratégica / Táctica", $r->region, $r->country, $r->chartProject),
            'gestion_humana_chart' => $this->generateChart("Gestión Humana", $r->region, $r->country, $r->chartProject),

            'projects_opens' => $this->getProjectsOpen($r->region, $r-> country),
            'facturas_vencidas' => $this->getFechaFacturasVencidas(),
            'regions' => Region::all(),
            'countries' => $countries,
            'projects' => $projects,
            'region' => $region,
            'country' => $country,
            'jsCountries' => Country::all(),

            'chartProject' => ($chartProject > 0 ? true : false),
            'chartProjectData' => ($chartProject > 0 ? Project::find($chartProject) : false)
        ]);
    }

    public function viewProjectOpens($region, $country) {
        return view('projects', [
            'projects' => $this->getProjectsOpen($region, $country, true)
        ]);
    }

    private function generateChart($rootName, $region, $country, $projectId = 0) {
        if($projectId == 0) {
            if($region == 0 && $country == 0)
                $projects   =   Project::all();
            if($region != 0 && $country == 0)
                $projects   =   Project::where('regionId', $region)->get();
            if($region != 0 && $country != 0)
                $projects   =   Project::where('regionId', $region)->where('countryId', $country)->get();
        }
        else
            $projects   =   Project::where('id', $projectId)->get();
        $result     =   [];
        if(sizeof($projects) == 0) {
            $porcentaje     =   [];
            $porcentaje["total_ok"]     =   0;
            $porcentaje["total_bad"]    =   0;
            $porcentaje['total_sub_ok'][0]    =   0;
            $porcentaje['total_sub_bad'][0]    =   0;
            $porcentaje['keys']         =   ["no-result"];
            return $porcentaje;
        }
        for($i = 0; $i < sizeof($projects); $i++) {
            $project    =   $projects[$i];
            $root       =   Directory::where('projectId', $project->id)->where('name', $rootName)->first();
            $dirs       =   Directory::where('link', $root->id)->get();
            $result[$i]["name"]  =   $project->name;

            $startDate  =   new \DateTime($project->inicia);
            $endDate    =   new \DateTime();

            $diff   =   $endDate->diff($startDate);
            $weeks  =   (floor($diff->days / 7)+1);

            for($x = 0; $x < sizeof($dirs); $x++)
            {  
                $dir    =   $dirs[$x];
                
                if($i+1 == sizeof($projects))
                    $result["keys"][$x]   =   $dir->name;
                if($dir->type == 'directory' && $dir->week_from > 0)
                {
                    $files  =   Directory::where('link', $dir->id)->where('type', '!=', 'directory')->get();
                    // sí el directorio no esta vacío calculamos la fecha de entrega de cada archivo
                    // que se subio para este directorio para saber si esta todo al día o con retrasos. 
                    if(sizeof($files) > 0) {
                        foreach($files as $file) {
                            if($dir->week_to == 0) {
                                $startDate  =   new \DateTime($project->inicia);
                                $endDate    =   new \DateTime($file->created_at);

                                $diff   =   $endDate->diff($startDate);
                                $nWeek  =   floor($diff->days / 7)+1;

                                if($nWeek > $dir->week_from)
                                    $result[$i]["bad"][$x]     =   ['key' => $dir->name, 'value' => 1];
                                else
                                    $result[$i]["ok"][$x]     =   ['key' => $dir->name, 'value' => 1];
                            }
                            else {
                                for($z = $dir->week_from; $z <= $dir->week_to; $z++)
                                {
                                    if($z == $file->file_week) {
                                        $startDate  =   new \DateTime($project->inicia);
                                        $endDate    =   new \DateTime($file->created_at);
        
                                        $diff   =   $endDate->diff($startDate);
                                        $nWeek  =   floor($diff->days / 7)+1;
                                        
                                        // if(!array_key_exists($x, $result[$i]["ok"]))
                                        //     $result[$i]["ok"][$x]   =   0;

                                        if($nWeek > $z)
                                            $result[$i]["bad"][$x]    =   ['key' => $dir->name, 'value' => ($result[$i]["ok"][$x]+1)];
                                        else
                                            $result[$i]["ok"][$x]     =   ['key' => $dir->name, 'value' => ($result[$i]["ok"][$x]+1)];
                                        break;
                                    }
                                }
                            }
                        }
                    }
                    // en cambio calculamos semana o fecha de entrega de archivos para este directorio.
                    // para saber si aún están a tiempo para subir los archivos o se atrasaron.
                    else {
                        $startDate  =   new \DateTime($project->inicia);
                        $endDate    =   new \DateTime($dir->created_at);

                        $diff   =   $endDate->diff($startDate);
                        $nWeek  =   (floor($diff->days / 7)+1);
                        if($dir->week_from < $weeks)
                            $result[$i]["bad"][$x]      =   ['key' => $dir->name, 'value' => 1];
                        else
                            $result[$i]["ok"][$x]       =   ['key' => $dir->name, 'value' => 1];
                    }
                }
            }
        }
        /* Ordenamos un poco los datos para Chart.js */
        $replaceResult     =   [];
        $total  =   0;
        // dd($result);
        for($i = 0; $i < sizeof($result); $i++)
        {
            $keys       =   $result['keys'];
            if($i+1 == sizeof($result))
                break;
            $total     +=   ($i+1);
            $replaceResult[$i]['name']  =   $result[$i]['name']; // guardamos nombre del proyecto.
            for($x = 0; $x < sizeof($keys); $x++) {
                $valor  =   0;
                if(array_key_exists("ok", $result[$i])) {
                    foreach($result[$i]['ok'] as $key=>$item) {
                        if($item['key'] == $keys[$x]) {
                            $valor  =   $item['value'];
                            break;
                        }
                    }
                }
                $replaceResult[$i]['ok'][$x]  =   $valor;
            }
            for($x = 0; $x < sizeof($keys); $x++) {
                $valor  =   0;
                if(array_key_exists("bad", $result[$i])) {
                    foreach($result[$i]['bad'] as $key=>$item) {
                        if($item['key'] == $keys[$x]) {
                            $valor  =   $item['value'];
                            break;
                        }
                    }
                }
                $replaceResult[$i]['bad'][$x]  =   $valor;
            }
        }
        // dd($replaceResult);
        $OkConteo       =   0;
        $SubOkConteo    =   [];
        $BadConteo      =   0;
        $SubBadConteo   =   [];
        for($i = 0; $i < sizeof($replaceResult); $i++) {
            foreach($replaceResult[$i]['ok'] as $key=>$item) {
                $OkConteo   +=  $item;
                if(!array_key_exists($key, $SubOkConteo))
                    $SubOkConteo[$key]  =   $item;
                else
                    $SubOkConteo[$key]    +=  $item;
            }
            foreach($replaceResult[$i]['bad'] as $key=>$item) { 
                $BadConteo   +=  $item;
                if(!array_key_exists($key, $SubBadConteo))
                    $SubBadConteo[$key]     =   $item;
                else     
                    $SubBadConteo[$key]   +=  $item;
            }
        }
        $totalKeys      =   sizeof($result['keys']);
        $totalDirs      =   (sizeof($projects)*$totalKeys);
        $porcentaje     =   [];
        $porcentaje["total_ok"]     =   $OkConteo/$totalDirs*100;
        $porcentaje["total_bad"]    =   $BadConteo/$totalDirs*100;
        foreach($SubOkConteo as $key=>$item) 
            $porcentaje['total_sub_ok'][]    =   ($item/sizeof($projects))*100;
        foreach($SubBadConteo as $key=>$item) 
            $porcentaje['total_sub_bad'][]    =   ($item/sizeof($projects))*100;
        $porcentaje['keys']         =   $result['keys'];

        return $porcentaje;
    }

    private function getProjectsOpen($region, $country, $list = false) {
        if($region == 0 && $country == 0)
            $projects   =   Project::all();
        if($region != 0 && $country == 0)
            $projects   =   Project::where('regionId', $region)->get();
        if($region != 0 && $country != 0)
            $projects   =   Project::where('regionId', $region)->where('countryId', $country)->get();
        $actives    =   [];
        for($i = 0; $i < sizeof($projects); $i++) {
            $project    =   $projects[$i];
            $now        =   new \DateTime();
            $fTime      =   date('Y-m-d', strtotime($project->inicia."+ ".$project->semanas." week"));
            $finish     =   new \DateTime($fTime);
            if($now <= $finish) {
                $actives[$i]        =   $project;
                if($list)
                    $actives[$i]->bad   =   $this->checkProjectRetrasado($project->id);    
            }
        }
        return $actives;
    }
    public function checkProjectRetrasado($projectId) {
        $bad    =   false;
        $f  =   $this->generateChart("Admin. / Financiera", 0, 0, $projectId);
        $o  =   $this->generateChart("Operativa", 0, 0, $projectId);
        $e  =   $this->generateChart("Estratégica / Táctica", 0, 0, $projectId);
        $g  =   $this->generateChart("Gestión Humana", 0, 0, $projectId);
        if($f['total_bad'] > 0 || $o['total_bad'] > 0 || $e['total_bad'] > 0 || $g['total_bad'] > 0)
            $bad = true;
        return $bad;
    }

    private function getFechaFacturasVencidas() {
        $cronogramas    =   Cronograma::all();
        $facturas       =   [];
        foreach($cronogramas as $item) {
            if($item->fecha_pagoreal > $item->fecha_vencimiento)
                $facturas[]     =   $item;
        }
        return $facturas;
    }
}
