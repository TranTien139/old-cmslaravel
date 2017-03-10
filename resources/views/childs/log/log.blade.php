@extends('layouts.master')
@section('main_content')
<style type="text/css">

.scrollbar
{
    height: 600px;
    background: #F5F5F5;
    overflow-y: scroll;
    overflow-x:hidden; 
    margin-bottom: 25px;
}

.force-overflow
{
    min-height: 900px;
}


/*
 *  STYLE 5
 */

#style-5::-webkit-scrollbar-track
{
    -webkit-box-shadow: inset 0 0 5px rgba(0,0,0,0.3);
    background-color: #F5F5F5;
}

#style-5::-webkit-scrollbar
{
    width: 5px;
    background-color: #F5F5F5;
}

#style-5::-webkit-scrollbar-thumb
{
    background-color: #0ae;
    
    background-image: -webkit-gradient(linear, 0 0, 0 100%,
                       color-stop(.5, rgba(255, 255, 255, .2)),
                       color-stop(.5, transparent), to(transparent));
}


</style>

<section class="content">
<div class="">
    <h2>Lịch sử</h2>
</div>
<div class="head_log_file">
    <div class="row">
        <div class="col-sm-2">Tên thao tác</div>
        <div class="col-sm-2">Thuộc danh mục</div>
        <div class="col-sm-2">Id của bài viết</div>
        <div class="col-sm-2">User thực hiện</div>
        <div class="col-sm-2">Thời gian</div>
        <div class="col-sm-2"></div>
    </div>
</div>
    <div class="scrollbar" id="style-5">
        <div class="force-overflow">
            <?php echo $content; ?>
        </div>
    </div>
</section>
@stop

@section('custom_footer')

@stop
