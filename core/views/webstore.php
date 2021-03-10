<div class="container rodape-overlay">
    <!-- Botões das categorias -->
    <div class="row">
        <div class="col-8 offset-2 text-center mt-5">
            <a href="?a=webstore&c=geral" class="btn btn-secondary btn-150">Geral</a>
            <?php foreach ($categorias as $categoria) : ?>
                <a href="?a=webstore&c=<?= $categoria ?>" class="btn btn-primary btn-150">
                    <!-- Botões das secções e substitui o _ por " " caso exista nos campos da BD -->
                    <?= ucfirst(preg_replace("/\_/", " ", $categoria)) ?>
                </a>
            <?php endforeach; ?>
            <hr>
        </div>
    </div>

    <!-- Produtos -->
    <div class="row">
        <!-- Caso não existam produtos -->
        <?php if (count($produtos) == 0) : ?>
            <div class="text-center my-5">
                <h3>Não existem produtos disponiveis</h3>
            </div>
        <?php else : ?>
            <!-- Ciclo de apresentação dos produtos -->
            <?php foreach ($produtos as $produto) : ?>
                <div class="col-sm-3 p-2">
                    <div class="text-center p-3 box-product">
                        <img class="img-fluid" src="assets/images/produtos/<?= $produto->imagem ?>">
                        <h6><i><?= $produto->nome_produto ?></i></h6>
                        <h5><b><?= preg_replace("/\./", ",", '€'.$produto->preco) ?></b></h5>
                        <div class="text-center my-2">
                            <!-- Gestão do stock dos produtos na BD -->
                            <?php if ($produto->stock > 0) : ?>
                                <!-- Existe em stock -->
                                <button class="btn btn-outline-primary btn-sm" onclick="addToShoppingCart(<?= $produto->id_produto ?>)">
                                    <i class="fas fa-shopping-cart me-2"></i>Adicionar ao carrinho
                                </button>
                            <?php else : ?>
                                <!-- Ruptura de stock -->
                                <button class="btn btn-info btn-sm" disabled><i class="fas fa-shopping-cart me-2"></i>Indisponivel</button>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>