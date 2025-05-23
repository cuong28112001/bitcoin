<?php

/**
 * Duplicator Backup row in table Backups list
 *
 * @package   Duplicator
 * @copyright (c) 2022, Snap Creek LLC
 */

defined("ABSPATH") or die("");

use Duplicator\Controllers\PackagesPageController;
use Duplicator\Controllers\StoragePageController;
use Duplicator\Controllers\ToolsPageController;
use Duplicator\Core\Controllers\ControllersManager;
use Duplicator\Libs\Snap\SnapJson;

/**
 * Variables
 *
 * @var \Duplicator\Core\Controllers\ControllersManager $ctrlMng
 * @var \Duplicator\Core\Views\TplMng  $tplMng
 * @var array<string, mixed> $tplData
 */

$perPage     = $tplData['perPage'];
$offset      = $tplData['offset'];
$currentPage = $tplData['currentPage'];

$tplMng->render('admin_pages/tools/recovery/widget/recovery-widget-scripts');
$tplMng->render('admin_pages/packages/remote_download/scripts');

$transferBaseUrl   = PackagesPageController::getInstance()->getPackageTransferUrl();
$reloadPackagesURL = $ctrlMng->getCurrentLink(
    ['paged' => $currentPage]
);

/* ------------------------------------------
 * ALERT:  Remote > Storage items          */
$remoteDlg           = new DUP_PRO_UI_Dialog();
$remoteDlg->width    = 750;
$remoteDlg->height   = 475;
$remoteDlg->title    = __('Storage Locations', 'duplicator-pro');
$remoteDlg->message  = __('Loading. Please Wait...', 'duplicator-pro');
$remoteDlg->boxClass = 'dup-packs-remote-store-dlg';
$remoteDlg->initAlert();

/* ------------------------------------------
 * ALERT:  Bulk action > no selection      */
$alert1               = new DUP_PRO_UI_Dialog();
$alert1->title        = __('Bulk Action Required', 'duplicator-pro');
$alert1->templatePath = 'parts/dialogs/contents/bulk-action-not-selected';
$alert1->initAlert();

/* ------------------------------------------
 * ALERT:  Bulk action > no backup selected  */
$alert2                      = new DUP_PRO_UI_Dialog();
$alert2->title               = __('Selection Required', 'duplicator-pro');
$alert2->wrapperClassButtons = 'dpro-dlg-nopackage-sel-bulk-action-btns';
$alert2->templatePath        = 'parts/dialogs/contents/bulk-action-delete-not-selected';
$alert2->initAlert();

/* ------------------------------------------
 * ALERT: Process > Error undefined        */
$alert4          = new DUP_PRO_UI_Dialog();
$alert4->title   = __('ERROR!', 'duplicator-pro');
$alert4->message = __('Got an error or a warning: undefined', 'duplicator-pro');
$alert4->initAlert();

/* ------------------------------------------
 * ALERT: Process > Error no details       */
$alert5          = new DUP_PRO_UI_Dialog();
$alert5->title   = $alert4->title;
$alert5->message = __('Failed to get details.', 'duplicator-pro');
$alert5->initAlert();

/* ------------------------------------------
 * CONFIRM: Delete Backups?               */
$confirm1                      = new DUP_PRO_UI_Dialog();
$confirm1->height              = 280;
$confirm1->title               = __('Delete Backups?', 'duplicator-pro');
$confirm1->wrapperClassButtons = 'dpro-dlg-detete-packages-btns';
$confirm1->message             = __('Are you sure you want to delete the selected Backup(s)?', 'duplicator-pro');
$confirm1->message            .= '<br/><br/>';
$confirm1->message            .= '<small><i>' . __(
    'Note: This action removes only Backups located on this server. If a remote Backup was created then it will not be removed or affected.',
    'duplicator-pro'
) . '</i></small>';
$confirm1->progressText        = __('Removing Backups, Please Wait...', 'duplicator-pro');
$confirm1->jsCallback          = 'DupPro.Pack.Delete()';
$confirm1->initConfirm();

/* ------------------------------------------
 * ALERT: Recovery > toolbar button        */
