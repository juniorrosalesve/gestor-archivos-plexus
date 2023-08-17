@extends('layouts.app')

@section('body')
    <div class="card shadow bordered bg-base-100">
        <div class="card-body">
            <div class="flex justify-between my-4">
                <div>
                    <h2 class="text-2xl inline-block">Dashboard</h2>
                </div>
            </div>
            <hr />
            <div class="overflow-x-auto mt-2">
                <div class="grid grid-cols-1">
                    <div>
                        <h1 class="text-lg mb-3">Algunos registros</h1>
                        <table class="w-full" id="table">
                            <thead>
                                <td>#</td>
                                <td>Nombre</td>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                    <div>
                        <canvas id="myChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection