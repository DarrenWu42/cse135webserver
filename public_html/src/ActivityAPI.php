<?php
namespace Src;

class ActivityAPI {
    private $db;
    private $requestMethod;
    private $activityId;

    public function __construct($db, $requestMethod, $activityId){
        $this->db = $db;
        $this->requestMethod = $requestMethod;
        $this->activityId = $activityId;
    }

    public function processRequest(){
        switch ($this->requestMethod) {
        case 'GET':
            if ($this->activityId)
                $response = $this->getActivity($this->activityId);
            else
                $response = $this->getAllActivities();
            break;
        case 'POST':
            $response = $this->createActivity();
            break;
        case 'PUT':
            $response = $this->updateActivity($this->activityId);
            break;
        case 'DELETE':
            $response = $this->deleteActivity($this->activityId);
            break;
        default:
            $response = $this->notFoundResponse();
            break;
        }
        header($response['status_code_header']);
        if ($response['body'])
            echo $response['body'];
    }

    private function getAllActivities(){
        $query = "
        SELECT 
            * 
        FROM 
            activity
        ";

        try {
            $statement = $this->db->query($query);
            $result = $statement->fetchAll(\PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            exit($e->getMessage());
        }

        $response['status_code_header'] = 'HTTP/1.1 200 OK';
        $response['body'] = json_encode($result);
        return $response;
    }

    private function getActivity($id){
        $result = $this->find($id);
        if (! $result) 
            return $this->notFoundResponse();

        $response['status_code_header'] = 'HTTP/1.1 200 OK';
        $response['body'] = json_encode($result);
        return $response;
    }

    private function createActivity(){
        $input = (array) json_decode(file_get_contents('php://input'), TRUE);

        $input_array = $this->createArray($input);

        if (!$this->validateInput($input_array))
            return $this->unprocessableEntityResponse();

        $query = "
        INSERT INTO activity
            (id, sess_id, activity_type, activity_info, alt_key, ctrl_key, shift_key, timestamp)
        VALUES
            (0, :sess_id, :activity_type, :activity_info, :alt_key, :ctrl_key, :shift_key, :timestamp);
        ";

        try {
            $this->executeSet($query, $input_array);
        } catch (\PDOException $e) {
            exit($e->getMessage());
        }

        $response['status_code_header'] = 'HTTP/1.1 201 Created';
        $response['body'] = json_encode(['message' => 'Activity Created']);
        return $response;
    }

    private function updateActivity($id){
        $result = $this->find($id);
        if (!$result)
            return $this->notFoundResponse();

        $input = (array) json_decode(file_get_contents('php://input'), TRUE);

        $input_array = $this->createArray($input);

        if (!$this->validateInput($input_array))
            return $this->unprocessableEntityResponse();
        
        $input_array = array('id' => $id) + $input_array;
        
        $query = "
        UPDATE activity
        SET
            sess_id = :sess_id,
            activity_type = :activity_type, 
            activity_info = :activity_info,
            alt_key = :alt_key,
            ctrl_key = :ctrl_key,
            shift_key = :shift_key,
            timestamp = :timestamp
        WHERE id = :id;
        ";

        try {
            $this->executeSet($query, $input_array);
        } catch (\PDOException $e) {
            exit($e->getMessage());
        }

        $response['status_code_header'] = 'HTTP/1.1 200 OK';
        $response['body'] = json_encode(['message' => 'Activity Updated!']);
        return $response;
    }

    private function deleteActivity($id){
        $result = $this->find($id);
        if (!$result)
            return $this->notFoundResponse();

        $query = "
        DELETE FROM activity
        WHERE id = :id;
        ";

        try {
            $statement = $this->db->prepare($query);
            $statement->execute(array('id' => $id));
            $statement->rowCount();
        } catch (\PDOException $e) {
            exit($e->getMessage());
        }

        $response['status_code_header'] = 'HTTP/1.1 200 OK';
        $response['body'] = json_encode(['message' => 'Activity Deleted!']);
        return $response;
    }

    public function find($id)
    {
        $query = "
        SELECT
            *
        FROM
            activity
        WHERE id = :id;
        ";

        try {
            $statement = $this->db->prepare($query);
            $statement->execute(array('id' => $id));
            $result = $statement->fetch(\PDO::FETCH_ASSOC);
            return $result;
        } catch (\PDOException $e) {
            exit($e->getMessage());
        }
    }

    private function createArray($input){
        $input_array = [
            'sess_id'       => $input['sess_id'],
            'activity_type' => $input['activityType'],
            'activity_info' => $input['activityInfo'],
            'alt_key'       => (int) $input['altKey'] ?? 0,
            'ctrl_key'      => (int) $input['ctrlKey'] ?? 0,
            'shift_key'     => (int) $input['shiftKey'] ?? 0,
            'timestamp'     => floatval($input['timestamp']) ?? null,
        ];
        return $input_array;
    }

    private function validateInput($input_array){
        $can_be_null = ['alt_key', 'ctrl_key', 'shift_key', 'timestamp'];
        foreach ($input_array as $value)
            if(!isset($value))
                return false;
        
        return true;
    }

    private function executeSet($query, $input_array){
        $statement = $this->db->prepare($query);
        $statement->execute($input_array);
        $statement->rowCount();
    }

    private function unprocessableEntityResponse(){
        $response['status_code_header'] = 'HTTP/1.1 422 Unprocessable Entity';
        $response['body'] = json_encode(['error' => 'Invalid input']);
        return $response;
    }

    private function notFoundResponse(){
        $response['status_code_header'] = 'HTTP/1.1 404 Not Found';
        $response['body'] = null;
        return $response;
    }
}