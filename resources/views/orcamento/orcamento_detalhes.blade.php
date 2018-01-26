@extends('layouts.app')

@section('content')

	<div class="container">


		<div class="row">


			<div class="col-md-8 col-md-offset-2">


				<div class="panel panel-default">

					<div class="panel-heading">
						<p> 
							<b>Dados do orçamento:</b> {{ $dadosOrcamentos[0]['orcamento']->Workflow_ID }}
						</p>
						<p>
							<b>Título:</b> {{ $dadosOrcamentos[0]['orcamento']->Titulo }}

							<a href="{{ URL::to('/orcamentos') }}" class="btn btn-primary pull-right">Voltar</a>
						</p>

						<p>
							<b>Status:</b> {{ $dadosOrcamentos[0]['orcamento']->Descr_Tipo }}
						</p>

						<p>
							<b>Tempo aberto:</b> 
							@if(! empty($dadosOrcamentos[0]['tempoAberto']))

								@if($dadosOrcamentos[0]['tempoAberto']['anos'] > 0)
 					
					                {{ number_format($dadosOrcamentos[0]['tempoAberto']['anos'], 0) }} ano(s), 	

				                @endif

				                @if($dadosOrcamentos[0]['tempoAberto']['meses'] > 0)

					                {{ number_format($dadosOrcamentos[0]['tempoAberto']['meses'], 0) }} mese(s), 

				                @endif

				                {{ number_format($dadosOrcamentos[0]['tempoAberto']['dias'], 0) }} dia(s).

							@else

								<p>
					                Não definido.
					            </p>

							@endif
						<p>

					</div>

					<div class="panel-body">

						<div class="row">
	                        <div class="col-md-12">

	                        	@if (! empty($dadosOrcamentos))

	                        		<table width="100%" class="table table-striped table-bordered table-hover" id="dataTables-example">

	                        			<thead>
				                            <tr>
				                                <th>Faturamento</th>
				                                <th>Despesas</th>
				                            </tr>
				                        </thead>
				                        <tbody>

				                        	<tr>
				                        		<td>

				                        			<table width="100%" class="table table-striped table-bordered table-hover" id="dataTables-example">

				                        				<tr>
							                                <th>Produto(s) a faturar do cliente</th>
							                            </tr>

					                        			@foreach($dadosOrcamentos[0]['produtos'] as $produto)


					                        				@if($produto->Cobranca_Cliente == 1 && (! empty($produto->Proposta_ID)) )


					                        				<tr>

					                        					<td>
					                        						Produto : {{ $produto->Descricao_Produto }}
					                        					</td>

					                        				</tr>

					                        				<tr>

					                        					<td>
					                        						Quantidade : {{ number_format($produto->Quantidade, 0) }}
					                        					</td>

					                        				</tr>

					                        				<tr>

					                        					<td>
					                        						Valor Unitario : R$ {{ number_format($produto->Valor_Venda_Unitario, 2) }}
					                        					</td>

					                        				</tr>

					                        				<tr>

					                        					<td>
					                        						<hr>
					                        					</td>

					                        				</tr>


					                        				@endif


					                        			@endforeach

					                        			<tr style="background-color: #a1efa1;">

					                        				@if( is_numeric($dadosOrcamentos[0]['gastos']))

					                        					<td>
						                        					Total do orçamento: R${{ number_format($dadosOrcamentos[0]['faturamento'], 2) }}
						                        				</td>

					                        				@else

					                        					<td>
					                        						Não informado.
					                        					</td>

					                        				@endif

					                        				

					                        			</tr>

					                        		</table>

				                        		</td>

				                        		<td>

				                        			<table width="100%" class="table table-striped table-bordered table-hover" id="dataTables-example">

				                        				<tr>
							                                <th>Despesas do orçamento</th>
							                            </tr>

							                            @foreach($dadosOrcamentos[0]['produtos'] as $produto)


					                        				@if($produto->Pagamento_Prestador == 1 )

					                        					<tr>

						                        					@if(! empty($produto->Descricao_Produto))

						                        						<td>
							                        						Produto : {{ $produto->Descricao_Produto }}
							                        					</td>

						                        					@elseif(! empty($produto->Descricao_Produto_Campo))

						                        						<td>
							                        						Produto : {{ $produto->Descricao_Produto_Campo }}
							                        					</td>

							                        				@elseif(! empty($produto->Produto_Variacao))

							                        					<td>
							                        						Produto : {{ $produto->Produto_Variacao }}
							                        					</td>

							                        				@elseif(! empty($produto->Produto_nome))

							                        					<td>
							                        						Produto : {{ $produto->Produto_nome }}
							                        					</td>

						                        					@endif

						                        				</tr>

						                        				<tr>

						                        					<td>
						                        						Quantidade : {{ number_format($produto->Quantidade, 0) }}
						                        					</td>

						                        				</tr>

						                        				<tr>

						                        					<td>
						                        						Valor Unitario : R$ {{ number_format($produto->Valor_Custo_Unitario, 2) }}
						                        					</td>

						                        				</tr>

						                        				<tr>

						                        					<td>
						                        						<hr>
						                        					</td>

						                        				</tr>

					                        				@endif

					                        			@endforeach

					                        			<tr style="background-color: #f29b9b;">

					                        				@if( is_numeric($dadosOrcamentos[0]['gastos']))

					                        					<td>
						                        					Total de gastos: R${{ number_format($dadosOrcamentos[0]['gastos'], 2) }}
						                        				</td>

					                        				@else

					                        					<td>
					                        						Não informado.
					                        					</td>

					                        				@endif

					                        				

					                        			</tr>

				                        			</table>

				                        		</td>

				                        	</tr>

				                        </tbody>

	                        		</table>

	                        	@else

	                            	<span class="help-block">
                                        <strong>Nenhum produto cadastrado.</strong>
                                    </span>

	                            @endif


	                        </div>

	                    </div>

					</div>

				</div>


			</div>


		</div>


	</div>

@endsection
