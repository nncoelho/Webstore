<?php

namespace core\controllers;

use core\classes\SendEmail;
use core\classes\Store;
use core\models\Clients;
use core\models\Orders;
use core\models\Products;

class ShoppingCart{

    // ============================================================
    public function addToShoppingCart(){

        // Vai buscar o id produto escolhido á query string
        if (!isset($_GET['id_produto'])) {
            // Condição ternária para contagem dos produtos
            echo (isset($_SESSION['shoppingcart'])) ? count($_SESSION['shoppingcart']) : '';
            return;
        }

        // Define o id do produto
        $id_produto = $_GET['id_produto'];
        $produtos = new Products();
        $resultados = $produtos->checkProductStock($id_produto);
        if (!$resultados) {
            echo (isset($_SESSION['shoppingcart'])) ? count($_SESSION['shoppingcart']) : '';
            return;
        }

        // Adiciona/Gestão da variável de sessão do carrinho
        $shoppingcart = [];
        if (isset($_SESSION['shoppingcart'])) {
            $shoppingcart = $_SESSION['shoppingcart'];
        }

        // Adiciona o produto ao carrinho
        if (key_exists($id_produto, $shoppingcart)) {

            // Já existe o produto no carrinho acrescenta uma unidade
            $shoppingcart[$id_produto]++;
        } else {
            // Adiciona novo produto ao carrinho
            $shoppingcart[$id_produto] = 1;
        }

        // Atualiza os dados do carrinho na sessão
        $_SESSION['shoppingcart'] = $shoppingcart;

        // Resposta (Número de produto no carrinho)
        $total_produtos = 0;
        foreach ($shoppingcart as $produto_qtd) {
            $total_produtos += $produto_qtd;
        }
        echo $total_produtos;
    }

    // ============================================================
    public function deleteItemShoppingCart(){

        // Vai buscar o id produto na query string
        $id_produto = $_GET['id_produto'];

        // Vai buscar o carrinho à sessão
        $shoppingcart = $_SESSION['shoppingcart'];

        // Remove o produto do carrinho
        unset($shoppingcart[$id_produto]);

        // Atualiza o carrinho na sessão
        $_SESSION['shoppingcart'] = $shoppingcart;

        // Refresca a página do carrinho
        $this->shoppingCart();
    }

    // ============================================================
    public function clearShoppingCart(){

        // Limpa todos os produtos do carrinho
        unset($_SESSION['shoppingcart']);

        // Refresca a página do carrinho após o mesmo ser limpo
        $this->shoppingCart();
    }

    // ============================================================
    public function shoppingCart(){

        // Verifica se existe carrinho
        if (!isset($_SESSION['shoppingcart']) || count($_SESSION['shoppingcart']) == 0) {
            $dados = [
                'shoppingcart' => null
            ];
        } else {
            $ids = [];
            foreach ($_SESSION['shoppingcart'] as $id_produto => $quantidade) {
                array_push($ids, $id_produto);
            }

            $ids = implode(",", $ids);
            $produtos = new Products();
            $resultados = $produtos->getProductsByIds($ids);

            $dados_encomenda = [];
            foreach ($_SESSION['shoppingcart'] as $id_produto => $quantidade) {

                // Ciclo de produtos
                foreach ($resultados as $produto) {
                    if ($produto->id_produto == $id_produto) {
                        $id_produto = $produto->id_produto;
                        $imagem = $produto->imagem;
                        $titulo = $produto->nome_produto;
                        $qtd = $quantidade;
                        $preco = $produto->preco * $qtd;

                        // Coloca os atributos do produto no array da encomenda
                        array_push($dados_encomenda, [
                            'id_produto' => $id_produto,
                            'imagem' => $imagem,
                            'titulo' => $titulo,
                            'qtd'    => $qtd,
                            'preco'  => $preco
                        ]);
                        break;
                    }
                }
            }

            // Calcula o total da encomenda e coloca-o na coleção da mesma
            $valor_total_encomenda = 0;
            foreach ($dados_encomenda as $item) {
                $valor_total_encomenda += $item['preco'];
            }
            array_push($dados_encomenda, $valor_total_encomenda);

            // Coloca o preço total na sessão para finalizar encomenda
            $_SESSION['total_encomenda'] = $valor_total_encomenda;

            $dados = [
                'shoppingcart' => $dados_encomenda
            ];
        }

        // Apresenta a pagina do carrinho
        Store::Layout([
            'layouts/html_header',
            'layouts/header',
            'shopping_cart',
            'layouts/footer',
            'layouts/html_footer'
        ], $dados);
    }

