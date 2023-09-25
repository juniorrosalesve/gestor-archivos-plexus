<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;

use App\Models\User;
use App\Models\Project;
use App\Models\Region;
use App\Models\Country;
use App\Models\Directory;
use App\Models\Cronograma;
use App\Models\SemanaLibre;

class ProyectoController extends Controller
{
    public function index() {
        return view('project.index', [
            'regions' => Region::orderBy('name', 'asc')->get(),
            'countries' => Country::orderBy('name', 'asc')->get()
        ]);
    }
    public function create() {
        return view('project.create', [
            'regions' => Region::orderBy('name', 'asc')->get(),
            'countries' => Country::orderBy('name', 'asc')->get(),
            'gerentes' => User::where('access', 'g')->orderBy('name', 'asc')->get(),
            'financiera' => $this->financiera,
            'operativa' => $this->operativa,
            'estrategica_tactica' => $this->estrategica_tactica,
            'gestion_humana' => $this->gestion_humana
        ]);
    }
    public function edit($projectId) {
        $project    =   Project::find($projectId);
        $root       =   Directory::where('projectId', $projectId)->where('route', 0)->get();
        $dirs       =   Directory::where('projectId', $projectId)->where('route', 1)->get();
        return view('project.edit', [
            'regions' => Region::orderBy('name', 'asc')->get(),
            'countries' => Country::orderBy('name', 'asc')->get(),
            'gerentes' => User::where('access', 'g')->orderBy('name', 'asc')->get(),
            'project' => $project,
            'root' => $root,
            'dirs' => $dirs,
            'weeks_free' => SemanaLibre::where('projectId', $projectId)->get() 
        ]);
    }
    public function projects($regionId, $countryId) {
        $projects   =   Project::where('regionId', $regionId)->where('countryId', $countryId)->get();
        return view('project.project', [
            'region' => Region::find($regionId),
            'country' => Country::find($countryId),
            'projects' => $projects
        ]);
    }
    public function project($regionId, $countryId, $projectId) {
        $cronogramas    =   Cronograma::where('projectId', $projectId)->get();
        $dirs           =   Directory::where('projectId', $projectId)->where('route', 0)->get();
        return view('project.navigate', [
            'region' => Region::find($regionId),
            'country' => Country::find($countryId),
            'project' => Project::find($projectId),
            'dirs' => $dirs,
            'cronogramas' => $cronogramas,
            'weeknd' => $this->weeknd($projectId)
        ]);
    }

