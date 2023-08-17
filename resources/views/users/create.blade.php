@extends('layouts.app')
@section('body')
    <div class="card shadow bordered bg-base-100">
        <div class="card-body">
            <div class="text-sm breadcrumbs">
                <ul>
                    <li>
                        <a href="{{ route('users') }}">Usuarios</a>
                    </li> 
                    <li>Nuevo usuario</li>
                </ul>
            </div>
            <form method="POST" action="{{ route('store-user') }}">
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
                                <span class="label-text">E-mail <i class="text-red-500">*</i></span>
                            </label> 
                            <input type="text" name="email" class="input input-bordered" autocomplete="off" required>
                        </div>
                    </div>
                    <div class="mt-5 ml-3">
                        <div class="form-control">
                            <label class="label">
                                <span class="label-text">Contraseña <small>(Se genera automaticamente)</small> <i class="text-red-500">*</i></span>
                            </label> 
                            <input type="text" value="{{ mt_rand(1000, 9999) }}" name="password" class="input input-bordered" autocomplete="off" readonly required>
                        </div>
                    </div>
                    <div class="mt-5 ml-3 form-control">
                        <label class="label">
                            <span class="label-text">Acceso <i class="text-red-500">*</i></span>
                        </label> 
                        <select name="access" class="select select-bordered w-full" required>
                            <option value="" disabled selected>Elige uno</option>
                            @foreach ($ranks as $key=>$value)
                                <option value="{{ $key }}">{{ $value }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <hr style="margin-top:30px;">
                <div class="modal-action">
                    <button type="submit"class="btn btn-secondary btn-sm">Registrar</button>
                </div>
            </form>
        </div>
    </div>
@endsection