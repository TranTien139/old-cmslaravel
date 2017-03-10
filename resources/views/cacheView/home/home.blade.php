<div id="contentRight">
    <!--videoHot-->
    <div id="videoHot" class="contentRight ">
        <div class="col-lg-12 listVideo">
            <div class="lable-big w100pt left">
                <a href="#"> <i class="fa fa-star" aria-hidden="true"></i>
                    <h1>{{$category_clip_hot->title}}</h1></a>
            </div>
            <div class="list-product ">
                <ul class="listVideoMore col-lg-12">
                    @include('cacheView.components.article' , ['articles' => $article_clip_hot ])
                </ul>
            </div>

        </div>
    </div>
    <!--contentRight-->
    <div class="contentRight bgf1">
        <div class="col-lg-12 listVideo">
            <div class="lable-list w100pt left">
                <a href="#"> <i class="fa fa-star" aria-hidden="true"></i>
                    <h1>{{$category_hai_nuoc_ngoai->title}}</h1></a>
            </div>
            <div class="slider1 listVideoMore col-lg-12">
                @include('cacheView.components.article' , ['articles' => $article_clip_hot ])
            </div>
        </div>
    </div>
    <!--contentRight-->
    <div class="contentRight bgf1">
        <div class="col-lg-12 listVideo">
            <div class="lable-list w100pt left">
                <a href="#"> <i class="fa fa-star" aria-hidden="true"></i>
                    <h1>{{$category_hai_viet_nam->title}}</h1></a>
            </div>
            <div class="slider1 listVideoMore col-lg-12">
                @include('cacheView.components.article' , ['articles' => $article_clip_hot ])
            </div>


        </div>
    </div>
    <!--footer-->
    @include('cacheView.partials.footer')
</div>