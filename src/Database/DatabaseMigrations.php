<?php

namespace Dochne\Shopping\Database;

use PDO;

class DatabaseMigrations
{
    const MIGRATIONS_FOLDER = __DIR__ . "/Migrations";

    public function migrate(Database $database)
    {
        $migrations = $this->verify($database);
        $map = [];
        foreach ($migrations as $migration) {
            $map[$migration["filename"]] = $migration["created_at"];
        }

        $files = scandir(self::MIGRATIONS_FOLDER);
        usort($files, function($name1, $name2) {
            return $name1 <=> $name2;
        });

        foreach ($files as $file) {
            if ($file === "." || $file === "..") {
                continue;
            }

            if (!isset($map[$file])) {
                $sql = file_get_contents(self::MIGRATIONS_FOLDER . "/" . $file);
                $database->raw($sql);
                $database->insert("migrations", ["filename" => $file, "created_at" => time()]);
            }
        }
    }


    private function verify(Database $database) : array
    {
        try{
            return $database->query("SELECT * FROM migrations ORDER BY created_at ASC", []);
        } catch (\PDOException $e) {}

        $sql = <<<EOF
CREATE TABLE migrations (
	filename TEXT NOT NULL,
	created_at INTEGER NOT NULL
);
EOF;
        $database->raw($sql);
        return $database->query("SELECT * FROM migrations ORDER BY created_at", []);
    }
}