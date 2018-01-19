@extends('layouts.app')

@section('content')

<div class="container">

	<div class="row">

		 <div class="col-md-8 col-md-offset-2">

		 	<div class="panel panel-default">

		 		<div class="panel-heading"><h3>Detalhes do serviço</h3></div>


		 		<div class="panel-body">

		 			<div class="row">

		 				<div class="col-md-4 control-label"><h4>Id do orçamento:</h4></div>

		 				<div class="col-md-6">

		 					@if( ! empty($servico->Workflow_ID))
			            		<h4> {{ $servico->Workflow_ID }}</h4>
			            	@else
			            		<h4>Não informado</h4>
			    			@endif

		 				</div>

		 			</div>

		 			<div class="row">
		 				
		 				<div class="col-md-4 control-label">

		 					@if($tipo == 'chamado')

		 						<h4>Título do chamado:</h4>

		 					@else

		 						<h4>Título do orçamento:</h4>

		 					@endif

		 				</div>
		 				
		 				<div class="col-md-6">

		 					@if( ! empty($servico->Titulo))
			            		<h4> {{ $servico->Titulo }}</h4>
			    			@endif

		 				</div>

		 			</div>

		 			<div class="row">
		 				
		 				<div class="col-md-4 control-label"><h4>Operador responsável:</h4></div>
		 				
		 				<div class="col-md-6">

		 					@if( ! empty($servico->Nome))
			            		<h4> {{ $servico->Nome }}</h4>
			            	@else
			            		<h4> {{ $servico->Nome_Fantasia }}</h4>
			    			@endif

		 				</div>

		 			</div>

		 			<div class="row">

		 				<div class="col-md-4 control-label"><h4>Solicitante:</h4></div>

		 				<div class="col-md-6">

		 					@if( ! empty($solicitante->Nome))
			            		<h4>{{ $solicitante->Nome_Fantasia }}</h4>
			    			@else
			    				<h4>{{ $solicitante->Nome }}</h4>
			    			@endif

		 				</div>

		 			</div>

		 			<div class="row">

		 				<div class="col-md-4 control-label"><h4>Endereço solicitante:</h4></div>

		 				<div class="col-md-6">

		 					@if( ! empty($endereco->Logradouro))
			            		<h4>
			            			{{ $endereco->Logradouro }} - {{ $endereco->Numero }} - {{ $endereco->Bairro }}
			            			<br>
			            			{{ $endereco->Complemento }} 
			            			<br>
			            			{{ $endereco->Cidade }} - {{ $endereco->UF }}
			            		</h4>
			    			@else
			    				<h4>Não informado</h4>
			    			@endif
		 					
		 				</div>

		 			</div>

		 		</div>

		 	</div>

		 	
		 	<div class="panel panel-default">


		 		@if (session('msg_ok'))

		 			<div class="alert alert-success" role="alert">
						<h4 class="text-center"> {{ session('msg_ok') }} </h4>
					</div>

		 		@elseif (session('msg_error'))
			    				
			    	<div class="alert alert-danger" role="alert">
						<h4 class="text-center"> {{ session('msg_error') }} </h4>
					</div>

			    @endif


		 		<div class="panel-heading"><h3>Cadastrar nova despesa</h3></div>

		 		<div class="panel-body">

		 			{{ Form::open(array('route'=> 'despesas.store', 'class'=>'form', 'method'=>'post')) }}


		 				<div class="form-group{{ $errors->has('despesa') ? ' has-error' : '' }}">
		 					{{ Form::hidden('opcao', $tipo, array('id' => 'opcao')) }}
		            		{{ Form::hidden('protocolo', $servico->Workflow_ID, array('id' => 'protocolo')) }}
		            		{{ Form::hidden('chamada', $chamada, array('id' => 'chamada')) }}
		            			

		                    <!-- <label for="nomeCliente">Nome do usuário</label> -->
		                    {{ Form::Label('despesa', 'Nome despesa') }}
		                    {{ Form::text('despesa', null, array( 'id'=>'despesa', 'class'=>'form-control', 'placeholder'=>'Nome da despesa.')) }}
		                    
		                    @if ($errors->has('despesa'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('despesa') }}</strong>
                                </span>
                            @endif
		                </div>

		                <div class="form-group col-md-6 {{ $errors->has('valor') ? ' has-error' : '' }}">

		                	{{ Form::Label('valor', 'Valor unitário da despesa  R$') }}
		                    {{ Form::text('valor', null, array( 'id'=>'valor', 'class'=>'form-control', 'placeholder'=>'Valor da despesa.')) }}

		                    @if ($errors->has('valor'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('valor') }}</strong>
                                </span>
                            @endif

		                </div>

		                <div class="form-group col-md-6{{ $errors->has('quantidade') ? ' has-error' : '' }}">

		                	{{ Form::Label('quantidade', 'Quantidade') }}
		                    {{ Form::text('quantidade', null, array( 'id'=>'quantidade', 'class'=>'form-control', 'placeholder'=>'Quantidade da despesa.')) }}

		                    @if ($errors->has('quantidade'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('quantidade') }}</strong>
                                </span>
                            @endif

		                </div>

		                <div class="form-group">

		                	{{ Form::Label('observacao', 'Observação') }}
		                    {{ Form::textarea('observacao', null, array( 'id'=>'observacao', 'class'=>'form-control', 'placeholder'=>'Observação')) }}
		                    {{ $errors->first('observacao', '<span class=inputError>:message</span>') }}

		                </div>

		                <div class="form-group">
		                    {{ Form::submit('Cadastrar despesa', array('class'=>'btn btn-large btn-primary btn-block'))}}
		                </div>

		 			{{ Form::close() }}

		 		</div>

		 	</div>


		 	<div class="panel panel-default">

		 		<div class="panel-heading"><h3>Despesas cadastradas para o serviço</h3></div>

		 		@if (! empty($despesas) )

		 			<div class="panel-body">

			 			@foreach($despesas as $despesa)

			 				<div class="row">

			 					<div class="col-md-4 control-label"><h4>Título do orçamento:</h4></div>
		 				
				 				<div class="col-md-6">

				 					@if( ! empty($despesa->Descricao_Produto))
					            		<h4 class="text-center"> {{ $despesa->Descricao_Produto }}</h4>
					    			@endif

				 				</div>

			 				</div>

			 				<div class="row">
			 					<div class="col-md-4 control-label"><h5>Quantidade:</h5></div>
		 				
				 				<div class="col-md-6">

				 					@if( ! empty($despesa->Quantidade))
					            		<h5> {{ $despesa->Quantidade }}</h5>
					    			@endif

				 				</div>
			 				</div>

			 				<div class="row">
			 					<div class="col-md-4 control-label"><h5>Valor:</h5></div>
		 				
				 				<div class="col-md-6">

				 					@if( ! empty($despesa->Valor_Produto))
					            		<h5>R$ {{ $despesa->Valor_Produto }}</h5>
					    			@endif

				 				</div>
			 				</div>

			 				<div class="row">
			 					<div class="col-md-4 control-label"><h5>Observação:</h5></div>
		 				
				 				<div class="col-md-6">

				 					@if( ! empty($despesa->Observacao_Despesa))
					            		<h5> {{ $despesa->Observacao_Despesa }}</h5>
					    			@endif

				 				</div>
			 				</div>

			 				<div class="row">
			 					<div class="col-md-4 control-label"><h5>Cadastrado por:</h5></div>
		 				
				 				<div class="col-md-6">

				 					@if( ! empty($despesa->Nome))
					            		<h5> {{ $despesa->Nome }}</h5>
					    			@endif

				 				</div>
			 				</div>

			 			@endforeach

			 		</div>

		 		@else

		 			<h4 class="text-center"> Nenhuma despesa cadastrada no momento.</h4>

		 		@endif

		 	</div>


		 </div>

	</div>
</div>
@endsection