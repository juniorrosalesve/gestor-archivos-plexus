@extends('layouts.app')

@section('body')
    <div class="card shadow bordered bg-base-100">
        <div class="card-body">
            <div class="flex justify-between my-4">
                <div>
                    <label for="my-drawer-2" class="my-auto btn btn-primary btn-sm drawer-button">Panel</label> 
                    <h2 class="text-2xl inline-block my-auto ml-3">Dashboard</h2>
                </div>
                <div>
                    <select class="select select-bordered" id="eligeRegion" onchange="selectRegion(this)">
                        @if ($region != null)
                            <option value="0">Todas las regiones</option>
                            <option value="{{ $region->id }}" selected>{{ $region->name }} - {{ $region->user->name }}</option>
                        @else
                            <option value="0" selected>Todas las regiones</option>
                        @endif
                        @foreach ($regions as $item)
                            @if ($region != null)
                                @if ($region->id == $item->id)
                                    @continue
                                @endif
                            @endif
                            <option value="{{ $item->id }}">{{ $item->name }} - {{ $item->user->name }}</option>
                        @endforeach
                    </select>
                    <select class="select select-bordered ml-3" id="eligePais" onchange="selectCountry(this)">
                        @if($region != null)
                            @if ($country != null)
                                <option value="0">Todos los paises</option>
                                <option value="{{ $country->id }}" selected>{{ $country->name }}</option>
                            @else
                                <option value="0" selected>Todos los paises</option>
                            @endif
                            @foreach ($region->countries as $item)
                                @if ($country != null)
                                    @if ($country->id == $item->id)
                                        @continue
                                    @endif
                                @endif
                                <option value="{{ $item->id }}">{{ $item->name }}</option>
                            @endforeach
                        @else
                            <option value="0">Todos los paises</option>
                        @endif
                    </select>
                    <select class="select select-bordered mx-3" id="eligeProyecto">
                        @if ($chartProject)
                            <option value="0">Todos los proyectos</option>
                            <option value="{{ $chartProjectData->id }}" selected>{{ $chartProjectData->name }}</option>
                        @else 
                            @if ($region != null && $country != null)
                                @if (!$chartProject)
                                    <option value="0" selected>Todos los proyectos</option>
                                @endif
                                @foreach ($country->projects as $item)
                                    @if ($chartProject)
                                        @if ($chartProjectData->id == $item->id)
                                            @continue
                                        @endif
                                    @endif

                                    <option value="{{ $item->id }}">{{ $item->name }}</option>
                                @endforeach
                            @else
                               <option value="0">Todos los proyectos</option> 
                            @endif
                        @endif
                    </select>
                    <select class="select select-bordered mr-3" id="eligeYear">
                        @if ($filterByDate != null)
                            <option value="">Filtrar por año</option>
                            <option value="{{ $filterByDate }}" selected>{{ $filterByDate }}</option>
                            @foreach ($years as $item)
                                @if ($filterByDate != $item)
                                    <option value="{{ $item }}">{{ $item }}</option>
                                @endif
                            @endforeach
                        @else
                            <option value="" selected>Filtrar por año</option>
                            @foreach ($years as $item)
                                <option value="{{ $item }}">{{ $item }}</option>
                            @endforeach
                        @endif
                    </select>
                    <button type="button" class="btn btn-primary" onclick="onClickSearch()">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" class="w-6"><title>magnify</title><path d="M9.5,3A6.5,6.5 0 0,1 16,9.5C16,11.11 15.41,12.59 14.44,13.73L14.71,14H15.5L20.5,19L19,20.5L14,15.5V14.71L13.73,14.44C12.59,15.41 11.11,16 9.5,16A6.5,6.5 0 0,1 3,9.5A6.5,6.5 0 0,1 9.5,3M9.5,5C7,5 5,7 5,9.5C5,12 7,14 9.5,14C12,14 14,12 14,9.5C14,7 12,5 9.5,5Z" /></svg>
                    </button>
                </div>
            </div>
            <hr />
            <div class="overflow-x-auto mt-2">
                @if (!$chartProject)
                    <dl class="grid @if($region == null) grid-cols-4 @else @if($country == null) grid-cols-3 @else grid-cols-2 @endif @endif gap-8 p-4 mx-auto text-gray-900 sm:p-8">
                        @php
                            if($region == null)
                                $region = 0;
                            if($country == null) 
                                $country = 0;  
                        @endphp
                        <div class="flex flex-col items-center justify-center bg-base-200 p-3 rounded cursor-pointer" onclick="location.href='{{ route('dashboard-projects', [
                            'region' => $region,
                            'country' => $country
                        ]) }}'">
                            <dt class="mb-2 text-3xl font-extrabold">{{ sizeof($projects_opens) }}</dt>
                            <dd class="text-gray-500 text-center">Proyectos abiertos</dd>
                        </div>
                        @if ($region == null)
                            <div class="flex flex-col items-center justify-center bg-base-200 p-3 rounded cursor-pointer">
                                <dt class="mb-2 text-3xl font-extrabold">{{ sizeof($regions) }}</dt>
                                <dd class="text-gray-500 text-center">Total de regiones</dd>
                            </div>
                        @endif
                        @if ($country == null)
                            <div class="flex flex-col items-center justify-center bg-base-200 p-3 rounded cursor-pointer">
                                <dt class="mb-2 text-3xl font-extrabold">{{ sizeof($countries) }}</dt>
                                <dd class="text-gray-500 text-center">Total paises</dd>
                            </div>
                        @endif
                        <div class="flex flex-col items-center justify-center bg-base-200 p-3 rounded cursor-pointer" onclick="location.href='{{ route('allprojects') }}'">
                            <dt class="mb-2 text-3xl font-extrabold">{{ sizeof($projects) }}</dt>
                            <dd class="text-gray-500 text-center">Total proyectos</dd>
                        </div>
                    </dl>
                @endif
                <div class="mt-10">
                    <div class="grid grid-cols-2">
                        <div class="w-[350px] mx-auto my-auto">
                            <canvas id="cumplimiento_por_root"></canvas>
                        </div>
                        <div class="w-[600px] mx-auto my-auto" id="div_cumplimiento_por_root_1">
                            <div class="flex justify-around">
                                <h1 class="italic font-semibold">In time: {{ $financiera_chart["total_countOk"] }}</h1>
                                <h1 class="italic font-semibold">Out Time: {{ $financiera_chart["total_countOutTime"] }}</h1>
                                <h1 class="italic font-semibold">Undelivered: {{ $financiera_chart["total_countBad"] }}</h1>
                            </div>
                            <canvas id="cumplimiento_por_root_1" class="border-2 rounded"></canvas>
                        </div>
                        <div class="w-[600px] mx-auto my-auto hidden" id="div_cumplimiento_por_root_2">
                            <div class="flex justify-around">
                                <h1 class="italic font-semibold">In time: {{ $operativa_chart["total_countOk"] }}</h1>
                                <h1 class="italic font-semibold">Out Time: {{ $operativa_chart["total_countOutTime"] }}</h1>
                                <h1 class="italic font-semibold">Undelivered: {{ $operativa_chart["total_countBad"] }}</h1>
                            </div>
                            <canvas id="cumplimiento_por_root_2" class="border-2 rounded"></canvas>
                        </div>
                        <div class="w-[600px] mx-auto my-auto hidden" id="div_cumplimiento_por_root_3">
                            <div class="flex justify-around">
                                <h1 class="italic font-semibold">In time: {{ $estrategica_tactica_chart["total_countOk"] }}</h1>
                                <h1 class="italic font-semibold">Out Time: {{ $estrategica_tactica_chart["total_countOutTime"] }}</h1>
                                <h1 class="italic font-semibold">Undelivered: {{ $estrategica_tactica_chart["total_countBad"] }}</h1>
                            </div>
                            <canvas id="cumplimiento_por_root_3" class="border-2 rounded"></canvas>
                        </div>
                        <div class="w-[600px] mx-auto my-auto hidden" id="div_cumplimiento_por_root_4">
                            <div class="flex justify-around">
                                <h1 class="italic font-semibold">In time: {{ $gestion_humana_chart["total_countOk"] }}</h1>
                                <h1 class="italic font-semibold">Out Time: {{ $gestion_humana_chart["total_countOutTime"] }}</h1>
                                <h1 class="italic font-semibold">Undelivered: {{ $gestion_humana_chart["total_countBad"] }}</h1>
                            </div>
                            <canvas id="cumplimiento_por_root_4" class="border-2 rounded"></canvas>
                        </div>
                    </div>
                    <div class="w-full mx-auto" id="div_cumplimiento_por_subdirs_1">
                        <canvas id="cumplimiento_por_subdirs_1"></canvas>
                    </div>
                    <div class="w-full mx-auto hidden" id="div_cumplimiento_por_subdirs_2">
                        <canvas id="cumplimiento_por_subdirs_2"></canvas>
                    </div>
                    <div class="w-full mx-auto hidden" id="div_cumplimiento_por_subdirs_3">
                        <canvas id="cumplimiento_por_subdirs_3"></canvas>
                    </div>
                    <div class="w-full mx-auto hidden" id="div_cumplimiento_por_subdirs_4">
                        <canvas id="cumplimiento_por_subdirs_4"></canvas>
                    </div>
                </div>
                <hr style="margin-top:2rem;margin-bottom:2rem;" />
                <div>
                    @if (!$chartProject)
                        <div class="my-10 bg-base-200 p-2 rounded">
                            <h1 class="mb-3 text-lg">Proyectos en mora</h1>
                            <table id="table2">
                                <thead>
                                    <tr>
                                        <td>Nombre</td>
                                        <td>N° Factura</td>
                                        <td>Fecha factura</td>
                                        <td>Fecha vencimiento</td>
                                        <td>Moneda</td>
                                        <td>Monto</td>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($facturas_mora as $item)
                                        <tr>
                                            <td><a href="{{ route('project', [
                                                'regionId' => $item->project->regionId,
                                                'countryId' => $item->project->countryId,
                                                'projectId' => $item->project->id
                                            ]) }}" class="underline">{{ $item->project->name }}</a></td>
                                            <td>{{ $item->n_factura }}</td>
                                            <td class="bg-yellow-200">{{ date('d-m-Y', strtotime($item->fecha_factura)) }}</td>
                                            <td class="bg-red-200">{{ date('d-m-Y', strtotime($item->fecha_vencimiento)) }}</td>
                                            <td>{{ $item->moneda }}</td>
                                            <td>{{ number_format($item->monto, 2, ',', '.') }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else 
                        <div class="my-10 bg-base-200 p-2 rounded">
                            <h1 class="mb-3 text-lg">Facturas en mora</h1>
                            <table id="table2">
                                <thead>
                                    <tr>
                                        <td>N° Factura</td>
                                        <td>Fecha factura</td>
                                        <td>Fecha vencimiento</td>
                                        <td>Moneda</td>
                                        <td>Monto</td>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($facturas_mora as $item)
                                        <tr>
                                            <td>{{ $item->n_factura }}</td>
                                            <td class="bg-yellow-200">{{ date('d-m-Y', strtotime($item->fecha_factura)) }}</td>
                                            <td class="bg-red-200">{{ date('d-m-Y', strtotime($item->fecha_vencimiento)) }}</td>
                                            <td>{{ $item->moneda }}</td>
                                            <td>{{ number_format($item->monto, 2, ',', '.') }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                    <div class="my-3 bg-base-200 p-2 rounded">
                        <h1 class="mb-3 text-lg">Historial de facturas con pagos atrasados</h1>
                        <table id="table">
                            <thead>
                                <tr>
                                    @if (!$chartProject)
                                        <td>Nombre</td>
                                    @endif
                                    <td>N° Factura</td>
                                    <td>Fecha factura</td>
                                    <td>Fecha vencimiento</td>
                                    <td>Fecha pago real</td>
                                    <td>Moneda</td>
                                    <td>Monto</td>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($facturas_vencidas as $item)
                                    <tr>
                                        @if (!$chartProject)
                                            <td><a href="{{ route('project', [
                                                'regionId' => $item->project->regionId,
                                                'countryId' => $item->project->countryId,
                                                'projectId' => $item->project->id
                                            ]) }}" class="underline">{{ $item->project->name }}</a></td>
                                        @endif
                                        <td>{{ $item->n_factura }}</td>
                                        <td>{{ date('d-m-Y', strtotime($item->fecha_factura)) }}</td>
                                        <td class="bg-yellow-200">{{ date('d-m-Y', strtotime($item->fecha_vencimiento)) }}</td>
                                        <td class="bg-red-200">{{ date('d-m-Y', strtotime($item->fecha_pagoreal)) }}</td>
                                        <td>{{ $item->moneda }}</td>
                                        <td>{{ number_format($item->monto, 2, ',', '.') }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div> 
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <script>
        const categories    =   document.getElementById('cumplimiento_por_root');
        var barColors = [
            "rgb(147 197 253)",
            "rgb(156 163 175)",
            "rgb(254 240 138)",
            "rgb(134 239 172)"
        ];

        Chart.defaults.font.size = 12;

        new Chart("cumplimiento_por_root_1", {
            type: 'bar',
            data: {
                labels: ["In time", "Out time", "Undelivered"],
                datasets: [
                    {
                        label: 'Admin. / Financiera',
                        data: [{{ $financiera_chart['total_ok'] }}, {{ $financiera_chart["total_outTime"] }}, {{ $financiera_chart['total_bad'] }}],
                        backgroundColor:[barColors[0], "rgb(253 224 71)", "rgb(252 165 165)",],
                        borderWidth: 1
                    },
                ],
            },
            options: {
                scales: {
                    y: {
                        min: 0,
                        max: 100,
                        ticks: {
                            stepSize: 10,
                            callback: function(value, index, values) {
                                return value + " %";
                            }            
                        }
                    }
                }
            },
        });
        new Chart("cumplimiento_por_subdirs_1", {
            type: 'bar',
            data: {
                labels: @json($financiera_chart['keys']),
                datasets: [
                    {
                        label: 'In Time',
                        data: @json($financiera_chart['total_sub_ok']),
                        backgroundColor:"rgb(134 239 172)",
                        borderWidth: 1
                    },
                    {
                        label: 'Out Time',
                        data: @json($financiera_chart['total_sub_outTime']),
                        backgroundColor:"rgb(253 224 71)",
                        borderWidth: 1
                    },
                    {
                        label: 'Undelivered',
                        data: @json($financiera_chart['total_sub_bad']),
                        backgroundColor:"rgb(252 165 165)",
                        borderWidth: 1
                    },
                ]
            },
            options: {
                indexAxis: 'y',
                // Elements options apply to all of the options unless overridden in a dataset
                // In this case, we are setting the border of each horizontal bar to be 2px wide
                elements: {
                    bar: {
                        borderWidth: 3,
                    }
                },
                responsive: true,
                plugins: {
                    legend: {
                        position: 'right',
                    },
                    title: {
                        display: true,
                        text: 'Cumplimiento en % de la categoría FINANCIERA'
                    }
                },
                scales: {
                    x: {
                        min: 0,
                        max: 100,
                        ticks: {
                            stepSize: 10,
                            callback: function(value, index, values) {
                                return value + " %";
                            }            
                        }
                    }
                }
            },
        });

        new Chart("cumplimiento_por_root_2", {
            type: 'bar',
            data: {
                labels: ["In time", "Out time", "Undelivered"],
                datasets: [
                    {
                        label: 'Operativa',
                        data: [{{ $operativa_chart['total_ok'] }}, {{ $operativa_chart["total_outTime"] }}, {{ $operativa_chart['total_bad'] }}],
                        backgroundColor:[barColors[0], "rgb(253 224 71)", "rgb(252 165 165)",],
                        borderWidth: 1
                    },
                ],
            },
            options: {
                scales: {
                    y: {
                        min: 0,
                        max: 100,
                        ticks: {
                            stepSize: 10,
                            callback: function(value, index, values) {
                                return value + " %";
                            }            
                        }
                    }
                }
            },
        });
        new Chart("cumplimiento_por_subdirs_2", {
            type: 'bar',
            data: {
                labels: @json($operativa_chart['keys']),
                datasets: [
                    {
                        label: 'In Time',
                        data: @json($operativa_chart['total_sub_ok']),
                        backgroundColor:"rgb(134 239 172)",
                        borderWidth: 1
                    },
                    {
                        label: 'Out Time',
                        data: @json($operativa_chart['total_sub_outTime']),
                        backgroundColor:"rgb(253 224 71)",
                        borderWidth: 1
                    },
                    {
                        label: 'Undelivered',
                        data: @json($operativa_chart['total_sub_bad']),
                        backgroundColor:"rgb(252 165 165)",
                        borderWidth: 1
                    },
                ]
            },
            options: {
                indexAxis: 'y',
                // Elements options apply to all of the options unless overridden in a dataset
                // In this case, we are setting the border of each horizontal bar to be 2px wide
                elements: {
                    bar: {
                        borderWidth: 3,
                    }
                },
                responsive: true,
                plugins: {
                    legend: {
                        position: 'right',
                    },
                    title: {
                        display: true,
                        text: 'Cumplimiento en % de la categoría OPERATIVA'
                    }
                }
            },
        });


        new Chart("cumplimiento_por_root_3", {
            type: 'bar',
            data: {
                labels: ["In time", "Out time", "Undelivered"],
                datasets: [
                    {
                        label: 'Estratégica / Táctica',
                        data: [{{ $estrategica_tactica_chart['total_ok'] }}, {{ $estrategica_tactica_chart["total_outTime"] }}, {{ $estrategica_tactica_chart['total_bad'] }}],
                        backgroundColor:[barColors[0], "rgb(253 224 71)", "rgb(252 165 165)",],
                        borderWidth: 1
                    },
                ],
            },
            options: {
                scales: {
                    y: {
                        min: 0,
                        max: 100,
                        ticks: {
                            stepSize: 10,
                            callback: function(value, index, values) {
                                return value + " %";
                            }            
                        }
                    }
                }
            },
        });
        new Chart("cumplimiento_por_subdirs_3", {
            type: 'bar',
            data: {
                labels: @json($estrategica_tactica_chart['keys']),
                datasets: [
                    {
                        label: 'In Time',
                        data: @json($estrategica_tactica_chart['total_sub_ok']),
                        backgroundColor:"rgb(134 239 172)",
                        borderWidth: 1
                    },
                    {
                        label: 'Out Time',
                        data: @json($estrategica_tactica_chart['total_sub_outTime']),
                        backgroundColor:"rgb(253 224 71)",
                        borderWidth: 1
                    },
                    {
                        label: 'Undelivered',
                        data: @json($estrategica_tactica_chart['total_sub_bad']),
                        backgroundColor:"rgb(252 165 165)",
                        borderWidth: 1
                    },
                ]
            },
            options: {
                indexAxis: 'y',
                // Elements options apply to all of the options unless overridden in a dataset
                // In this case, we are setting the border of each horizontal bar to be 2px wide
                elements: {
                    bar: {
                        borderWidth: 3,
                    }
                },
                responsive: true,
                plugins: {
                    legend: {
                        position: 'right',
                    },
                    title: {
                        display: true,
                        text: 'Cumplimiento en % de la categoría ESTRATÉGICA / TÁCTICAS'
                    }
                }
            },
        });

        new Chart("cumplimiento_por_root_4", {
            type: 'bar',
            data: {
                labels: ["In time", "Out time", "Undelivered"],
                datasets: [
                    {
                        label: 'Gestión Humana',
                        data: [{{ $gestion_humana_chart['total_ok'] }}, {{ $gestion_humana_chart["total_outTime"] }}, {{ $gestion_humana_chart['total_bad'] }}],
                        backgroundColor:[barColors[0], "rgb(253 224 71)", "rgb(252 165 165)",],
                        borderWidth: 1
                    },
                ],
            },
            options: {
                scales: {
                    y: {
                        min: 0,
                        max: 100,
                        ticks: {
                            stepSize: 10,
                            callback: function(value, index, values) {
                                return value + " %";
                            }            
                        }
                    }
                }
            },
        });
        new Chart("cumplimiento_por_subdirs_4", {
            type: 'bar',
            data: {
                labels: @json($gestion_humana_chart['keys']),
                datasets: [
                    {
                        label: 'In Time',
                        data: @json($gestion_humana_chart['total_sub_ok']),
                        backgroundColor:"rgb(134 239 172)",
                        borderWidth: 1
                    },
                    {
                        label: 'Out Time',
                        data: @json($gestion_humana_chart['total_sub_outTime']),
                        backgroundColor:"rgb(253 224 71)",
                        borderWidth: 1
                    },
                    {
                        label: 'Undelivered',
                        data: @json($gestion_humana_chart['total_sub_bad']),
                        backgroundColor:"rgb(252 165 165)",
                        borderWidth: 1
                    },
                ]
            },
            options: {
                indexAxis: 'y',
                // Elements options apply to all of the options unless overridden in a dataset
                // In this case, we are setting the border of each horizontal bar to be 2px wide
                elements: {
                    bar: {
                        borderWidth: 3,
                    }
                },
                responsive: true,
                plugins: {
                    legend: {
                        position: 'right',
                    },
                    title: {
                        display: true,
                        text: 'Cumplimiento en % de la categoría GESTIÓN HUMANA'
                    }
                }
            },
        });

        const labelsCategories  =   [
            'Admin. / Financiera',
            'Operativa',
            'Estratégica / Táctica',
            'Gestión Humana'
        ];
        chartCategories     =   new Chart(categories, {
            type: "pie",
            data: {
                labels: labelsCategories,
                datasets: [{
                    backgroundColor: barColors,
                    data: [{{ sizeof($projects) }}, {{ sizeof($projects) }}, {{ sizeof($projects) }}, {{ sizeof($projects) }}]
                }]
            },
            options: {
                title: {
                    display: true,
                    text: "Cumplimiento por categorías en %"
                }
            }
        });
        let actualShow        =   'Admin. / Financiera';
        categories.onclick  =   (evt) => {
            const res = chartCategories.getElementsAtEventForMode(
                evt,
                'nearest',
                { intersect: true },
                true
            );
            // If didn't click on a bar, `res` will be an empty array
            if (res.length === 0) {
                return;
            }
            const newShow   =   chartCategories.data.labels[res[0].index];
            if(actualShow != newShow) {
                console.log(newShow);
                for(var i = 0; i < labelsCategories.length; i++)
                {
                    if(actualShow == labelsCategories[i]) {
                        $("#div_cumplimiento_por_root_"+(i+1)).addClass('hidden');
                        $("#div_cumplimiento_por_subdirs_"+(i+1)).addClass('hidden');
                    }
                    else {
                        if(newShow == labelsCategories[i]) {
                            $("#div_cumplimiento_por_root_"+(i+1)).removeClass('hidden');
                            $("#div_cumplimiento_por_subdirs_"+(i+1)).removeClass('hidden');
                        }
                    }
                }
                actualShow  =   newShow;
            }
        };

        const paises    =   @json($jsCountries);
        const projects  =   @json($projects);

        function onClickSearch() {
            const region        =   $("#eligeRegion").val();
            const country       =   $("#eligePais").val();
            const xproject      =   $("#eligeProyecto").val();
            const filterDate    =   $("#eligeYear").val();
            
            let regionValue     =   (region == null ? 0 : region);
            let countryValue    =   (country == null ? 0 : country);
            let projectValue    =   (xproject == null ? 0 : xproject);

            location.href='{{ url("/dashboard/") }}?region='+regionValue+'&country='+countryValue+'&projectId='+projectValue+'&filterDate='+filterDate;
        }

        function selectRegion(e) {
            const id    =   $("#eligeRegion").val();
            let countries   =   [];
            for(var i = 0; i < paises.length; i++) {
                if(paises[i].regionId == id)
                    countries.push(paises[i]);
            }
            $("#eligePais").html('<option value="0" selected>Todos los paises</option>');
            for(var i = 0; i < countries.length; i++) 
                $("#eligePais").append('<option value="'+countries[i].id+'">'+countries[i].name+'</option>');
        }
        function selectCountry(e) {
            const id    =   $("#eligePais").val();
            let xprojects   =   [];
            for(var i = 0; i < projects.length; i++) {
                if(projects[i].id == id)
                    xprojects.push(projects[i]);
            }
            $("#eligeProyecto").html('<option value="0" selected>Todos los proyectos</option>');
            for(var i = 0; i < xprojects.length; i++) 
                $("#eligeProyecto").append('<option value="'+xprojects[i].id+'">'+xprojects[i].name+'</option>');
        }
    </script>
@endsection