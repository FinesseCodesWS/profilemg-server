<?php
require_once __DIR__ . '/../config/db.php';

class Personnel
{
    private $pdo;

    public function __construct()
    {
        $db = new Database();
        $this->pdo = $db->connect();
    }

    public function getAll()
    {
        $stmt = $this->pdo->query("SELECT * FROM personnel");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function findByPersonnelId($id)
    {
        $stmt = $this->pdo->prepare("SELECT * FROM personnel WHERE personnel_id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function createPersonnel($personnel_id, $first_name, $last_name, $division, $department, $status, $photo_url)
    {
        if (empty($first_name)) {
            http_response_code(400);
            echo json_encode(['error' => 'first_name is required']);
            exit;
        }
        $stmt = $this->pdo->prepare("INSERT INTO personnel (personnel_id, first_name, last_name, division, department, current_status, photo_url)
                                    VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([$personnel_id, $first_name, $last_name, $division, $department, $status, $photo_url]);
    }

    public function updatePersonnel($id, $first_name, $last_name, $division, $department, $status, $photo_url)
    {
        $stmt = $this->pdo->prepare("UPDATE personnel SET first_name = ?, last_name = ?, division = ?, department = ?, current_status = ?, photo_url = ? WHERE personnel_id = ?");
        $stmt->execute([$first_name, $last_name, $division, $department, $status, $photo_url, $id]);
        if ($photo_url) {
            $stmt->bindParam(':photo_url', $photo_url);
        }
    }
    public function deletePersonnel($personnel_id)
    {
        $sql = "DELETE FROM personnel WHERE personnel_id = :personnel_id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':personnel_id', $personnel_id);

        return $stmt->execute(); // true if delete was successful, false otherwise
    }
}
