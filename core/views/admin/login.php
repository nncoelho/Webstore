<div class="container-fluid">
    <div class="row mt-3">
        <div class="col-4 offset-4">

            <h4 class="text-center my-4">Admin login</h4>

            <form action="?a=admin_login_submit" method="post">
                <!-- Email -->
                <div class="my-4">
                    <small><label>Administrador:</label></small>
                    <input type="email" name="text_admin" class="form-control" placeholder="E-mail de administrador" required>
                </div>
                <!-- Password -->
                <div class="my-4">
                    <small><label>Password:</label></small>
                    <input type="password" name="text_senha" class="form-control" placeholder="Password" required>
                </div>
                <!-- Apresenta mensagen de erro caso existam no quadro de login -->
                <?php if (isset($_SESSION['erro'])) : ?>
                    <div class="alert alert-danger my-3 p-2 text-center">
                        <?= $_SESSION['erro']; ?>
                        <?php unset($_SESSION['erro']); ?>
                    </div>
                <?php endif; ?>

                <!-- Login -->
                <div class="my-3 text-center">
                    <input type="submit" class="btn btn-primary btn-150" value="Entrar">
                </div>
            </form>
        </div>
    </div>
</div>