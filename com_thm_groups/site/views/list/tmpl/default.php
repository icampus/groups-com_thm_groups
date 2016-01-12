<?php
/**
 * @version     v3.4.6
 * @category    Joomla component
 * @package     THM_Groups
 * @subpackage  com_thm_groups.site
 * @name        THMGroupsViewList
 * @description THMGroupsViewList file from com_thm_groups
 * @author      Dennis Priefer, <dennis.priefer@mni.thm.de>
 * @author      Markus Kaiser,  <markus.kaiser@mni.thm.de>
 * @author      Daniel Bellof,  <daniel.bellof@mni.thm.de>
 * @author      Jacek Sokalla,  <jacek.sokalla@mni.thm.de>
 * @author      Niklas Simonis, <niklas.simonis@mni.thm.de>
 * @author      Alexander Boll, <alexander.boll@mni.thm.de>
 * @author      Peter May,      <peter.may@mni.thm.de>
 * @author      Ilja Michajlow, <ilja.michajlow@mni.thm.de>
 * @copyright   2012 TH Mittelhessen
 * @license     GNU GPL v.2
 * @link        www.mni.thm.de
 */

// Include Bootstrap
JHtmlBootstrap::loadCSS();
?>

<div id="title">
<?php
    if (isset($this->title))
    {
        echo "<h2 class='contentheading'>" . $this->title . "</h2>";
    }
?>
</div>

<div id="desc">
<?php
    if (isset($this->desc))
    {
        echo $this->desc;
    }
?>
</div>

<!--<div id="gslistview"></div>-->
<?php
/**
 * Method to get list all
 *
 * @param   Array    $params     contain the Paramter for the View
 * @param   String   $pagetitle  Title of the page
 * @param   Integer  $gid        contain the group id
 *
 * @return  String   $result  the HTML code of te view
 */
