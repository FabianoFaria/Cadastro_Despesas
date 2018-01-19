<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Validator;
use DB;
use Illuminate\Support\Facades\Auth;


class DespesasController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        return view('despesa.despesa_index');

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
        $rules = array(
            'despesa'       =>'required',
            'quantidade'    =>'required|numeric',
            'valor'         =>'required|numeric|between:0,999999.99'
        );


        $validacao  = $this->isValid($input = Input::all(), $rules);

        if( $validacao != ''){

            //return Redirect::back()->withInput()->withErrors($validacao);

            //return redirect()->action('DespesasController@procurar', ['protocolo' => Input::get('protocolo'), 'opcao' => Input::get('opcao')]);

            return redirect()->action('DespesasController@procurar', ['protocolo' => Input::get('protocolo'), 'opcao' => Input::get('opcao')])->withInput()->withErrors($validacao);

        }else{

            echo "Ok!";

            /*
                DB::insert('insert into users (id, name) values (?, ?)', [1, 'Dayle']);
            */

            $origem     = Input::get('opcao');
            $id_origem  = Input::get('protocolo');
            $despesa    = Input::get('despesa');
            $valor      = Input::get('valor');
            $quantidade = Input::get('quantidade');
            $observacao = Input::get('observacao');
            $chamada    = Input::get('chamada');
            $data_Cadastro = date('Y-m-d H:i:s');

            if(Input::get('observacao') == 'chamado'){
                $origem = 1;
            }else{
                $origem = 2;
            }

            $user               = Auth::user();

            $idUsuario          = $user->Cadastro_ID;

            //ADICIONA A DESPESA NA TABELA PRÓPRIA DE DESPESAS CADASTRADAS PELOS TÉCNICOS

            $cadastroDespesa    = DB::insert('INSERT INTO despesas_atendimento 
                                           (Descricao_Produto, Quantidade, Valor_Produto, Observacao_Despesa,
                                           Usuario_Cadastro_ID, Origem_Despesa, tipo_origem_despesa) 
                                           VALUES (?, ?, ?, ?, ?, ?, ?)', [ $despesa, $quantidade, $valor, $observacao, $idUsuario, $id_origem, $origem]);

            //ADICIONA A DESPESA NA TABELA DE PRODUTOS DO CHAMADO GERADO PELO ORÇAMENTO OU SÓ PELO CHAMADO

            $cadastroDespesaProduto = DB::insert('INSERT INTO chamados_workflows_produtos
                                                (Workflow_ID, Produto_Variacao_ID, Quantidade, Valor_Custo_Unitario, Valor_Venda_Unitario, Cobranca_Cliente, Pagamento_Prestador, Faturamento_Direto, Prestador_ID, Situacao_ID, Data_Cadastro, Usuario_Cadastro_ID, Observacao_Produtos)
                                                VALUES
                                                (?,?,?,?,?,?,?,?,?,?,?,?,?)
                                                ', [$chamada, 0, $quantidade, $valor, 0, 1, 1, 0, 0, 1, $data_Cadastro, $idUsuario, $observacao]);

            //dd($cadastroDespesa);

            /*
            INSERT INTO chamados_workflows_produtos
            (Workflow_ID, 
            Produto_Variacao_ID, 
            Quantidade, 
            Valor_Custo_Unitario, 
            Valor_Venda_Unitario, 
            Cobranca_Cliente, 
            Pagamento_Prestador, 
            Faturamento_Direto, 
            Prestador_ID, 
            Situacao_ID, 
            Data_Cadastro,  
            Usuario_Cadastro_ID, 
            Observacao_Produtos) 
            VALUE 
            ('8900',
            '1074',
             '1',
             '348.00',
             '60.24',
             '1',
             '1',
             '0',
             '14452',
             1,
             '2018-01-12 10:36:21',
             '-1',
             'Teste de despesa.'
             )
            */


            if($cadastroDespesa){

                return redirect()->action('DespesasController@procurar', ['protocolo' => Input::get('protocolo'), 'opcao' => Input::get('opcao')])->with('msg_ok', 'Despesa cadastrada com sucesso!');

            }else{

                return redirect()->action('DespesasController@procurar', ['protocolo' => Input::get('protocolo'), 'opcao' => Input::get('opcao')])->with('msg_error', 'Ocorreu um erro ao tentar cadastrar, favor tentar em alguns instantes.');
            }


        }

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
        
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit()
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


    /*
        Route::any('/search',function(){
            $q = Input::get ( 'q' );
            $user = User::where('name','LIKE','%'.$q.'%')->orWhere('email','LIKE','%'.$q.'%')->get();
            if(count($user) > 0)
                return view('welcome')->withDetails($user)->withQuery ( $q );
            else return view ('welcome')->withMessage('No Details found. Try to search again !');
        });
    */

    public function procurar(){

        if(empty(Input::get('protocolo'))){

            $errors = array('protocolo' => 'É obrigatório informa um número!');

            return Redirect::back()->withErrors($errors);

        }else{

            //dd(Input::all());
            $opcao      = Input::get('opcao');
            $protocolo  = Input::get('protocolo');

            if($opcao == 'orcamento'){

                /*
                    Carrega os dados do orçamento
                */

                $servico = DB::select('SELECT w.Workflow_ID,
                                        w.Empresa_ID, 
                                        w.Solicitante_ID, 
                                        w.Representante_ID, 
                                        w.Situacao_ID, 
                                        w.Codigo, 
                                        w.Titulo, 
                                        w.Data_Abertura, 
                                        w.Data_Finalizado,
                                        w.Data_Cadastro, 
                                        w.Usuario_Cadastro_ID,
                                        w.Origem_ID,
                                        w.Parceiro_ID,
                                        s.Descr_Tipo as Situacao,
                                        c.Nome,
                                        c.Nome_Fantasia
                                        FROM orcamentos_workflows w
                                        LEFT JOIN tipo s on s.Tipo_ID = w.Situacao_ID
                                        LEFT JOIN cadastros_dados c on c.Cadastro_ID = w.Usuario_Cadastro_ID
                                        WHERE Workflow_ID = ?', [$protocolo]);


                if(!empty($servico)){

                    $servicoDetalhes = $servico[0];

                    $solicitanteID   = $servicoDetalhes->Solicitante_ID;

                    /*
                        PROCURAR POR UM NÚMERO DE CHAMADO NA QUAL SERÁ CADASTRADO AS DESPESAS
                    
                    */

                    $numeroChamado   = DB::select("SELECT
                                                    Orcamento_Chamado_ID,
                                                    Orcamento_ID,
                                                    Chamado_ID
                                                    FROM orcamentos_chamados
                                                    WHERE Orcamento_ID = ?
                                                    ", [$protocolo]);

                    //dd($numeroChamado[0]->Chamado_ID);

                    if(!empty($numeroChamado)){
                        $chamado = $numeroChamado[0]->Chamado_ID;
                    }else{
                        $chamado = "";
                    }

                   
                    /*
                        Carrega os dados do endereço do cliente
                    */

                    $endereco  = DB::select("SELECT
                                            Logradouro,
                                            Numero,
                                            Complemento,
                                            Bairro,
                                            Cidade,
                                            UF
                                            FROM cadastros_enderecos
                                            WHERE Cadastro_ID = ?
                                            ", [$solicitanteID]);
                    if(!empty($endereco)){
                        $enderecoSolicitante = $endereco[0];
                    }else{
                        $enderecoSolicitante = '';
                    }

                    /*
                        Carrega os dados do cliente
                    */

                    $historico = DB::select("SELECT
                                                Follow_ID, 
                                                Descricao, 
                                                Dados, 
                                                t.Descr_Tipo as Situacao, 
                                                f.Situacao_ID as Situacao_ID, 
                                                DATE_FORMAT(Data_Cadastro, '%d/%m/%Y %H:%i') as Data_Cadastro, 
                                                cd.Nome as Usuario_Follow
                                                FROM orcamentos_follows f
                                                LEFT JOIN cadastros_dados cd on cd.Cadastro_ID = f.Usuario_Cadastro_ID
                                                LEFT JOIN tipo t on t.Tipo_ID = f.Situacao_ID
                                                WHERE Workflow_ID = ?
                                                ORDER BY f.Follow_ID desc", [$protocolo]
                                            );


                    /*
                        Carrega os dados do usuário
                    */

                    $empresa   = DB::select("SELECT
                                            Tipo_Pessoa, 
                                            Tipo_Cadastro, 
                                            Grupo_ID, 
                                            Codigo, 
                                            Centro_Custo_ID, 
                                            Nome, 
                                            Nome_Fantasia, 
                                            Senha, 
                                            Email, 
                                            Data_Nascimento,
                                            Foto, 
                                            Cpf_Cnpj, 
                                            RG, 
                                            Inscricao_Municipal, 
                                            Inscricao_Estadual, 
                                            Tipo_Vinculo, 
                                            Observacao, 
                                            Usuario_Cadastro_ID, 
                                            Situacao_ID, 
                                            Areas_Atuacoes, 
                                            Sexo, 
                                            Regional_ID,
                                            Origem_ID,
                                            Parceiro_Origem_ID
                                            FROM cadastros_dados
                                            WHERE Cadastro_ID = ?
                                            ", [$solicitanteID]);

                    if(!empty($empresa)){

                        $solicitante = $empresa[0];

                    }else{

                        $solicitante = null;

                    }

                    /*
                        Carrega os dados das despesas já lançadas
                    */

                    $despesas = DB::select("SELECT
                                                d.Despesa_ID,
                                                d.Descricao_Produto,
                                                d.Quantidade,
                                                d.Valor_Produto,
                                                d.Observacao_Despesa,
                                                d.Situacao_ID,
                                                d.Usuario_Cadastro_ID,
                                                u.Nome
                                                FROM despesas_atendimento d
                                                LEFT JOIN cadastros_dados u on d.Usuario_Cadastro_ID = u.Cadastro_ID
                                                WHERE Origem_Despesa = ?
                                                ", [$protocolo]
                                            );

                    if(!empty($despesas)){

                        $despesasCadastradas = $despesas;

                    }else{

                        $despesasCadastradas = '';
                    }


                }else{

                    $errors = array('protocolo' => 'Número de orçamento não encontrado!');

                    return Redirect::back()->withErrors($errors);

                }


                //dd($servicoDetalhes);

                $dados = array( 'tipo'          => 'orcamento',
                                'solicitante'   => $solicitante,
                                'servico'       => $servicoDetalhes,
                                'historico'     => $historico,
                                'endereco'      => $enderecoSolicitante,
                                'despesas'      => $despesasCadastradas,
                                'chamada'       => $chamado
                            );

            }else{

                $servico = DB::select('SELECT w.Workflow_ID,
                                        w.Titulo,
                                        w.Solicitante_ID,
                                        c.Nome,
                                        c.Nome_Fantasia
                                        FROM chamados_workflows w
                                        LEFT JOIN cadastros_dados c on c.Cadastro_ID = w.Cadastro_ID
                                        WHERE w.Workflow_ID = ?', [$protocolo]);


                if(!empty($servico)){

                    $servicoDetalhes = $servico[0];

                    $solicitanteID   = $servicoDetalhes->Solicitante_ID;


                    /*
                        Carrega os dados do endereço do cliente
                    */

                    $endereco  = DB::select("SELECT
                                            Logradouro,
                                            Numero,
                                            Complemento,
                                            Bairro,
                                            Cidade,
                                            UF
                                            FROM cadastros_enderecos
                                            WHERE Cadastro_ID = ?
                                            ", [$solicitanteID]);
                    if(!empty($endereco)){
                        $enderecoSolicitante = $endereco[0];
                    }else{
                        $enderecoSolicitante = '';
                    }


                    /*
                        Carrega os dados do cliente
                    */

                    $historico = DB::select("SELECT
                                                Follow_ID, 
                                                Descricao, 
                                                Dados, 
                                                t.Descr_Tipo as Situacao, 
                                                f.Situacao_ID as Situacao_ID, 
                                                DATE_FORMAT(Data_Cadastro, '%d/%m/%Y %H:%i') as Data_Cadastro, 
                                                cd.Nome as Usuario_Follow
                                                FROM chamados_follows f
                                                LEFT JOIN cadastros_dados cd on cd.Cadastro_ID = f.Usuario_Cadastro_ID
                                                LEFT JOIN tipo t on t.Tipo_ID = f.Situacao_ID
                                                WHERE Workflow_ID = ?
                                                ORDER BY f.Follow_ID desc", [$protocolo]
                                            );

                    /*
                        SELECT Follow_ID, Descricao, Dados, t.Descr_Tipo as Situacao, cf.Situacao_ID as Situacao_ID, DATE_FORMAT(Data_Cadastro, '%d/%m/%Y %H:%i') as Data_Cadastro, cd.Nome as Usuario_Follow
                FROM chamados_follows cf
                LEFT JOIN cadastros_dados cd on cd.Cadastro_ID = cf.Usuario_Cadastro_ID
                LEFT JOIN tipo t on t.Tipo_ID = cf.Situacao_ID
                WHERE Workflow_ID = $workflowID
                ORDER BY cf.Follow_ID desc


                    */

                    /*
                        Carrega os dados do usuário
                    */

                    $empresa   = DB::select("SELECT
                                            Tipo_Pessoa, 
                                            Tipo_Cadastro, 
                                            Grupo_ID, 
                                            Codigo, 
                                            Centro_Custo_ID, 
                                            Nome, 
                                            Nome_Fantasia, 
                                            Senha, 
                                            Email, 
                                            Data_Nascimento,
                                            Foto, 
                                            Cpf_Cnpj, 
                                            RG, 
                                            Inscricao_Municipal, 
                                            Inscricao_Estadual, 
                                            Tipo_Vinculo, 
                                            Observacao, 
                                            Usuario_Cadastro_ID, 
                                            Situacao_ID, 
                                            Areas_Atuacoes, 
                                            Sexo, 
                                            Regional_ID,
                                            Origem_ID,
                                            Parceiro_Origem_ID
                                            FROM cadastros_dados
                                            WHERE Cadastro_ID = ?
                                            ", [$solicitanteID]);

                    if(!empty($empresa)){

                        $solicitante = $empresa[0];

                    }else{

                        $solicitante = null;

                    }

                    /*
                        Carrega os dados das despesas já lançadas
                    */

                    $despesas = DB::select("SELECT
                                                d.Despesa_ID,
                                                d.Descricao_Produto,
                                                d.Quantidade,
                                                d.Valor_Produto,
                                                d.Observacao_Despesa,
                                                d.Situacao_ID,
                                                d.Usuario_Cadastro_ID,
                                                u.Nome
                                                FROM despesas_atendimento d
                                                LEFT JOIN cadastros_dados u on d.Usuario_Cadastro_ID = u.Cadastro_ID
                                                WHERE Origem_Despesa = ?
                                                ", [$protocolo]
                                            );

                    if(!empty($despesas)){

                        $despesasCadastradas = $despesas;

                    }else{

                        $despesasCadastradas = '';
                    }

                    //dd($historico);


                }else{

                    $errors = array('protocolo' => 'Número de chamado não encontrado!');

                    return Redirect::back()->withErrors($errors);

                }

                $dados = array( 'tipo'          => 'chamado',
                                'solicitante'   => $solicitante,
                                'servico'       => $servicoDetalhes,
                                'historico'     => $historico,
                                'endereco'      => $enderecoSolicitante,
                                'despesas'      => $despesasCadastradas,
                                'chamada'       => $protocolo
                            );
            }

            return view('despesa.despesa_origem_servico', $dados);

        }


    }

    static function isValid($data, $rules){

        //FAZENDO A VALIDAÇÃO COM OS ATRIBUTOS DO PROPRIO OBJETO
        //$validacao = Validator::Make($this->attributes, static::$rules);

        $validacao = Validator::Make($data, $rules);

        if($validacao->passes()){

            $errors = '';

            return $errors;

        }else{

            $errors = $validacao->messages();

            return $errors;

        }

    }

}
