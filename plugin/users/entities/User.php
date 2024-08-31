<?php

/**
 * @copyright (C) 2024, 299Ko
 * @license https://www.gnu.org/licenses/gpl-3.0.en.html GPLv3
 * @author Maxence Cauderlier <mx.koder@gmail.com>
 * 
 * @package 299Ko https://github.com/299Ko/299ko
 */
defined('ROOT') or exit('Access denied!');

class User implements JsonSerializable
{

    /**
     * User id property.
     */
    public int $id;

    public string $email;

    /**
     * User password
     */
    public string $pwd;

    /**
     * User's authentication token.
     */
    public string $token;

    /**
     * Delete link for the user. 
     */
    public string $deleteLink;

    /**
     * Construct a new User instance.
     *
     * @param array $infos User data including id, email, password hash, and token.
     */
    public function __construct($infos = [])
    {
        if (!empty($infos)) {
            $this->id = $infos['id'];
            $this->email = $infos['email'];
            $this->pwd = $infos['pwd'];
            $this->token = $infos['token'];
        }
    }

    /**
     * Converts the User object into an array for JSON serialization. 
     * Returns an array containing the id, email, password hash, and auth token for the user.
     */
    public function jsonSerialize(): array
    {
        return [
            'id' => $this->id,
            'email' => $this->email,
            'pwd' => $this->pwd,
            'token' => $this->token
        ];
    }

    /**
     * Saves the user to the database.
     * Generates an ID and auth token for the user if they don't already exist.
     * Delegates to the UsersManager to handle the actual saving.
     */
    public function save()
    {
        $this->id = $this->id ?? UsersManager::getNextId();
        $this->token = $this->token ?? UsersManager::generateToken();
        return UsersManager::saveUser($this);
    }

    /**
     * Checks if the user is authorized based on the request token matching the user's token.
     * 
     * Checks if a 'token' parameter is present in the request (URL params or POST data).
     * If so, returns true if it matches the user's token, false otherwise.
     * 
     * Also checks the $_REQUEST global for a 'token' value and compares it to the user's token.
     * 
     * Returns false if no token is present. 
     */
    public function isAuthorized(): bool
    {
        $matches = router::getInstance()->match();
        if (isset($matches['params']['token'])) {
            if ($matches['params']['token'] === $this->token) {
                return true;
            }
            return false;
        }
        $contentType = isset($_SERVER["CONTENT_TYPE"]) ? trim($_SERVER["CONTENT_TYPE"]) : '';
        if ($contentType === "application/json") {
            $content = trim(file_get_contents("php://input"));
            $data = json_decode($content, true);
            if (isset($data['token'])) {
                if ($data['token'] === $this->token) {
                    return true;
                }
                return false;
            }
        }
        if (!isset($_REQUEST['token']))
            return false;
        if ($_REQUEST['token'] != $this->token)
            return false;
        return true;
    }

    /**
     * Deletes the user from the database.
     * Delegates to the UsersManager to handle the actual deletion.
     * 
     * @return bool True if the user was deleted successfully, false otherwise.
     */
    public function delete(): bool
    {
        return UsersManager::deleteUser($this);
    }
}