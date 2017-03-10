<?php

function getTopPublish()
{
    $category = \App\Models\Category::find(79);
    $articles = getByCategory($category, 50);
    return $articles;
}

function getTopModify($except = [])
{
    $top_modify = \App\Models\Article::select('title', 'slug', 'description', 'status', 'parent_category', 'published_at', 'type', 'video_id', 'created_at', 'updated_at')->where('status', 'publish')->whereRaw('created_at' != 'updated_at')->whereNotIn('id', $except)->orderBy('updated_at', 'desc')->take(20)->get();
    return $top_modify;
}

function ExportIA()
{

    $text_ads_bottom = "";
    $text_ads_bottom .= '		<figure class="op-interactive">';
    $text_ads_bottom .= '			  <iframe width="300" height="160" style="border:0; margin:0;" src="http://m.1news.video/ads/text_ads_bottom.html"></iframe>';
    $text_ads_bottom .= '		</figure>';


    $str_tinlq = "";
    $str_tinlq .= "<p>Tin liên quan </p>";
    $str_tinlq .= "<ul title=\"Tin liên quan\">";
    $str_tinlq .= '<li><a href="http://smarturl.it/downloadfeedy"> Các "thánh ăn" cài ngay app Feedy để biết thêm nhiều công thức nấu nướng độc đáo nè!</a></li>';
    $str_tinlq .= " </ul>";


    $top_publish = getTopPublish();
    $array_id = [];
    if (count($top_publish)) {
        foreach ($top_publish as $item) {
            $array_id[] = $item->id;
        }
    }

    $top_modify = getTopModify($array_id);

    $result = $top_publish->merge($top_modify);

    $today = date("Y-m-d H:i:s");

    $strxml = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n";
    $strxml .= "<rss version=\"2.0\" xmlns:content=\"http://purl.org/rss/1.0/modules/content/\">\n";
    $strxml .= "<channel>\n";
    $strxml .= "<title>m.1news.video</title>\n";
    $strxml .= "<link>http://m.1news.video/</link>\n";
    $strxml .= "<description>\n";
    $strxml .= "Tin tức video trong ngày. \n";
    $strxml .= "</description>\n";
    $strxml .= "<language>vi</language>\n";
    $strxml .= "<lastBuildDate>" . $today . "</lastBuildDate>\n";

    foreach ($result as $item) {
        $date_pub = isset($item->published_at) ? $item->published_at : '';
        $date_modify = isset($item->updated_at) ? $item->updated_at : '';
        $description = isset($item->description) ? $item->description : '';
        $creator = isset($item->getUser->name) ? $item->getUser->name : 'Anonymous';
        $title = isset($item->title) ? $item->title : '';
        $thumbnail = get_thumbnail($item->thumbnail);
        $link = genLink($item, 'chi-tiet', $item->title, $item->id);
        $link = 'http://1news.video' . $link;

        $video = isset($item->getVideo->short_code) ? $item->getVideo->short_code : '';
        $youtube = isset($item->getVideo->youtube) ? $item->getVideo->youtube : '';

        $pattern = "/\[shortcode-video.*img.*=.*[\"|\'](.*)[\"|\'].*url=.*[\"|\'](.*)[\"|\'].*\]/";
        //lấy content video
        if ($video != '') {
            $content = replacecontent_video_iframe($pattern, $video);
        } else {
            $content = contentVideoYT($youtube);
        }
        if ($item->parent_category != null) {
            $cate_name = GetCategoryFromCache($item->parent_category);
            $cate_name = $cate_name->title;
        } else {
            $cate_name = '';
        }

        $strxml .= "<item>\n";
        $strxml .= " 	<title><![CDATA[" . strip_tags($title) . " ]]></title>\n";
        $strxml .= " 	<link>" . $link . "</link>\n";
        $strxml .= "	<content:encoded>\n";
        $strxml .= "		<![CDATA[ \n";
        $strxml .= "		<!doctype html>\n";
        $strxml .= "		<html lang=\"vi\" prefix=\"op: http://media.facebook.com/op#\">\n";
        $strxml .= "		<head>\n";
        $strxml .= "		<meta charset=\"utf-8\">\n";
        $strxml .= "		<link rel=\"canonical\" href=\"$link\">\n";
        $strxml .= "		<meta property=\"op:markup_version\" content=\"v1.0\">\n";
        $strxml .= "		<meta property=\"fb:article_style\" content=\"default\">\n";
        $strxml .= "		<meta property=\"fb:use_automatic_ad_placement\" content=\"true\">\n";
        $strxml .= "		<meta property=\"fb:comments\" content=\"true\">\n";
        $strxml .= "		</head>\n";
        $strxml .= "		<body>\n";
        $strxml .= "	<article>\n";
        $strxml .= "      <header>\n";
        $strxml .= "<section class=\"op-ad-template\">\n";
        $strxml .= "<figure class=\"op-ad\">\n";
        $strxml .= "	<iframe width=\"300\" height=\"250\" style=\"border:0; margin:0;\" src=\"https://www.facebook.com/adnw_request?placement=1197435757038942_1199341436848374&amp;adtype=banner300x250&amp;adslot=1\"></iframe>	\n";
        $strxml .= "</figure>\n";
        $strxml .= "<figure class=\"op-ad\">\n";
        $strxml .= "	<iframe width=\"300\" height=\"250\" style=\"border:0; margin:0;\" src=\"https://www.facebook.com/adnw_request?placement=1197435757038942_1199341483515036&amp;adtype=banner300x250&amp;adslot=2\"></iframe>\n";
        $strxml .= "</figure>\n";
        $strxml .= "<figure class=\"op-ad\">\n";
        $strxml .= "	<iframe width=\"300\" height=\"250\" style=\"border:0; margin:0;\" src=\"https://www.facebook.com/adnw_request?placement=1197435757038942_1199341556848362&amp;adtype=banner300x250&amp;adslot=3\"></iframe>	\n";
        $strxml .= "</figure>\n";
        $strxml .= "</section>\n";
        $strxml .= "        <figure>\n";
        $strxml .= "          <img src=\"" . $thumbnail . "\">\n";
        $strxml .= "        </figure>\n";
        $strxml .= "        <h1>" . strip_tags($title) . "</h1>\n";
        $strxml .= "        <time class=\"op-published\" datetime=\"$date_pub\">" . Date("F j, Y, g:i a", strtotime($date_pub)) . "</time>\n";
        $strxml .= "        <time class=\"op-modified\" dateTime=\"$date_modify\">" . Date("F j, Y, g:i a", strtotime($date_modify)) . "</time>\n";
        if ($description != '') {
            $strxml .= "        <h2>" . strip_tags($description) . "</h2>\n";
        }
        if ($cate_name != '') {
            $strxml .= "        <h3 class=\"op-kicker\">" . strip_tags($cate_name) . "</h3>\n";
        }

        $strxml .= "      </header>\n";
        $strxml .= $str_tinlq;
        $strxml .= $content;
        $strxml .= $text_ads_bottom;
        $strxml .= "		<figure class=\"op-tracker\">\n";
        $strxml .= "			 <iframe>\n";
        $strxml .= "	<script type=\"text/javascript\">";
        $strxml .= "		var _gaq = _gaq || [];";
        $strxml .= "		_gaq.push(['_setAccount', 'UA-89846679-1']);";
        $strxml .= "		_gaq.push(['_trackPageview']);";
        $strxml .= "		_gaq.push(['b._setAccount', 'UA-89846679-4']);";
        $strxml .= "		_gaq.push(['b._trackPageview']);";
        $strxml .= "		(function() {";
        $strxml .= "		var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true; ga.src = ('https:' == document.location.protocol ? 'https://' : 'http://') + 'stats.g.doubleclick.net/dc.js'; ";
        $strxml .= "		var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);";
        $strxml .= "		})();";
        $strxml .= "	</script>";
        $strxml .= "			</iframe>\n";
        $strxml .= "		</figure>\n";
        $strxml .= "    </article>\n";
        $strxml .= "		</body>\n";
        $strxml .= "		</html>\n";
        $strxml .= "		]]>\n";
        $strxml .= "</content:encoded>\n";
        $strxml .= "	<guid isPermaLink=\"false\">" . $link . "</guid>\n";
        $strxml .= "	<description><![CDATA[" . strip_tags($description) . "]]></description>\n";
        $strxml .= "	<pubDate>$date_pub</pubDate>\n";
        $strxml .= "	<modDate>$date_modify</modDate>\n";
        $strxml .= "	<author>" . $creator . "</author>\n";
        $strxml .= "</item>\n";
    }
    $strxml .= "</channel>\n";
    $strxml .= "</rss>";

    $strxml = str_replace('<p><strong></strong></p>', '', $strxml);
    $strxml = str_replace('<figcaption> </figcaption>', '', $strxml);
    $strxml = str_replace('<figcaption></figcaption>', '', $strxml);

    $filename = '1new_ia_article.xml';
    Storage::disk('genfilehome')->put($filename, $strxml);
}

