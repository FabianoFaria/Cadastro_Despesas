<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use Auth;

use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Redirect;

class CustomLoginController extends Controller
{

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('home');
    }


    /**
     * Handle an authentication attempt.
     *
     * @return Response
     */
    public function authenticate()
    {

        //var_dump($_POST);
        //dd(" teste de funcão:  psd: ");

        $dataAttempt = array(
            'Email'         => $_POST['email'],
            'password'      => $_POST['password']
        );


        if (Auth::attempt($dataAttempt)) {
            // Authentication passed...
            
            //$dadosUsuario  =  User::where('Email', $_POST['email'])->first();

            //$dadosUsuario = DB::table('cadastros_dados')->where('Email', $_POST['email'])->first();

            //echo $dadosUsuario->Email;
            //echo $dadosUsuario->Nome;

            //session()->put('email', $dadosUsuario->Email);
            //session()->put('usuario', $dadosUsuario->Nome);

            $user = Auth::user();

            //var_dump($user);

            //dd(" Validação Ok");

            return redirect('/home');
            //return redirect()->intended('dashboard');
        }else{
            //dd(" Validação Nok");
            $loginData  = array(
                'email' => $_POST['email']
            );

            $errors = array(
                'email' => 'Email ou senha estão incorretos!'
            );

            return Redirect::back()->withInput($loginData)->withErrors($errors);
        }
    }


    public function desauthenticate(Request $request)
    {
        $this->guard()->logout();
        $request->session()->flush();
        $request->session()->regenerate();
        return redirect($this->redirectAfterLogout);
    }
}