    public function store(Request $r) {
        // dd($r->all());

        $store  =   Project::create($r->except([
            '_token',
            'financiera',
            'financiera_week',
            'operativa',
            'operativa_week',
            'estrategica_tactica',
            'estrategica_tactica_week',
            'gestion_humana',
            'gestion_humana_week',
            'week_free'
        ]));

        for($i = 0; $i < sizeof($r->week_free); $i++) {
            if($r->week_free[$i] != null)
                SemanaLibre::create([
                    'projectId' => $store->id,
                    'week_free' => $r->week_free[$i]
                ]);
        }

        $this->create_default_directory(
            'Admin. / Financiera', 
            $r->financiera, 
            $r->financiera_week, 
            ($r->has('financiera_no_aplica') ? $r->financiera_no_aplica : []), 
            $store->id
        );
        $this->create_default_directory(
            'Operativa', 
            $r->operativa, 
            $r->operativa_week, 
            ($r->has('operativa_no_aplica') ? $r->operativa_no_aplica : []), 
            $store->id
        );
        $this->create_default_directory(
            'Estratégica / Táctica', 
            $r->estrategica_tactica, 
            $r->estrategica_tactica_week, 
            ($r->has('estrategica_tactica_no_aplica') ? $r->estrategica_tactica_no_aplica : []), 
            $store->id
        );
        $this->create_default_directory(
            'Gestión Humana', 
            $r->gestion_humana, 
            $r->gestion_humana_week, 
            ($r->has('gestion_humana_no_aplica') ? $r->gestion_humana_no_aplica : []), 
            $store->id
        );

        return "<script>alert('Proyecto creado correctamente!');location.href='".route('projects', [
            'regionId' => $store->regionId,
            'countryId' => $store->countryId
        ])."'</script>";
    }
    public function update(Request $r) {
        $update     =   $r->except([
            '_token',
            'dir_update_value',
            'dir_update_id',
            'projectId',
            'week_free'
        ]);
        SemanaLibre::where('projectId', $r->projectId)->delete();
        foreach($r->week_free as $item)
        {
            if($item != null)
                SemanaLibre::create([
                    'projectId' => $r->projectId,
                    'week_free' => $item
                ]);
        }
        $weeknd     =   $this->weeknd($r->projectId);
        for($i = 0; $i < sizeof($r->dir_update_id); $i++) {
            $weeks  =   explode(",", $r->dir_update_value[$i]);
            if(sizeof($weeks) > 1) {
                if(sizeof($weeknd['freeList']) > 0)
                {
                    for($x = 0; $x < sizeof($weeks); $x++)
                    {
                        if($x > 0)
                        {
                            if($weeks[($x-1)] == $weeks[$x])
                                $weeks[$x]  +=  1;
                        }
                        foreach($weeknd['freeList'] as $item)
                        {
                            if($item['week'] == $weeks[$x])
                            {
                                $weeks[$x]  +=  1;
                                break;
                            }
                        }
                    }
                }
                $insertWeeks    =   '';
                for($x = 0; $x < sizeof($weeks); $x++) {
                    if(($x+1) == sizeof($weeks))
                        $insertWeeks    .=  $weeks[$x];
                    else
                        $insertWeeks    .=  $weeks[$x].',';
                }
                Directory::where('id', $r->dir_update_id[$i])->update([
                    'week_from' => 0,
                    'week_to' => 0,
                    'week_selected' => $insertWeeks
                ]);
            }
            else {
                $weeks  =   explode("-", $r->dir_update_value[$i]);
                if(sizeof($weeks) > 1) {
                    if(sizeof($weeknd['freeList']) > 0)
                    {
                        foreach($weeknd['freeList'] as $item)
                        {
                            if($item['week'] == $weeks[0]) {
                                $weeks[0]   +=  1;
                                $weeks[1]   +=  1;
                            }
                            else {
                                if($item['week'] > $weeks[0] && $item['week'] <= $weeks[1])
                                    $weeks[1] += 1;
                                else {
                                    if(($weeks[1]+1) == $weeknd['totalWeek'])
                                        $weeks[1]   += 1;
                                }
                            }
                        }
                    }
                    Directory::where('id', $r->dir_update_id[$i])->update(['week_from' => $weeks[0], 'week_to' => $weeks[1]]);
                }
                else {
                    if(sizeof($weeknd['freeList']) > 0) {
                        foreach($weeknd['freeList'] as $item)
                        {
                            if($item['week'] == $weeks[0])
                                $weeks[0]   +=  1;
                            else {
                                if($weeks[0]+1 == $weeknd['totalWeek'])
                                    $weeks[0] += 1;
                            }
                        }
                    }
                    Directory::where('id', $r->dir_update_id[$i])->update(['week_from' => $weeks[0], 'week_to' => 0]);
                }
            }
        }
        Project::where('id', $r->projectId)->update($update);
        return "<script>alert('Guardado correctamente!');location.href='".route('edit-project', [
            'projectId' => $r->projectId,
        ])."'</script>";
    }
    public function update_cronograma(Request $r) {
        $project    =   Project::find($r->projectId);
        Cronograma::where('projectId', $r->projectId)->delete();
        if($r->numero_factura != null) {
            for($i = 0; $i < sizeof($r->numero_factura); $i++)
                Cronograma::create([
                    'projectId' => $r->projectId,
                    'n_factura' => $r->numero_factura[$i],
                    'fecha_factura' => $r->fecha_factura[$i],
                    'fecha_vencimiento' => $r->fecha_vencimiento[$i],
                    'fecha_pagoreal' => $r->fecha_pagoreal[$i],
                    'moneda' => $r->moneda[$i],
                    'monto' => $r->monto[$i]
                ]);
        }
        return '<script>alert("Cronograma guardado correctamente");location.href="'.route('project', [
            'regionId' => $project->regionId,
            'countryId' => $project->countryId,
            'projectId' => $project->id
        ]).'"</script>';
    }
    public function deleteFile($fileId) {
        $file   =   Directory::find($fileId);
        $user   =   \Auth::user();
        if($user->access == 'a') {
            Storage::delete('public/plexus/'.$file->file_path);
            Directory::where('id', $fileId)->delete();
        } else 
            Directory::where('id', $fileId)->update([
                'deleted_by' => $user->name
            ]);
        return "<script>alert('Eliminado correctamente!');location.href='".route('project', [
            'regionId' => $file->project->regionId,
            'countryId' => $file->project->countryId,
            'projectId' => $file->projectId
        ])."'</script>";
    }
    private function create_default_directory($root, $subdirs, $week, $noaplica, $projectId) {
        $create     =   Directory::create([
            'projectId' => $projectId,
            'name' => $root
        ]);
        for($i = 0; $i < sizeof($subdirs); $i++)
        {
            $weeks      =   explode(",", $week[$i]);
            if(sizeof($weeks) > 1) {
                Directory::create([
                    'projectId' => $projectId,
                    'name' => $subdirs[$i],
                    'route' => 1,
                    'link' => $create->id,
                    'week_selected' => $week[$i],
                    'no_aplica' => (isset($noaplica[$i]) ? true : false)
                ]);
            }
            else {
                $weeks      =   explode("-", $week[$i]);
                $week_from  =   $weeks[0];
                $week_to    =   0;
                if(sizeof($weeks) > 1)
                    $week_to    =   $weeks[1];
                Directory::create([
                    'projectId' => $projectId,
                    'name' => $subdirs[$i],
                    'route' => 1,
                    'link' => $create->id,
                    'week_from' => $week_from,
                    'week_to' => $week_to,
                    'no_aplica' => (isset($noaplica[$i]) ? true : false)
                ]);
            }
        }
    }