function getListAll($params, $pagetitle, $gid)
{
    $result = '<div id="listWrapper" class="row-fluid">
                <div class="span12">';

    $result .= '<div id="gslistview" class="gslistview">';

        // $showAll = $model->getShowMode();
        $groupid = $gid;
        $paramLinkTarget = $params['linkTarget'];
        $rows = THMLibThmGroups::getUserCount($groupid);
         $app = JFactory::getApplication()->input;

        $numColumns = $params['columnCount'];
        $orderAttr = $params['orderingAttributes'];
        $showStructure = $params['showstructure'];
        $linkElement = $params['showLinks'];

        $arrOrderAtt = array();
        if ($orderAttr)
        {
            $arrOrderAtt = explode(",", $orderAttr);
        }
        else
        {
            $arrOrderAtt = null;
        }

        if (isset($numColumns))
        {

        }
        else
        {
            $numColumns = 4;
        }

        $allLastNames = THMLibThmGroups::getFirstletter($groupid);

        $itemid = $app->get('Itemid', 0);
        $abc = array(
                'A',
                'B',
                'C',
                'D',
                'E',
                'F',
                'G',
                'H',
                'I',
                'J',
                'K',
                'L',
                'M',
                'N',
                'O',
                'P',
                'Q',
                'R',
                'S',
                'T',
                'U',
                'V',
                'W',
                'X',
                'Y',
                'Z'
        );

        // Anzahl der verschiedenen Anfangsbuchstaben ermitteln

        $fLetters = array();

        foreach ($allLastNames as $name)
        {
            if (!in_array(strtoupper(substr($name->lastName, 0, 1)), $fLetters))
            {
                $fLetters[] = strtoupper(substr($name->lastName, 0, 1));
            }
        }

        $maxColumnSize = ceil(($rows[0]->anzahl) / $numColumns);
        $numberOfPersons = $rows[0]->anzahl;

        $divStyle = "class='span" . floor(12 / $numColumns) . " col-sm-6'";

        $attribut = THMLibThmGroups::getUrl(array("name", "gsuid", "gsgid"));

        // Welche Detailansicht bei Klick auf Person? Modul oder Profilview?
        $linkTarget = "";
        switch ($paramLinkTarget)
        {
            case "module":
                $linkTarget = 'index.php?option=com_thm_groups&view=list&layout=default&' . $attribut . 'Itemid=' . $itemid;
                break;
            case "profile":
                $linkTarget = 'index.php?option=com_thm_groups&view=profile&layout=default&' . $attribut . 'Itemid=' . $itemid;
                break;
            default:
                $linkTarget = 'index.php?option=com_thm_groups&view=list&layout=default&' . $attribut . 'Itemid=' . $itemid;
        }

        $actualRowPlaced = 0;
        $stop = 0;
        $remeberNextTime = 0;
        $allCount = 0;

        // Durchgehen aller Buchstaben des Alphabets
        for ($i = 0; $i < count($abc); $i++)
        {
            $char = $abc[$i];
            $rows = THMLibThmGroups::getUserByLetter($groupid, $char);
            $actualLetterPlaced = 0;
            $oneEntryMore = 0;

            // Wenn keine Einträge für diesen Buchstaben, dann weiter it nächsten
            if (count($rows) <= 0)
            {
                continue;
            }

            // Wenn noch keine Zeile geschrieben wurde, neu Spalte öffnen
            if ($actualRowPlaced == 0)
            {

                $divid = "row_column_max_" . $maxColumnSize;
                $result .= '<div id="' . $divid . '"' . $divStyle . '>';

            }

      $result .= '<ul class="thm_groups_alphabet">';
      //$result .= '<a class="thm_groups_list">' . $char . '</a>';
        $result .= '<div class="respListHeader" onclick="toogle(this);">' . $char . '</div>';
      $result .= '<div class="thm_groups_listitem thm_groups_toogleitem" style="display: none;">';


      // Wurde beim letzten Durchlauf  ein Buchstabenpaket komplett geschrieben
      if ($remeberNextTime == 0)
         {
             if ($actualRowPlaced + count($rows) - $maxColumnSize > 2 && $actualLetterPlaced == 1)
             {
                 $oneEntryMore = 1;
             }
             // Passt das aktuelle Buchstabenpaket noch in die aktuelle Spalte ($maxColumnSize +2)
             if ($actualRowPlaced + count($rows) - $maxColumnSize > 2)
             {
                 $i--;
                 $stop = $maxColumnSize - $actualRowPlaced;
                 if ($stop == 1)
                 {
                        $stop = 2;
                 }
             }
      }

      // Alle Personen zu einem Buchstaben ausgeben
      foreach ($rows as $row)
      {
          // Wenn aktuelles Buchstabenpaket schon Einträge in der vorherigen Spalte hat, werden diese übersprungen
          if ($remeberNextTime == 0)
          {
              $result .= '<div style="margin-bottom: -11px;">';
              $result .= writeName($arrOrderAtt, $row, $showStructure, $linkElement, $linkTarget, $groupid);
              $actualRowPlaced++;
              $allCount++;
              $actualLetterPlaced++;

              // Ist Stop > 0, werden in die aktuelle Reihe die Einträge eines Buchstabenpaket geschrieben bis $maxColumnSize
              if ($stop > 0 && $actualRowPlaced >= $maxColumnSize && $actualLetterPlaced > 1)
              {
                  $remeberNextTime = $stop;
                  $stop = 0;
                  break;
              }

              $result .= '</div><br/>';
          }
          else
          {
              $remeberNextTime--;
          }
      }

      $result .= '</div></ul>';


      // Schließen einer Reihe, wenn $maxColumnSize erreichtwurde, $remeberNextTime gesetzt ist, oder alle Einträge ausgegebn wurden
      if ($actualRowPlaced >= $maxColumnSize || $remeberNextTime > 0 || $allCount == $numberOfPersons)
      {
          $result .= '</div>';
          $actualRowPlaced = 0;
      }
      else
      {
      }
        }
// 	    echo $result .'</div>';

      return $result . '</div></div></div>';
}

