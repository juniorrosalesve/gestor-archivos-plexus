@extends('layouts.app')

@section('body')
    <div class="card shadow bordered bg-base-100">
        <div class="card-body">
            <div class="flex justify-between my-4">
                <div>
                    <h2 class="text-2xl inline-block">Todos los proyectos</h2>
                </div>
            </div>
            <hr />
            <div class="overflow-x-auto mt-2">
                <table id="table">
                    <thead>
                        <tr>
                            <td>#</td>
                            <td>Proyecto</td>
                            <td>Fecha de inicio</td>
                            <td>Semanas</td>
                            <td>Fecha final</td>
                            <td>Semana actual</td>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($projects as $project)
                            <tr>
                                <td>{{ $project->id }}</td>
                                <td><a href="{{ route('project', [
                                    'regionId' => $project->regionId,
                                    'countryId' => $project->countryId,
                                    'projectId' => $project->id
                                ]) }}" class="underline">{{ $project->name }}</a></td>
                                <td>{{ date("d-m-y", strtotime($project->inicia)) }}</td>
                                <td>{{ $project->semanas }}</td>
                                <td>{{ date("d-m-y", strtotime($project->inicia."+ ".$project->semanas." week")) }}</td>
                                <td>
                                    @php
                                        $startDate = new DateTime($project->inicia);
                                        $endDate = new DateTime();
            
                                        $diff = $endDate->diff($startDate);
                                        $numberOfWeeks  =   floor($diff->days / 7);
                                        $weekActual     =   $numberOfWeeks+1;
                                        if($weekActual >= $project->semanas)
                                            $weekActual     =   $project->semanas;
            
                                        if($weekActual == $project->semanas)
                                            echo 'Proyecto cerrado';
                                        else
                                            echo $weekActual;
                                    @endphp
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection