<?php
namespace Src;

class StaticAPI {
    private $db;
    private $requestMethod;
    private $staticId;

    public function __construct($db, $requestMethod, $staticId){
        $this->db = $db;
        $this->requestMethod = $requestMethod;
        $this->staticId = $staticId;
    }

    public function processRequest(){
        switch ($this->requestMethod) {
        case 'GET':
            if ($this->staticId)
                $response = $this->getStatic($this->staticId);
            else
                $response = $this->getAllStatics();
            break;
        case 'POST':
            $response = $this->createStatic();
            break;
        case 'PUT':
            $response = $this->updateStatic($this->staticId);
            break;
        case 'DELETE':
            $response = $this->deleteStatic($this->StaticId);
            break;
        default:
            $response = $this->notFoundResponse();
            break;
        }
        header($response['status_code_header']);
        if ($response['body'])
            echo $response['body'];
    }

    private function getAllStatics(){
        $query = "
        SELECT 
            * 
        FROM 
            static
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

    private function getStatic($sess_id){
        $result = $this->find($sess_id);
        if (! $result) 
            return $this->notFoundResponse();

        $response['status_code_header'] = 'HTTP/1.1 200 OK';
        $response['body'] = json_encode($result);
        return $response;
    }

    private function createStatic(){
        $input = (array) json_decode(file_get_contents('php://input'), TRUE);

        $input_array = $this->createArray($input);

        if (!$this->validateInput($input_array))
            return $this->unprocessableEntityResponse();

        $query = "
        INSERT INTO static
            (sess_id, user_agent, language, cookies, inner_width, inner_height, outer_width, outer_height, downlink, effective_type, rtt, save_data)
        VALUES
            (:sess_id, :user_agent, :language, :cookies, :inner_width, :inner_height, :outer_width, :outer_height, :downlink, :effective_type, :rtt, :save_data);
        ";

        try {
            $this->executeSet($query, $input_array);
        } catch (\PDOException $e) {
            exit($e->getMessage());
        }

        $response['status_code_header'] = 'HTTP/1.1 201 Created';
        $response['body'] = json_encode['message' => 'Post Created']);
        return $response;
    }

    private function updateStatic($sess_id){
        $result = $this->find($sess_id);
        if (!$result)
            return $this->notFoundResponse();

        $input = (array) json_decode(file_get_contents('php://input'), TRUE);

        $input_array = $this->createArray($input);

        if (!$this->validateInput($input_array))
            return $this->unprocessableEntityResponse();
        
        $query = "
        UPDATE static
        SET
            user_agent = :user_agent,
            language = :language,
            cookies = :cookies,
            inner_width = :inner_width,
            inner_height = :inner_height,
            outer_width = :outer_width,
            outer_height = :outer_height,
            downlink = :downlink,
            effective_type = :effective_type,
            rtt = :rtt,
            save_data = :save_data
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

    private function deleteStatic($sess_id){
        $result = $this->find($sess_id);
        if (!$result)
            return $this->notFoundResponse();

        $query = "
        DELETE FROM static
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

    public function find($id)
    {
        $query = "
        SELECT
            *
        FROM
            static
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
            'sess_id'        => $input['sess_id'],
            'user_agent'     => $input['userAgent'],
            'language'       => $input['language'],
            'cookies'        => (int) $input['acceptsCookies'],
            'inner_width'    => (int) $input['screenDimensions']['inner']['innerWidth'],
            'inner_height'   => (int) $input['screenDimensions']['inner']['innerHeight'],
            'outer_width'    => (int) $input['screenDimensions']['outer']['outerWidth'],
            'outer_height'   => (int) $input['screenDimensions']['outer']['outerWidth'],
            'downlink'       => floatval($input['connection']['downlink']),
            'effective_type' => $input['connection']['effectiveType'],
            'rtt'            => (int) $input['connection']['rtt'],
            'save_data'      => (int) $input['connection']['saveData']
        ];
        return $input_array;
    }

    private function validateInput($input_array){
        foreach ($input_array as $value)
            if(!isset($value))
                return false;
        
        return true;
    }

    private function executeSet($query, $input){
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