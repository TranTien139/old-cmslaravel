<?php

namespace App\Http\Middleware;

use Auth;
use Menu;
use Closure;
use Illuminate\Support\Facades\Lang;

class LeftSidebarMiddleware {

    public $articles_unapprove;

    public function __construct() {
        $this->articles_unapprove = 20;
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next) {
        $index_1st = $this->articles_unapprove;
        if (Auth::check()) {

            Menu::make('left_navbar', function ($menu) use ($index_1st) {
                $menu->style('navigation');


                $menu->add([
                    'title' => trans('menu.main_navi'),
                    'header' => true
                ]);
                
                $menu->add([
                    'url' => '/home',
                    'title' => 'Trang Chủ',
                    'icon' => 'fa fa-bank'
                ]);

                $menu->add([
                    'url' => '/article',
                    'title' => 'Quản Lý Bài Viết',
                    'icon' => 'fa fa-database'
                ]);

                // $menu->add([
                //     'url' => '/member/article',
                //     'title' => 'Quản Lý Bài Viết Thành Viên',
                //     'icon' => 'fa fa-database'
                // ]);

                $menu->add([
                    'url' => '/category',
                    'title' => 'Quản Lý chuyên mục',
                    'icon' => 'fa fa-feed'
                ]);

                $menu->add([
                    'url' => '/video',
                    'title' => 'Quản Lý Video',
                    'icon' => 'fa fa-youtube'
                ]);

                if (auth()->user()->user_type == 'Admin' || auth()->user()->user_type == 'Editor') :
                    $menu->add([
                        'url' => '/questions',
                        'title' => 'Danh Sách Bình Luận',
                        'icon' => 'fa fa-comments'
                    ]);
                ENDIF;
                $menu->add([
                    'url' => '/thanh-vien',
                    'title' => 'Thành Viên',
                    'icon' => 'fa fa-users'
                ]);
                $menu->add([
                    'url' => '/filemanager/index.html',
                    'title' => 'Quản Lý album',
                    'icon' => 'fa fa-medium'
                ]);
                $menu->add([
                    'url' => '/quan-tri-vien',
                    'title' => 'Quản Trị Viên',
                    'icon' => 'fa fa-mortar-board'
                ]);

                $menu->add([
                    'url' => '/profile',
                    'title' => 'Trang Cá Nhân',
                    'icon' => 'fa fa-user-plus'
                ]);

            });
        }   

        return $next($request);
    }

}
