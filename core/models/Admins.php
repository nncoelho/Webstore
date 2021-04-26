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
}
