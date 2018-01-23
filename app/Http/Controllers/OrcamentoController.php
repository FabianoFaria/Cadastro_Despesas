<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Redirect;
use DB;

class OrcamentoController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(){
        // Carrega os últimos orçamentos existentes

        $dadosOrcamentos    = array();

        $orçamentos         = DB::select('SELECT
                                    w.Workflow_ID,
                                    w.Titulo,
                                    w.Solicitante_ID,
                                    w.Situacao_ID,
                                    w.Data_Abertura,
                                    w.Data_Finalizado,
                                    w.Usuario_Cadastro_ID,
                                    s.Descr_Tipo,
                                    s.Tipo_Auxiliar,
                                    u.Nome
                                    FROM orcamentos_workflows w
                                    LEFT JOIN tipo s on s.Tipo_ID = w.Situacao_ID
                                    LEFT JOIN cadastros_dados u on u.Cadastro_ID = w.Usuario_Cadastro_ID
                                    ORDER BY Workflow_ID DESC
                                    LIMIT 40');

        if(! empty($orçamentos)){
            

            foreach ($orçamentos as $orcamento) {

                //Carrega os faturamentos com o orçamento



                /*
                     +"Workflow_ID": 277
                      +"Solicitante_ID": 14509
                      +"Situacao_ID": 110
                      +"Data_Abertura": "2017-12-28 13:28:00"
                      +"Data_Finalizado": null
                      +"Usuario_Cadastro_ID": -1
                      +"Descr_Tipo": "Aberto"
                      +"Nome": "Administrador Geral"
                */

                    $workflowID = $orcamento->Workflow_ID;


                    //var_dump($orcamento->Tipo_Auxiliar);

                    //USANDO explode() POIS UNSERIALIZED ESTÁ CAUSANDO ERRO

                    $temp         = explode('cor-fundo";s:7:', $orcamento->Tipo_Auxiliar);

                    //$descricao  = unserialize($orcamento->Tipo_Auxiliar);

                    $parte = substr($temp[1], 1, 7);

                    //var_dump($temp);

                    $corSituacao  = $parte;  

                    //'s:7:"#ded1d1";'

                    //Carregando as propostas contidas no orçamento

                    $propostasOrcamento = DB::select('SELECT op.Proposta_ID,
                                                        op.Workflow_ID,
                                                        op.Titulo,
                                                        op.Data_Cadastro,
                                                        op.Usuario_Cadastro_ID,
                                                        u.Nome AS Usuario,
                                                        SUM(opp.Quantidade) as Quantidade_Total_Proposta,
                                                        SUM(opp.Quantidade * opp.Valor_Venda_Unitario) as Valor_Total_Proposta,
                                                        count(opp.Proposta_Produto_ID) as Total_Itens_Proposta,
                                                        op.Status_ID as Status_ID,
                                                        upper(t.Descr_Tipo) as Status,
                                                        ow.Situacao_ID as Situacao_ID,
                                                        fc.Descr_Tipo as Forma_Cobranca,
                                                        fc.Tipo_Auxiliar as Detalhe_Cobranca
                                                    FROM orcamentos_propostas op
                                                    INNER JOIN orcamentos_workflows ow on ow.Workflow_ID = op.Workflow_ID
                                                    LEFT JOIN produtos_tabelas_precos tp on tp.Tabela_Preco_ID = op.Tabela_Preco_ID
                                                    LEFT JOIN orcamentos_propostas_produtos opp on opp.Proposta_ID = op.Proposta_ID and opp.Situacao_ID = 1
                                                    LEFT JOIN cadastros_dados u ON u.Cadastro_ID = op.Usuario_Cadastro_ID
                                                    LEFT JOIN tipo t on t.Tipo_ID = op.Status_ID
                                                    INNER JOIN tipo fc ON fc.Tipo_ID = op.Forma_Pagamento_ID
                                                    WHERE op.Workflow_ID = ? and op.Situacao_ID = 1
                                                    GROUP BY op.Proposta_ID, op.Workflow_ID, op.Titulo, op.Data_Cadastro, op.Usuario_Cadastro_ID, u.Nome, op.Status_ID
                                                     ', [$workflowID]);

    
                    if(!empty($propostasOrcamento)){


                        foreach ($propostasOrcamento as $propostas) {


                            //echo $propostas->Forma_Cobranca;

                            $statusProposta         = DB::select("SELECT Status_ID, Tabela_Preco_ID 
                                                              FROM orcamentos_propostas 
                                                              WHERE Proposta_ID = ?", [$propostas->Proposta_ID]);

                            $totalProdutosFaturar   = 0;
                            $totalProdutoDespesa    = 0;
                            $totalGastoCampo        = 0;


                            //RECUPERA A FORMA DE PAGAMENTO DA PROPOSTA PARA CALCULAR O VALOR COBRADO
                            $formaPagamento         = unserialize($propostas->Detalhe_Cobranca);

                            if(!empty($formaPagamento['tipo-bonus-disponivel'])){

                              $tipoModificacao        = $formaPagamento['tipo-bonus-disponivel'];
                              $bonusModificacao       = $formaPagamento['valor_modificado'];

                            }else{
                              $tipoModificacao        = 'Desconto';
                              $bonusModificacao       = 0;
                            }
                           

                            //var_dump($formaPagamento);


                            $produtosPropostas      = DB::select("SELECT 
                                                                    opp.Proposta_Produto_ID as Chave_Primaria_ID,
                                                                    pv.Produto_Variacao_ID,
                                                                    CONCAT(COALESCE(pd.Nome,''),
                                                                    ' ',
                                                                    COALESCE(pv.Descricao,'')) AS Descricao_Produto,
                                                                    opp.Observacao_Produtos,
                                                                    opp.Valor_Venda_Unitario, 
                                                                    opp.Valor_Custo_Unitario, 
                                                                    opp.Faturamento_Direto,
                                                                    opp.Prestador_ID, 
                                                                    re.Nome as Prestador,
                                                                    opp.Cliente_Final_ID, 
                                                                    cf.Nome as Cliente_Final,
                                                                    cf.Foto as Foto_Cliente_Final,
                                                                    opp.Quantidade as Quantidade, 
                                                                    opp.Cobranca_Cliente, 
                                                                    opp.Pagamento_Prestador,
                                                                    opp.Data_Cadastro, 
                                                                    cd.Nome as Autor,
                                                                    ma.Nome_Arquivo as Nome_Arquivo, 
                                                                    tp.Descr_Tipo as Tipo, 
                                                                    fc.Descr_Tipo as Forma_Cobranca,
                                                                    op.Proposta_ID as Proposta_ID,
                                                                    pd.Produto_ID as Produto_ID, 
                                                                    ow.Solicitante_ID as Solicitante_ID,
                                                                    op.Forma_Pagamento_ID as Forma_Pagamento_ID
                                                                  FROM orcamentos_propostas_produtos opp
                                                                  INNER JOIN orcamentos_propostas op ON op.Proposta_ID = opp.Proposta_ID
                                                                  INNER JOIN orcamentos_workflows ow on ow.Workflow_ID = op.Workflow_ID
                                                                  INNER JOIN produtos_variacoes pv ON pv.Produto_Variacao_ID = opp.Produto_Variacao_ID
                                                                  INNER JOIN produtos_dados pd ON pd.Produto_ID = pv.Produto_ID
                                                                  INNER JOIN tipo tp ON tp.Tipo_ID = pd.Tipo_Produto
                                                                  INNER JOIN tipo fc ON fc.Tipo_ID = pv.Forma_Cobranca_ID
                                                                  LEFT JOIN modulos_anexos ma ON ma.Anexo_ID = pv.Imagem_ID
                                                                  LEFT JOIN cadastros_dados cd ON cd.Cadastro_ID = opp.Usuario_Cadastro_ID
                                                                  LEFT JOIN cadastros_dados re ON re.Cadastro_ID = opp.Prestador_ID
                                                                  LEFT JOIN cadastros_dados cf ON cf.Cadastro_ID = opp.Cliente_Final_ID
                                                                  WHERE opp.Proposta_ID = ? AND opp.Situacao_ID = 1
                                                                  ORDER BY opp.Data_Cadastro DESC
                                                                  ", [$propostas->Proposta_ID]);


                            //var_dump($statusProposta);

                            if(!empty($produtosPropostas)){

                                foreach ($produtosPropostas as $produto) {

                                  //CONTABILIZA O TOTAL A SER COBRADO DO CLIENTE
                                  if($produto->Cobranca_Cliente == 1){

                                     $precoTotal  = $produto->Quantidade * $produto->Valor_Venda_Unitario;

                                    //PRODUTO SERÁ FATURADO DO CLIENTE
                                    $totalProdutosFaturar = $totalProdutosFaturar + $precoTotal;

                                  }
                                  
                                  //VERIFICA SE O PRODUTO É UMA DESPESA
                                  if($produto->Pagamento_Prestador == 1){


                                    //PRODUTO É UMA DESPESA
                                    $precoTotal  = $produto->Quantidade * $produto->Valor_Custo_Unitario;

                                    //PRODUTO SERÁ FATURADO DO CLIENTE
                                    $totalProdutoDespesa = $totalProdutoDespesa + $precoTotal;

                                  }
                                  //var_dump($produto->Pagamento_Prestador);

                                }

                            }


                        }


                        //CARREGA O(S) CHAMADO(S) DO ORÇAMENTO

                        $chamadoOrcamentos = DB::select('SELECT cw.Workflow_ID as Chamado_ID
                                                          FROM orcamentos_chamados oc
                                                          INNER JOIN chamados_workflows cw on cw.Workflow_ID = oc.Chamado_ID
                                                          WHERE oc.Orcamento_ID = ? 
                                                          AND oc.Situacao_ID = 1', [$workflowID]
                                                        );

                        if(!empty($chamadoOrcamentos)){

                          foreach ($chamadoOrcamentos as $chamado) {
                            //echo $chamado->Chamado_ID;

                            $produtosChamado = DB::select('SELECT Workflow_ID,
                                                            Quantidade,
                                                            Valor_Custo_Unitario,
                                                            Cobranca_Cliente,
                                                            Situacao_ID
                                                            FROM chamados_workflows_produtos
                                                            WHERE Workflow_ID = ? AND Situacao_ID = 1',[$chamado->Chamado_ID]);

                            if(!empty($produtosChamado)){

                              foreach ($produtosChamado as $produto) {
                                
                                if($produto->Situacao_ID == 1){

                                  $totalGastoCampo = $totalGastoCampo + ($produto->Quantidade * $produto->Valor_Custo_Unitario);

                                }


                              }

                            }


                          }

                        }else{

                          $totalGastoCampo = 0;

                        }


                        // var_dump($totalProdutosFaturar);
                        // var_dump($tipoModificacao);
                        // var_dump($bonusModificacao);
                        //echo "<br>";

                        //$faturamentoProposta    =   '';                         

                        //$faturamento   =  $totalProdutosFaturar;
                        $gastos        =  $totalProdutoDespesa + $totalGastoCampo;

                        //CONTABILIZAR OS VALORES DE DESCONTO OU ACRESCIMO NO TOTAL DE PRODUTOS  

                        $totalModificado = ($totalProdutosFaturar / 100) * $bonusModificacao;


                        if($tipoModificacao == 'Desconto'){

                          $faturamento   =  $totalProdutosFaturar - $totalModificado;

                        }else{

                          $faturamento   =  $totalProdutosFaturar + $totalModificado;

                        }
                

                    }else{

                        $faturamento   =  'Não definido';
                        
                        //Carrega os gastos efetuados com o orçamento
                        $gastos        =  'Não definido';

                    }


                //$produtosOrcamento = "";


                $orcamentoCompleto = array('orcamento'      => $orcamento,
                                            'faturamento'   => $faturamento,
                                            'gastos'        => $gastos,
                                            'situacaoCor'   => $corSituacao
                                            );  

                array_push($dadosOrcamentos, $orcamentoCompleto);
                
            }

            //var_dump($faturamento);
            //dd($dadosOrcamentos);
            $dados = array('dadosOrcamentos' => $dadosOrcamentos);


            return view('orcamento.orcamento_index', $dados);

        }else{

            $dados = array('dadosOrcamentos' => $dadosOrcamentos);

            return view('orcamento.despesa_origem_servico', $dados);
        }

        //dd($orçamentos);

    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
        if(is_numeric($id)){

          $dadosOrcamentos    = Array();
          $dadosProduto       = Array();

          $msg_error  = '';

          $orcamento  = DB::select('SELECT
                                    w.Workflow_ID,
                                    w.Titulo,
                                    w.Solicitante_ID,
                                    w.Situacao_ID,
                                    w.Data_Abertura,
                                    w.Data_Finalizado,
                                    w.Usuario_Cadastro_ID,
                                    s.Descr_Tipo,
                                    s.Tipo_Auxiliar,
                                    u.Nome
                                    FROM orcamentos_workflows w
                                    LEFT JOIN tipo s on s.Tipo_ID = w.Situacao_ID
                                    LEFT JOIN cadastros_dados u on u.Cadastro_ID = w.Usuario_Cadastro_ID
                                    WHERE w.Workflow_ID = ?', [$id]);


          if(! empty($orcamento)){

            $orcamento    = $orcamento[0];

            $workflowID   = $orcamento->Workflow_ID;

            $temp         = explode('cor-fundo";s:7:', $orcamento->Tipo_Auxiliar);

            $parte        = substr($temp[1], 1, 7);

            $corSituacao  = $parte; 


            $propostasOrcamento = DB::select('SELECT op.Proposta_ID,
                                                        op.Workflow_ID,
                                                        op.Titulo,
                                                        op.Data_Cadastro,
                                                        op.Usuario_Cadastro_ID,
                                                        u.Nome AS Usuario,
                                                        SUM(opp.Quantidade) as Quantidade_Total_Proposta,
                                                        SUM(opp.Quantidade * opp.Valor_Venda_Unitario) as Valor_Total_Proposta,
                                                        count(opp.Proposta_Produto_ID) as Total_Itens_Proposta,
                                                        op.Status_ID as Status_ID,
                                                        upper(t.Descr_Tipo) as Status,
                                                        ow.Situacao_ID as Situacao_ID,
                                                        fc.Descr_Tipo as Forma_Cobranca,
                                                        fc.Tipo_Auxiliar as Detalhe_Cobranca
                                                    FROM orcamentos_propostas op
                                                    INNER JOIN orcamentos_workflows ow on ow.Workflow_ID = op.Workflow_ID
                                                    LEFT JOIN produtos_tabelas_precos tp on tp.Tabela_Preco_ID = op.Tabela_Preco_ID
                                                    LEFT JOIN orcamentos_propostas_produtos opp on opp.Proposta_ID = op.Proposta_ID and opp.Situacao_ID = 1
                                                    LEFT JOIN cadastros_dados u ON u.Cadastro_ID = op.Usuario_Cadastro_ID
                                                    LEFT JOIN tipo t on t.Tipo_ID = op.Status_ID
                                                    INNER JOIN tipo fc ON fc.Tipo_ID = op.Forma_Pagamento_ID
                                                    WHERE op.Workflow_ID = ? and op.Situacao_ID = 1
                                                    GROUP BY op.Proposta_ID, op.Workflow_ID, op.Titulo, op.Data_Cadastro, op.Usuario_Cadastro_ID, u.Nome, op.Status_ID, t.Descr_Tipo, ow.Situacao_ID, fc.Descr_Tipo, fc.Tipo_Auxiliar
                                                     ', [$workflowID]);


            //dd($propostasOrcamento);


            if(!empty($propostasOrcamento)){

              foreach ($propostasOrcamento as $propostas) {

                $totalProdutosFaturar   = 0;
                $totalProdutoDespesa    = 0;
                $totalGastoCampo        = 0;

                //RECUPERA A FORMA DE PAGAMENTO DA PROPOSTA PARA CALCULAR O VALOR COBRADO
                $formaPagamento         = unserialize($propostas->Detalhe_Cobranca);

                if(!empty($formaPagamento['tipo-bonus-disponivel'])){

                  $tipoModificacao        = $formaPagamento['tipo-bonus-disponivel'];
                  $bonusModificacao       = $formaPagamento['valor_modificado'];

                }else{
                  $tipoModificacao        = 'Desconto';
                  $bonusModificacao       = 0;
                }


                $produtosPropostas      = DB::select("SELECT 
                                                        opp.Proposta_Produto_ID as Chave_Primaria_ID,
                                                        pv.Produto_Variacao_ID,
                                                        CONCAT(COALESCE(pd.Nome,''),
                                                        ' ',
                                                        COALESCE(pv.Descricao,'')) AS Descricao_Produto,
                                                        opp.Observacao_Produtos,
                                                        opp.Valor_Venda_Unitario, 
                                                        opp.Valor_Custo_Unitario, 
                                                        opp.Faturamento_Direto,
                                                        opp.Prestador_ID, 
                                                        re.Nome as Prestador,
                                                        opp.Cliente_Final_ID, 
                                                        cf.Nome as Cliente_Final,
                                                        cf.Foto as Foto_Cliente_Final,
                                                        opp.Quantidade as Quantidade, 
                                                        opp.Cobranca_Cliente, 
                                                        opp.Pagamento_Prestador,
                                                        opp.Data_Cadastro, 
                                                        cd.Nome as Autor,
                                                        ma.Nome_Arquivo as Nome_Arquivo, 
                                                        tp.Descr_Tipo as Tipo, 
                                                        fc.Descr_Tipo as Forma_Cobranca,
                                                        op.Proposta_ID as Proposta_ID,
                                                        pd.Produto_ID as Produto_ID, 
                                                        ow.Solicitante_ID as Solicitante_ID,
                                                        op.Forma_Pagamento_ID as Forma_Pagamento_ID
                                                        FROM orcamentos_propostas_produtos opp
                                                        INNER JOIN orcamentos_propostas op ON op.Proposta_ID = opp.Proposta_ID
                                                        INNER JOIN orcamentos_workflows ow on ow.Workflow_ID = op.Workflow_ID
                                                        INNER JOIN produtos_variacoes pv ON pv.Produto_Variacao_ID = opp.Produto_Variacao_ID
                                                        INNER JOIN produtos_dados pd ON pd.Produto_ID = pv.Produto_ID
                                                        INNER JOIN tipo tp ON tp.Tipo_ID = pd.Tipo_Produto
                                                        INNER JOIN tipo fc ON fc.Tipo_ID = pv.Forma_Cobranca_ID
                                                        LEFT JOIN modulos_anexos ma ON ma.Anexo_ID = pv.Imagem_ID
                                                        LEFT JOIN cadastros_dados cd ON cd.Cadastro_ID = opp.Usuario_Cadastro_ID
                                                        LEFT JOIN cadastros_dados re ON re.Cadastro_ID = opp.Prestador_ID
                                                        LEFT JOIN cadastros_dados cf ON cf.Cadastro_ID = opp.Cliente_Final_ID
                                                        WHERE opp.Proposta_ID = ? AND opp.Situacao_ID = 1
                                                        ORDER BY opp.Data_Cadastro DESC
                                                                  ", [$propostas->Proposta_ID]);


                //dd($produtosPropostas);

                if(!empty($produtosPropostas)){

                  foreach ($produtosPropostas as $produto) {

                    array_push($dadosProduto, $produto);

                    //CONTABILIZA O TOTAL A SER COBRADO DO CLIENTE
                    if($produto->Cobranca_Cliente == 1){

                      $precoTotal  = $produto->Quantidade * $produto->Valor_Venda_Unitario;

                      //PRODUTO SERÁ FATURADO DO CLIENTE
                      $totalProdutosFaturar = $totalProdutosFaturar + $precoTotal;

                    }
                                    
                    //VERIFICA SE O PRODUTO É UMA DESPESA
                    if($produto->Pagamento_Prestador == 1){


                      //PRODUTO É UMA DESPESA
                      $precoTotal  = $produto->Quantidade * $produto->Valor_Custo_Unitario;

                      //PRODUTO SERÁ FATURADO DO CLIENTE
                      $totalProdutoDespesa = $totalProdutoDespesa + $precoTotal;

                    }
                    //var_dump($produto->Pagamento_Prestador);

                  }

                }


                //CARREGA O(S) CHAMADO(S) DO ORÇAMENTO

                $chamadoOrcamentos = DB::select('SELECT cw.Workflow_ID as Chamado_ID
                                                    FROM orcamentos_chamados oc
                                                    INNER JOIN chamados_workflows cw on cw.Workflow_ID = oc.Chamado_ID
                                                    WHERE oc.Orcamento_ID = ? 
                                                    AND oc.Situacao_ID = 1', [$workflowID]
                                                );

                if(!empty($chamadoOrcamentos)){

                  foreach ($chamadoOrcamentos as $chamado) {

                    $produtosChamado = DB::select('SELECT prod.Workflow_ID,
                                                          prod.Quantidade,
                                                          prod.Valor_Custo_Unitario,
                                                          prod.Cobranca_Cliente,
                                                          prod.Situacao_ID,
                                                          prod.Pagamento_Prestador,
                                                          prod.Descricao_Produto,
                                                          prod.Valor_Venda_Unitario,
                                                          desp.Descricao_Produto
                                                            FROM chamados_workflows_produtos prod
                                                            LEFT JOIN despesas_atendimento desp on desp.Despesa_ID_Relacionado = prod.Workflow_Produto_ID
                                                            WHERE prod.Workflow_ID = ? AND prod.Situacao_ID = 1',[$chamado->Chamado_ID]);
                    if(!empty($produtosChamado)){

                      foreach ($produtosChamado as $produto) {

                        array_push($dadosProduto, $produto);
                                
                        if($produto->Situacao_ID == 1){

                          $totalGastoCampo = $totalGastoCampo + ($produto->Quantidade * $produto->Valor_Custo_Unitario);

                        }

                      }

                    }

                  }

                }else{

                  $totalGastoCampo = 0;

                }


                $gastos        =  $totalProdutoDespesa + $totalGastoCampo;

                //CONTABILIZAR OS VALORES DE DESCONTO OU ACRESCIMO NO TOTAL DE PRODUTOS  

                $totalModificado = ($totalProdutosFaturar / 100) * $bonusModificacao;


                if($tipoModificacao == 'Desconto'){

                  $faturamento   =  $totalProdutosFaturar - $totalModificado;

                }else{

                  $faturamento   =  $totalProdutosFaturar + $totalModificado;

                }

              }


            }else{

              $faturamento   =  'Não definido';
                        
              //Carrega os gastos efetuados com o orçamento
              $gastos        =  'Não definido';

            }

            $orcamentoCompleto = array('orcamento'      => $orcamento,
                                        'faturamento'   => $faturamento,
                                        'gastos'        => $gastos,
                                        'situacaoCor'   => $corSituacao,
                                        'produtos'      => $dadosProduto
                                      );  

            array_push($dadosOrcamentos, $orcamentoCompleto);


            //var_dump($faturamento);
            
            $dados = array('dadosOrcamentos' => $dadosOrcamentos);

            //dd($dadosOrcamentos);
            return view('orcamento.orcamento_detalhes', $dados);

          }else{

            $dados = array('dadosOrcamentos' => $dadosOrcamentos);

            return view('orcamento.orcamento_detalhes', $dados);

          }


        }else{

          return view('home');

        }

    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
