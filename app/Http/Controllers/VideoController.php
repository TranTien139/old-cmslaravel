<?php

namespace App\Http\Controllers;

use App\Models\Video;
use App\Repositories\Article\ArticleInterface;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Models\Category;

class VideoController extends Controller
{
    function __construct(ArticleInterface $articleInterface)
    {
        parent::__construct();
        $this->_article = $articleInterface;
    }

    function getIndex()
    {
        $videos = Video::orderBy('created_at', 'desc');

        if (request()->has('key')) {
            $videos = $videos->where('title', 'like', '%' . request()->get('key') . '%');
        }
        $videos = $videos->paginate(50);

        return view('childs.video.index')->with('videos', $videos);
    }

    function getCreate()
    {
        return view('childs.video.create');
    }

    function getEdit($id = null)
    {
        $video = Video::findorFail($id);
        return view('childs.video.edit')->with('video', $video);
    }

    function postCreate()
    {
        try {
            $video = new Video();
            $data = request()->except('_token');
            foreach ($data as $k => $v) {
                $video->$k = $v;
            }
            $video->creator = auth()->user()->email;
            $video->save();
            return json_encode(['status' => 'success', 'msg' => 'Lưu bài viết thành công']);
        } catch (\Exception $e) {
            return response()->json($e);
        }
    }

    function postEdit()
    {
        try {
            $video = Video::find(request()->get('id'));
            $data = request()->except('_token');
            foreach ($data as $k => $v) {
                $video->$k = $v;
            }
            $video->save();
            return json_encode(['status' => 'success', 'msg' => 'Lưu bài viết thành công']);
        } catch (\Exception $e) {
            return response()->json($e);
        }
    }

    function getDelete($id)
    {
        try {
            $video = Video::findorFail($id);
            $video->delete();
            return redirect()->back();
        } catch (\Exception $e) {
            return redirect()->back();
        }
    }

    function getSearch()
    {
        $query = request()->get('q');
        $teams = Video::where(function ($q) use ($query) {
            $q->where('title', 'like', '%' . $query);
            $q->OrWhere('title', 'like', $query . '%');
            $q->OrWhere('title', 'like', '%' . $query . '%');
            $q->OrWhere('title', $query);
            $q->OrWhere('title', 'like', $query . ' %');
            $q->OrWhere('title', 'like', '% ' . $query . ' %');
        })->get();
        $id_team = [];
        foreach ($teams as $item) {
            $id_team [] = [
                'id' => $item->id,
                'name' => $item->title,
            ];
        }
        $id_teams = array_unique($id_team);
        return response()->json(['items' => $id_teams]);
    }

}
