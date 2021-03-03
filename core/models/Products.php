<?php

namespace core\models;

use core\classes\Database;
use core\classes\Store;

class Products{

    // ============================================================
    public function lista_produtos_disponiveis($categoria){
        
        // Obtem todas as informações dos produtos da BD
        $bd = new Database();

        // Obtem a lista das categorias na BD
        $categorias = $this->lista_categorias();

        $sql = "SELECT * FROM produtos ";
        $sql .= "WHERE visivel = 1 ";

        if(in_array($categoria, $categorias)){
            $sql .= "AND categoria = '$categoria'";
        }

        $stock_produtos = $bd->select($sql);
        return $stock_produtos;
    }

    // ============================================================
    public function lista_categorias(){
        
        // Devolve a lista de categorias existentes na BD
        $bd = new Database();
        $resultados = $bd->select("SELECT DISTINCT categoria FROM produtos");
        $categorias = [];
        foreach($resultados as $resultado){
            array_push($categorias, $resultado->categoria);
        }
        return $categorias;
    }

    // ============================================================
    public function verifica_stock_produto($id_produto){

        $bd = new Database();
        $parametros = [
            ':id_produto' => $id_produto
        ];
        $stock = $bd->select("SELECT * FROM produtos WHERE id_produto = :id_produto AND visivel = 1 AND stock > 0", $parametros);

        return count($stock) != 0 ? true : false;
    }

    // ============================================================
    public function get_products_by_ids($ids){

        $bd = new Database();
        return $bd->select("SELECT * FROM produtos WHERE id_produto IN ($ids)");
    }
}