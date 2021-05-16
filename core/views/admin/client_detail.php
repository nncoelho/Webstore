<?php

use core\classes\Store;
?>

<div class="container-fluid">
    <div class="row mt-3">
        <div class="col-md-2">
            <?php include(__DIR__ . '\layouts\admin_menu.php'); ?>
        </div>
        <div class="col-md-10">
            <h4 class="my-1">Detalhe do cliente</h4>
            <hr>
            <div class="row mt-3">
                <!-- Nome completo -->
                <div class="col-2 text-end"><b>Nome completo:</b></div>
                <div class="col-10"><?= $dados_cliente->nome_completo ?></div>
                <!-- E-mail -->
                <div class="col-2 text-end"><b>E-mail:</b></div>
                <div class="col-10"><a href="mailto:<?= $dados_cliente->email ?>"><?= $dados_cliente->email ?></a></div>
                <!-- Morada -->
                <div class="col-2 text-end"><b>Morada:</b></div>
                <div class="col-10"><?= $dados_cliente->morada ?></div>
                <!-- Cidade -->
                <div class="col-2 text-end"><b>Cidade:</b></div>
                <div class="col-10"><?= $dados_cliente->cidade ?></div>
                <!-- Telefone -->
                <div class="col-2 text-end"><b>Telefone:</b></div>
                <div class="col-10"><?= empty($dados_cliente->telefone) ? '---------' : $dados_cliente->telefone ?></div>
                <!-- Estado -->
                <div class="col-2 text-end"><b>Estado:</b></div>
                <div class="col-10"><?= $dados_cliente->activo == 0 ? '<span class="text-danger">Inativo</span>' : '<span class="text-success">Activo</span>' ?></div>
                <!-- Criado em -->
                <div class="col-2 text-end"><b>Cliente desde:</b></div>
                <?php
                $data = DateTime::createFromFormat('Y-m-d H:i:s', $dados_cliente->created_at);
                ?>
                <div class="col-10"><?= $data->format('d-m-Y') ?></div>
            </div>
            <div class="row mt-3">
                <div class="col-10 offset-2">
                    <?php if ($total_encomendas == 0) : ?>
                        <p class="text-a1a1a1 mt-2">Não existe histórico de encomendas deste cliente.</p>
                    <?php else : ?>
                        <a href="?a=client_order_history&c=<?= Store::aesEncrypt($dados_cliente->id_cliente) ?>" class="btn btn-primary">
                            Ver histórico de encomendas deste cliente
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <div class="text-center mt-5">
            <a href="?a=client_list" class="btn btn-secondary btn-150">Voltar</a>
        </div>
    </div>
</div>