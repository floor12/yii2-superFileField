/**
 * Created by floor12 on 23.06.2016.
 */


function superfileIndex(classname, field, object_id, selector) {
    $.ajax({
        url: '/superfilefield/index',
        dataType: "json",
        data: {class: classname, field: field, object_id: object_id},
        success: function (response) {
            $.each(response, function (key, value) {
                superfileRander(value, selector);
            });
        }

    });
}


function superfileRander(data, selector) {

    console.log(data);
    switch (data.type) {
        case 0:
            glyph = 'glyphicon glyphicon-file';
            break;
        case 1:
            glyph = 'glyphicon glyphicon-picture';
            break;
        case 2:
            glyph = 'glyphicon glyphicon-facetime-video';
            break;
    }

    cssClass = 'superfile superfile-type-' + data.type;

    if (data.video_status != 2 && data.type == 2)
        cssClass += ' superfile-converting';

    view = ' <div class="' + cssClass + '">';
    view += '<span class="' + glyph + '"></span> <span class="superfile-title">' + data.title + '</span>';

    if (data.type == 1 || data.type == 2) {
        view += '<div class="superfile-preview">';


        if (data.type == 2 && data.video_status == 2)
            view += '<video src="' + data.filename + '"></video>';
        else
            view += '<img src="' + data.filename + '.jpg?' + Math.floor(Math.random() * 1000) + '" class="img-responsive" id="superfilefield-preview-' + data.id + '">';
        view += '</div>';

    }

    view += '<div class="superfile-control">' +
        '<div class="edit"></div>' +
        '<div class="buttons">';

    if (data.type == 1) {
        view += '<a class="btn btn-default btn-xs superfile-crop" id="superfilefield-crop-' + data.id + '" data-src="' + data.filename + '" data-id="' + data.id + '"><span class="glyphicon glyphicon-scissors"></span></a>"';
        view += '<a class="btn btn-default btn-xs superfile-rotate" data-direction="1" data-id="' + data.id + '"><span class="glyphicon glyphicon-share-alt flipet"></span></a>"';
        view += '<a class="btn btn-default btn-xs superfile-rotate" data-direction="2" data-id="' + data.id + '"><span class="glyphicon glyphicon-share-alt "></span></a>"';
    }

    view += '<a href="' + data.filename + '" target="_blank"  id="superfilefield-viewlink-' + data.id + '" class="btn btn-default btn-xs superfile-view" data-id="' + data.id + '"><span class="glyphicon glyphicon-eye-open"></span></a>"' +
        '<a class="btn btn-default btn-xs superfile-edit" data-id="' + data.id + '"><span class="glyphicon glyphicon-pencil"></span></a>"' +
        '<a class="btn btn-danger btn-xs superfile-delete" data-id="' + data.id + '"><span class="glyphicon glyphicon-remove"></span></a>"' +
        '</div>' +
        '</div>';

    view += '<input type="hidden" value="' + data.id + '" name="' + data.class.replace("common\\models\\", "") + '[superfilesArray][' + data.field + '][]">';
    view += '</div>';

    $(selector).append(view);
}


function superfileCrop() {
    сropBoxData = cropper.cropper('getCropBoxData');
    imageData = cropper.cropper('getImageData');
    canvasData = cropper.cropper('getCanvasData');
    ratio = imageData.height / imageData.naturalHeight;
    cropLeft = (сropBoxData.left - canvasData.left) / ratio;
    cropTop = (сropBoxData.top - canvasData.top) / ratio;
    cropWidth = сropBoxData.width / ratio;
    cropHeight = сropBoxData.height / ratio;
    data = {
        id: currentCropId,
        width: cropWidth,
        height: cropHeight,
        top: cropTop,
        left: cropLeft,
    }
    $.ajax({
        method: "put",
        url: '/superfilefield/crop/',
        data: data,
        dataType: "json",
        success: function (msg) {
            console.log(msg.filename);
            $('.superfield-list').find('#superfilefield-preview-' + currentCropId).attr('src', msg.filename);
            $('.superfield-list').find('#superfilefield-viewlink-' + currentCropId).attr('href', msg.filename);
            $('.superfield-list').find('#superfilefield-crop-' + currentCropId).attr('data-src', msg.filename);

            superfilemodal.modal('hide');
        }
    })
}


