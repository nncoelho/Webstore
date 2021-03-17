<?php

use core\classes\Store;

// Calcula o número de produtos no carrinho
$total_produtos = 0;
if (isset($_SESSION['shoppingcart'])) {
    foreach ($_SESSION['shoppingcart'] as $shopcart_qtd) {
        $total_produtos += $shopcart_qtd;
    }
}
?>

<div class="container-fluid navegacao">
    <div class="row">
        <div class="col-6 p-3">
            <a href="?a=home">
                <h3><?= APP_NAME ?></h3>
            </a>
        </div>
        <div class="col-6 p-3 text-end">
            <a href="?a=home" class="nav-item">Home</a>
            <a href="?a=webstore" class="nav-item">Store</a>

            <!-- Verifica se existe cliente na sessao -->
            <?php if (Store::clienteLogado()) : ?>
                <i class="fas fa-user-alt me-2"></i><?= $_SESSION['utilizador'] ?>
                <a href="?a=logout" class="nav-item"><i class="fas fa-sign-out-alt"></i></a>
            <?php else : ?>
                <a href="?a=login" class="nav-item">Login</a>
                <a href="?a=signup" class="nav-item">Signup</a>
            <?php endif; ?>

            <!-- Carrinho com condição ternária para apresentação do total de produtos -->
            <a href="?a=shoppingcart"><i class="fas fa-shopping-cart"></i></a>
            <span class="badge bg-warning" id="shopcart_qtd"><?= $total_produtos == 0 ? '' : $total_produtos ?></span>
        </div>
    </div>
</div>