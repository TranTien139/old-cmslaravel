@extends('layouts.master')
@section('main_content')
    <style>
        #article_panel > div > div > div > div.col-md-12 > div > span > span.selection > span {
            height: 34px;
            border-radius: 0px;
        }
    </style>
    <section class="content-header">
        <h1>Tạo Video</h1>
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
                <div class="col-md-9">
                    <div class="tab-content">
                        <div role="tabpanel" class="tab-pane active" id="article_panel">
                            <div class="box box-info">
                                <div class="">
                                    <div class="box-body">
                                        <div class="form-group">
                                            <label><i class="fa fa-list-ul"></i> Upload File</label>
                                            <input type="file" name="file_upload" id="file_upload"/>
                                            <input type="hidden" name="media_path">
                                            <input type="hidden" name="posters">
                                            <video src=""></video>
                                        </div>


                                        <div class="form-group">
                                            <label><i class="fa fa-list-ul"></i> {{ trans('article.title') }}<span
                                                        style="color:red;">*</span></label>
                                            <input type="name" class="form-control" name='title'
                                                   placeholder="Điền tiêu đề">
                                        </div>


                                        <div class="form-group">
                                            <label><i class="fa fa-youtube"></i>Mã Nhúng video</label>
                                            <textarea type="text" class="form-control" rows="3" name="youtube" value=""
                                                      placeholder="Embed Youtube"></textarea>
                                        </div><!-- /.box-body -->
                                        <div class="form-group">
                                            <label><i class="fa fa-youtube"></i>ShortCode</label>
                                            <input type="text" class="form-control" rows="3" name="shortcode"
                                                   value="" disabled
                                                   placeholder="Embed Youtube"></input>
                                        </div><!-- /.box-body -->

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
                file_path: $('input[name=media_path]').val(),
                title: $.trim($("input[name='title']").val()) === '' ? swal({
                    title: 'Chưa nhập tiêu đề',
                    type: 'error'
                }) : $("input[name='title']").val(),
                youtube: $.trim($("textarea[name='youtube']").val()),
                thumbnail: $('#id_of_the_target_input').val(),
                short_code: $.trim($("input[name='shortcode']").val()),
                posters: $.trim($("input[name='posters']").val()),
            };

            if (!formData.title) {
                return;
            }
            $('button[type=submit]').hide();
            $.ajax({
                type: "POST",
                url: '/video/create/',
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

    </script>
    <script type="text/javascript">
        $short_code = "[shortcode-video " +
                "img='http://img.video.blogtamsu.vn/2016/12/03/banhca.jpg' " +
                "url='http://video.blogtamsu.vn/2016/12/03/10/banhca-1480735734_new.mp4']";
        $(function () {
            $('#file_upload').uploadify({
                'swf': '/uploadify/uploadify.swf',
                'uploader': '{{env("CONVERT_LINK")}}',
                'onUploadSuccess': function (file, data, response) {
                    var posters = JSON.parse(data).data.img;
                    var link_video = JSON.parse(data).data.src;

                    $short_code = "[shortcode-video " +
                            "img='" + '{{env('VIDEO_PATH')}}' + JSON.parse(data).data.img[0] +
                            "' " +
                            "url='" + '{{env('VIDEO_PATH')}}' + link_video +
                            "']";

                    $('input[name=media_path]').attr('value', link_video);
                    $('video').attr('src', '{{env('VIDEO_PATH')}}' + link_video);
                    $('input[name=shortcode]').val($short_code);
                    $('input[name=posters]').attr('value', posters);
                }
            });
        });
    </script>
@stop