    /* Axios callback */
    public function navigate(Request $r) {
        $dirs       =   Directory::where('projectId', $r->projectId)->where('route', ($r->route+1))->where('link', $r->link)->get();
        $weeknd     =   $this->weeknd($r->projectId);
        for($i = 0; $i < sizeof($dirs); $i++) {
            $dirs[$i]->created_atFormat   =   date('d-m-Y', strtotime($dirs[$i]->created_at));

            if($dirs[$i]->type == 'directory') {
                $dirs[$i]->count    =   Directory::where('link', $dirs[$i]->id)->count();
                $isAlert    =   false;
                $week_to    =   $dirs[$i]->week_to;
                $week_from  =   $dirs[$i]->week_from;
                $week_selected  =   $dirs[$i]->week_selected;
                foreach($weeknd['freeList'] as $item)
                {
                    if($dirs[$i]->week_selected != null) {
                        $selects    =   explode(",", $dirs[$i]->week_selected);
                        $newWeek    =   '';
                        for($w = 0; $w < sizeof($selects); $w++) {
                            $newWeekReplace     =   0;
                            if($selects[$w] <= $item['week']) 
                                $newWeekReplace     =  $selects[$w]+1;
                            else
                                $newWeekReplace     =  $selects[$w];
                            if($w+1 == sizeof($selects))
                                $newWeek    .=  $newWeekReplace;
                            else
                                $newWeek    .=  $newWeekReplace.',';
                        }
                        $week_selected     =   $newWeek;
                    }
                    else {
                        if($dirs[$i]->week_from <= $item['week']) {
                            $week_from     += 1;
                            if($dirs[$i]->week_to > 0)
                                $week_to   += 1;
                        }
                    }
                }
                if($dirs[$i]->week_selected != null) {
                    $getWeeks   =   explode(",", $week_selected);
                    foreach($getWeeks as $item)
                    {
                        if($weeknd['weekActual'] > $getWeeks) {
                            if($dirs[$i]->count == 0) {
                                $isAlert = true;
                                break;
                            }
                        }
                    }
                }
                else {
                    if($week_to != 0) {
                        for($x = $week_from; $x <= $week_to; $x++)
                        {
                            if($weeknd['weekActual'] > $x) {
                                if($dirs[$i]->count == 0) {
                                    $isAlert = true;
                                    break;
                                }
                            } 
                        }
                    }
                    else {
                        if($weeknd['weekActual'] > $week_from) {
                            if($dirs[$i]->count == 0)
                                $isAlert = true;
                        }
                    }
                }
                $dirs[$i]->alert    =   ($dirs[$i]->name == 'Cronograma de pagos' ? false : $isAlert);
            }
            else
                $dirs[$i]->alert    =   ($weeknd['totalWeek'] > $dirs[$i]->file_week ? true : false);
        }
        return $dirs->toJson();
    }
    public function navigateAddDir(Request $r) {
        $data   =   $r->except(['_token', 'semanas']);
        $weeks      =   explode("-", $r->semanas);
        $data['week_from']  =   $weeks[0];
        $data['week_to']    =   0;
        if(sizeof($weeks) > 1)
            $data['week_to']    =   $weeks[1];
        return Directory::create($data)->toJson();
    }
    public function navigateAddFile(Request $r) {
        $imageExtensions = ['jpg', 'jpeg', 'gif', 'png', 'bmp', 'svg', 'svgz', 'cgm', 'djv', 'djvu', 'ico', 'ief','jpe', 'pbm', 'pgm', 'pnm', 'ppm', 'ras', 'rgb', 'tif', 'tiff', 'wbmp', 'xbm', 'xpm', 'xwd'];
        $data   =   $r->except(['_token', 'file']);
        $fileName   =   uniqid().".".$r->file->extension();
        $data['file_path']  =   $fileName;
        $data['file_ext']   =   $r->file->extension();
        if(in_array($r->file->extension(), $imageExtensions))
            $data['type']   =   'image';
        else
            $data['type']   =   'docs';
        $r->file('file')->storeAs(
            'public/plexus', $fileName
        );
        return Directory::create($data)->toJson();
        // return "<script>alert('Archivo subido correctamente!');location.href='".url()->previous()."'</script>";
    }

