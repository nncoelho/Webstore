<?php

namespace core\classes;

use Exception;

class Store{

    // ============================================================
    public static function layout($estruturas, $dados = null){

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
    public static function layoutAdmin($estruturas, $dados = null){

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
            include("../../core/views/$estrutura.php");
        }
    }


    // ============================================================
    public static function clientLogged(){

        // Verifica se existe um cliente logado
        return isset($_SESSION['cliente']);
    }

    // ============================================================
    public static function adminLogged(){

        // Verifica se existe um admin logado
        return isset($_SESSION['admin']);
    }

    // ============================================================
    public static function createHash($num_caracteres = 12){

        $chars = '01234567890123456789abcdefghijklmnopqrstuvwxyzabcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZABCDEFGHIJKLMNOPQRSTUVWXYZ';
        return substr(str_shuffle($chars), 0, $num_caracteres);
    }

    // ============================================================
    public static function redirect($route = '', $admin = false){

        // Faz o redirecionamento para a URL desejada (route)
        if (!$admin) {
            header("Location: " . BASE_URL . "?a=$route");
        } else {
            header("Location: " . BASE_URL . "admin?a=$route");
        }
    }

    // ============================================================
    public static function generateOrderCode(){

        // Gera um código único para cada encomenda
        $codigo = "";
        $chars = "ABCDEFGHIJKLMNOPQRSTUVWXYZABCDEFGHIJKLMNOPQRSTUVWXYZABCDEFGHIJKLMNOPQRSTUVWXYZ";
        $codigo .= substr(str_shuffle($chars), 0, 2);
        $codigo .= rand(100000, 999999);
        return $codigo;
    }

    // ============================================================
    // ENCRIPTAR / DECRIPTAR ID ENCOMENDA
    // ============================================================
    public static function aesEncrypt($valor){
        return bin2hex(openssl_encrypt($valor, 'aes-256-cbc', AES_KEY, OPENSSL_RAW_DATA, AES_IV));
    }

    public static function aesDecrypt($valor){
        return openssl_decrypt(hex2bin($valor), 'aes-256-cbc', AES_KEY, OPENSSL_RAW_DATA, AES_IV);
    }

    // ============================================================
    public static function printData($data){
        if (is_array($data) || is_object($data)) {
            echo '<pre>';
            print_r($data);
        } else {
            echo '<pre>';
            echo $data;
        }
        die('<br>Concluido');
    }
}
