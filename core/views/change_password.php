<div class="container">
    <div class="row">
        <div class="col-6 offset-3">
            <h3 class="text-center mb-5">Alterar password</h3>
            <form action="?a=change_password_submit" method="post">
                <!-- Password -->
                <div class="my-3">
                    <small><label>Password atual:</label></small>
                    <input type="password" maxlength="30" name="text_senha_atual" class="form-control" required>
                </div>
                <div class="my-3">
                    <small><label>Nova password:</label></small>
                    <input type="password" maxlength="30" name="text_nova_senha" class="form-control" required>
                </div>
                <div class="my-3">
                    <small><label>Confirmação da nova password:</label></small>
                    <input type="password" maxlength="30" name="text_rep_nova_senha" class="form-control" required>
                </div>

                <!-- Tratamento de mensagens de erro no caso de existirem -->
                <?php if (isset($_SESSION['erro'])) : ?>
                    <div class="alert alert-danger my-3 text-center p-2">
                        <?= $_SESSION['erro'] ?>
                        <?php unset($_SESSION['erro']) ?>
                    </div>
                <?php endif; ?>

                <!-- Submit -->
                <div class="mt-4 text-center">
                    <a href="?a=profile" class="btn btn-secondary btn-150 m-1">Cancelar</a>
                    <input type="submit" class="btn btn-primary btn-150 m-1" value="Alterar">
                </div>
            </form>
        </div>
    </div>
</div>