    // ============================================================
    public function alternativeAddress(){

        // Receber os dados via AJAX(axios)
        $post = json_decode(file_get_contents('php://input'), true);

        // Adiciona na sessão a variável/array dados_alternativos
        $_SESSION['dados_alternativos'] = [
            'morada' => $post['text_morada'],
            'cidade' => $post['text_cidade'],
            'email' => $post['text_email'],
            'telefone' => $post['text_telefone']
        ];
    }

    // ============================================================
    public function finalizeOrder(){

        // Verifica se existe cliente logado
        if (!isset($_SESSION['cliente'])) {
            // Coloca na sessão um referrer temporário
            $_SESSION['tmp_cart'] = true;
            // Redireciona para o quadro de login
            Store::redirect('login');
        } else {
            Store::redirect('finalize_order_resume');
        }
    }

    // ============================================================
    public function finalizeOrderResume(){

        // Verifica se existe cliente logado
        if (!isset($_SESSION['cliente'])) {
            Store::redirect('home');
        }

        // Verifica se pode avançar para o registo da encomenda na BD
        if (!isset($_SESSION['shoppingcart']) || count($_SESSION['shoppingcart']) == 0) {
            Store::redirect('home');
            return;
        }

        // Informações do carrinho
        $ids = [];
        foreach ($_SESSION['shoppingcart'] as $id_produto => $quantidade) {
            array_push($ids, $id_produto);
        }
        $ids = implode(",", $ids);
        $produtos = new Products();
        $resultados = $produtos->getProductsByIds($ids);

        $dados_encomenda = [];
        foreach ($_SESSION['shoppingcart'] as $id_produto => $quantidade) {

            // Ciclo de produtos
            foreach ($resultados as $produto) {
                if ($produto->id_produto == $id_produto) {
                    $id_produto = $produto->id_produto;
                    $imagem = $produto->imagem;
                    $titulo = $produto->nome_produto;
                    $qtd = $quantidade;
                    $preco = $produto->preco * $qtd;

                    // Coloca os atributos do produto no array da encomenda
                    array_push($dados_encomenda, [
                        'id_produto' => $id_produto,
                        'imagem' => $imagem,
                        'titulo' => $titulo,
                        'qtd'    => $qtd,
                        'preco'  => $preco
                    ]);
                    break;
                }
            }
        }

        // Calcula o total da encomenda e coloca-o na coleção da mesma
        $valor_total_encomenda = 0;
        foreach ($dados_encomenda as $item) {
            $valor_total_encomenda += $item['preco'];
        }
        array_push($dados_encomenda, $valor_total_encomenda);

        // Prepara os dados da encomenda para a view
        $dados = [];
        $dados['shoppingcart'] = $dados_encomenda;

        // Vai buscar as informações do cliente
        $cliente = new Clients();
        $dados_cliente = $cliente->getClientData($_SESSION['cliente']);
        $dados['cliente'] = $dados_cliente;

        // Gera o código único da encomenda
        if (!isset($_SESSION['order_code'])) {
            $order_code = Store::generateOrderCode();
            $_SESSION['order_code'] = $order_code;
        }

        // Apresenta a pagina de resumo da encomenda
        Store::Layout([
            'layouts/html_header',
            'layouts/header',
            'order_resume',
            'layouts/footer',
            'layouts/html_footer'
        ], $dados);
    }

