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
                                ]) }}"><span class="underline">{{ $item->name }}</span></a> <small><a href="{{ route('edit-project', [
                                    'projectId' => $item->id
                                ]) }}"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" class="w-5 ml-3 -mt-1 inline-block"><title>Editar Proyecto</title><path d="M4 18H12.13L11 19.13V20H4C2.9 20 2 19.11 2 18V6C2 4.89 2.89 4 4 4H10L12 6H20C21.1 6 22 6.89 22 8V10.15C21.74 10.06 21.46 10 21.17 10C20.75 10 20.36 10.11 20 10.3V8H4V18M22.85 13.47L21.53 12.15C21.33 11.95 21 11.95 20.81 12.15L19.83 13.13L21.87 15.17L22.85 14.19C23.05 14 23.05 13.67 22.85 13.47M13 19.96V22H15.04L21.17 15.88L19.13 13.83L13 19.96Z" /></svg></a></small></td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection