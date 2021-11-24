<?php

use core\classes\Store;
?>
<div class="container">
    <div class="row">
        <div class="col-8 offset-2">
            <h3 class="text-center mb-5">Histórico de encomendas</h3>
            <?php if (count($order_history) == 0) : ?>
                <div class="text-center my-5">
                    <h3 class="box-product p-5">Não existem encomendas registadas</h3>
                </div>
            <?php else : ?>
                <table class="table table-striped">
                    <thead class="table-dark">
                        <tr class="text-center">
                            <th>Data da encomenda</th>
                            <th>Código da encomenda</th>
                            <th>Status</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($order_history as $order) : ?>
                            <tr class="text-center">
                                <td><?= $order->data_encomenda ?></td>
                                <td><?= $order->codigo_encomenda ?></td>
                                <td><?= $order->status ?></td>
                                <td>
                                    <a href="?a=order_detail&id=<?= Store::aesEncrypt($order->id_encomenda) ?>">Detalhes</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                <hr>
                <p><b>Total de encomendas:</b> <?= count($order_history) ?></p>
            <?php endif; ?>
        </div>
    </div>
</div>