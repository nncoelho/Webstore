<?php

namespace core\controllers;

use core\classes\SendEmail;
use core\classes\Store;
use core\models\Clients;
use core\models\Orders;
use core\models\Products;

class Main{

    // ============================================================
    public function index(){

        // Apresenta a pagina inicial
        Store::layout([
            'clients/layouts/html_header',
            'clients/layouts/header',
            'clients/home',
            'clients/layouts/footer',
            'clients/layouts/html_footer'
        ]);
    }

    // ============================================================
    public function webstore(){

        // Lista de produtos disponiveis em stock da BD
        $produtos = new Products();

        // Analisa a categoria/secção a mostrar
        $categoria = 'geral';
        if (isset($_GET['c'])) {
            $categoria = $_GET['c'];
        }

        // Vai buscar todos os produtos e a lista de categorias disponiveis na BD
        $lista_produtos = $produtos->listAvailableProducts($categoria);
        $lista_categorias = $produtos->listCategories();

        $dados = [
            'produtos' => $lista_produtos,
            'categorias' => $lista_categorias
        ];

        // Apresenta a pagina da loja
        Store::layout([
            'clients/layouts/html_header',
            'clients/layouts/header',
            'clients/webstore',
            'clients/layouts/footer',
            'clients/layouts/html_footer'
        ], $dados);
    }

    // ============================================================
    public function signup(){

        // Verifica se ja existe sessao
        if (Store::clientLogged()) {
            $this->index();
            return;
        }

        // Apresenta a pagina do signup
        Store::layout([
            'clients/layouts/html_header',
            'clients/layouts/header',
            'clients/signup',
            'clients/layouts/footer',
            'clients/layouts/html_footer'
        ]);
    }

    // ============================================================
    public function signupSubmit(){

        // Verifica se ja existe sessao
        if (Store::clientLogged()) {
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
            Store::layout([
                'clients/layouts/html_header',
                'clients/layouts/header',
                'clients/signup_success',
                'clients/layouts/footer',
                'clients/layouts/html_footer'
            ]);
            return;
        }
    }

