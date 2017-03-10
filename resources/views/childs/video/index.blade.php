@extends('layouts.master')

@section('main_content')
    <section class="content-header">
        <h1 style="margin-bottom: 10px;">{{ trans('menu.list_articles') }}</h1>
        <a href="{{ URL::to('video/create') }}" class="btn btn-success">Tạo Video</a>
        <ol class="breadcrumb">
            <li><a href="{!! url('/') !!}"><i class="fa fa-dashboard"></i> Home</a></li>
            <li class="active">{{ trans('menu.media_zone') }}</li>
            <li class="active">{{ trans('menu.list_articles') }}</li>
        </ol>
    </section>
    <section class="content">
        <div>
            <div style="padding-bottom: 5px; margin-bottom: 5px; border-bottom: 1px solid #ddd">
                <form method="get" action="/video" autocomplete="off" role="form" class="form-inline">
                    <div class="form-group">
                        <input type="text" class="form-control search_top" name="key" id="key"
                               value="{{old('key')}}"
                               autocomplete="off" placeholder="{{ trans('article.search_by_title') }}"
                               style="width: 150px;">
                    </div>
                    <button type="submit" class="btn btn-danger">Tìm kiếm</button>
                </form>
            </div>
            <div class="post-container">
                <div class="box box-solid">
                    <div class="box-body no-padding">
                        <div>
                            <table class="table table-striped">
                                <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Ảnh Đại Diện</th>
                                    <th>{{ trans('article.title') }}</th>
                                    <th>{{ trans('article.author') }}</th>
                                    <th>{{ trans('article.created_at') }}</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($videos as $video)
                                    <tr id="" class="post-item post-id-{{$video['id']}}">
                                        <td><p>{{ $video->id }}</p></td>
                                        <td><p>
                                                @if (!str_contains($video->thumbnail, 'http') == true)
                                                    <img style="width:200px;" src="{{ env('MEDIA_PATH') . str_replace(env('REPLACE_PATH') , '' , $video->thumbnail )}}">
                                                @else
                                                    <img style="width:200px;" src="{{ $video->thumbnail }}">
                                                @endif
                                            </p>
                                        </td>
                                        <td>
                                            <p>{{ $video['title'] }}</p>
                                            <p>
                                                <a href="{!! url('video/edit/'.$video->id) !!}">Edit</a>
                                                &nbsp;&nbsp;
                                                <a href="{!! url('video/delete/'.$video->id) !!}"><span>Move to trash</span></a>
                                            </p>
                                        </td>
                                        <td><p>{{ $video->creator }}</p></td>
                                        <td><p>{{ $video->created_at }}</p></td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                            <div class="pull-right">
                                {!! $videos->render() !!}
                            </div>
                        </div>
                    </div>
                </div>
                <style>
                    table tr td p span {
                        cursor: pointer;
                        color: #3c8dbc;
                    }

                    table tr td p span:hover {
                        color: #72afd2;
                    }
                </style>


            </div>
        </div>
    </section>

    <div class="modal fade" id="reviewArticleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel">
        <div style="width: 70%;" class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button id="close" type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                aria-hidden="true">&times;</span></button>
                    <h2 class="col-md-9 modal-title" id="exampleModalLabel">{{ trans('article.review_article') }}</h2>
                    <button id="btn-status" style="width:130px;margin-left: 60px;" status="off" id="summitToPublish"
                            class="btn btn-success" type="button">
                        {{ trans('article.publish') }}
                    </button>
                </div>
                <div class="modal-body" id="reviewArticleModalBody">

                </div>
            </div>
        </div>
    </div>
@stop

@section('custom_header')
    <link rel="stylesheet" href="{{ asset('plugins/daterangepicker/daterangepicker-bs3.css') }}">
    <link href="{{ asset('plugins/iCheck/minimal/blue.css') }}" rel="stylesheet">
@stop

