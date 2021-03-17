<div class="container-fluid rodape-overlay">
    <div class="row">
        <div class="col-8 offset-2 text-center">
            <h3 class="mt-5">Carrinho das compras</h3>
            <hr>
            <?php if ($shoppingcart == null) : ?>
                <h4 class="my-5 p-5 box-product">Não existem produtos no carrinho</h4>
                <a href="?a=webstore" class="btn btn-primary">Voltar à loja</a>
            <?php else : ?>

                <!-- Tabela dos produtos da encomenda -->
                <table class="table">
                    <thead>
                        <tr>
                            <th>Item</th>
                            <th class="text-start">Descrição</th>
                            <th>Quantidade</th>
                            <th class="text-end">Valor</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $index = 0;
                        $total_rows = count($shoppingcart);
                        ?>
                        <!-- Ciclo da lista dos produtos -->
                        <?php foreach ($shoppingcart as $produto) : ?>
                            <?php if ($index < $total_rows - 1) : ?>
                                <tr>
                                    <td class="align-middle"><img src="assets/images/produtos/<?= $produto['imagem']; ?>" alt="img" class="img-fluid" width="45px"></td>
                                    <td class="align-middle text-start"><?= $produto['titulo'] ?></td>
                                    <td class="align-middle"><?= $produto['qtd'] ?></td>
                                    <td class="align-middle text-end"><?= '€' . number_format($produto['preco'], 2, ',', '.'); ?></td>
                                    <td class="align-middle">
                                        <a href="?a=delete_item_shopcart&id_produto=<?= $produto['id_produto'] ?>" class="btn btn-danger btn-sm">
                                            <i class="fas fa-trash-alt"></i>
                                        </a>
                                    </td>
                                </tr>
                            <?php else : ?>
                                <tr>
                                    <td></td>
                                    <td></td>
                                    <td class="align-middle text-end">Valor total:</td>
                                    <td class="align-middle text-end">
                                        <h6><?= '€' . number_format($produto, 2, ',', '.'); ?></h6>
                                    </td>
                                    <td></td>
                                </tr>
                            <?php endif; ?>
                            <?php $index++; ?>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                <div class="row">
                    <div class="col text-start">
                        <button onclick="clearShoppingCart()" class="btn btn-warning btn-sm">Limpar carrinho</button>
                        <span id="confirm_clear_shoppingcart" class="ms-2" style="display: none">Tem a certeza?
                            <button onclick="clearShoppingCartNo()" class="btn btn-primary btn-sm">Não</button>
                            <a href="?a=clear_shoppingcart" class="btn btn-danger btn-sm">Sim</a>
                        </span>
                    </div>
                    <div class="col text-end">
                        <a href="?a=webstore" class="btn btn-primary btn-sm">Continuar a comprar</a>
                        <a href="?a=finalizeOrder" class="btn btn-success btn-sm">Finalizar a encomenda</a>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>