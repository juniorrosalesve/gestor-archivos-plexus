@extends('layouts.app')
@section('body')
    <style>
        .fake-input--box{
            box-sizing:border-box;
            width:100%;
            padding:5px 0;
        }

        /* .fake-input--box input[type=text]{
            margin:5px;
            padding:5px;
            border:none;
            outline:none;
            border-bottom:2px #ddd solid;
            width:100px;
            float:left;
        }

        .fake-input--box input[type=text]:focus{
            border-bottom:2px #4285F4 solid;   
        } */

        .tag-label{
            font-size:14px;
            color:#999;
        }

        .tag{
            cursor:pointer;
            text-align: center;
            -webkit-border-radius: 3px;
            -moz-border-radius: 3px;
            border-radius: 3px;
            float:left;
            margin:5px 5px 5px 0;
            padding:5px;
            background:#4285F4;
            color:#FFFFFF;
            position: relative;
            overflow: hidden;
        }

        .tag:hover:after{
            position:absolute;
            padding:5px 0;
            top: 0;
            right:0;
            height:100%;
            width:100%;
            background:#4285F4;
            content: "\00d7";
        }

        #showarray{
            
            color:black;
            margin-top:30px;
                box-sizing:border-box;
            width:100%;
            padding:30px;
            border:1px solid #ddd;
        }

        .group:after {
            content: "";
            display: table;
            clear: both;
        }
    </style>

    <div class="card shadow bordered bg-base-100">
        <div class="card-body">
            <div class="text-sm breadcrumbs">
                <ul>
                    <li>
                        <a href="{{ route('regions') }}">Regiones</a>
                    </li> 
                    <li>Nueva región</li>
                </ul>
            </div>
            <form method="POST" action="{{ route('store-region') }}" id="formstore">
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
                    <div class="mt-5 ml-3 form-control">
                        <label class="label">
                            <span class="label-text">Director <i class="text-red-500">*</i></span>
                        </label> 
                        <select name="userId" class="select select-bordered w-full" required>
                            @foreach ($users as $user)
                                <option value="{{ $user->id }}">{{ $user->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mt-5 ml-3">
                        <div class="form-control">
                            <label class="label">
                                <span class="label-text">Ingrese sus países, sepárelos con coma o presione enter <i class="text-red-500">*</i></span>
                            </label> 
                            <div id="fakeinput" class="fake-input--box group">
                                <input type="text" class="input input-bordered" id="tags-input" autocomplete="off">
                            </div>
                            <input type="hidden" name="countries" id="input-countries" required>
                        </div>
                    </div>
                </div>
                <hr style="margin-top:30px;">
                <div class="modal-action">
                    <button type="button" onclick="submitForm()" class="btn btn-secondary btn-sm">Registrar</button>
                </div>
            </form>
        </div>
    </div>
    <script>
        tags = [];

        function submitForm() {
            if(tags.length == 0) 
                alert('Necesitas añadir uno o varios paises.');
            else
                $("#formstore").submit();
        }

        $(document).on("keydown", '#fakeinput', function (e) {
            var tagname = $('#tags-input').val().replace(",", "");

        if (tagname.length < 1 && e.keyCode == 8){
            if(tags.length > 0){
                deleteLastTagFromArray();
            }
        }  
        });

        $(document).on("keyup", '#fakeinput', function (e) {
            var tagname = $('#tags-input').val().replace(",", "");
            if (e.keyCode == 188 || e.keyCode == 13) {
                console.log("enter or comma used");
                if (tagname.length > 2 && !doesTagExist(tagname)){
                    makeTag(tagname);
                    addTagToArray(tagname);
                }
            }
        });

        function doesTagExist(tagname){
            var o = 0;
            var len = tags.length;
                for (; o < len; o++) { 
                    if(tagname == tags[o]){
                    return ture;
                }
            }
            return false;
        }

        function makeTag(tagname){
            var tagTemplate = '<div id="'+tagname+'" class="tag">'+tagname+'</div>';
            $(tagTemplate).insertBefore( "#tags-input" );
            $('#tags-input').val('');
        }

        function removeTag(tagname){
            var tagid = "#"+tagname;
            $(tagid).remove();
        }

        function addTagToArray(tagname){
            // code to come
            tags.push(tagname);
            setValueCountries();
        }

        function deleteLastTagFromArray(){
            var last = tags.length - 1;
            var lastTag = tags[last];
            // alert(tags[last]);
            removeTag(lastTag);
            deleteTagFromArray(lastTag);

            setValueCountries(); //demo
        }

        function deleteTagFromArray(tagname){
            var i = 0;
            var len = tags.length;
            for (; i < len; i++) { 
                if(tagname == tags[i]){
                tags.splice(i, 1);
                }
            }
            setValueCountries(); // demo only
        }


        $('.fake-input--box').on('click', '.tag', function(e){
            console.log("delete: "+this.id);
            removeTag(this.id);
            deleteTagFromArray(this.id);
        });

        // demo only
        function setValueCountries(){
            $("#input-countries").val(tags);
        }
    </script>
@endsection