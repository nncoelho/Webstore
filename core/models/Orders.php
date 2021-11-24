<?php

namespace core\models;

use core\classes\Database;

class Orders
{

    // ============================================================
    public function saveOrderBD($dados_encomenda, $dados_produtos)
    {

        $bd = new Database();

        // Guarda os dados da encomenda na BD
        $parametros = [
            ':id_cliente' => $_SESSION['cliente'],
            ':morada' => $dados_encomenda['morada'],
            ':cidade' => $dados_encomenda['cidade'],
            ':email' => $dados_encomenda['email'],
            ':telefone' => $dados_encomenda['telefone'],
            ':order_code' => $dados_encomenda['order_code'],
            ':status' => $dados_encomenda['status'],
            ':mensagem' => $dados_encomenda['mensagem']
        ];

        $bd->insert(
            "INSERT INTO encomendas VALUES(
                0,
                :id_cliente,
                NOW(),
                :morada,
                :cidade,
                :email,
                :telefone,
                :order_code,
                :status,
                :mensagem,
                NOW(),
                NOW())",
            $parametros
        );

        // Vai buscar o id encomenda
        $id_encomenda = $bd->select("SELECT MAX(id_encomenda) AS id_encomenda FROM encomendas")[0]->id_encomenda;

        // Guarda os dados dos produtos da encomenda na BD
        foreach ($dados_produtos as $produto) {
            $parametros = [
                ':id_encomenda' => $id_encomenda,
                ':designacao_produto' => $produto['designacao_produto'],
                ':preco_unidade' => $produto['preco_unidade'],
                ':quantidade' => $produto['quantidade']
            ];

            $bd->insert(
                "INSERT INTO encomenda_produto VALUES(
                    0,
                    :id_encomenda,
                    :designacao_produto,
                    :preco_unidade,
                    :quantidade,
                    NOW())",
                $parametros
            );
        }
    }

    // ============================================================
    public function getOrderHistory($id_cliente)
    {

        // Vai buscar o historico das encomendas por cliente
        $parametros = [
            ':id_cliente' => $id_cliente
        ];

        $bd = new Database();
        $resultados = $bd->select(
            "SELECT id_encomenda, data_encomenda, codigo_encomenda, status
                FROM encomendas
                WHERE id_cliente = :id_cliente
                ORDER BY data_encomenda DESC",
            $parametros
        );
        return $resultados;
    }

    // ============================================================
    public function checkClientOwnerOrder($id_cliente, $id_encomenda)
    {

        // Verifica se a encomenda tem relação com o cliente logado
        $parametros = [
            ':id_cliente' => $id_cliente,
            ':id_encomenda' => $id_encomenda
        ];

        $bd = new Database();
        $resultado = $bd->select(
            "SELECT id_encomenda
                FROM encomendas
                WHERE id_encomenda = :id_encomenda
                AND id_cliente = :id_cliente",
            $parametros
        );

        return count($resultado) == 0 ? false : true;
    }

    // ============================================================
    public function orderDetails($id_cliente, $id_encomenda)
    {

        // Vai buscar os dados da encomenda e a lista dos produtos
        $parametros = [
            ':id_cliente' => $id_cliente,
            ':id_encomenda' => $id_encomenda
        ];

        // Dados dos detalhes da encomenda
        $bd = new Database();
        $dados_encomenda = $bd->select(
            "SELECT *
                FROM encomendas
                WHERE id_cliente = :id_cliente
                AND id_encomenda = :id_encomenda",
            $parametros
        )[0];

        // Dados da lista de produtos da encomenda
        $parametros = [
            ':id_encomenda' => $id_encomenda
        ];

        $produtos_encomenda = $bd->select(
            "SELECT *
                FROM encomenda_produto
                WHERE id_encomenda = :id_encomenda",
            $parametros
        );

        // Devolve ao controlador os dados do detalhe da encomenda
        return [
            'dados_encomenda' => $dados_encomenda,
            'produtos_encomenda' => $produtos_encomenda
        ];
    }

    // ============================================================
    public function payingOrder($codigo_encomenda)
    {

        $parametros = [
            ':codigo_encomenda' => $codigo_encomenda
        ];

        $bd = new Database();
        $resultado = $bd->select(
            "SELECT *
                FROM encomendas
                WHERE codigo_encomenda = :codigo_encomenda
                AND status = 'PENDENTE'",
            $parametros
        );

        if (count($resultado) == 0) {
            return false;
        }

        // Efectua a alteração do estado da encomenda indicada
        $bd->update(
            "UPDATE encomendas
                SET status = 'EM PROCESSAMENTO',
                updated_at = NOW() 
                WHERE codigo_encomenda = :codigo_encomenda",
            $parametros
        );

        return true;
    }
}
