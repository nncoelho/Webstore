<?php

use core\classes\Store;
?>

<div class="container-fluid">
    <div class="row mt-3">
        <div class="col-md-2">
            <?php include(__DIR__ . '\layouts\admin_menu.php'); ?>
        </div>
        <div class="col-md-10">
            <div class="row">
                <div class="col">
                    <h4 class="my-1">Detalhe da encomenda:</h4>
                    <i class="fas fa-shopping-basket fa-lg me-2"></i><strong><?= $encomenda->codigo_encomenda ?></strong>
                </div>
                <div class="col text-end">
                    <div class="text-center p-3 badge bg-primary status-click" onclick="showModal()">
                        <?= $encomenda->status ?>
                    </div>
                    <?php if ($encomenda->status == 'EM PROCESSAMENTO') : ?>
                        <div class="mt-2">
                            <a href="?a=pdf" class="btn btn-success btn-sm">PDF da Nota de pagamento</a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
            <hr>
            <div class="row">
                <div class="col">
                    <p>Nome do cliente:<br><strong><?= $encomenda->nome_completo ?></strong></p>
                    <p>E-mail:<br><strong><?= $encomenda->email ?></strong></p>
                    <p>Telefone:<br><strong><?= $encomenda->telefone == 0 ? '---------' : $encomenda->telefone ?></strong></p>
                </div>
                <div class="col">
                    <?php
                    $data = DateTime::createFromFormat('Y-m-d H:i:s', $encomenda->data_encomenda);
                    ?>
                    <p>Data da encomenda:<br><strong><?= $data->format('d-m-Y') ?></strong></p>
                    <p>Morada:<br><strong><?= $encomenda->morada ?></strong></p>
                    <p>Cidade:<br><strong><?= $encomenda->cidade ?></strong></p>
                </div>
            </div>
            <hr>
            <table class="table">
                <thead class="table-dark">
                    <th>Produto</th>
                    <th class="text-end">Preço/unid.</th>
                    <th class="text-center">Quantidade</th>
                </thead>
                <tbody>
                    <?php foreach ($lista_produtos as $produto) : ?>
                        <tr>
                            <td><?= $produto->designacao_produto ?></td>
                            <td class="text-end"><?= '€' . number_format($produto->preco_unidade, 2, ',', '.') ?></td>
                            <td class="text-center"><?= $produto->quantidade ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <div class="text-center mt-5">
            <a href="?a=order_list" class="btn btn-secondary btn-150">Voltar</a>
        </div>
    </div>
</div>

<!-- MODAL -->
<div class="modal fade" id="modalStatus" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Alterar estado da encomenda</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="text-center">
                    <?php foreach (STATUS as $estado) : ?>
                        <?php if ($encomenda->status == $estado) : ?>
                            <p><?= $estado . '<br>' ?></p>
                        <?php else : ?>
                            <p><a href="?a=change_order_status&e=<?= Store::aesEncrypt($encomenda->id_encomenda) ?>&s=<?= $estado ?>"><?= $estado ?></a></p>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
            </div>
        </div>
    </div>
</div>

<script>
    function showModal() {
        var modalStatus = new bootstrap.Modal(document.getElementById('modalStatus'))
        modalStatus.show();
    }
</script>