<div class="container">
    <div class="row my-5">
        <div class="col-8 offset-2">

            <table class="table table-striped">
                <?php foreach ($dados_cliente as $key => $value) : ?>
                    <tr>
                        <td width="40%" class="text-end"><?= $key ?>:</td>
                        <td width="60%"><strong><?= $value ?></strong></td>
                    </tr>
                <?php endforeach; ?>
            </table>

        </div>
    </div>
</div>