<?php

// Coleção das routes
$routes = [
    'home'          => 'main@index',
    'webstore'      => 'main@webstore',

    // Clients
    'signup'        => 'main@signup',
    'signup_submit' => 'main@signupSubmit',
    'confirm_email' => 'main@confirmEmail',

    // Login / Logout
    'login'         => 'main@login',
    'login_submit'  => 'main@loginSubmit',
    'logout'        => 'main@logout',

    // User profile
    'profile'                       => 'main@profile',
    'change_personal_data'          => 'main@changePersonalData',
    'change_personal_data_submit'   => 'main@changePersonalDataSubmit',
    'change_password'               => 'main@changePassword',
    'change_password_submit'        => 'main@changePasswordSubmit',
    'order_history'                 => 'main@orderHistory',
    'order_detail'                  => 'main@orderHistoryDetail',

    // Shopping cart
    'shoppingcart'              => 'shoppingcart@shoppingCart',
    'addToShoppingCart'         => 'shoppingcart@addToShoppingCart',
    'clear_shoppingcart'        => 'shoppingcart@clearShoppingCart',
    'delete_item_shoppingcart'  => 'shoppingcart@deleteItemShoppingCart',
    'finalize_order'            => 'shoppingcart@finalizeOrder',
    'finalize_order_resume'     => 'shoppingcart@finalizeOrderResume',
    'alternative_address'       => 'shoppingcart@alternativeAddress',
    'confirm_order'             => 'shoppingcart@confirmOrder'
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
