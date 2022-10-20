<?php declare(strict_types=1);

/**
 * @param $options
 *
 * @return array|false|null
 */
function myalbum_tag_block_cloud_show($options)
{
    if (file_exists(XOOPS_ROOT_PATH . '/modules/tag/blocks/block.php')) {
        //        global $module_dirname;
        $module_dirname = '';
        require_once XOOPS_ROOT_PATH . '/modules/tag/blocks/block.php';

        return tag_block_cloud_show($options, $module_dirname);
    }

    return null;
}

/**
 * @param $options
 *
 * @return null|false|string
 */
function myalbum_tag_block_cloud_edit($options)
{
    if (file_exists(XOOPS_ROOT_PATH . '/modules/tag/blocks/block.php')) {
        require_once XOOPS_ROOT_PATH . '/modules/tag/blocks/block.php';

        return tag_block_cloud_edit($options);
    }

    return null;
}

/**
 * @param $options
 *
 * @return array|false|null
 */
function myalbum_tag_block_top_show($options)
{
    if (file_exists(XOOPS_ROOT_PATH . '/modules/tag/blocks/block.php')) {
        //        global $module_dirname;
        $module_dirname = '';
        require_once XOOPS_ROOT_PATH . '/modules/tag/blocks/block.php';

        return tag_block_top_show($options, $module_dirname);
    }

    return null;
}

/**
 * @param $options
 *
 * @return false|string
 */
function myalbum_tag_block_top_edit($options)
{
    if (file_exists(XOOPS_ROOT_PATH . '/modules/tag/blocks/block.php')) {
        require_once XOOPS_ROOT_PATH . '/modules/tag/blocks/block.php';

        return tag_block_top_edit($options);
    }

    return false;
}
