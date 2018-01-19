<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    
    if(Auth::check()){

		//Usuário logado
		return Redirect::to('/home');

	}else{

		return view('auth/login');
	}

});

Auth::routes();

Route::get('/home', 'HomeController@index');

/* Rotas para login e logout para */

Route::post('/customLogin','CustomLoginController@authenticate');

// Route::get('customLogout','Auth/LoginController@logout');

Route::group(array('before' =>'auth'), function()
{
	Route::resource('orcamentos','OrcamentoController');

	Route::resource('despesas','DespesasController');

	Route::post('search', 'DespesasController@procurar');

	Route::get('search', array('as'=> 'despesas', 'uses' => 'DespesasController@procurar'));

});