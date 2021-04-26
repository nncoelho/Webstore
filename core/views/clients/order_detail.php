<div class="container rodape-overlay">
    <div class="row">
        <div class="col-10 offset-1 text-center">
            <h3 class="mb-5">Detalhes da encomenda</h3>
            <!-- Dados da encomenda -->
            <div class="row">
                <div class="col box-product mx-2">
                    <div class="p-2 my-3">
                        <span><strong>Data da encomenda:</strong></span><br>
                        <?= $dados_encomenda->data_encomenda ?>
                    </div>
                    <div class="p-2 my-3">
                        <span><strong>Morada:</strong></span><br>
                        <?= $dados_encomenda->morada ?>
                    </div>
                    <div class="p-2 my-3">
                        <span><strong>Cidade:</strong></span><br>
                        <?= $dados_encomenda->cidade ?>
                    </div>
                </div>
                <div class="col box-product mx-2">
                    <div class="p-2 my-3">
                        <span><strong>E-mail:</strong></span><br>
                        <?= $dados_encomenda->email ?>
                    </div>
                    <div class="p-2 my-3">
                        <span><strong>Telefone:</strong></span><br>
                        <?= !empty($dados_encomenda->telefone) ? $dados_encomenda->telefone : '&nbsp;' ?>
                    </div>
                    <div class="p-2 my-3">
                        <span><strong>Código da encomenda:</strong></span><br>
                        <?= $dados_encomenda->codigo_encomenda ?>
                    </div>
                </div>
                <div class="col box-product mx-2">
                    <div class="p-2 my-5">
                        <span><strong>Estado da encomenda:</strong></span><br>
                        <h5 class="mt-4"><?= $dados_encomenda->status ?></h5>
                    </div>
                </div>
            </div>

            <!-- Lista de produtos da encomenda -->
            <h4 class="mt-4 text-start">Produtos da encomenda:</h4>
            <div class="row">
                <div class="col">
                    <table class="table table-striped">
                        <thead class="table-dark">
                            <tr>
                                <th class="text-start">Produto</th>
                                <th>Quantidade</th>
                                <th class="text-end">Preço / uni.</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($produtos_encomenda as $produto) : ?>
                                <tr>
                                    <td class="text-start"><?= $produto->designacao_produto ?></td>
                                    <td><?= $produto->quantidade ?></td>
                                    <td class="text-end"><?= '€' . number_format($produto->preco_unidade, 2, ',', '.'); ?></td>
                                </tr>
                            <?php endforeach; ?>
                            <tr>
                                <td colspan="3" class="text-end">
                                    <b><span>Total: &nbsp; </span></b>
                                    <?= '€' . number_format($total_encomenda, 2, ',', '.') ?>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="row mt-3">
                <div class="col text-center">
                    <a href="?a=order_history" class="btn btn-primary btn-150">Voltar</a>
                </div>
            </div>
        </div>
    </div>
</div>