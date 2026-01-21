<?php
abstract class BaseRepository
{
    protected PDO $db;
    protected string $table;

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    public function insert(array $data): bool
    {
        $columns = array_keys($data);
        $fields = implode(',', $columns);
        $values = ':' . implode(',:', $columns);
        $sql = "INSERT INTO {$this->table} ($fields) VALUES ($values)";
        $stmt = $this->db->prepare($sql);

        return $stmt->execute($data);
    }

    public function delete(int $id): bool
    {
        $stmt = $this->db->prepare("DELETE FROM {$this->table} WHERE id = :id");
        $stmt->bindParam(':id', $id);
        return $stmt->execute();
    }

    public function findAll(): array
    {
        $stmt = $this->db->query("SELECT * FROM {$this->table}");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function update(int $id, array $data): bool
    {
        $fields = [];

        foreach ($data as $column => $value) {
            $fields[] = "$column = :$column";
        }

        $sql = "UPDATE {$this->table} SET " . implode(', ', $fields) . " WHERE id = :id";

        $stmt = $this->db->prepare($sql);

        $data['id'] = $id;

        return $stmt->execute($data);
    }

    public function findById(int $id): ?array
    {
        $sql = "SELECT * FROM {$this->table} WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['id' => $id]);

        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        return $result ?: null;
    }



}

