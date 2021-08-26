<?php
namespace Src;

class StaticAPI {
    private $db;
    private $requestMethod;
    private $performanceId;

    public function __construct($db, $requestMethod, $performanceId){
        $this->db = $db;
        $this->requestMethod = $requestMethod;
        $this->performanceId = $performanceId;
    }

    public function processRequest(){
        switch ($this->requestMethod) {
        case 'GET':
            if ($this->performanceId)
                $response = $this->getPerformance($this->performanceId);
            else
                $response = $this->getAllPerformances();
            break;
        case 'POST':
            $response = $this->createPerformance();
            break;
        case 'PUT':
            $response = $this->updatePerformance($this->performanceId);
            break;
        case 'DELETE':
            $response = $this->deletePerformance($this->performanceId);
            break;
        default:
            $response = $this->notFoundResponse();
            break;
        }
        header($response['status_code_header']);
        if ($response['body'])
            echo $response['body'];
    }

    private function getAllPerformances(){
        $query = "
        SELECT 
            * 
        FROM 
            performance
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

    private function getPerformance($sess_id){
        $result = $this->find($sess_id);
        if (! $result) 
            return $this->notFoundResponse();

        $response['status_code_header'] = 'HTTP/1.1 200 OK';
        $response['body'] = json_encode($result);
        return $response;
    }

    private function createPerformance(){
        $input = (array) json_decode(file_get_contents('php://input'), TRUE);

        $input_array = $this->createArray($input);

        if (!$this->validateInput($input_array))
            return $this->unprocessableEntityResponse();

        $query = "
        INSERT INTO performance
            (sess_id, start_time, fetch_start, request_start, response_start, response_end, dom_interactive, dom_loaded_start, dom_loaded_end, dom_complete, load_event_start, load_event_end, duration, transfer_size, decoded_body_size)
        VALUES
            (:sess_id, :start_time, :fetch_start, :request_start, :response_start, :response_end, :dom_interactive, :dom_loaded_start, :dom_loaded_end, :dom_complete, :load_event_start, :load_event_end, :duration, :transfer_size, :decoded_body_size);
        ";

        try {
            $this->executeSet($query, $input_array);
        } catch (\PDOException $e) {
            exit($e->getMessage());
        }

        $response['status_code_header'] = 'HTTP/1.1 201 Created';
        $response['body'] = json_encode(['message' => 'Post Created']);
        return $response;
    }

    private function updatePerformance($sess_id){
        $result = $this->find($sess_id);
        if (!$result)
            return $this->notFoundResponse();

        $input = (array) json_decode(file_get_contents('php://input'), TRUE);

        $input_array = $this->createArray($input);

        if (!$this->validateInput($input_array))
            return $this->unprocessableEntityResponse();

        $query = "
        UPDATE performance
        SET
            start_time = :start_time
            fetch_start = :fetch_start
            request_start = :request_start
            response_start = :response_start
            response_end = :response_end
            dom_interactive = :dom_interactive
            dom_loaded_start = :dom_loaded_start
            dom_loaded_end = :dom_loaded_end
            dom_complete = :dom_complete
            load_event_start = :load_event_start
            load_event_end = :load_event_end
            duration = :duration
            transfer_size = :transfer_size
            decoded_body_size = :decoded_body_size
        WHERE sess_id = :sess_id;
        ";

        try {
            $this->executeSet($query, $input_array);
        } catch (\PDOException $e) {
            exit($e->getMessage());
        }

        $response['status_code_header'] = 'HTTP/1.1 200 OK';
        $response['body'] = json_encode(['message' => 'Post Updated!']);
        return $response;
    }

    private function deletePerformance($sess_id)
    {
        $result = $this->find($sess_id);
        if (!$result)
            return $this->notFoundResponse();

        $query = "
        DELETE FROM performance
        WHERE sess_id = :sess_id;
        ";

        try {
            $statement = $this->db->prepare($query);
            $statement->execute(array('sess_id' => $sess_id));
            $statement->rowCount();
        } catch (\PDOException $e) {
            exit($e->getMessage());
        }

        $response['status_code_header'] = 'HTTP/1.1 200 OK';
        $response['body'] = json_encode(['message' => 'Post Deleted!']);
        return $response;
    }

    public function find($sess_id)
    {
        $query = "
        SELECT
            *
        FROM
            performance
        WHERE sess_id = :sess_id;
        ";

        try {
            $statement = $this->db->prepare($query);
            $statement->execute(array('sess_id' => $sess_id));
            $result = $statement->fetch(\PDO::FETCH_ASSOC);
            return $result;
        } catch (\PDOException $e) {
            exit($e->getMessage());
        }
    }

    private function createArray($input){
        $input_array = [
            'sess_id'           => $input['sess_id'],
            'start_time'        => $input['startTime'],
            'fetch_start'       => $input['fetchStart'],
            'request_start'     => $input['requestStart'],
            'response_start'    => $input['responseStart'],
            'response_end'      => $input['responseEnd'],
            'dom_interactive'   => $input['domInteractive'],
            'dom_loaded_start'  => $input['domContentLoadedEventStart'],
            'dom_loaded_end'    => $input['domContentLoadedEventEnd'],
            'dom_complete'      => $input['domComplete'],
            'load_event_start'  => $input['loadEventStart'],
            'load_event_end'    => $input['loadEventEnd'],
            'duration'          => $input['duration'],
            'transfer_size'     => $input['transferSize'],
            'decoded_body_size' => $input['decodedBodySize']
        ];
        return $input_array;
    }

    private function validateInput($input_array){
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