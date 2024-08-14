<?php

use Castlegate\MaintenanceMode\ShopMessage;

?>

<b><?= esc_html__('Notice:') ?></b> <?= esc_html(ShopMessage::getMessage(true)) ?>
