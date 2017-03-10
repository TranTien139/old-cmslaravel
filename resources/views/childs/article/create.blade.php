@extends('layouts.master')
@section('main_content')
    <style>
        #article_panel > div > div > div > div.col-md-12 > div > span > span.selection > span {
            height: 34px;
            border-radius: 0px;
        }
    </style>
    <section class="content-header">
        <h1>Tạo bài viết video</h1>
        <ol class="breadcrumb">
            <li><a href="{!! url('/') !!}"><i class="fa fa-dashboard"></i> Home</a></li>
            <li class="active">{{ trans('menu.media_zone') }}</li>
            <li class="active">{{ trans('menu.create_article') }}</li>
        </ol>
    </section>
    <!-- Main content -->
    <section class="content">
        <div class="row">
            <form role="form" id="create_article_form">
                {{csrf_field()}}
                <div class="col-md-2">
                    <ul class="nav nav-tabs-custom nav-stacked" role="tablist">
                        <li role="presentation" class="active">
                            <a aria-controls="article_panel" role="tab" data-toggle="tab" href="#article_panel">
                                <i class="fa fa-bars"></i>
                                <strong>Bài viết </strong>
                            </a>
                        </li>
                        <li role="presentation">
                            <a aria-controls="seo_panel" role="tab" data-toggle="tab" href="#seo_panel">
                                <i class="fa fa-bars"></i>
                                <strong>Tùy chọn SEO</strong>
                            </a>
                        </li>

                    </ul>
                </div>
                <div class="col-md-7">
                    <div class="tab-content">
                        <div role="tabpanel" class="tab-pane active" id="article_panel">
                            <div class="box box-info">
                                <div class="">
                                    <div class="box-body">
                                        <div class="form-group">
                                            <label><i class="fa fa-list-ul"></i> Chọn Video</label>
                                            <select class="form-control select1" name="video_id"></select>
                                        </div>
                                        <div class="form-group">
                                            <label><i class="fa fa-list-ul"></i> {{ trans('article.title') }}<span
                                                        style="color:red;">*</span></label>
                                            <input type="name" class="form-control" name='title'
                                                   placeholder="Điền tiêu đề">
                                        </div>

                                        <div class="form-group">
                                            <label><i class="fa fa-paragraph"></i> Mô tả
                                            </label>
                                            <textarea class="form-control" name="description" rows="4"
                                                      id="description_article"
                                                      placeholder="{{ trans('article.description_ph') }}"></textarea>

                                            <small class="pull-right" style="margin-top: -25px;margin-right: 5px;">
                                                (words left: <span
                                                        id="word_left"></span>)
                                            </small>
                                        </div>
                                        <div class="form-group">
                                            <div class="form-group" id="tags_article_top">
                                                <label><i class="fa fa-tags"></i> {{ trans('article.tag') }}</label>
                                                <input type="name" class="form-control"
                                                       placeholder="{{ trans('article.tag_ph') }}" id="tags_article"
                                                       name="tags">
                                            </div>
                                            <div class="form-group" id="related_article_top">
                                                <label><i class="fa fa-tags"></i> Tin Liên Quan</label>
                                                <input type="name" class="form-control"
                                                       placeholder="{{ trans('article.tag_ph') }}" id="tags_article"
                                                       name="related">
                                            </div>
                                        </div><!-- /.box-body -->

                                    </div>

                                </div><!-- /.box-body -->
                            </div>
                        </div>
                        <div role="tabpanel" class="tab-pane" id="seo_panel">
                            <div class="box box-info">
                                <div class="box-body">
                                    <div class="form-group">
                                        <label><i class="fa fa-list-ul"></i> {{ trans('article.seo_title') }}</label>
                                        <input type="name" class="form-control" name="seo_title"
                                               placeholder="{{ trans('article.seo_title_ph') }}">
                                    </div>
                                    <div class="form-group">
                                        <label><i class="fa fa-list-ul"></i> {{ trans('article.seo_meta') }}</label>
                                        <input type="name" class="form-control" name="seo_meta"
                                               placeholder="{{ trans('article.seo_meta_ph') }}">
                                    </div>
                                    <div class="form-group">
                                        <label><i class="fa fa-paragraph"></i> {{ trans('article.seo_description') }}
                                        </label>
                                    <textarea class="form-control" name="seo_description" rows="4"
                                              name="seo_description"
                                              placeholder="{{ trans('article.seo_description_ph') }}"></textarea>
                                    </div>
                                </div><!-- /.box-body -->
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="box box-solid">
                        <div class="box-header with-border">
                            <i class="fa fa-image"></i>
                            <h3 class="box-title">Ảnh Đại Diện</h3>
                        </div>
                        <div class="box-body">
                            <a class="btn btn-block btn-danger fa fa-trash"
                               style="width:35px;position: relative;top:0px ;float: right;display: none;"
                               onclick="javascript:removeImage(this,1);">
                            </a>
                            {{--<i class="fa fa-trash" style="margin-left: 94%;cursor: pointer;display: none"></i>--}}
                            <img onclick="BrowseServer('id_of_the_target_input');" src="" id="image_replace"
                                 style="display:none;margin-top:-28px;cursor: pointer;max-height: 200px;width:100%;">
                            <div class="preview-placeholder" id="replace">
                                <div>
                                    <i class="fa fa-plus fa-2x"
                                       onclick="BrowseServer('id_of_the_target_input');"></i><br>
                                    <h4 class="text-muted">Bấm Vào! Chọn Ảnh</h4>
                                </div>
                            </div>
                        </div>
                        <input id="id_of_the_target_input" type="hidden" name="thumbnail"/>
                    </div>

                    @can('PublishArticle')
                    <div class="box box-solid">
                        <div class="box-header">
                            <i class="fa fa-image"></i>
                            <h3 class="box-title">Thời gian xuất bản</h3>
                        </div>
                        <div class="box-body">
                            <!-- Color Picker -->
                            <div class="form-group">
                                <label>Ngày Xuất Bản:</label>
                                <div class="input-group">
                                    <input type="text" class="form-control" id="datepicker" name="publish_date">
                                    <div class="input-group-addon add-on">
                                        <i class="fa fa-calendar" data-time-icon="icon-time"
                                           data-date-icon="icon-calendar"></i>
                                    </div>
                                </div>
                            </div>
                            <!-- /.form group -->


                            <!-- time Picker -->
                            <div class="bootstrap-timepicker">
                                <div class="bootstrap-timepicker-widget dropdown-menu">
                                    <table>
                                        <tbody>
                                        <tr>
                                            <td><a href="#" data-action="incrementHour"><i
                                                            class="glyphicon glyphicon-chevron-up"></i></a></td>
                                            <td class="separator">&nbsp;</td>
                                            <td><a href="#" data-action="incrementMinute"><i
                                                            class="glyphicon glyphicon-chevron-up"></i></a></td>
                                            <td class="separator">&nbsp;</td>
                                            <td class="meridian-column"><a href="#" data-action="toggleMeridian"><i
                                                            class="glyphicon glyphicon-chevron-up"></i></a></td>
                                        </tr>
                                        <tr>
                                            <td><span class="bootstrap-timepicker-hour">02</span></td>
                                            <td class="separator">:</td>
                                            <td><span class="bootstrap-timepicker-minute">15</span></td>
                                            <td class="separator">&nbsp;</td>
                                            <td><span class="bootstrap-timepicker-meridian">PM</span></td>
                                        </tr>
                                        <tr>
                                            <td><a href="#" data-action="decrementHour"><i
                                                            class="glyphicon glyphicon-chevron-down"></i></a></td>
                                            <td class="separator"></td>
                                            <td><a href="#" data-action="decrementMinute"><i
                                                            class="glyphicon glyphicon-chevron-down"></i></a></td>
                                            <td class="separator">&nbsp;</td>
                                            <td><a href="#" data-action="toggleMeridian"><i
                                                            class="glyphicon glyphicon-chevron-down"></i></a></td>
                                        </tr>
                                        </tbody>
                                    </table>
                                </div>
                                <div class="form-group">
                                    <label>Thời gian xuất bản</label>

                                    <div class="input-group">
                                        <input type="text" class="form-control timepicker" name="publish_time">

                                        <div class="input-group-addon">
                                            <i class="fa fa-clock-o"></i>
                                        </div>
                                    </div>
                                    <!-- /.input group -->
                                </div>
                                <!-- /.form group -->
                            </div>
                        </div>
                        <div class="box-body">
                            <!-- Color Picker -->
                            <div class="form-group">
                                <label>Trạng Thái:</label>
                                <div class="input-group">
                                    <select class="form-control" name="status">
                                        <option value="draft">Draft</option>
                                        <option value="verify">Verify</option>
                                        <option value="schedule">Schedule</option>
                                    </select>
                                    <div class="input-group-addon add-on">
                                        <i class="fa fa-warning" data-time-icon="icon-time"
                                           data-date-icon="icon-calendar"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- /.box-body -->
                    </div>
                    @else
                        <div class="box box-solid">
                            <div class="box-header">
                                <i class="fa fa-image"></i>
                                <h3 class="box-title">Chọn Trạng Thái Văn Bản</h3>
                            </div>
                            <div class="box-body">
                                <!-- Color Picker -->
                                <div class="form-group">
                                    <label>Trạng Thái:</label>
                                    <div class="input-group">
                                        <select class="form-control" name="status">
                                            <option value="draft">Draft</option>
                                            <option value="verify">Verify</option>
                                        </select>
                                        <div class="input-group-addon add-on">
                                            <i class="fa fa-warning" data-time-icon="icon-time"
                                               data-date-icon="icon-calendar"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- /.box-body -->
                        </div>
                        @endcan

                        <div class="box box-solid">
                            <div class="box-header with-border">
                                <i class="fa fa-book"></i>
                                <h3 class="box-title">Thuộc Chuyên Mục<span style="color:red;">*</span></h3>
                            </div><!-- /.box-header -->
                            <div class="box-body">
                                <div class="category">
                                    @if(isset($category))
                                        <?php cate_parent_checkbox($category, 0, '', 0) ?>
                                    @endif
                                    <input type="hidden" name="parent_id">
                                </div>
                            </div><!-- /.box-body -->
                        </div><!-- /.box -->

                        <button type="submit" class="btn btn-success btn-block btn-lg"><i class="fa fa-save"></i>
                            Lưu bài viết
                        </button>
            </form>
        </div>
        <style type="text/css">
            .slLevel, .slPrepTime {
                width: 100%;
                padding: 5px;
                border-radius: 5px;
                font-size: 16px;
                border: 1px solid #dadada;
            }

        </style>
    </section>
