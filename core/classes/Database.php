<?php

namespace core\classes;

use PDO;
use PDOException;

class Database{

    private $connection;

    // ============================================================
    private function connect(){
        // Connection to the database
        $this->connection = new PDO(
            'mysql:' .
                'host=' . MYSQL_SERVER . ';' .
                'dbname=' . MYSQL_DATABASE . ';' .
                'charset=' . MYSQL_CHARSET,
            MYSQL_USER,
            MYSQL_PASS,
            array(PDO::ATTR_PERSISTENT => true)
        );

        // Debug
        $this->connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING);
    }

    // ============================================================
    private function disconnect(){
        // Disconnection from the database
        $this->connection = null;
    }

    // ============================================================
    // CRUD - DB MANAGEMENT
    // ============================================================
    public function select($sql, $parametros = null){

        // Funçao de pesquisa de SQL
        $this->connect();

        $resultados = null;

        try {
            // Comunicação com a BD
            if(!empty($parametros)){
                $executar = $this->connection->prepare($sql);
                $executar->execute($parametros);
                $resultados = $executar->fetchAll(PDO::FETCH_CLASS);
            } else {
                $executar = $this->connection->prepare($sql);
                $executar->execute();
                $resultados = $executar->fetchAll(PDO::FETCH_CLASS);
            }
        } catch (PDOException $e) {
            // Caso exista erro
            return false;
            
        }

        // Desligar da BD
        $this->disconnect();

        // Devolver os resultados obtidos
        return $resultados;
    }
}