<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Ward;
use Illuminate\Http\Request;

class CategoryController extends Controller
{

    public function __construct(Request $request)
    {
        $this->_request = $request;
    }

    public function getIndex()
    {
        $this->authorize('ReadCategory');

        if (in_array('Admin', config('permission.ViewArticle'))) {
            $parentCats = Category::select('id', 'title', 'slug', 'status', 'created_at')
                ->where('id', '!=', 79)
                ->where('category.type', '=', 'Category')
                ->orderBy('created_at', 'desc')
                ->paginate(10);
            if (!empty($parentCats)) {
                return view('childs.category.index')
                    ->with('categories', $parentCats);
            } else {
                echo "No data";
            }
        }
    }


    public function getCreate()
    {
        $this->authorize('SaveCategory');
        $parentCats = Category::where('status', 1)->where('type', 'Category')->get();
        return view('childs.category.create')->with('parentCats', $parentCats);
    }

    public function postCreate(Request $request)
    {
        $this->authorize('SaveCategory');
        try {
            $data = \Input::only('title', 'description', 'parent_id');
            $category = new Category();
            if ($request->get('parent_id') != 0) {
                $category->parent_id = $request->get('parent_id');
            }
            $category->status = 1;
            $category->title = $request->get('title');
            $category->slug = str_slug($request->get('title'), '-');
            $category->desc = $request->get('description');
            $category->save();
            CategoryRedis($category->id);
            return json_encode(['status' => 'success', 'msg' => 'Thêm Category thành công']);
        } catch (\Exception $e) {
            return json_encode(['status' => 'error', 'msg' => $e->getMessage()]);
        }
    }

    public function getEdit($id)
    {
        $this->authorize('SaveCategory');
        $parentCats = Category::where('status', 1)->where('type', 'Category')->get();
        $category = Category::findOrFail($id);
        return view('childs.category.edit', compact('parentCats', 'category'));
    }

    public function postEdit($id, Request $request)
    {
        $this->authorize('SaveCategory');
        try {
            $data = \Input::only('title', 'description', 'parent_id', 'slug', 'status');
            $category = Category::findOrFail($id);
            if ($request->get('parent_id') != 0) {
                $category->parent_id = $request->get('parent_id');
            }
            $category->status = $request->get('status');;
            $category->title = $request->get('title');
            $category->slug = $request->get('slug');
            $category->desc = $request->get('description');
            $category->update();
            CategoryRedis($category->id);
            return json_encode(['status' => 'success', 'msg' => 'Sửa category thành công']);
        } catch (\Exception $e) {
            return json_encode(['status' => 'error', 'msg' => $e->getMessage()]);
        }
    }

    public function postUpdateStatus(Request $request)
    {
        $this->authorize('SaveCategory');
        try {
            $id = $request->get('id');
            $category = Category::find($id);
            if ($category->status == 1) {
                $category->status = 0;
                $data['name'] = 'Inactive';
            } else {
                $category->status = 1;
                $data['name'] = 'Active';
            }
            $data['status'] = $category->status;
            $category->save();
            CategoryRedis($category->id);
            return json_encode(['status' => 'success', 'msg' => 'Post Successfully', 'data' => $data]);
        } catch (\Exception $e) {
            return json_encode(['status' => 'error', 'msg' => $e->getMessage()]);
        }
    }

    function postDelete()
    {
        $this->authorize('SaveCategory');
        try {
            $request = request();
            $id = $request->get('id');
            $category = Category::find($id);
            $category->delete();
            CategoryRedis($id, 'delete');
            return json_encode(['status' => 'success', 'msg' => 'Post Successfully']);
        } catch (\Exception $e) {
            return json_encode(['status' => 'error', 'msg' => $e->getMessage()]);
        }
    }
}
