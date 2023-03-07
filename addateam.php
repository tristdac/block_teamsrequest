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
 * Simple slider block for Moodle
 *
 * @package   block_teamsrequest
 * 
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;


if (isset($_GET['addteam']) && isset($_GET['userid'])) {
    $courseid = $_GET['addteam'];
    $userid = $_GET['userid'];
    add_team($userid, $courseid);
}


function add_team($userid, $courseid) {
    global $DB;
    if (!$DB->record_exists('block_teamsrequest', array('courseid' => $courseid))) {
        $DB->insert_record('block_teamsrequest', array('userid' => $userid, 'courseid' => $courseid, 'timecreated' => time()));
        $userfrom = $DB->get_record('user',array('id'=>$userid));
        send_notification($userfrom, $courseid);
    }
}

function send_notification($userfrom, $courseid) {
    global $DB, $CFG;
    $course = $DB->get_record('course',array('id' => $courseid));
    $courseurl = $CFG->wwwroot.'/course/view.php?id='.$courseid;
    $adminurl = $CFG->wwwroot.'/local/o365/acp.php?mode=usergroupcustom&search='.$course->shortname;
    $message = 'Please create a new Team for course:'.$course->fullname.' ('.$courseid.')';
    if (get_config('block_teamsrequest', 'notifyusers') === '1') {
        $usersto = get_config('block_teamsrequest', 'usersto');
        if ($usersto) {
            $usersto = explode(',',str_replace(' ', '', $usersto));
            foreach ($usersto as $userto) {
                $userto = $DB->get_record('user', array('username'=>$userto));
                email_to_user($userto, $userfrom, 'MS Team Request', $message, $message.'<br><br><a href="'.$courseurl.'">'.$courseurl.'</a><br><a class="btn btn-primary" href="'.$adminurl.'">Add Team</a>', '', '', true);
            }
        }
    }
}