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


function get_requests() {
    global $DB, $CFG;
    
    $allrequests = $DB->get_records('block_teamsrequest', null, 'timecreated ASC', 'courseid');
    $existingteams = $DB->get_records('local_o365_objects', array('subtype'=>'courseteam'), '', 'moodleid');
    // $outstanding_teams = $diff = array_diff($allrequests, $existing);
    // $existing_teams = (array) $existing;
    $allrequests_arr = array();
    foreach ($allrequests as $request) {
        $allrequests_arr[] .= $request->courseid;
    }
    $existingteams_arr = array();
    foreach ($existingteams as $existingteam) {
        $existingteams_arr[] .= $existingteam->moodleid;
    }
    $html = '<a href="'.$CFG->wwwroot.'/local/o365/acp.php?mode=usergroupcustom" class="btn btn-primary">Create Teams</a><br><br>';
    $html .= '<h5>Awaiting Creation</h5>';
    $html .= '<ul id="outstanding_teams" style="list-style:none;padding-left:5px;">';
    $outstanding_teams = array();
    foreach ($allrequests_arr as $req) {
        if (!in_array($req, $existingteams_arr)) {
            $course = $DB->get_record('course',array('id' => $req));
            $courseurl = $CFG->wwwroot.'/course/view.php?id='.$req;
            $adminurl = $CFG->wwwroot.'/local/o365/acp.php?mode=usergroupcustom&search='.$course->shortname;
            $html .= '<li class="outstanding_team">';
            $html .= $course->fullname;
            $html .= ' <a href="'.$courseurl.'" target="_blank">[View]</a>';
            $html .= ' <a href="'.$adminurl.'">[Create]</a>';
            $html .= '</li>';
            $hasoutstanding = TRUE;
        }
    }
    if ($hasoutstanding === TRUE) {
        foreach ($outstanding_teams as $outstanding_team) {
            $html .= '<li class="outstanding_team">'.$outstanding_team.'</li>';
        }
    } else {
        $html .= 'Great! There are no outstanding requests';
    }
    $html .= '</ul>';

    return $html;
}

function teamrequested($courseid) {
    global $DB;
    if ($DB->record_exists('block_teamsrequest', array('courseid' => $courseid))) {
        return TRUE;
    } else {
        return FALSE;
    }
}
