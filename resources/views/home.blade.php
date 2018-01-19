@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <div class="panel panel-default">
                <div class="panel-heading">Painel principal</div>

                <div class="panel-body">
                    
                    <div class="row">
                        <div class="col-md-6">

                            <p>Você está logado!</p>

                            <hr>

                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            
                            <p>
                               <i class="fa fa-credit-card-alt fa-3x" aria-hidden="true"></i> <a href="{{ url('/despesas') }}">Cadastradar despesa!</a>
                            </p>

                            <!-- <form class="form-horizontal" role="form" method="POST" action="{{ url('/despesas/') }}">

                            </form> -->
                        </div>

                        <div class="col-md-6">

                            <p>
                                <i class="fa fa-file-text fa-3x" aria-hidden="true"></i><a href="{{ url('/orcamentos') }}">Status dos orçamentos!</a>
                            </p>


                        </div>

                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <hr>
                            <p>
                                <a href="{{ url('/logout') }}"
                                    onclick="event.preventDefault();
                                    document.getElementById('logout-form').submit();">
                                    Sair
                                </a>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
