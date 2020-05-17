<?php


namespace Dochne\Shopping\Database;

use PDO;

class Database
{
    private $pdo;

    /**
     * @var DatabaseMigrations
     */
    private $databaseMigrations;

    public function __construct(DatabaseMigrations $databaseMigrations)
    {
        $this->databaseMigrations = $databaseMigrations;
    }

    private function load() : PDO
    {
        if ($this->pdo === null) {
            $filename = 'sqlite:/' . getcwd() . '/data/database.sqlite';
            $this->pdo = new PDO($filename) or die("cannot open the database");
            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->databaseMigrations->migrate($this);
        }

        return $this->pdo;
    }

    public function insert(string $table, array $row)
    {
        $pdo = $this->load();
        $keys = array_keys($row);
        $params = array_values($row);
        $bound = [];
        for ($x=0; $x<count($params); $x++) {
            $bound[] = "?";
        }
        $sql = "INSERT INTO {$table} (".implode(",", $keys).") VALUES (".implode(",", $bound).")";
        $query = $pdo->prepare($sql);
        $query->execute($params);
    }

    public function update(string $table, array $row, array $where)
    {
        if (empty($where)) {
            throw new \Exception("Update attempted without WHERE");
        }

        $database = $this->load();
        $params = array_values($row);

        $set = [];
        foreach ($row as $key => $value) {
            $set[] = "{$key} = ?";
        }

        $sql = "UPDATE {$table} SET " . implode(",", $set) . " WHERE 1=1";

        foreach ($where as $key => $value) {
            $sql .= " AND {$key}=?";
            $params[] = $value;
        }

        $query = $database->prepare($sql);
        $query->execute($params);
    }

    public function query(string $sql, array $params = []) : array
    {
        $pdo = $this->load();
        $query = $pdo->prepare($sql);
        $query->execute($params);
        return $query->fetchAll(PDO::FETCH_ASSOC);
    }

    public function raw(string $sql)
    {
        return $this->load()->exec($sql);
    }
}