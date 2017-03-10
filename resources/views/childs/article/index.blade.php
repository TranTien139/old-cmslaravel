@extends('layouts.master')

@section('main_content')
    <section class="content-header">
        <h1 style="margin-bottom: 10px;">{{ trans('menu.list_articles') }}</h1>
        <a href="{{ URL::to('media/article/create') }}" class="btn btn-success">Tạo bài viết video</a>
        <ol class="breadcrumb">
            <li><a href="{!! url('/') !!}"><i class="fa fa-dashboard"></i> Home</a></li>
            <li class="active">{{ trans('menu.media_zone') }}</li>
            <li class="active">{{ trans('menu.list_articles') }}</li>
        </ol>
    </section>
    <section class="content">
        <div>
            <div style="padding-bottom: 5px; margin-bottom: 5px; border-bottom: 1px solid #ddd">
                <form method="get" action="/media/article" autocomplete="off" role="form" class="form-inline">
                    <div class="form-group">
                        <input type="text" class="form-control search_top" name="key" id="key"
                               value="{{old('key')}}"
                               autocomplete="off" placeholder="{{ trans('article.search_by_title') }}"
                               style="width: 150px;">
                    </div>
                    <div class="form-group">
                        <select class="form-control" name="category" data-live-search="true" data-width="120px">
                            <option value="">none</option>
                            <?php cate_parent($category, 0, $str = "", $cate_id); ?>
                        </select>
                    </div>

                    <div class="form-group">

                        <div id="reportrange"
                             style="background: #fff; cursor: pointer; padding: 5px 10px; border: 1px solid #ccc; width: 100%">
                            <i class="glyphicon glyphicon-calendar fa fa-calendar"></i>&nbsp;
                            <span></span> <b class="caret"></b>
                        </div>
                        <input type="hidden" name="start_date" id="start_date"
                               value="<?php echo isset($start_date) ? $start_date : '' ?>">
                        <input type="hidden" name="end_date" id="end_date"
                               value="<?php echo isset($end_date) ? $end_date : '' ?>">

                    </div>
                    <div class="form-group">
                        <select class="form-control" name="status" data-live-search="true" style="width:140px;">
                            <option value="">{{ trans('article.status') }}</option>
                            <option @if(isset($status) && $status == "publish"){{"selected"}}@endif value="publish">{{ trans('article.published') }}</option>
                            <option @if(isset($status) && $status == "scheduled"){{"selected"}}@endif value="scheduled">{{ trans('article.scheduled') }}</option>
                            <option @if(isset($status) && $status == "draft"){{"selected"}}@endif value="draft">{{ trans('article.draft') }}</option>
                            <option @if(isset($status) && $status == "pending"){{"selected"}}@endif value="pending">{{ trans('article.pending') }}</option>
                            <option @if(isset($status) && $status == "trash"){{"selected"}}@endif value="trash">
                                Trash
                            </option>
                        </select>
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
                                    <th>{{ trans('article.title') }}</th>
                                    <th>{{ trans('article.author') }}</th>
                                    <th>{{ trans('article.category') }}</th>
                                    <th>{{ trans('article.status') }}</th>
                                    <th>{{ trans('article.created_at') }}</th>
                                    <th>Thời gian Đăng</th>
                                    <th>{{ trans('article.approved_by') }}</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($articles as $article)
                                    <tr id="" class="post-item post-id-{{$article['id']}}">
                                        <td>
                                            <p>{{ $article['title'] }}</p>
                                            <p>
                                                <a href="{!! url('media/article/edit',$article->id) !!}">Edit</a> &nbsp;&nbsp;
                                                <span onclick="javacript:verifyArt('{{$article->id}}',1, 0);">Verify</span>
                                                &nbsp;&nbsp;
                                                <span onclick="javacript:trashArt('{{$article->id}}', 1, 0);">Move to trash</span>
                                                &nbsp;&nbsp;
                                                <span onclick="javacript:publishArt('{{$article->id}}', 1);">Publish</span>
                                                &nbsp;&nbsp;
                                                <span onclick="javacript:draftArt('{{$article->id}}', 1);">Draft</span>
                                            </p>
                                        </td>
                                        <td><p>{{ $article['creator'] }}</p></td>
                                        <td><p>
                                                <?php $cate = DB::table('category')->join('article_category', 'article_category.category_id', '=', 'category.id')->where('article_category.article_id', $article->id)->select('category.title')->get();
                                                foreach ($cate as $key => $value) {
                                                    echo $value->title;
                                                    echo ' - ';
                                                }
                                                ?>
                                            </p></td>

                                        <td><p>{{ $article['status'] }}</p></td>
                                        <td><p>{{ $article['created_at'] }}</p></td>
                                        <td><p>{{ $article['published_at'] }}</p></td>
                                        <td><p>{{ $article['approve_by'] }}</p></td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                            <div class="pull-right">
                                <?php
                                $link_limit = 9;
                                $video = $articles;
                                ?>
                                @if ($video->lastPage() > 1)
                                    <ul class="pagination">
                                        <li class="{{ ($video->currentPage() == 1) ? ' disabled' : '' }}">
                                            <a href="<?php curPageURL(); ?>&page=1">First</a>
                                        </li>
                                        @for ($i = 1; $i <= $video->lastPage(); $i++)
                                            <?php
                                            $half_total_links = floor($link_limit / 2);
                                            $from = $video->currentPage() - $half_total_links;
                                            $to = $video->currentPage() + $half_total_links;
                                            if ($video->currentPage() < $half_total_links) {
                                                $to += $half_total_links - $video->currentPage();
                                            }
                                            if ($video->lastPage() - $video->currentPage() < $half_total_links) {
                                                $from -= $half_total_links - ($video->lastPage() - $video->currentPage()) - 1;
                                            }
                                            ?>
                                            @if ($from < $i && $i < $to)
                                                <li class="{{ ($video->currentPage() == $i) ? ' active' : '' }}">
                                                    <a href="<?php curPageURL(); ?>&page={!! $i !!}">{{ $i }}</a>
                                                </li>
                                            @endif
                                        @endfor
                                        <li class="{{ ($video->currentPage() == $video->lastPage()) ? ' disabled' : '' }}">
                                            <a href="<?php curPageURL(); ?>&page={!! $video->lastPage() !!}">Last</a>
                                        </li>
                                    </ul>
                                @endif
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
            <?php if(empty($start_date) && empty($end_date)){ ?>
                var start = moment('01/01/2000');
                var end = moment('01/01/2100');
            <?php }else{ ?>
                var start = moment('<?php echo date('m/d/Y',strtotime($start_date)) ?>');
                var end = moment('<?php echo date('m/d/Y',strtotime($end_date)) ?>');
            <?php } ?>

            function cb(start, end) {
                <?php if(empty($start_date) && empty($end_date)){ ?>
                $('#reportrange span').html(start.format('D/M/YYYY') + ' - ' + end.format('D/M/YYYY'));
                $('input[name="start_date"]').attr('value', Math.floor(start / 1000));
                $('input[name="end_date"]').attr('value', Math.floor(end / 1000));
                <?php }else{ ?>

                    $('#reportrange span').html('<?php echo date('d/m/Y',strtotime($start_date)) ?>' + ' - '+'<?php echo date('d/m/Y',strtotime($end_date)) ?>');
                    $('input[name="start_date"]').attr('value', Math.floor(start / 1000));
                    $('input[name="end_date"]').attr('value', Math.floor(end / 1000));

                <?php } ?>
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