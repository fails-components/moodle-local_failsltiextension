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


namespace local_failsltiextension\observer;

use core\event\course_deleted;
use core\event\user_deleted;

use local_failsltiextension\task;


defined('MOODLE_INTERNAL') || die();


class delete_observer {
    public static function course_deleted(course_deleted $event) {
        $taskdelete = new task\delete_course();
        $taskdelete->set_custom_data(['courseid' => $event->objectid]);
        \core\task\manager::queue_adhoc_task($taskdelete);
    }

    public static function user_deleted(user_deleted $event) {
        $taskdelete = new task\delete_user();
        $taskdelete->set_custom_data(['userid' => $event->userid]);
        \core\task\manager::queue_adhoc_task($taskdelete);
    }
}

