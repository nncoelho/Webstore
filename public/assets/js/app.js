// APP JS

// ============================================================
function addToShoppingCart(id_produto) {

    // Adiciona produto ao carrinho
    axios.default.withCredentials = true;
    axios.get('?a=addToShoppingCart&id_produto=' + id_produto)
        .then(function (response) {
            
            var total_produtos = response.data;
            document.getElementById('shopcart_qtd').innerText = total_produtos;
        });
}