@stop

<script>
    // var text = document.getElementById("infoartist").value;
    // text = text.replace(/\r?\n/g, '<br />');
</script>
@section('custom_footer')
    <link rel="stylesheet" type="text/css" href="/uploadify/uploadify.css">
    <script type="text/javascript" src="/uploadify/jquery.uploadify.min.js"></script>
    <script>
        var max_len = 500;
        $(document).ready(function () {
            $.getScript("https://ajax.googleapis.com/ajax/libs/jquery/2.2.0/jquery.min.js");
            //$.getScript("{{ asset('dist/js/module/article.js') }}");
            $("textarea[name=description]").on('keyup', function () {
                var words = 0;
                if (this.value !== '') {
                    var words = this.value.match(/\S+/g).length;
                    if (words > max_len) {
                        // Split the string on first 200 words and rejoin on spaces
                        var trimmed = $(this).val().split(/\s+/, max_len).join(" ");
                        // Add a space at the end to keep new typing making new words
                        $(this).val(trimmed + " ");
                    }
                }
                $('#word_left').text(max_len - words);
            });

            //iCheck for checkbox and radio inputs
            $('input[type="checkbox"].minimal').iCheck({
                checkboxClass: 'icheckbox_square-blue',
                radioClass: 'iradio_square-blue',
                increaseArea: '20%' // optional
            });

            $(".category").slimScroll({
                height: '250px'
            });
        });

        initTinyMCE("#editor", "{{url('/')}}");

        var urlobj;
        function BrowseServer(obj) {
            urlobj = obj;
            OpenServerBrowser(
                    "{{url('/')}}" + '/filemanager/index.html',
                    screen.width * 0.7,
                    screen.height * 0.7);
        }
        function OpenServerBrowser(url, width, height) {
            var iLeft = (screen.width - width) / 2;
            var iTop = (screen.height - height) / 2;
            var sOptions = "toolbar=no,status=no,resizable=yes,dependent=yes";
            sOptions += ",width=" + width;
            sOptions += ",height=" + height;
            sOptions += ",left=" + iLeft;
            sOptions += ",top=" + iTop;
            var oWindow = window.open(url, "BrowseWindow", sOptions);
        }

        function SetUrl(url, width, height, alt) {
            document.getElementById(urlobj).value = url;
            if (urlobj == 'id_of_the_target_input') {
                $('#replace').hide();
                $('.fa-trash').show();
                $('#image_replace').attr('src', '{{env("MEDIA_PATH")}}' + url.replace('{{env("REPLACE_PATH")}}', ''));
                $('#image_replace').show();
                oWindow = null;
            } else if (urlobj == 'id_of_the_target_input_extra') {
                $('#replace_extra').hide();
                $('.fa-trash').show();
                $('#image_replace_extra').attr('src', '{{env("MEDIA_PATH")}}' + url.replace('{{env("REPLACE_PATH")}}', ''));
                $('#image_replace_extra').show();
                oWindow = null;
            } else {
                $('#text-folder').html(url);
            }
        }
        $('input[name="tags"]').tagEditor({
            autocomplete: {
                delay: 0,
                source: '/media/article/tag',
                minLength: 3,
                maxlength: 255,
                placeholder: "Enter Tags In Here!",
                position: {collision: 'flip'},
            }
        });
        $('input[name="related"]').tagEditor({
            autocomplete: {
                delay: 0,
                source: '/media/article/related',
                minLength: 3,
                maxlength: 255,
                placeholder: "Enter Tags In Here!",
                position: {collision: 'flip'},
            }
        });

        function removeImage(tag, index) {
            if (index == 1) {
                $('#image_replace').hide();
                $('#replace').show();
                $('#id_of_the_target_input').attr('value', '');
            } else {
                $('#image_replace_extra').hide();
                $('#replace_extra').show();
                $('#id_of_the_target_input_extra').attr('value', '');
            }
            $(tag).hide();

        }
        $('#get_url_image').on('click', function () {
            swal({
                        title: "Link Image!",
                        text: "Write Url Here:",
                        type: "input",
                        showCancelButton: true,
                        closeOnConfirm: false,
                        animation: "slide-from-top",
                        inputPlaceholder: "Write something"
                    },
                    function (inputValue) {
                        if (inputValue === false)
                            return false;
                        if (inputValue === "") {
                            swal.showInputError("You need to write something!");
                            return false
                        }
                        swal({title: 'Choose Image Success', type: 'success'}, function (isConfirm) {
                            if (isConfirm) {
                                $('#replace').hide();
                                $('.fa-trash').show();
                                $('#id_of_the_target_input').attr('value', inputValue);
                                $('#image_replace').attr('src', inputValue);
                                $('#image_replace').show();

                            }
                        });
                    });
        });
        $('#get_url_image_extra').on('click', function () {
            swal({
                        title: "Link Image!",
                        text: "Write Url Here:",
                        type: "input",
                        showCancelButton: true,
                        closeOnConfirm: false,
                        animation: "slide-from-top",
                        inputPlaceholder: "Write something"
                    },
                    function (inputValue) {
                        if (inputValue === false)
                            return false;
                        if (inputValue === "") {
                            swal.showInputError("You need to write something!");
                            return false
                        }
                        swal({title: 'Choose Image Success', type: 'success'}, function (isConfirm) {
                            if (isConfirm) {
                                $('#replace_extra').hide();
                                $('.fa-trash').show();
                                $('#id_of_the_target_input_extra').attr('value', inputValue);
                                $('#image_replace_extra').attr('src', inputValue);
                                $('#image_replace_extra').show();

                            }
                        });
                    });
        });
        $(function () {
            $(".timepicker").timepicker({
                showInputs: false
            });
            $('#datepicker').datepicker({
                autoclose: true
            });
        });
        $('.add_ingredients').on('click', function () {
            var str = makeid();
            $html = '<div class="ig_' + str + '"><br><input class="form-control" style="height: 30px;" name="ingredients[]" id="' + str + '" placeholder="Nguyên liệu"> <input class="" type="number" style="height: 30px;width:80px;margin-top:10px;" step="0.01" min="0" name="quanlity[]" placeholder="Số lượng"> <input class="" style="height: 30px;width:80px;" name="quanlity_type[]" placeholder="Đơn vị"><br><div onclick="RemoveClass(' + "'.ig_" + str + "'" + ')" style="margin-top:10px;cursor: pointer"> <i class="fa fa-close"></i>Xóa nguyên liệu </div></div>' +
                    '<script>' +
                    'createTag("' + str + '") ; ' +
                    '<\/script>';
            $('#append_ingredients').append($html);
            $(this).attr('data-id', str + 1);

        });
        function createTag(str) {
            $(document).ready(function () {
                $.getScript("{{ asset('dist/js/jquery-ui.js') }}", function () {
                    $.getScript("{{ asset('dist/js/caret.js') }}", function () {
                        $.getScript("{{ asset('dist/js/tag.js') }}", function () {
                            $('#' + str).tagEditor({
                                autocomplete: {
                                    delay: 0,
                                    source: '/media/article/search-ingredients/',
                                    minLength: 3,
                                    maxlength: 255,
                                    position: {collision: 'flip'},
                                },
                                maxTags: 1,
                                style_height: true
                            });
                        });

                    });
                });
            });

        }

        $('#create_article_form').submit(function (event) {
            event.preventDefault();
            $image = $('#image_replace').attr('src');
            $array = [];
            $('input[name="category[]"]').each(function () {
                if ($(this).prop('checked')) {
                    $array.push($(this).val());
                }
            });
            var category = $array.join(',');
            $list_tag = [];
            $('#tags_article_top .tag-editor.ui-sortable .tag-editor-tag').each(function () {
                $list_tag.push($(this).text());
            });
            var list_tag = $list_tag.join('||');

            $list_related = [];
            $('#related_article_top .tag-editor.ui-sortable .tag-editor-tag').each(function () {
                $list_related.push($(this).text());
            });
            var list_related = $list_related.join('||');

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
                publish_date: $("input[name='publish_date']").val(),
                parent_category: $('input[name=parent_id]').val() === '' ? swal({
                    title: 'Chưa Chọn Cờ Đỏ',
                    type: 'error'
                }) : $("input[name='parent_id']").val(),
                publish_time: $("input[name='publish_time']").val(),
                video_id: $.trim($('select[name="video_id"]').val()),
                thumbnail: $('#id_of_the_target_input').val(),
                category: category == '' ? swal({
                    title: 'Chọn Chuyên Mục',
                    type: 'error'
                }) : category,
                tags: list_tag,
                related: list_related,
                seo_title: $("input[name='seo_title']").val() === '' ? $("input[name='title']").val() : $("input[name='seo_title']").val(),
                seo_meta: $("input[name='seo_meta']").val() === '' ? $("input[name='title']").val() : $("input[name='seo_meta']").val(),
                seo_description: $("textarea[name='seo_description']").val() === '' ? $("textarea[name='description']").val() : $("textarea[name='seo_description']").val(),
            };

            if (!formData.title || !formData.category || !formData.parent_category) {
                return;
            }
            $('button[type=submit]').hide();
            $.ajax({
                type: "POST",
                url: '/media/article/create/',
                dataType: 'json',
                data: formData,
                success: function (res) {
                    event.preventDefault();
                    swal({title: res.msg, type: res.status}, function (isConfirm) {
                        if (isConfirm) {
                            location.reload();
                        }
                    });
                },
                error: function (err) {
                    alert('Có lỗi xảy ra');
                },
            });
        });
        $(document).ready(function () {
            $('.select1').select2(
                    {
                        ajax: {
                            url: "/video/search",
                            dataType: 'json',
                            delay: 250,
                            data: function (params) {
                                return {
                                    q: params.term, // search term
                                    page: params.page
                                };
                            },
                            processResults: function (data, params) {
                                params.page = params.page || 1;
                                return {
                                    results: data.items,
                                    pagination: {
                                        more: (params.page * 30) < data.total_count
                                    }
                                };
                            },
                            cache: true
                        },
                        escapeMarkup: function (markup) {
                            return markup;
                        }, // let our custom formatter work
                        minimumInputLength: 1,
                        templateResult: formatRepo, // omitted for brevity, see the source of this page
                        templateSelection: formatRepoSelection // omitted for brevity, see the source of this page
                    }
            );
        });
        function formatRepo(repo) {
            var markup = "<div class='select2-result-repository clearfix'>" +
                    "<div class='select2-result-repository__title'>" + repo.name + "</div></div>";
            return markup;
        }

        function formatRepoSelection(repo) {
            return repo.name || repo.text;
        }
    </script>
    <script type="text/javascript">
        $(function () {
            $('#file_upload').uploadify({
                'swf': '/uploadify/uploadify.swf',
                'uploader': 'http://convert.blogtamsudev.vn/upload',
                'onUploadSuccess': function (file, data, response) {
                    var link_video = JSON.parse(data).data.src;
                    $('input[name=media_path]').attr('value', link_video);
                    $('video').attr('src', '{{env('VIDEO_PATH')}}' + link_video);
                }
            });
        });
    </script>
@stop
