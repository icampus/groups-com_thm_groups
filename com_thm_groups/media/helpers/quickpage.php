<?php
/**
 * @category    Joomla component
 * @package     THM_Groups
 * @subpackage  com_thm_groups.admin
 * @name        THM_GroupsHelperProfile
 * @author      James Antrim, <james.antrim@nm.thm.de>
 * @copyright   2016 TH Mittelhessen
 * @license     GNU GPL v.2
 * @link        www.thm.de
 */

/**
 * Class providing helper functions for batch select options
 *
 * @category  Joomla.Component.Admin
 * @package   thm_groups
 */
class THM_GroupsHelperQuickpage
{
    /**
     * Gets the user's quickpage category id according to their user id
     *
     * @param   int  $userID  the user id
     *
     * @return  mixed  int on successful query, null if the query failed
     */
    public static function getQPCategoryID($userID)
    {
        if (empty($userID))
        {
            return 0;
        }

        $dbo = JFactory::getDBO();
        $query = $dbo->getQuery(true);

        $query->select('qpCats.categoriesID');
        $query->from('#__thm_groups_users_categories AS qpCats');
        $query->innerJoin('#__categories AS contentCats ON contentCats.id = qpCats.categoriesID');
        $query->where("qpCats.usersID = '$userID'");
        $query->where("contentCats.extension = 'com_content'");
        $dbo->setQuery((string) $query);

        try
        {
            return $dbo->loadResult();
        }
        catch (Exception $exc)
        {
            JFactory::getApplication()->enqueueMessage($exc->getMessage(), 'error');
            return 0;
        }
    }
}