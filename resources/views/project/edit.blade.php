@extends('layouts.app')
@section('body')
    <div class="card shadow bordered bg-base-100">
        <div class="card-body">
            <div class="text-sm breadcrumbs">
                <ul>
                    <li>
                        <a href="{{ route('project-index') }}">Proyectos</a>
                    </li> 
                    <li>Editando</li>
                    <li>{{ $project->region->name }} - {{ $project->region->user->name }}</li>
                    <li><a href="{{ route('projects', [
                        'regionId' => $project->regionId,
                        'countryId' => $project->countryId
                    ]) }}">{{ $project->country->name }}</a></li>
                    <li>{{ $project->name }}</li>
                </ul>
            </div>
            <form method="POST" action="{{ route('update-project') }}" >
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
                            <input type="text" name="name" value="{{ $project->name }}" class="input input-bordered" autocomplete="off" required>
                        </div>
                    </div>
                    <div class="mt-5 ml-3">
                        <div class="form-control">
                            <label class="label">
                                <span class="label-text">Fecha de inicio <i class="text-red-500">*</i></span>
                            </label> 
                            <input type="date" value="{{ $project->inicia }}" name="inicia" id="inicia" class="input input-bordered" autocomplete="off" required>
                        </div>
                    </div>
                    <div class="mt-5 ml-3">
                        <div class="form-control">
                            <label class="label">
                                <span class="label-text">Semanas <i class="text-red-500">*</i></span>
                            </label> 
                            <input type="number" name="semanas" value="{{ $project->semanas }}" id="semanas" min="1" class="input input-bordered" autocomplete="off" required>
                        </div>
                    </div>
                    <div class="mt-5 ml-3 form-control">
                        <label class="label">
                            <span class="label-text">Región <i class="text-red-500">*</i></span>
                        </label> 
                        <select name="regionId" class="select select-bordered w-full" id="selectRegionId" onchange="selectRegion(this)" required>
                            @foreach ($regions as $item)
                                @if ($item->id == $project->regionId)
                                    <option value="{{ $item->id }}">{{ $item->name }} - {{ $item->user->name }}</option>
                                    @break
                                @endif
                            @endforeach
                            @foreach ($regions as $item)
                                @if ($item->id != $project->regionId)
                                    <option value="{{ $item->id }}">{{ $item->name }} - {{ $item->user->name }}</option>
                                @endif
                            @endforeach
                        </select>
                    </div>
                    <div class="mt-5 ml-3 form-control">
                        <label class="label">
                            <span class="label-text">País <i class="text-red-500">*</i></span>
                        </label> 
                        <select name="countryId" class="select select-bordered w-full" id="eligePais" onchange="selectPais(this)">
                            @foreach ($project->region->countries as $item)
                                @if ($item->id == $project->countryId)
                                    <option value="{{ $item->id }}">{{ $item->name }}</option>
                                    @break
                                @endif
                            @endforeach
                            @foreach ($project->region->countries as $item)
                                @if ($item->id != $project->countryId)
                                    <option value="{{ $item->id }}">{{ $item->name }}</option>
                                @endif
                            @endforeach
                        </select>
                    </div>
                    <div class="mt-5 ml-3 form-control">
                        <label class="label">
                            <span class="label-text">Gerente <i class="text-red-500">*</i></span>
                        </label> 
                        <select name="managerId" class="select select-bordered w-full" required>
                            @foreach ($gerentes as $gerente)
                                @if ($gerente->id == $project->managerId)
                                    <option value="{{ $gerente->id }}">{{ $gerente->name }}</option>
                                    @break
                                @endif
                            @endforeach
                            @foreach ($gerentes as $gerente)
                                @if ($gerente->id != $project->managerId)
                                    <option value="{{ $gerente->id }}">{{ $gerente->name }}</option>
                                @endif
                            @endforeach
                        </select>
                    </div>
                </div>
                <hr />
                <div class="ml-3 mt-7">
                    @php
                        $bgcolors   =   [
                            'bg-blue-200',
                            'bg-base-200',
                            'bg-yellow-200',
                            'bg-green-200'
                        ];
                        $i = 0;
                    @endphp
                    <h1 class="text-2xl">Estructura</h1>
                    @foreach ($root as $item)
                        <h1 class="mt-3 text-lg italic">{{ $item->name }}</h1>
                        <div class="grid grid-cols-2 rounded-lg {{ $bgcolors[$i] }} p-4">
                            @foreach ($dirs as $xitem)
                                @if ($xitem->link == $item->id)
                                    <div class="ml-3">
                                        <div class="form-control">
                                            <label class="label">
                                                <span class="label-text">Sub-carpeta <i class="text-red-500">*</i></span>
                                            </label> 
                                            <input type="text" value="{{ $xitem->name }}" readonly class="input input-bordered" autocomplete="off" required>
                                        </div>
                                    </div>
                                    <div class="ml-3">
                                        <div class="form-control">
                                            <label class="label">
                                                <span class="label-text">N° Semana <i class="text-red-500">*</i></span>
                                            </label> 
                                            @if ($xitem->week_to == 0)
                                                <input type="text" name="dir_update_value[]" min="1" value="{{ $xitem->week_from }}" class="input input-bordered" autocomplete="off" required>
                                            @else
                                                <input type="text" name="dir_update_value[]" min="1" value="{{ $xitem->week_from }}-{{ $xitem->week_to }}" class="input input-bordered" autocomplete="off" required>
                                            @endif
                                        </div>
                                    </div>
                                    <input type="hidden" name="dir_update_id[]" value="{{ $xitem->id }}">
                                @endif
                            @endforeach
                        </div>
                        @php
                            $i++;
                        @endphp
                    @endforeach
                </div>
                <input type="hidden" name="projectId" value="{{ $project->id }}">
                <hr style="margin-top:30px;">
                <div class="modal-action">
                    <button type="submit" class="btn btn-secondary btn-sm">Guardar</button>
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