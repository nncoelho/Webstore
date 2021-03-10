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

// ============================================================
function clearShoppingCart() {
    var e = document.getElementById('confirm_clear_shoppingcart');
    e.style.display = "inline";
}

// ============================================================
function clearShoppingCartNo() {
    var e = document.getElementById('confirm_clear_shoppingcart');
    e.style.display = "none";
}

// ============================================================
function defineAddressAlt() {
    // Mostra ou esconde espaço para morada alternativa
    var e = document.getElementById('check_morada_alt');
    if (e.checked == true) {
        document.getElementById('morada_alt').style.display = "block";
    } else {
        document.getElementById('morada_alt').style.display = "none";
    }
}