@extends('layouts.app')
@section('body')
    <link rel="stylesheet" href="{{ asset('css/tree.css') }}">
    <div class="card shadow bordered bg-base-100">
        <div class="card-body">
            <div class="flex justify-between">
                <div class="text-sm breadcrumbs">
                    <ul>
                        <li>
                            @if (Auth::user()->access == 'a')
                                <a href="{{ route('project-index') }}">Proyectos</a>
                            @else
                                <a href="{{ route('manager-list') }}">Proyectos</a>
                            @endif
                        </li> 
                        <li>{{ $region->name }} - {{ $region->user->name }}</li>
                        @if (Auth::user()->access == 'a')
                            <li><a href="{{ route('projects', [
                                'regionId' => $region->id,
                                'countryId' => $country->id
                            ]) }}">{{ $country->name }}</a></li>
                        @else 
                            <li>{{ $country->name }}</li>
                        @endif
                        <li>{{ $project->name }}</li>
                    </ul>
                </div>
                <div>
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" class="w-8 cursor-pointer" onclick="location.href='{{ url('dashboard') }}?projectId={{ $project->id }}'"><title>Estadísticas de este proyecto</title><path d="M17.45,15.18L22,7.31V19L22,21H2V3H4V15.54L9.5,6L16,9.78L20.24,2.45L21.97,3.45L16.74,12.5L10.23,8.75L4.31,19H6.57L10.96,11.44L17.45,15.18Z" /></svg>
                </div>
            </div>
            <hr />
            <dl class="grid grid-cols-4 gap-8 p-4 mx-auto text-gray-900 sm:p-8">
                <div class="flex flex-col items-center justify-center">
                    <dt class="mb-2 text-3xl font-extrabold">{{ date("d-m-y", strtotime($project->inicia)) }}</dt>
                    <dd class="text-gray-500">Fecha de inicio</dd>
                </div>
                <div class="flex flex-col items-center justify-center">
                    <dt class="mb-2 text-3xl font-extrabold">{{ $project->semanas }}</dt>
                    <dd class="text-gray-500">Semanas</dd>
                </div>
                <div class="flex flex-col items-center justify-center">
                    <dt class="mb-2 text-3xl font-extrabold">
                        {{ date("d-m-y", strtotime($project->inicia."+ ".$project->semanas." week")) }}
                    </dt>
                    <dd class="text-gray-500">Fecha final</dd>
                </div>
                <div class="flex flex-col items-center justify-center">
                    <dt class="mb-2 text-3xl font-extrabold">
                        @php
                            $startDate = new DateTime($project->inicia);
                            $endDate = new DateTime();

                            $diff = $endDate->diff($startDate);
                            $numberOfWeeks  =   floor($diff->days / 7);
                            $weekActual     =   $numberOfWeeks+1;
                            if($weekActual >= $project->semanas)
                                $weekActual     =   $project->semanas;

                            echo $weekActual
                        @endphp
                    </dt>
                    <dd class="text-gray-500">Semana actual</dd>
                </div>
            </dl>

            <div class="overflow-x-auto">
                <div class="w-full">
                    <div>
                        <ul class="menu menu-xs bg-primary rounded-lg max-w-full w-full">
                            @foreach ($dirs as $dir)
                                @if ($dir->route == 0 && $dir->link == 0)
                                    <li id="dir_{{ $dir->id }}" onclick="navigate('{{ $dir->name }}', {{ $dir->route }}, {{ $dir->id }})">
                                        <details>
                                            <summary>
                                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12.75V12A2.25 2.25 0 014.5 9.75h15A2.25 2.25 0 0121.75 12v.75m-8.69-6.44l-2.12-2.12a1.5 1.5 0 00-1.061-.44H4.5A2.25 2.25 0 002.25 6v12a2.25 2.25 0 002.25 2.25h15A2.25 2.25 0 0021.75 18V9a2.25 2.25 0 00-2.25-2.25h-5.379a1.5 1.5 0 01-1.06-.44z" />
                                                </svg>
                                                {{ $dir->name }}
                                            </summary>
                                        </details>
                                    </li>
                                @endif
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <input type="checkbox" id="modal_add_file" class="modal-toggle" />
    <div class="modal">
        <div class="modal-box">
            <h3 class="font-bold text-lg">Añadir archivo</h3>
            <form action="{{ route('navigate-add-file') }}" method="POST" id="formAddFile" class="py-4" enctype="multipart/form-data">
                @csrf
                <div class="form-control">
                    <label class="label">
                        <span class="label-text">Nombre <i class="text-red-500">*</i></span>
                    </label> 
                    <input type="text" name="name" class="input input-bordered" autocomplete="off" required>
                </div>
                <div class="form-control">
                    <label class="label">
                        <span class="label-text">Semana <i class="text-red-500">*</i></span>
                    </label> 
                    <select name="file_week" class="select select-bordered w-full" id="addFileWeek" required></select>
                </div>
                <div class="form-control">
                    <label class="label">
                        <span class="label-text">Archivo <i class="text-red-500">*</i></span>
                    </label> 
                    <input type="file" name="file" class="file-input file-input-success" autocomplete="off" required>
                </div>
                <input type="hidden" name="projectId" value="{{ $project->id }}">
                <input type="hidden" name="route" id="addFileRoute">
                <input type="hidden" name="link" id="addFileLink">
                <button type="submit" class="btn btn-primary btn-sm mt-10 mb-2 float-right w-[50%]">Añadir</button>
            </form>
        </div>
        <label class="modal-backdrop" for="modal_add_file">Close</label>
    </div>
    <input type="checkbox" id="modal_add_dir" class="modal-toggle" />
    <div class="modal">
        <div class="modal-box">
            <h3 class="font-bold text-lg">Crear carpeta</h3>
            <form action="{{ route('navigate-add-dir') }}" method="POST" id="formAddDir" class="py-4">
                @csrf
                <div class="form-control">
                    <label class="label">
                        <span class="label-text">Nombre <i class="text-red-500">*</i></span>
                    </label> 
                    <input type="text" name="name" class="input input-bordered" autocomplete="off" required>
                </div>
                <div class="form-control">
                    <label class="label">
                        <span class="label-text">Semanas <i class="text-red-500">*</i></span>
                    </label> 
                    <input type="text" name="semanas" value="1" class="input input-bordered" autocomplete="off" required>
                </div>
                <input type="hidden" name="projectId" value="{{ $project->id }}">
                <input type="hidden" name="route" id="addDirRoute">
                <input type="hidden" name="link" id="addDirLink">
                <button type="submit" class="btn btn-primary btn-sm mt-10 mb-2 float-right w-[50%]">Añadir</button>
            </form>
        </div>
        <label class="modal-backdrop" for="modal_add_dir">Close</label>
    </div>
    <dialog id="modal_previewFile" class="modal">
        <div method="dialog" class="modal-box w-11/12 max-w-5xl">
            <h3 class="font-bold text-lg mb-3" id="preview_NameFile">Name file</h3>
            <div class="float-right -mt-10">
                <a download="#" href="#" title="Descargar" id="preview_DownloadFile">
                    <button class="btn btn-primary btn-xs">Descargar</button>
                </a>
                @if (Auth::user()->access == 'a')
                    <button class="btn btn-warning btn-xs">Eliminar</button>
                @endif
            </div>
            <hr />
            <div class="w-full h-full mt-3">
                <img src="#" alt="preview" id="preview_Image">
                <iframe src="#" id="preview_UrlFile" id="previewFile" class="mx-auto w-full min-h-[800px]" frameborder="0"></iframe>
            </div>
        </div>
        <form method="dialog" class="modal-backdrop">
            <button>close</button>
        </form>
    </dialog>
    <dialog id="modal_managerCronograma" class="modal">
        <div method="dialog" class="modal-box min-w-[1200px]">
            <h3 class="font-bold text-lg mb-3">Cronograma de pagos</h3>
            <hr />
            <div class="float-right mt-2 mr-1">
                <span class="btn btn-xs btn-success" onclick="addInputCronograma()">Añadir</span>
            </div>
            <div class="w-full h-full mt-12">
                <form action="{{ route('update-cronograma') }}" method="POST" id="inputCronograma">
                    @csrf
                    @foreach ($cronogramas as $item)
                        <div class="grid grid-cols-6 gap-4">
                            <div class="form-control">
                                <label class="label">
                                    <span class="label-text">N° Factura <i class="text-red-500">*</i></span>
                                </label> 
                                <input type="text" value="{{ $item->n_factura }}" name="numero_factura[]" class="input input-bordered" autocomplete="off" required>
                            </div>
                            <div class="form-control">
                                <label class="label">
                                    <span class="label-text">Fecha Factura <i class="text-red-500">*</i></span>
                                </label> 
                                <input type="date" value="{{ $item->fecha_factura }}" name="fecha_factura[]" class="input input-bordered" autocomplete="off" required>
                            </div>
                            <div class="form-control">
                                <label class="label">
                                    <span class="label-text">Fecha vencimiento <i class="text-red-500">*</i></span>
                                </label> 
                                <input type="date" value="{{ $item->fecha_vencimiento }}" name="fecha_vencimiento[]" class="input input-bordered" autocomplete="off" required>
                            </div>
                            <div class="form-control">
                                <label class="label">
                                    <span class="label-text">Fecha pago real</span>
                                </label> 
                                <input type="date" value="{{ $item->fecha_pagoreal }}" name="fecha_pagoreal[]" class="input input-bordered" autocomplete="off" required>
                            </div>
                            <div class="form-control">
                                <label class="label">
                                    <span class="label-text">Moneda <i class="text-red-500">*</i></span>
                                </label> 
                                <input type="text" value="{{ $item->moneda }}" name="moneda[]" class="input input-bordered" autocomplete="off" required>
                            </div>
                            <div class="form-control">
                                <label class="label">
                                    <span class="label-text">Monto <i class="text-red-500">*</i></span>
                                    {{-- <span class="btn btn-xs btn-error float-right -mt-1" onclick="removeInputCronograma()">x</span> --}}
                                </label> 
                                <input type="number" value="{{ $item->monto }}" name="monto[]" class="input input-bordered" autocomplete="off" required>
                            </div>
                        </div>
                    @endforeach
                    <input type="hidden" name="projectId" value="{{ $project->id }}">
                </form>
                <button type="button" class="btn btn-success btn-sm mt-4" onclick="submitCronograma()">Guardar cronograma</button>
            </div>
        </div>
        <form method="dialog" class="modal-backdrop">
            <button>close</button>
        </form>
    </dialog>
    <!-- cronograma form input --> 
    <div class="grid grid-cols-6 gap-4 hidden" id="clonateCronograma">
        <div class="form-control">
            <label class="label">
                <span class="label-text">N° Factura <i class="text-red-500">*</i></span>
            </label> 
            <input type="text" name="numero_factura[]" class="input input-bordered" autocomplete="off" required>
        </div>
        <div class="form-control">
            <label class="label">
                <span class="label-text">Fecha Factura <i class="text-red-500">*</i></span>
            </label> 
            <input type="date" name="fecha_factura[]" class="input input-bordered" autocomplete="off" required>
        </div>
        <div class="form-control">
            <label class="label">
                <span class="label-text">Fecha vencimiento <i class="text-red-500">*</i></span>
            </label> 
            <input type="date" name="fecha_vencimiento[]" class="input input-bordered" autocomplete="off" required>
        </div>
        <div class="form-control">
            <label class="label">
                <span class="label-text">Fecha pago real</span>
            </label> 
            <input type="date" name="fecha_pagoreal[]" class="input input-bordered" autocomplete="off" required>
        </div>
        <div class="form-control">
            <label class="label">
                <span class="label-text">Moneda <i class="text-red-500">*</i></span>
            </label> 
            <input type="text" name="moneda[]" class="input input-bordered" autocomplete="off" required>
        </div>
        <div class="form-control">
            <label class="label">
                <span class="label-text">Monto <i class="text-red-500">*</i></span>
                {{-- <span class="btn btn-xs btn-error float-right -mt-1" onclick="removeInputCronograma()">x</span> --}}
            </label> 
            <input type="number" name="monto[]" class="input input-bordered" autocomplete="off" required>
        </div>
    </div>
    <script>
        let sendingRequest      =   false;
        let fileActualPreview   =   'image';
        $("#preview_UrlFile").hide();

        function navigate(name, route, id, weekFrom = 0, weekTo = 0) {
            const detailsId =   "#dir_"+id+" details"
            const url       =   '{{ url("/projects/navigate") }}?projectId={{ $project->id }}&route='+route+'&link='+id;
            const isOpen    =   $(detailsId).attr('open');

            if (typeof isOpen !== 'undefined' && isOpen !== false) {
                return false;
            }

            axios.get(url)
                .then(function(res) {
                    if(res.status == 200) {
                        const data  =   res.data;
                        if($(detailsId+" ul").length) 
                            $(detailsId+" ul").remove();
                        let prepareAppend       =   '<ul id="dir_id_'+id+'">';
                        if(name == 'Cronograma de pagos')
                            prepareAppend   +=  '<li onclick="managerCronograma();" class="ml-1 mr-3 p-1 rounded-lg cursor-pointer text-xs">Gestionar cronograma</li>';
                        else
                            prepareAppend   +=  '--- <span onclick="openModalAddDir('+route+', '+id+');" class="ml-1 mr-3 p-1 rounded-lg cursor-pointer text-xs">Crear carpeta</span> • <span onclick="openModalAddFile('+route+', '+id+', '+weekFrom+', '+weekTo+')" class="p-1 rounded-lg ml-3 text-xs cursor-pointer">Subir archivo</span>';
                        if(data.length > 0) {
                            for(var i = 0; i < data.length; i++) {
                                if(data[i].type == 'directory') {
                                    if({{ $weekActual }} > data[i].week_from)
                                        prepareAppend   +=      '<li id="dir_'+data[i].id+'" class="text-red-600" onclick="navigate('+data[i].route+', '+data[i].id+')">';
                                    else
                                        prepareAppend   +=      '<li id="dir_'+data[i].id+'" onclick="navigate(\''+data[i].name+'\', '+data[i].route+', '+data[i].id+', '+data[i].week_from+', '+data[i].week_to+')">';
                                    prepareAppend   +=      '<details><summary><svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12.75V12A2.25 2.25 0 014.5 9.75h15A2.25 2.25 0 0121.75 12v.75m-8.69-6.44l-2.12-2.12a1.5 1.5 0 00-1.061-.44H4.5A2.25 2.25 0 002.25 6v12a2.25 2.25 0 002.25 2.25h15A2.25 2.25 0 0021.75 18V9a2.25 2.25 0 00-2.25-2.25h-5.379a1.5 1.5 0 01-1.06-.44z" /></svg>';
                                    if(data[i].week_to == 0)
                                        prepareAppend   +=      data[i].name+' [ '+data[i].week_from+' ]';
                                    else
                                        prepareAppend   +=      data[i].name+' [ '+data[i].week_from+'-'+data[i].week_to+' ]';
                                    prepareAppend   +=      '</summary></details></li>';
                                }
                            }
                            for(var i = 0; i < data.length; i++) {
                                if(data[i].type != 'directory') {
                                    prepareAppend   +=  '<li><a href="javascript:void(0);" onclick="openModalPreviewFile(\''+data[i].name+'\', \''+data[i].file_path+'\', \''+data[i].type+'\')">';
                                    if(data.type !== 'image')
                                        prepareAppend   +=  '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m2.25 0H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" /></svg>';
                                    else
                                        prepareAppend   +=  '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 15.75l5.159-5.159a2.25 2.25 0 013.182 0l5.159 5.159m-1.5-1.5l1.409-1.409a2.25 2.25 0 013.182 0l2.909 2.909m-18 3.75h16.5a1.5 1.5 0 001.5-1.5V6a1.5 1.5 0 00-1.5-1.5H3.75A1.5 1.5 0 002.25 6v12a1.5 1.5 0 001.5 1.5zm10.5-11.25h.008v.008h-.008V8.25zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0z" /></svg>';
                                    prepareAppend   +=  data[i].name+'.'+data[i].file_ext;
                                    prepareAppend   +=  '</a></li>'
                                }
                            }
                        }
                        // else {
                        //     prepareAppend   +=  '<li class="text-xs">- Vacío</li>';
                        // }
                        prepareAppend   +=   '</ul>';
                        $(detailsId).append(prepareAppend);
                    }
                    else {
                        alert('No se logro abrir el directorio, intente de nuevo.');
                        let isOpen   =   $("#dir_"+id+" details").removeAttr('open');
                    }
                })
                .catch(function(err) {
                    console.log(err);
                    alert('No se logro abrir el directorio, intente de nuevo.');
                    let isOpen   =   $("#dir_"+id+" details").removeAttr('open');
                });
        }
        function openModalAddFile(route, id, weekFrom, weekTo) {
            $("#addFileRoute").val((route+1));
            $("#addFileLink").val(id);
            let selectWeek  =   '<option value="" disabled selected>Elige una semana</option>';
            if(weekTo == 0) {
                if(weekFrom > 0)
                    selectWeek  +=  '<option value="'+weekFrom+'">'+weekFrom+'</option>';
            }
            else {
                for(weekFrom; weekFrom <= weekTo; weekFrom++)
                    selectWeek  +=  '<option value="'+weekFrom+'">'+weekFrom+'</option>'
            }
            $("#addFileWeek").html(selectWeek);
            document.getElementById('modal_add_file').checked = true;
        }
        function openModalAddDir(route, id) {
            // modal_add_dir.showModal();
            $("#addDirRoute").val((route+1));
            $("#addDirLink").val(id);
            document.getElementById('modal_add_dir').checked = true;
        }
        function openModalPreviewFile(name, file_path, type) {
            $("#preview_NameFile").html(name);
            $("#preview_DownloadFile").attr('href', '{{ URL("storage/plexus") }}/'+file_path);
            $("#preview_DownloadFile").attr('download', file_path);
            
            if(type == 'image')
                $("#preview_Image").attr('src', '{{ URL("storage/plexus") }}/'+file_path);
            else
                $("#preview_UrlFile").attr('src', 'https://docs.google.com/gview?url={{ URL("storage/plexus") }}/'+file_path+'&embedded=true') 
            if(fileActualPreview != type) {
                if(fileActualPreview == 'docs' && type == 'image') {
                    $("#preview_UrlFile").hide();
                    $("#preview_Image").show();
                }
                if(fileActualPreview == 'image' && type == 'docs') {
                    $("#preview_Image").hide();
                    $("#preview_UrlFile").show();
                }
            }
            fileActualPreview   =   type;
            modal_previewFile.showModal();
        }
        function managerCronograma() {
            modal_managerCronograma.showModal();
        }
        function addInputCronograma() {
            const modelInput    =   document.getElementById('clonateCronograma');
            const getForm       =   document.getElementById('inputCronograma');
            let newInput    =   getForm.appendChild(modelInput.cloneNode(true));
            newInput.classList.remove('hidden');
        }
        function submitCronograma() {
            $("#inputCronograma").submit();
        }

        $("#formAddDir").submit(function(e){
            e.preventDefault();
            const data  =   new FormData(this);
            axios.post('{{ route("navigate-add-dir") }}', data)
                .then(function(res) {
                    if(res.status == 200)
                    {
                        const data  =   res.data;
                        let prepareAppend   =   '';
                        if({{ $weekActual }} > data.week_from)
                            prepareAppend   +=      '<li id="dir_'+data.id+'" class="text-red-600" onclick="navigate('+data.route+', '+data.id+')">';
                        else
                            prepareAppend   +=      '<li id="dir_'+data.id+'" onclick="navigate('+data.route+', '+data.id+', '+data.week_from+', '+data.week_to+')">';
                        prepareAppend   +=      '<details><summary><svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12.75V12A2.25 2.25 0 014.5 9.75h15A2.25 2.25 0 0121.75 12v.75m-8.69-6.44l-2.12-2.12a1.5 1.5 0 00-1.061-.44H4.5A2.25 2.25 0 002.25 6v12a2.25 2.25 0 002.25 2.25h15A2.25 2.25 0 0021.75 18V9a2.25 2.25 0 00-2.25-2.25h-5.379a1.5 1.5 0 01-1.06-.44z" /></svg>';
                        if(data.week_to == 0)
                            prepareAppend   +=      data.name+' [ '+data.week_from+' ]';
                        else
                            prepareAppend   +=      data.name+' [ '+data.week_from+'-'+data.week_to+' ]';
                        prepareAppend   +=      '</summary></details></li>';
                        $("#dir_id_"+data.link).append(prepareAppend);
                        document.getElementById('modal_add_dir').checked = false;
                    }
                })
                .catch(function(err) {
                    console.log(err);
                });
        });

        $("#formAddFile").submit(function(e) {
            e.preventDefault();
            const data      =   new FormData(this);
            const config    =   {
                headers: {
                    'content-type': 'multipart/form-data'
                }
            }
            axios.post('{{ route("navigate-add-file") }}', data, config)
                .then(function(res) {
                    if(res.status == 200)
                    {
                        const data  =   res.data;
                        let prepareAppend   =  '<li><a href="javascript:void(0);" onclick="openModalPreviewFile(\''+data.name+'\', \''+data.file_path+'\', \''+data.type+'\')">';
                        if(data.type !== 'image')
                            prepareAppend   +=  '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m2.25 0H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" /></svg>';
                        else
                            prepareAppend   +=  '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 15.75l5.159-5.159a2.25 2.25 0 013.182 0l5.159 5.159m-1.5-1.5l1.409-1.409a2.25 2.25 0 013.182 0l2.909 2.909m-18 3.75h16.5a1.5 1.5 0 001.5-1.5V6a1.5 1.5 0 00-1.5-1.5H3.75A1.5 1.5 0 002.25 6v12a1.5 1.5 0 001.5 1.5zm10.5-11.25h.008v.008h-.008V8.25zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0z" /></svg>';
                        prepareAppend   +=  data.name+'.'+data.file_ext;
                        prepareAppend   +=  '</a></li>'

                        $("#dir_id_"+data.link).append(prepareAppend);
                        document.getElementById('modal_add_file').checked = false;
                    }
                })
                .catch(function(err) {
                    console.log(err);
                });
        });
    </script>
@endsection