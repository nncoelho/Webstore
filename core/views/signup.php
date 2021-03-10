<div class="container-fluid rodape-overlay">
    <div class="row mt-3">
        <div class="col-4 offset-4">
            <h3 class="text-center my-4">Registo de novo cliente</h3>
            <form action="?a=signup_submit" method="post">
                <!-- Email -->
                <div class="my-3">
                    <small><label>E-mail:</label></small>
                    <input type="email" name="text_email" class="form-control" placeholder="Endereço de correio electrónico" required>
                </div>
                <!-- Password -->
                <div class="my-3">
                    <small><label>Password:</label></small>
                    <input type="password" name="text_senha1" class="form-control" placeholder="Crie a sua password" required>
                </div>
                <!-- Password confirmacao -->
                <div class="my-3">
                    <small><label>Confirmar password:</label></small>
                    <input type="password" name="text_senha2" class="form-control" placeholder="Confirme a sua password" required>
                </div>
                <!-- Nome completo -->
                <div class="my-3">
                    <small><label>Nome completo:</label></small>
                    <input type="text" name="text_nome_completo" class="form-control" placeholder="Insira o seu nome completo" required>
                </div>
                <!-- Morada -->
                <div class="my-3">
                    <small><label>Morada:</label></small>
                    <input type="text" name="text_morada" class="form-control" placeholder="Insira a sua morada completa" required>
                </div>
                <!-- Cidade -->
                <div class="my-3">
                    <small><label>Cidade:</label></small>
                    <input type="text" name="text_cidade" class="form-control" placeholder="Insira a sua cidade" required>
                </div>
                <!-- Telefone -->
                <div class="my-3">
                    <small><label>Telefone:</label></small>
                    <input type="text" name="text_telefone" class="form-control" placeholder="Número de telefone (opcional)">
                </div>

                <!-- Mensagem de erro no caso das senhas não coincidirem -->
                <?php if (isset($_SESSION['erro'])) : ?>
                    <div class="alert alert-danger my-3 text-center p-2">
                        <?= $_SESSION['erro'] ?>
                        <?php unset($_SESSION['erro']) ?>
                    </div>
                <?php endif; ?>

                <!-- Submit -->
                <div class="my-3 text-center">
                    <input type="submit" class="btn btn-primary btn-150" value="Criar conta">
                </div>
            </form>
        </div>
    </div>
</div>