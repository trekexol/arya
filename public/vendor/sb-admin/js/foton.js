
  $(document).ready(function() {
 
      $("#fotop").fileinput({
            language: 'es',
            allowedFileExtensions: ['jpg','jpeg','png'],
            maxFileSize: 1000,
            showUpload: false,
            showClose: false,
            initialPreviewAsData: true,
            dropZoneEnabled: false,
            theme: "fas"    
        });

        $(".fotop").fileinput({
          language: 'es',
          allowedFileExtensions: ['jpg','jpeg','png'],
          maxFileSize: 1000,
          showUpload: false,
          showClose: false,
          initialPreviewAsData: true,
          dropZoneEnabled: false,
          theme: "fas"    
      });
 });