//lay cate gory from cache
function GetCategoryFromCache($_id)
{
    $id_key = 'categories_' . $_id;
    $redis = new \Redis();
    $redis->connect(env('REDIS_HOST'), env('REDIS_PORT'));
    $key = \Cache::get($id_key);

    $category_detail = $redis->lIndex('categories', (int)$key - 1);
    $category = json_decode($category_detail);
    $redis->close();
    return $category;
}

function replacecontent_video_iframe($pattern_video, $content_2)
{
    preg_match_all($pattern_video, $content_2, $matches);

    $link_img = "";
    $link_video = "";
    $figure_video = "";
    foreach ($matches[0] as $key => $match) {
        if (isset($matches[1][$key])) {
            $link_img = $matches[1][$key];
        }
        if (isset($matches[2][$key])) {
            $link_video = $matches[2][$key];
        }
        if (!empty($link_video)) {

            $length_url = strlen($link_video);
            $id_last = substr($link_video, $length_url - 12, -8);
            $figure_video .= '<figure class="op-interactive">';
            $figure_video .= "<iframe width=\"320\" height=\"180\" style=\"border:0; margin:0;\" src=\"http://m.1news.video/iaplayer.html?url=$link_video&image=$link_img&id=$id_last\"></iframe>";
            $figure_video .= '</figure><br>';

            $content_2 = str_replace($match, '', $content_2);
            $content_2 = $figure_video . $content_2;
        }
    }
    return $content_2;
}

