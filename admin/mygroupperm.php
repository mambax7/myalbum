<?php declare(strict_types=1);

use Xmf\Request;

/**
 * @param string|null    $gperm_name
 * @param int|null       $gperm_itemid
 *
 */
function myDeleteByModule(\XoopsDatabase $db, int $gperm_modid, string $gperm_name = null, int $gperm_itemid = null): bool
{
    $criteria = new \CriteriaCompo(new \Criteria('gperm_modid', (int)$gperm_modid));
    if (isset($gperm_name)) {
        $criteria->add(new \Criteria('gperm_name', $gperm_name));
        if (isset($gperm_itemid)) {
            $criteria->add(new \Criteria('gperm_itemid', (int)$gperm_itemid));
        }
    }
    $sql = 'DELETE FROM ' . $db->prefix('group_permission') . ' ' . $criteria->renderWhere();
    return (bool) ($result = $db->query($sql));
}

// require_once  \dirname(__DIR__, 3) . '/include/cp_header.php'; GIJ
$modid = Request::getInt('modid', 1, 'POST');
// we dont want system module permissions to be changed here ( 1 -> 0 GIJ)
if ($modid <= 0 || !is_object($xoopsUser) || !$xoopsUser->isAdmin($modid)) {
    redirect_header(XOOPS_URL . '/user.php', 1, _NOPERM);
}
/** @var \XoopsModuleHandler $moduleHandler */
$moduleHandler = xoops_getHandler('module');
$module        = $moduleHandler->get($modid);
if (!is_object($module) || !$module->getVar('isactive')) {
    redirect_header(XOOPS_URL . '/admin.php', 1, _MODULENOEXIST);
}
/** @var \XoopsMemberHandler $memberHandler */
$memberHandler = xoops_getHandler('member');
$group_list    = $memberHandler->getGroupList();
if (is_array($_POST['perms']) && !empty($_POST['perms'])) {
    /** @var \XoopsGroupPermHandler $grouppermHandler */
    $grouppermHandler = xoops_getHandler('groupperm');
    foreach ($_POST['perms'] as $perm_name => $perm_data) {
        foreach ($perm_data['itemname'] as $item_id => $item_name) {
            if (myDeleteByModule($grouppermHandler->db, $modid, $perm_name, $item_id)) {
                if (empty($perm_data['groups'])) {
                    continue;
                }
                foreach ($perm_data['groups'] as $group_id => $item_ids) {
                    $selected = $item_ids[$item_id] ?? 0;
                    if (1 === $selected) {
                        // make sure that all parent ids are selected as well
                        if ('' !== $perm_data['parents'][$item_id]) {
                            $parent_ids = explode(':', $perm_data['parents'][$item_id]);
                            foreach ($parent_ids as $pid) {
                                if (0 !== (int)$pid && !array_key_exists($pid, $item_ids)) {
                                    // one of the parent items were not selected, so skip this item
                                    $msg[] = sprintf(_MD_AM_PERMADDNG, '<strong>' . $perm_name . '</strong>', '<strong>' . $perm_data['itemname'][$item_id] . '</strong>', '<strong>' . $group_list[$group_id] . '</strong>') . ' (' . _MD_AM_PERMADDNGP . ')';
                                    continue 2;
                                }
                            }
                        }
                        $gperm = $grouppermHandler->create();
                        $gperm->setVar('gperm_groupid', $group_id);
                        $gperm->setVar('gperm_name', $perm_name);
                        $gperm->setVar('gperm_modid', $modid);
                        $gperm->setVar('gperm_itemid', $item_id);
                        if (!$grouppermHandler->insert($gperm)) {
                            $msg[] = sprintf(_MD_AM_PERMADDNG, '<strong>' . $perm_name . '</strong>', '<strong>' . $perm_data['itemname'][$item_id] . '</strong>', '<strong>' . $group_list[$group_id] . '</strong>');
                        } else {
                            $msg[] = sprintf(_MD_AM_PERMADDOK, '<strong>' . $perm_name . '</strong>', '<strong>' . $perm_data['itemname'][$item_id] . '</strong>', '<strong>' . $group_list[$group_id] . '</strong>');
                        }
                        unset($gperm);
                    }
                }
            } else {
                $msg[] = sprintf(_MD_AM_PERMRESETNG, $module->getVar('name'));
            }
        }
    }
}