/**
 * Method to get list alphabet
 *
 * @param   Array    $params     Contains the Paramter for the View
 * @param   String   $pagetitle  Title of teh page
 * @param   Integer  $gid        Contain the group Id
 *
 * @return String  $result 	Contain the HTML Code of the view
 */
function getListAlphabet($params, $pagetitle, $gid)
{
    $scriptDir = JUri::root() . "libraries/thm_groups/assets/js/";

        JHTML::script($scriptDir . 'getUserOfLetter.js');

        $groupid = $gid;

        $retString = "";
        $app = JFactory::getApplication()->input;

        $shownLetter = $app->get('letter');
        $paramLinkTarget = $params['linkTarget'];


        $allLastNames = THMLibThmGroups::getFirstletter($groupid);

        $orderAttr = $params['orderingAttributes'];
        $showStructure = $params['showstructure'];

        $linkElement = $params['showLinks'];


        $abc = array(
                'A',
                'B',
                'C',
                'D',
                'E',
                'F',
                'G',
                'H',
                'I',
                'J',
                'K',
                'L',
                'M',
                'N',
                'O',
                'P',
                'Q',
                'R',
                'S',
                'T',
                'U',
                'V',
                'W',
                'X',
                'Y',
                'Z'
        );
        $fLetters = array();
        foreach ($allLastNames as $name)
        {
            $searchUm = str_replace("Ãƒâ€“", "O", $name->lastName);
            $searchUm = str_replace("ÃƒÂ¶", "o", $searchUm);
            $searchUm = str_replace("Ãƒâ€ž", "A", $searchUm);
            $searchUm = str_replace("ÃƒÂ¤", "a", $searchUm);
            $searchUm = str_replace("ÃƒÅ“", "U", $searchUm);
            $searchUm = str_replace("ÃƒÂ¼", "u", $searchUm);

            $searchUm = str_replace("ÃƒÆ’Ã‚Â¶", "O", $searchUm);
            $searchUm = str_replace("ÃƒÆ’Ã‚Â¶", "o", $searchUm);
            $searchUm = str_replace("ÃƒÆ’Ã‚Â¤", "a", $searchUm);
            $searchUm = str_replace("ÃƒÆ’Ã‚Â¤", "A", $searchUm);
            $searchUm = str_replace("ÃƒÆ’Ã‚Â¼", "u", $searchUm);
            $searchUm = str_replace("ÃƒÆ’Ã‚Â¼", "U", $searchUm);

            $searchUm = str_replace("&Ouml;", "O", $searchUm);
            $searchUm = str_replace("&ouml;", "o", $searchUm);
            $searchUm = str_replace("&Auml;", "A", $searchUm);
            $searchUm = str_replace("&auml;", "a", $searchUm);
            $searchUm = str_replace("&uuml;", "u", $searchUm);
            $searchUm = str_replace("&Uuml;", "U", $searchUm);

            $searchUm = str_replace("Ã–", "O", $searchUm);
            $searchUm = str_replace("Ã¶", "o", $searchUm);
            $searchUm = str_replace("Ã„", "A", $searchUm);
            $searchUm = str_replace("Ã¤", "a", $searchUm);
            $searchUm = str_replace("Ã¼", "u", $searchUm);
            $searchUm = str_replace("Ãœ", "U", $searchUm);

            if (!in_array(strtoupper(substr($searchUm, 0, 1)), $fLetters))
            {
                $fLetters[] = strtoupper(substr($searchUm, 0, 1));
            }
        }
        // When first call of the view, search first character with members in it
        sort($fLetters);
        if (!isset($shownLetter))
        {
            $shownLetter = $fLetters[0];
        }
        $linkElementString = " ";
        if (!empty($linkElement))
        {
        foreach ($linkElement as $linkTemp)
        {
            $linkElementString .= $linkTemp . ",";
        }
        }
        $showStructureString = " ";
        if (!empty($showStructure))
        {
            foreach ($showStructure as $showStructureTemp)
            {
                $showStructureString .= $showStructureTemp . ",";
            }
        }
        $itemid = $app->get('Itemid');

        $attribut = THMLibThmGroups::getUrl(array("name", "gsuid", "gsgid", "letter", "groupid"));

        $retString .= '<input type=hidden id="thm_groups_columnNumber" value="' . $params['columnCount'] . '">';
        $retString .= '<input type=hidden id="thm_groups_gid" value="' . $groupid . '">';
        $retString .= '<input type=hidden id="thm_groups_paramLinkTarget" value="' . $paramLinkTarget . '">';
        $retString .= '<input type=hidden id="thm_groups_orderAttr" value="' . $orderAttr . '">';
        $retString .= '<input type=hidden id="thm_groups_showStructure" value="' . $showStructureString . '">';
        $retString .= '<input type=hidden id="thm_groups_linkElement" value="' . $linkElementString . '">';
        $retString .= '<input type=hidden id="thm_groups_itemid" value="' . $itemid . '">';
        $retString .= '<input type=hidden id="thm_groups_url" value="' . $attribut . '">';

        $retString .= "<div class='thm_groups_alphabet'>";
        foreach ($abc as $char)
        {
            $idvalue = "thm_groups_letter" . $char;

            if (in_array(strtoupper($char), $fLetters))
            {
                if ($char == $shownLetter)
                {
            $retString .= '<a class="thm_groups_active" id="' . $idvalue . '" onclick ="jQuery(this).lib_thm_groups_alphabet()" >' . $char . "</a>";
                }
                else
                {
                    $retString .= '<a id="' . $idvalue . '"  onclick ="jQuery(this).lib_thm_groups_alphabet()">' . $char . "</a>";
                }
            }
            else
            {
                $retString .= '<a class="thm_groups_inactive">' . $char . "</a>";
            }
        }

        $retString .= "</div>";
        if ($fLetters == null)
        {
            $retString .= "<div style='float:left'><br />Keine Mitglieder vorhanden.</div>";
        }

        $retString .= getUserForLetter(
                $groupid,
                $params['columnCount'],
                $shownLetter,
                $paramLinkTarget,
                $orderAttr, $showStructure,
                $linkElement, $attribut
         );

        return $retString;
}

/**
 * Method to get a many user with the same Letter
 *
 * @param   Integer  $gid              String of the Attributes order
 * @param   Integer  $column           Array of the attributes order
 * @param   Integer  $letter           Data of the user
 * @param   String   $paramLinkTarget  contain module oder profile
 * @param   String   $orderAttr        contain the Order of Attribute
 * @param   String   $showStructure    is the title on
 * @param   String   $linkElement      the Element of the link
 * @param   String   $oldattrinut      contain the all url attribut for the Breadscrum modul
 *
 * @return  String  $string  Return String
 */
function getUserForLetter($gid, $column, $letter, $paramLinkTarget, $orderAttr, $showStructure, $linkElement, $oldattrinut)
{
    $retString = '<div id="new_user_list">';
        $retString .= "<ul><br /><br />";

        $groupMember = THMLibThmGroups::getGroupMemberByLetter($gid, $letter);

        $memberWithU = array();

        $numColumns = $column;

        $groupid = $gid;
        $app = JFactory::getApplication()->input;
        $pagetitle = $app->get("title");


        $linkTarget = "";
        $itemid = $app->get('Itemid');

        switch ($paramLinkTarget)
        {
            case "module":

                $linkTarget = 'index.php?option=com_thm_groups&view=list&layout=default&' . $oldattrinut . 'Itemid=' . $itemid
                . '&groupid=' . $groupid . '&letter=' . $letter;
                break;
            case "profile":
                $linkTarget = 'index.php?option=com_thm_groups&view=profile&layout=default&' . $oldattrinut . 'Itemid=' . $itemid
                . '&pageTitle=' . rawurlencode($pagetitle);
                break;
            default:
                $linkTarget = 'index.php?option=com_thm_groups&view=list&layout=default&' . $oldattrinut . 'Itemid=' . $itemid
                . '&groupid=' . $groupid . '&letter=' . $letter;
        }
        $arrOrderAtt = array();
        if ($orderAttr)
        {
            $arrOrderAtt = explode(",", $orderAttr);
        }
        else
        {
            $arrOrderAtt = null;
        }

        if (isset($numColumns))
        {

        }
        else
        {
            $numColumns = 4;
        }
        $maxColumnSize = ceil(count($groupMember) / $numColumns);
        $actualRowPlaced = 0;
        $divStyle = "style='width: " . floor(100 / $numColumns) . "%; float: left;'";

        foreach ($groupMember as $member)
        {
            $searchUm = str_replace("Ãƒâ€“", "&Ouml;", $member->lastName);
            $searchUm = str_replace("ÃƒÂ¶", "&ouml;", $searchUm);
            $searchUm = str_replace("Ãƒâ€ž", "&Auml;", $searchUm);
            $searchUm = str_replace("ÃƒÂ¤", "&auml;", $searchUm);
            $searchUm = str_replace("ÃƒÅ“", "&Uuml;", $searchUm);
            $searchUm = str_replace("ÃƒÂ¼", "&uuml;", $searchUm);

            $searchUm = str_replace("ÃƒÆ’Ã‚Â¶", "&Ouml;", $searchUm);
            $searchUm = str_replace("ÃƒÆ’Ã‚Â¶", "&ouml;", $searchUm);
            $searchUm = str_replace("ÃƒÆ’Ã‚Â¤", "&auml;", $searchUm);
            $searchUm = str_replace("ÃƒÆ’Ã‚Â¤", "&Auml;", $searchUm);
            $searchUm = str_replace("ÃƒÆ’Ã‚Â¼", "&uuml;", $searchUm);
            $searchUm = str_replace("ÃƒÆ’Ã‚Â¼", "&Uuml;", $searchUm);

            if ($actualRowPlaced == 0)
            {
                $retString .= '<div ' . $divStyle . '>';
            }
            if (substr($searchUm, 0, 6) == "&Auml;" || substr($searchUm, 0, 6) == "&Ouml;" || substr($searchUm, 0, 6) == "&Uuml;")
            {
                $memberWithU[] = $member;
            }
            else
            {
                $path = "'index.php?option=com_thm_groups&view=list&layout=default&Itemid='";
                $trmimname = trim($member->lastName);
                $retString .= writeName($arrOrderAtt, $member, $showStructure, $linkElement, $linkTarget, $groupid);
                $actualRowPlaced++;
            }

            if ($actualRowPlaced == $maxColumnSize)
            {
                $retString .= "</div>";
                $actualRowPlaced = 0;
            }
        }
        foreach ($memberWithU as $member)
        {
            $path = "'index.php?option=com_thm_groups&view=list&layout=default&Itemid='";
            $trmimname = trim($member['lastName']);
            $retString .= writeName($arrOrderAtt, $member, $showStructure, $linkElement, $linkTarget, $groupid);
        }
        $retString .= "</ul>";
        $retString .= '</div>';
        return $retString;
}

