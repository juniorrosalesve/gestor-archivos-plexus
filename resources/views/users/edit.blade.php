@extends('layouts.app')
@section('body')
    <div class="card shadow bordered bg-base-100">
        <div class="card-body">
            <div class="text-sm breadcrumbs">
                <ul>
                    <li>
                        <a href="{{ route('users') }}">Usuarios</a>
                    </li> 
                    <li>Editando usuario</li>
                </ul>
            </div>
            <form method="POST" action="{{ route('update-user') }}">
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
                            <input type="text" name="name" value="{{ $user->name }}" class="input input-bordered" autocomplete="off" required>
                        </div>
                    </div>
                    <div class="mt-5 ml-3">
                        <div class="form-control">
                            <label class="label">
                                <span class="label-text">E-mail <i class="text-red-500">*</i></span>
                            </label> 
                            <input type="text" name="email" value="{{ $user->email }}" class="input input-bordered" autocomplete="off" required>
                        </div>
                    </div>
                    <div class="mt-5 ml-3 form-control">
                        <label class="label">
                            <span class="label-text">Acceso <i class="text-red-500">*</i></span>
                        </label> 
                        <select name="access" class="select select-bordered w-full" required>
                            {{-- <option value="" disabled selected>Elige uno</option> --}}
                            <option value="{{ $user->access }}" selected>{{ $ranks[$user->access] }}</option>
                            @foreach ($ranks as $key=>$value)
                                @if ($key != $user->access)
                                    <option value="{{ $key }}">{{ $value }}</option>
                                @endif
                            @endforeach
                        </select>
                    </div>

                    <div class="mt-5 ml-3 form-control">
                        <label class="label">
                            <span class="label-text">Usuario suspendido <i class="text-red-500">*</i></span>
                        </label> 
                        <select name="suspend" class="select select-bordered w-full" required>
                            @if ($user->suspend == true)
                                <option value="1">Sí</option>
                                <option value="0">No</option>
                            @else
                                <option value="0">No</option>
                                <option value="1">Sí</option>
                            @endif
                        </select>
                    </div>
                </div>
                <input type="hidden" name="userId" value="{{ $user->id }}">
                <hr style="margin-top:30px;">
                <div class="modal-action">
                    <button type="submit"class="btn btn-secondary btn-sm">Guardar</button>
                </div>
            </form>
        </div>
    </div>
@endsection