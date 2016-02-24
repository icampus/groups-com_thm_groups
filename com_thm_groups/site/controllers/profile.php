<?php
/**
 * @category    Joomla component
 * @package     THM_Groups
 * @subpackage  com_thm_groups.site
 * @name        THMGroupsControllerProfile
 * @description THMGroups component site profile controller
 * @author      Dennis Priefer, <dennis.priefer@mni.thm.de>
 * @author      Alexander Boll, <alexander.boll@mni.thm.de>
 * @copyright   2016 TH Mittelhessen
 * @license     GNU GPL v.2
 * @link        www.thm.de
 */
defined('_JEXEC') or die;
jimport('joomla.application.component.controller');

/**
 * Site profile controller class for component com_thm_groups
 *
 * Profile controller for the site section of the component
 *
 * @category  Joomla.Component.Site
 * @package   com_thm_groups.site
 * @link      www.thm.de
 */
class THM_GroupsControllerProfile extends JControllerLegacy
{
    /**
     *  Constructor (registers additional tasks to methods)
     */
    public function __construct()
    {
        parent::__construct();
        $this->registerTask('backToRefUrl', '');
    }

    /**
     *  Method to get the link, where the redirect has to go
     *
     *@return   string  link.
     */
    public function getLink()
    {
        $model = $this->getModel('profile');
        return $model->getLink();
    }

    /**
     *  Method, which sets the redirect for the 'back' button.
     *
     *@return   void
     */
    public function backToRefUrl()
    {
        /*
        $option_back = JRequest::getVar('option_back', 0);
        $layout_back = JRequest::getVar('layout_back', 0);
        $view_back = JRequest::getVar('view_back', 0);

        $link = JRoute::_('index.php'
            . '?option=' . $option_back
            . '&view=' . $view_back
            . '&layout_back=' . $layout_back
        );*/
        $this->setRedirect(JRequest::getVar('refUrl'));
    }
}
