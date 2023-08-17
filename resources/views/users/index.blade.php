@extends('layouts.app')

@section('body')
    <div class="card shadow bordered bg-base-100">
        <div class="card-body">
            <div class="flex justify-between my-4">
                <div>
                    <h2 class="text-2xl inline-block">Usuarios</h2>
                </div>
                <div>
                    <select class="select select-bordered inline-block mr-3" onchange="filterBy(this)">
                        @foreach ($ranks as $key=>$value)
                            @if ($key == $actualAccess)
                                <option value="{{ $key }}">{{ $value }}</option>
                                @break
                            @endif
                        @endforeach
                        @foreach ($ranks as $key=>$value)
                            @if ($key != $actualAccess)
                                <option value="{{ $key }}">{{ $value }}</option>
                            @endif
                        @endforeach
                    </select>
                    <button onclick="location.href='{{ route('create-user') }}'" class="btn btn-secondary btn-md modal-button inline-block">Añadir nuevo</button> 
                </div>
            </div>
            <hr />
            <div class="overflow-x-auto mt-2">
                <table class="w-full" id="table">
                    <thead>
                        <th>Nombre</th>
                    </thead>
                    <tbody>
                        @foreach ($users as $user)
                            <tr>
                                <td>{{ $user->name }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <script>
        function filterBy(e) {
            location.href='{{ route("users") }}?access='+e.value;
        }
    </script>
@endsection