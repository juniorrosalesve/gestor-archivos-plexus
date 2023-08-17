@extends('layouts.app')

@section('body')
    <div class="card shadow bordered bg-base-100">
        <div class="card-body">
            <div class="text-sm breadcrumbs">
                <ul>
                    <li>
                        <a href="{{ route('project-index') }}">Proyectos</a>
                    </li> 
                    <li>{{ $region->name }} - {{ $region->user->name }}</li>
                    <li>{{ $country->name }}</li>
                </ul>
            </div>
            <hr />
            <div class="max-w-[99%] overflow-x-auto mt-2 py-10">
                <table class="w-auto" id="table">
                    <thead>
                        <tr>
                            <td>Id</td>
                            <td>Nombre</td>
                            <td>Tiempo de entrega</td>
                            <td>Progreso</td>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($projects as $item)
                            <tr>
                                <td>{{ $item->id }}</td>
                                <td><a href="{{ route('project', [
                                    'regionId' => $region->id,
                                    'countryId' => $country->id,
                                    'projectId' => $item->id
                                ]) }}" class="underline">{{ $item->name }}</a></td>
                                <td>
                                    @if ($item->delivery == null)
                                        <span class="italic">No aplica</span>
                                    @else
                                        {{ date('d-m-Y', strtotime($item->delivery)) }}
                                    @endif
                                </td>
                                <td>{{ $item->porcentaje }}%</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection