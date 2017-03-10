$('#create_article_form').submit(function (event) {
    event.preventDefault();
    $image = $('#image_replace').attr('src');

    var formData = {
        _token: $("input[name='_token']").val(),
        type: 'Video',
        status: $('select[name="status"]').val() === '' ? '' : $('select[name="status"]').val(),
        title: $.trim($("input[name='title']").val()) === '' ? swal({
            title: 'Chưa nhập tiêu đề',
            type: 'error'
        }) : $("input[name='title']").val(),

        description: $("textarea[name='description']").val(),
        tags: $("input[name='tags']").val(),
        related: $("input[name='related']").val(),
        media_path: $('input[name=media_path]').val(),
        publish_date: $("input[name='publish_date']").val() === '' ? swal({
            title: 'Chưa nhập ngày đăng bài',
            type: 'error'
        }) : $("input[name='publish_date']").val(),

        publish_time: $("input[name='publish_time']").val() === '' ? swal({
            title: 'Chưa nhập thời gian đăng bài',
            type: 'error'
        }) : $("input[name='publish_time']").val(),

        youtube: $.trim($("textarea[name='youtube']").val()) === '' ? swal({
            title: 'Chưa nhập link nhúng video',
            type: 'error'
        }) : $("textarea[name='youtube']").val(),

        thumbnail: $image,

        seo_title: $("input[name='seo_title']").val() === '' ? $("input[name='title']").val() : $("input[name='seo_title']").val(),
        seo_meta: $("input[name='seo_meta']").val() === '' ? $("input[name='title']").val() : $("input[name='seo_meta']").val(),
        seo_description: $("textarea[name='seo_description']").val() === '' ? $("textarea[name='description']").val() : $("textarea[name='seo_description']").val(),
    };
    if (!formData.title || !formData.youtube || !formData.publish_time || !formData.publish_date) {
        return;
    }
    $('button[type=submit]').hide();
    $.ajax({
        type: "POST",
        url: '/media/recipe/create/',
        dataType: 'json',
        data: formData,
        success: function (res) {
            console.log(res);
            event.preventDefault();
            swal({title: res.msg, type: res.status}, function (isConfirm) {

            });
        },
        error: function (err) {
            alert('có lỗi xảy ra');
        },
    });
});

$('#article_form_edit').submit(function (event) {
    event.preventDefault();

    // $categories = [];
    // $array = $('input[name="category[]"]:checked').each(function () {
    //     if ($(this).is(':checked'))
    //         $categories.push($(this).val());
    // });
    // if ($("select[name='slLevel']").val().length > 0) {
    //     $categories.push($("select[name='slLevel']").val());
    // }
    // $ingredients = [];
    // $array = $('input[name="ingredients[]"]').each(function () {
    //     if ($(this).val() != '') {
    //         $ingredients.push($(this).val());
    //     } else {
    //         $ingredients = false;
    //     }
    // });
    // $quanlity = [];
    // $array = $('input[name="quanlity[]"]').each(function () {
    //     if ($(this).val() != '') {
    //         $quanlity.push($(this).val());
    //     } else {
    //         swal('Thất Bại', 'Nhập thiếu box số lượng');
    //         $quanlity = false;
    //     }
    // });
    // $quanlity_type = [];
    // $array = $('input[name="quanlity_type[]"]').each(function () {
    //     if ($(this).val() != '') {
    //         $quanlity_type.push($(this).val());
    //     } else {
    //         swal('Thất Bại', 'Nhập thiếu box đơn vị');
    //         $quanlity_type = false;
    //     }
    // });


    // $steps = [];

    // $array = $('textarea[name="steps[]"]').each(function () {
    //     if ($(this).val() != '') {
    //         $steps.push($(this).val());
    //     } else {
    //         swal('Thất Bại', 'Nhập thiếu Hướng Dẫn');
    //         $steps = false;
    //     }
    // });

    // $end_count = $steps.length;
    // if ($end_count === undefined) {
    //     $end_count = 1;
    // }
    // $formSteps = [];
    // for ($i = 1; $i <= $end_count; $i++) {
    //     $image_steps = [];
    //     $array = $('input[name="files_step_' + $i + '[]"]').each(function () {
    //         if ($(this).val() != '') {
    //             $image_steps.push($(this).val());
    //         } else {
    //             $image_steps.push('');
    //         }
    //     });

    //     $formSteps[parseInt($i) - 1] = $image_steps;
    // }

    // var id = $('input[name=id]').val() === '' ? null : $('input[name=id]').val();

    // var formData = {
    //     _token: $("input[name='_token']").val(),
    //     id: $('input[name=id]').val() === '' ? null : $('input[name=id]').val(),
    //     type: 'Recipe',
    //     status: $('select[name="status"]').val() === '' ? '' : $('select[name="status"]').val(),
    //     steps: $steps,
    //     files_steps: JSON.stringify($formSteps),
    //     content: $("textarea[name='content']").val(),
    //     title: $("input[name='title']").val() === '' ? swal({
    //         title: 'Chưa nhập tiêu đề',
    //         type: 'error'
    //     }) : $("input[name='title']").val(),
    //     type_article: $("select[name='type_article']").val(),
    //     event_id: $("select[name='event']").val(),
    //     title_extra: $("input[name='title_extra']").val(),
    //     prep_time: $("select[name='prep_time']").val(),
    //     cook_time: $("select[name='cook_time']").val(),
    //     directions: $("textarea[name='directions']").val(),
    //     description: $("textarea[name='description']").val(),
    //     ingredients: $ingredients == false ? swal('Thất Bại', 'Nhập thiếu box nguyên liệu') : $ingredients,
    //     quanlity: $quanlity == false ? swal('Thất Bại', 'Nhập thiếu box số lượng') : $quanlity,
    //     quanlity_type: $quanlity_type == false ? swal('Thất Bại', 'Nhập thiếu box đơn vị') : $quanlity_type,
    //     publish_date: $("input[name='publish_date']").val(),
    //     publish_time: $("input[name='publish_time']").val(),
    //     tags: $("input[name='tags']").val(),
    //     number_people: $("input[name='number_people']").val(),
    //     youtube: $("input[name='youtube']").val(),
    //     related: $("input[name='related']").val(),
    //     category: $categories.length === 0 ? swal({
    //         title: 'Chưa chọn chuyên mục',
    //         type: 'error'
    //     }) : JSON.stringify($categories),
    //     thumbnail: $("input[name='thumbnail']").val() === '' ? swal({
    //         title: 'Chưa chọn ảnh đại diện',
    //         type: 'error'
    //     }) : $("input[name='thumbnail']").val(),
    //     thumbnail_extra: $("input[name='thumbnail_extra']").val(),
    //     parent_category: $("input[name='parent_id']").val() === '' ? swal({
    //         title: 'Chưa Chọn Chuyên Mục Cha Hãy Hover Vào Lá Cờ',
    //         type: 'error'
    //     }) : $("input[name='parent_id']").val(),
    //     gallery: $("input[name='gallery']").val(),
    //     seo_title: $("input[name='seo_title']").val() === '' ? $("input[name='title']").val() : $("input[name='seo_title']").val(),
    //     seo_meta: $("input[name='seo_meta']").val() === '' ? $("input[name='title']").val() : $("input[name='seo_meta']").val(),
    //     seo_description: $("textarea[name='seo_description']").val() === '' ? $("textarea[name='description']").val() : $("textarea[name='seo_description']").val(),
    // };

    // if (!formData.title || !formData.category || !formData.thumbnail || !formData.ingredients || !formData.quanlity || !formData.quanlity_type || !formData.steps || !formData.parent_category) {
    //     return;
    // }
    // $('button[type=submit]').hide();
    // $.ajax({
    //     type: "POST",
    //     url: '/media/recipe/edit/' + id,
    //     dataType: 'json',
    //     data: formData,
    //     success: function (res) {
    //         event.preventDefault();
    //         swal({title: res.msg, type: res.status}, function (isConfirm) {
    //             if (isConfirm) {
    //                 location.reload();
    //             }
    //         });
    //     }
    // }
    // );
    var id = $('input[name=id]').val() === '' ? null : $('input[name=id]').val();
    $image = $('#image_replace').attr('src');
    var formData = {
        _token: $("input[name='_token']").val(),
        status: $('select[name="status"]').val() === '' ? '' : $('select[name="status"]').val(),
        title: $.trim($("input[name='title']").val()) === '' ? swal({
            title: 'Chưa nhập tiêu đề',
            type: 'error'
        }) : $("input[name='title']").val(),

        description: $("textarea[name='description']").val(),
        tags: $("input[name='tags']").val(),
        related: $("input[name='related']").val(),

        publish_date: $("input[name='publish_date']").val() === '' ? swal({
            title: 'Chưa nhập ngày đăng bài',
            type: 'error'
        }) : $("input[name='publish_date']").val(),

        publish_time: $("input[name='publish_time']").val() === '' ? swal({
            title: 'Chưa nhập thời gian đăng bài',
            type: 'error'
        }) : $("input[name='publish_time']").val(),

        youtube: $.trim($("textarea[name='youtube']").val()) === '' ? swal({
            title: 'Chưa nhập link nhúng video',
            type: 'error'
        }) : $("textarea[name='youtube']").val(),

        thumbnail: $image,

        seo_title: $("input[name='seo_title']").val() === '' ? $("input[name='title']").val() : $("input[name='seo_title']").val(),
        seo_meta: $("input[name='seo_meta']").val() === '' ? $("input[name='title']").val() : $("input[name='seo_meta']").val(),
        seo_description: $("textarea[name='seo_description']").val() === '' ? $("textarea[name='description']").val() : $("textarea[name='seo_description']").val(),
    };
    if (!formData.title || !formData.youtube || !formData.publish_time || !formData.publish_date) {
        return;
    }
    $('button[type=submit]').hide();
    $.ajax({
        type: "POST",
        url: '/media/recipe/edit/' + id,
        dataType: 'json',
        data: formData,
        success: function (res) {
            event.preventDefault();
            swal({title: res.msg, type: res.status}, function (isConfirm) {
                if (isConfirm) {
                    location.reload();
                }
            });
        }
    });
});

function deleteArticle(target) {
    var article_id = $(target).data('article_id');
    return swal({
        title: AdminCPLang.lang_1,
        type: "warning",
        showCancelButton: true,
        confirmButtonColor: "#DD6B55",
        confirmButtonText: AdminCPLang.lang_3,
        closeOnConfirm: false
    }, function () {
        $.ajax({
                type: "POST",
                url: '/media/article/delete',
                dataType: 'json',
                data: {id: article_id, select_action: 'Delete', type: 'ajax'}, // serializes the form's elements.
                success: function (res) {
                    swal({title: res.msg, type: res.status});
                    $('tr.post-item.post-id-' + article_id).remove();
                },
                error: function (resp) {
                    alert('Erorr!');
                }
            }
        );
    });
}

// review Article
function reviewArticle(target) {
    var article_id = $(target).data('article_id');
    loadModalContent('reviewArticleModalBody', '/media/article/review/' + article_id)
    $("#reviewArticleModal").modal('show');
}

// mo cua so chon list to web publish
$("#btn-status").on('click', function () {
    var status = $(this).attr('status');
    if (status == "off") {
        $(".modal-body #webPublish ").slideDown(300);
        $(".modal-body #viewReviewArticle ").slideUp(300);
        $(this).html('Back');
        $(this).attr('status', 'on');
    }
    if (status == "on") {
        $(".modal-body #webPublish ").slideUp(300);
        $(".modal-body #viewReviewArticle ").slideDown(300);
        $(this).html('Publish');
        $(this).attr('status', 'off');
    }
});
$('.modal-content .modal-header #close').click(function () {
    // click vao nut dong cua so thi dong bang publish
    $(".modal-body #webPublish ").slideDown(300);
    $(".modal-body #viewReviewArticle ").slideUp(300);
    $('.modal-content .modal-header #btn-status').html('Publish');
    $('.modal-content .modal-header #btn-status').attr('status', 'off');
});
$("html").click(function (e) {                          // click ra ngoai dong cua so thi dong bang publish
    if ($('.modal-content').is(":visible")) {
    } else {
        $('.modal-body #webPublish').slideDown(300);
        $('.modal-body #viewReviewArticle').slideUp(300);
        $('.modal-content .modal-header #btn-status').html('Publish');
        $('.modal-content .modal-header #btn-status').attr('status', 'off');
    }
});

// submit to list web publish
function summitToWebPublish(target) {
    var article_id = $(target).data('article_id');
    var type_to_publish = $(target).data('type');
    var list = [];
    $array = $('input[name="listWeb[]"]:checked').each(function () {
        if ($(this).is(':checked'))
            list.push($(this).val());
    });
    if (list == '') {
        alert("Error ! Choose Web Fail !");
    } else {
        $.ajax({
                type: "POST",
                url: '/media/article/submit',
                dataType: 'json',
                data: {id: article_id, st: type_to_publish, li: list},
                success: function (res) {
                    if (res.status == "error") {
                        swal({title: res.msg, type: res.status});
                    } else {
                        $('#reviewArticleModal').modal('hide');
                        swal({title: res.msg, type: res.status});
                        location.reload();
                    }
                },
                error: function (resp) {
                    alert('Erorr!');
                }
            }
        );
    }

}

// Active status Article
function activeArticle(target) {
    var article_id = $(target).data('article_id');
    var st = $(target).data('status');
    $.ajax({
            type: "POST",
            url: '/media/article/update-status',
            dataType: 'json',
            data: {id: article_id, st: st}, // serializes the form's elements.
            success: function (res) {
                $('#reviewArticleModal').modal('hide');
                swal({title: res.msg, type: res.status});
                setTimeout(function () {
                    window.location.reload(1);
                }, 2000);
            },
            error: function (resp) {
                alert('Erorr!');
            }
        }
    );
}
function makeid() {
    var text = "";
    var possible = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789";

    for (var i = 0; i < 5; i++)
        text += possible.charAt(Math.floor(Math.random() * possible.length));

    return text;
}

function RemoveClass($class) {
    $($class).remove();
}