    // ============================================================
    public function confirmEmail(){

        // Verifica se ja existe sessao
        if (Store::clientLogged()) {
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
        $validation = $cliente->validateEmail($purl);

        if ($validation) {
            // Apresenta mensagem de conta validada com sucesso
            Store::layout([
                'clients/layouts/html_header',
                'clients/layouts/header',
                'clients/validate_success',
                'clients/layouts/footer',
                'clients/layouts/html_footer'
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
        if (Store::clientLogged()) {
            Store::redirect();
            return;
        }

        // Apresenta o formulário de login
        Store::layout([
            'clients/layouts/html_header',
            'clients/layouts/header',
            'clients/login',
            'clients/layouts/footer',
            'clients/layouts/html_footer'
        ]);
    }

    // ============================================================
    public function loginSubmit(){

        // Verifica se já existe um cliente logado
        if (Store::clientLogged()) {
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
        $resultado = $cliente->validateLogin($utilizador, $senha);

        // Analisa o resultado
        if (is_bool($resultado)) {
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
            if (isset($_SESSION['tmp_cart'])) {
                // Remove a varivável temporária da sessão
                unset($_SESSION['tmp_cart']);
                // Redireciona para o resumo da encomenda
                Store::redirect('finalize_order_resume');
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

    // ============================================================
    // PERFIL DE CLIENTE / UTILIZADOR
    // ============================================================
    public function profile(){

        // Verifica se existe um utilizador logado
        if (!Store::clientLogged()) {
            Store::redirect();
            return;
        }

        // Vai buscar informações do cliente
        $cliente = new Clients();
        $dtemp = $cliente->getClientData($_SESSION['cliente']);
        $dados_cliente = [
            'E-mail'        => $dtemp->email,
            'Nome completo' => $dtemp->nome_completo,
            'Morada'        => $dtemp->morada,
            'Cidade'        => $dtemp->cidade,
            'Telefone'      => $dtemp->telefone
        ];
        $dados = [
            'dados_cliente' => $dados_cliente
        ];

        // Página de perfil de utilizador
        Store::layout([
            'clients/layouts/html_header',
            'clients/layouts/header',
            'clients/profile_nav',
            'clients/profile',
            'clients/layouts/footer',
            'clients/layouts/html_footer'
        ], $dados);
    }

    // ============================================================
    public function changePersonalData(){

        // Verifica se existe um utilizador logado
        if (!Store::clientLogged()) {
            Store::redirect();
            return;
        }

        // Vai buscar os dados pessoais
        $cliente = new Clients();
        $dados = [
            'dados_pessoais' => $cliente->getClientData($_SESSION['cliente'])
        ];

        // Página de alterar dados pessoais
        Store::layout([
            'clients/layouts/html_header',
            'clients/layouts/header',
            'clients/profile_nav',
            'clients/change_personal_data',
            'clients/layouts/footer',
            'clients/layouts/html_footer'
        ], $dados);
    }

    // ============================================================
    public function changePersonalDataSubmit(){

        // Verifica se existe um utilizador logado
        if (!Store::clientLogged()) {
            Store::redirect();
            return;
        }

        // Verifica se houve uma submissão do formulário
        if ($_SERVER['REQUEST_METHOD'] != 'POST') {
            Store::redirect();
            return;
        }

        // Validar dados
        $email = trim(strtolower($_POST['text_email']));
        $nome_completo = trim($_POST['text_nome_completo']);
        $morada = trim($_POST['text_morada']);
        $cidade = trim($_POST['text_cidade']);
        $telefone = trim($_POST['text_telefone']);

        // Validar se o email é valido
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $_SESSION['erro'] = 'Endereço de e-mail inválido';
            $this->changePersonalData();
            return;
        }

        // Validar os restantes campos
        if (empty($nome_completo) || empty($morada) || empty($cidade)) {
            $_SESSION['erro'] = 'Preencha correctamente o formulário';
            $this->changePersonalData();
            return;
        }

        // Validar se o e-mail já existe noutra conta de cliente
        $cliente = new Clients();
        $already_exists = $cliente->checkifMailExistsInOtherAccount($_SESSION['cliente'], $email);
        if ($already_exists) {
            $_SESSION['erro'] = 'Endereço de e-mail já existe noutra conta de cliente';
            $this->changePersonalData();
            return;
        }

        // Atualiza os dados do cliente na base de dados
        $cliente->updateClientDatainBD($email, $nome_completo, $morada, $cidade, $telefone);

        // Atualiza os dados do cliente na sessão
        $_SESSION['utilizador'] = $email;
        $_SESSION['nome_cliente'] = $nome_completo;

        // Redirecciona para a página do perfil do cliente
        Store::redirect('profile');
    }

    // ============================================================
    public function changePassword(){

        // Verifica se existe um utilizador logado
        if (!Store::clientLogged()) {
            Store::redirect();
            return;
        }

        // Página de alterar password
        Store::layout([
            'clients/layouts/html_header',
            'clients/layouts/header',
            'clients/profile_nav',
            'clients/change_password',
            'clients/layouts/footer',
            'clients/layouts/html_footer'
        ]);
    }

    // ============================================================
    public function changePasswordSubmit(){

        // Verifica se existe um utilizador logado
        if (!Store::clientLogged()) {
            Store::redirect();
            return;
        }

        // Verifica se houve uma submissão do formulário
        if ($_SERVER['REQUEST_METHOD'] != 'POST') {
            Store::redirect();
            return;
        }

        // Validar dados
        $senha_atual = trim($_POST['text_senha_atual']);
        $nova_senha = trim($_POST['text_nova_senha']);
        $rep_nova_senha = trim($_POST['text_rep_nova_senha']);

        // Validar se a nova password vem com dados
        if (strlen($nova_senha) < 6) {
            $_SESSION['erro'] = "A nova password e a sua repetição tem que ter no minimo 6 caracteres";
            $this->changePassword();
            return;
        }

        // Verifica se a password nova e a sua repetição coincidem
        if ($nova_senha != $rep_nova_senha) {
            $_SESSION['erro'] = "A nova password e a sua repetição não coincidem";
            $this->changePassword();
            return;
        }

        // Verifica se a password atual coincide com a que está na BD
        $cliente = new Clients();
        if (!$cliente->checkIfPasswordMatchesWithBD($_SESSION['cliente'], $senha_atual)) {
            $_SESSION['erro'] = "A password atual está errada";
            $this->changePassword();
            return;
        }

        // Verifica se a password nova é diferente da password atual
        if ($senha_atual == $nova_senha) {
            $_SESSION['erro'] = "A password nova não pode ser igual á password atual";
            $this->changePassword();
            return;
        }

        // Atualizar a nova password na BD
        $cliente->updateNewPasswordInBD($_SESSION['cliente'], $nova_senha);

        // Redirecciona para a página do perfil do cliente
        Store::redirect('profile');
    }

    // ============================================================
    public function orderHistory(){

        // Verifica se existe um utilizador logado
        if (!Store::clientLogged()) {
            Store::redirect();
            return;
        }

        // Carrega o histórico das encomendas
        $orders = new Orders();
        $order_history = $orders->getOrderHistory($_SESSION['cliente']);

        // Prepara os dados para passar para a view
        $data = [
            'order_history' => $order_history
        ];

        // Página de histórico de encomendas
        Store::layout([
            'clients/layouts/html_header',
            'clients/layouts/header',
            'clients/profile_nav',
            'clients/order_history',
            'clients/layouts/footer',
            'clients/layouts/html_footer'
        ], $data);
    }

    // ============================================================
    public function orderHistoryDetail(){

        // Verifica se existe um utilizador logado
        if (!Store::clientLogged()) {
            Store::redirect();
            return;
        }

        // Verifica se veio indicado um id encomenda (encriptado)
        if (!isset($_GET['id'])) {
            Store::redirect();
            return;
        }

        // Verifica se o id encomenda é uma string com 32 caracteres
        $id_encomenda = null;
        if (strlen($_GET['id']) != 32) {
            Store::redirect();
            return;
        } else {
            $id_encomenda = Store::aesDecrypt($_GET['id']);
            if (empty($id_encomenda)) {
                Store::redirect();
                return;
            }
        }

        // Verifica se a encomenda em questão pertence ao cliente logado
        $orders = new Orders();
        $resultado = $orders->checkClientOwnerOrder($_SESSION['cliente'], $id_encomenda);
        if (!$resultado) {
            Store::redirect();
            return;
        }

        // Vai buscar os dados de detalhe da encomenda e prepara para view
        $order_detail = $orders->orderDetails($_SESSION['cliente'], $id_encomenda);

        // Cálculo do valor total da encomenda
        $total = 0;
        foreach ($order_detail['produtos_encomenda'] as $produto) {
            $total += ($produto->quantidade * $produto->preco_unidade);
        }

        $data = [
            'dados_encomenda' => $order_detail['dados_encomenda'],
            'produtos_encomenda' => $order_detail['produtos_encomenda'],
            'total_encomenda' => $total
        ];

        // Página de detalhe dos históricos das encomendas
        Store::layout([
            'clients/layouts/html_header',
            'clients/layouts/header',
            'clients/profile_nav',
            'clients/order_detail',
            'clients/layouts/footer',
            'clients/layouts/html_footer'
        ], $data);
    }

    // ============================================================
    // SIMULAÇÃO DO WEBHOOK DO GATEWAY DE PAGAMENTO
    // ============================================================
    public function pagamento(){

        // Verifica se veio o código da encomenda indicado
        $codigo_encomenda = '';
        if (!isset($_GET['cod'])) {
            return;
        } else {
            $codigo_encomenda = $_GET['cod'];
        }

        // Verifica se existe o código com o estado pendente
        $orders = new Orders();
        $resultado = $orders->payingOrder($codigo_encomenda);

        echo (int)$resultado;
    }
}
