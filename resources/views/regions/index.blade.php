@extends('layouts.app')

@section('body')
    <div class="card shadow bordered bg-base-100">
        <div class="card-body">
            <div class="flex justify-between my-4">
                <div>
                    <h2 class="text-2xl inline-block">Regiones</h2>
                </div>
                <div>
                    <button onclick="location.href='{{ route('create-region') }}'" class="btn btn-secondary btn-sm modal-button inline-block">Añadir nuevo</button> 
                </div>
            </div>
            <hr />
            <div class="overflow-x-auto mt-2">
                <table class="w-full" id="table">
                    <thead>
                        <th>Nombre</th>
                        <th>Director</th>
                        <th>Paises</th>
                    </thead>
                    <tbody>
                        @foreach ($regions as $region)
                            <tr>
                                <td>{{ $region->name }}</td>
                                <td>{{ $region->user->name }}</td>
                                <td>
                                    @for ($i = 0; $i < sizeof($region->countries); $i++)
                                        @if ($i+1 == sizeof($region->countries))
                                            {{ $region->countries[$i]->name }}
                                        @else
                                            {{ $region->countries[$i]->name }}, 
                                        @endif
                                    @endfor
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection