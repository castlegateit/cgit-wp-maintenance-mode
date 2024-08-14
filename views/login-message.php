<?php

use Castlegate\MaintenanceMode\LoginMessage;

?>

<p class="message"><b><?= esc_html__('Notice:') ?></b> <?= esc_html(LoginMessage::getMessage(true)) ?></p>
