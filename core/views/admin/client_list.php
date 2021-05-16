<?php

use core\classes\Store;
?>

<div class="container-fluid">
    <div class="row mt-3">
        <div class="col-md-2">
            <?php include(__DIR__ . '\layouts\admin_menu.php'); ?>
        </div>
        <div class="col-md-10">
            <h4 class="my-1">Lista de clientes</h4>
            <hr>
            <?php if (count($clientes) == 0) : ?>
                <p class="text-a1a1a1 my-3">Não existem clientes registados na base de dados.</p>
            <?php else : ?>
                <!-- Apresenta a tabela de clientes -->
                <table class="table table-striped table-sm" id="clients-table">
                    <thead class="table-dark">
                        <tr>
                            <th>Nome</th>
                            <th>E-mail</th>
                            <th>Telefone</th>
                            <th class="text-center">Encomendas</th>
                            <th class="text-center">Activo</th>
                            <th class="text-center">Eliminado</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($clientes as $cliente) : ?>
                            <tr>
                                <td>
                                    <a href="?a=client_detail&c=<?= Store::aesEncrypt($cliente->id_cliente) ?>"><?= $cliente->nome_completo ?></a>
                                </td>
                                <td><?= $cliente->email ?></td>
                                <td><?= $cliente->telefone ?></td>
                                <td class="text-center">
                                    <?php if ($cliente->total_encomendas == 0) : ?>
                                        -
                                    <?php else : ?>
                                        <?= $cliente->total_encomendas ?>
                                    <?php endif; ?>
                                </td>
                                <!-- Activo -->
                                <td class="text-center">
                                    <?php if ($cliente->activo == 1) : ?>
                                        <i class="text-success fas fa-check-circle"></i>
                                    <?php else : ?>
                                        <i class="text-danger fas fa-times-circle"></i>
                                    <?php endif; ?>
                                </td>
                                <!-- Eliminado -->
                                <td class="text-center">
                                    <?php if ($cliente->deleted_at == null) : ?>
                                        <i class="text-danger fas fa-times-circle"></i>
                                    <?php else : ?>
                                        <i class="text-success fas fa-check-circle"></i>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
            <hr>
        </div>
    </div>
</div>
<script>
    $(document).ready(function() {
        $('#clients-table').DataTable({
            language: {
                lengthMenu: "Apresenta _MENU_ clientes por página",
                zeroRecords: "Não foram encontrados clientes",
                info: "Mostrando página _PAGE_ de um total de _PAGES_ página(s)",
                infoEmpty: "Não existem clientes disponiveis",
                infoFiltered: "(Filtrado de um total de _MAX_ cliente(s))",
                search: "Pesquisa:",
                paginate: {
                    first: "Primeira",
                    last: "Última",
                    next: "Seguinte",
                    previous: "Anterior"
                }
            }
        });
    });
</script>