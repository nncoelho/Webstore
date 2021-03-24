<div class="container">
    <div class="row my-5">
        <div class="col-6 offset-3 text-center">
            <h4 class="my-5">Encomenda confirmada com sucesso</h4>
            <h5>Muito obrigado pela sua compra.</h5>

            <div class="my-4 text-start">
                <h6>Dados da encomenda</h6>
                <small>
                    <p>
                        Preço total: <?= '€' . number_format($total_encomenda, 2, ',', '.') ?> <br>
                        Conta bancária: 1234567890 <br>
                        Código da encomenda: <?= $order_code ?>
                    </p>
                </small>
            </div>

            <p class="text-start">Irá receber um email com a confirmação da encomenda e os respectivos dados para pagamento.
                Por favor verifique a sua caixa de SPAM caso não apareça na sua caixa de entrada.
                A sua encomenda só será processada após confirmação do pagamento.
                Obrigado pela sua preferência e volte sempre.
            </p>
            <div class="my-5 text-center">
                <a href="?a=home" class="btn btn-primary btn-150">Voltar</a>
            </div>
        </div>
    </div>
</div>