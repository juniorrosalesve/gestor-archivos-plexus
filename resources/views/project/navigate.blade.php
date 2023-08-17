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
                <div class="-mt-3">
                    <p class="text-xs text-center">Progreso {{ $porcentaje }}%</p>
                    <progress class="progress @if($porcentaje == 100) progress-success @else progress-accent @endif w-56" value="{{ $porcentaje }}" max="100"></progress>
                </div>
            </div>
            <hr />
            <div class="overflow-x-auto mt-2">
                <div class="w-full">
                    <div>
                        <ul class="menu menu-xs bg-primary rounded-lg max-w-full w-full">
                            @foreach ($dirs as $dir)
                                @if ($dir->route == 0 && $dir->link == 0)
                                    <li id="dir_{{ $dir->id }}" onclick="navigate({{ $dir->route }}, {{ $dir->id }})">
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
    <dialog id="modal_add_file" class="modal">
        <div method="dialog" class="modal-box">
            <h3 class="font-bold text-lg">Añadir archivo</h3>
            <form action="{{ route('navigate-add-file') }}" method="POST" id="formAddFile" class="py-4" enctype="multipart/form-data">
                @csrf
                <div class="form-control">
                    <label class="label">
                        <span class="label-text">Nombre <i class="text-red-500">*</i></span>
                    </label> 
                    <input type="text" name="name" class="input input-bordered" autocomplete="off" required>
                </div>
                {{-- <div class="form-control">
                    <label class="label">
                        <span class="label-text">Fecha de entrega (opcional)</span>
                    </label> 
                    <input type="date" name="delivery" class="input input-bordered" autocomplete="off">
                </div> --}}
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
        <form method="dialog" class="modal-backdrop">
            <button>close</button>
        </form>
    </dialog>
    <dialog id="modal_add_dir" class="modal">
        <div method="dialog" class="modal-box">
            <h3 class="font-bold text-lg">Añadir archivo</h3>
            <form action="{{ route('navigate-add-dir') }}" method="POST" class="py-4">
                @csrf
                <div class="form-control">
                    <label class="label">
                        <span class="label-text">Nombre <i class="text-red-500">*</i></span>
                    </label> 
                    <input type="text" name="name" class="input input-bordered" autocomplete="off" required>
                </div>
                <div class="form-control">
                    <label class="label">
                        <span class="label-text">Fecha de entrega (opcional)</span>
                    </label> 
                    <input type="date" name="delivery" class="input input-bordered" autocomplete="off">
                </div>
                <input type="hidden" name="projectId" value="{{ $project->id }}">
                <input type="hidden" name="route" id="addDirRoute">
                <input type="hidden" name="link" id="addDirLink">
                <button type="submit" class="btn btn-primary btn-sm mt-10 mb-2 float-right w-[50%]">Añadir</button>
            </form>
        </div>
        <form method="dialog" class="modal-backdrop">
            <button>close</button>
        </form>
    </dialog>
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
    <div id="contextMenu" class="context-menu" style="display: none"> 
        <ul class="menu"> 
            <li class="share"><a href="#"><i class="fa fa-share" aria-hidden="true"></i> Nueva carpeta</a></li> 
            <li class="rename"><a href="#"><i class="fa fa-pencil" aria-hidden="true"></i> Subir archivo</a></li> 
            <li class="link"><a href="#"><i class="fa fa-link" aria-hidden="true"></i> Previsualizar</a></li> 
            <li class="download"><a href="#"><i class="fa fa-download" aria-hidden="true"></i> Descargar</a></li> 
            <li class="trash"><a href="#"><i class="fa fa-trash" aria-hidden="true"></i> Eliminar</a></li> 
        </ul> 
    </div> 
    <script>
        let sendingRequest      =   false;
        let fileActualPreview   =   'image';
        $("#preview_UrlFile").hide();

        function navigate(route, id) {
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
                        if($(detailsId+" ul").length) {
                            $(detailsId+" ul").remove();
                            console.log('debería eliminar');
                        }
                        let prepareAppend   =   '<ul>';
                        prepareAppend   +=  '--- <span onclick="openModalAddDir('+route+', '+id+');" class="ml-1 mr-3 p-1 rounded-lg cursor-pointer text-xs">Crear carpeta</span> • <span onclick="openModalAddFile('+route+', '+id+')" class="p-1 rounded-lg ml-3 text-xs cursor-pointer">Subir archivo</span>';
                        if(data.length > 0) {
                            for(var i = 0; i < data.length; i++) {
                                if(data[i].type == 'directory') {
                                    console.log(data[i]);
                                    prepareAppend   +=      '<li id="dir_'+data[i].id+'" onclick="navigate('+data[i].route+', '+data[i].id+')">';
                                    prepareAppend   +=      '<details><summary><svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12.75V12A2.25 2.25 0 014.5 9.75h15A2.25 2.25 0 0121.75 12v.75m-8.69-6.44l-2.12-2.12a1.5 1.5 0 00-1.061-.44H4.5A2.25 2.25 0 002.25 6v12a2.25 2.25 0 002.25 2.25h15A2.25 2.25 0 0021.75 18V9a2.25 2.25 0 00-2.25-2.25h-5.379a1.5 1.5 0 01-1.06-.44z" /></svg>';
                                    if(data[i].required > 0)
                                        prepareAppend   +=      data[i].name+' ('+data[i].registers+'/'+data[i].required+')</summary></details></li>';
                                    else
                                        prepareAppend   +=      data[i].name+'</summary></details></li>';
                                }
                            }
                            for(var i = 0; i < data.length; i++) {
                                if(data[i].type != 'directory') {
                                    // prepareAppend   +=  '<li><a href="{{ url("/storage/plexus") }}/'+data[i].file_path+'" target="_blank">';
                                    prepareAppend   +=  '<li><a href="javascript:void(0);" onclick="openModalPreviewFile(\''+data[i].name+'\', \''+data[i].file_path+'\', \''+data[i].type+'\')">';
                                    if(data[i].type == 'docs')
                                        prepareAppend   +=  '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m2.25 0H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" /></svg>';
                                    else
                                        prepareAppend   +=  '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 15.75l5.159-5.159a2.25 2.25 0 013.182 0l5.159 5.159m-1.5-1.5l1.409-1.409a2.25 2.25 0 013.182 0l2.909 2.909m-18 3.75h16.5a1.5 1.5 0 001.5-1.5V6a1.5 1.5 0 00-1.5-1.5H3.75A1.5 1.5 0 002.25 6v12a1.5 1.5 0 001.5 1.5zm10.5-11.25h.008v.008h-.008V8.25zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0z" /></svg>';
                                    prepareAppend   +=  data[i].name+'.'+data[i].file_ext;
                                    prepareAppend   +=  '</a></li>'
                                }
                            }
                        }
                        else {
                            prepareAppend   +=  '<li class="text-xs">- Vacío</li>';
                        }
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
        function openModalAddFile(route, id) {
            modal_add_file.showModal();
            $("#addFileRoute").val((route+1));
            $("#addFileLink").val(id);
        }
        function openModalAddDir(route, id) {
            modal_add_dir.showModal();
            $("#addDirRoute").val((route+1));
            $("#addDirLink").val(id);
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
    </script>
@endsection