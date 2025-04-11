<?php

/**
 * @package Duplicator
 */

defined("ABSPATH") or die("");

use Duplicator\Libs\Snap\SnapIO;

/**
 * Variables
 *
 * @var Duplicator\Core\Controllers\ControllersManager $ctrlMng
 * @var Duplicator\Core\Views\TplMng $tplMng
 * @var array<string, mixed> $tplData
 */

$global = DUP_PRO_Global_Entity::getInstance();
?>
<h3 class="title">
    <?php esc_html_e("Storage Settings", 'duplicator-pro'); ?>
</h3>
<hr>
<label class="lbl-larger" >
    <?php esc_html_e("Storage", 'duplicator-pro'); ?>
</label>
<div class="margin-bottom-1" >
    <p>
        <?php esc_html_e("Default Local Storage Path", 'duplicator-pro'); ?>:
        <b>
            <?php echo esc_html(SnapIO::safePath(DUPLICATOR_PRO_SSDIR_PATH)); ?>
        </b>
    </p>

    <input 
        type="checkbox" 
        name="_storage_htaccess_off" 
        id="_storage_htaccess_off" 
        value="1"
        class="margin-0"
        <?php checked($global->storage_htaccess_off); ?> 
    >
    <label for="_storage_htaccess_off">
        <?php esc_html_e("Disable .htaccess File In Storage Directory", 'duplicator-pro') ?> 
    </label>
    <p class="description">
        <?php esc_html_e("Disable if issues occur when downloading installer/archive files.", 'duplicator-pro'); ?>
    </p>
</div>

<label class="lbl-larger" >
    <?php esc_html_e("Max Retries", 'duplicator-pro'); ?>
</label>
<div class="margin-bottom-1" >
    <input 
        class="width-small margin-0" 
        type="text" 
        name="max_storage_retries" 
        id="max_storage_retries" 
        data-parsley-required data-parsley-min="0" 
        data-parsley-type="number" 
        data-parsley-errors-container="#max_storage_retries_error_container" 
        value="<?php echo (int) $global->max_storage_retries; ?>" 
    >
    <div id="max_storage_retries_error_container" class="duplicator-error-container"></div>
    <p class="description">
        <?php esc_html_e('Max upload/copy retries to attempt after failure encountered.', 'duplicator-pro'); ?>
    </p>
</div>