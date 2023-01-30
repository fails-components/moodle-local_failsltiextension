<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * @package   local_failsltiextension
 * @copyright 2023 Daniil Fajnberg, innoCampus, TU Berlin
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_failsltiextension;

use Firebase\JWT\JWT;
use mod_lti\local\ltiopenid\jwks_helper;


class api_request_exception extends \moodle_exception {
    public $statuscode;

    public function __construct($statuscode) {
        parent::__construct('api_request_exception', 'local_failsltiextension', '', $statuscode);
        $this->statuscode = $statuscode;
    }
}


/**
 * Class api.
 *
 * Serves as a wrapper for FAILS API.
 *
 * @package local_failsltiextension
 */
class api {

    /** @var string */
    private $base_url;


    /**
     * Removes trailing slashes from the base URL.
     *
     * @param string $base_url
     */
    public function __construct(string $base_url) {
        $this->base_url = preg_replace('|/+$|i', '', $base_url);
    }


    /**
     * Generates a JWT signed with the `mod_lti` private key.
     *
     * @param array $payload additional JWT payload (by default `iss` and `exp` are set)
     * @param string $algorithm algorithm to use for encoding the token
     * @return string the encoded JWT as a string
     */
    public function get_jwt(array $payload = [], string $algorithm = 'RS256') : string {
        global $CFG;
        $payload['iss'] = $payload['iss'] ?? $CFG->wwwroot;
        $payload['exp'] = $payload['exp'] ?? time() + 60;
        list('key'=>$key, 'kid'=>$kid) = jwks_helper::get_private_key();
        $payload['kid'] = $kid;
        return JWT::encode($payload, $key, $algorithm, $kid);
    }

    /**
     * Performs a generic API request.
     *
     * @param string $method HTTP method (e.g. "GET" or "DELETE")
     * @param string $path URL path to append to the base URL
     * @param bool $auth whether to add a JWT for authentication to the headers
     * @param array $payload request body (to be encoded as JSON)
     * @param array $headers additional HTTP headers to include
     * @return string
     * @throws api_request_exception with the HTTP status code if it is not 200
     */
    public function request(
        string $method,
        string $path,
        bool $auth = true,
        array $payload = [],
        array $headers = []
    ) {
        $url = $this->base_url . $path;
        if ($auth) {
            $headers[] = "Authorization: Bearer " . $this->get_jwt();
        }
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
        curl_setopt($ch, CURLOPT_URL, $url);
        if (!empty($payload)) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
            $headers[] = "Content-Type: application/json";
        }
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $output = curl_exec($ch);
        $status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        if ($status != 200) {
            throw new api_request_exception($status);
        }
        curl_close($ch);
        return $output;
    }

    /**
     * Convenience method for a GET request.
     *
     * @param string $path URL path to append to the base URL
     * @param bool $auth whether to add a JWT for authentication to the headers
     * @param array $payload request body (to be encoded as JSON)
     * @param array $headers additional HTTP headers to include
     * @return string
     * @throws api_request_exception with the HTTP status code if it is not 200
     */
    public function get(
        string $path,
        bool $auth = true,
        array $payload = [],
        array $headers = []
    ) {
        return $this->request('GET', $path, $auth, $payload, $headers);
    }

    /**
     * Convenience method for a DELETE request.
     *
     * @param string $path URL path to append to the base URL
     * @param bool $auth whether to add a JWT for authentication to the headers
     * @param array $payload request body (to be encoded as JSON)
     * @param array $headers additional HTTP headers to include
     * @return string
     * @throws api_request_exception with the HTTP status code if it is not 200
     */
    public function delete(
        string $path,
        bool $auth = true,
        array $payload = [],
        array $headers = []
    ) {
        return $this->request('DELETE', $path, $auth, $payload, $headers);
    }

    /**
     * Convenience method for a PATCH request.
     *
     * @param string $path URL path to append to the base URL
     * @param bool $auth whether to add a JWT for authentication to the headers
     * @param array $payload request body (to be encoded as JSON)
     * @param array $headers additional HTTP headers to include
     * @return string
     * @throws api_request_exception with the HTTP status code if it is not 200
     */
    public function patch(
        string $path,
        bool $auth = true,
        array $payload = [],
        array $headers = []
    ) {
        return $this->request('PATCH', $path, $auth, $payload, $headers);
    }

    /**
     * Requests a user's UUID from the FAILS backend.
     *
     * @param ?string $username the name of the user for which to get the UUID
     * @param ?string $email the email address of the user for which to get the UUID
     * @return array with one key `uuid`
     * @throws api_request_exception with the HTTP status code if it is not 200
     */
    public function get_user_uuid(?string $username = null, ?string $email = null) : array {
        $path = '/lti/maintenance/user/';
        $payload = [];
        if (!is_null($username)) {
            $payload['username'] = $username;
        }
        if (!is_null($email)) {
            $payload['email'] = $email;
        }
        return json_decode($this->get($path, true, $payload), true);
    }

    /**
     * Requests deletion of a user at the FAILS backend.
     *
     * @param string $uuid the UUID (on the backend) of the user to be deleted
     * @return array with the keys `deletedusers` and `modifieddocs`
     * @throws api_request_exception with the HTTP status code if it is not 200
     */
    public function delete_user(string $uuid) : array {
        $path = '/lti/maintenance/user/';
        $payload = ['uuid' => $uuid];
        return json_decode($this->delete($path, true, $payload), true);
    }

    /**
     * Requests deletion of a course at the FAILS backend.
     *
     * @param int $courseid the id of the course to be deleted
     * @return array with the key `modifieddocs`
     * @throws api_request_exception with the HTTP status code if it is not 200
     */
    public function delete_course(int $courseid) : array {
        $path = '/lti/maintenance/course/';
        $payload = ['courseid' => $courseid];
        return json_decode($this->delete($path, true, $payload), true);
    }

    /**
     * Requests restoration of a course at the FAILS backend.
     *
     * @param int $courseid the id of the course to be restored
     * @return array with the key `modifieddocs`
     * @throws api_request_exception with the HTTP status code if it is not 200
     */
    public function restore_course(int $courseid) : array {
        $path = '/lti/maintenance/course/';
        $payload = ['courseid' => $courseid];
        return json_decode($this->patch($path, true, $payload), true);
    }
}
