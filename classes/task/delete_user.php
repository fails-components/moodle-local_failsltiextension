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


class delete_user extends adhoc_task {
    /**
     * @inheritDoc
     * @throws \Exception
     */
    public function execute() {
        $userid = $this->get_custom_data()->userid;
        mtrace("Requesting deletion of user {$userid} in the FAILS backend.");
        $api = new api();
        try {
            $uuid = $api->get_user_uuid($userid)['uuid'];
        }
        catch (api_request_exception $th) {
            if ($th->statuscode == 404) {
                // This is fine and will happen most of the time
                // because most users likely never used the tool
                // and thus have no entry for them in the DB.
                mtrace("User {$userid} not found in the FAILS backend.");
                return;
            } else {
                throw $th;
            }
        }
        try {
            list(
                'deletedusers' => $deletedusers,
                'modifieddocs' => $modifieddocs
            ) = $api->delete_user($uuid);
        } catch (api_request_exception $th) {
            // PLACEHOLDER
            throw $th;
        }
        if ($deletedusers == 0) {
            $msg = "User {$userid} ({$uuid}) was not deleted";
        } elseif ($deletedusers == 1) {
            $msg = "User {$userid} ({$uuid}) was successfully deleted";
        } else {
            $msg = "Unexpectedly, {$deletedusers} users were deleted";
        }
        $msg .= " in the FAILS backend; modified {$modifieddocs} document(s).";
        mtrace($msg);
    }
}
