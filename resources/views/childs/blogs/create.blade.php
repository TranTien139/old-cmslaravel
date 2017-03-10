@extends('layouts.master')

@section('main_content')
    <style>
        #article_panel > div > div > div > div.col-md-12 > div > span > span.selection > span {
            height: 34px;
            border-radius: 0px;
        }
    </style>
    <section class="content-header">
        <h1>Tạo bài viết Video</h1>
        <ol class="breadcrumb">
            <li><a href="#"><i class="fa fa-dashboard"></i> Trang chủ</a></li>
            <li class="active">{{ trans('menu.media_zone') }}</li>
        </ol>
    </section>
    <!-- Main content -->
    <section class="content">
        <div class="row">
            <form role="form" id="blog_form" method="post">
                {{csrf_field()}}
                <div class="col-md-2">
                    <ul class="nav nav-tabs-custom nav-stacked" role="tablist">
                        <li role="presentation" class="active">
                            <a aria-controls="article_panel" role="tab" data-toggle="tab" href="#article_panel">
                                <i class="fa fa-bars"></i>
                                <strong>Bài viết</strong>
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
                                <div class="box-body">
                                    <div class="">
                                        <div class="form-group">
                                            <label><i class="fa fa-list-ul"></i> {{ trans('article.title') }}</label>
                                            <input type="name" class="form-control" name='title'
                                                   placeholder="Điền tiêu đề">
                                        </div>
                                        <div class="form-group">
                                            <label><i class="fa fa-list-ul"></i> {{ trans('article.title_extra') }}
                                            </label>
                                            <input type="name" class="form-control" name='title_extra'
                                                   placeholder="Điền thêm tiêu đề">
                                        </div>
                                        <div class="form-group">
                                            <label><i class="fa fa-paragraph"></i> Mô Tả
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
                                            <label><i class="fa fa-paragraph"></i> Nội Dung
                                            </label>
                                            <textarea class="form-control" name="content" id="editor"></textarea>
                                        </div>
                                        <div class="form-group">
                                            <label><i class="fa fa-tags"></i> {{ trans('article.tag') }}</label>
                                            <input type="name" class="form-control"
                                                   placeholder="{{ trans('article.tag_ph') }}" id="tags_article"
                                                   name="tags">
                                        </div>
                                    </div><!-- /.box-body -->
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
                        </div><!-- /.box-header -->
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

                        </div><!-- /.box-body -->
                        <input id="id_of_the_target_input" type="hidden" name="thumbnail"/>
                    </div><!-- /.box -->
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
                                        <option value="">Schedule</option>
                                        <option value="pending">Verify</option>
                                    </select>
                                    <div class="input-group-addon add-on">
                                        <i class="fa fa-warning" data-time-icon="icon-time"
                                           data-date-icon="icon-calendar"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
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
                        <!-- /.box-body -->
                    </div>
                    <button type="submit" class="btn btn-success btn-block btn-lg"><i class="fa fa-save"></i>
                        Lưu bài viết
                    </button>
            </form>
        </div>
    </section>
@stop
{{--Script Import --}}
@section('custom_footer')

    <script>
        var max_len = 200;
        $(document).ready(function () {


            $.getScript("https://ajax.googleapis.com/ajax/libs/jquery/2.2.0/jquery.min.js");
            $.getScript("{{ asset('dist/js/module/article.js?v4') }}");
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
                delay: 0, // show suggestions immediately
                source: '/media/article/tag/',
                minLength: 3,
                maxlength: 255,
                placeholder: "Enter Tags In Here!",
                position: {collision: 'flip'},
            }
        });
        $('input[name="related"]').tagEditor({
            autocomplete: {
                delay: 0, // show suggestions immediately
                source: '/media/article/related/',
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

    </script>
@stop
{{--End Script--}}