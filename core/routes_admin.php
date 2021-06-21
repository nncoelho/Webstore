<?php

// =========================================
// ADMIN ROUTES
// =========================================

// Coleção das routes
$routes = [
    // Admin
    'home'                  => 'admin@index',
    'admin_login'           => 'admin@adminLogin',
    'admin_login_submit'    => 'admin@adminLoginSubmit',
    'admin_logout'          => 'admin@adminLogout',

    // Clientes
    'client_list'           => 'admin@clientList',
    'client_detail'         => 'admin@clientDetail',
    'client_order_history'  => 'admin@clientOrderHistory',

    // Encomendas
    'order_list'            => 'admin@orderList',
    'order_details'         => 'admin@orderDetails',
    'change_order_status'   => 'admin@changeOrderStatus',

    'create_pdf_order'      => 'admin@createPDFOrder',
    'send_pdf_order'        => 'admin@sendPDFOrder'
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
