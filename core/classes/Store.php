<?php

namespace core\classes;

use Exception;

class Store{

    // ============================================================
    public static function Layout($estruturas, $dados = null){

        // Verifica se estruturas é um array
        if (!is_array($estruturas)) {
            throw new Exception("Coleção de estruturas inválida");
        }

        // Variáveis
        if (!empty($dados) && is_array($dados)) {
            extract($dados);
        }

        // Apresenta as views da aplicação
        foreach ($estruturas as $estrutura) {
            include("../core/views/$estrutura.php");
        }
    }

    // ============================================================
    public static function clienteLogado(){

        // Verifica se existe um cliente com sessao
        return isset($_SESSION['cliente']);
    }

    // ============================================================
    public static function criarHash($num_caracteres = 12){

        $chars = '01234567890123456789abcdefghijklmnopqrstuvwxyzabcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZABCDEFGHIJKLMNOPQRSTUVWXYZ';
        return substr(str_shuffle($chars), 0, $num_caracteres);
    }

    // ============================================================
    public static function redirect($route = ''){

        // Faz o redirecionamento para a URL desejada (route)
        header("Location: " . BASE_URL . "?a=$route");
    }

    // ============================================================
    public static function printData($data){
        if(is_array($data) || is_object($data)){
            echo '<pre>';
            print_r($data);
        } else {
            echo '<pre>';
            echo $data;
        }
        die('<br>CONCLUIDO');
    }
}