@if(isset($articles))
    @foreach($articles as $article)
        <?php
        $creator = isset($article->getUser->name) ? $article->getUser->name : 'Anonymous';
        $str_cache = 'article_view_' . $article->id;
        $view = \Cache::has($str_cache) ? \Cache::get($str_cache) : rand(1000, 2000);
        ?>
        <div class=" slide">
            <a href="" class="imgVideo w100pt left">
                <button href="#" class="btPlay slit-action"><i class="fa fa-play" aria-hidden="true"></i>
                </button>
                {{--<div class="time">05:02</div>--}}
                <div class="bgR"></div>
                <img src="/dist/imgDemo/a2.jpg">
            </a>
            <a href="#" class="title"><h2>{{$article->title}}</h2></a>
            <p> Đăng bởi: <b>{{$creator}}</b></p>
            <p><b>{{number_format($view)}} </b>views ,<b> {{ time_elapsed_string($article->published_at)}} </p>
        </div>
    @endforeach
@endif