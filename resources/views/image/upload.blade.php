@extends('layouts.app')

@section('content')
<div class="card">
    <div class="card-header">
        {{ $image->name }}
    </div>

    <div class="card-body">
        <p>
            {{ $image->description }}
        </p>

        <form action="{{ action("ImageController@doUpload", ["image" => $image]) }}"
              class="dropzone"
              id="my-great-dropzone">
            {{ csrf_field() }}
            <input type="file" name="file"  style="display: none;">
        </form>

        <p class="text-muted">Progress: <span id="progress-text">0</span>%</p>
    </div>
</div>

<script>
    window.addEventListener('DOMContentLoaded', function() {
        var token = $('input[name=_token]').val();

        var myDropzone = new window.Dropzone.Dropzone("#my-great-dropzone", {
            chunking: true,
            method: "POST",
            maxFilesize: 200 * 1024, // MB
            chunkSize: 1024 * 1024,  // Bytes
            parallelChunkUploads: false,
            maxFiles: 1,
            createImageThumbnails: false,
            acceptedFiles: "application/x-virtualbox-ova",
            dictDefaultMessage: "Drop .ova file here to upload (max 200GB)",

            uploadprogress: function(file, progress, bytesSent) {
                // original behavior
                // https://github.com/dropzone/dropzone/blob/main/src/options.js#L727
                if (file.previewElement) {
                  for (let node of file.previewElement.querySelectorAll(
                    "[data-dz-uploadprogress]"
                  )) {
                    node.nodeName === "PROGRESS"
                      ? (node.value = progress)
                      : (node.style.width = `${progress}%`);
                  }
                }

                $("#progress-text").text(progress.toFixed(2));
            }
        });

        myDropzone.on('sending', function (file, xhr, formData) {
            // Append token to the request - required for web routes
            formData.append("_token", token);
        });

        myDropzone.on('addedfile', function () {
            console.log("File added to upload queue");
        });

        myDropzone.on('success', function (file) {
            console.log("Upload success!");
            window.location = "{{ action('ImageController@show', ["image" => $image]) }}";
        });

        myDropzone.on('error', function(file, message) {
           console.log(message);
        });
    });
</script>
@endsection
