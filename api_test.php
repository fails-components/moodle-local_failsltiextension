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


require_once(dirname(__FILE__) . '/../../config.php');
use local_failsltiextension\api;
use local_failsltiextension\api_request_exception;


// Check permissions.
require_login();
$context = context_system::instance();
require_capability('moodle/site:config', $context);

// Set up page.
$PAGE->set_context($context);
$PAGE->set_url('/local/failsltiextension/api_test.php');
$title = get_string('apitest:title', 'local_failsltiextension');
$PAGE->set_title($title);

// Start output.
echo $OUTPUT->header();
echo $OUTPUT->heading($title);

// Try to delete course with impossible ID;
// the response should say that 0 documents were affected.
$api = new api();
try {
    $data = $api->delete_course(-1);
    if ($data == ['modifieddocs' => 0]) {
        \core\notification::success(
            get_string('apitest:success', 'local_failsltiextension')
        );
    } else {
        \core\notification::warning(
            get_string('apitest:unexpected', 'local_failsltiextension')
        );
    }
} catch (api_request_exception $th) {
    \core\notification::error(
        get_string('apitest:error', 'local_failsltiextension', $th->statuscode)
    );
}

echo $OUTPUT->footer();
