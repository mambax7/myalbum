<?php declare(strict_types=1);

/**
 * @param $options
 * @return array
 */
function myalbum0_tag_block_cloud_show($options): array
{
    if (file_exists(XOOPS_ROOT_PATH . '/modules/tag/blocks/block.php')) {
        //        global $module_dirname;
        $module_dirname = '';
        require_once XOOPS_ROOT_PATH . '/modules/tag/blocks/block.php';

        return tag_block_cloud_show($options, $module_dirname);
    }
}

/**
 * @param $options
 * @return string
 */
function myalbum0_tag_block_cloud_edit($options): string
{
    require_once XOOPS_ROOT_PATH . '/modules/tag/blocks/block.php';

    return tag_block_cloud_edit($options);
}

/**
 * @param $options
 * @return array
 */
function myalbum0_tag_block_top_show($options): array
{
    if (file_exists(XOOPS_ROOT_PATH . '/modules/tag/blocks/block.php')) {
        //        global $module_dirname;
        $module_dirname = '';
        require_once XOOPS_ROOT_PATH . '/modules/tag/blocks/block.php';

        return tag_block_top_show($options, $module_dirname);
    }

    return;
}

/**
 * @param $options
 *
 * @return string|false
 */
function myalbum0_tag_block_top_edit($options)
{
    require_once XOOPS_ROOT_PATH . '/modules/tag/blocks/block.php';

    return tag_block_top_edit($options);
}
