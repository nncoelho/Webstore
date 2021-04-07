<?php

namespace core\models;

use core\classes\Database;
use core\classes\Store;

class Orders{

    // ============================================================
    public function saveOrderBD($dados_encomenda, $dados_produtos){

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
    public function getOrderHistory($id_cliente){

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
}
