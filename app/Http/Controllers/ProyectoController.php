<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\User;
use App\Models\Project;
use App\Models\Region;
use App\Models\Country;
use App\Models\Directory;

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
            'gerentes' => User::where('access', 'g')->orderBy('name', 'asc')->get()
        ]);
    }
    public function projects($regionId, $countryId) {
        $projects   =   Project::where('regionId', $regionId)->where('countryId', $countryId)->get();
        for($i = 0; $i < sizeof($projects); $i++) {
            $dirs   =   $projects[$i]->directories;
            $requireds  =   0;
            $registers  =   0;
            for($x = 0; $x < sizeof($dirs); $x++) {
                if($dirs[$x]['type'] == 'directory')
                {
                    if($dirs[$x]['required'] != 0) {
                        $dirs[$x]['registers']  =   Directory::where('type', '!=', 'directory')->where('link', $dirs[$x]['id'])->count();
                        $requireds  +=  $dirs[$x]['required'];
                        $registers  +=  $dirs[$x]['registers'];
                    }
                }
            }
            $projects[$i]['porcentaje']     =   round(($registers*100)/$requireds, 2);
        }
        return view('project.project', [
            'region' => Region::find($regionId),
            'country' => Country::find($countryId),
            'projects' => $projects
        ]);
    }
    public function project($regionId, $countryId, $projectId) {
        $dirs       =   Directory::where('projectId', $projectId)->where('route', 0)->get();
        $subdirs    =   Directory::where('projectId', $projectId)->where('route', '>=', 1)->get();
        $requireds  =   0;
        $registers  =   0;
        for($i = 0; $i < sizeof($subdirs); $i++) {
            if($subdirs[$i]['type'] == 'directory')
            {
                if($subdirs[$i]['required'] != 0) {
                    $subdirs[$i]['registers']  =   Directory::where('type', '!=', 'directory')->where('link', $subdirs[$i]['id'])->count();
                    $requireds  +=  $subdirs[$i]['required'];
                    $registers  +=  $subdirs[$i]['registers'];
                }
            }
        }
        $porcentaje     =   round(($registers*100)/$requireds, 2);
        return view('project.navigate', [
            'region' => Region::find($regionId),
            'country' => Country::find($countryId),
            'project' => Project::find($projectId),
            'dirs' => $dirs,
            'porcentaje' => $porcentaje
        ]);
    }

    public function store(Request $r) {
        $store  =   Project::create($r->except('_token'));
        $rootDir    =   Directory::create([
            'projectId' => $store->id,
            'name' => 'Admin. / Financiera'
        ]);
        for($i = 0; $i < sizeof($this->financiera); $i++)
            Directory::create([
                'projectId' => $store->id,
                'name' => $this->financiera[$i],
                'route' => 1,
                'link' => $rootDir->id
            ]);
        $rootDir    =   Directory::create([
            'projectId' => $store->id,
            'name' => 'Operativa'
        ]);
        for($i = 0; $i < sizeof($this->operativa); $i++)
            Directory::create([
                'projectId' => $store->id,
                'name' => $this->operativa[$i],
                'route' => 1,
                'link' => $rootDir->id
            ]);

        $rootDir    =   Directory::create([
            'projectId' => $store->id,
            'name' => 'Estratégica / Táctica'
        ]);
        for($i = 0; $i < sizeof($this->estrategica_tactica); $i++)
            Directory::create([
                'projectId' => $store->id,
                'name' => $this->estrategica_tactica[$i],
                'route' => 1,
                'link' => $rootDir->id
            ]);
        $rootDir    =   Directory::create([
            'projectId' => $store->id,
            'name' => 'Gestión Humana'
        ]);
        for($i = 0; $i < sizeof($this->gestion_humana); $i++)
            Directory::create([
                'projectId' => $store->id,
                'name' => $this->gestion_humana[$i],
                'route' => 1,
                'link' => $rootDir->id
            ]);
        return "<script>alert('Proyecto creado correctamente!');location.href='".route('projects', [
            'regionId' => $store->regionId,
            'countryId' => $store->countryId
        ])."'</script>";
    }

    /* Axios callback */
    public function navigate(Request $r) {
        // return json_encode($r->all());
        $dirs   =   Directory::where('projectId', $r->projectId)->where('route', ($r->route+1))->where('link', $r->link)->get();
        for($i = 0; $i < sizeof($dirs); $i++) {
            if($dirs[$i]['type'] == 'directory')
            {
                if($dirs[$i]['required'] != 0)
                    $dirs[$i]['registers']  =   Directory::where('type', '!=', 'directory')->where('link', $dirs[$i]['id'])->count();
            }
        }
        return $dirs->toJson();
    }
    public function navigateAddDir(Request $r) {
        Directory::create($r->except('_token'));
        return "<script>alert('Carpeta creada correctamente!');location.href='".url()->previous()."'</script>";
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
        Directory::create($data);
        return "<script>alert('Archivo subido correctamente!');location.href='".url()->previous()."'</script>";
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
