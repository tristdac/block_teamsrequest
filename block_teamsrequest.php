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
 * teamsrequest block caps.
 *
 * @package    block_teamsrequest
 * @copyright  Daniel Neis <danielneis@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/blocks/teamsrequest/lib.php');

class block_teamsrequest extends block_base {

    /** @var bool|null */
    protected $docked = null;

    function init() {
        $this->title = get_string('pluginname', 'block_teamsrequest');
    }

    function instance_config_save($data, $nolongerused = false) {
        parent::instance_config_save($data);
    }

    function get_content() {
        global $CFG, $OUTPUT;

        if ($this->content !== null) {
            return $this->content;
        }

        if (empty($this->instance)) {
            $this->content = '';
            return $this->content;
        }

        $this->content = new stdClass();
        $this->content->items = array();
        $this->content->icons = array();
        $this->content->footer = '';

        // // user/index.php expect course context, so get one if page has module context.
        // $currentcontext = $this->page->context->get_course_context(false);

        // if (! empty($this->config->text)) {
        //     $this->content->text = $this->config->text;
        // }

        // $this->content = '';
        // if (empty($currentcontext)) {
        //     return $this->content;
        // }
        // if ($this->page->course->id == SITEID) {
        //     $this->content->text .= "site context";
        // }

        // if (! empty($this->config->text)) {
        $this->content->text = get_requests();
        // }

        return $this->content;
    }

    // my moodle can only have SITEID and it's redundant here, so take it away
    public function applicable_formats() {
        if (has_capability('moodle/site:config', context_system::instance())) {
            return array('all' => true);
        } else {
            return array('all' => false);
        }
    }

    public function instance_allow_multiple() {
          return false;
    }

    function has_config() {return true;}

    public function cron() {
            mtrace( "Hey, my cron script is running" );
             
                 // do something
                  
                      return true;
    }
}
