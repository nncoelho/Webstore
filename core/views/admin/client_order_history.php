<div class="container-fluid">
    <div class="row mt-3">
        <div class="col-md-2">
            <?php include(__DIR__ . '\layouts\admin_menu.php'); ?>
        </div>
        <div class="col-md-10">
            <h4 class="my-1">Histórico de encomendas do cliente</h4>
            <hr>
            <div class="row">
                <div class="col">Nome: <b><?= $cliente->nome_completo ?></b></div>
                <div class="col">E-mail: <b><?= $cliente->email ?></b></div>
                <div class="col">Telefone: <b><?= $cliente->telefone == 0 ? '---------' : $cliente->telefone ?></b></div>
            </div>
            <hr>
            <?php if (count($listing_orders) == 0) : ?>
                <hr>
                <p class="text-a1a1a1 my-3">Não existe histórico de encomendas deste cliente registadas na base de dados.</p>
            <?php else : ?>
                <small>
                    <table class="table table-striped table-sm" id="orders-table">
                        <thead class="table-dark">
                            <tr>
                                <th>Data</th>
                                <th>Morada</th>
                                <th>Cidade</th>
                                <th>E-mail</th>
                                <th>Telefone</th>
                                <th>Código</th>
                                <th>Status</th>
                                <th>Mensagem</th>
                                <th>Atualizada em</th>
                            </tr>
                        </thead>
                        <tbody>

                            <?php foreach ($listing_orders as $order) : ?>
                                <tr>
                                    <td><?= $order->data_encomenda ?></td>
                                    <td><?= $order->morada ?></td>
                                    <td><?= $order->cidade ?></td>
                                    <td><?= $order->email ?></td>
                                    <td><?= $order->telefone ?></td>
                                    <td><?= $order->codigo_encomenda ?></td>
                                    <td><?= $order->status ?></td>
                                    <td><?= $order->mensagem ?></td>
                                    <td><?= $order->updated_at ?></td>
                                </tr>
                            <?php endforeach; ?>

                        </tbody>
                    </table>
                </small>
            <?php endif; ?>
            <hr>
        </div>
        <div class="text-center mt-5">
            <a href="?a=client_list" class="btn btn-secondary btn-150">Voltar</a>
        </div>
    </div>
</div>
<script>
    $(document).ready(function() {
        $('#orders-table').DataTable({
            language: {
                lengthMenu: "Apresenta _MENU_ encomendas por página",
                zeroRecords: "Não foram encontradas encomendas",
                info: "Mostrando página _PAGE_ de um total de _PAGES_ página(s)",
                infoEmpty: "Não existem encomendas disponiveis",
                infoFiltered: "(Filtrado de um total de _MAX_ encomendas)",
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