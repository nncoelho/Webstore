<?php

// =========================================
// ADMIN ROUTES
// =========================================

// Coleção das routes
$routes = [
    'home'                  => 'admin@index',
    'admin_login'           => 'admin@adminLogin',
    'admin_login_submit'    => 'admin@adminLoginSubmit',
    'admin_logout'          => 'admin@adminLogout',
    'client_list'           => 'admin@clientList'
];

// Define a ação por defeito
$acao = 'home';

// Verifica se existe a ação na query string
if (isset($_GET['a'])) {

    // Verifica se a ação existe nas routes
    if (!key_exists($_GET['a'], $routes)) {
        $acao = 'home';
    } else {
        $acao = $_GET['a'];
    }
}

// Trata a definição e estrutura da route
$partes = explode('@', $routes[$acao]);
$controlador = 'core\\controllers\\' . ucfirst($partes[0]);
$metodo = $partes[1];

$ctr = new $controlador();
$ctr->$metodo();
