<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Geral extends CI_Controller {

	public function index()
	{
		$this->load->helper('url');
		$this->load->view('view_header');
		$this->load->view('view_navbar');
		$this->load->view('view_modal');
		$this->load->view('view_login');
		$this->load->view('view_toast');
		$this->load->view('view_home');
		$this->load->view('view_footer');
	}

	public function pontos()
	{
		$this->load->model('Model_pontos');
		$pontos = $this->Model_pontos->selectPonto();
		
        foreach ($pontos as $n => $row) 
        { 
            $ponto[$n] = [
                'cod' => $row->cod_ponto,
                'vagasLivres' => $row->vagas_livres_ponto,
                'vagasTotais' => $row->vagas_totais_ponto,
                'descricao' => $row->descricao,
                'mapa' => $row->mapa
			];
        }

		$dados['pontos'] = $ponto;

		$this->load->helper('url');
		$this->load->view('view_header');
		$this->load->view('view_navbar');
		$this->load->view('view_modal');
		$this->load->view('view_login');
		$this->load->view('view_toast');
		$this->load->view('view_servico');
		$this->load->view('view_pontos', $dados);
		$this->load->view('view_footer');
	}
	
	public function sobre()
	{
		$this->load->helper('url');
		$this->load->view('view_header');
		$this->load->view('view_navbar');
		$this->load->view('view_modal');
		$this->load->view('view_login');
		$this->load->view('view_toast');
		$this->load->view('view_sobre');
		$this->load->view('view_footer');
	}

	public function login() 
	{

		$usuario = $this->input->post('usuario');
		$senha = md5($this->input->post('senha'));
		
		$this->load->model('Model_login');
		$confirmacao = $this->Model_login->verificarLogin($usuario, $senha);

		if($confirmacao == "#erro")
		{
			echo "SenhaUserIn";
		}
		else
		{
			$this->session->set_userdata('user', $confirmacao);

			echo $confirmacao;
		}
	}
	

	public function cadastro() 
	{
		$cadNome = $this->input->post('cad_nome_completo');
		$cadCpf = $this->input->post('cad_cpf');
		$cadUsuario = $this->input->post('cad_usuario');
		$cadEmail = $this->input->post('cad_email');
		$cadTelefone = $this->input->post('cad_telefone');
		$cadSenha = md5($this->input->post('cad_senha'));

		$this->load->model('Model_cadastro');
		$confirmacao2 = $this->Model_cadastro->cadastrarUsuario($cadNome, $cadCpf, $cadUsuario, $cadEmail, $cadTelefone, $cadSenha);
		
		echo $confirmacao2;
	}
	
	public function sair() 
	{
		$this->session->unset_userdata('user');
	}
	
	public function servico($id_ponto, $tempo, $tipo){

		$valorTempo = $tempo;
		
		date_default_timezone_set('America/Sao_Paulo');

		$time = date("h:m:s");

		$tempoAlt = date('Y-m-d h:i:s', strtotime($time.' + '. $tempo . ' minutes'));

		$this->load->model('Model_servico');
		$this->Model_servico->cadastrarServico($id_ponto, $tempoAlt, $tipo, $valorTempo);

		header('Location:'.base_url('index.php/Geral/pontos'));
		
	}

	public function verificarStatusServicos()
	{
		$usuario = $_SESSION['user'];

		$this->load->model('Model_servico');
		
		$resultado = $this->Model_servico->verificarStatus($usuario);

		echo $resultado;
		
	}

	public function listarServ()
	{
		$usuario = $_SESSION['user'];

		$this->load->model('Model_servico');
		
		$servicos = $this->Model_servico->listarServicos($usuario);
		
        foreach ($servicos as $n => $row)
        { 
			if($row->status == 1) {
				$status = 'Ativo';
				$tipoMsg = 'success';
			}
			else {
				$status = 'Finalizado';
				$tipoMsg = 'danger';
			}

			$tempo = date_create($row->tempo_permanencia);

            $servicoHistorico[$n] = [
                'codServ' => $row->cod_servico,
                'descricaoPonto' => $row->descricao,
                'tipoServ' => $row->tipo,
                'tempoPerma' => date_format($tempo, "h:m"),
				'valorTempoPerma' => $row->valorTempo,
                'statusServ' => $status,
				'tipoMsg' => $tipoMsg
			];
        }

		$jsonData = json_encode($servicoHistorico);

		echo $jsonData;
	}
}
