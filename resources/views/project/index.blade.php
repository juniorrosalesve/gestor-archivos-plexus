@extends('layouts.app')

@section('body')
    <div class="card shadow bordered bg-base-100">
        <div class="card-body">
            <div class="flex justify-between my-4">
                <div>
                    <h2 class="text-2xl inline-block">Proyectos</h2>
                </div>
                <div>
                    <button onclick="location.href='{{ route('create-project') }}'" class="btn btn-secondary modal-button">Crear Proyecto</button> 
                </div>
            </div>
            <hr />
            <div class="overflow-x-auto mt-2 py-10">
                <div class="grid grid-cols-2">
                    <div>
                        <select class="select select-bordered min-w-[90%] mx-auto" id="eligeRegion" onchange="selectRegion(this)">
                            <option value="" disabled selected>Elige una región</option>
                            @foreach ($regions as $item)
                                <option value="{{ $item->id }}">{{ $item->name }} - {{ $item->user->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <select class="select select-bordered min-w-[90%] mx-auto" id="eligePais" onchange="selectPais(this)"></select>
                    </div>
                </div>
                <div class="w-[13%] mx-auto">
                    <button type="button" class="btn btn-primary btn-sm mt-10" onclick="joinToProject()">Ingresar</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        $("#eligePais").hide();

        const paises    =   @json($countries);
        let isVisible   =   false;

        function joinToProject() {
            const region    =   $("#eligeRegion").val();
            const country   =   $("#eligePais").val();
            if(region != null && country != null)
                location.href='{{ url("/projects/l/") }}/'+region+'/'+country;
            else
                alert("Debes elegir una región y un país.");
        }

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
            if(!isVisible) {
                $("#eligePais").show();
                isVisible = true;
            }
        }
    </script>
@endsection