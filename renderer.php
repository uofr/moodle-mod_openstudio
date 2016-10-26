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
 * @package mod_openstudio
 * @copyright The Open University
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

// Make sure this isn't being directly accessed.
defined('MOODLE_INTERNAL') || die();

use \mod_openstudio\local\renderer_utils;

/**
 * OpenStudio renderer.
 *
 * @package mod_openstudio
 * @copyright 2016 The Open University
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class mod_openstudio_renderer extends plugin_renderer_base {
    /**
     * This function renders the HTML fragment for the primary and secnodary
     * navigation for the Open Studio module.
     *
     * @param int $coursedata Course module data.
     * @param object $permissions The permission object for the given user/view.
     * @param object $theme The theme settings.
     * @param string $sitename Site name to display.
     * @param string $searchtext Search text to display.
     * @param int $viewmode View mode: module, group, studio, pinboard or workspace.
     * @param array $rssdata data to generate RSS and Atom button.
     * @return string The rendered HTM fragment.
     */
    public function siteheader(
            $coursedata, $permissions, $theme, $sitename = 'Design', $searchtext = '',
            $viewmode = OPENSTUDIO_VISIBILITY_MODULE, $rssdata) {
        global $OUTPUT;

        $cminstance = $coursedata->cminstance;
        $cm = $coursedata->cm;
        $cmid = $cm->id;

        $data = new stdClass();
        $data->sitename = $sitename;

        // Check if Rss enabled.
        if ($permissions->feature_enablerss) {
            $atomfeedurl = new moodle_url('/mod/openstudio/feed.php',
                array('id' => $cmid,
                        'studioid' => $cminstance->id,
                        'userid' => $rssdata['viewuser']->id,
                        'ownerid' => $rssdata['slotowner']->id,
                        'type' => $rssdata['rssfeedtype'],
                        'format' => 'atom',
                        'key' => $rssdata['rssfeedkey']
                ));

            $rssfeedurl = new moodle_url('/mod/openstudio/feed.php',
                array('id' => $cmid,
                        'studioid' => $cminstance->id,
                        'userid' => $rssdata['viewuser']->id,
                        'ownerid' => $rssdata['slotowner']->id,
                        'type' => $rssdata['rssfeedtype'],
                        'format' => 'rss',
                        'key' => $rssdata['rssfeedkey']
                ));

            $data->atomfeedurl = $atomfeedurl;
            $data->rssfeedurl = $rssfeedurl;
        }

        // Placeholder text.
        $placeholdertext = '';
        switch ($viewmode) {
            case OPENSTUDIO_VISIBILITY_MODULE:
                $placeholdertext = $theme->thememodulename;
                break;

            case OPENSTUDIO_VISIBILITY_GROUP:
                $placeholdertext = $theme->themegroupname;
                break;

            case OPENSTUDIO_VISIBILITY_WORKSPACE:
            case OPENSTUDIO_VISIBILITY_PRIVATE:
                $placeholdertext = $theme->themestudioname;
                break;

            case OPENSTUDIO_VISIBILITY_PRIVATE_PINBOARD:
                $placeholdertext = $theme->themepinboardname;
                break;
        }
        $data->placeholdertext = $placeholdertext;

        // Render navigation.
        $data->navigation = array();

        $navigationurls = renderer_utils::navigation_urls($cmid);

        $menuitem = array(
                'hassubnavigation' => false,
                'subnavigation' => array()
        );

        // Generate shared content items.
        if ($permissions->feature_module) {
            $submenuitem = array(
                    'name' => $theme->thememodulename,
                    'url' => $navigationurls->strmymoduleurl
            );
            $menuitem['hassubnavigation'] = true;
            $menuitem['subnavigation'][] = $submenuitem;
        }

        if ($permissions->feature_group) {
            $submenuitem = array(
                    'name' => $theme->themegroupname,
                    'url' => $navigationurls->strmygroupurl
            );
            $menuitem['hassubnavigation'] = true;
            $menuitem['subnavigation'][] = $submenuitem;
        }

        if (!empty($menuitem['subnavigation'])) {
            $menuitem['name'] = get_string('menusharedcontent', 'openstudio');
            $menuitem['url'] = '#';
            $menuitem['pix'] = $OUTPUT->pix_url('shared_content_rgb_32px', 'openstudio');
            $menuitem['class'] = 'shared-content';
            $data->navigation[] = $menuitem;
        }

        // Generate people items.
        $menuitem['name'] = get_string('menupeople', 'openstudio');
        $menuitem['url'] = $navigationurls->strpeoplemoduleurl;
        $menuitem['pix'] = $OUTPUT->pix_url('people_rgb_32px', 'openstudio');
        $menuitem['class'] = 'people';
        $menuitem['hassubnavigation'] = false;
        $data->navigation[] = $menuitem;

        // Generate my content items.
        $menuitem = array(
                'hassubnavigation' => false,
                'subnavigation' => array()
        );

        if (!$permissions->feature_studio || ($permissions->activitydata->used > 0)) {
            $submenuitem = array(
                    'name' => $theme->themestudioname,
                    'url' => $navigationurls->strmyworkurl
            );
            $menuitem['hassubnavigation'] = true;
            $menuitem['subnavigation'][] = $submenuitem;
        }

        if ($permissions->feature_pinboard || ($permissions->pinboarddata->usedandempty > 0)) {
            $submenuitem = array(
                    'name' => $theme->themepinboardname,
                    'url' => $navigationurls->strpinboardurl
            );
            $menuitem['hassubnavigation'] = true;
            $menuitem['subnavigation'][] = $submenuitem;
        }

        if (!empty($menuitem['subnavigation'])) {
            $menuitem['name'] = get_string('menumycontent', 'openstudio');
            $menuitem['url'] = '#';
            $menuitem['pix'] = $OUTPUT->pix_url('openstudio_rgb_32px', 'openstudio');
            $menuitem['class'] = 'my-content';
            $data->navigation[] = $menuitem;
        }

        // Generate admin items.
        $adminmenuitem = $this->navigation_admin($coursedata, $permissions);
        $data->navigation[] = $adminmenuitem;

        $data->notificationnumber = 3;
        $data->notificationicon = $OUTPUT->pix_url('notifications_rgb_32px', 'openstudio');

        return $this->render_from_template('mod_openstudio/header', $data);

    }

    /**
     * This function renders admin menu items for Open Studio module.
     *
     * @param int $cmid The course module id.
     * @param object $permissions The permission object for the given user/view.
     * @return menu items array.
     */
    public function navigation_admin($coursedata, $permissions) {
        global $OUTPUT, $CFG;

        $cm = $coursedata->cm;
        $course = $coursedata->course;
        $context = context_module::instance($cm->id);

        $menuitem = array(
                'hassubnavigation' => false,
                'subnavigation' => array()
        );

        if ($permissions->addinstance || $permissions->managelevels) {

            if ($permissions->addinstance) {
                $redirectorurl = new moodle_url('/course/modedit.php',
                    array('update' => $cm->id, 'return' => 0, 'sr' => ''));

                $submenuitem = array(
                        'name' => get_string('navadmineditsettings', 'openstudio'),
                        'url' => $redirectorurl
                );
                $menuitem['hassubnavigation'] = true;
                $menuitem['subnavigation'][] = $submenuitem;
            }
            if ($permissions->managelevels) {
                $redirectorurl = new moodle_url('/mod/openstudio/manageblocks.php',
                    array('id' => $cm->id));

                $submenuitem = array(
                        'name' => get_string('navadminmanagelevel', 'openstudio'),
                        'url' => $redirectorurl
                );
                $menuitem['hassubnavigation'] = true;
                $menuitem['subnavigation'][] = $submenuitem;
            }
            if (has_capability('moodle/role:assign', $permissions->coursecontext)) {
                $redirectorurl = new moodle_url('/admin/roles/assign.php',
                    array('contextid' => $context->id));

                $submenuitem = array(
                        'name' => get_string('navadminassignroles', 'openstudio'),
                        'url' => $redirectorurl
                );
                $menuitem['hassubnavigation'] = true;
                $menuitem['subnavigation'][] = $submenuitem;
            }
            if (has_capability('moodle/role:review', $permissions->coursecontext)) {
                $redirectorurl = new moodle_url('/admin/roles/permissions.php',
                array('contextid' => $context->id));

                $submenuitem = array(
                        'name' => get_string('navadminpermissions', 'openstudio'),
                        'url' => $redirectorurl
                );
                $menuitem['hassubnavigation'] = true;
                $menuitem['subnavigation'][] = $submenuitem;
            }
            if (has_any_capability(array('moodle/role:assign',
                    'moodle/role:safeoverride',
                    'moodle/role:override',
                    'moodle/role:manage'),
                    $permissions->coursecontext)) {

                $redirectorurl = new moodle_url('/admin/roles/check.php',
                    array('contextid' => $context->id));

                $submenuitem = array(
                        'name' => get_string('navadmincheckpermissions', 'openstudio'),
                        'url' => $redirectorurl
                );
                $menuitem['hassubnavigation'] = true;
                $menuitem['subnavigation'][] = $submenuitem;
            }
            if (has_capability('moodle/filter:manage', $permissions->coursecontext)) {
                $redirectorurl = new moodle_url('/filter/manage.php',
                    array('contextid' => $context->id));

                $submenuitem = array(
                        'name' => get_string('navadminfilters', 'openstudio'),
                        'url' => $redirectorurl
                );
                $menuitem['hassubnavigation'] = true;
                $menuitem['subnavigation'][] = $submenuitem;
            }
            if (has_capability('report/log:view', $permissions->coursecontext)) {
                $redirectorurl = new moodle_url('/report/log/index.php',
                    array('id' => $course->id, 'modid' => $cm->id, 'chooselog' => 1));

                $submenuitem = array(
                        'name' => get_string('navadminlogs', 'openstudio'),
                        'url' => $redirectorurl
                );
                $menuitem['hassubnavigation'] = true;
                $menuitem['subnavigation'][] = $submenuitem;
            }
            if (has_capability('mod/openstudio:managecontent', $permissions->coursecontext)) {
                $redirectorurl = new moodle_url('/mod/openstudio/reportusage.php',
                    array('id' => $cm->id));

                $submenuitem = array(
                        'name' => get_string('navadminusagereport', 'openstudio'),
                        'url' => $redirectorurl
                );
                $menuitem['hassubnavigation'] = true;
                $menuitem['subnavigation'][] = $submenuitem;
            }
            if (file_exists("{$CFG->dirroot}/report/restrictuser/lib.php") &&
                    has_any_capability(array('report/restrictuser:view',
                            'report/restrictuser:restrict',
                            'report/restrictuser:removerestrict'),
                            context_module::instance($cm->id))) {
                // Restrict user report available.
                require_once("{$CFG->dirroot}/report/restrictuser/lib.php");
                $redirectorurl = report_restrictuser_get_user_navurl($context);

                $submenuitem = array(
                        'name' => get_string('navlink', 'report_restrictuser'),
                        'url' => $redirectorurl
                );
                $menuitem['hassubnavigation'] = true;
                $menuitem['subnavigation'][] = $submenuitem;
            }

            if (!empty($menuitem['subnavigation'])) {
                $menuitem['name'] = get_string('menuadministration', 'openstudio');
                $menuitem['url'] = '#';
                $menuitem['pix'] = $OUTPUT->pix_url('administration_rgb_32px', 'openstudio');
                $menuitem['class'] = 'administration';
            }
        }

        return $menuitem;
    }

    /**
     * This function renders the HTML for search form.
     * @param object $theme The theme settings.
     * @param int $viewmode View mode: module, group, studio, pinboard or workspace.
     * @return string The rendered HTM search form.
     */
    public function searchform($theme, $viewmode) {
        global $OUTPUT, $CFG;
        $data = new stdClass();

        // Placeholder text.
        $placeholdertext = '';
        switch ($viewmode) {
            case OPENSTUDIO_VISIBILITY_MODULE:
                $placeholdertext = $theme->thememodulename;
                break;

            case OPENSTUDIO_VISIBILITY_GROUP:
                $placeholdertext = $theme->themegroupname;
                break;

            case OPENSTUDIO_VISIBILITY_WORKSPACE:
            case OPENSTUDIO_VISIBILITY_PRIVATE:
                $placeholdertext = $theme->themestudioname;
                break;

            case OPENSTUDIO_VISIBILITY_PRIVATE_PINBOARD:
                $placeholdertext = $theme->themepinboardname;
                break;
        }

        $data->placeholdertext = $placeholdertext;
        $data->searchlink = $CFG->wwwroot.'/mod/openstudio/search.php';
        $data->helplink = $CFG->wwwroot.'/help.php';
        $data->iconsearch = $OUTPUT->pix_url('i/search');

        return $this->render_from_template('mod_openstudio/search_form', $data);
    }
}