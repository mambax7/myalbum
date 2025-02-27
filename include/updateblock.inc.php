<?php declare(strict_types=1);
// $Id$

if (\mb_substr(XOOPS_VERSION, 6, 3) < 2.1) {
    // Keep Block option values when update (by nobunobu)
    global $xoopsDB;
    $query  = 'SELECT mid FROM ' . $xoopsDB->prefix('modules') . " WHERE dirname='" . $modversion['dirname'] . "' ";
    $result = $xoopsDB->query($query);
    if ($xoopsDB->isResultSet($result)) {
        $record = $xoopsDB->fetchArray($result);
    } else {
        \trigger_error("Query Failed! SQL: $sql Error: " . $xoopsDB->error(), \E_USER_ERROR);
    }

    if ($record) {
        $mid   = $record['mid'];
        $count = count($modversion['blocks']);
        /* $sql = "SELECT * FROM ".$xoopsDB->prefix('newblocks')." WHERE mid=".$mid." AND block_type ='D'";
        $fresult = $xoopsDB->query($sql);
        $n_funcnum = $count;
        while (false !== ($fblock = $xoopsDB->fetchArray($fresult))) {
            $bnum = 0;
            for ($i = 1 ; $i <= $count ; ++$i) {
                if (($modversion['blocks'][$i]['file'] == $fblock['func_file']) and ($modversion['blocks'][$i]['show_func'] == $fblock['show_func'])) {
                    $bnum = $i;
                    break;
                }
            }
            if ($bnum) {
                ++$n_funcnum;
                $modversion['blocks'][$n_funcnum]['file'] = $fblock['func_file'];
                $modversion['blocks'][$n_funcnum]['name'] = $fblock['name'];
                $modversion['blocks'][$n_funcnum]['description'] = $fblock['name'];
                $modversion['blocks'][$n_funcnum]['show_func'] = $fblock['show_func'];
                $modversion['blocks'][$n_funcnum]['edit_func'] = $fblock['edit_func'];
                $modversion['blocks'][$n_funcnum]['template'] = $fblock['template'];
                if ($fblock['options']) {
                    $old_vals=explode("|",$fblock['options']);
                    $def_vals=explode("|",$modversion['blocks'][$bnum]['options']);
                    if (count($old_vals) == count($def_vals)) {
                        // the number of parameters is not changed
                        $modversion['blocks'][$n_funcnum]['options'] = $fblock['options'];
                        $local_msgs[] = "Option's values of the cloned block <b>".$fblock['name']."</b> will be kept. (value = <b>".$fblock['options']."</b>)";
                    } elseif (count($old_vals) < count($def_vals)) {
                        // the number of parameters is increased
                        for ($j=0; $j < count($old_vals); ++$j) {
                            $def_vals[$j] = $old_vals[$j];
                        }
                        $modversion['blocks'][$n_funcnum]['options'] = implode("|",$def_vals);
                        $local_msgs[] = "Option's values of the cloned block <b>".$fblock['name']."</b> will be kept and new options are added. (value = <b>".$modversion['blocks'][$fblock['func_num']]['options']."</b>)";
                    } else {
                        $modversion['blocks'][$n_funcnum]['options'] = implode("|",$def_vals);
                        $local_msgs[] = "Option's values of the cloned block <b>".$fblock['name']."</b> will be reset to the default, because of some decrease of options. (value = <b>".$modversion['blocks'][$n_funcnum]['options']."</b>)";
                    }
                }
                $sql = "UPDATE ".$xoopsDB->prefix('newblocks')." SET func_num='$n_funcnum' WHERE mid=".$mid." AND bid='".$fblock['bid']."'";
                $iret = $xoopsDB->query($sql);

            }
        } */

        $sql     = 'SELECT * FROM ' . $xoopsDB->prefix('newblocks') . ' WHERE mid=' . $mid . " AND block_type <>'D' AND func_num > $count";
        $fresult = $xoopsDB->query($sql);

        if (!$xoopsDB->isResultSet($fresult)) {
            \trigger_error("Query Failed! SQL: $sql- Error: " . $xoopsDB->error(), E_USER_ERROR);
        } else {
            /** @var array $fblock */
            while (false !== ($fblock = $xoopsDB->fetchArray($fresult))) {
                $local_msgs[] = 'Non Defined Block <b>' . $fblock['name'] . '</b> will be deleted';
                $sql          = 'DELETE FROM ' . $xoopsDB->prefix('newblocks') . " WHERE bid='" . $fblock['bid'] . "'";
                $iret         = $xoopsDB->query($sql);
            }
        }
  
        for ($i = 1; $i <= $count; ++$i) {
            $sql     = 'SELECT name,options FROM ' . $xoopsDB->prefix('newblocks') . ' WHERE mid=' . $mid . ' AND func_num=' . $i . " AND show_func='" . addslashes($modversion['blocks'][$i]['show_func']) . "' AND func_file='" . addslashes($modversion['blocks'][$i]['file']) . "'";
            $fresult = $xoopsDB->query($sql);
            if ($xoopsDB->isResultSet($fresult)) {
                /** @var array $fblock */
                $fblock = $xoopsDB->fetchArray($fresult);
            } else {
                \trigger_error("Query Failed! SQL: $sql- Error: " . $xoopsDB->error(), E_USER_ERROR);
            }
              if (isset($fblock['options'])) {
                /** @var array $old_vals */
                $old_vals = explode('|', $fblock['options']);
                $def_vals = explode('|', $modversion['blocks'][$i]['options']);
                if (count($old_vals) === count($def_vals)) {
                    $modversion['blocks'][$i]['options'] = $fblock['options'];
                    $local_msgs[]                        = "Option's values of the block <b>" . $fblock['name'] . '</b> will be kept. (value = <b>' . $fblock['options'] . '</b>)';
                } elseif (count($old_vals) < count($def_vals)) {
                    for ($j = 0, $jMax = count($old_vals); $j < $jMax; ++$j) {
                        $def_vals[$j] = $old_vals[$j];
                    }
                    $modversion['blocks'][$i]['options'] = implode('|', $def_vals);
                    $local_msgs[]                        = "Option's values of the block <b>" . $fblock['name'] . '</b> will be kept and new option(s) are added. (value = <b>' . $modversion['blocks'][$i]['options'] . '</b>)';
                } else {
                    $local_msgs[] = "Option's values of the block <b>" . $fblock['name'] . '</b> will be reset to the default, because of some decrease of options. (value = <b>' . $modversion['blocks'][$i]['options'] . '</b>)';
                }
            }
        }
    }

    global $msgs, $myblocksadmin_parsed_updateblock;
    if (!empty($msgs) && empty($myblocksadmin_parsed_updateblock)) {
        $msgs                             = array_merge($msgs, $local_msgs);
        $myblocksadmin_parsed_updateblock = true;
    }
}
