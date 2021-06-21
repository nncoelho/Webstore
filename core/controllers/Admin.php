<?php

namespace core\controllers;

use core\classes\PDF;
use core\classes\SendEmail;
use core\classes\Store;
use core\models\Admins;

class Admin{

    // ============================================================
    // ADMINISTRADOR: admin@admin.com
    // PASSWORD: admin123
    // ============================================================
    public function index(){

        // Verifica se existe um admin logado
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
    // AUTENTICAÇÃO
    // ============================================================
    public function adminLogin(){

        // Verifica se existe um admin logado
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
    // CLIENTES
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
        if (!isset($_GET['c'])) {
            Store::redirect('home', true);
            return;
        }

        $id_cliente = Store::aesDecrypt($_GET['c']);
        // Verifica se o id cliente é válido
        if (empty($id_cliente)) {
            Store::redirect('home', true);
            return;
        }

        // Vai buscar os dados do cliente
        $admin = new Admins();
        $dados = [
            'dados_cliente' => $admin->getClient($id_cliente),
            'total_encomendas' => $admin->clientTotalOrders($id_cliente)
        ];

        // Apresenta a pagina de detalhes do cliente
        Store::layoutAdmin([
            'admin/layouts/html_header',
            'admin/layouts/header',
            'admin/client_detail',
            'admin/layouts/footer',
            'admin/layouts/html_footer'
        ], $dados);
    }

    // ============================================================
    public function clientOrderHistory(){

        // Verifica se existe um admin logado
        if (!Store::adminLogged()) {
            Store::redirect('home', true);
            return;
        }

        // Verifica se existe o id_cliente encriptado
        if (!isset($_GET['c'])) {
            Store::redirect('home', true);
        }

        // Define o id_cliente que vem encriptado
        $id_cliente = Store::aesDecrypt($_GET['c']);

        // Verifica se o id cliente é válido
        if (empty($id_cliente)) {
            Store::redirect('home', true);
            return;
        }

        $administrador = new Admins();
        $dados = [
            'cliente'        => $administrador->getClient($id_cliente),
            'listing_orders' => $administrador->getClientOrders($id_cliente)
        ];

        // Apresenta a pagina do histórico das encomendas do cliente
        Store::layoutAdmin([
            'admin/layouts/html_header',
            'admin/layouts/header',
            'admin/client_order_history',
            'admin/layouts/footer',
            'admin/layouts/html_footer'
        ], $dados);
    }

    // ============================================================
    // ENCOMENDAS
    // ============================================================
    public function orderList(){

        // Verifica se existe um admin logado
        if (!Store::adminLogged()) {
            Store::redirect('home', true);
            return;
        }

        // Verfica se existe um filtro na query string
        $filtros = [
            'pendente'          => 'PENDENTE',
            'em_processamento'  => 'EM PROCESSAMENTO',
            'cancelada'         => 'CANCELADA',
            'enviada'           => 'ENVIADA',
            'concluida'         => 'CONCLUIDA'
        ];

        $filtro = '';
        if (isset($_GET['f'])) {

            // Verifica se a variavel é uma key restrita dos filtros
            if (key_exists($_GET['f'], $filtros)) {
                $filtro = $filtros[$_GET['f']];
            }
        }

        // Vai buscar o id cliente se existir na query string
        $id_cliente = null;
        if (isset($_GET['c'])) {
            $id_cliente = Store::aesDecrypt($_GET['c']);
        }

        // Carrega a lista de encomendas (com filtro caso necessário)
        $admins = new Admins();
        $listing_orders = $admins->listingOrders($filtro, $id_cliente);

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

    // ============================================================
    public function orderDetails(){

        // Verifica se existe um admin logado
        if (!Store::adminLogged()) {
            Store::redirect('home', true);
            return;
        }

        // Vai buscar o id_encomenda
        $id_encomenda = null;
        if (isset($_GET['e'])) {
            $id_encomenda = Store::aesDecrypt($_GET['e']);
        }
        if (gettype($id_encomenda) != 'string') {
            Store::redirect('home', true);
            return;
        }

        // Carregar os dados da encomenda selecionada
        $admin = new Admins();
        $encomenda = $admin->getOrderDetails($id_encomenda);

        // Apresenta a página de detalhes de formar a poder alterar o seu status
        $dados = $encomenda;
        Store::layoutAdmin([
            'admin/layouts/html_header',
            'admin/layouts/header',
            'admin/order_details',
            'admin/layouts/footer',
            'admin/layouts/html_footer'
        ], $dados);
    }

    // ============================================================
    public function changeOrderStatus(){

        // Verifica se existe um admin logado
        if (!Store::adminLogged()) {
            Store::redirect('home', true);
            return;
        }

        // Vai buscar o id_encomenda
        $id_encomenda = null;
        if (isset($_GET['e'])) {
            $id_encomenda = Store::aesDecrypt($_GET['e']);
        }
        if (gettype($id_encomenda) != 'string') {
            Store::redirect('home', true);
            return;
        }

        // Vai buscar o novo estado
        $estado = null;
        if (isset($_GET['s'])) {
            $estado = $_GET['s'];
        }
        if (!in_array($estado, STATUS)) {
            Store::redirect('home', true);
            return;
        }

        // ============================================================
        // REGRAS DE NEGÓCIO PARA GESTÃO DA ENCOMENDA (NOVO ESTADO)
        // ============================================================

        // Atualizar o estado da encomenda na base de dados
        $admin = new Admins();
        $admin->updateOrderStatus($id_encomenda, $estado);

        // Executar operações baseadas no novo estado da encomenda
        switch ($estado) {
            case 'PENDENTE':
                // Não existem ações
                break;

            case 'EM PROCESSAMENTO':
                // Não existem ações
                break;

            case 'ENVIADA':
                // Enviar um email ao cliente sobre o envio da encomenda
                $this->enviar_email_encomenda_envidada($id_encomenda);
                break;

            case 'CANCELADA':
                $this->enviar_email_encomenda_cancelada($id_encomenda);
                break;

            case 'CONCLUIDA':
                // Não existe ações
                break;
        }

        // Redirecciona para a página da própria encomenda
        Store::redirect('order_details&e=' . $_GET['e'], true);
    }

    // ============================================================
    // OPERAÇÕES APÓS MUDANÇA DE ESTADO
    // ============================================================
    private function enviar_email_encomenda_envidada($id_encomenda){

        // Executa as operações para enviar o email ao cliente
        $email = new SendEmail();
    }

    private function enviar_email_encomenda_cancelada($id_encomenda){
    }

    // ============================================================
    public function createPDFOrder(){

        // Verifica se existe um admin logado
        if (!Store::adminLogged()) {
            Store::redirect('home', true);
            return;
        }

        // Vai buscar o id_encomenda
        $id_encomenda = null;
        if (isset($_GET['e'])) {
            $id_encomenda = Store::aesDecrypt($_GET['e']);
        }
        if (gettype($id_encomenda) != 'string') {
            Store::redirect('home', true);
            return;
        }

        // Vai buscar os detalhes da encomenda
        $admin = new Admins();
        $encomenda = $admin->getOrderDetails($id_encomenda);

        // Cria o PDF com os detalhes da encomenda
        $pdf = new PDF(true);
        // $pdf->setTemplate(getcwd() . '/assets/templates/template.pdf' );

        // Preparar opções base do PDF
        $pdf->set_letra_familia('Arial');
        $pdf->set_letra_tamanho('12px');
        $pdf->set_letra_tipo('bold');

        // Data da encomenda
        $pdf->posicao_dimensao(225, 204, 135, 22);
        $pdf->writeHTML($encomenda['encomenda']->data_encomenda);

        // Código da encomenda
        $pdf->posicao_dimensao(550, 204, 125, 22);
        $pdf->writeHTML($encomenda['encomenda']->codigo_encomenda);

        // Dados do cliente
        $pdf->posicao_dimensao(75, 260, 600, 22);
        $pdf->writeHTML($encomenda['encomenda']->nome_completo);

        // Morada - Cidade
        $pdf->posicao_dimensao(75, 285, 600, 22);
        $pdf->writeHTML($encomenda['encomenda']->morada . ' - ' . $encomenda['encomenda']->cidade);

        // Email - Telefone
        $pdf->posicao_dimensao(75, 310, 600, 22);
        $telefone = empty($encomenda['encomenda']->telefone) ? '' : ' - ' . $encomenda['encomenda']->telefone;
        $pdf->writeHTML($encomenda['encomenda']->email . $telefone);

        // Lista dos produtos encomendados
        $y = 400;
        $total_encomenda = 0;
        $pdf->set_letra_tipo('regular');

        foreach ($encomenda['lista_produtos'] as $produto) {
            // Localização da apresentacão da quantidade x produto
            $pdf->set_alinhamento('left');
            $pdf->posicao_dimensao(75, $y, 475, 22);
            $pdf->writeHTML($produto->quantidade . ' x ' . $produto->designacao_produto);

            // Preco do produto
            $pdf->set_alinhamento('right');
            $pdf->posicao_dimensao(555, $y, 120, 22);
            $preco = $produto->quantidade * $produto->preco_unidade;
            $total_encomenda += $preco;
            $pdf->writeHTML('€' . number_format($preco, 2, ',', '.'));

            $y += 25;
        }

        // Apresenta o preco do total da compra
        $pdf->set_alinhamento('right');
        $pdf->posicao_dimensao(460, 900, 205, 27);
        $pdf->set_letra_tamanho('15px');
        $pdf->set_letra_tipo('bold');
        $pdf->writeHTML('Total: ' . '€' . number_format($total_encomenda, 2, ',', '.'));

        // Apresenta o PDF criado
        $pdf->outputPDF();
    }

    // ============================================================
    public function sendPDFOrder(){

        // Verifica se existe um admin logado
        if (!Store::adminLogged()) {
            Store::redirect('home', true);
            return;
        }

        // Vai buscar o id_encomenda
        $id_encomenda = null;
        if (isset($_GET['e'])) {
            $id_encomenda = Store::aesDecrypt($_GET['e']);
        }
        if (gettype($id_encomenda) != 'string') {
            Store::redirect('home', true);
            return;
        }

        // Vai buscar os detalhes da encomenda
        $admin = new Admins();
        $encomenda = $admin->getOrderDetails($id_encomenda);

        // Cria o PDF com os detalhes da encomenda
        $pdf = new PDF(true);
        // $pdf->setTemplate(getcwd() . '/assets/templates/template.pdf' );

        // Preparar opções base do PDF
        $pdf->set_letra_familia('Arial');
        $pdf->set_letra_tamanho('12px');
        $pdf->set_letra_tipo('bold');

        // Data da encomenda
        $pdf->posicao_dimensao(225, 204, 135, 22);
        $pdf->writeHTML($encomenda['encomenda']->data_encomenda);

        // Código da encomenda
        $pdf->posicao_dimensao(550, 204, 125, 22);
        $pdf->writeHTML($encomenda['encomenda']->codigo_encomenda);

        // Dados do cliente
        $pdf->posicao_dimensao(75, 260, 600, 22);
        $pdf->writeHTML($encomenda['encomenda']->nome_completo);

        // Morada - Cidade
        $pdf->posicao_dimensao(75, 285, 600, 22);
        $pdf->writeHTML($encomenda['encomenda']->morada . ' - ' . $encomenda['encomenda']->cidade);

        // Email - Telefone
        $pdf->posicao_dimensao(75, 310, 600, 22);
        $telefone = empty($encomenda['encomenda']->telefone) ? '' : ' - ' . $encomenda['encomenda']->telefone;
        $pdf->writeHTML($encomenda['encomenda']->email . $telefone);

        // Lista dos produtos encomendados
        $y = 400;
        $total_encomenda = 0;
        $pdf->set_letra_tipo('regular');

        foreach ($encomenda['lista_produtos'] as $produto) {
            // Localização da apresentacão da quantidade x produto
            $pdf->set_alinhamento('left');
            $pdf->posicao_dimensao(75, $y, 475, 22);
            $pdf->writeHTML($produto->quantidade . ' x ' . $produto->designacao_produto);

            // Preco do produto
            $pdf->set_alinhamento('right');
            $pdf->posicao_dimensao(555, $y, 120, 22);
            $preco = $produto->quantidade * $produto->preco_unidade;
            $total_encomenda += $preco;
            $pdf->writeHTML('€' . number_format($preco, 2, ',', '.'));

            $y += 25;
        }

        // Apresenta o preco do total da compra
        $pdf->set_alinhamento('right');
        $pdf->posicao_dimensao(460, 900, 205, 27);
        $pdf->set_letra_tamanho('15px');
        $pdf->set_letra_tipo('bold');
        $pdf->writeHTML('Total: ' . '€' . number_format($total_encomenda, 2, ',', '.'));

        // Define permissoes e protecção
        $permissoes = [
            // 'copy',
            'print',
            // 'modify',
            // 'annot-forms',
            // 'fill-forms',
            // 'extract',
            // 'assemble',
            // 'print-highres'
        ];
        $pdf->set_permissoes([], 'pdf123');

        // Guarda o PDF criado
        $ficheiro = $encomenda['encomenda']->codigo_encomenda . '_' . date('YmdHis') . '.pdf';
        $pdf->savePDF($ficheiro);

        // Enviar o email com o ficheiro em anexo
        $email = new SendEmail();
        $email->sendPDFOrderToClient($encomenda['encomenda']->email, $ficheiro);

        // Elimina o ficheiro PDF da pasta temp
        unlink(PDF_PATH . $ficheiro);

        // Recarrega a pagina da encomenda
        Store::redirect('order_details&e='.$_GET['e'], true);
    }
}
