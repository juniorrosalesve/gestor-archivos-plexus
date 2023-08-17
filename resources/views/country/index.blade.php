@extends('layouts.app')

@section('body')
    <div class="card shadow bordered bg-base-100">
        <div class="card-body">
            <div class="flex justify-between my-4">
                <div>
                    <h2 class="text-2xl inline-block">Paises</h2>
                </div>
                <div>
                    {{-- <button onclick="location.href='#'" class="btn btn-secondary btn-sm modal-button inline-block">Añadir nuevo</button>  --}}
                </div>
            </div>
            <hr />
            <div class="overflow-x-auto mt-2">
                <table class="w-full" id="table">
                    <thead>
                        <th>Nombre</th>
                    </thead>
                    <tbody>
                        @foreach ($countries as $country)
                            <tr>
                                <td>{{ $country->name }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection