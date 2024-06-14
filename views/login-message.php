<?php

use Castlegate\MaintenanceMode\MaintenanceMode;

?>

<p class="message"><b><?= esc_html__('Notice:') ?></b> <?= esc_html(MaintenanceMode::getLoginMessage(true)) ?></p>
