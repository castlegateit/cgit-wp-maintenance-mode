<?php

use Castlegate\MaintenanceMode\Admin;
use Castlegate\MaintenanceMode\MaintenanceMode;

?>

<div class="wrap">
    <h1><?= esc_html__('Maintenance Mode') ?></h1>

    <form action="" method="post">
        <input type="hidden" name="<?= esc_attr(Admin::FORM_ID_KEY) ?>" value="<?= esc_attr(Admin::FORM_ID) ?>">
        <table class="form-table" role="presentation">
            <tr>
                <th scope="row">
                    <?= esc_html__('Status') ?>
                </th>

                <td>
                    <fieldset>
                        <legend class="screen-reader-text"><?= esc_html__('Maintence mode status') ?></legend>

                        <label>
                            <input type="radio" name="<?= esc_attr($this->activeKey) ?>" value="1" <?= esc_attr(MaintenanceMode::isActive() ? 'checked' : '') ?>>
                            <?= esc_html__('Enabled') ?>
                        </label>

                        <br>

                        <label>
                            <input type="radio" name="<?= esc_attr($this->activeKey) ?>" value="" <?= esc_attr(!MaintenanceMode::isActive() ? 'checked' : '') ?>>
                            <?= esc_html__('Disabled') ?>
                        </label>
                    </fieldset>
                </td>
            </tr>

            <tr>
                <th scope="row">
                    <label for="<?= esc_attr($this->loginMessageKey) ?>"><?= esc_html__('Login message') ?></label>
                </th>

                <td>
                    <textarea name="<?= esc_attr($this->loginMessageKey) ?>" id="<?= esc_attr($this->loginMessageKey) ?>" class="regular-text" rows="6" columns="60"><?= esc_html(MaintenanceMode::getLoginMessage()) ?></textarea>
                    <p class="description" style="max-width: 40em;"><?= esc_html(sprintf(__('Shown above the login form. Default: %s'), MaintenanceMode::getDefaultLoginMessage())) ?></p>
                </td>
            </tr>

            <?php

            if (class_exists('\\WooCommerce')) {
                ?>
                <tr>
                    <th scope="row">
                        <label for="<?= esc_attr($this->shopMessageKey) ?>"><?= esc_html__('Shop message') ?></label>
                    </th>

                    <td>
                        <textarea name="<?= esc_attr($this->shopMessageKey) ?>" id="<?= esc_attr($this->shopMessageKey) ?>" class="regular-text" rows="6" columns="60"><?= esc_html(MaintenanceMode::getShopMessage()) ?></textarea>
                        <p class="description" style="max-width: 40em;"><?= esc_html(sprintf(__('Shown above the shop pages. Default: %s'), MaintenanceMode::getDefaultShopMessage())) ?></p>
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
