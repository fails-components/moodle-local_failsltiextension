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
 * German strings for local_failsltiextension
 *
 * @package   local_failsltiextension
 * @copyright 2023 Daniil Fajnberg, innoCampus, TU Berlin
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

// Required:
$string['pluginname'] = 'FAILS LTI Extension';

// Exceptions:
$string['apirequestexception'] = 'Server antwortete mit Statuscode {$a}';

// Settings:
$string['settings:linksheading'] = 'Links';
$string['settings:heading'] = 'Einstellungen';
$string['settings:apiurl'] = 'API Basis-URL';

// API test page:
$string['apitest:linktext'] = 'Das <a href="{$a->link}">API Testskript</a> hilft dabei, sicherzustellen, dass die API erreichbar ist und erwratungsgemäß antwortet.';
$string['apitest:title'] = 'FAILS API Testseite';
$string['apitest:success'] = 'API test war erfolgreich';
$string['apitest:unexpected'] = 'Unerwarteter API Antworttext';
$string['apitest:error'] = 'API Antwort Status {$a}';
