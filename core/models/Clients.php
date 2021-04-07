<?php

namespace core\models;

use core\classes\Database;
use core\classes\Store;

class Clients{

    // ============================================================
    public function checkifMailExists($email){

        // Verifica na BD se existe uma conta com o mesmo email
        $bd = new Database();
        $parametros = [
            ':email' => strtolower(trim($email))
        ];
        
        $resultados = $bd->select(
            "SELECT email 
                FROM clientes 
                WHERE email = :email",
            $parametros
        );

        // Caso exista alguma conta com o mesmo email
        if (count($resultados) != 0) {
            return true;
        } else {
            return false;
        }
    }

    // ============================================================
    public function saveClientBD(){

        // Regista o novo cliente na BD
        $bd = new Database();

        // Cria uma Hash para definir uma PURL para ativacao de conta
        $purl = Store::createHash();

        $parametros = [
            ':email' => strtolower(trim($_POST['text_email'])),
            ':senha' => password_hash(trim($_POST['text_senha1']), PASSWORD_DEFAULT),
            ':nome_completo' => trim($_POST['text_nome_completo']),
            ':morada' => trim($_POST['text_morada']),
            ':cidade' => trim($_POST['text_cidade']),
            ':telefone' => trim($_POST['text_telefone']),
            ':purl' => $purl,
            ':activo' => 0
        ];

        $bd->insert(
            "INSERT INTO clientes VALUES(
                0,
                :email,
                :senha,
                :nome_completo,
                :morada,
                :cidade,
                :telefone,
                :purl,
                :activo,
                NOW(),
                NOW(),
                NULL)",
            $parametros
        );

        // Retorna o purl criado
        return $purl;
    }

    // ============================================================
    public function validateEmail($purl){

        // Validar o email do novo cliente
        $bd = new Database();
        $parametros = [
            ':purl' => $purl
        ];

        $resultados = $bd->select(
            "SELECT * FROM clientes 
                WHERE purl = :purl",
            $parametros
        );

        // Verifica se foi encontrado o cliente
        if (count($resultados) != 1) {
            return false;
        }

        // Foi encontrado o cliente com o PURL indicado
        $id_cliente = $resultados[0]->id_cliente;

        // Atualiza os dados do cliente
        $parametros = [
            ':id_cliente' => $id_cliente,
        ];
        $bd->update(
            "UPDATE clientes SET purl = NULL, 
                activo = 1, 
                updated_at = NOW() 
                WHERE id_cliente = :id_cliente",
            $parametros
        );
        return true;
    }

    // ============================================================
    public function validateLogin($utilizador, $senha){

        // Verifica se o login é válido
        $parametros = [
            ':utilizador' => $utilizador
        ];

        // Verifica se existe um cliente registado com o endereço de email indicado
        $bd = new Database();
        $resultados = $bd->select(
            "SELECT * FROM clientes 
                WHERE email = :utilizador 
                AND activo = 1 AND deleted_at IS NULL",
            $parametros
        );

        if (count($resultados) != 1) {
            return false;
        } else {

            // Se existir utilizador verifica se a password coincide com a da BD
            $utilizador = $resultados[0];
            if (!password_verify($senha, $utilizador->senha)) {

                // Login inválido password não coincide
                return false;
            } else {
                // Login válido
                return $utilizador;
            }
        }
    }

    // ============================================================
    public function getClientData($id_cliente){

        $parametros = [
            'id_cliente' => $id_cliente
        ];

        $bd = new Database();
        $resultados = $bd->select(
            "SELECT email, nome_completo, morada, cidade, telefone 
                FROM clientes 
                WHERE id_cliente = :id_cliente",
            $parametros
        );
        return $resultados[0];
    }

    // ============================================================
    public function checkifMailExistsInOtherAccount($id_cliente, $email){

        // Verifica se já existe o email noutra conta de cliente
        $parametros = [
            ':email' => $email,
            ':id_cliente' => $id_cliente
        ];

        $bd = new Database();
        $resultados = $bd->select(
            "SELECT id_cliente 
                FROM clientes 
                WHERE id_cliente <> :id_cliente 
                AND email = :email",
            $parametros
        );

        if (count($resultados) != 0) {
            return true;
        } else {
            return false;
        }
    }

    // ============================================================
    public function updateClientDatainBD($email, $nome_completo, $morada, $cidade, $telefone){

        // Atualiza os dados do cliente na base de dados
        $parametros = [
            ':id_cliente' => $_SESSION['cliente'],
            ':email' => $email,
            ':nome_completo' => $nome_completo,
            ':morada' => $morada,
            ':cidade' => $cidade,
            ':telefone' => $telefone
        ];

        $bd = new Database();
        $bd->update(
            "UPDATE clientes 
                SET email = :email, 
                nome_completo = :nome_completo, 
                morada = :morada, 
                cidade = :cidade,
                telefone = :telefone,
                updated_at = NOW()
                WHERE id_cliente = :id_cliente",
            $parametros
        );
    }

    // ============================================================
    public function checkIfPasswordMatchesWithBD($id_cliente, $senha_atual){

        // Verifica se a password atual coincide com a que está na BD
        $parametros = [
            ':id_cliente' => $id_cliente
        ];

        $bd = new Database();
        $senhaBD = $bd->select(
            "SELECT senha 
                FROM clientes 
                WHERE id_cliente = :id_cliente",
            $parametros
        )[0]->senha;

        return password_verify($senha_atual, $senhaBD);
    }

    // ============================================================
    public function updateNewPasswordInBD($id_cliente, $nova_senha){

        // Atualização da senha do cliente na base de dados
        $parametros = [
            ':id_cliente' => $id_cliente,
            ':nova_senha' => password_hash($nova_senha, PASSWORD_DEFAULT)
        ];

        $bd = new Database();
        $bd->update(
            "UPDATE clientes 
                SET senha = :nova_senha,
                updated_at = NOW()
                WHERE id_cliente = :id_cliente",
            $parametros
        );
    }
}
