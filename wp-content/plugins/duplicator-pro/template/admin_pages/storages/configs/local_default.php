<?php

/**
 * Duplicator messages sections
 *
 * @package   Duplicator
 * @copyright (c) 2022, Snap Creek LLC
 */

use Duplicator\Models\Storages\Local\LocalStorage;

defined("ABSPATH") or die("");

/**
 * Variables
 *
 * @var \Duplicator\Core\Controllers\ControllersManager $ctrlMng
 * @var \Duplicator\Core\Views\TplMng  $tplMng
 * @var array<string, mixed> $tplData
 * @var LocalStorage $storage
 */
$storage = $tplData["storage"];
/** @var int */
$maxPackages =  $tplData["maxPackages"];
/** @var bool  */
$purgePackages = $tplData["purgePackages"];
/** @var string */
$storageFolder = $tplData["storageFolder"];

$tplMng->render('admin_pages/storages/parts/provider_head');
?>
<tr valign="top">
    <th scope="row"><label><?php esc_html_e("Location", 'duplicator-pro'); ?></label></th>
    <td><?php echo esc_html($storageFolder); ?></td>
</tr>  
<tr>
<th scope="row"><label for=""><?php esc_html_e("Max Backups", 'duplicator-pro'); ?></label></th>
    <td>
        <div class="horizontal-input-row">
            <input 
                data-parsley-errors-container="#max_default_store_files_error_container" 
                id="max_default_store_files" 
                name="max_default_store_files" 
                type="text" 
                data-parsley-type="number" 
                data-parsley-min="0" 
                data-parsley-required="true" 
                value="<?php echo intval($maxPackages); ?>" 
                maxlength="4"
            >
            <label for="max_default_store_files">
                <?php esc_html_e("Number of Backups to keep in folder. ", 'duplicator-pro'); ?><br/>
            </label>
        </div>
        <i><?php esc_html_e("When this limit is exceeded, the oldest Backup will be deleted. Set to 0 for no limit.", 'duplicator-pro'); ?></i>
        <div id="max_default_store_files_error_container" class="duplicator-error-container"></div>
    </td>
</tr>
<tr>
    <th scope="row"><label for=""></label></th>
    <td>
        <div class="horizontal-input-row">
            <input 
                name="purge_default_package_record"
                <?php checked($purgePackages); ?> 
                class="checkbox" 
                value="1" 
                type="checkbox" 
                id="purge_default_package_record" 
            >
            <label for="purge_default_package_record">
                <i><?php esc_html_e("Delete associated Backup record when Max Backups limit is exceeded.", 'duplicator-pro'); ?></i>
            </label>
        </div>
    </td>
</tr>
<?php $tplMng->render('admin_pages/storages/parts/provider_foot'); ?>
