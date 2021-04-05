<div class="container rodape-overlay">
    <div class="row">
        <div class="col-8 offset-2">

            <h3 class="text-center my-4">Alterar dados pessoais</h3>
            <form action="?a=change_personal_data_submit" method="post">
                <!-- Email -->
                <div class="my-3">
                    <small><label>E-mail:</label></small>
                    <input type="email" maxlength="50" name="text_email" class="form-control" value="<?= $dados_pessoais->email ?>" required>
                </div>
                <!-- Nome completo -->
                <div class="my-3">
                    <small><label>Nome completo:</label></small>
                    <input type="text" maxlength="50" name="text_nome_completo" class="form-control" value="<?= $dados_pessoais->nome_completo ?>" required>
                </div>
                <!-- Morada -->
                <div class="my-3">
                    <small><label>Morada:</label></small>
                    <input type="text" maxlength="100" name="text_morada" class="form-control" value="<?= $dados_pessoais->morada ?>" required>
                </div>
                <!-- Cidade -->
                <div class="my-3">
                    <small><label>Cidade:</label></small>
                    <input type="text" maxlength="50" name="text_cidade" class="form-control" value="<?= $dados_pessoais->cidade ?>" required>
                </div>
                <!-- Telefone -->
                <div class="my-3">
                    <small><label>Telefone:</label></small>
                    <input type="text" maxlength="20" name="text_telefone" class="form-control" value="<?= $dados_pessoais->telefone ?>">
                </div>

                <!-- Tratamento de mensagens de erro -->
                <?php if (isset($_SESSION['erro'])) : ?>
                    <div class="alert alert-danger my-3 text-center p-2">
                        <?= $_SESSION['erro'] ?>
                        <?php unset($_SESSION['erro']) ?>
                    </div>
                <?php endif; ?>

                <!-- Submit -->
                <div class="my-4 text-center">
                    <a href="?a=profile" class="btn btn-secondary btn-150 m-1">Cancelar</a>
                    <input type="submit" class="btn btn-primary btn-150 m-1" value="Guardar">
                </div>
            </form>

        </div>
    </div>
</div>