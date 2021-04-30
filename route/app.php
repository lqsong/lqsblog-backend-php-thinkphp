<?php
// +----------------------------------------------------------------------
// | LqsBlog - Route
// +----------------------------------------------------------------------
// | Copyright (c)  2018-present http://liqingsong.cc All rights reserved.
// +----------------------------------------------------------------------
// | Author: LiQingSong <957698457@qq.com>
// +----------------------------------------------------------------------
use think\facade\Route;

// Admin路由
Route::group('admin/v1', function () {

    Route::get('guest/validateCodeImg$', 'guest/validateCodeImg');
    Route::post('user/login$', 'login/index');
    Route::get('user/info$', 'user/info');
    Route::post('user/logout$', 'user/logout');

    Route::get('articles$', 'article/articleList');
    Route::post('articles$', 'article/articleCreate');
    Route::put('articles/:id', 'article/articleUpdate');
    Route::delete('articles/:id', 'article/articleDelete');
    Route::get('articles/:id', 'article/articleRead');
    Route::get('article/categorys$', 'article/categoryList');
    Route::post('article/categorys$', 'article/categoryCreate');
    Route::put('article/categorys/:id', 'article/categoryUpdate');
    Route::delete('article/categorys/:id', 'article/categoryDelete');
    Route::get('article/categorys/cascader$', 'article/categoryCascader');

    Route::get('works$', 'works/worksList');
    Route::post('works$', 'works/worksCreate');
    Route::put('works/:id', 'works/worksUpdate');
    Route::delete('works/:id', 'works/worksDelete');
    Route::get('works/:id', 'works/worksRead');

    Route::get('topics$', 'topics/topicsList');
    Route::post('topics$', 'topics/topicsCreate');
    Route::put('topics/:id', 'topics/topicsUpdate');
    Route::delete('topics/:id', 'topics/topicsDelete');
    Route::get('topics/:id', 'topics/topicsRead');

    Route::get('links$', 'link/linkList');
    Route::post('links$', 'link/linkCreate');
    Route::put('links/:id', 'link/linkUpdate');
    Route::delete('links/:id', 'link/linkDelete');
    Route::get('links/:id', 'link/linkRead');
    Route::get('link/categorys$', 'link/categoryList');
    Route::post('link/categorys$', 'link/categoryCreate');
    Route::put('link/categorys/:id', 'link/categoryUpdate');
    Route::delete('link/categorys/:id', 'link/categoryDelete');

    Route::get('tags$', 'tag/tagsList');
    Route::post('tags$', 'tag/tagsCreate');
    Route::put('tags/:id', 'tag/tagsUpdate');
    Route::delete('tags/:id', 'tag/tagsDelete');
    Route::get('tags/search$', 'tag/tagsSearch');

    Route::get('searchs$', 'search/searchList');
    Route::get('searchs/keywords$', 'search/keywordsList');

    Route::get('stats/articles/dailynew$', 'stats/articlesDailyNew');
    Route::get('stats/works/weeknew$', 'stats/worksWeekNew');
    Route::get('stats/topics/monthnew$', 'stats/topicsMonthNew');
    Route::get('stats/links/annualnew$', 'stats/linksAnnualNew');

    Route::get('about$', 'about/aboutRead');
    Route::post('about$', 'about/aboutCreate');

    Route::get('config$', 'config/configRead');
    Route::post('config$', 'config/configCreate');

    Route::get('apis$', 'api/apiList');
    Route::post('apis$', 'api/apiCreate');
    Route::put('apis/:id', 'api/apiUpdate');
    Route::delete('apis/:id', 'api/apiDelete');
    Route::get('apis/cascader$', 'api/apiCascader');
    Route::get('apis/all$', 'api/apiListAll');

    Route::get('menus$', 'menu/menuList');
    Route::post('menus$', 'menu/menuCreate');
    Route::put('menus/:id', 'menu/menuUpdate');
    Route::delete('menus/:id', 'menu/menuDelete');
    Route::get('menus/cascader$', 'menu/menuCascader');
    Route::get('menus/all$', 'menu/menuListAll');

    Route::get('roles$', 'role/roleList');
    Route::post('roles$', 'role/roleCreate');
    Route::put('roles/:id', 'role/roleUpdate');
    Route::delete('roles/:id', 'role/roleDelete');

    Route::get('accounts$', 'account/accountList');
    Route::post('accounts$', 'account/accountCreate');
    Route::put('accounts/:id', 'account/accountUpdate');
    Route::delete('accounts/:id', 'account/accountDelete');
    Route::get('accounts/:id', 'account/accountRead');

    Route::get('upload/images$', 'upload/imagesList');
    Route::post('upload/images$', 'upload/imagesCreate');
    // Route::get('upload/images/:id', 'upload/imagesDown');

})->prefix('admin.v1.');


// Admin路由
Route::group('pc/v1', function () {

    Route::get('index/recommend$', 'home/indexRecommend');
    Route::get('index/list$', 'home/indexList');

    Route::get('article/category$', 'article/articleCategory');
    Route::get('article/list$', 'article/articleList');
    Route::get('article/detail$', 'article/articleDetail');
    Route::get('article/interest$', 'article/articleInterest');

    Route::get('works/list$', 'works/worksList');
    Route::get('works/detail$', 'works/worksDetail');

    Route::get('tag/list$', 'tag/tagList');
    Route::get('tag/detail$', 'tag/tagDetail');

    Route::get('topics/list$', 'topics/topicsList');
    Route::get('topics/detail$', 'topics/topicsDetail');

    Route::get('links/list$', 'link/linksList');
    Route::get('links/recommend$', 'link/linksRecommend');

    Route::get('search$', 'search/searchList');
    
    Route::get('about$', 'about/aboutRead');

    Route::get('config$', 'config/configRead');

})->prefix('pc.v1.');
