<div class="container-fluid">
    <div class="row mt-3">
        <div class="col-md-2">
            <?php include(__DIR__ . '\layouts\admin_menu.php'); ?>
        </div>
        <div class="col-md-10">
            <h4 class="my-1">Home - Backoffice</h4>
            <hr>
            <!-- Apresenta informações sobre o total de encomendas em estado PENDENTE -->
            <h5><i class="far fa-hand-paper me-2"></i>Encomendas pendentes:</h5>
            <?php if ($total_pending_orders == 0) : ?>
                <p class="text-a1a1a1 my-3">Não existem encomendas em estado: PENDENTE</p>
            <?php else : ?>
                <div class="alert alert-primary p-3 text-center">
                    <i class="fas fa-info-circle me-2 fa-lg"></i><span class="me-3">Existem encomendas em estado:
                        <b>PENDENTE</b> - Total: <b><?= $total_pending_orders ?></b></span>
                    <a href="?a=order_list&f=pendente" class="btn btn-warning btn-sm"><i class="far fa-eye me-2"></i> Ver</a>
                </div>
            <?php endif; ?>
            <hr>
            <!-- Apresenta informações sobre o total de encomendas EM PROCESSAMENTO -->
            <h5><i class="fas fa-cogs me-2"></i>Encomendas em processamento:</h5>
            <?php if ($total_processing_orders == 0) : ?>
                <p class="text-a1a1a1 my-3">Não existem encomendas no estado: EM PROCESSAMENTO</p>
            <?php else : ?>
                <div class="alert alert-success p-3 text-center">
                    <i class="fas fa-info-circle me-2 fa-lg"></i><span class="me-3">Existem encomendas no estado:
                        <b>EM PROCESSAMENTO</b> - Total: <b><?= $total_processing_orders ?></b></span>
                    <a href="?a=order_list&f=em_processamento" class="btn btn-warning btn-sm"><i class="far fa-eye me-2"></i> Ver</a>
                </div>
            <?php endif; ?>
            <hr>
        </div>
    </div>
</div>