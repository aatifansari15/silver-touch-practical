var $modal = $('#modal');
var image = document.getElementById('image');
var cropper;
$("body").on("change", ".image", function(e) {
    var files = e.target.files;
    var done = function(url) {
        image.src = url;
        $modal.modal('show');
    };
    var reader;
    var file;
    var url;
    if (files && files.length > 0) {
        file = files[0];
        if (URL) {
            done(URL.createObjectURL(file));
        } else if (FileReader) {
            reader = new FileReader();
            reader.onload = function(e) {
                done(reader.result);
            };
            reader.readAsDataURL(file);
        }
    }
});
$modal.on('shown.bs.modal', function() {
    cropper = new Cropper(image, {
        //aspectRatio: 1,
        minCropBoxWidth:250,
        minCropBoxHeight:250,
        viewMode: 3,
        preview: '.preview'
    });
}).on('dragmove.cropper', function (e) {
    console.log('dragmove.cropper');

    var $cropper = $(e.target);

    // Call getData() or getImageData() or getCanvasData() or
    // whatever fits your needs
    var data = $cropper.cropper('getCropBoxData');

    console.log("data = %o", data);

    // Analyze the result
    if (data.height <= 250 || data.width <= 250) {
        console.log("Minimum size reached!");

        // Stop resize
        return false;
    }

    // Continue resize
    return true;
}).on('dragstart.cropper', function (e) {
    console.log('dragstart.cropper');

    var $cropper = $(e.target);

    // Get the same data as above 
    var data = $cropper.cropper('getCropBoxData');

    // Modify the dimensions to quit from disabled mode
    if (data.height <= 250 || data.width <= 250) {
        data.width = 251;
        data.height = 251;

        $(e.target).cropper('setCropBoxData', data);
    }
}).on('hidden.bs.modal', function() {
    cropper.destroy();
    cropper = null;
});
$("#crop").click(function() {
    canvas = cropper.getCroppedCanvas(/*{
        width: 160,
        height: 160,
    }*/);
    canvas.toBlob(function(blob) {
        url = URL.createObjectURL(blob);
        var reader = new FileReader();
        reader.readAsDataURL(blob);
        reader.onloadend = function() {
            var base64data = reader.result;
            $('#base64Image').val(base64data);
            $modal.modal('hide');
        }
    });
});