<?php
/**
 * @version     v1.0.0
 * @category    Joomla component
 * @package     THM_Groups
 * @subpackage  com_thm_groups.admin
 * @name        THM_GroupsViewDynamic_Type_Edit
 * @description THM_GroupsViewDynamic_Type_Edit file from com_thm_groups
 * @author      Ilja Michajlow, <ilja.michajlow@mni.thm.de>
 * @copyright   2014 TH Mittelhessen
 * @license     GNU GPL v.2
 * @link        www.mni.thm.de
 */
defined('_JEXEC') or die('Restricted access');
jimport('thm_core.edit.view');

/**
 * THM_GroupsViewDynamic_Type_Edit class for component com_thm_groups
 *
 * @category  Joomla.Component.Admin
 * @package   com_thm_groups.admin
 * @link      www.mni.thm.de
 * @since     Class available since Release 2.0
 */
class THM_GroupsViewProfile_Edit extends THM_CoreViewEdit
{

    public $model;

    public $profilid;

    /**
     * loads model data into view context
     *
     * @param   string  $tpl  the name of the template to be used
     *
     * @return void
     */
    public function display($tpl = null)
    {
        if (!JFactory::getUser()->authorise('core.manage', 'com_thm_groups'))
        {
            return JError::raiseWarning(404, JText::_('JERROR_ALERTNOAUTHOR'));
        }

        $app = JFactory::getApplication()->input;
        $user = JFactory::getUser();

        // Get user ids
        $this->profilid = intval($app->get('id'));


        $this->model = $this->getModel();

        parent::display($tpl);
    }

    protected function addToolbar()
    {
        $app = JFactory::getApplication();
        $title = intval($app->get('id')) == 0 ? 'New' : 'Edit';

        JToolBarHelper::title($title, 'test');

        JToolBarHelper::apply('profile.apply', 'JTOOLBAR_APPLY');
        JToolBarHelper::save('profile.save', 'JTOOLBAR_SAVE');
        JToolBarHelper::custom('profile.save2new', 'save-new.png', 'save-new_f2.png', 'JTOOLBAR_SAVE_AND_NEW', false);
        JToolBarHelper::cancel('profile.cancel', 'JTOOLBAR_CLOSE');
    }
}
