<?php

namespace aspfw\app\core\database;

use aspfw\app\core\Application;

class Database
{
    public \PDO $pdo;

    public function __construct(array $config)
    {   
        $dsn = $config['dsn'] ?? false;
        $user = $config['user'] ?? false;
        $password = $config['password'] ?? false;
        $this->pdo = new \PDO($dsn, $user, $password);
        $this->pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
    }

    public function applyMigrations(){
        $this->createMigrationsTable();
        $appliedMigrations = $this->getAppliedMigrations();

        $newMigrations = [];
        $files = scandir(Application::$ROOT_DIR.'/migrations');

        $toApply = array_diff($files, $appliedMigrations);

        foreach($toApply as $migration){
            if($migration === '.' || $migration === '..'){
                continue;
            }

            require_once Application::$ROOT_DIR.'/migrations/'.$migration;
            $fileName = pathinfo($migration, PATHINFO_FILENAME);
            $instance = new $fileName();
            $this->log('Applying migration '. $migration);
            $instance->up();
            $this->log('Migration '. $migration . ' applied');
            $newMigrations[] = $migration;
        }

        if(!empty($newMigrations)){
            $this->saveMigrations($newMigrations);
        } else{
            $this->log('All migrations applied');
        }
    }

    public function createMigrationsTable(){
        $this->pdo->exec(
            "CREATE TABLE IF NOT EXISTS migrations (
                id INT AUTO_INCREMENT PRIMARY KEY,
                migration VARCHAR(255),
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            );"
        );
    }

    public function getAppliedMigrations(){
        $statement = $this->pdo->prepare("SELECT migration FROM migrations");
        $statement->execute();

        return $statement->fetchAll(\PDO::FETCH_COLUMN);
    }

    public function saveMigrations(array $migrations){
        $str = implode(",", array_map(fn($m) => "('$m')", $migrations));
        var_dump($str);
        $statement = $this->pdo->prepare("INSERT INTO migrations (migration) VALUES 
            $str
        ");

        $statement->execute();
    }

    public function prepare($sql){
        return $this->pdo->prepare($sql);
    }

    protected function log($message){
        echo '[',date('Y-m-d H:i:s').'] - '.$message.PHP_EOL;
    }
}
