<?php
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
$title = get_string('api_test:title', 'local_failsltiextension');
$PAGE->set_title($title);

// Start output.
echo $OUTPUT->header();
echo $OUTPUT->heading($title);

$api = new api();

try {
    $data = $api->delete_course(-1);
    if ($data == ['modifieddocs' => 0]) {
        \core\notification::success(
            get_string('api_test:success', 'local_failsltiextension')
        );
    } else {
        \core\notification::warning(
            get_string('api_test:unexpected', 'local_failsltiextension')
        );
    }
} catch (api_request_exception $th) {
    \core\notification::error(
        get_string('api_test:error', 'local_failsltiextension', $th->statuscode)
    );
}

echo $OUTPUT->footer();