/**
 * Writes a string containing all available, not empty and published attributes of a user
 *
 * @param   array   $arrOrderAtt       array with attributes' ordering
 * @param   object  $member            object with a user information
 * @param   array   $arrshowStructure  array with attributes' id to be shown
 * @param   array   $linkElement       array with ids of attributes to be linked
 * @param   string  $linkTarget        string with target url
 * @param   int     $groupid           group id
 *
 * @return  string a string containing containing all available, not empty and published attributes of a user
 */
function writeName($arrOrderAtt, $member, $arrshowStructure, $linkElement, $linkTarget, $groupid)
{
    $result = '';
    $arrName = array();
    $sortedUserAttributes = array();
    JArrayHelper::toInteger($arrOrderAtt);
    JArrayHelper::toInteger($arrshowStructure);
    JArrayHelper::toInteger($linkElement);

    $attributes = array(
        0 => 'title',
        1 => 'firstName',
        2 => 'lastName',
        3 => 'posttitle'
    );

    // Merge user attributes and their order in one array
    foreach ($arrOrderAtt as $attributeID => $order)
    {
        if (array_search($attributeID, $arrshowStructure) !== false)
        {
            $value = $member->$attributes[$attributeID];
            if (!empty($value))
            {
                array_push(
                    $sortedUserAttributes, array(
                        'id' => $attributeID,
                        'value' => $value,
                        'order' => $order
                    )
                );
            }
        }
    }

    // Sort merged array by order
    usort($sortedUserAttributes, "cmp");

    //var_dump($sortedUserAttributes);
    // Write name
    foreach ($sortedUserAttributes as $userAttribute)
    {
        switch ($userAttribute['id'])
        {
            case 0:
                if (!empty(str_replace(' ', '', $userAttribute['value'])))
                {
                    $arrName[$userAttribute['id']] = $userAttribute['value'] . ' ';
                }
                break;
            case 1:
                // If there is a last name on the second place
                if (array_key_exists(array_search('lastName', $attributes), $arrName))
                {
                    $arrName[$userAttribute['id']] = ', '
                        . isLink($userAttribute['id'], $linkElement, $linkTarget, $member, $groupid, $userAttribute['value']);
                }
                else
                {
                    $arrName[$userAttribute['id']]
                        = isLink($userAttribute['id'], $linkElement, $linkTarget, $member, $groupid, $userAttribute['value']);
                }
                break;
            case 2:
                // If there is a first name on the second place
                if (array_key_exists(array_search('firstName', $attributes), $arrName))
                {
                    $arrName[$userAttribute['id']] = ' '
                        . isLink($userAttribute['id'], $linkElement, $linkTarget, $member, $groupid, $userAttribute['value']);
                }
                else
                {
                    $arrName[$userAttribute['id']]
                        = isLink($userAttribute['id'], $linkElement, $linkTarget, $member, $groupid, $userAttribute['value']);
                }
                break;
            case 3:
                if (!empty(str_replace(' ', '', $userAttribute['value'])))
                {
                    $arrName[$userAttribute['id']] = ', ' . $userAttribute['value'];
                }
                break;
        }
    }

    $result .= implode('', $arrName);
    return $result;
}

