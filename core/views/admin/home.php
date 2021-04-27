<div class="container">
    <div class="row">
        <h3 class="text-center p-5">Home - Backoffice</h3>
        <div class="col-md-3">
            <?php include(__DIR__ . '\layouts\admin_menu.php'); ?>
        </div>
        <div class="col-md-9">

            <!-- Apresenta informações sobre o total de encomendas em estado PENDENTE -->
            <?php if ($total_pending_orders == 0) : ?>
                <div class="alert alert-success p-3 text-center">
                    <i class="far fa-smile me-2 fa-lg"></i><span>Não existem encomendas em estado: PENDENTE</span>
                </div>
            <?php else : ?>
                <div class="alert alert-primary p-3 text-center">
                    <i class="fas fa-info-circle me-2 fa-lg"></i><span class="me-3">Existem encomendas em estado:
                        <b>PENDENTE</b> - Total: <b><?= $total_pending_orders ?></b></span>
                    <a href="?a=orders_list&f=pending" class="btn btn-info btn-sm"><i class="far fa-eye me-2"></i> Ver</a>
                </div>
            <?php endif; ?>

        </div>
    </div>
</div>