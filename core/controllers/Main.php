<?php

namespace core\controllers;
    
use core\classes\Database;
use core\classes\SendEmail;
use core\classes\Store;
use core\models\Clients;
use core\models\Products;

class Main{

    // ============================================================
    public function index(){

        // Apresenta a pagina inicial
        Store::Layout([
            'layouts/html_header',
            'layouts/header',
            'home',
            'layouts/footer',
            'layouts/html_footer'
        ]);
    }

    // ============================================================
    public function webstore(){

        // Lista de produtos disponiveis em stock da BD
        $produtos = new Products();

        // Analisa a categoria/secção a mostrar
        $categoria = 'geral';
        if(isset($_GET['c'])){
            $categoria = $_GET['c'];
        }

        // Vai buscar todos os produtos e a lista de categorias disponiveis na BD
        $lista_produtos = $produtos->lista_produtos_disponiveis($categoria);
        $lista_categorias = $produtos->lista_categorias();

        $dados = [
            'produtos' => $lista_produtos,
            'categorias' => $lista_categorias
        ];

        // Apresenta a pagina da loja
        Store::Layout([
            'layouts/html_header',
            'layouts/header',
            'webstore',
            'layouts/footer',
            'layouts/html_footer'
        ], $dados);
    }

    // ============================================================
    public function signup(){

        // Verifica se ja existe sessao
        if (Store::clienteLogado()) {
            $this->index();
            return;
        }

        // Apresenta a pagina do signup
        Store::Layout([
            'layouts/html_header',
            'layouts/header',
            'signup',
            'layouts/footer',
            'layouts/html_footer'
        ]);
    }

    // ============================================================
    public function signup_submit(){

        // Verifica se ja existe sessao
        if (Store::clienteLogado()) {
            $this->index();
            return;
        }

        // Verifica se houve submissão de um formulario
        if ($_SERVER['REQUEST_METHOD'] != 'POST') {
            $this->index();
            return;
        }

        // Verifica se as passwords do formulario coincidem
        if ($_POST['text_senha1'] != $_POST['text_senha2']) {

            // Mensagem de erro se as passwords nao coincidirem
            $_SESSION['erro'] = 'As senhas não coincidem';
            $this->signup();
            return;
        }

        // Verifica e apresenta mensagem de erro caso exista uma conta com o mesmo email
        $cliente = new Clients();

        if ($cliente->checkifMailExists($_POST['text_email'])) {
            $_SESSION['erro'] = 'Já existe um cliente com o mesmo email';
            $this->signup();
            return;
        }

        // Insere novo cliente na BD e devolve o PURL
        $email_client = strtolower(trim($_POST['text_email']));
        $purl = $cliente->saveClientBD();

        // Envio do email para o cliente
        $email = new SendEmail();
        $send_email = $email->sendEmailCheckNewClient($email_client, $purl);

        if ($send_email) {
            // Apresenta mensagem de conta criada e email enviado com sucesso
            Store::Layout([
                'layouts/html_header',
                'layouts/header',
                'signup_success',
                'layouts/footer',
                'layouts/html_footer'
            ]);
            return;
        }
    }

    // ============================================================
    public function confirm_email(){

        // Verifica se ja existe sessao
        if (Store::clienteLogado()) {
            $this->index();
            return;
        }

        // Verifica na query string se existe um PURL
        if (!isset($_GET['purl'])) {
            $this->index();
            return;
        }

        // Verifica se o PURL é valido
        $purl = $_GET['purl'];
        if (strlen($purl) != 12) {
            $this->index();
            return;
        }

        $cliente = new Clients();
        $validation = $cliente->validate_email($purl);

        if ($validation) {
            // Apresenta mensagem de conta validada com sucesso
            Store::Layout([
                'layouts/html_header',
                'layouts/header',
                'validate_success',
                'layouts/footer',
                'layouts/html_footer'
            ]);
            return;
        } else {
            // Redireciona para a pagina inicial
            Store::redirect();
        }
    }

    // ============================================================
    public function login(){

        // Verifica se já existe um cliente logado
        if (Store::clienteLogado()) {
            Store::redirect();
            return;
        }

        // Apresenta o formulário de login
        Store::Layout([
            'layouts/html_header',
            'layouts/header',
            'login',
            'layouts/footer',
            'layouts/html_footer'
        ]);
    }

    // ============================================================
    public function login_submit(){

        // Verifica se já existe um cliente logado
        if (Store::clienteLogado()) {
            Store::redirect();
            return;
        }

        // Verifica se foi efectuado o POST do formulário de login
        if ($_SERVER['REQUEST_METHOD'] != 'POST') {
            Store::redirect();
            return;
        }

        // Verifica se os campos do login vieram correctamente preenchidos
        if (
            !isset($_POST['text_utilizador']) ||
            !isset($_POST['text_senha']) ||
            !filter_var(trim($_POST['text_utilizador']), FILTER_VALIDATE_EMAIL)
        ) {
            // Erro de preenchimento do formulário
            $_SESSION['erro'] = 'Login inválido';
            Store::redirect('login');
            return;
        }

        // Prepara os dados para o model clients
        $utilizador = trim(strtolower($_POST['text_utilizador']));
        $senha = trim($_POST['text_senha']);

        // Carrega o model e verifica se o login é válido
        $cliente = new Clients();
        $resultado = $cliente->validate_login($utilizador, $senha);

        // Analisa o resultado
        if(is_bool($resultado)){
            // Login inválido
            $_SESSION['erro'] = 'Login inválido';
            Store::redirect('login');
            return;
        } else {
            // Login válido - Coloca os dados na sessão
            $_SESSION['cliente'] = $resultado->id_cliente;
            $_SESSION['utilizador'] = $resultado->email;
            $_SESSION['nome_cliente'] = $resultado->nome_completo;

            // Redirecciona correspondentemente ao estado do cliente
            if(isset($_SESSION['tmp_cart'])){
                // Remove a varivável temporária da sessão
                unset($_SESSION['tmp_cart']);
                // Redireciona para o resumo da encomenda
                Store::redirect('finalizeOrderResume');
            } else {
                // Redireciona para a loja
                Store::redirect();
            }
        }
    }

    // ============================================================
    public function logout(){

        // Remove as variáveis da sessão
        unset($_SESSION['cliente']);
        unset($_SESSION['utilizador']);
        unset($_SESSION['nome_cliente']);

        // Redirecciona para a home page da webstore
        Store::redirect();
    }
}