    // ============================================================
    public function confirmOrder(){

        // Verifica se existe cliente logado
        if (!isset($_SESSION['cliente'])) {
            Store::redirect('home');
        }

        // Verifica se pode avançar para o registo da encomenda na BD
        if (!isset($_SESSION['shoppingcart']) || count($_SESSION['shoppingcart']) == 0) {
            Store::redirect('home');
            return;
        }

        // Enviar email ao cliente tratar os dados e guardar encomenda na BD
        $dados_encomenda = [];

        // Vai buscar os dados dos produtos
        $ids = [];
        foreach ($_SESSION['shoppingcart'] as $id_produto => $quantidade) {
            array_push($ids, $id_produto);
        }
        $ids = implode(",", $ids);
        $produtos = new Products();
        $produtos_encomenda = $produtos->getProductsByIds($ids);

        // Estrutura dos dados dos produtos
        $string_produtos = [];
        foreach ($produtos_encomenda as $resultado) {
            // Quantidade
            $quantidade = $_SESSION['shoppingcart'][$resultado->id_produto];
            // String do produto
            $string_produtos[] = "$quantidade x $resultado->nome_produto - €" . number_format($resultado->preco, 2, ',', '.') . "/Unid.";
        }

        // Lista de produtos para o email
        $dados_encomenda['lista_produtos'] = $string_produtos;

        // Preço total da encomenda para o email
        $dados_encomenda['total'] = '€' . number_format($_SESSION['total_encomenda'], 2, ',', '.');

        // Dados de pagamento para o email
        $dados_encomenda['dados_pagamento'] = [
            'numero_da_conta'   => '1234567890',
            'order_code'        => $_SESSION['order_code'],
            'total'             => '€' . number_format($_SESSION['total_encomenda'], 2, ',', '.')
        ];

        // Envia o email ao cliente com os dados da encomenda
        $email = new SendEmail();
        $resultado = $email->sendEmailCheckingOrder($_SESSION['utilizador'], $dados_encomenda);

        // Prepara os dados do cliente respectivo a encomenda
        $dados_encomenda = [];
        $dados_encomenda['id_cliente'] = $_SESSION['cliente'];
        // Morada
        if (isset($_SESSION['dados_alternativos']['morada']) && !empty($_SESSION['dados_alternativos']['morada'])) {
            // Considera a morada alternativa
            $dados_encomenda['morada'] = $_SESSION['dados_alternativos']['morada'];
            $dados_encomenda['cidade'] = $_SESSION['dados_alternativos']['cidade'];
            $dados_encomenda['email'] = $_SESSION['dados_alternativos']['email'];
            $dados_encomenda['telefone'] = $_SESSION['dados_alternativos']['telefone'];
        } else {
            // Considera a morada guardada na BD
            $cliente = new Clients();
            $dados_cliente = $cliente->getClientData($_SESSION['cliente']);
            $dados_encomenda['morada'] = $dados_cliente->morada;
            $dados_encomenda['cidade'] = $dados_cliente->cidade;
            $dados_encomenda['email'] = $dados_cliente->email;
            $dados_encomenda['telefone'] = $dados_cliente->telefone;
        }

        // Código da encomenda
        $dados_encomenda['order_code'] = $_SESSION['order_code'];

        // Status
        $dados_encomenda['status'] = 'PENDENTE';
        $dados_encomenda['mensagem'] = '';

        // Prepara os dados dos produtos da encomenda
        $dados_produtos = [];
        foreach ($produtos_encomenda as $produto) {
            $dados_produtos[] = [
                'designacao_produto' => $produto->nome_produto,
                'preco_unidade'      => $produto->preco,
                'quantidade'         => $_SESSION['shoppingcart'][$produto->id_produto]
            ];
        }

        // Guarda a encomenda na BD
        $order = new Orders();
        $order->saveOrderBD($dados_encomenda, $dados_produtos);

        // Prepara os dados a apresentar na pagina de agradecimento
        $order_code = $_SESSION['order_code'];
        $total_encomenda = $_SESSION['total_encomenda'];

        // Limpa todos os dados da encomenda no carrinho
        unset($_SESSION['order_code']);
        unset($_SESSION['shoppingcart']);
        unset($_SESSION['total_encomenda']);
        unset($_SESSION['dados_alternativos']);

        // Apresenta a pagina de confirmação da encomenda
        $dados = [
            'order_code' => $order_code,
            'total_encomenda' => $total_encomenda
        ];

        Store::Layout([
            'layouts/html_header',
            'layouts/header',
            'confirm_order',
            'layouts/footer',
            'layouts/html_footer'
        ], $dados);
    }
}
