@extends('layouts.app')

@section('content')

<div class="container">

    <div class="row">

        <div class="col-md-8 col-md-offset-2">

            <div class="panel panel-default">
                
                <div class="panel-heading">Cadastro de despesas</div>

                <div class="panel-body">
                    
                    <div class="row">
                        <div class="col-md-12">

                            <h4>Selecione o orçamento ou o chamado</h4>

                        </div>
                    </div>

                    <div class="row">

                        <div class="col-md-12">

                            <!-- <form class="form-horizontal" role="form" method="POST" action="{{ url('/despesas.show') }}"> -->
                          
                           <form action="/search" method="POST" role="search">

                                {{ csrf_field() }}


                                <div class="form-group{{ $errors->has('opcao') ? ' has-error' : '' }}">

                                    <label for="opcao" class="col-md-4 control-label">Orçamento ou chamado</label>

                                    <div class="col-md-6">

                                        <input name="opcao" type="radio" value="chamado"> Chamado <br>
                                        <input checked="checked" name="opcao" type="radio" value="orcamento"> Orçamento  

                                    </div>
                                </div>


                                <div class="form-group{{ $errors->has('protocolo') ? ' has-error' : '' }}">
                                    <label for="protocolo" class="col-md-4 control-label">Número do chamado ou do orçamento</label>

                                    <div class="col-md-6">
                                        <input id="protocolo" type="text" class="form-control" name="protocolo" value="{{ old('email') }}" autofocus>

                                        @if ($errors->has('protocolo'))
                                            <span class="help-block">
                                                <strong>{{ $errors->first('protocolo') }}</strong>
                                            </span>
                                        @endif
                                    </div>
                                </div>

                                <div class="form-group">    
                                    <div class="col-md-8 col-md-offset-4">
                                        <button type="submit" class="btn btn-primary">
                                            Procurar
                                        </button>
                                    </div>
                                </div>

                            </form>

                        </div>

                    </div>
                    
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
