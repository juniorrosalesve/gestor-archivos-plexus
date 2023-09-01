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
            'gerentes' => User::where('access', 'g')->orderBy('name', 'asc')->get(),
            'financiera' => $this->financiera,
            'operativa' => $this->operativa,
            'estrategica_tactica' => $this->estrategica_tactica,
            'gestion_humana' => $this->gestion_humana
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
        $dirs       =   Directory::where('projectId', $projectId)->where('route', 0)->get();
        return view('project.navigate', [
            'region' => Region::find($regionId),
            'country' => Country::find($countryId),
            'project' => Project::find($projectId),
            'dirs' => $dirs
        ]);
    }

    public function store(Request $r) {
        $store  =   Project::create($r->except([
            '_token',
            'financiera',
            'financiera_week',
            'operativa',
            'operativa_week',
            'estrategica_tactica',
            'estrategica_tactica_week',
            'gestion_humana',
            'gestion_humana_week'
        ]));

        $this->create_default_directory('Admin. / Financiera', $r->financiera, $r->financiera_week, $store->id);
        $this->create_default_directory('Operativa', $r->operativa, $r->operativa_week, $store->id);
        $this->create_default_directory('Estratégica / Táctica', $r->estrategica_tactica, $r->estrategica_tactica_week, $store->id);
        $this->create_default_directory('Gestión Humana', $r->gestion_humana, $r->gestion_humana_week, $store->id);

        return "<script>alert('Proyecto creado correctamente!');location.href='".route('projects', [
            'regionId' => $store->regionId,
            'countryId' => $store->countryId
        ])."'</script>";
    }
    private function create_default_directory($root, $subdirs, $week, $projectId) {
        $create     =   Directory::create([
            'projectId' => $projectId,
            'name' => $root
        ]);
        for($i = 0; $i < sizeof($subdirs); $i++)
        {
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
                'week_to' => $week_to
            ]);
        }
    }

    /* Axios callback */
    public function navigate(Request $r) {
        $dirs   =   Directory::where('projectId', $r->projectId)->where('route', ($r->route+1))->where('link', $r->link)->get();
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
