<?php

namespace core\models;

use core\classes\Database;

class Admins
{

    // ============================================================
    public function validateLogin($administrador, $senha)
    {

        // Verifica se o login é válido
        $parametros = [
            ':administrador' => $administrador
        ];

        // Verifica se existe um cliente registado com o endereço de email indicado
        $bd = new Database();
        $resultados = $bd->select(
            "SELECT * FROM admins 
                WHERE administrador = :administrador 
                AND deleted_at IS NULL",
            $parametros
        );

        if (count($resultados) != 1) {
            // Não existe admin
            return false;
        } else {

            // Se existir administrador verifica se a password coincide com a da BD
            $administrador = $resultados[0];
            if (!password_verify($senha, $administrador->senha)) {

                // Login inválido password não coincide
                return false;
            } else {
                // Login válido
                return $administrador;
            }
        }
    }

    // ============================================================
    // CLIENTES
    // ============================================================
    public function listingClients()
    {

        // Vai buscar todos os clientes registados na BD
        $bd = new Database();
        $resultados = $bd->select(
            "SELECT 
                clientes.id_cliente,
                clientes.email,
                clientes.nome_completo,
                clientes.telefone,
                clientes.activo,
                clientes.deleted_at,
            COUNT(encomendas.id_encomenda) total_encomendas
            FROM clientes LEFT JOIN encomendas
            ON clientes.id_cliente = encomendas.id_cliente
            GROUP BY clientes.id_cliente"
        );
        return $resultados;
    }

    // ============================================================
    public function getClient($id_cliente)
    {

        $parametros = [
            'id_cliente' => $id_cliente
        ];

        $bd = new Database();
        $resultados = $bd->select(
            "SELECT * FROM clientes 
                WHERE id_cliente = :id_cliente",
            $parametros
        );
        return $resultados[0];
    }

    // ============================================================
    public function clientTotalOrders($id_cliente)
    {

        $parametros = [
            'id_cliente' => $id_cliente
        ];

        $bd = new Database();
        return $bd->select(
            "SELECT count(*) total 
                FROM encomendas
                WHERE id_cliente = :id_cliente",
            $parametros
        )[0]->total;
    }

    // ============================================================
    public function getClientOrders($id_cliente)
    {

        // Vai buscar todas as encomendas do cliente indicado
        $parametros = [
            ':id_cliente' => $id_cliente
        ];

        $bd = new Database();
        return $bd->select(
            "SELECT *
                FROM encomendas
                WHERE id_cliente = :id_cliente",
            $parametros
        );
    }

    // ============================================================
    // ENCOMENDAS
    // ============================================================
    public function totalPendingOrders()
    {

        // Vai buscar a quantidade de encomendas pendentes
        $bd = new Database();
        $resultados = $bd->select(
            "SELECT COUNT(*) total
                FROM encomendas
            WHERE status = 'PENDENTE'"
        );
        return $resultados[0]->total;
    }

    // ============================================================
    public function totalProcessingOrders()
    {

        // Vai buscar a quantidade de encomendas em processamento
        $bd = new Database();
        $resultados = $bd->select(
            "SELECT COUNT(*) total
                FROM encomendas
            WHERE status = 'EM PROCESSAMENTO'"
        );
        return $resultados[0]->total;
    }

    // ============================================================
    public function listingOrders($filtro, $id_cliente)
    {

        // Vai buscar a lista de encomendas com filtro
        $bd = new Database();
        $sql = "SELECT e.*, c.nome_completo FROM encomendas e LEFT JOIN clientes c ON e.id_cliente = c.id_cliente WHERE 1";
        if ($filtro != '') {
            $sql .= " AND e.status = '$filtro'";
        }
        if (!empty($id_cliente)) {
            $sql .= " AND e.id_cliente = $id_cliente";
        }
        $sql .= " ORDER BY e.id_encomenda DESC";
        return $bd->select($sql);
    }

    // ============================================================
    public function getOrderDetails($id_encomenda)
    {

        // Vai buscar os detalhes de uma encomenda
        $bd = new Database();
        $parametros = [
            ':id_encomenda' => $id_encomenda
        ];

        // Vai buscar os dados da encomenda
        $dados_encomenda = $bd->select(
            "SELECT clientes.nome_completo, encomendas.*
                FROM clientes, encomendas
                WHERE encomendas.id_encomenda = :id_encomenda
                AND clientes.id_cliente = encomendas.id_cliente",
            $parametros
        );

        // Vai buscar a lista de produtos da encomenda
        $lista_produtos = $bd->select(
            "SELECT * 
                FROM encomenda_produto
                WHERE id_encomenda = :id_encomenda",
            $parametros
        );

        return [
            'encomenda'      => $dados_encomenda[0],
            'lista_produtos' => $lista_produtos
        ];
    }

    // ============================================================
    public function updateOrderStatus($id_encomenda, $estado)
    {

        // Atualizar o estado da encomenda na BD
        $bd = new Database();

        $parametros = [
            ':id_encomenda' => $id_encomenda,
            ':status'       => $estado
        ];

        $bd->update(
            "UPDATE encomendas
                SET status = :status,
                updated_at = NOW()
                WHERE id_encomenda = :id_encomenda",
            $parametros
        );
    }
}
