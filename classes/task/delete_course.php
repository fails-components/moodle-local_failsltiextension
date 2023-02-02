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

namespace local_failsltiextension\task;

use core\task\adhoc_task;

use local_failsltiextension\api;
use local_failsltiextension\api_request_exception;

defined('MOODLE_INTERNAL') || die();


class delete_course extends adhoc_task {
    /**
     * @inheritDoc
     * @throws \Exception
     */
    public function execute() {
        $courseid = $this->get_custom_data()->courseid;
        mtrace("Requesting deletion of course with ID {$courseid} in the FAILS backend.");
        $api = new api();
        try {
            list('modifieddocs' => $modifieddocs) = $api->delete_course($courseid);
        } catch (api_request_exception $th) {
            // PLACEHOLDER
            throw $th;
        }
        mtrace("Course deletion for ID {$courseid} modified {$modifieddocs} document(s).");
    }
}
