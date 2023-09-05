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
                    <a href="{{ url('dashboard') }}?projectId={{ $project->id }}">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" class="w-8 cursor-pointer inline-block mr-5"><title>Estadísticas de este proyecto</title><path d="M17.45,15.18L22,7.31V19L22,21H2V3H4V15.54L9.5,6L16,9.78L20.24,2.45L21.97,3.45L16.74,12.5L10.23,8.75L4.31,19H6.57L10.96,11.44L17.45,15.18Z" /></svg>
                    </a>
                    <a href="{{ route('edit-project', [
                            'projectId' => $project->id
                        ]) }}">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" class="w-8 cursor-pointer inline-block"><title>Editar Proyecto</title><path d="M4 18H12.13L11 19.13V20H4C2.9 20 2 19.11 2 18V6C2 4.89 2.89 4 4 4H10L12 6H20C21.1 6 22 6.89 22 8V10.15C21.74 10.06 21.46 10 21.17 10C20.75 10 20.36 10.11 20 10.3V8H4V18M22.85 13.47L21.53 12.15C21.33 11.95 21 11.95 20.81 12.15L19.83 13.13L21.87 15.17L22.85 14.19C23.05 14 23.05 13.67 22.85 13.47M13 19.96V22H15.04L21.17 15.88L19.13 13.83L13 19.96Z" /></svg>
                    </a>
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
                        @php
                            if(date('D', strtotime($project->inicia)) == 'Mon')
                                $realDate   =   date("d-m-y", strtotime($project->inicia."+ ".$project->semanas." week"." - 3 days"));
                            else
                                $realDate   =   date("d-m-y", strtotime($project->inicia."+ ".$project->semanas." week"));
                            $canAddFile     =   true;
                            $now    =   date('d-m-y');
                            if($now > $realDate && \Auth::user()->access != 'a')
                                $canAddFile =   false;
                        @endphp
                        {{ $realDate }}
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
                <div class="mt-5">
                    <h1 class="italic">Notas del proyecto</h1>
                    @if (!empty($project->notes))
                        <p class="bg-base-200 italic text-sm p-2 rounded">{{ $project->notes }}</p>
                    @else
                        <p class="bg-base-200 italic text-sm p-2 rounded text-center w-full">No hay notas pendientes.</p>
                    @endif
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
                    <input type="text" name="name" class="input input-bordered" id="inputNameAddFile" autocomplete="off" required>
                </div>
                <div class="form-control">
                    <label class="label">
                        <span class="label-text">Semana <i class="text-red-500">*</i></span>
                    </label> 
                    <select name="file_week" class="select select-bordered w-full" id="addFileWeek" required></select>
                </div>
                @if (Auth::user()->access == 'a')
                    <div class="form-control">
                        <label class="label">
                            <span class="label-text">Fecha <i class="text-red-500">*</i></span>
                        </label> 
                        <input type="date" name="created_at" class="input input-bordered" autocomplete="off" required>
                    </div>
                @endif
                <div class="form-control" id="addFileCronograma"></div>
                <div class="form-control">
                    <label class="label">
                        <span class="label-text">Archivo <i class="text-red-500">*</i></span>
                    </label> 
                    <input type="file" name="file" class="file-input file-input-success" id="inputFileAddFile" autocomplete="off" required>
                </div>
                <input type="hidden" name="created_by" value="{{ Auth::user()->name }}">
                <input type="hidden" name="projectId" value="{{ $project->id }}">
                <input type="hidden" name="route" id="addFileRoute">
                <input type="hidden" name="link" id="addFileLink">
                <button type="submit" class="btn btn-primary btn-sm mt-10 mb-2 float-right w-[50%]" id="btnAddFile">
                    <span class="loading loading-spinner hidden"></span> Añadir
                </button>
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
        <div method="dialog" class="modal-box w-11/12 max-w-5xl min-h-full">
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
                        <div class="grid grid-cols-6 gap-4 cron_query_{{ $item->id }}">
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
                                    <span class="btn btn-xs btn-error float-right -mt-1" onclick="removeInputCronograma('cron_query_{{ $item->id }}')">x</span>
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
    <dialog id="modal_showCronograma" class="modal">
        <div method="dialog" class="modal-box min-w-[1200px]">
            <h3 class="font-bold text-lg mb-3">Cronograma de pagos</h3>
            <hr />
            <div class="w-full mt-3">
                <table id="table">
                    <thead>
                        <tr>
                            <td>N° Factura</td>
                            <td>Fecha factura</td>
                            <td>Fecha vencimiento</td>
                            <td>Fecha pago real</td>
                            <td>Moneda</td>
                            <td>Monto</td>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $totalFacturado     =   0;
                            $totalPagado        =   0;
                            $porCobrar          =   0;
                            $now        =   date('Y-m-d');
                        @endphp
                        @foreach ($cronogramas as $item)
                            <tr @if($now > $item->fecha_vencimiento) style="background:rgb(252 165 165);" @endif>
                                <td>{{ $item->n_factura }}</td>
                                <td>{{ date('d-m-Y', strtotime($item->fecha_factura)) }}</td>
                                <td>{{ date('d-m-Y', strtotime($item->fecha_vencimiento)) }}</td>
                                @if ($item->fecha_pagoreal != null)
                                    <td>{{ date('d-m-Y', strtotime($item->fecha_pagoreal)) }}</td>
                                @else
                                    <td>-</td>
                                @endif
                                <td>{{ $item->moneda }}</td>
                                <td>{{ number_format($item->monto, 2, ',', '.') }}</td>
                            </tr>
                            @php
                                $totalFacturado     +=  $item->monto;
                                if($item->fecha_pagoreal != null)
                                    $totalPagado    +=  $item->monto;
                            @endphp
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr class="bg-gray-200">
                            <td colspan="4">Total facturado</td>
                            <td>{{ $cronogramas[0]->moneda }}</td>
                            <td>{{ number_format($totalFacturado, 2, ',', '.') }}</td>
                        </tr>
                        <tr class="bg-green-400">
                            <td colspan="4">Pagado</td>
                            <td>{{ $cronogramas[0]->moneda }}</td>
                            <td>{{ number_format($totalPagado, 2, ',', '.') }}</td>
                        </tr>
                        <tr class="bg-yellow-200">
                            <td colspan="4">Por cobrar</td>
                            <td>{{ $cronogramas[0]->moneda }}</td>
                            <td>{{ number_format(($totalFacturado-$totalPagado), 2, ',', '.') }}</td>
                        </tr>
                    </tfoot>
                </table>
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
    </div>
    <script>
        const cronogramas   =   @json($cronogramas);
        let sendingRequest      =   false;
        let fileActualPreview   =   'image';
        let iCrono  =   1;
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
                        if(name == 'Cronograma de pagos') {
                            prepareAppend   +=  '<li><a href="javascript:void(0);" onclick="managerCronograma()">Gestionar cronograma</li>';
                            prepareAppend   +=  '<li><a href="javascript:void(0);" onclick="showCronograma()">Mostrar cronograma</li>';
                        }
                        else {
                            // prepareAppend   +=  '--- <span onclick="openModalAddDir('+route+', '+id+');" class="ml-1 mr-3 p-1 rounded-lg cursor-pointer text-xs">Crear carpeta</span> • <span onclick="openModalAddFile('+route+', '+id+', '+weekFrom+', '+weekTo+')" class="p-1 rounded-lg ml-3 text-xs cursor-pointer">Subir archivo</span>';
                            @if($canAddFile)
                                prepareAppend   +=  '--- <span onclick="openModalAddFile(\''+name+'\', '+route+', '+id+', '+weekFrom+', '+weekTo+')" class="p-1 rounded-lg text-xs cursor-pointer">Subir archivo</span>';
                            @endif
                        }
                        if(data.length > 0) {
                            for(var i = 0; i < data.length; i++) {
                                if(data[i].type == 'directory') {
                                    // if({{ $weekActual }} > data[i].week_from)
                                    //     prepareAppend   +=      '<li id="dir_'+data[i].id+'" class="text-red-600" onclick="navigate(\''+data[i].name+'\', '+data[i].route+', '+data[i].id+')">';
                                    // else
                                    prepareAppend   +=      '<li id="dir_'+data[i].id+'" onclick="navigate(\''+data[i].name+'\', '+data[i].route+', '+data[i].id+', '+data[i].week_from+', '+data[i].week_to+')">';
                                    prepareAppend   +=      '<details><summary><svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12.75V12A2.25 2.25 0 014.5 9.75h15A2.25 2.25 0 0121.75 12v.75m-8.69-6.44l-2.12-2.12a1.5 1.5 0 00-1.061-.44H4.5A2.25 2.25 0 002.25 6v12a2.25 2.25 0 002.25 2.25h15A2.25 2.25 0 0021.75 18V9a2.25 2.25 0 00-2.25-2.25h-5.379a1.5 1.5 0 01-1.06-.44z" /></svg>';
                                    if(data[i].week_to == 0)
                                        prepareAppend   +=      data[i].name+' [ '+data[i].week_from+' ]';
                                    else
                                        prepareAppend   +=      data[i].name+' [ '+data[i].week_from+'-'+data[i].week_to+' ]';
                                    prepareAppend   +=  '<span>'+data[i].count+'</span>';
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
                                    if(data[i].alert)
                                        prepareAppend   +=  '<span class="float-right text-xs italic text-yellow-600">'+data[i].created_by+' | '+data[i].created_atFormat+' | ['+data[i].file_week+']</span>'
                                    else
                                        prepareAppend   +=  '<span class="float-right text-xs italic">'+data[i].created_by+' | '+data[i].created_atFormat+' | ['+data[i].file_week+']</span>'
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
        function openModalAddFile(name, route, id, weekFrom, weekTo) {
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
            if(name == 'Ficha de depósito / SWIFT')
            {
                cronogramaInput     =  '<label class="label"><span class="label-text">Cronograma <i class="text-red-500">*</i></span></label>';
                cronogramaInput     +=  '<select name="cronograma" class="select select-bordered w-full" required>';
                cronogramaInput     +=  '<option value="" selected>Selecciona una factura</option>';
                for(var i = 0; i < cronogramas.length; i++) 
                    cronogramaInput     +=  '<option value="'+cronogramas[i].id+'">'+cronogramas[i].n_factura+'</option>';
                cronogramaInput     +=  '</select>';
                $("#addFileCronograma").html(cronogramaInput);
            }
            else
                $("#addFileCronograma").html("");
            $("#inputNameAddFile").val("");
            $("#inputFileAddFile").val("");
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
        function showCronograma() {
            modal_showCronograma.showModal();
        }
        function addInputCronograma() {
            const modelInput    =   document.getElementById('clonateCronograma');
            const getForm       =   document.getElementById('inputCronograma');
            let newInput    =   getForm.appendChild(modelInput.cloneNode(true));
            newInput.removeAttribute('id');
            newInput.classList.remove('hidden');
            let prepareExtra    =   '<div class="form-control"><label class="label"><span class="label-text">Monto <i class="text-red-500">*</i></span>'
            prepareExtra    +=  '<span class="btn btn-xs btn-error float-right -mt-1" onclick="removeInputCronograma(\'cron_view_'+iCrono+'\')">x</span>';
            prepareExtra    +=  '</label><input type="number" name="monto[]" class="input input-bordered" autocomplete="off" required></div>'
            $(newInput).append(prepareExtra);
            $(newInput).addClass('cron_view_'+iCrono);
            iCrono++;
        }
        function removeInputCronograma(iclass) {
            $('.'+iclass).remove();
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
            const data          =   new FormData(this);
            const button        =   $("#btnAddFile");
            const buttonSpan    =   $("#btnAddFile span");
            const config    =   {
                headers: {
                    'content-type': 'multipart/form-data'
                }
            }
            button.prop('disabled', true);
            buttonSpan.removeClass("hidden");
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
                    else
                        alert('No se logro añadir el archivo, intente de nuevo.');
                    buttonSpan.addClass("hidden");
                    button.prop('disabled', false);
                })
                .catch(function(err) {
                    console.log(err);
                    alert('No se logro añadir el archivo, intente de nuevo.');
                    buttonSpan.addClass("hidden");
                    button.prop('disabled', false);
                });
        });
    </script>
@endsection