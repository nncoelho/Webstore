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

    // Mostra os botões de confirmação para limpeza do carrinho
    var e = document.getElementById('confirm_clear_shoppingcart');
    e.style.display = "inline";
}

// ============================================================
function clearShoppingCartNo() {

    // Esconde os botões de confirmação para limpeza do carrinho
    var e = document.getElementById('confirm_clear_shoppingcart');
    e.style.display = "none";
}

// ============================================================
function defineAltAddress() {

    // Mostra ou esconde espaço para morada alternativa
    var e = document.getElementById('check_morada_alt');
    if (e.checked == true) {
        document.getElementById('morada_alt').style.display = "block";
    } else {
        document.getElementById('morada_alt').style.display = "none";
    }
}

// ============================================================
function alternativeAddress() {

    axios({
        method: 'post',
        url: '?a=alternativeAddress',
        data: {
            text_morada: document.getElementById('text_morada_alt').value,
            text_cidade: document.getElementById('text_cidade_alt').value,
            text_email: document.getElementById('text_email_alt').value,
            text_telefone: document.getElementById('text_telefone_alt').value
        }
    });
}