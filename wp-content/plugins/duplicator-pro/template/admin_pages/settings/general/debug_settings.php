<?php

/**
 * @package Duplicator
 */

defined("ABSPATH") or die("");

/**
 * Variables
 *
 * @var Duplicator\Core\Controllers\ControllersManager $ctrlMng
 * @var Duplicator\Core\Views\TplMng $tplMng
 * @var array<string, mixed> $tplData
 */

$trace_log_enabled       = (bool) get_option('duplicator_pro_trace_log_enabled');
$send_trace_to_error_log = (bool) get_option('duplicator_pro_send_trace_to_error_log');

if ($trace_log_enabled) {
    $logging_mode = ($send_trace_to_error_log) ?  'enhanced' : 'on';
} else {
    $logging_mode = 'off';
}
?>

<div class="dup-accordion-wrapper display-separators close" >
    <div class="accordion-header" >
        <h3 class="title">
            <?php esc_html_e('Debug', 'duplicator-pro') ?>
        </h3>
    </div>
    <div class="accordion-content">
        <label class="lbl-larger">
            <?php esc_html_e('Trace Log', 'duplicator-pro'); ?>
        </label>
        <div class="margin-bottom-1">
            <select 
                name="_logging_mode"
                class="margin-0 width-medium"
            >
                <option value="off" <?php selected($logging_mode, 'off'); ?>>
                    <?php esc_html_e('Off', 'duplicator-pro'); ?>
                </option>
                <option value="on" <?php selected($logging_mode, 'on'); ?>>
                    <?php esc_html_e('On', 'duplicator-pro'); ?>
                </option>
                <option value="enhanced" <?php selected($logging_mode, 'enhanced'); ?>>
                    <?php esc_html_e('On (Enhanced)', 'duplicator-pro'); ?>
                </option>
            </select>
            <p class="description">
                <?php
                esc_html_e("Turning on log initially clears it out. The enhanced setting writes to both trace and PHP error logs.", 'duplicator-pro');
                echo "<br/>";
                esc_html_e("WARNING: Only turn on this setting when asked to by support as tracing will impact performance.", 'duplicator-pro');
                ?>
            </p>

            <button 
                class="button secondary hollow small margin-0" 
                <?php disabled(DUP_PRO_Log::traceFileExists(), false); ?> 
                onclick="DupPro.Pack.DownloadTraceLog(); return false"
            >
                <i class="fa fa-download"></i>
                <?php echo esc_html__('Download Trace Log', 'duplicator-pro') . ' (' . esc_html(DUP_PRO_Log::getTraceStatus()) . ')'; ?>
            </button>
        </div>
    </div>
</div>