$toolBarRecoveryButtonInfo               = new DUP_PRO_UI_Dialog();
$toolBarRecoveryButtonInfo->showButtons  = false;
$toolBarRecoveryButtonInfo->height       = 600;
$toolBarRecoveryButtonInfo->width        = 600;
$toolBarRecoveryButtonInfo->title        = __('Disaster Recovery', 'duplicator-pro');
$toolBarRecoveryButtonInfo->templatePath = 'admin_pages/packages/recovery_info/info';
$toolBarRecoveryButtonInfo->initAlert();

/* ------------------------------------------
 * ALERT: Recovery                         */
$availableRecoveryBox              = new DUP_PRO_UI_Dialog();
$availableRecoveryBox->title       = __('Disaster Recovery Available', 'duplicator-pro');
$availableRecoveryBox->boxClass    = 'dup-recovery-box-info';
$availableRecoveryBox->showButtons = false;
$availableRecoveryBox->width       = 600;
$availableRecoveryBox->height      = 400;
$availableRecoveryBox->message     = '';
$availableRecoveryBox->initAlert();

$unavailableRecoveryBox              = new DUP_PRO_UI_Dialog();
$unavailableRecoveryBox->title       = __('Disaster Recovery Unavailable', 'duplicator-pro');
$unavailableRecoveryBox->boxClass    = 'dup-recovery-box-info';
$unavailableRecoveryBox->showButtons = false;
$unavailableRecoveryBox->width       = 600;
$unavailableRecoveryBox->height      = 700;
$unavailableRecoveryBox->message     = '';
$unavailableRecoveryBox->initAlert();

/* ------------------------------------------
 * ALERT: Package overeview > Help   */
$linkInfoDlg               = new DUP_PRO_UI_Dialog();
$linkInfoDlg->width        = 700;
$linkInfoDlg->height       = 550;
$linkInfoDlg->title        = __('Duplicator Pro Tutorial', 'duplicator-pro');
$linkInfoDlg->templatePath = 'admin_pages/packages/packages_overview_help';
$linkInfoDlg->initAlert();

$baseStorageEditURL = StoragePageController::getInstance()->getMenuLink(
    null,
    null,
    [
        ControllersManager::QUERY_STRING_INNER_PAGE => StoragePageController::INNER_PAGE_EDIT,
    ]
);
?>
<script>
jQuery(document).ready(function($) {
    
    DupPro.Pack.RestorePackageId = null;
    DupPro.PackagesTable = $('.dup-packtbl');
     
    /**
     * Click event to expands each row and show Backup details
     *
     * @returns void
     */
    $('th#dup-header-chkall').on('click', function() {
        var $this = $(this);
        var $icon = $this.find('i');
        if ($icon.hasClass('fa-plus')) {
            $icon.removeClass('fa-plus').addClass('fa-minus');
            $("tr.dup-row-complete").each(function() {
                $(this).find('.dup-cell-toggle-btn i').removeClass('fa-plus').addClass('fa-minus');
                $(this).next('tr').removeClass('no-display');
            });
        } else {
            $icon.removeClass('fa-minus').addClass('fa-plus');
            $("tr.dup-row-complete").each(function() {
                $(this).find('.dup-cell-toggle-btn i').removeClass('fa-minus').addClass('fa-plus');
                $(this).next('tr').addClass('no-display');
            });
        }
    });

    /**
     * Click event to expands each row and show Backup details
     *
     * @returns void
     */
    $('td.dup-cell-toggle-btn').on('click', function(e) {
        var $this = $(this);
        var $icon  = $this.find('i');
        if ($icon.hasClass('fa-plus')) {
            $icon.removeClass('fa-plus').addClass('fa-minus');
            $(this).closest('tr').next('tr').removeClass('no-display');
        } else {
            $icon.removeClass('fa-minus').addClass('fa-plus');
            $(this).closest('tr').next('tr').addClass('no-display');
        }
    });

    $('.dup-pro-quick-fix-notice').on('click', '.dup-pro-quick-fix', function() {
        var $this = $(this),
            params = JSON.parse($this.attr('data-param')),
            toggle = $this.attr('data-toggle'),
            id = $this.attr('data-id'),
            fix = $(toggle),
            button = {
                loading: function() {
                    $this.prop('disabled', true)
                        .addClass('disabled')
                        .html('<i class="fas fa-circle-notch fa-spin fa-fw"></i> <?php esc_html_e('Please Wait...', 'duplicator-pro') ?>');
                },
                reset: function() {
                    $this.prop('disabled', false)
                        .removeClass('disabled')
                        .html("<i class='fa fa-wrench' aria-hidden='true'></i>&nbsp; <?php esc_html_e('Resolve This', 'duplicator-pro') ?>");
                }
            },
            error = {
                message: function(text) {
                    fix.append(
                        "&nbsp; <span style='color:#cc0000' id='" + 
                        toggle.replace('#', '') + 
                        "-error'><i class='fa fa-exclamation-triangle'></i>&nbsp; " + text + "</span>"
                    );
                },
                remove: function() {
                    if ($(toggle + "-error"))
                        $(toggle + "-error").remove();
                }
            };

        error.remove();
        button.loading();

        Duplicator.Util.ajaxWrapper(
           {
                action: 'duplicator_pro_quick_fix',
                setup: params,
                id: id,
                nonce: '<?php echo esc_js(wp_create_nonce('duplicator_pro_quick_fix')); ?>'
            },
            function (result, data, funcData, textStatus, jqXHR) {
                if (funcData.success) {
                    fix.remove();

                    // If there is no fixes and notifications - remove container
                    if (typeof funcData.recommended_fixes != 'undefined') {
                        if (funcData.recommended_fixes == 0) {
                            $('.dup-pro-quick-fix-notice').remove();
                        }
                    }

                    DupPro.addAdminMessage(
                        "<?php esc_html_e('Successfully applied quick fix!', 'duplicator-pro'); ?>",
                        'success',
                        {hideDelay: 5000}
                    );
                } else {
                    button.reset();
                    error.message(funcData.message);
                }
                return '';
            },
            function (result, data, funcData, textStatus, jqXHR) {
                button.reset();
                error.message('<?php esc_html_e('Unexpected Error!', 'duplicator-pro') ?>');
                console.log(data);
                return '';
            }
        );
    });

    $('.dpro-toolbar-recovery-info').click(function () {
        if ($(this).hasClass('dup-recovery-unset')) {
            <?php $toolBarRecoveryButtonInfo->showAlert(); ?>
        } else {
            let openUrl = <?php echo json_encode($ctrlMng->getMenuLink($ctrlMng::TOOLS_SUBMENU_SLUG, ToolsPageController::L2_SLUG_RECOVERY)); ?>;
            window.open(openUrl,"_self");
        }
    });

    //DOWNLOAD MENU
    $('button.dup-dnload-btn').click(function(e) {
        var $menu = $(this).parent().find('nav.dup-dnload-menu-items');

        if ($menu.is(':visible')) {
            $menu.addClass('no-display');
        } else {
            $('nav.dup-dnload-menu-items').addClass('no-display');
            $menu.removeClass('no-display');
        }
        return false;
    });

    $(document).click(function(e) {
        var className = e.target.className;
        if (className != 'dpro-menu-x') {
            $('nav.dup-dnload-menu-items').addClass('no-display');
        }
    });

    $("nav.dup-dnload-menu-items button").each(function() {
        $(this).addClass('dpro-menu-x');
    });
    $("nav.dup-dnload-menu-items button span").each(function() {
        $(this).addClass('dpro-menu-x');
    });

    /*  Creats a comma seperate list of all selected Backup ids  */
    DupPro.Pack.GetDeleteList = function() {
        var arr = [];
        $("input[name=delete_confirm]:checked").each(function() {
            arr.push(this.id);
        });
        return arr;
    }

    DupPro.Pack.BackupRestore = function() {
        Duplicator.Util.ajaxWrapper({
                action: 'duplicator_pro_restore_backup_prepare',
                packageId: DupPro.Pack.RestorePackageId,
                nonce: '<?php echo esc_js(wp_create_nonce('duplicator_pro_restore_backup_prepare')); ?>'
            },
            function(result, data, funcData, textStatus, jqXHR) {
                window.location.href = data.funcData;
            },
            function(result, data, funcData, textStatus, jqXHR) {
                alert('FAIL');
            }
        );
    };

    /*  Provides the correct confirmation items when deleting Backups */
    DupPro.Pack.ConfirmDelete = function() {
        $('#dpro-dlg-confirm-delete-btns input').removeAttr('disabled');
        if ($("#dup-pack-bulk-actions").val() != "delete") {
            <?php $alert1->showAlert(); ?>
            return;
        }

        var list = DupPro.Pack.GetDeleteList();
        if (list.length == 0) {
            <?php $alert2->showAlert(); ?>
            return;
        }
        <?php $confirm1->showConfirm(); ?>
    }

    /*  Removes all selected Backup sets with ajax call  */
    DupPro.Pack.Delete = function() {
        var packageIds = DupPro.Pack.GetDeleteList();
        var pageCount = $('#current-page-selector').val();
        var pageItems = $('input[name="delete_confirm"]');
        
        Duplicator.Util.ajaxWrapper(
            {
                action: 'duplicator_pro_package_delete',
                package_ids: packageIds,
                nonce: '<?php echo esc_js(wp_create_nonce('duplicator_pro_package_delete')); ?>'
            },
            function (result, data, funcData, textStatus, jqXHR) {
                //Increment back a page-set if no items are left
                if ($('#form-duplicator-nav').length) {
                    if (pageItems.length == packageIds.length)
                        $('#current-page-selector').val(pageCount - 1);
                    $('#form-duplicator-nav').submit();
                } else {
                    window.location.reload();
                }
                return '';
            },
            function (result, data, funcData, textStatus, jqXHR) {
                DupPro.addAdminMessage(okMsgContent, 'notice');
                return '';
            }
        );
    }

    /* Toogles the Bulk Action Check boxes */
    DupPro.Pack.SetDeleteAll = function() {
        var state = $('input#dup-chk-all').is(':checked') ? 1 : 0;
        $("input[name=delete_confirm]").each(function() {
            this.checked = (state) ? true : false;
        });
    }

    /* Stops the build from running */
    DupPro.Pack.StopBuild = function(packageID) {
        $('#stop-backup-id').val(packageID);
        $('#form-duplicator').submit();
        
        $('.dup-build-stop-btn').html('<?php esc_html_e("Cancelling...", 'duplicator-pro'); ?>');
        $('.dup-build-stop-btn').prop('disabled', true);
    }

    /*  Redirects to the Backups detail screen using the Backup id */
    DupPro.Pack.OpenPackTransfer = function(id) {
        window.location.href = '<?php echo esc_url_raw(SnapJson::jsonEncode($transferBaseUrl)) ?>' + '&id=' + id;
    }

    /* Shows remote storage location dialogs */
    DupPro.Pack.ShowRemote = function(package_id, name) {
        <?php $remoteDlg->showAlert(); ?>
        
        Duplicator.Util.ajaxWrapper(
            {
                action: 'duplicator_pro_get_storage_details',
                package_id: package_id,
                nonce: '<?php echo esc_js(wp_create_nonce('duplicator_pro_get_storage_details')); ?>'
            },
            function (result, data, funcData, textStatus, jqXHR) {
                if (!funcData.success) {
                    var text = "<?php esc_html_e('Got an error or a warning', 'duplicator-pro'); ?>: " + funcData.message;
                    $('#TB_window .dpro-dlg-alert-txt').html(text);
                    return false;
                }

                var info = '<div class="dup-dlg-store-remote">';
                for (storage_provider_key in funcData.storage_providers) {
                    var store = funcData.storage_providers[storage_provider_key];
                    info += store.infoHTML;
                }
                info += '</div>';
                info += "<small><a href='" + funcData.logURL + "' class='dup-dlg-store-log-link' target='_blank'>" + 
                    '<?php echo esc_html__('[Backup Build Log]', 'duplicator-pro'); ?>' + "</a></small>";
                $('#TB_window .dpro-dlg-alert-txt').html(info);
            },
            function(data) {
                <?php $alert5->showAlert(); ?>
                console.log(data);
                return '';
            }
        );
        
        return false;
    };

    $('.dup-restore-backup').click(function(event) {
        event.preventDefault();

        let packageId = $(this).data('package-id');
        if ($(this).data('needs-download') == true ) {
            DupPro.Pack.ShowRemoteDownloadOptions(packageId, 'restore');
        } else {
            DupPro.Pack.ShowRestoreModal(packageId);
        }
    });

    $('.dup-remote-download').click(function(event) {
        event.preventDefault();
        let packageId = $(this).data('package-id');
        DupPro.Pack.ShowRemoteDownloadOptions(packageId, 'download');
    });

    DupPro.Pack.ShowRestoreModal = function(packageId) {
        Duplicator.Util.ajaxWrapper(
            {
                action: 'duplicator_pro_backup_redirect',
                packageId: packageId,
                nonce: '<?php echo esc_js(wp_create_nonce('duplicator_pro_backup_redirect')); ?>'
            },
            function (result, data, funcData, textStatus, jqXHR) {
                if (funcData.success) {
                    let box = new DuplicatorModalBox({
                        url: data.funcData.redirect_url, 
                        openCallback: function (iframe, modalObj) {
                            let body = $(iframe.contentWindow.document.body);
                            // For old Backups
                            body.find("#content").css('background-color', 'white');

                            body.on( "click", "#s1-deploy-btn", function() {
                                modalObj.disableClose();
                            });
                        }
                    });
                    box.open();
                    //window.location.href = data.funcData.redirect_url;
                } else {
                    DupPro.addAdminMessage(funcData.message, 'error');
                }
                return '';        
            },
            function(data) {
                <?php $alert5->showAlert(); ?>
                console.log(data);
                return '';
            }
        );
        return false;
    }

    <?php if (isset($tplData['triggerRestore']) && $tplData['triggerRestore'] !== -1) : ?>
        setTimeout(function() {
            DupPro.Pack.ShowRestoreModal(<?php echo (int) $tplData['triggerRestore']; ?>);
        }, 500);
    <?php endif; ?>

    /*  Virtual states that UI uses for easier tracking of the three general states a Backup can be in*/
    DupPro.Pack.ProcessingStats = {
        PendingCancellation: -3,
        Pending: 0,
        Building: 1,
        Storing: 2,
        Finished: 3,
    }

    DupPro.Pack.setIntervalID = -1;

    DupPro.Pack.SetUpdateInterval = function(period) {
        if (DupPro.Pack.setIntervalID != -1) {
            clearInterval(DupPro.Pack.setIntervalID);
            DupPro.Pack.setIntervalID = -1
        }
        DupPro.Pack.setIntervalID = setInterval(DupPro.Pack.UpdateUnfinishedPackages, period * 1000);
    }

    DupPro.Pack.UpdateUnfinishedPackages = function() {
        let packagesTables = $('.dup-packtbl');

        var data = {
            action: 'duplicator_pro_get_package_statii',
            nonce: '<?php echo esc_js(wp_create_nonce("duplicator_pro_get_package_statii")); ?>',
            offset: <?php echo (int) $offset; ?>,
            limit: <?php echo (int) $perPage; ?>,
        }

        $.ajax({
            type: "POST",
            url: ajaxurl,
            timeout: 10000000,
            data: data,
            complete: function() {},
            success: function(result) {
                let packagesTable = $('.dup-packtbl');
                let currentFirstPackageId = -1;
                let statiiFistPackageId = -1;
                if (packagesTables.find('.dup-row').length) {
                    currentFirstPackageId = packagesTables.find('.dup-row').first().data('package-id');
                }

                let statusInfo = result.data.funcData;
                if (statusInfo.length) {
                    statiiFistPackageId = statusInfo[0].ID;
                }

                if (currentFirstPackageId != statiiFistPackageId) {
                    window.location = <?php echo wp_json_encode($reloadPackagesURL); ?>;
                }

                let activePackagePresent = false;

                for (package_info_key in statusInfo) {
                    let package_info = statusInfo[package_info_key];
                    let statusNode = packagesTable.find('.status-' + package_info.ID);
                    let sizeNode = packagesTable.find('#dup-row-pack-id-' + package_info.ID + ' .dup-size-column');

                    let currentStatus = parseInt(statusNode.text());
                    let currentProcessingState;

                    if (currentStatus == -3) {
                        currentProcessingState = DupPro.Pack.ProcessingStats.PendingCancellation;
                    } else if (currentStatus == 0) {
                        currentProcessingState = DupPro.Pack.ProcessingStats.Pending;
                    } else if ((currentStatus >= 0) && (currentStatus < 75)) {
                        currentProcessingState = DupPro.Pack.ProcessingStats.Building;
                    } else if ((currentStatus >= 75) && (currentStatus < 100)) {
                        currentProcessingState = DupPro.Pack.ProcessingStats.Storing;
                    } else {
                        // Has to be negative(error) or 100 - both mean complete
                        currentProcessingState = DupPro.Pack.ProcessingStats.Finished;
                    }
                    if (currentProcessingState == DupPro.Pack.ProcessingStats.Pending) {
                        if (package_info.status != 0) {
                            window.location = window.location.href;
                        }
                    } else if (currentProcessingState == DupPro.Pack.ProcessingStats.Building) {
                        if (package_info.status >= 75 || (package_info.status < 0)) {
                            window.location = window.location.href;
                            break;
                        } else {
                            statusNode.text(package_info.status);
                            sizeNode.hide().fadeIn(1000).text(package_info.size);
                        }
                    } else if (currentProcessingState == DupPro.Pack.ProcessingStats.Storing) {
                        var statusProgressSelector = '.status-progress-' + package_info.ID;
                        if (package_info.status == 100 || package_info.status < 0) {
                            if (DupPro.Pack.IsRemoteDownloadModalOpen() && package_info.status == 100) {
                                // do a dynamic form submission to start the restore/download
                                $(statusProgressSelector).text(100);
                                DupPro.Pack.afterRemoteDownloadAction();
                            } else {
                                // Transitioned to storing so refresh
                                window.location = window.location.href;
                            }
                            break;
                        } else {
                            $('.dpro-progress-status-message-transfer-msg').html(package_info.status_progress_text);
                            $(statusProgressSelector).text(package_info.status_progress);
                            console.log("status progress: " + package_info.status_progress);
                        }
                    } else if (currentProcessingState == DupPro.Pack.ProcessingStats.PendingCancellation) {
                        if ((package_info.status == -2) || (package_info.status == -4)) {
                            // refresh when its gone to cancelled
                            window.location = window.location.href;
                        }
                    } else if (currentProcessingState == DupPro.Pack.ProcessingStats.Finished) {
                        // IF something caused the Backup to come out of finished refresh everything (has to be out of finished or error state)
                        if ((package_info.status != 100) && (package_info.status > 0)) {
                            // wait one miutes to prevent a realod loop
                            setTimeout(function() {
                                window.location = window.location.href;
                            }, 60000);
                        }
                    }

                    if ((currentStatus >= 0 && currentStatus < 100) || currentStatus == -3) {
                        activePackagePresent = true;
                    }
                }

                if (activePackagePresent) {
                    $('#dup-pro-create-new').addClass('disabled');
                    packagesTable.find(".dup-restore-backup").prop('disabled', true);
                    packagesTable.find(".dup-dnload-btn").prop('disabled', true);
                    packagesTable.find(".dup-remote-download").prop('disabled', true);
                    DupPro.Pack.SetUpdateInterval(5);
                } else {
                    $('#dup-pro-create-new').removeClass('disabled');
                    packagesTable.find(".dup-restore-backup.can-enabled").prop('disabled', false);
                    packagesTable.find(".dup-dnload-btn.can-enabled").prop('disabled', false);
                    packagesTable.find(".dup-remote-download.can-enabled").prop('disabled', false);
                    // Kick refresh down to 60 seconds if nothing is being actively worked on
                    DupPro.Pack.SetUpdateInterval(60);
                }
            },
            error: function(data) {
                DupPro.Pack.SetUpdateInterval(60);
                console.log(data);
            }
        });
    };

    //Init
    DupPro.UI.Clock(DupPro._WordPressInitTime);
    DupPro.Pack.UpdateUnfinishedPackages();
    
    $('.dpro-btn-open-recovery-box').click(function(event) {
        event.preventDefault();

        let packageId = $(this).data('package-id');
        
        Duplicator.Util.ajaxWrapper(
            {
                action: 'duplicator_pro_get_recovery_box_content',
                packageId: packageId,
                nonce: '<?php echo esc_js(wp_create_nonce('duplicator_pro_get_recovery_box_content')); ?>'
            },
            function (result, data, funcData, textStatus, jqXHR) {
                if (funcData.success) {
                    let boxContent = funcData.content;
                    if (funcData.isRecoveable) {
                        <?php
                            $availableRecoveryBox->updateMessage('boxContent');
                            $availableRecoveryBox->showAlert();
                        ?>
                        $('.dup-pro-recovery-download-launcher').off().click(function () {
                            DupPro.Pack.downloadLauncher();
                        });
                    } else {
                        <?php
                            $unavailableRecoveryBox->updateMessage('boxContent');
                            $unavailableRecoveryBox->showAlert();
                        ?>
                    }
                } else {
                    DupPro.addAdminMessage(funcData.message, 'error');
                }
                return '';        
            }
        );

        return false;
    });
});
</script>