    private function weeknd($projectId) {
        $project        =   Project::find($projectId);
        $libre          =   SemanaLibre::where('projectId', $projectId)->get();
        $weekFreeList   =   [];
        if(date('D', strtotime($project->inicia)) == 'Mon')
            $endDate    =   date("Y-m-d", strtotime($project->inicia."+ ".$project->semanas." week"." - 3 days"));
        else
            $endDate    =   date("Y-m-d", strtotime($project->inicia."+ ".$project->semanas." week"));
        if(!empty($libre)) {
            $i = 0;
            foreach($libre as $item)
            {
                $weekFreeList[$i]['date']   =   $item->week_free;
                $weekFreeList[$i]['week']   =   $this->getWeekNumber($project->inicia, $item->week_free);
                // $endDate    =   date("Y-m-d", strtotime($endDate.'+ 7 days')); // correr 1 semana todo el proyecto.
                $i++;
            }
        }
        return [
            'endDate' => $endDate,
            'totalWeek' => $this->getWeekNumber($project->inicia, $endDate),
            'weekActual' => $this->getWeekNumber($project->inicia, date('Y-m-d')),
            'freeList' => $weekFreeList
        ];
    }
    private function getWeekNumber($a, $b)
    {
        $startDate = new \DateTime($a);
        $endDate = new \DateTime($b);

        $diff = $endDate->diff($startDate);
        $numberOfWeeks  =   floor($diff->days / 7);
        return ($numberOfWeeks+1);
    }



    public $financiera     =   [
        'Contrato firmado',
        'Propuesta',
        'Ficha de proyecto',
        'Ficha de depósito / SWIFT',
        'Contratos de arrendamiento',
        'Situación migratoria',
        'Presupuesto de gastos',
        'Control de gastos',
        'Cronograma de pagos',
        'Espacio físico',
        'Cronogramas de rotaciones',
        'Reporte de cierre de Proyecto'
    ];
    public $operativa  =   [
        'Equipos integrados asignados',
        'To Do semanal integrados y consultores',
        'Sustento numérico estrategia BD diagnostico',
        'Aca de entrega de diagnóstico',
        'Cronograma detallado por área 1',
        'Estrategia Director - Gerente',
        'Estrategia Gerente - Consultor 1',
        'Planes de choque',
        'Acuerdo de expectativa de cliente',
        'STEERCOM Y EXCOM',
        'Periodos base firmados',
        'WP, DPR, MCES, CAJAS NEGRAS',
        'Sistemas de trabajo completo del diseño',
        'Indicadores de desempeño por frente de trabajo',
        'Acuerdo de implementación con el cliente',
        'Cronograma y material de capacitación área 1',
        'Cronograma de implementación',
        'Acta de implementación por frente de trábajo',
        'Sistema, reporte y políticas de auditoria área 1',
        'Ahorros validados por objetivos',
        'Plan More Work',
        'Ejecución de Plan More Work',
        'Entrega de proyecto al cliente: entregables completos entregados al cliente',
        'Carta de recomendación',
        'Lista de referencia',
        'Resumen ejecutivo (lecciones aprendidas)'
    ];
    public $estrategica_tactica    =   [
        'Reuniones de involucramiento',
        'Nombre del proyecto',
        'Lista definitiva de entregables detalladas para el cliente',
        'Actas de retraso cliente',
        'Bitácora de eventos',
        'Estudio de perfil del cliente (Top y otros)'
    ];
    public $gestion_humana     =   [
        'Compromiso de desarrollo consultor',
        'Actas de retroalimentación mitad proyectos y esfuerzos por parte del consultor',
        'Evaluación del consultor al final del proyecto por consultor 1',
        'Evaluación del Director Gerente'
    ];
}
