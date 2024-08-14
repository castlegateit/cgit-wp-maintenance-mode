<?php

use Castlegate\MaintenanceMode\EndDateTime;
use Castlegate\MaintenanceMode\LoginMessage;
use Castlegate\MaintenanceMode\MaintenanceMode;
use Castlegate\MaintenanceMode\ShopMessage;
use Castlegate\MaintenanceMode\StartDateTime;

?>

<div class="wrap">
    <h1><?= esc_html__('Maintenance Mode') ?></h1>

    <form action="" method="post">
        <input type="hidden" name="form_id" value="cgit_wp_maintenance_mode_admin">

        <table class="form-table" role="presentation">
            <tr>
                <th scope="row">
                    <?= esc_html__('Status') ?>
                </th>

                <td>
                    <fieldset>
                        <legend class="screen-reader-text"><?= esc_html__('Maintence mode status') ?></legend>

                        <label>
                            <input type="radio" name="cgit_wp_maintenance_mode_enabled" value="1" <?= esc_attr(MaintenanceMode::isEnabled() ? 'checked' : '') ?>>
                            <?= esc_html__('Enabled') ?>
                        </label>

                        <br>

                        <label>
                            <input type="radio" name="cgit_wp_maintenance_mode_enabled" value="" <?= esc_attr(!MaintenanceMode::isEnabled() ? 'checked' : '') ?>>
                            <?= esc_html__('Disabled') ?>
                        </label>
                    </fieldset>
                </td>
            </tr>

            <tr>
                <th scope="row">
                    <label for="cgit_wp_maintenance_mode_start_date_time"><?= esc_html__('Start date and time') ?></label>
                </th>

                <td>
                    <input type="datetime-local" name="cgit_wp_maintenance_mode_start_date_time" id="cgit_wp_maintenance_mode_start_date_time" value="<?= esc_attr(StartDateTime::getDateTimeString()) ?>">
                    <p class="description" style="max-width: 40em;"><?= esc_html__('Leave blank to start maintenance mode immediately.') ?></p>
                </td>
            </tr>

            <tr>
                <th scope="row">
                    <label for="cgit_wp_maintenance_mode_end_date_time"><?= esc_html__('End date and time') ?></label>
                </th>

                <td>
                    <input type="datetime-local" name="cgit_wp_maintenance_mode_end_date_time" id="cgit_wp_maintenance_mode_end_date_time" value="<?= esc_attr(EndDateTime::getDateTimeString()) ?>">
                    <p class="description" style="max-width: 40em;"><?= esc_html__('Leave blank to keep the site in maintenance mode until it is manually deactivated.') ?></p>
                </td>
            </tr>

            <tr>
                <th scope="row">
                    <label for="cgit_wp_maintenance_mode_login_message"><?= esc_html__('Login message') ?></label>
                </th>

                <td>
                    <textarea name="cgit_wp_maintenance_mode_login_message" id="cgit_wp_maintenance_mode_login_message" class="regular-text" rows="6" columns="60"><?= esc_html(LoginMessage::getMessage()) ?></textarea>
                    <p class="description" style="max-width: 40em;"><?= esc_html(sprintf(__('Shown above the login form. Default: %s'), LoginMessage::getDefaultMessage())) ?></p>
                </td>
            </tr>

            <?php

            if (class_exists('\\WooCommerce')) {
                ?>
                <tr>
                    <th scope="row">
                        <label for="cgit_wp_maintenance_mode_shop_message"><?= esc_html__('Shop message') ?></label>
                    </th>

                    <td>
                        <textarea name="cgit_wp_maintenance_mode_shop_message" id="cgit_wp_maintenance_mode_shop_message" class="regular-text" rows="6" columns="60"><?= esc_html(ShopMessage::getMessage()) ?></textarea>
                        <p class="description" style="max-width: 40em;"><?= esc_html(sprintf(__('Shown above the shop pages. Default: %s'), ShopMessage::getDefaultMessage())) ?></p>
                    </td>
                </tr>
                <?php
            }

            ?>
        </table>

        <p class="submit">
            <button type="submit" class="button button-primary"><?= esc_html__('Save Changes') ?></button>
        </p>
    </form>
</div>
