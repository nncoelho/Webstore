<?php

namespace core\classes;

use Exception;
use PDO;
use PDOException;

class Database
{

    private $connection;

    // ============================================================
    private function connect()
    {
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
    private function disconnect()
    {
        // Disconnection from the database
        $this->connection = null;
    }

    // ============================================================
    // CRUD - DB MANAGEMENT
    // ============================================================
    public function select($sql, $parametros = null)
    {

        $sql = trim($sql);

        // Verifica se é uma instrução SELECT (case insensitive)
        if (!preg_match("/^SELECT/i", $sql)) {
            throw new Exception("Base de dados - Não é uma instrução SELECT");
        }

        // Liga a BD
        $this->connect();

        $resultados = null;

        try {
            // Comunicação com a BD
            if (!empty($parametros)) {
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

        // Desliga da BD
        $this->disconnect();

        // Devolve os resultados obtidos
        return $resultados;
    }

    // ============================================================
    public function insert($sql, $parametros = null)
    {

        $sql = trim($sql);

        // Verifica se é uma instrução INSERT (case insensitive)
        if (!preg_match("/^INSERT/i", $sql)) {
            throw new Exception("Base de dados - Não é uma instrução INSERT");
        }

        // Liga a BD
        $this->connect();

        try {
            // Comunicação com a BD
            if (!empty($parametros)) {
                $executar = $this->connection->prepare($sql);
                $executar->execute($parametros);
            } else {
                $executar = $this->connection->prepare($sql);
                $executar->execute();
            }
        } catch (PDOException $e) {
            // Caso exista erro
            return false;
        }

        // Desliga da BD
        $this->disconnect();
    }

    // ============================================================
    public function update($sql, $parametros = null)
    {

        $sql = trim($sql);

        // Verifica se é uma instrução UPDATE (case insensitive)
        if (!preg_match("/^UPDATE/i", $sql)) {
            throw new Exception("Base de dados - Não é uma instrução UPDATE");
        }

        // Liga a BD
        $this->connect();

        try {
            // Comunicação com a BD
            if (!empty($parametros)) {
                $executar = $this->connection->prepare($sql);
                $executar->execute($parametros);
            } else {
                $executar = $this->connection->prepare($sql);
                $executar->execute();
            }
        } catch (PDOException $e) {
            // Caso exista erro
            return false;
        }

        // Desliga da BD
        $this->disconnect();
    }

    // ============================================================
    public function delete($sql, $parametros = null)
    {

        $sql = trim($sql);

        // Verifica se é uma instrução DELETE (case insensitive)
        if (!preg_match("/^DELETE/i", $sql)) {
            throw new Exception("Base de dados - Não é uma instrução DELETE");
        }

        // Liga a BD
        $this->connect();

        try {
            // Comunicação com a BD
            if (!empty($parametros)) {
                $executar = $this->connection->prepare($sql);
                $executar->execute($parametros);
            } else {
                $executar = $this->connection->prepare($sql);
                $executar->execute();
            }
        } catch (PDOException $e) {
            // Caso exista erro
            return false;
        }

        // Desliga da BD
        $this->disconnect();
    }

    // ============================================================
    // GENERIC SQL
    // ============================================================
    public function statement($sql, $parametros = null)
    {

        $sql = trim($sql);

        // Verifica se é uma instrução diferente das anteriores (case insensitive)
        if (preg_match("/^(SELECT|INSERT|UPDATE|DELETE)/i", $sql)) {
            throw new Exception("Base de dados - Instrução inválida");
        }

        // Liga a BD
        $this->connect();

        try {
            // Comunicação com a BD
            if (!empty($parametros)) {
                $executar = $this->connection->prepare($sql);
                $executar->execute($parametros);
            } else {
                $executar = $this->connection->prepare($sql);
                $executar->execute();
            }
        } catch (PDOException $e) {
            // Caso exista erro
            return false;
        }

        // Desliga da BD
        $this->disconnect();
    }
}
