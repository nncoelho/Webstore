<?php

namespace core\controllers;

use core\classes\SendEmail;
use core\classes\Store;
use core\models\Admins;

class Admin{

    // ============================================================
    // Administrador: admin@admin.com
    // Senha: admin123
    // ============================================================
    public function index(){

        // Verifica se ja existe sessao do admin aberta
        if (!Store::adminLogged()) {
            Store::redirect('admin_login', true);
            return;
        }

        // Verifica o estado das encomendas
        $admin = new Admins();
        $total_pending_orders = $admin->totalPendingOrders();
        $total_processing_orders = $admin->totalProcessingOrders();

        $dados = [
            'total_pending_orders' => $total_pending_orders,
            'total_processing_orders' => $total_processing_orders
        ];

        // Apresenta a pagina home do admin
        Store::layoutAdmin([
            'admin/layouts/html_header',
            'admin/layouts/header',
            'admin/home',
            'admin/layouts/footer',
            'admin/layouts/html_footer'
        ], $dados);
    }

    // ============================================================
    public function adminLogin(){

        // Verifica se ja existe sessao do admin aberta
        if (Store::adminLogged()) {
            Store::redirect('home', true);
            return;
        }

        // Apresenta a pagina de login do admin
        Store::layoutAdmin([
            'admin/layouts/html_header',
            'admin/layouts/header',
            'admin/login',
            'admin/layouts/footer',
            'admin/layouts/html_footer'
        ]);
    }

    // ============================================================
    public function adminLoginSubmit(){

        // Verifica se existe um admin logado
        if (Store::adminLogged()) {
            Store::redirect('home', true);
            return;
        }

        // Verifica se foi efectuado o POST do formulário de login do admin
        if ($_SERVER['REQUEST_METHOD'] != 'POST') {
            Store::redirect('home', true);
            return;
        }

        // Verifica se os campos do login do admin vieram correctamente preenchidos
        if (
            !isset($_POST['text_admin']) ||
            !isset($_POST['text_senha']) ||
            !filter_var(trim($_POST['text_admin']), FILTER_VALIDATE_EMAIL)
        ) {
            // Erro de preenchimento do formulário
            $_SESSION['erro'] = 'Login inválido';
            Store::redirect('admin_login', true);
            return;
        }

        // Prepara os dados para o model do admin
        $admin = trim(strtolower($_POST['text_admin']));
        $senha = trim($_POST['text_senha']);

        // Carrega o model e verifica se o login do admin é válido
        $administrador = new Admins();
        $resultado = $administrador->validateLogin($admin, $senha);

        // Analisa o resultado
        if (is_bool($resultado)) {
            // Login inválido
            $_SESSION['erro'] = 'Login de administrador inválido';
            Store::redirect('login', true);
            return;
        } else {
            // Login válido - Coloca os dados na sessão do administrador
            $_SESSION['admin'] = $resultado->id_admin;
            $_SESSION['administrador'] = $resultado->administrador;

            // Redireciona para a pagina inicial do backoffice
            Store::redirect('home', true);
        }
    }

    // ============================================================
    public function adminLogout(){

        // Remove as variáveis da sessão
        unset($_SESSION['admin']);
        unset($_SESSION['administrador']);

        // Redirecciona para a pagin de login do admin
        Store::redirect('admin_login', true);
    }

    // ============================================================
    public function clientList(){

        // Verifica se existe um admin logado
        if (!Store::adminLogged()) {
            Store::redirect('home', true);
            return;
        }

        // Vai buscar a lista de clientes
        $administrador = new Admins();
        $clientes = $administrador->listingClients();

        $dados = [
            'clientes' => $clientes
        ];

        // Apresenta a pagina da lista dos clientes
        Store::layoutAdmin([
            'admin/layouts/html_header',
            'admin/layouts/header',
            'admin/client_list',
            'admin/layouts/footer',
            'admin/layouts/html_footer'
        ], $dados);
    }

    // ============================================================
    public function clientDetail(){

        // Verifica se existe um admin logado
        if (!Store::adminLogged()) {
            Store::redirect('home', true);
            return;
        }

        // Verifica se existe um id_cliente na query string
        if(!isset($_GET['c'])){
            Store::redirect('home', true);
            return;
        }

        $id_cliente = Store::aesDecrypt($_GET['c']);
        // Verifica se o id cliente é válido
        if(empty($id_cliente)){
            Store::redirect('home', true);
            return;
        }

        echo $id_cliente;
    }

    // ============================================================
    public function orderList(){

        // LISTA DE ENCOMENDAS (COM FILTRO CASO NECESSARIO)

        // Verfica se existe um filtro na query string
        $filtros = [
            'pendente'          => 'PENDENTE',
            'em_processamento'  => 'EM PROCESSAMENTO',
            'cancelada'         => 'CANCELADA',
            'enviada'           => 'ENVIADA',
            'concluida'         => 'CONCLUIDA'
        ];

        $filtro = '';
        if(isset($_GET['f'])){

            // Verifica se a variavel é uma key restrita dos filtros
            if(key_exists($_GET['f'], $filtros)){   
                $filtro = $filtros[$_GET['f']];
            }
        }

        // Carregar a lista de encomendas
        $admins = new Admins();
        $listing_orders = $admins->listingOrders($filtro);

        $dados = [
            'listing_orders'    => $listing_orders,
            'filtro'            => $filtro
        ];
        
        // Apresenta a pagina da lista das encomendas
        Store::layoutAdmin([
            'admin/layouts/html_header',
            'admin/layouts/header',
            'admin/order_list',
            'admin/layouts/footer',
            'admin/layouts/html_footer'
        ], $dados);
    }
}
