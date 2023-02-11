<?php
require_once 'config.php';

class Database {
    protected $connection = null;

    public function __construct() {
        $this->connection = new PDO('mysql:host=' . DB_HOST . ';dbname=' . DB_NAME, DB_USERNAME, DB_PASSWORD, array(PDO::ATTR_PERSISTENT => true));
    }

    private function expand_values($beginning, $values, $separator, $ending) {
        return $beginning . implode($separator, $values) . $ending;
    }

    private static function column_value_mapper(string $column, mixed $value) {
        $value_string = (gettype($value) == 'string') ? "'$value'" : $value;
        return "$column = $value_string";
    }

    private function generate_column_value_string(array $columns, array $values) {
        return implode(',', array_map('Database::column_value_mapper', $columns, $values));
    }

    public function select(array $columns, string $from, string $additional_params=''): array|bool {
        $stmt = $this->connection->prepare("SELECT {$this->expand_values('', $columns, ',', '')} FROM $from $additional_params;");
        if (!$stmt->execute()) {
            return false;
        }

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function insert(string $into, array $columns, array $values, string $additional_params=''): bool {
        $stmt = $this->connection->prepare("INSERT INTO $into {$this->expand_values('(', $columns, ',', ')')} VALUES {$this->expand_values('(', $values, ',', ')')} $additional_params;");
        return $stmt->execute();
    }

    public function update(string $table, array $columns, array $values, string $additional_params=''): bool {
        $stmt = $this->connection->prepare("UPDATE $table SET {$this->generate_column_value_string($columns, $values)} $additional_params;");
        return $stmt->execute();
    }

    public function delete(string $table, string $condition, $additional_params=''): bool {
        $stmt = $this->connection->prepare("DELETE FROM $table WHERE $condition $additional_params;");
        return $stmt->execute();
    }
}