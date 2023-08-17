@extends('layouts.app')
@section('body')
    <div class="card shadow bordered bg-base-100">
        <div class="card-body">
            <div class="text-sm breadcrumbs">
                <ul>
                    <li>
                        <a href="{{ route('store-project') }}">Proyectos</a>
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
                                <span class="label-text">Nombre <i class="text-red-500">*</i></span>
                            </label> 
                            <input type="text" name="name" class="input input-bordered" autocomplete="off" required>
                        </div>
                    </div>
                    <div class="mt-5 ml-3">
                        <div class="form-control">
                            <label class="label">
                                <span class="label-text">Fecha de entrega (opcional)</span>
                            </label> 
                            <input type="date" name="delivery" class="input input-bordered" autocomplete="off" required>
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
                        <select name="countryId" class="select select-bordered w-full" id="eligePais" onchange="selectPais(this)"></select>
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
                <hr style="margin-top:30px;">
                <div class="modal-action">
                    <button type="submit" class="btn btn-secondary btn-sm">Registrar</button>
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