@section('custom_footer')
    <script src="{{ asset('plugins/datatables/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('plugins/datatables/dataTables.bootstrap.min.js') }}"></script>
    <script src="{{ asset('dist/js/module/article.js') }}"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.10.2/moment.min.js"></script>
    <script src="{{ asset('plugins/daterangepicker/daterangepicker.js') }}"></script>
    <script src="{{ asset('plugins/iCheck/icheck.js') }}"></script>
    <script src="{{ asset('plugins/iCheck/icheck.min.js') }}"></script>
    <script>
        function deleteArt(id, check) {
            if (check == 1) {
                link = "{{URL::to('/media/article/delete/')}}";
            } else if (check == 2) {
                link = "{{URL::to('/media/recipe/delete/')}}";
            } else if (check == 3) {
                link = "{{URL::to('/media/blogs/delete/')}}";
            }
            $.ajax({
                type: 'POST',
                url: link,
                data: 'id=' + id,
                success: function (obj) {
                    if (obj !== null) {
                        obj = $.parseJSON(obj);
                        if (obj.status === 'success') {
                            location.reload();
                        }
                    }
                },
                error: function (a, b, c) {
                }
            });
        }

        function publishArt(id, check) {
            if (check == 1) {
                link = "{{URL::to('/media/article/publish/')}}";
            } else if (check == 2) {
                link = "{{URL::to('/media/recipe/publish/')}}";
            } else if (check == 3) {
                link = "{{URL::to('/media/blogs/publish/')}}";
            }
            $.ajax({
                type: 'POST',
                url: link,
                data: 'id=' + id,
                success: function (obj) {
                    if (obj !== null) {
                        obj = $.parseJSON(obj);
                        if (obj.status === 'success') {
                            location.reload();
                        }
                    }
                },
                error: function (a, b, c) {
                }
            });
        }

        function trashArt(id, check, status) {
            if (check == 1) {
                if (status === 1) {
                    link = "{{URL::to('/media/article/untrash/')}}";
                } else {
                    link = "{{URL::to('/media/article/trash/')}}";
                }
            } else if (check == 2) {
                if (status === 1) {
                    link = "{{URL::to('/media/recipe/untrash/')}}";
                } else {
                    link = "{{URL::to('/media/recipe/trash/')}}";
                }
            } else if (check == 3) {
                if (status === 1) {
                    link = "{{URL::to('/media/blogs/untrash/')}}";
                } else {
                    link = "{{URL::to('/media/blogs/trash/')}}";
                }
            }
            $.ajax({
                type: 'POST',
                url: link,
                data: 'id=' + id,
                success: function (obj) {
                    if (obj !== null) {
                        obj = $.parseJSON(obj);
                        if (obj.status === 'success') {
                            location.reload();
                        }
                    }
                },
                error: function (a, b, c) {
                }
            });
        }

        function draftArt(id, check) {
            if (check === 1) {
                link = "{{URL::to('/media/article/draft/')}}";
            } else if (check === 2) {
                link = "{{URL::to('/media/recipe/draft/')}}";
            } else if (check === 3) {
                link = "{{URL::to('/media/blogs/draft/')}}";
            }
            $.ajax({
                type: 'POST',
                url: link,
                data: 'id=' + id,
                success: function (obj) {
                    if (obj !== null) {
                        obj = $.parseJSON(obj);
                        if (obj.status === 'success') {
                            location.reload();
                        }
                    }
                },
                error: function (a, b, c) {
                }
            });
        }

        function verifyArt(id, check, status) {
            if (check == 1) {
                if (status === 1) {
                    link = "{{URL::to('/media/article/unverify/')}}";
                } else {
                    link = "{{URL::to('/media/article/verify/')}}";
                }
            }

            $.ajax({
                type: 'POST',
                url: link,
                data: 'id=' + id,
                success: function (obj) {
                    if (obj !== null) {
                        obj = $.parseJSON(obj);
                        if (obj.status === 'success') {
                            location.reload();
                        }
                    }
                },
                error: function (a, b, c) {
                }
            });
        }


        //range date
        $(function () {
            var start = moment().subtract(29, 'days');
            var end = moment();

            function cb(start, end) {
                $('#reportrange span').html(start.format('D/M/YYYY') + ' - ' + end.format('D/M/YYYY'));
                $('input[name="start_date"]').attr('value', Math.floor(start / 1000) + 7 * 3600);
                $('input[name="end_date"]').attr('value', Math.floor(end / 1000) + 7 * 3600);
            }

            $('#reportrange').daterangepicker({
                language: 'vi',
                startDate: start,
                endDate: end,
                ranges: {
                    'tất cả các ngày': [946684800000, 4102444800000],
                    'ngày hôm nay': [moment(), moment()],
                    'ngày hôm qua': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                    '7 ngày gần đây': [moment().subtract(6, 'days'), moment()],
                    '30 ngày gần đây': [moment().subtract(29, 'days'), moment()],
                    'thấng này': [moment().startOf('month'), moment().endOf('month')],
                    'tháng trước': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
                }
            }, cb);
            cb(start, end);
        });
    </script>
@stop