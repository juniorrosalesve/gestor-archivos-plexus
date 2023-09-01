<!DOCTYPE html>
<html lang="es" data-theme="plexus_theme">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>Plexus Business System</title>

        <!-- ViteJS -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <!-- Styles -->
        <link rel="stylesheet" href="{{ asset('css/admin.css') }}">
        <link rel="stylesheet" href="{{ asset('css/jquery.dataTables.min.css') }}">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.23/dist/sweetalert2.min.css">

        <!-- Scripts -->
        <script src="{{ asset('js/jquery.min.js') }}"></script>
        <script src="{{ asset('js/jquery.dataTables.min.js') }}"></script>
        
        <script src="{{ asset('js/axios.min.js') }}"></script>
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.23/dist/sweetalert2.all.min.js"></script>
    </head>
    <body>
        <div class="shadow bg-base-200 drawer @if(\Request::route()->getName() != 'dashboard') lg:drawer-open @endif" id="body_sidebar">
            <input id="my-drawer-2" type="checkbox" class="drawer-toggle"> 
            <div class="flex flex-col drawer-content p-5">
                <label for="my-drawer-2" class="mb-4 btn btn-primary drawer-button lg:hidden">Kit de herramientas</label> 
                <div class="container">
                    @yield('body')
                </div>
            </div>
            <div class="drawer-side shadow" id="sidebar">
                <label for="my-drawer-2" class="drawer-overlay"></label> 
                <ul class="menu p-4 h-full overflow-y-auto w-80 bg-base-100 text-base-content">
                    <div class="bg-primary shadow absolute w-full left-0 top-0 p-2">
                        <center><img src="{{ asset('images/logo-plexus.png') }}" width="200"></center>
                    </div>
                    <li style="margin-top:110px;">
                        <a href="{{ route('dashboard') }}" @if (\Request::route()->getName() == 'dashboard' || 
                            \Request::route()->getName() == 'dashboard-projects')
                            class="bg-base-300 text-secondary-content rounded-lg"
                        @endif>
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" class="w-6"><title>chart-bar</title><path d="M22,21H2V3H4V19H6V10H10V19H12V6H16V19H18V14H22V21Z" /></svg>
                            Dashboard
                        </a>
                    </li>
                    @if (Auth::user()->access == 'a')
                        <li>
                            <a href="{{ route('project-index') }}" @if (\Request::route()->getName() == 'project-index'
                                || \Request::route()->getName() == 'projects' || \Request::route()->getName() == 'project')
                                class="bg-base-300 text-secondary-content rounded-lg"
                            @endif>
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" class="w-6"><title>tie</title><path d="M6,2L10,6L7,17L12,22L17,17L14,6L18,2Z" /></svg>
                                Proyectos
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('users') }}" @if (\Request::route()->getName() == 'users' || 
                                \Request::route()->getName() == 'create-user')
                                class="bg-base-300 text-secondary-content rounded-lg"
                            @endif>
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" class="w-6"><title>account-multiple-outline</title><path d="M13.07 10.41A5 5 0 0 0 13.07 4.59A3.39 3.39 0 0 1 15 4A3.5 3.5 0 0 1 15 11A3.39 3.39 0 0 1 13.07 10.41M5.5 7.5A3.5 3.5 0 1 1 9 11A3.5 3.5 0 0 1 5.5 7.5M7.5 7.5A1.5 1.5 0 1 0 9 6A1.5 1.5 0 0 0 7.5 7.5M16 17V19H2V17S2 13 9 13 16 17 16 17M14 17C13.86 16.22 12.67 15 9 15S4.07 16.31 4 17M15.95 13A5.32 5.32 0 0 1 18 17V19H22V17S22 13.37 15.94 13Z" /></svg>
                                Usuarios
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('regions') }}" @if (\Request::route()->getName() == 'regions' || 
                                \Request::route()->getName() == 'create-region')
                                class="bg-base-300 text-secondary-content rounded-lg"
                            @endif>
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" class="w-6"><title>map-legend</title><path d="M9,3L3.36,4.9C3.15,4.97 3,5.15 3,5.38V20.5A0.5,0.5 0 0,0 3.5,21L3.66,20.97L9,18.9L15,21L20.64,19.1C20.85,19.03 21,18.85 21,18.62V3.5A0.5,0.5 0 0,0 20.5,3L20.34,3.03L15,5.1L9,3M8,5.45V17.15L5,18.31V6.46L8,5.45M10,5.47L14,6.87V18.53L10,17.13V5.47M19,5.7V17.54L16,18.55V6.86L19,5.7M7.46,6.3L5.57,6.97V9.12L7.46,8.45V6.3M7.46,9.05L5.57,9.72V11.87L7.46,11.2V9.05M7.46,11.8L5.57,12.47V14.62L7.46,13.95V11.8M7.46,14.55L5.57,15.22V17.37L7.46,16.7V14.55Z" /></svg>
                                Regiones
                            </a>
                        </li>
                    @else 
                        <li>
                            <a href="{{ route('manager-list') }}" @if (\Request::route()->getName() == 'manager-list')
                                class="bg-base-300 text-secondary-content rounded-lg"
                            @endif>
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" class="w-6"><title>tie</title><path d="M6,2L10,6L7,17L12,22L17,17L14,6L18,2Z" /></svg>
                                Proyectos
                            </a>
                        </li>
                    @endif

                    {{-- <li>
                        <a href="{{ route('cajero-tester') }}" @if (\Request::route()->getName() == 'cajero-tester')
                            class="bg-base-300 text-secondary-content rounded-lg"
                        @endif>
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" class="w-6"><title>piggy-bank-outline</title><path d="M15 10C15 9.45 15.45 9 16 9C16.55 9 17 9.45 17 10S16.55 11 16 11 15 10.55 15 10M8 9H13V7H8V9M22 7.5V14.47L19.18 15.41L17.5 21H12V19H10V21H4.5C4.5 21 2 12.54 2 9.5S4.46 4 7.5 4H12.5C13.41 2.79 14.86 2 16.5 2C17.33 2 18 2.67 18 3.5C18 3.71 17.96 3.9 17.88 4.08C17.74 4.42 17.62 4.81 17.56 5.23L19.83 7.5H22M20 9.5H19L15.5 6C15.5 5.35 15.59 4.71 15.76 4.09C14.79 4.34 14 5.06 13.67 6H7.5C5.57 6 4 7.57 4 9.5C4 11.38 5.22 16.15 6 19H8V17H14V19H16L17.56 13.85L20 13.03V9.5Z" /></svg>
                            Cajeros testers
                        </a>
                    </li> --}}
                    <li class="mt-1 border-t">
                        <a href="{{ route('profile.edit') }}" @if (\Request::route()->getName() == 'profile.edit')
                            class="bg-base-300 text-secondary-content rounded-lg"
                        @endif>
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" class="w-6"><title>account-edit</title><path d="M21.7,13.35L20.7,14.35L18.65,12.3L19.65,11.3C19.86,11.09 20.21,11.09 20.42,11.3L21.7,12.58C21.91,12.79 21.91,13.14 21.7,13.35M12,18.94L18.06,12.88L20.11,14.93L14.06,21H12V18.94M12,14C7.58,14 4,15.79 4,18V20H10V18.11L14,14.11C13.34,14.03 12.67,14 12,14M12,4A4,4 0 0,0 8,8A4,4 0 0,0 12,12A4,4 0 0,0 16,8A4,4 0 0,0 12,4Z" /></svg>
                            Mi cuenta
                        </a>
                    </li>
                    <div class="bg-primary p-2 text-center" style="position:absolute;bottom:0;left:0;width:100%;">
                        <p class="text-black text-sm italic inline">
                            Versión {{ env('APP_VERSION') }}
                            <form method="POST" action="{{ route('logout') }}" class="appearance-none inline ml-2 tooltip tooltip-secondary" data-tip="Cerrar sesión">
                                @csrf
                                <a href="{{ route('logout') }}" onclick="event.preventDefault();this.closest('form').submit();">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" class="inline-block w-4 mr-2 stroke-current text-red-500"><title>logout</title><path d="M16,17V14H9V10H16V7L21,12L16,17M14,2A2,2 0 0,1 16,4V6H14V4H5V20H14V18H16V20A2,2 0 0,1 14,22H5A2,2 0 0,1 3,20V4A2,2 0 0,1 5,2H14Z" /></svg>
                                </a>
                                <input type="hidden" name="redirecTo" value="Admin" />
                            </form>
                        </p>
                    </div>
                </ul>
            </div>
        </div>

        <script>
            $(function(){
                $("#table, #table2").DataTable({
                    "language": {
                        "lengthMenu": "Mostrar _MENU_ registros por página",
                        "zeroRecords": "Sin resultados",
                        "info": "Mostrando página _PAGE_ de _PAGES_",
                        "infoEmpty": "No hay registros disponibles",
                        "infoFiltered": "(filtrados de _MAX_ registros totales)",
                        "search": "Buscar",
                        "paginate": {
                            "next": "Siguiente",
                            "previous": "Atras"
                        }
                    },
                    order:[
                        [0, 'asc']
                    ]
                });
            })
        </script>
    </body>
</html>