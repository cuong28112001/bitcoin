<?php

/**
 * @package Duplicator
 */

defined("ABSPATH") or die("");

use Duplicator\Ajax\ServicesSchedule;
use Duplicator\Controllers\PackagesPageController;
use Duplicator\Controllers\SchedulePageController;
use Duplicator\Controllers\SettingsPageController;
use Duplicator\Controllers\ToolsPageController;
use Duplicator\Core\CapMng;
use Duplicator\Core\Controllers\ControllersManager;
use Duplicator\Models\Storages\AbstractStorageEntity;

/**
 * Variables
 *
 * @var Duplicator\Core\Controllers\ControllersManager $ctrlMng
 * @var Duplicator\Core\Views\TplMng $tplMng
 * @var array<string, mixed> $tplData
 * @var bool $blur
 */

$blur = $tplData['blur'];

$active_schedules = DUP_PRO_Schedule_Entity::get_active();
$active_count     = count($active_schedules);
$schedules        = DUP_PRO_Schedule_Entity::getAll();
$schedule_count   = DUP_PRO_Schedule_Entity::count();

$active_package     = DUP_PRO_Package::get_next_active_package();
$active_schedule_id = -1;

if ($active_package != null) {
    $active_schedule_id = $active_package->schedule_id;
}

$packagesPageUrl = PackagesPageController::getInstance()->getMenuLink();

$scheduleEditBaseURL   = SchedulePageController::getInstance()->getEditBaseUrl();
$scheduleCopyBaseURL   = SchedulePageController::getInstance()->getCopyActionUrl();
$settingsScheudleUrl   = ControllersManager::getMenuLink(
    ControllersManager::SETTINGS_SUBMENU_SLUG,
    SettingsPageController::L2_SLUG_SCHEDULE
);
$templatesPageUrl      = ToolsPageController::getInstance()->getMenuLink(ToolsPageController::L2_SLUG_TEMPLATE);
$tooltopCreatedContent = __(
    'Backup date and time expressed in UTC (Coordinated Universal Time). 
    The displayed date corresponds to the server\'s international time, independent of local time zones.',
    'duplicator-pro'
);

?>

<div class="dup-toolbar <?php echo ($blur ? 'dup-mock-blur' : ''); ?>">
    <label for="bulk_action" class="screen-reader-text">Select bulk action</label>
    <select id="bulk_action" class="small" >
        <option value="-1" selected="selected">
            <?php esc_html_e("Bulk Actions", 'duplicator-pro'); ?>
        </option>
        <option value="<?php echo (int) ServicesSchedule::SCHEDULE_BULK_ACTIVATE; ?>" title="Activate selected schedules(s)">
            <?php esc_html_e("Activate", 'duplicator-pro'); ?>
        </option>
        <option value="<?php echo (int) ServicesSchedule::SCHEDULE_BULK_DEACTIVATE; ?>" title="Deactivate selected schedules(s)">
            <?php esc_html_e("Deactivate", 'duplicator-pro'); ?>
        </option>
        <option value="<?php echo (int) ServicesSchedule::SCHEDULE_BULK_DELETE; ?>" title="Delete selected schedules(s)">
            <?php esc_html_e("Delete", 'duplicator-pro'); ?>
        </option>
    </select>
    <input 
        type="button" 
        id="dup-schedule-bulk-apply" 
        class="button hollow secondary small action" 
        value="<?php esc_attr_e("Apply", 'duplicator-pro') ?>" 
        onclick="DupPro.Schedule.BulkAction()"
    >
    <span class="separator"></span>
    <?php if (CapMng::can(CapMng::CAP_SETTINGS, false)) { ?>
        <a 
            href="<?php echo esc_url($settingsScheudleUrl); ?>" 
            class="button hollow secondary small dup-schedule-settings"
            title="<?php esc_attr_e("Settings", 'duplicator-pro') ?>"
        >
            <i class="fas fa-sliders-h fa-fw"></i>
        </a>
    <?php } ?>
    <a 
        href="<?php echo esc_url($templatesPageUrl); ?>" 
        id="btn-logs-dialog" 
        class="button hollow secondary small dup-schedule-templates" 
        title="<?php esc_attr_e("Templates", 'duplicator-pro') ?>"
    >
        <i class="far fa-clone"></i>
    </a>
</div>

