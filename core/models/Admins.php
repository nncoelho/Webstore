<?php

namespace core\models;

use core\classes\Database;
use core\classes\Store;

class Admins{

    // ============================================================
    public function validateLogin($administrador, $senha){

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
    public function listingClients(){

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
    public function getClient($id_cliente){

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
    public function clientTotalOrders($id_cliente){

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
    public function getClientOrders($id_cliente){

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
    public function totalPendingOrders(){

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
    public function totalProcessingOrders(){

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
    public function listingOrders($filtro){

        // Vai buscar a lista de encomendas com filtro
        $bd = new Database();
        $sql = "SELECT e.*, c.nome_completo FROM encomendas e LEFT JOIN clientes c ON e.id_cliente = c.id_cliente";
        if ($filtro != '') {
            $sql .= " WHERE e.status = '$filtro'";
        }
        $sql .= ' ORDER BY e.id_encomenda DESC';
        return $bd->select($sql);
    }
}
