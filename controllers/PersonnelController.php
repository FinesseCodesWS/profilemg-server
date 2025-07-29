<?php
require_once '../models/Personnel.php';

class PersonnelController
{
    private $personnelModel;

    public function __construct()
    {
        $this->personnelModel = new Personnel();
    }

    public function index()
    {
        $data = $this->personnelModel->getAll();
        echo json_encode($data);
    }

    public function create()
    {
        // Get POST data
        $first_name = $_POST['first_name'] ?? null;
        $last_name = $_POST['last_name'] ?? null;
        $division = $_POST['division'] ?? null;
        $department = $_POST['department'] ?? null;
        $status = $_POST['current_status'] ?? null;
        $personnel_id = uniqid('UN');
        $photo_url = null;

        // Handle image upload if any
        if (isset($_FILES['photo']) && $_FILES['photo']['error'] == 0) {
            $target_dir = "../uploads/";
            $file_name = basename($_FILES["photo"]["name"]);
            $target_file = $target_dir . time() . "_" . $file_name;
            if (move_uploaded_file($_FILES["photo"]["tmp_name"], $target_file)) {
                $photo_url = str_replace("../", "", $target_file);
            }
        }

        // Insert data into the database
        $this->personnelModel->createPersonnel($personnel_id, $first_name, $last_name, $division, $department, $status, $photo_url);

        // Return the response
        echo json_encode(['status' => 'created', 'personnel_id' => $personnel_id]);
    }

    public function update($id)
    {
        // Get POST data from JSON
        $data = json_decode(file_get_contents("php://input"), true);

        // Check if required fields are present
        if (!isset($data['first_name'], $data['last_name'], $data['division'], $data['department'], $data['current_status'])) {
            echo json_encode(['error' => 'Missing required fields']);
            return;
        }

        $first_name = $data['first_name'];
        $last_name = $data['last_name'];
        $division = $data['division'];
        $department = $data['department'];
        $status = $data['current_status'];

        // Handle image upload if any
        $photo_url = null;
        if (isset($_FILES['photo']) && $_FILES['photo']['error'] == 0) {
            $target_dir = "../uploads/";
            $file_name = basename($_FILES["photo"]["name"]);
            $target_file = $target_dir . time() . "_" . $file_name;
            if (move_uploaded_file($_FILES["photo"]["tmp_name"], $target_file)) {
                $photo_url = str_replace("../", "", $target_file);
            }
        }

        // Call the model to update the record
        $this->personnelModel->updatePersonnel($id, $first_name, $last_name, $division, $department, $status, $photo_url);

        // Return the response
        echo json_encode(['status' => 'updated']);
    }

    public function delete($personnel_id)
    {
        // Call the model method to delete the personnel based on the personnel_id
        $result = $this->personnelModel->deletePersonnel($personnel_id);

        if ($result) {
            echo json_encode(['status' => 'deleted']);
        } else {
            echo json_encode(['error' => 'Personnel not found']);
        }
    }

    public function show($personnel_id)
    {
        $data = $this->personnelModel->findByPersonnelId($personnel_id);
        if ($data) {
            echo json_encode($data);
        } else {
            http_response_code(404);
            echo json_encode(['error' => 'Personnel not found']);
        }
    }
}
