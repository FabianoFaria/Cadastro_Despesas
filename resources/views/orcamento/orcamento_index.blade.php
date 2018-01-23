@extends('layouts.app')

@section('content')

	<div class="container">
			
		<div class="row">

			<div class="col-md-8 col-md-offset-2">

				<div class="panel panel-default">

					<div class="panel-heading">Situação dos últimos orçamentos</div>

					<div class="panel-body">

						<div class="row">
	                        <div class="col-md-12">

	                            
	                            @if (! empty($dadosOrcamentos))


	                            	<table width="100%" class="table table-striped table-bordered table-hover" id="dataTables-example">

	                            		<thead>
				                            <tr>
				                                <th>Número orçamento</th>
				                                <th>Título</th>
				                                <th>Status</th>
				                                <th>Responsável</th>
				                                <th>Data abertura</th>
				                                <th>Data Finalizada</th>
				                            </tr>
				                        </thead>
				                        <tbody>

	                            		<!--
											
											"orcamento" => {#196 ▼
										      +"Workflow_ID": 277,
										      Titulo
										      +"Solicitante_ID": 14509
										      +"Situacao_ID": 110
										      +"Data_Abertura": "2017-12-28 13:28:00"
										      +"Data_Finalizado": null
										      +"Usuario_Cadastro_ID": -1
										      +"Descr_Tipo": "Aberto"
										      +"Nome": "Administrador Geral"
    }

	                            		-->
	                            		@foreach($dadosOrcamentos as $orcamento)

	                            			<tr>
	                            				<td>
				                        			<a href="{{ URL::to('/orcamentos/'.$orcamento['orcamento']->Workflow_ID) }}" class=""> {{ $orcamento['orcamento']->Workflow_ID }} </a>
				                        		</td>
				                        		<td>
				                        			<a href="{{ URL::to('/orcamentos/'.$orcamento['orcamento']->Workflow_ID) }}" class=""> {{ $orcamento['orcamento']->Titulo }} </a>
				                        		</td>
				                        		<td style="background-color:{{ $orcamento['situacaoCor'] }};">
				                        			{{ $orcamento['orcamento']->Descr_Tipo }}
				                        		</td>
				                        		<td>
				                        			{{ $orcamento['orcamento']->Nome }}
				                        		</td>
				                        		<td>
				                        			@php

				                        				$data  = $orcamento['orcamento']->Data_Abertura;
				                        				$teste = explode(' ',$data); 
				                        				echo implode('/',array_reverse(explode('-', $teste[0])));

				                        			@endphp

				                        		</td>
				                        		<td>
				                        			@if (! empty($orcamento['orcamento']->Data_Finalizado))

					                        			@php

					                        				$data  = $orcamento['orcamento']->Data_Finalizado;
					                        				$teste = explode(' ',$data); 
					                        				echo implode('/',array_reverse(explode('-', $teste[0])));

					                        			@endphp

				                        			@else

				                        				<span>Não definido.</span>

				                        			@endif

				                        			
				                        		</td>
				                        	<tr>
				                        	<tr>
				                        		<td colspan="2">
				                        			
				                        		</td>
				                        		<td colspan="2">

				                        			<p style="color: blue">
				                        				Faturamento :  
				                        				@if( is_numeric($orcamento['faturamento']))
				                        					<span>R$ {{ number_format($orcamento['faturamento'], 2) }}</span>
				                        				@else
				                        					<span>Não definido</span>
				                        				@endif
				                        				
				                        			</p>
				                        			
				                        		</td>
				                        		<td colspan="2">
				                        			<p style="color: red">
				                        				Custos :
				                        				@if( is_numeric($orcamento['gastos']))
				                        					<span>R$ {{ number_format($orcamento['gastos'], 2) }}</span>
				                        				@else
				                        					<span>Não definido</span>
				                        				@endif
				                        			</p>
				                        			
				                        		</td>
				                        	</tr>

	                            		@endforeach

	                            		</tbody>
	                            	</table>

	                            @else

	                            	<span class="help-block">
                                        <strong>Nenhum orçamento localizado.</strong>
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
