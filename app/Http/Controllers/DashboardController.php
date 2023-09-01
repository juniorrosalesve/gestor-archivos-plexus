<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Project;
use App\Models\Directory;
use App\Models\Region;
use App\Models\Country;

class DashboardController extends Controller
{
    public function index() {
        return view('dashboard', [
            'financiera_chart' => $this->generateChart("Admin. / Financiera"),
            'operativa_chart' => $this->generateChart("Operativa"),
            'estrategica_tactica_chart' => $this->generateChart("Estratégica / Táctica"),
            'gestion_humana_chart' => $this->generateChart("Gestión Humana"),

            'projects_opens' => $this->getProjectsOpen(),
            'regions' => Region::all(),
            'countries' => Country::all(),
            'projects' => Project::all()
        ]);
    }

    public function viewProjectOpens() {
        return view('projects', [
            'projects' => $this->getProjectsOpen()
        ]);
    }

    private function generateChart($rootName) {
        $projects   =   Project::all();
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
                                        
                                        if(!array_key_exists($x, $result[$i]["ok"]))
                                            $result[$i]["ok"][$x]   =   0;

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
                        if($weeks > $nWeek && $dir->week_from < $weeks)
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

    private function getProjectsOpen() {
        $projects   =   Project::all();
        $actives    =   [];
        foreach($projects as $project) {
            $now        =   new \DateTime();
            $fTime      =   date('Y-m-d', strtotime($project->inicia."+ ".$project->semanas." week"));
            $finish     =   new \DateTime($fTime);
            if($now <= $finish)
                $actives[]  =   $project;
        }
        return $actives;
    }
}
