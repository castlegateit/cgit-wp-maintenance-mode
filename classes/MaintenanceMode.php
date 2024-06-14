<?php

declare(strict_types=1);

namespace Castlegate\MaintenanceMode;

use WP_Error;
use WP_User;

final class MaintenanceMode
{
    /**
     * Active option name
     *
     * @var string
     */
    public const OPTION_NAME_ACTIVE = 'cgit_maintenance_mode_active';

    /**
     * Login message option name
     *
     * @var string
     */
    public const OPTION_NAME_LOGIN_MESSAGE = 'cgit_maintenance_mode_login_message';

    /**
     * Shop message option name
     *
     * @var string
     */
    public const OPTION_NAME_SHOP_MESSAGE = 'cgit_maintenance_mode_shop_message';

    /**
     * Initialization
     *
     * @return void
     */
    public static function init(): void
    {
        $mm = new static();

        add_action('init', [$mm, 'logout']);
        add_action('woocommerce_init', [$mm, 'addShopNotice']);
        add_action('woocommerce_login_form_start', [$mm, 'printShopLoginMessageHtml']);

        add_filter('wp_authenticate_user', [$mm, 'authenticate']);
        add_filter('login_message', [$mm, 'getLoginMessageHtml']);
        add_filter('woocommerce_is_purchasable', [$mm, 'isShopProductPurchasable']);
    }

    /**
     * Toggle maintenance mode
     *
     * @param bool|null $enable True to enable, false to disable
     * @return void
     */
    public static function toggle(bool $enable = null): void
    {
        if (is_null($enable)) {
            $enable = !static::isActive();
        }

        update_option(static::OPTION_NAME_ACTIVE, $enable);
    }

    /**
     * Enable maintenance mode
     *
     * @return void
     */
    public static function enable(): void
    {
        static::toggle(true);
    }

    /**
     * Disable maintenance mode
     *
     * @return void
     */
    public static function disable(): void
    {
        static::toggle(false);
    }

    /**
     * Maintenance mode is active?
     *
     * @return bool
     */
    public static function isActive(): bool
    {
        return (bool) get_option(static::OPTION_NAME_ACTIVE);
    }

    /**
     * Set login message
     *
     * @param string $message
     * @return void
     */
    public static function setLoginMessage(string $message): void
    {
        update_option(static::OPTION_NAME_LOGIN_MESSAGE, $message);
    }

    /**
     * Login message
     *
     * @param bool $default Return default message if no message saved
     * @return string
     */
    public static function getLoginMessage(bool $default = false): string
    {
        $message = get_option(static::OPTION_NAME_LOGIN_MESSAGE);

        if (!is_string($message)) {
            $message = '';
        }

        if (!$message && $default) {
            return static::getDefaultLoginMessage();
        }

        return $message;
    }

    /**
     * Default login message
     *
     * @return string
     */
    public static function getDefaultLoginMessage(): string
    {
        return __('This site is currently undergoing essential maintenace. You will be able to log in again when the maintenance work is complete.');
    }

    /**
     * Set shop message
     *
     * @param string $message
     * @return void
     */
    public static function setShopMessage(string $message): void
    {
        update_option(static::OPTION_NAME_SHOP_MESSAGE, $message);
    }

    /**
     * Shop message
     *
     * @param bool $default Return default message if no message saved
     * @return string
     */
    public static function getShopMessage(bool $default = false): string
    {
        $message = get_option(static::OPTION_NAME_SHOP_MESSAGE);

        if (!is_string($message)) {
            $message = '';
        }

        if (!$message && $default) {
            return static::getDefaultShopMessage();
        }

        return $message;
    }

    /**
     * Default login message
     *
     * @return string
     */
    public static function getDefaultShopMessage(): string
    {
        return __('This site is currently undergoing essential maintenace. You will be able to purchase products from the shop again when the maintenance work is complete.');
    }

    /**
     * Log out if maintenance mode is enabled
     *
     * Run on `init` action. Excludes administrators.
     *
     * @return void
     */
    public function logout(): void
    {
        if (!static::isActive() || !is_user_logged_in() || current_user_can('activate_plugins')) {
            return;
        }

        wp_logout();
    }

    /**
     * Do not authenticate users if maintenance mode is enabled
     *
     * Run on `authenticate` filter. Excludes administrators.
     *
     * @param mixed $user
     * @param mixed $password
     * @return mixed
     */
    public function authenticate($user, $password = null)
    {
        if (!static::isActive() || !($user instanceof WP_User) || $user->has_cap('activate_plugins')) {
            return $user;
        }

        return new WP_Error('maintenance_mode', __('You cannot log in while the site is in maintenance mode.'));
    }

    /**
     * Return login message HTML if maintenance mode is enabled
     *
     * Run on `login_message` filter.
     *
     * @param mixed $message
     * @return mixed
     */
    public function getLoginMessageHtml($message)
    {
        if (!static::isActive()) {
            return $message;
        }

        ob_start();
        include CGIT_WP_MAINTENANCE_MODE_PLUGIN_DIR . '/views/login-message.php';

        return ob_get_clean() . $message;
    }

    /**
     * Print WooCommerce shop login message HTML if enabled
     *
     * Run on `woocommerce_login_form_start` action.
     *
     * @return void
     */
    public function printShopLoginMessageHtml(): void
    {
        if (!static::isActive()) {
            return;
        }

        include CGIT_WP_MAINTENANCE_MODE_PLUGIN_DIR . '/views/shop-login-message.php';
    }

    /**
     * Add WooCommerce notice if maintenance mode enabled
     *
     * @return void
     */
    public function addShopNotice(): void
    {
        if (
            !static::isActive() ||
            !function_exists('wc_has_notice') ||
            !function_exists('wc_add_notice')
        ) {
            return;
        }

        $message = '<b>Notice:</b> ' . esc_html(static::getShopMessage(true));
        $type = 'notice';

        if (wc_has_notice($message, $type)) {
            return;
        }

        wc_add_notice($message, $type);
    }

    /**
     * Set products as unpurchasable if maintenance mode enabled
     *
     * Run on `woocommerce_is_purchasable` filter.
     *
     * @param mixed $purchasable
     * @param mixed $product
     * @return mixed
     */
    public function isShopProductPurchasable($purchasable, $product = null)
    {
        if (!static::isActive()) {
            return $purchasable;
        }

        return false;
    }
}
