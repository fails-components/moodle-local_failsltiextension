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

defined('MOODLE_INTERNAL') || die;


if ($hassiteconfig) {
    $settings = new admin_settingpage(
        'local_failsltiextension',
        get_string('pluginname', 'local_failsltiextension'),
        'moodle/site:config'
    );

    $settings->add(
        new admin_setting_heading(
            'links',
            get_string('settings:linksheading', 'local_failsltiextension'),
            get_string(
                'apitest:linktext',
                'local_failsltiextension',
                ['link' => (string) new moodle_url('/local/failsltiextension/api_test.php')]
            )
        )
    );

    $settings->add(
        new admin_setting_heading(
            'settings',
            get_string('settings:heading', 'local_failsltiextension'),
            ''
        )
    );
    $settings->add(
        new admin_setting_configtext(
            'local_failsltiextension/api_url',
            get_string('settings:apiurl', 'local_failsltiextension'),
            '',
            '',
            PARAM_URL
        )
    );

    $ADMIN->add('localplugins', $settings);
}