function contentVideoYT($content_2)
{
    preg_match('/src="https\:\/\/www\.youtube(.+)\"\ /', $content_2, $match);

    if (isset($match[0])) {
        $video_ex = explode('"', $match[0]);
    }
    $yt = '';
    if (!empty($video_ex)) {
        foreach ($video_ex as $video) {
            $video_link = trim(substr($video, 0, 5));
            if ($video_link == 'https') {
                $yt = $video;
            }
        }
    }
    if ($yt != '' && str_contains($yt, 'youtube')) {
        $figure_video = "";
        $figure_video .= '<figure class="op-interactive">';
        $figure_video .= "<iframe frameborder=\"0\" allowfullscreen width=\"300\" height=\"180\" style=\"border:0; margin:0;\" src=\"$yt\" ></iframe>";
        $figure_video .= '</figure><br>';

        $content_2 = $figure_video . preg_replace('/<iframe.*?\/iframe>/i', '', $content_2);
    }
    return $content_2;
}

function changeimg($pattern_img, $content)
{
    //truong hop 2 ko co the p chi co the a
    preg_match_all($pattern_img, $content, $matches_img);
    $figure_image = "";
    $i = 0;
    foreach ($matches_img[0] as $key => $match_img) {

        $linkimg = $matches_img[1][$i];
        $linkimg = str_replace('"', '', $linkimg);
        $figure_image = '<p><figure data-feedback="fb:likes, fb:comments">';
        $figure_image .= "<img src='" . $linkimg . "' />";
        $figure_image .= "</figure><br></p>";

        $content = str_replace($match_img, $figure_image, $content);

        $i++;
    }
    return $content;
}


