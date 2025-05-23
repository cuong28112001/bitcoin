<?php

/**
 * Duplicator messages sections
 *
 * @package   Duplicator
 * @copyright (c) 2022, Snap Creek LLC
 */

use Duplicator\Core\Controllers\ControllersManager;
use Duplicator\Models\Storages\AbstractStorageEntity;
use Duplicator\Models\Storages\Local\DefaultLocalStorage;
use Duplicator\Models\Storages\StoragesUtil;

defined("ABSPATH") or die("");

/**
 * Variables
 *
 * @var \Duplicator\Core\Controllers\ControllersManager $ctrlMng
 * @var \Duplicator\Core\Views\TplMng  $tplMng
 * @var array<string, mixed> $tplData
 * @var AbstractStorageEntity $storage
 */
$storage = $tplData["storage"];
/** @var bool */
$blur = $tplData['blur'];
/** @var int */
$storage_id = $tplData["storage_id"];

$storage_tab_url = ControllersManager::getMenuLink(
    ControllersManager::STORAGE_SUBMENU_SLUG
);

$baseCopyUrl = ControllersManager::getMenuLink(
    ControllersManager::STORAGE_SUBMENU_SLUG,
    null,
    null,
    array(
        ControllersManager::QUERY_STRING_INNER_PAGE => 'edit',
        'action'                                    => $tplData['actions']['copy-storage']->getKey(),
        '_wpnonce'                                  => $tplData['actions']['copy-storage']->getNonce(),
        'storage_id'                                => $storage_id,
    )
);

if ($storage->getId() > 0) {
    $storages = AbstractStorageEntity::getAllBySType($storage->getSType());
} else {
    $storages = AbstractStorageEntity::getAll(0, 0, [StoragesUtil::class, 'sortByPriority']);
}

if ($storages === false) {
    $storages = [];
}

$storages = array_filter($storages, function (AbstractStorageEntity $s) use ($storage) {
    if ($s->getId() == $storage->getId()) {
        return false;
    }
    if ($s->getSType() == DefaultLocalStorage::getSType()) {
        return false;
    }
    return true;
});

$storage_count = count($storages);
?>
<div class="dup-toolbar <?php echo ($blur ? 'dup-mock-blur' : ''); ?>">
    <label for="dup-copy-source-id-select" class="screen-reader-text">Copy storage action</label>
    <select 
        id="dup-copy-source-id-select" 
        name="duppro-source-storage-id"
        class="small"
        <?php disabled($storage_count, 0); ?>
    >
        <option value="-1" selected="selected" disabled="true">
            <?php esc_html_e('Copy From', 'duplicator-pro'); ?>
        </option>
        <?php foreach ($storages as $copy_storage) { ?>
            <option value="<?php echo (int) $copy_storage->getId(); ?>">
                <?php echo esc_html($copy_storage->getName()); ?> [<?php echo esc_html($copy_storage->getStypeName()); ?>]
            </option>
        <?php } ?>
    </select>
    <input 
        type="button" 
        class="button hollow secondary small action" 
        value="<?php esc_attr_e("Apply", 'duplicator-pro') ?>" 
        onclick="DupPro.Storage.Copy()"
        <?php disabled($storage_count, 0); ?>
    >
    <span class="separator"></span>
    <a 
        href="<?php echo esc_url($storage_tab_url); ?>" 
        class="button hollow secondary small "
        title="<?php esc_attr_e('Back to storages list.', 'duplicator-pro'); ?>"
    > 
        <i class="fas fa-server fa-sm"></i> <?php esc_html_e('Storages', 'duplicator-pro'); ?>
    </a>
</div>

<hr class="dup-toolbar-divider"/>
<script>
    jQuery(document).ready(function ($) {
        // COMMON STORAGE RELATED METHODS
        DupPro.Storage.Copy = function ()
        {
            document.location.href = <?php echo json_encode($baseCopyUrl); ?> + 
                '&duppro-source-storage-id=' + $("#dup-copy-source-id-select option:selected").val();
        };
    });    
</script>
