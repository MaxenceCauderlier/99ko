<?php

/**
 * @copyright (C) 2024, 299Ko
 * @license https://www.gnu.org/licenses/gpl-3.0.en.html GPLv3
 * @author Maxence Cauderlier <mx.koder@gmail.com>
 * 
 * @package 299Ko https://github.com/299Ko/299ko
 */
defined('ROOT') or exit('Access denied!');

/**
 * UsersManager provides methods for user management and authentication.
 * 
 * It handles login/logout, retrieving user objects, password encryption, 
 * auth tokens, and persistence of user data.
 */
class UsersManager
{

    /**
     * Path file where users are stored
     */
    protected static $file = DATA_PLUGIN . 'users/users.json';

    /**
     * Logs in a user with the provided email and password.
     * 
     * @param string $email The user's email address
     * @param string $password The user's password
     * @param bool $useCookies Whether to set auth cookies after successful login
     * @return bool True if login succeeded, false otherwise
     */
    public static function login(string $mail, string $password, bool $useCookies = false): bool
    {
        $user = self::getUser($mail);
        if ($user === false) {
            // User dont exist
            return false;
        }
        if ($user->pwd !== self::encrypt($password)) {
            // Incorrect mail & pwd
            return false;
        }
        $user->token = self::generateToken();
        $user->save();
        self::logon($user);
        if ($useCookies) {
            self::setRememberCookies($user);
        }
        return true;
    }

    /**
     * Checks if the user is logged in using auth cookies.
     *
     * @return bool True if user is logged in via cookies, false otherwise.
     */
    protected static function loginByCookies(): bool
    {
        $parts = explode('//', $_COOKIE['koAutoConnect']);
        $mail = $parts[0] ?? '';
        $cryptedPwd = $parts[1] ?? '';

        $user = self::getUser($mail);
        if ($user === false) {
            // User dont exist
            setcookie('koAutoConnect', '/', 1, '/');
            return false;
        }
        if ($user->pwd !== $cryptedPwd) {
            // Incorrect mail & pwd
            setcookie('koAutoConnect', '/', 1, '/');
            return false;
        }
        $user->token = self::generateToken();
        $user->save();
        self::logon($user);
        return true;
    }

    /**
     * Checks if the user is currently logged in.
     *
     * @return bool True if user is logged in, false otherwise.
     */
    public static function isLogged(): bool
    {
        if (self::getCurrentUser() === null) {
            // Try to connect by cookies
            if (isset($_COOKIE['koAutoConnect']) && is_string($_COOKIE['koAutoConnect'])) {
                return self::loginByCookies();
            }
            return false;
        }
        return true;
    }

    /**
     * Logs in the given user.
     *
     * @param User $user The user to log in.
     */
    protected static function logon(User $user):void
    {
        $_SESSION['email'] = $user->email;
        $_SESSION['token'] = $user->token;
    }

    /**
     * Sets remember me cookies for the given user.
     */
    protected static function setRememberCookies(User $user)
    {
        setcookie(
            'koAutoConnect',
            $user->email . '//' . $user->pwd,
            [
                'expires' => time() + 60 * 24 * 3600,
                'secure' => true,
                'httponly' => true,
                'path' => '/'
            ]
        );
    }

    /**
     * Get an user from his mail
     * @return User|false
     */
    public static function getUser(string $mail)
    {
        $users = self::getUsers();
        foreach ($users as $user) {
            if ($user->email == $mail) {
                return $user;
            }
        }
        return false;
    }

    /**
     * Get a user by their ID.
     *
     * @param int $id The ID of the user to retrieve.
     * @return User|false The User object if found, false if not found.
     */
    public static function getUserById(int $id): ?User
    {
        $users = self::getUsers();
        foreach ($users as $user) {
            if ($user->id == $id) {
                return $user;
            }
        }
        return null;
    }

    /**
     * Return the current User, if connected by session
     * @return User|false User or false if not connected
     */
    public static function getCurrentUser(): ?User
    {
        if (!isset($_SESSION['email'])) {
            return null;
        }
        $user = self::getUser($_SESSION['email']);
        if ($user !== false) {
            if ($_SESSION['token'] === $user->token) {
                return $user;
            }
        }
        return null;
    }

    /**
     * Saves a User object to persistent storage.
     */
    public static function saveUser(User $user):bool
    {
        $users = self::getUsers();
        $users[$user->id] = $user;
        return self::saveUsers($users);
    }

    /**
     * Deletes a user from persistent storage.
     *
     * @param User $user The user object to delete.
     * @return bool True if the user was deleted, false otherwise.
     */
    public static function deleteUser(User $user): bool
    {
        $users = self::getUsers();
        unset($users[$user->id]);
        return self::saveUsers($users);
    }

    /**
     * Saves the given array of User objects to persistent storage.
     */
    protected static function saveUsers(array $users)
    {
        return util::writeJsonFile(self::$file, $users);
    }

    /**
     * Returns all User objects.
     */
    public static function getUsers(): array
    {
        $userSource = util::readJsonFile(self::$file);
        if ($userSource === false) {
            return [];
        }
        $users = [];
        foreach ($userSource as $rawUser) {
            $users[$rawUser['id']] = new User($rawUser);
        }
        return $users;
    }

    /**
     * Encrypts the given data string.
     *
     * @param string $data The data to encrypt
     * @return string The encrypted data
     */
    public static function encrypt(string $data): string
    {
        return hash_hmac('sha1', $data, KEY);
    }

    /**
     * Generates a random token string to be used for authentication.
     *
     * @return string A random token string.
     */
    public static function generateToken(): string
    {
        return sha1(uniqid(mt_rand(), true));
    }

    /**
     * Gets the next available user ID.
     *
     * @return string The next available user ID.
     */
    public static function getNextId(): int
    {
        if (empty(self::getUsers())) {
            return 1;
        }
        return max(array_keys(self::getUsers())) + 1;
    }
}