/**
 * Checks if an attribute must be a link
 *
 * @param   int     $currentElement  id of current attribute
 * @param   array   $linkElement     array with ids of attributes to be linked
 * @param   string  $linkTarget      string with target url
 * @param   object  $member          object with user information
 * @param   int     $gid             group id
 * @param   string  $value           attribute that will be captured in link
 *
 * @return  string a string containing a link or just a $value
 */
function isLink($currentElement, $linkElement, $linkTarget, $member, $gid, $value)
{
    $return = '';
    if (array_search($currentElement, $linkElement) !== false)
    {
        $return .= JHtml::link(
            JRoute::_(
            $linkTarget . '&gsuid=' . $member->id . '&name=' .
            trim($member->lastName) . '&gsgid=' . $gid
        ), $value
        );
    }
    else
    {
        $return .= $value;
    }
    return $return;
}

/**
 * Sort array by order
 *
 * @param   int  $a  element a
 * @param   int  $b  element b
 *
 * @return  int
 */
function cmp($a, $b)
{
    return strcmp($a["order"], $b["order"]);
}

/**
 * Method to write the  Stylesheet for List View im
 *
 * @param   Array  $params  contain the Paramter for the View
 *
 * @return  String  $result  the HTML code of te view
 */
function getCssView($params)
{

    $out = ".thm_groups_alphabet > .thm_groups_listitem {
            margin-left: 25px;
            margin-top: 0px;
            padding-top: 7px;
            padding-left: 7px;}";

   /* $out .= ".thm_groups_alphabet > a {
            background: none repeat scroll 0 0 " . $params['alphabet_exists_color'] . ";
            border-color: " . $params['alphabet_exists_color'] . ";
            border-style: solid;
            border-width: 1px;
            color: " . $params['alphabet_exists_font_color'] . " ;
            text-align: center;
            padding: 2px 5px;
            width: 10px;
            float: left;
            font-weight: bold;
            margin: 2px 2px 0 0;
            text-decoration: none;
            cursor:pointer;
        }";*/

    $out .= ".thm_groups_alphabet > .respListHeader{
            background: none repeat scroll 0 0 " . $params['alphabet_exists_color'] . ";
            border-color: " . $params['alphabet_exists_color'] . ";
            color: " . $params['alphabet_exists_font_color'] . " ;
            }";

    $out .= ".thm_groups_alphabet > div.respListHeader:hover, .thm_groups_alphabet > div.respListHeader:focus,
            .alphabet > div.respListHeader:thm_groups_active {
                background: none repeat scroll 0 0 " . $params['alphabet_active_color'] . " ;
                border-color: " . $params['alphabet_active_color'] . ";
                color:" . $params['alphabet_active_font_color'] . " ;}";

    $out .= ".thm_groups_alphabet > .thm_groups_inactive, .thm_groups_alphabet > .thm_groups_inactive:hover,
                 .thm_groups_alphabet > .thm_groups_inactive , .thm_groups_active {
                background: none repeat scroll 0 0" . $params['alphabet_inactive_color'] . " ;
                border-color: " . $params['alphabet_inactive_color'] . ";
                color: " . $params['alphabet_inactive_font_color'] . ";}";

    $out .= ".thm_groups_alphabet > .thm_groups_active {
                background: none repeat scroll 0 0  " . $params['alphabet_active_color'] . ";
                border-color:" . $params['alphabet_active_color'] . ";
                   color: " . $params['alphabet_active_font_color'] . ";}";

    $out .= ".thm_groups_alphabet > .thm_groups_list, .thm_groups_alphabet > .thm_groups_list:hover,
                .thm_groups_alphabet > .thm_groups_list:thm_groups_active {
            background: none repeat scroll 0 0 " . $params['alphabet_exists_color'] . ";
            border-color: " . $params['alphabet_exists_color'] . ";
            color: " . $params['alphabet_exists_font_color'] . ";}";
    return $out;
}

$mainframe = Jfactory::getApplication();
$model = $this->model;
$params = $mainframe->getParams();
$paramsArray = $params->toArray();
$mycss = getCssView($paramsArray);

$document = JFactory::getDocument();
$document->addStyleDeclaration($mycss);

// Mainframe Parameter

$pagetitle = $params->get('page_title');
$showall = $params->get('showAll');
$showpagetitle = $params->get('show_page_heading');

if ($showpagetitle)
{
    $this->title = $pagetitle;
}

if ($showall == 1)
{

    echo getListAll($paramsArray, $pagetitle, $model->getGroupNumber());
}
else
{
    echo getListAlphabet($paramsArray, $pagetitle, $model->getGroupNumber());
}

$script = '<script type="text/javascript">'
    . 'jQuery(".thm_groups_alphabet").click('
    . 'function() {'
    . 'if(jQuery(window).width() <= 480){'
    . 'jQuery(".thm_groups_toogleitem", this).slideToggle();}});'
    . '</script>';
?>
<script>
    function toogle(caller){
        if(caller.nextElementSibling.style.display == "none"){
            caller.nextElementSibling.style.display = "inherit";
        }
        else
        {
            caller.nextElementSibling.style.display = "none";
        }
    }
</script>
