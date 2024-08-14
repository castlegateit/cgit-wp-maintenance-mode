<?php

use Castlegate\MaintenanceMode\LoginMessage;

?>

<div class="woocommerce-info">
    <b><?= esc_html__('Notice:') ?></b> <?= esc_html(LoginMessage::getMessage(true)) ?>
</div>
