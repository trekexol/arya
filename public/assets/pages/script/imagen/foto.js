
  $(document).ready(function() {

       $("#fotop").fileinput({
            language: 'es',
            allowedFileExtensions: ['jpg','jpeg','png'],
            maxFileSize: 1000,
            showUpload: false,
            showClose: false,
            initialPreviewAsData: false,
            dropZoneEnabled: false,
            theme: "fas"    
        });
 });