<form 
    id="dup-schedule-form" 
    class="<?php echo ($blur ? 'dup-mock-blur' : ''); ?>"
    action="<?php echo esc_url(ControllersManager::getCurrentLink()); ?>" 
    method="post"
>
    <input type="hidden" id="dup-schedule-form-action" name="action" value="" />
    <input type="hidden" id="dup-schedule-selected-schedule" name="schedule_id" value="-1" />

    <!-- ====================
    LIST ALL SCHEDULES -->
    <table class="widefat storage-tbl dup-table-list valign-top schedule-tbl">
        <thead>
            <tr>
                <th style='width:10px;'><input type="checkbox" id="dpro-chk-all" title="Select all Backups" onclick="DupPro.Schedule.SetDeleteAll(this)"></th>
                <th style='width:275px;'><?php esc_html_e('Name', 'duplicator-pro'); ?></th>
                <th><?php esc_html_e('Storage', 'duplicator-pro'); ?></th>
                <th>
                    <?php esc_html_e('Runs Next', 'duplicator-pro'); ?>&nbsp;
                    <i 
                        class="fa-solid fa-circle-info"
                        data-tooltip-title="<?php esc_attr_e('Runs Next Date/Time', 'duplicator-pro'); ?>"
                        data-tooltip="<?php echo esc_attr($tooltopCreatedContent); ?>"
                    ></i>
                </th>
                <th>
                    <?php esc_html_e('Last Ran', 'duplicator-pro'); ?>&nbsp;
                    <i 
                        class="fa-solid fa-circle-info"
                        data-tooltip-title="<?php esc_attr_e('Last Ran Date/Time', 'duplicator-pro'); ?>"
                        data-tooltip="<?php echo esc_attr($tooltopCreatedContent); ?>"
                    ></i>
                </th>
                <th><?php esc_html_e('Active', 'duplicator-pro'); ?></th>
                <th class="dup-col-recovery" ><?php esc_html_e('Recovery', 'duplicator-pro'); ?></th>
            </tr>
        </thead>
        <tbody>
            <?php if ($schedule_count <= 0) : ?>
                <tr>
                    <td colspan="7" class="dup-schedules-no-schedule">
                        <div class="margin-top-1 margin-bottom-1" >
                            <h3 class="margin-bottom-0">
                                <b><i class="far fa-clock fa-sm"></i> <?php esc_html_e('No Schedules Found', 'duplicator-pro') ?></b>
                            </h3>
                            <a href="<?php echo esc_url($scheduleEditBaseURL); ?>">
                                [<?php esc_html_e('Add New Schedule', 'duplicator-pro') ?>]
                            </a>
                        </div>
                    </td>
                </tr>
            <?php endif; ?>

            <?php
            $i = 0;
            foreach ($schedules as $schedule) :
                $i++;
                $icon_display = (($schedule->getId() == $active_schedule_id) ? 'inline' : 'none');
                ?>
                <tr class="schedule-row <?php echo ($i % 2) ? 'alternate' : ''; ?>">
                    <td>
                        <input name="selected_id[]" type="checkbox" value="<?php echo (int) $schedule->getId() ?>" class="item-chk" />
                    </td>
                    <td>
                        <i 
                            id="<?php echo "icon-{" . (int) $schedule->getId() . "}-status"; ?>" 
                            class="fas fa-cog fa-spin schedule-status-icon" 
                            style="display:<?php echo esc_attr($icon_display); ?>; margin-right:4px;"
                            data-tooltip="<?php esc_attr_e('This scheduled backup is currently in progress', 'duplicator-pro'); ?>">
                        </i>
                        <a 
                            id="<?php echo "text-{" . (int) $schedule->getId() . "}"; ?>" 
                            href="javascript:void(0);" 
                            onclick="DupPro.Schedule.Edit('<?php echo (int) $schedule->getId() ?>');" 
                            class="name"
                        >
                            <?php echo esc_html($schedule->name); ?>
                        </a>
                        <div class="sub-menu">
                            <span class="link-style dup-schedule-quick-view" onclick="DupPro.Schedule.QuickView('<?php echo (int) $schedule->getId() ?>');">
                                <?php esc_html_e('Quick View', 'duplicator-pro'); ?>
                            </span> |
                            <span class="link-style dup-schedule-edit" onclick="DupPro.Schedule.Edit('<?php echo (int) $schedule->getId() ?>');">
                                <?php esc_html_e('Edit', 'duplicator-pro'); ?>
                            </span> |
                            <span class="link-style dup-schedule-copy" onclick="DupPro.Schedule.Copy('<?php echo (int) $schedule->getId(); ?>');">
                                <?php esc_html_e('Copy', 'duplicator-pro'); ?>
                            </span> |
                            <span class="link-style dup-schedule-delete" onclick="DupPro.Schedule.Delete('<?php echo (int) $schedule->getId(); ?>');">
                                <?php esc_html_e('Delete', 'duplicator-pro'); ?>
                            </span> |
                            <span class="link-style dup-schedule-run-now" onclick="DupPro.Schedule.RunNow('<?php echo (int) $schedule->getId(); ?>');">
                                <?php esc_html_e('Run Now', 'duplicator-pro'); ?>
                            </span>
                        </div>
                    </td>
                    <td>
                        <?php
                        if (count($schedule->storage_ids) > 0 && strlen(implode('', $schedule->storage_ids)) != 0) {
                            foreach ($schedule->storage_ids as $storage_id) {
                                if (($storage = AbstractStorageEntity::getById($storage_id)) === false) {
                                    continue;
                                }
                                echo esc_html($storage->getName());
                                echo '<br/>';
                            }
                        } else {
                            $txt_DeleteStorage = __('No Storage', 'duplicator-pro');
                            echo "<a href='javascript:void(0)' onclick='DupPro.Schedule.showDeleteStorageMessage()'>"
                                . "<i class='fa fa-info-circle fa-fw fa-sm'></i>"
                                . "<u>" . esc_html($txt_DeleteStorage) . "</u></a>";
                        }
                        ?>
                    </td>
                    <td>
                        <?php echo esc_html($schedule->get_next_run_time_string()); ?>
                    </td>
                    <td id="schedule-<?php echo (int) $schedule->getId() ?>-last-ran-string">
                        <?php echo esc_html($schedule->get_last_ran_string()); ?>
                    </td>
                    <td>
                        <b>
                            <?php if ($schedule->active) { ?>
                                <span class="green" ><?php esc_html_e('Yes', 'duplicator-pro'); ?></span>
                            <?php } else { ?>
                                <span class="maroon" ><?php esc_html_e('No', 'duplicator-pro'); ?></span>
                            <?php } ?>
                        </b>
                    </td>
                    <td class="dup-col-recovery" >
                        <?php $schedule->recoveableHtmlInfo(true); ?>
                    </td>
                </tr>
                <tr id='detail-<?php echo (int) $schedule->getId() ?>' class='<?php echo ($i % 2) ? 'alternate' : ''; ?> schedule-detail'>
                    <td colspan="7">
                        <?php
                        $template = DUP_PRO_Package_Template_Entity::getById($schedule->template_id);
                        ?>
                        <ul class="no-bullet">
                            <li>
                                <b><?php esc_html_e('Backup Template:', 'duplicator-pro'); ?></b>
                                <?php echo esc_html($template->name); ?>
                            </li>
                            <li>
                                <b><?php esc_html_e('Summary:', 'duplicator-pro'); ?></b>
                                <?php echo sprintf(esc_html__('Runs %1$s', 'duplicator-pro'), esc_html($schedule->get_repeat_text())); ?>
                            </li>
                            <li>
                                <b><?php esc_html_e('Last Ran:', 'duplicator-pro') ?></b>
                                <?php echo esc_html($schedule->get_last_ran_string()); ?>
                            </li>
                            <li>
                                <b><?php esc_html_e('Times Run:', 'duplicator-pro') ?></b>
                                <?php echo (int) $schedule->times_run; ?>
                            </li>
                        </ul>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
        <tfoot>
            <tr>
                <th colspan="7" style="text-align:right; white-space: nowrap; font-size:12px">
                    <?php
                    printf(
                        esc_html_x(
                            'Total: %1$s | Active: %2$s | Time: %3$s',
                            '%1$s represents total schedules, %2$s represents active schedules, %3$s represents current time',
                            'duplicator-pro'
                        ),
                        (int) $schedule_count,
                        (int) $active_count,
                        '<span id="dpro-clock-container"></span>'
                    );
                    ?>
                </th>
            </tr>
        </tfoot>
    </table>
