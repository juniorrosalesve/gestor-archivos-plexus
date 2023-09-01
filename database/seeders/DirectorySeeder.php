<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

use App\Models\Directory;

class DirectorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // $this->test(1);
        // $this->test(2);
        // $this->test(3);
    }
    private function test($projectId) {
        $financiera     =   [
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
        $operativa  =   [
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
        $estrategica_tactica    =   [
            'Reuniones de involucramiento',
            'Nombre del proyecto',
            'Lista definitiva de entregables detalladas para el cliente',
            'Actas de retraso cliente',
            'Bitácora de eventos',
            'Estudio de perfil del cliente (Top y otros)'
        ];
        $gestion_humana     =   [
            'Compromiso de desarrollo consultor',
            'Actas de retroalimentación mitad proyectos y esfuerzos por parte del consultor',
            'Evaluación del consultor al final del proyecto por consultor 1',
            'Evaluación del Director Gerente'
        ];

        Directory::create([
            'projectId' => $projectId,
            'name' => 'Admin. / Financiera'
        ]);
        Directory::create([
            'projectId' => $projectId,
            'name' => 'Operativa'
        ]);

        Directory::create([
            'projectId' => $projectId,
            'name' => 'Estratégica / Táctica'
        ]);
        Directory::create([
            'projectId' => $projectId,
            'name' => 'Gestión Humana'
        ]);
        for($i = 0; $i < sizeof($financiera); $i++) {
            if($i == 0) {
                Directory::create([
                    'projectId' => $projectId,
                    'name' => $financiera[$i],
                    'route' => 1,
                    'link' => 1,
                    'required' => 1
                ]);
            }
            else {
                Directory::create([
                    'projectId' => $projectId,
                    'name' => $financiera[$i],
                    'route' => 1,
                    'link' => 1,
                    'required' => mt_rand(1, 5)
                ]);
            }
        }
        for($i = 0; $i < sizeof($operativa); $i++)
            Directory::create([
                'projectId' => $projectId,
                'name' => $operativa[$i],
                'route' => 1,
                'link' => 2,
                'required' => mt_rand(1, 5)
            ]);
        for($i = 0; $i < sizeof($estrategica_tactica); $i++)
            Directory::create([
                'projectId' => $projectId,
                'name' => $estrategica_tactica[$i],
                'route' => 1,
                'link' => 3,
                'required' => mt_rand(1, 5)
            ]);
        for($i = 0; $i < sizeof($gestion_humana); $i++)
            Directory::create([
                'projectId' => $projectId,
                'name' => $gestion_humana[$i],
                'route' => 1,
                'link' => 4,
                'required' => mt_rand(1, 5)
            ]);
    }
}
