<?php
/**
 * @package     THM_Groups
 * @extension   com_thm_groups
 * @author      Ilja Michajlow, <ilja.michajlow@mni.thm.de>
 * @author      James Antrim, <james.antrim@nm.thm.de>
 * @copyright   2018 TH Mittelhessen
 * @license     GNU GPL v.2
 * @link        www.thm.de
 */

/**
 * Class providing helper functions for batch select options
 */
class THM_GroupsHelperBatch
{
    /**
     * Return all existing roles as select field
     *
     * @return  array  an array of options for drop-down list
     * @throws Exception
     */
    public static function getRoles()
    {
        $dbo   = JFactory::getDbo();
        $query = $dbo->getQuery(true)
            ->select('id AS value, name AS text')
            ->from('#__thm_groups_roles')
            ->order('id');
        $dbo->setQuery($query);

        try {
            $options = $dbo->loadObjectList();
        } catch (Exception $exc) {
            JFactory::getApplication()->enqueueMessage($exc->getMessage(), 'error');

            return [];
        }

        for ($i = 0, $n = count($options); $i < $n; $i++) {
            $roles[] = JHtml::_('select.option', $options[$i]->value, $options[$i]->text);
        }

        return $roles;
    }

    /**
     * Returns groups as a select field
     * It shows only groups with users in it, because this select field
     * will be used only for filtering in backend-user-manager
     *
     * @return array
     * @throws Exception
     */
    public static function getGroupOptions()
    {
        $dbo   = JFactory::getDbo();
        $query = $dbo->getQuery(true);

        // TODO: Explain the logic behind this
        $select = 'ug.id, ug.title, COUNT(DISTINCT ugTemp.id) AS level';
        $query->select($select);
        $query->from('#__usergroups as ug');
        $query->innerJoin('#__thm_groups_role_associations AS roleAssoc ON ug.id = roleAssoc.groupID');
        $query->leftJoin('#__usergroups AS ugTemp ON ug.lft > ugTemp.lft AND ug.rgt < ugTemp.rgt');
        $query->where('ug.id NOT IN  (1,2)');
        $query->group('ug.id, ug.title, ug.lft, ug.rgt');
        $query->order('ug.lft ASC');

        $dbo->setQuery($query);

        try {
            $options = $dbo->loadObjectList();
        } catch (Exception $exc) {
            JFactory::getApplication()->enqueueMessage($exc->getMessage(), 'error');

            return [];
        }

        for ($i = 0, $n = count($options); $i < $n; $i++) {
            $groups[] = JHtml::_('select.option', $options[$i]->id,
                str_repeat('- ', $options[$i]->level) . $options[$i]->title);
        }

        return $groups;
    }
}
