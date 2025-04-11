<?php

/**
 * @package Duplicator
 */

use Duplicator\Core\Controllers\ControllersManager;
use Duplicator\Core\Views\TplMng;
use Duplicator\Controllers\SettingsPageController;
use Duplicator\Core\Controllers\PageAction;

defined("ABSPATH") or die("");

/**
 * Variables
 *
 * @var Duplicator\Core\Controllers\ControllersManager $ctrlMng
 * @var Duplicator\Core\Views\TplMng $tplMng
 * @var array<string, mixed> $tplData
 */

$global = DUP_PRO_Global_Entity::getInstance();

/** @var PageAction */
$resetAction = $tplData['actions'][SettingsPageController::ACTION_RESET_SETTINGS];

?>
<?php do_action('duplicator_settings_general_before'); ?>

<form id="dup-settings-form" action="<?php echo esc_url(ControllersManager::getCurrentLink()); ?>" method="post" data-parsley-validate>
    <?php $tplData['actions'][SettingsPageController::ACTION_GENERAL_SAVE]->getActionNonceFileds(); ?>

    <div class="dup-settings-wrapper margin-bottom-1" >
        <?php $tplMng->render('admin_pages/settings/general/plugin_settings'); ?>
        <hr>
        <?php TplMng::getInstance()->render('admin_pages/settings/general/email_summary'); ?>
        <?php TplMng::getInstance()->render('admin_pages/settings/general/debug_settings'); ?>
        <?php TplMng::getInstance()->render('admin_pages/settings/general/advanced_settings'); ?>
    </div>

    <p>
        <input 
            type="submit" name="submit" id="submit" 
            class="button primary small" 
            value="<?php esc_attr_e('Save Settings', 'duplicator-pro') ?>"
        >
    </p>
</form>

<?php
$resetSettingsDialog                 = new DUP_PRO_UI_Dialog();
$resetSettingsDialog->title          = __('Reset Settings?', 'duplicator-pro');
$resetSettingsDialog->message        = __('Are you sure you want to reset settings to defaults?', 'duplicator-pro');
$resetSettingsDialog->progressText   = __('Resetting settings, Please Wait...', 'duplicator-pro');
$resetSettingsDialog->jsCallback     = 'DupPro.Pack.ResetAll()';
$resetSettingsDialog->progressOn     = false;
$resetSettingsDialog->okText         = __('Yes', 'duplicator-pro');
$resetSettingsDialog->cancelText     = __('No', 'duplicator-pro');
$resetSettingsDialog->closeOnConfirm = true;
$resetSettingsDialog->initConfirm();

$resetPackagesDialog                 = new DUP_PRO_UI_Dialog();
$resetPackagesDialog->title          = __('Reset Backups ?', 'duplicator-pro');
$resetPackagesDialog->message        = __('This will clear and reset all of the current temporary Backups.  Would you like to continue?', 'duplicator-pro');
$resetPackagesDialog->progressText   = __('Resetting settings, Please Wait...', 'duplicator-pro');
$resetPackagesDialog->jsCallback     = 'DupPro.Pack.ResetPackages()';
$resetPackagesDialog->progressOn     = false;
$resetPackagesDialog->okText         = __('Yes', 'duplicator-pro');
$resetPackagesDialog->cancelText     = __('No', 'duplicator-pro');
$resetPackagesDialog->closeOnConfirm = true;
$resetPackagesDialog->initConfirm();

$msg_ajax_error                 = new DUP_PRO_UI_Messages(
    __('AJAX ERROR!', 'duplicator-pro') . '<br>' . __('Ajax request error', 'duplicator-pro'),
    DUP_PRO_UI_Messages::ERROR
);
$msg_ajax_error->hide_on_init   = true;
$msg_ajax_error->is_dismissible = true;
$msg_ajax_error->initMessage();

$msg_response_error                 = new DUP_PRO_UI_Messages(__('RESPONSE ERROR!', 'duplicator-pro'), DUP_PRO_UI_Messages::ERROR);
$msg_response_error->hide_on_init   = true;
$msg_response_error->is_dismissible = true;
$msg_response_error->initMessage();

$msg_response_success                 = new DUP_PRO_UI_Messages('', DUP_PRO_UI_Messages::NOTICE);
$msg_response_success->hide_on_init   = true;
$msg_response_success->is_dismissible = true;
$msg_response_success->initMessage();
?>

<script>
    jQuery(document).ready(function($) {
        // which: 0=installer, 1=archive, 2=sql file, 3=log
        DupPro.Pack.DownloadTraceLog = function() {
            var actionLocation = ajaxurl + '?action=duplicator_pro_get_trace_log&nonce=' 
            + '<?php echo esc_js(wp_create_nonce('duplicator_pro_get_trace_log')); ?>';
            location.href = actionLocation;
        };

        DupPro.Pack.ConfirmResetAll = function() {
            <?php $resetSettingsDialog->showConfirm(); ?>
        };

        DupPro.Pack.ConfirmResetPackages = function() {
            <?php $resetPackagesDialog->showConfirm(); ?>
        };

        DupPro.Pack.ResetAll = function() {
            let resetUrl = <?php echo wp_json_encode($resetAction->getUrl()); ?>;
            location.href = resetUrl;
        };

        DupPro.Pack.ResetPackages = function() {
            $.ajax({
                type: "POST",
                url: ajaxurl,
                dataType: "json",
                data: {
                    action: 'duplicator_pro_reset_packages',
                    nonce: '<?php echo esc_js(wp_create_nonce('duplicator_pro_reset_packages')); ?>'
                },
                success: function(result) {
                    if (result.success) {
                        var message = '<?php esc_html_e('Backups successfully reset', 'duplicator-pro'); ?>';
                        <?php
                        $msg_response_success->updateMessage('message');
                        $msg_response_success->showMessage();
                        ?>
                    } else {
                        var message = '<?php esc_html_e('RESPONSE ERROR!', 'duplicator-pro'); ?>' + '<br><br>' + result.data.message;
                        <?php
                        $msg_response_error->updateMessage('message');
                        $msg_response_error->showMessage();
                        ?>
                    }
                },
                error: function(result) {
                    <?php $msg_ajax_error->showMessage(); ?>
                }
            });
        };

        //Init
        $("#_trace_log_enabled").click(function() {
            $('#_send_trace_to_error_log').attr('disabled', !$(this).is(':checked'));
        });

    });
</script>