</form>
<?php
$alert1          = new DUP_PRO_UI_Dialog();
$alert1->title   = __('Bulk Action Required', 'duplicator-pro');
$alert1->message = __('Please select an action from the "Bulk Actions" drop down menu!', 'duplicator-pro');
$alert1->initAlert();

$alert2                      = new DUP_PRO_UI_Dialog();
$alert2->title               = __('Selection Required', 'duplicator-pro');
$alert2->wrapperClassButtons = 'dpro-dlg-noschedule-sel-bulk-action-btns';
$alert2->message             = __('Please select at least one schedule to perform the action on!', 'duplicator-pro');
$alert2->initAlert();

$alert3           = new DUP_PRO_UI_Dialog();
$alert3->title    = __('No Storage', 'duplicator-pro');
$alert3->message  = __('All storage types associated with this schedule have been deleted.&nbsp;', 'duplicator-pro');
$alert3->message .= __('To enable this schedule please assign a valid storage type.', 'duplicator-pro');
$alert3->message .= '<br/><br/>';
$alert3->initAlert();

$confirm1                      = new DUP_PRO_UI_Dialog();
$confirm1->title               = __('Delete Schedule?', 'duplicator-pro');
$confirm1->wrapperClassButtons = 'dpro-dlg-delete-schedules-btns';
$confirm1->message             = __('Are you sure you want to delete the selected schedule(s)?', 'duplicator-pro');
$confirm1->message            .= '<br/>';
$confirm1->message            .= '<small><i>' . __('Note: This action removes all schedules.', 'duplicator-pro') . '</i></small>';
$confirm1->progressText        = __('Removing Schedules, Please Wait...', 'duplicator-pro');
$confirm1->jsCallback          = 'DupPro.Schedule.BulkDelete()';
$confirm1->initConfirm();

$confirm4                      = new DUP_PRO_UI_Dialog();
$confirm4->title               = __('Activate Schedule?', 'duplicator-pro');
$confirm4->wrapperClassButtons = 'dpro-dlg-activate-schedules-btns';
$confirm4->message             = __('Are you sure you want to activate the selected schedule(s)?', 'duplicator-pro');
$confirm4->message            .= '<br/>';
$confirm4->message            .= '<small><i>' . __('Note: This action activates all schedules.', 'duplicator-pro') . '</i></small>';
$confirm4->progressText        = __('Activating Schedules, Please Wait...', 'duplicator-pro');
$confirm4->jsCallback          = 'DupPro.Schedule.BulkActivate()';
$confirm4->initConfirm();

$confirm5                      = new DUP_PRO_UI_Dialog();
$confirm5->title               = __('Deactivate Schedule?', 'duplicator-pro');
$confirm5->wrapperClassButtons = 'dpro-dlg-deactivate-schedules-btns';
$confirm5->message             = __('Are you sure you want to deactivate the selected schedule(s)?', 'duplicator-pro');
$confirm5->message            .= '<br/>';
$confirm5->message            .= '<small><i>' . __('Note: This action deactivates all schedules.', 'duplicator-pro') . '</i></small>';
$confirm5->progressText        = __('Deactivating Schedules, Please Wait...', 'duplicator-pro');
$confirm5->jsCallback          = 'DupPro.Schedule.BulkDeactivate()';
$confirm5->initConfirm();

$confirm2               = new DUP_PRO_UI_Dialog();
$confirm2->title        = __('RUN SCHEDULE?', 'duplicator-pro');
$confirm2->message      = __('Are you sure you want to run schedule now?', 'duplicator-pro');
$confirm2->progressText = __('Running Schedule, Please Wait...', 'duplicator-pro');
$confirm2->jsCallback   = 'DupPro.Schedule.Run(this)';
$confirm2->initConfirm();

$confirm3               = new DUP_PRO_UI_Dialog();
$confirm3->title        = $confirm1->title;
$confirm3->message      = __('Are you sure you want to delete this schedule?', 'duplicator-pro');
$confirm3->progressText = $confirm1->progressText;
$confirm3->jsCallback   = 'DupPro.Schedule.DeleteThis(this)';
$confirm3->initConfirm();

$schedule_bulk_action_nonce = wp_create_nonce('duplicator_pro_schedule_bulk_action');
?>
<script>
    jQuery(document).ready(function ($) {

        /*METHOD: Shows quick view summary */
        DupPro.Schedule.QuickView = function (id) {
            $('#detail-' + id).toggle();
        };

        /*METHOD: Run the schedule now and redirect to backups page */
        DupPro.Schedule.RunNow = function (schedule_id) {
<?php $confirm2->showConfirm(); ?>
            $("#<?php echo esc_js($confirm2->getID()); ?>-confirm").attr('data-id', schedule_id);
        };

        DupPro.Schedule.Run = function (e) {
            var schedule_id = $(e).attr('data-id');

            $('#icon-' + schedule_id + '-status').show();
            $('#text-' + schedule_id).html("<?php esc_html_e('Queueing Now - Please Wait...', 'duplicator-pro') ?>");
            var data = {
                action: 'duplicator_pro_run_schedule_now',
                schedule_id: schedule_id,
                nonce: '<?php echo esc_js(wp_create_nonce('duplicator_pro_run_schedule_now')); ?>'
            }
            $.ajax({
                type: "POST",
                url: ajaxurl,
                timeout: 10000000,
                data: data
            }).done(function (respData) {
                try {
                    var data = DupPro.parseJSON(respData);
                } catch (err) {
                    console.error(err);
                    console.error('JSON parse failed for response data: ' + respData);
                    return false;
                }

                window.location.href = <?php echo wp_json_encode($packagesPageUrl) ?>;
            });
        };

        /*METHOD: Deletes a single schedule */
        DupPro.Schedule.Delete = function (id) {
<?php $confirm3->showConfirm(); ?>
            $("#<?php echo esc_js($confirm3->getID()); ?>-confirm").attr('data-id', id);
        };

        DupPro.Schedule.DeleteThis = function (e) {
            var id = $(e).attr('data-id');

            $.ajax({
                type: "POST",
                url: ajaxurl,
                dataType: "json",
                data: {
                    action: 'duplicator_pro_schedule_bulk_action',
                    perform: <?php echo (int) ServicesSchedule::SCHEDULE_BULK_DELETE; ?>,
                    schedule_ids: [id],
                    nonce: '<?php echo esc_js($schedule_bulk_action_nonce); ?>'
                },
            }).done(function (data) {
                $('#dup-schedule-form').submit();
            });
        };

        //  Creats a comma seperate list of all selected Backup ids
        DupPro.Schedule.SelectedList = function () {
            var arr = [];

            $("input[name^='selected_id[]']").each(function () {
                if ($(this).is(':checked')) {
                    arr.push($(this).val());
                }
            });

            return arr;
        };

        // Bulk delete
        DupPro.Schedule.BulkDelete = function () {
            var list = DupPro.Schedule.SelectedList();

            $.ajax({
                type: "POST",
                url: ajaxurl,
                dataType: "json",
                data: {
                    action: 'duplicator_pro_schedule_bulk_action',
                    perform: <?php echo (int) ServicesSchedule::SCHEDULE_BULK_DELETE; ?>,
                    schedule_ids: list,
                    nonce: '<?php echo esc_js($schedule_bulk_action_nonce); ?>'
                },
            }).done(function (data) {
                $('#dup-schedule-form').submit();
            });
        };

        // Bulk activate
        DupPro.Schedule.BulkActivate = function () {
            var list = DupPro.Schedule.SelectedList();

            $.ajax({
                type: "POST",
                url: ajaxurl,
                dataType: "json",
                data: {
                    action: 'duplicator_pro_schedule_bulk_action',
                    perform: <?php echo (int) ServicesSchedule::SCHEDULE_BULK_ACTIVATE; ?>,
                    schedule_ids: list,
                    nonce: '<?php echo esc_js($schedule_bulk_action_nonce); ?>'
                },
            }).done(function (data) {
                if (data.success) {
                    $('#dup-schedule-form').submit();
                } else {
                    if (data.message.length > 0) {
                        $('#<?php echo esc_js($confirm4->getID()); ?>-progress').hide();
                        $('#<?php echo esc_js($confirm4->getMessageID()); ?>').html(data.message);
                    }
                }
            });
        };

        // Bulk deactivate
        DupPro.Schedule.BulkDeactivate = function () {
            var list = DupPro.Schedule.SelectedList();

            $.ajax({
                type: "POST",
                url: ajaxurl,
                dataType: "json",
                data: {
                    action: 'duplicator_pro_schedule_bulk_action',
                    perform: <?php echo (int) ServicesSchedule::SCHEDULE_BULK_DEACTIVATE; ?>,
                    schedule_ids: list,
                    nonce: '<?php echo esc_js($schedule_bulk_action_nonce); ?>'
                },
            }).done(function (data) {
                $('#dup-schedule-form').submit();
            });
        };

        /*METHOD: Bulk action response */
        DupPro.Schedule.BulkAction = function () {
            var list = DupPro.Schedule.SelectedList();

            if (list.length == 0) {
                <?php $alert2->showAlert(); ?>
                return;
            }

            var action = $('#bulk_action').val(),
                checked = ($('.item-chk:checked').length > 0);

            if (action == -1 ) {
                <?php $alert1->showAlert(); ?>
                return;
            }

            if (checked) {
                switch (action) {
                    case '<?php echo (int) ServicesSchedule::SCHEDULE_BULK_DELETE; ?>':
                    <?php $confirm1->showConfirm(); ?>
                        break;
                    case '<?php echo (int) ServicesSchedule::SCHEDULE_BULK_ACTIVATE; ?>':
                    <?php $confirm4->showConfirm(); ?>
                        break;
                    case '<?php echo (int) ServicesSchedule::SCHEDULE_BULK_DEACTIVATE; ?>':
                    <?php $confirm5->showConfirm(); ?>
                        break;
                    default:
                    <?php $alert2->showAlert(); ?>
                        break;
                }
            }
        };

        /*METHOD: Edit a single schedule */
        DupPro.Schedule.Edit = function (id) {
            document.location.href = <?php echo json_encode($scheduleEditBaseURL); ?> + '&schedule_id=' + id;
        };

        /*METHOD: Copy a schedule */
        DupPro.Schedule.Copy = function (id) {
            document.location.href = <?php echo json_encode($scheduleCopyBaseURL); ?> + '&duppro-source-schedule-id=' + id;
        };

        /*METHOD: Set delete all */
        DupPro.Schedule.SetDeleteAll = function (chkbox) {
            $('.item-chk').each(function () {
                this.checked = chkbox.checked;
            });
        };

        /*METHOD: Shows the delete storage message*/
        DupPro.Schedule.showDeleteStorageMessage = function() {
           <?php $alert3->showAlert(); ?>
        };

        /*METHOD: Enableds the update flag to track proccessing */
        DupPro.Schedule.SetUpdateInterval = function (period) {
            console.log('setting interval to ' + period);
            if (DupPro.Schedule.setIntervalID != -1) {
                clearInterval(DupPro.Schedule.setIntervalID);
                DupPro.Schedule.setIntervalID = -1
            }
            DupPro.Schedule.setIntervalID = setInterval(DupPro.Schedule.UpdateSchedules, period * 1000);
        };

        /*METHOD: Checks the schedule status */
        DupPro.Schedule.UpdateSchedules = function () {

            var data = {
                action: 'duplicator_pro_get_schedule_infos',
                nonce: '<?php echo esc_js(wp_create_nonce('duplicator_pro_get_schedule_infos')); ?>'
            };

            $.ajax({
                type: "POST",
                url: ajaxurl,
                data: data,
                success: function (schedule_infos) {
                    activeSchedulePresent = false;
                    for (schedule_info_key in schedule_infos) {
                        var schedule_info = schedule_infos[schedule_info_key];
                        var is_running_selector = "#icon-" + schedule_info.schedule_id + "-status";
                        var last_ran_selector = "#schedule-" + schedule_info.schedule_id + "-last-ran-string";
                        if (schedule_info.is_running) {
                            $(is_running_selector).show();
                            activeSchedulePresent = true;
                        } else {
                            $(is_running_selector).hide();
                        }
                        $(last_ran_selector).text(schedule_info.last_ran_string);
                    }

                    if (activeSchedulePresent) {
                        DupPro.Schedule.SetUpdateInterval(10);
                    } else {

                        DupPro.Schedule.SetUpdateInterval(60);
                    }
                },
                error: function (data) {
                    console.log("error");
                    console.log(data);
                    $(".schedule-status-icon").css('display', 'none');
                    DupPro.Schedule.SetUpdateInterval(60);
                }
            });
        };

        DupPro.UI.Clock(DupPro._WordPressInitTime);
        DupPro.Schedule.setIntervalID = -1;
        DupPro.Schedule.UpdateSchedules();
    });
</script>
