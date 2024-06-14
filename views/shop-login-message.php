<?php

use Castlegate\MaintenanceMode\MaintenanceMode;

?>

<div class="woocommerce-info">
    <b><?= esc_html__('Notice:') ?></b> <?= esc_html(MaintenanceMode::getLoginMessage(true)) ?>
</div>
