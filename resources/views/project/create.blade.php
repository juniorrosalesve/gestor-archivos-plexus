@extends('layouts.app')
@section('body')
    <div class="card shadow bordered bg-base-100">
        <div class="card-body">
            <div class="text-sm breadcrumbs">
                <ul>
                    <li>
                        <a href="{{ route('project-index') }}">Proyectos</a>
                    </li> 
                    <li>Nuevo proyecto</li>
                </ul>
            </div>
            <form method="POST" action="{{ route('store-project') }}" >
                @csrf
                <div class="grid grid-cols-1">
                    <div>
                        <div class="divider mb-8 text-xl">Información</div>
                    </div>
                </div>
                <div class="grid grid-cols-2">
                    <div class="mt-5 ml-3">
                        <div class="form-control">
                            <label class="label">
                                <span class="label-text">Nombre del proyecto <i class="text-red-500">*</i></span>
                            </label> 
                            <input type="text" name="name" class="input input-bordered" autocomplete="off" required>
                        </div>
                    </div>
                    <div class="mt-5 ml-3">
                        <div class="form-control">
                            <label class="label">
                                <span class="label-text">Fecha de inicio <i class="text-red-500">*</i></span>
                            </label> 
                            <input type="date" name="inicia" id="inicia" class="input input-bordered" autocomplete="off" required>
                        </div>
                    </div>
                    <div class="mt-5 ml-3">
                        <div class="form-control">
                            <label class="label">
                                <span class="label-text">Semanas <i class="text-red-500">*</i></span>
                            </label> 
                            <input type="number" name="semanas" id="semanas" min="1" class="input input-bordered" autocomplete="off" required>
                        </div>
                    </div>
                    <div class="mt-5 ml-3 form-control">
                        <label class="label">
                            <span class="label-text">Región <i class="text-red-500">*</i></span>
                        </label> 
                        <select name="regionId" class="select select-bordered w-full" onchange="selectRegion(this)" required>
                            <option value="" disabled selected>Elige una región</option>
                            @foreach ($regions as $item)
                                <option value="{{ $item->id }}">{{ $item->name }} - {{ $item->user->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mt-5 ml-3 form-control">
                        <label class="label">
                            <span class="label-text">País <i class="text-red-500">*</i></span>
                        </label> 
                        <select name="countryId" class="select select-bordered w-full" id="eligePais" onchange="selectPais(this)">
                            <option value="" disabled selected>Debes elegir una región para poder seleccionar un país</option>
                        </select>
                    </div>
                    <div class="mt-5 ml-3 form-control">
                        <label class="label">
                            <span class="label-text">Gerente <i class="text-red-500">*</i></span>
                        </label> 
                        <select name="managerId" class="select select-bordered w-full" required>
                            <option value="" disabled selected>Elige una opción</option>
                            @foreach ($gerentes as $gerente)
                                <option value="{{ $gerente->id }}">{{ $gerente->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <hr />
                <div class="ml-3 mt-7">
                    <h1 class="text-2xl">Estructura</h1>

                    <h1 class="mt-3 text-lg italic">Admin. / Financiera</h1>
                    <div class="grid grid-cols-2 rounded-lg bg-blue-200 p-4">
                        @foreach($financiera as $item)
                            <div class="ml-3">
                                <div class="form-control">
                                    <label class="label">
                                        <span class="label-text">Sub-carpeta <i class="text-red-500">*</i></span>
                                    </label> 
                                    <input type="text" name="financiera[]" value="{{ $item }}" readonly class="input input-bordered" autocomplete="off" required>
                                </div>
                            </div>
                            <div class="ml-3">
                                <div class="form-control">
                                    <label class="label">
                                        <span class="label-text">N° Semana <i class="text-red-500">*</i></span>
                                    </label> 
                                    <input type="number" name="financiera_week[]" min="1" value="1" class="input input-bordered" autocomplete="off" required>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    <h1 class="mt-3 text-lg italic">Operativa</h1>
                    <div class="grid grid-cols-2 rounded-lg bg-base-200 p-4">
                        @foreach ($operativa as $item)
                            <div class="ml-3">
                                <div class="form-control">
                                    <label class="label">
                                        <span class="label-text">Sub-carpeta <i class="text-red-500">*</i></span>
                                    </label> 
                                    <input type="text" name="operativa[]" value="{{ $item }}" readonly class="input input-bordered" autocomplete="off" required>
                                </div>
                            </div>
                            <div class="ml-3">
                                <div class="form-control">
                                    <label class="label">
                                        <span class="label-text">N° Semana <i class="text-red-500">*</i></span>
                                    </label> 
                                    <input type="number" name="operativa_week[]" min="1" value="1" class="input input-bordered" autocomplete="off" required>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    <h1 class="mt-3 text-lg italic">Estratégica / Táctica</h1>
                    <div class="grid grid-cols-2 rounded-lg bg-yellow-200 p-4">
                        @foreach ($estrategica_tactica as $item)
                            <div class="ml-3">
                                <div class="form-control">
                                    <label class="label">
                                        <span class="label-text">Sub-carpeta <i class="text-red-500">*</i></span>
                                    </label> 
                                    <input type="text" name="estrategica_tactica[]" value="{{ $item }}" readonly class="input input-bordered" autocomplete="off" required>
                                </div>
                            </div>
                            <div class="ml-3">
                                <div class="form-control">
                                    <label class="label">
                                        <span class="label-text">N° Semana <i class="text-red-500">*</i></span>
                                    </label> 
                                    <input type="number" name="estrategica_tactica_week[]" min="1" value="1" class="input input-bordered" autocomplete="off" required>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    
                    <h1 class="mt-3 text-lg italic">Gestión Humana</h1>
                    <div class="grid grid-cols-2 rounded-lg bg-green-200 p-4">
                        @foreach ($gestion_humana as $item)
                            <div class="ml-3">
                                <div class="form-control">
                                    <label class="label">
                                        <span class="label-text">Sub-carpeta <i class="text-red-500">*</i></span>
                                    </label> 
                                    <input type="text" name="gestion_humana[]" value="{{ $item }}" readonly class="input input-bordered" autocomplete="off" required>
                                </div>
                            </div>
                            <div class="ml-3">
                                <div class="form-control">
                                    <label class="label">
                                        <span class="label-text">N° Semana <i class="text-red-500">*</i></span>
                                    </label> 
                                    <input type="number" name="gestion_humana_week[]" min="1" value="1" class="input input-bordered" autocomplete="off" required>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
                <hr style="margin-top:30px;">
                <div class="modal-action">
                    <button type="submit" class="btn btn-secondary btn-sm">Crear proyecto</button>
                </div>
            </form>
        </div>
    </div>
    <script>
        const paises    =   @json($countries);
        function selectRegion(e) {
            const id    =   $(e).val();
            let countries   =   [];
            for(var i = 0; i < paises.length; i++) {
                if(paises[i].regionId == id)
                    countries.push(paises[i]);
            }
            $("#eligePais").html('<option value="" disabled selected>Elige un país</option>');
            for(var i = 0; i < countries.length; i++) 
                $("#eligePais").append('<option value="'+countries[i].id+'">'+countries[i].name+'</option>');
        }
    </script>
@endsection