$(document).ready(function () {

    superfilemodal = $('#superfileEditorModal');

    var timer;

    $(".superfield-list").sortable({
        cancel: "a,button",
        axis: 'y'
    });

    $(document).on('mouseover', '.superfile', function () {
        object = $(this);
        object.find('.superfile-control').stop().fadeIn(200);

        if (object.attr('data-showinput') != '1') {
            timer = window.setTimeout(function () {
                object.find('.superfile-preview').stop().fadeIn(200);

                video = object.find('.superfile-preview video').get(0);
                if (video) {
                    video.volume = 0.2;
                    video.play();
                }
            }, 300);
        }
    })

    $(document).on('mouseout', '.superfile', function () {
        if ($(this).attr('data-showinput') != '1')
            $(this).find('.superfile-control').stop().fadeOut(200);

        $(this).find('.superfile-preview').stop().fadeOut(200);
        video = object.find('.superfile-preview video').get(0);
        if (video)
            video.pause();
        clearTimeout(timer);
    })


    $(document).on('click', '.superfile-crop', function () {
        var obj = $(this).parent().parent().parent();
        currentCropId = $(this).attr('data-id');
        src = $(this).attr('data-src');
        var image = $('<img>').attr('src', src + '?' + Math.floor(Math.random() * 1000));
        $('#superfield-editor-aria').html(image);
        // $('#superfieldCurrentCropId').html('');
        // $('#superfieldCurrentCropId').html(currentCropId);
        superfilemodal.modal('show');
        cropper = image.cropper({
            viewMode: 1,
            minContainerWidth: 870,
            minContainerHeight: 500,
        });

        $('#superfield-control-01').click(function () {
            cropper.cropper('setAspectRatio', 1 / 1);
        });
        $('#superfield-control-02').click(function () {
            cropper.cropper('setAspectRatio', 3 / 4);
        });

        $('#superfield-control-03').click(function () {
            cropper.cropper('setAspectRatio', 4 / 3);
        });

        $('#superfield-control-04').click(function () {
            cropper.cropper('setAspectRatio', 16 / 9);
        });

        $('#superfield-control-05').click(function () {
            cropper.cropper('setAspectRatio', 64 / 19);
        });
    })

    $(document).on('click', '.superfile-rotate', function () {
        object = $(this).parent().parent().parent();
        id = $(this).attr('data-id');
        direction = $(this).attr('data-direction');
        $.ajax({
            url: '/superfilefield/rotate',
            data: {id: id, direction: direction},
            method: 'PATCH',
            success: function (response) {
                img = object.find('.superfile-preview img')
                img.attr('src', img.attr('src') + "?" + Math.floor(Math.random() * 1000));

            }
        });
    });

    $(document).on('click', '.superfile-edit', function () {
        object = $(this).parent().parent().parent();
        object.find('.buttons').hide();
        object.attr('data-showinput', 1);
        editInput = $('<input>').val(object.find('.superfile-title').html());
        object.find('.edit').append(editInput);
        editInput.focus();
        editInput.blur(function () {
            object.attr('data-showinput', 0);
            editInput.remove();
            object.find('.buttons').show();
        });
        editInput.keydown(function (event) {
            if (event.keyCode == 13) {
                $.ajax({
                    url: '/superfilefield/update',
                    data: {id: id, title: editInput.val()},
                    method: 'put',
                    success: function (response) {
                        object.attr('data-showinput', 0);
                        object.find('.superfile-title').html(editInput.val());
                        editInput.remove();
                        object.find('.buttons').show();
                    }
                });
            }
        });
        id = $(this).attr('data-id');
    })

    $(document).on('click', '.superfile-delete', function () {
        object = $(this).parent().parent().parent();
        id = $(this).attr('data-id');
        $.ajax({
            url: '/superfilefield/delete',
            data: {id: id},
            method: 'delete',
            success: function (response) {
                object.fadeOut(300, function () {
                    object.remove();
                })
            }
        });
    })

    $(window).keydown(function (event) {
        if (event.keyCode == 13) {
            event.preventDefault();
            return false;
        }
    });

});


