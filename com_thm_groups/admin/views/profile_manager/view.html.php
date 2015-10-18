<?php
/**
 * @version     v1.0.0
 * @category    Joomla component
 * @package     THM_Groups
 * @subpackage  com_thm_groups.admin
 * @name        THM_GroupsViewProfile_Manager
 * @description THM_GroupsViewProfile_Manager file from com_thm_groups
 * @author      Ilja Michajlow, <ilja.michajlow@mni.thm.de>
 * @copyright   2014 TH Mittelhessen
 * @license     GNU GPL v.2
 * @link        www.mni.thm.de
 */

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

jimport('thm_core.list.view');
JHtml::_('jquery.framework');
require_once JPATH_COMPONENT . '/assets/helpers/group_manager_helper.php';

/**
 * THM_GroupsViewProfile_Manager class for component com_thm_groups
 *
 * @category  Joomla.Component.Admin
 * @package   com_thm_groups.admin
 * @link      www.mni.thm.de
 * @since     Class available since Release 2.0
 */
class THM_GroupsViewProfile_Manager extends THM_CoreViewList
{
    public $items;

    public $pagination;

    public $state;

    public $groups;

    public $batch;

    /**
     * Method to get display
     *
     * @param   Object  $tpl  template
     *
     * @return void
     */
    public function display($tpl = null)
    {
        if (!JFactory::getUser()->authorise('core.manage', 'com_thm_groups'))
        {
            return JError::raiseWarning(404, JText::_('JERROR_ALERTNOAUTHOR'));
        }

        // Set batch template path
        $this->batch = array(
            'batch' => JPATH_COMPONENT_ADMINISTRATOR . '/views/profile_manager/tmpl/default_batch.php'
        );

        $this->groups = THM_GroupsHelperGroup_Manager::getGroups();

        $document = JFactory::getDocument();
        $document->addScript(JURI::root(true) . '/administrator/components/com_thm_groups/assets/js/profile_manager.js');


        parent::display($tpl);
    }

    /**
     * Add Joomla ToolBar with add edit delete options.
     *
     * @return void
     */
    protected function addToolbar()
    {
        $user = JFactory::getUser();

        JToolBarHelper::title(
            JText::_('COM_THM_GROUPS') . ': ' . JText::_('COM_THM_GROUPS_PROFILE_MANAGER'), 'profile_manager'
        );

        if ($user->authorise('core.create', 'com_thm_groups'))
        {
            JToolBarHelper::addNew('profile.add', 'COM_THM_GROUPS_PROFILE_MANAGER_ADD', false);
        }

        if ($user->authorise('core.edit', 'com_thm_groups'))
        {
            JToolBarHelper::editList('profile.edit', 'COM_THM_GROUPS_PROFILE_MANAGER_EDIT');
        }

        if ($user->authorise('core.delete', 'com_thm_groups'))
        {
            JToolBarHelper::deleteList('COM_THM_GROUPS_PROFILE_MANAGER_REALLY_DELETE', 'profile.delete', 'JTOOLBAR_DELETE');
        }

        if ($user->authorise('core.manage', 'com_thm_groups') && $user->authorise('core.edit', 'com_thm_groups'))
        {
            $bar = JToolBar::getInstance('toolbar');
            JHtml::_('bootstrap.modal', 'myModal');
            $title = JText::_('COM_THM_GROUPS_PROFILE_MANAGER_BATCH');
            $dhtml = "<button id='add_group_to_profile_btn' data-toggle='modal' data-target='#collapseModal' class='btn btn-small'><i class='icon-edit' title='$title'></i> $title</button>";

            $bar->appendButton('Custom', $dhtml, 'batch');
        }

        if ($user->authorise('core.admin', 'com_thm_groups') && $user->authorise('core.manage', 'com_thm_groups'))
        {
            JToolBarHelper::divider();
            JToolBarHelper::preferences('com_thm_groups');
        }
    }
}
