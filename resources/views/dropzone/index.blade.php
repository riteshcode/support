<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>Laravel</title>


        <link href="{{ url('linkdrop/dropzone.css')}}">
        <link href="{{ url('linkdrop/basic.css')}}">
    
        <script src="{{ url('linkdrop/dropzone.js')}}"></script>
    </head>
    <body class="antialiased">

    <section>

    <h1 id="try-it-out">Try it out!</h1>

    <div id="dropzone">
        <form action="{{ route('dropzonejs.store')}}" class="dropzone needsclick dz-clickable" id="demo-upload">

        <div class="dz-message needsclick">
            <button type="button" class="dz-button">Drop files here or click to upload.</button><br>
            <span class="note needsclick">(This is just a demo dropzone. Selected files are <strong>not</strong> actually uploaded.)</span>
        </div>

        </form>
    </div>

    </section>

</body>
</html>
