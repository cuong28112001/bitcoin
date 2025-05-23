<?php

/**
 * @package   Duplicator
 * @copyright (c) 2022, Snap Creek LLC
 */

defined('ABSPATH') || defined('DUPXABSPATH') || exit;

use Duplicator\Installer\Core\Params\PrmMng;
use Duplicator\Installer\Utils\InstDescMng;

$paramsManager = PrmMng::getInstance();
$descMng       = InstDescMng::getInstance();
?><p>
    <b>Deployment Path:</b> <i><?php echo DUPX_U::esc_html($paramsManager->getValue(PrmMng::PARAM_PATH_NEW)); ?></i>
</p>
<p>
    The installer has detected that the archive file has been extracted to the deployment path above. The installer is going
    to skip the extraction process by default. If you want to re-extract the archive file, switch to "Advanced" mode, and
    under "Options" > "Extraction Mode" choose the preferred extraction mode.
</p>
<small>
    Note: This test looks for a file named <i><?php echo $descMng->getGenericName(InstDescMng::TYPE_MANUAL_EXTRACT); ?></i> 
    in the <?php echo DUPX_U::esc_html(DUPX_INIT); ?> directory. If the file exists then this notice is shown.
    The <i><?php echo $descMng->getGenericName(InstDescMng::TYPE_MANUAL_EXTRACT); ?></i> file is created with every archive and
    removed once the install is complete. For more details on this process see the
    <a href="<?php echo DUPX_Constants::FAQ_URL; ?>how-to-handle-various-install-scenarios" target="_blank">manual extraction FAQ</a>.
</small>
