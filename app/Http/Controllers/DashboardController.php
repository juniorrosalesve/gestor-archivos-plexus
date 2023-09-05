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
        $filterByDate   =   null;
        $filterById     =   0;
        if($r->has('projectId'))
            $filterById     =   $r->projectId;
        if($r->has('filterDate'))
            $filterByDate   =   $r->filterDate;
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

        if($filterById == 0) {
            if($region == null && $country == null) {
                if($filterByDate != null)
                    $projects   =   Project::whereYear('created_at', $filterByDate)->get();
                else
                    $projects   =   Project::all();
            }
            if($region != null && $country == null) {
                if($filterByDate != null)
                    $projects   =   Project::where('regionId', $region->id)->whereYear('created_at', $filterByDate)->get();
                else
                    $projects   =   Project::where('regionId', $region->id)->get();
            }
            if($region != null && $country != null) {
                if($filterByDate != null)
                    $projects   =   Project::where('regionId', $region->id)->where('countryId', $country->id)->whereYear('created_at', $filterByDate)->get();
                else
                    $projects   =   Project::where('regionId', $region->id)->where('countryId', $country->id)->get();
            }
        }
        else
            $projects   =   Project::where('id', $filterById)->get();
        
        return view('dashboard', [
            'financiera_chart' => $this->generateChart("Admin. / Financiera", $projects),
            'operativa_chart' => $this->generateChart("Operativa", $projects),
            'estrategica_tactica_chart' => $this->generateChart("Estratégica / Táctica", $projects),
            'gestion_humana_chart' => $this->generateChart("Gestión Humana", $projects),

            'projects_opens' => $this->getProjectsOpen($projects),
            'facturas_vencidas' => $this->getFechaFacturasVencidas($r->region, $r->country, $r->chartProject),
            'facturas_mora' => $this->getFechaFacturasMora($r->region, $r->country, $r->chartProject),
            'years' => $this->getYearsDashboard(),
            'filterByDate' => $filterByDate,
            'regions' => Region::all(),
            'countries' => $countries,
            'projects' => $projects,
            'region' => $region,
            'country' => $country,
            'jsCountries' => Country::all(),

            'chartProject' => ($filterById > 0 ? true : false),
            'chartProjectData' => ($filterById > 0 ? Project::find($filterById) : false)
        ]);
    }

    public function viewProjectOpens($region, $country) {
        return view('projects', [
            'projects' => $this->getProjectsOpen($region, $country, true)
        ]);
    }
    public function viewAllProjects() {
        return view('allprojects', [
            'projects' => Project::all()
        ]);
    }

    private function generateChart($rootName, $projects) {
        $result     =   [];
        if(sizeof($projects) == 0) {
            $porcentaje     =   [];
            $porcentaje["total_ok"]     =   0;
            $porcentaje["total_countOk"]     =   0;
            $porcentaje["total_outTime"]    =   0;
            $porcentaje["total_countOutTime"]   =   0;
            $porcentaje["total_bad"]    =   0;
            $porcentaje["total_countBad"]   =   0;
            $porcentaje['total_sub_ok'][0]    =   0;
            $porcentaje['total_sub_outTime'][0] = 0;
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
                                    $result[$i]["outTime"][$x]     =   ['key' => $dir->name, 'value' => 1];
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
                                    
                                        if($nWeek > $z)
                                            $result[$i]["outTime"][$x]    =   ['key' => $dir->name, 'to' => $dir->week_to, 'value' => $z];
                                        else 
                                            $result[$i]["ok"][$x]     =   ['key' => $dir->name, 'to' => $dir->week_to, 'value' => $z];
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
                            if($item['value'] > 1)
                                $valor  =   1;
                            else
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
                            if($item['value'] > 1)
                                $valor  =   1;
                            else
                                $valor  =   $item['value'];
                            break;
                        }
                    }
                }
                $replaceResult[$i]['bad'][$x]  =   $valor;
            }
            for($x = 0; $x < sizeof($keys); $x++) {
                $valor  =   0;
                if(array_key_exists("outTime", $result[$i])) {
                    foreach($result[$i]['outTime'] as $key=>$item) {
                        if($item['key'] == $keys[$x]) {
                            if($item['value'] > 1)
                                $valor  =   1;
                            else
                                $valor  =   $item['value'];
                            break;
                        }
                    }
                }
                $replaceResult[$i]['outTime'][$x]  =   $valor;
            }
        }
        $OkConteo       =   0;
        $SubOkConteo    =   [];
        $BadConteo      =   0;
        $SubBadConteo   =   [];
        $outTimeConteo  =   0;
        $outTime        =   [];
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
            foreach($replaceResult[$i]['outTime'] as $key=>$item) { 
                $outTimeConteo   +=  $item;
                if(!array_key_exists($key, $outTime))
                    $outTime[$key]     =   $item;
                else     
                    $outTime[$key]   +=  $item;
            }
        }
        // dd($SubOkConteo);
        $totalKeys      =   sizeof($result['keys']);
        $totalDirs      =   (sizeof($projects)*$totalKeys);
        $porcentaje     =   [];
        $porcentaje["total_ok"]     =   $OkConteo/$totalDirs*100;
        $porcentaje["total_countOk"]     =   $OkConteo;
        $porcentaje["total_outTime"]    =   $outTimeConteo/$totalDirs*100;
        $porcentaje["total_countOutTime"]   =   $outTimeConteo;
        $porcentaje["total_bad"]    =   $BadConteo/$totalDirs*100;
        $porcentaje["total_countBad"]   =   $BadConteo;
        foreach($SubOkConteo as $key=>$item) 
            $porcentaje['total_sub_ok'][]    =   ($item/sizeof($projects))*100;
        foreach($outTime as $key=>$item) 
            $porcentaje['total_sub_outTime'][]    =   ($item/sizeof($projects))*100;
        foreach($SubBadConteo as $key=>$item) 
            $porcentaje['total_sub_bad'][]    =   ($item/sizeof($projects))*100;
        $porcentaje['keys']         =   $result['keys'];

        return $porcentaje;
    }

    private function getProjectsOpen($projects, $list = false) {
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

    private function getFechaFacturasVencidas($region, $country, $projectId = 0) {
        if($projectId == 0) 
            $cronogramas    =   Cronograma::all();
        else
            $cronogramas    =   Cronograma::where('projectId', $projectId)->get();
        $facturas       =   [];
        foreach($cronogramas as $item) {
            if($item->fecha_pagoreal > $item->fecha_vencimiento) {
                if($region != 0 && $country == 0) {
                    if($item->project->regionId == $region)
                        $facturas[]     =   $item;
                }
                else {
                    if($region != 0 && $country != 0) {
                        if($item->project->regionId == $region && $item->project->countryId == $country)
                            $facturas[]     =   $item;
                    }
                    else 
                        $facturas[]     =   $item;
                }
            }
        }
        return $facturas;
    }
    private function getFechaFacturasMora($region, $country, $projectId = 0) {
        if($projectId == 0) 
            $cronogramas    =   Cronograma::all();
        else
            $cronogramas    =   Cronograma::where('projectId', $projectId)->get();
        $facturas       =   [];
        foreach($cronogramas as $item) {
            $now    =   date('Y-m-d');
            if($now > $item->fecha_vencimiento && $item->fecha_pagoreal == null) {
                if($region != 0 && $country == 0) {
                    if($item->project->regionId == $region)
                        $facturas[]     =   $item;
                }
                else {
                    if($region != 0 && $country != 0) {
                        if($item->project->regionId == $region && $item->project->countryId == $country)
                            $facturas[]     =   $item;
                    }
                    else 
                        $facturas[]     =   $item;
                }
            }
        }
        return $facturas;
    }

    private function getYearsDashboard() {
        $projects   =   Project::orderBy('created_at', 'asc')->get();
        $years  =   [];
        foreach($projects as $project) {
            $date   =   date('Y', strtotime($project->created_at));
            if(!in_array($date, $years))
                $years[]    =   $date;
        }
        return $years;
    }
}
