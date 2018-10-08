<?php

//后台首页
Route::get('/','AdminController@index');

//Excel操作
Route::get('excel/export','ExcelController@export');
Route::post('excel/import','ExcelController@import');
Route::get('search','ProductController@search');


//产品管理模块
Route::group(['prefix' => 'products','middleware' => ['isLogin']], function () {
    //产品品牌   brand
    Route::get('brand','ProductController@brand');
    Route::post('batch','ProductController@batch');
    Route::post('productPriceSure','ProductController@productPriceSure');
//产品列表   list
    Route::any('list','ProductController@productsList')->name('list');
    Route::any('{category}/list','ProductController@categoryList');
    Route::any('{category}/{status}/list','ProductController@categoryHotList');
    Route::post('ajax/language','ProductController@ajaxRes');
    Route::post('ajax/getLog','ProductController@getLog');
    Route::post('ajax/getProduct','ProductController@getProduct');
    Route::post('ajax/getCategory','ProductController@getCategory');
    Route::any('ajax/addSession','ProductController@addSession');
//产品详情   detail
    Route::get('{model}/{language_id}','ProductController@detail');
//创建产品
    Route::any('store','ProductController@store');
//更新产品
    Route::post('{model}/{language_id}/update','ProductController@update');
//删除产品
    Route::get('{model}/{language_id}/delete','ProductController@delete');
//上传产品
    Route::any('import','ProductController@import');
    Route::any('import2','ProductController@import2');
//校验表格
    Route::any('testResult','ProductController@testResult');
});


//工具
Route::group(['prefix' => 'tool','middleware' => ['isLogin']], function (){
    Route::get('/','ToolController@index');
    Route::any('/imdpay','ToolController@imdpay');
    Route::any('/webShell','ToolController@webShell');
    Route::any('/sqlApi','ToolController@sqlApi');
    Route::any('uploads', 'ToolController@uploadImages');
    Route::any('category', 'ToolController@category');
    Route::any('res', 'ToolController@res');
    Route::get('eadme', 'ToolController@readme');
    Route::get('commit', 'ToolController@commit');
    Route::get('backupsql', 'ToolController@backupsql');
    Route::get('backupbtn', 'ToolController@backupbtn');
    Route::any('brand/add', 'ToolController@brandAdd');
    Route::any('language/add', 'ToolController@languageAdd');
    Route::get('zip', 'ToolController@zip');
    Route::get('uploadList', 'ToolController@uploadList');
    Route::get('log/{uploadLog}/delete', 'ToolController@logDelete');
	
    Route::get('download/commit', function (){
        $namepath = 'storage/commit_demo.xls';
        return response()->download(str_replace('\\', '/', public_path()).'/'.$namepath);
    });
    Route::get('download/product', function (){
        $namepath = 'storage/product_demo.xls';
        return response()->download(str_replace('\\', '/', public_path()).'/'.$namepath);
    });
    Route::get('download/common', function (){
        $namepath = 'storage/common_demo.xls';
        return response()->download(str_replace('\\', '/', public_path()).'/'.$namepath);
    });
    Route::any('optionsAdd','ToolController@optionsAdd');
});
Route::get('tool/email/{m}/{u}/{s}','ToolController@email');
//分类管理
Route::group(['prefix' => 'categorys','middleware' => ['isLogin']], function () {
    Route::get('/','CategoryController@index');
    //添加分类
    Route::any('add','CategoryController@add');
    Route::post('rename', 'CategoryController@rename');
});

//评论管理
Route::group(['prefix' => 'commits','middleware' => ['isLogin']], function () {
    Route::get('/','CommitController@index');
    Route::get('/common','CommitController@commonIndex');
    Route::get('/search','CommitController@search');
//添加评论
    Route::any('add','CommitController@add');
    Route::any('addCommon','CommitController@addCommon');
    Route::get('/{common_commit}/delete','CommitController@delete');
    //导入评论
    Route::any('import','CommitController@import')->name('commitsImport');
    Route::post('import/common','CommitController@commonImport');
    Route::post('getCommonCommits','CommitController@getCommonCommits');
    Route::post('addCommonCommits','CommitController@addCommonCommits');
    Route::post('addManyCommonCommits','CommitController@addManyCommonCommits');
});


//网站管理
Route::group(['prefix' => 'webs','middleware' => ['isLogin']], function () {
    //网站列表
    Route::get('/list', 'WebController@webList')->name('webList');
    //新建网站信息
    Route::any('/add', 'WebController@add');
    //配置网站
    Route::get('/{web}/set','WebController@webSet');
    Route::post('/{web}/{category}/set','WebController@categoryList');
    Route::any('/addSelect','WebController@addSelect');
    Route::any('/delSelect','WebController@delSelect');
    Route::any('/{web}/export','WebController@export');
    Route::get('/{web}/delete','WebController@delete');
    Route::post('/{web}/store','WebController@store');
    Route::post('/ajax/getWeb','WebController@getWeb');
    Route::post('/ajax/getLog','WebController@getLog');
    Route::post('priceChange/{url}','ProductController@priceChange');

});

Route::group(['prefix' => 'testWeb','middleware' => ['isLogin']], function () {
    Route::get('send','TestWebController@send');
    Route::get('{testWeb_id}/isTest','TestWebController@isTest');
    Route::any('index','TestWebController@index');
    Route::any('add','TestWebController@add');
});
Route::any('/api/testWeb/{action}/{md5}/{urls}','TestWebController@api');
Route::any('/api/statusSql/{status}/{orderId}/{md5}/{urls}','ToolController@statusApi');
Route::any('login','authController@login');