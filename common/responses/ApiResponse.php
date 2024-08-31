<?php

/**
 * @copyright (C) 2024, 299Ko
 * @license https://www.gnu.org/licenses/gpl-3.0.en.html GPLv3
 * @author Maxence Cauderlier <mx.koder@gmail.com>
 * 
 * @package 299Ko https://github.com/299Ko/299ko
 */
defined('ROOT') or exit('Access denied!');

class ApiResponse extends Response
{

    const STATUS_SUCCESS = 'HTTP/1.1 200 OK';
    const STATUS_CREATED = 'HTTP/1.1 201 Created';
    const STATUS_ACCEPTED = 'HTTP/1.1 202 Accepted';
    const STATUS_NO_CONTENT = 'HTTP/1.1 204 No Content';
    const STATUS_BAD_REQUEST = 'HTTP/1.1 400 Bad Request';
    const STATUS_NOT_AUTHORIZED = 'HTTP/1.1 401 Not Authorized';
    const STATUS_FORBIDDEN = 'HTTP/1.1 403 Forbidden';
    const STATUS_NOT_FOUND = 'HTTP/1.1 404 Not Found';

    public string $status = self::STATUS_NOT_FOUND;
    public ?string $body = null;

    protected $headers = [
        "Access-Control-Allow-Origin: *",
        "Content-Type: application/json; charset=UTF-8",
        "Cache-Control: no-cache",
        "Pragma: no-cache"
    ];

    public function output():string {
        foreach($this->headers as $header){
			header($header,true);
		};
        header($this->status, true);
        if (empty($this->body)) {
            die();
        }
        return json_encode($this->body);
    }

}