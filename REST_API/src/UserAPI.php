<?php
namespace Src;

class UserAPI {
    private $db;
    private $requestMethod;
    private $username;

    public function __construct($db, $requestMethod, $username){
        $this->db = $db;
        $this->requestMethod = $requestMethod;
        $this->username = $username;
    }

    public function processRequest(){
        switch ($this->requestMethod) {
        case 'GET':
            if ($this->username)
                $response = $this->getUser($this->username);
            else
                $response = $this->getAllUsers();
            break;
        case 'POST':
            $response = $this->createUser();
            break;
        case 'PUT':
            $response = $this->updateUser($this->username);
            break;
        case 'PATCH':
            $response = $this->updateUserCell($this->username);
            break;
        case 'DELETE':
            $response = $this->deleteUser($this->username);
            break;
        default:
            $response = $this->notFoundResponse();
            break;
        }
        header($response['status_code_header']);
        if ($response['body'])
            echo $response['body'];
    }

    private function getAllUsers(){
        $query = "
        SELECT 
            * 
        FROM 
            dwu_users
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

    private function getUser($username){
        $result = $this->find($username);
        if (!$result) 
            return $this->notFoundResponse();

        $response['status_code_header'] = 'HTTP/1.1 200 OK';
        $response['body'] = json_encode($result);
        return $response;
    }

    private function createUser(){
        $input = (array) json_decode(file_get_contents('php://input'), TRUE);

        $input_array = $this->createArray($input);

        if (!$this->validateInput($input_array))
            return $this->unprocessableEntityResponse();

        $query = "
        INSERT INTO dwu_users
            (username, password, group)
        VALUES
            (:username, :password, :group);
        ";

        try {
            $this->executeSet($query, $input_array);
        } catch (\PDOException $e) {
            exit($e->getMessage());
        }

        $response['status_code_header'] = 'HTTP/1.1 201 Created';
        $response['body'] = json_encode($input_array);
        return $response;
    }

    private function updateUser($username){
        $result = $this->find($username);
        if (!$result)
            return $this->notFoundResponse();

        $input = (array) json_decode(file_get_contents('php://input'), TRUE);

        $input_array = $this->createArray($input);

        if (!$this->validateInput($input_array))
            return $this->unprocessableEntityResponse();
        
        $query = "
        UPDATE dwu_users
        SET
            username = :username,
            password = :password,
            group    = :group
        WHERE username = :username;
        ";

        try {
            $this->executeSet($query, $input_array);
        } catch (\PDOException $e) {
            exit($e->getMessage());
        }

        $response['status_code_header'] = 'HTTP/1.1 200 OK';
        return $response;
    }

    private function updateUserCell($username){
        $result = $this->find($username);
        if (!$result)
            return $this->notFoundResponse();

        $input = (array) json_decode(file_get_contents('php://input'), TRUE);

        $input_array = $this->createArray($input);

        foreach ($input_array as $key => $value)
            $input_array[$key] = $value == null ? $result[$key] : $value;
        
        $query = "
        UPDATE dwu_users
        SET
            :username = :username,
            :password = :password,
            :group    = :group
        WHERE username = :username;
        ";

        try {
            $this->executeSet($query, $input_array);
        } catch (\PDOException $e) {
            exit($e->getMessage());
        }

        $response['status_code_header'] = 'HTTP/1.1 200 OK';
        return $response;
    }

    private function deleteUser($username){
        $result = $this->find($username);
        if (!$result)
            return $this->notFoundResponse();

        $query = "
        DELETE FROM dwu_users
        WHERE username = :username;
        ";

        try {
            $statement = $this->db->prepare($query);
            $statement->execute(array('username' => $username));
            $statement->rowCount();
        } catch (\PDOException $e) {
            exit($e->getMessage());
        }

        $response['status_code_header'] = 'HTTP/1.1 200 OK';
        return $response;
    }

    public function find($username)
    {
        $query = "
        SELECT
            *
        FROM
            dwu_users
        WHERE username = :username;
        ";

        try {
            $statement = $this->db->prepare($query);
            $statement->execute(['username' => $username]);
            $result = $statement->fetch(\PDO::FETCH_ASSOC);
            return $result;
        } catch (\PDOException $e) {
            exit($e->getMessage());
        }
    }

    private function encode_pw($password){
        return isset($password) ? '{SHA}' . base64_encode(sha1($password, TRUE)) : null;
    }

    private function createArray($input){
        $input_array = [
            'username' => $input['username'] ?? null,
            'password' => $this->encode_pw($input['password'] ?? null),
            'group'    => $input['group'] ?? null
        ];
        return $input_array;
    }

    private function validateInput($input_array){
        $can_be_null = [];
        foreach ($input_array as $key => $value)
            if(!in_array($key, $can_be_null) && !isset($value))
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