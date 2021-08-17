"use strict";
NProgress.start();
$(document).ready(function (){
    let tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
    let tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl)
    })
    NProgress.done();
});
Dropzone.autoDiscover = false;
$("#FileUploadButton").on("click", function () {
    let file = document.getElementById("FileUpload").files;
    for (let index in file){
        if(typeof file[index] == "object"){
            if(file[index].type.indexOf("image") > -1 && file[index].size > 0) {
                myDropzone.addFile(file[index])
            }
        }

    }
})
const myDropzone = new Dropzone("#myDropzone", {
    url: "/upload",
    uploadMultiple: false,
    method: 'post',
    acceptedFiles: 'image/*',
    maxFilesize: 10,
    filesizeBase: 1000,
    sending: function(file, xhr, formData) {
        // console.log(file)
        NProgress.start();
        formData.append("filesize", file.size);
    },
    success: function (file, response, e) {
        NProgress.done();
        console.log(response)
        // file.previewTemplate.appendChild(document.createTextNode(response.src));
        // window.open(window.location.origin + response.src)
        if (!response.success) {
            $(file.previewTemplate).children('.dz-error-mark').css('opacity', '1')
        }else{
            $("#imgShow").append('<div class="row border rounded" style="margin-top: 10px;">\n' +
                '<div class="col-lg-5 "><img src="'+response.src+'" class="w-75" alt="Image"></div>\n' +
                '<div class="col-lg-7 mt-3"><h5 class="text-start overflow-hidden">' + file.name +
                '</h5><div class="input-group mb-1"><div class="input-group-prepend">\n' +
                '    <span class="input-group-text" id="inputGroup-sizing-default">Image URL</span>\n' +
                '  </div>\n' +
                '  <input type="text" class="form-control" value="'+ window.location.origin + response.src + '"></div>\n' +
                '<div class="input-group mb-1"><div class="input-group-prepend">\n' +
                '    <span class="input-group-text" id="inputGroup-sizing-default">Markdown</span>\n' +
                '  </div>\n' +
                '  <input type="text" class="form-control" value="!['+ file.name +']('+ window.location.origin + response.src + ')">\n' +
                '</div>' +
                '<div class="input-group mb-1"><div class="input-group-prepend">\n' +
                '    <span class="input-group-text" id="inputGroup-sizing-default">HTML</span>\n' +
                '  </div>\n' +
                '  <input type="text" class="form-control" value="<img src=\''+ window.location.origin + response.src + '\' alt=\''+ file.name +'\' > \">\n' +
                '</div></div></div>\n' +
                '</div>').fadeIn();
            $(".form-control").on('click', function () {
                this.select();
                document.execCommand("copy");
            })
        }
    }
});
$(document).ready(function () {
    document.onpaste = function(event){
        let items = (event.clipboardData || event.originalEvent.clipboardData).items;
        for (let index in items) {
            let item = items[index];
            if (item.kind === 'file') {
                myDropzone.addFile(item.getAsFile())
            }
        }
    }
});
