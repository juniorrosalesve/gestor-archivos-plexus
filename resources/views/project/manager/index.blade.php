@extends('layouts.app')

@section('body')
    <div class="card shadow bordered bg-base-100">
        <div class="card-body">
            <div class="flex justify-between my-4">
                <div>
                    <h2 class="text-2xl inline-block">Proyectos</h2>
                </div>
            </div>
            <hr />
            <div class="max-w-[99%] overflow-x-auto mt-2">
                <table class="w-auto" id="table">
                    <thead>
                        <tr>
                            <td>Id</td>
                            <td>Nombre</td>
                            <td>Region</td>
                            <td>País</td>
                            <td>Tiempo de entrega</td>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($projects as $item)
                            <tr>
                                <td>{{ $item->id }}</td>
                                <td><a href="{{ route('project', [
                                    'regionId' => $item->regionId,
                                    'countryId' => $item->countryId,
                                    'projectId' => $item->id
                                ]) }}" class="underline">{{ $item->name }}</a></td>
                                <td>{{ $item->region->name }} - {{ $item->region->user->name }}</td>
                                <td>{{ $item->country->name }}</td>
                                <td>
                                    @if ($item->delivery == null)
                                        <span class="italic">No aplica</span>
                                    @else
                                        {{ date('d-m-Y', strtotime($item->delivery)) }}
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection