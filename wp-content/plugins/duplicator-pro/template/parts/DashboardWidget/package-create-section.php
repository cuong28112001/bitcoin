<?php

/**
 * Duplicator Backup row in table Backups list
 *
 * @package   Duplicator
 * @copyright (c) 2022, Snap Creek LLC
 */

use Duplicator\Controllers\PackagesPageController;
use Duplicator\Core\CapMng;

defined("ABSPATH") or die("");

/**
 * Variables
 *
 * @var \Duplicator\Core\Controllers\ControllersManager $ctrlMng
 * @var \Duplicator\Core\Views\TplMng  $tplMng
 * @var array<string, mixed> $tplData
 */

$tooltipTitle   = esc_attr__('Backup creation', 'duplicator-pro');
$tooltipContent = esc_attr__(
    'This will create a new Backup. If a Backup is currently running then this button will be disabled.',
    'duplicator-pro'
);

?>
<div class="dup-section-package-create dup-flex-content">
    <span>
        <?php esc_html_e('Last backup:', 'duplicator-pro'); ?>
        <span class="dup-last-backup-info">
            <?php
                echo wp_kses(
                    $tplData['lastBackupString'],
                    [
                        'b'    => [],
                        'span' => [
                            'class' => [],
                        ],
                    ]
                );
                ?>
        </span>
    </span>
    <?php if (CapMng::can(CapMng::CAP_CREATE, false)) { ?>
    <span
        class="dup-new-package-wrapper"
        data-tooltip-title="<?php echo esc_attr($tooltipTitle); ?>"
        data-tooltip="<?php echo esc_attr($tooltipContent); ?>"
    >
        <a  
            id="dup-pro-create-new" 
            class="button button-primary <?php echo DUP_PRO_Package::isPackageRunning() ? 'disabled' : ''; ?>"
            href="<?php echo esc_url(PackagesPageController::getInstance()->getPackageBuildS1Url()); ?>"
        >
            <?php esc_html_e('Create New', 'duplicator-pro'); ?>
        </a>
    </span>
    <?php } ?>
</div>
