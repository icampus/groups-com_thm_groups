<?php
/**
 * @category    Joomla component
 * @package     THM_Groups
 * @subpackage  com_thm_groups.admin
 * @name        THMGroupsControllerDynamic_Type
 * @description THMGroupsControllerDynamic_Type class from com_thm_groups
 * @author      Peter Janauschek, <peter.janauschek@mni.thm.de>
 * @copyright   2016 TH Mittelhessen
 * @license     GNU GPL v.2
 * @link        www.thm.de
 */

// No direct access to this file
defined('_JEXEC') or die;

// Import Joomla controller library
jimport('joomla.application.component.controller');
jimport('thm_groups.assets.elements.explorer');

/**
 * THMGroupsControllerDynamic_Type_Manager class for component com_thm_groups
 *
 * @category  Joomla.Component.Admin
 * @package   com_thm_groups.admin
 * @link      www.thm.de
 */
class THM_GroupsControllerDynamic_Type_Edit extends JControllerLegacy
{

    /**
     * Constructor
     *
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Reloads the regex options when a static type is chosen in the
     * edit view
     *
     * @return void
     */
    public function reloadTypeRegexOptions()
    {
        $mainframe = Jfactory::getApplication();
        $selected  = $mainframe->input->get('selectedID');
        $model     = $this->getModel('dynamic_type_edit');

        $regexOptions = $model->getRegexOptions($selected);

        if ($regexOptions != null)
        {
            echo $regexOptions;
        }

        $mainframe->close();
    }

    /**
     * Generates output for inputfields of additonal values for
     * the dynamic type, based on its static type accessed via ajax request.
     *
     * @throws Exception
     * @return void
     */
    public function getTypeOptions()
    {
        try
        {
            $app       = Jfactory::getApplication();
            $selected  = $app->input->get('selected');
            $isActType = $app->input->get('isActType');

            $result = $this->getModel('dynamic_type_edit')->getFieldOptions($selected, $isActType);

            echo $result;
        }
        catch (Exception $e)
        {
            echo $e;
        }
    }
}