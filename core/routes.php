<?php

// Coleção de routes
$routes = [
    'home'          => 'main@index',
    'webstore'      => 'main@webstore',
    // Clientes
    'signup'        => 'main@signup',
    'signup_submit' => 'main@signup_submit',
    'confirm_email' => 'main@confirm_email',
    // Login & Logout
    'login'         => 'main@login',
    'login_submit'  => 'main@login_submit',
    'logout'        => 'main@logout',
    // Shopping cart
    'shoppingcart'          => 'shoppingcart@shopping_cart',
    'addToShoppingCart'     => 'shoppingcart@addToShoppingCart',
    'clear_shoppingcart'    => 'shoppingcart@clear_shoppingcart',
    'delete_item_shopcart'  => 'shoppingcart@delete_item_shopcart',
    'finalizeOrder'         => 'shoppingcart@finalizeOrder',
    'finalizeOrderResume'   => 'shoppingcart@finalizeOrderResume',
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
