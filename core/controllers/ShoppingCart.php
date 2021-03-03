<?php

namespace core\controllers;

use core\classes\Database;
use core\classes\SendEmail;
use core\classes\Store;
use core\models\Clients;
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
        $resultados = $produtos->verifica_stock_produto($id_produto);
        if (!$resultados) {
            echo (isset($_SESSION['shoppingcart'])) ? count($_SESSION['shoppingcart']) : '';
            return;
        }

        // Adiciona / Gestão da variável de sessão do carrinho
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
    public function clear_shoppingcart(){

        // Limpa todos os produtos do carrinho
        unset($_SESSION['shoppingcart']);

        // Refresca a página do carrinho após o mesmo ser limpo
        $this->shopping_cart();
    }

    // ============================================================
    public function shopping_cart(){

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
            $resultados = $produtos->get_products_by_ids($ids);

            $dados_encomenda = [];
            foreach ($_SESSION['shoppingcart'] as $id_produto => $quantidade) {

                // Ciclo de produtos
                foreach ($resultados as $produto) {
                    if($produto->id_produto == $id_produto){
                        $imagem = $produto->imagem;
                        $titulo = $produto->nome_produto;
                        $qtd = $quantidade;
                        $preco = $produto->preco * $qtd;

                        // Coloca os atributos do produto no array da encomenda
                        array_push($dados_encomenda, [
                            'imagem' => $imagem,
                            'titulo' => $titulo,
                            'qtd'    => $qtd,
                            'preco'  => $preco
                        ]);
                        break;
                    }
                }
            }

            // Calcula o sub-total da encomenda e coloca-o na coleção da mesma
            $valor_total_encomenda = 0;
            foreach($dados_encomenda as $item){
                $valor_total_encomenda += $item['preco'];
            }
            array_push($dados_encomenda, $valor_total_encomenda);

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
}