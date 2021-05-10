<div class="container-fluid">
    <div class="row">
        <h3 class="text-center my-5">• Lista de encomendas •</h3>

        <div class="col-md-2">
            <?php include(__DIR__ . '\layouts\admin_menu.php'); ?>
        </div>

        <div class="col-md-10">
            <hr>
            <div class="d-inline-flex">
                <div>
                    <a href="?a=order_list" class="btn btn-primary btn-sm">Ver todas as encomendas</a>
                </div>

                <!-- Filter Status -->
                <?php
                $f = '';
                if (isset($_GET['f'])) {
                    $f = $_GET['f'];
                }
                ?>

                <div class="ms-3 align-self-center">
                    <label>Escolher estado:</label>
                    <select id="combo-status" onchange="define_filter()">
                        <option value="" <?= $f == '' ? 'selected' : '' ?>></option>
                        <option value="pendente" <?= $f == 'pendente' ? 'selected' : '' ?>>Pendentes</option>
                        <option value="em_processamento" <?= $f == 'em_processamento' ? 'selected' : '' ?>>Em processamento</option>
                        <option value="enviada" <?= $f == 'enviada' ? 'selected' : '' ?>>Enviadas</option>
                        <option value="cancelada" <?= $f == 'cancelada' ? 'selected' : '' ?>>Canceladas</option>
                        <option value="concluida" <?= $f == 'concluida' ? 'selected' : '' ?>>Concluidas</option>
                    </select>
                </div>
            </div>
            <hr>
            <?php if (count($listing_orders) == 0) : ?>
                <p class="text-a1a1a1 my-3">Não existem encomendas registadas na base de dados.</p>
            <?php else : ?>
                <small>
                    <table class="table table-striped table-sm" id="orders-table">
                        <thead class="table-dark">
                            <tr>
                                <th>Data</th>
                                <th>Código</th>
                                <th>Nome cliente</th>
                                <th>E-mail</th>
                                <th>Telefone</th>
                                <th>Status</th>
                                <th>Atualizado em</th>
                            </tr>
                        </thead>
                        <tbody>

                            <?php foreach ($listing_orders as $order) : ?>
                                <tr>
                                    <td><?= $order->data_encomenda ?></td>
                                    <td><?= $order->codigo_encomenda ?></td>
                                    <td><?= $order->nome_completo ?></td>
                                    <td><?= $order->email ?></td>
                                    <td><?= $order->telefone ?></td>
                                    <td><?= $order->status ?></td>
                                    <td><?= $order->updated_at ?></td>
                                </tr>
                            <?php endforeach; ?>

                        </tbody>
                    </table>
                </small>
            <?php endif; ?>
            <hr>
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

    function define_filter() {
        var filtro = document.getElementById("combo-status").value;
        // Reload da página com o determinado filtro
        window.location.href = window.location.pathname + "?" + $.param({
            'a': 'order_list',
            'f': filtro
        });
    }
</script>