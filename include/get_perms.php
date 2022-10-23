<?php declare(strict_types=1);

$global_perms = 0;
if (is_object($GLOBALS['xoopsDB'])) {
    if (!is_object($GLOBALS['xoopsUser'])) {
        $whr_groupid = 'gperm_groupid=' . XOOPS_GROUP_ANONYMOUS;
    } else {
        $groups      = $GLOBALS['xoopsUser']->getGroups();
        $whr_groupid = 'gperm_groupid IN (';
        foreach ($groups as $groupid) {
            $whr_groupid .= "$groupid,";
        }
        $whr_groupid = mb_substr($whr_groupid, 0, -1) . ')';
    }
    if (isset($GLOBALS['myalbum_mid'])) {
        $global_perms = [];
        $sql                     = 'SELECT gperm_itemid FROM ' . $GLOBALS['xoopsDB']->prefix('group_permission') . " WHERE gperm_modid='" . $GLOBALS['myalbum_mid'] . "' AND gperm_name='myalbum_global' AND ($whr_groupid)";
        $result                  = $GLOBALS['xoopsDB']->query($sql);

        if ($xoopsDB->isResultSet($result)) {
            while ([$itemid] = $GLOBALS['xoopsDB']->fetchRow($result)) {
                $global_perms |= $itemid;
            }
        } else {
            \trigger_error("Query Failed! SQL: $sql- Error: " . $xoopsDB->error(), E_USER_ERROR);
        }
    }
}
