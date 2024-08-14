<?php

declare(strict_types=1);

namespace Castlegate\MaintenanceMode;

use DateTime;
use WP_Error;
use WP_User;

final class MaintenanceMode
{
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
        add_action('woocommerce_login_form_start', [$mm, 'printShopLoginMessage']);

        add_filter('wp_authenticate_user', [$mm, 'authenticate']);
        add_filter('login_message', [$mm, 'getLoginMessage']);
        add_filter('option_users_can_register', [$mm, 'getUsersCanRegister'], 10, 2);
        add_filter('registration_errors', [$mm, 'getRegistrationErrors'], 10, 3);
        add_filter('woocommerce_is_purchasable', [$mm, 'isProductPurchasable']);
    }

    /**
     * Maintenance mode is active?
     *
     * @return bool
     */
    public static function isActive(): bool
    {
        if (!static::isEnabled() || !static::hasValidStartEnd()) {
            return false;
        }

        $start = static::getStartDateTime(true);
        $end = static::getEndDateTime(true);
        $current = static::getCurrentDateTime();

        if (
            ($start && $start > $current) ||
            ($end && $end < $current)
        ) {
            return false;
        }

        return true;
    }

    /**
     * Maintenance mode is enabled?
     *
     * @return bool
     */
    public static function isEnabled(): bool
    {
        return (bool) get_option('cgit_wp_maintenance_mode_enabled');
    }

    /**
     * Toggle maintenance mode
     *
     * @param bool|null
     * @return void
     */
    public static function toggle(bool $enabled = null): void
    {
        if (is_null($enabled)) {
            $enabled = !static::isEnabled();
        }

        update_option('cgit_wp_maintenance_mode_enabled', $enabled);
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
     * Return start date and time
     *
     * @param bool $past Return date and time even if it is in the past
     * @return DateTime|null
     */
    public static function getStartDateTime(bool $past = false): ?DateTime
    {
        return static::sanitizeDateTime(StartDateTime::getDateTime(), $past);
    }

    /**
     * Return end date and time
     *
     * @param bool $past Return date and time even if it is in the past
     * @return DateTime|null
     */
    public static function getEndDateTime(bool $past = false): ?DateTime
    {
        return static::sanitizeDateTime(EndDateTime::getDateTime(), $past);
    }

    /**
     * Return sanitized date and time
     *
     * @param DateTime|null $date
     * @param bool $past Return date and time even if it is in the past
     * @return DateTime|null
     */
    private static function sanitizeDateTime(?DateTime $date, bool $past = false): ?DateTime
    {
        if (is_null($date) || $past || $date > static::getCurrentDateTime()) {
            return $date;
        }

        return null;
    }

    /**
     * Return current date and time
     *
     * @return DateTime
     */
    public static function getCurrentDateTime(): DateTime
    {
        $format = AbstractDateTime::DATE_TIME_FORMAT;
        $current = DateTime::createFromFormat($format, wp_date($format));

        if ($current instanceof DateTime) {
            return $current;
        }

        return new DateTime();
    }

    /**
     * Start and end times are valid?
     *
     * If both start and end dates are set and the start date is after the end
     * date, the values are invalid.
     *
     * @return bool
     */
    public static function hasValidStartEnd(): bool
    {
        $start = static::getStartDateTime(true);
        $end = static::getEndDateTime(true);

        if (is_null($start) || is_null($end)) {
            return true;
        }

        return $start < $end;
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
    public function getLoginMessage($message)
    {
        if (!static::isActive()) {
            return $message;
        }

        ob_start();
        include CGIT_WP_MAINTENANCE_MODE_PLUGIN_DIR . '/views/login-message.php';

        return ob_get_clean() . $message;
    }

    /**
     * Add WooCommerce notice if maintenance mode enabled
     *
     * Run on `woocommerce_init` action.
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

        $type = 'notice';

        ob_start();
        include CGIT_WP_MAINTENANCE_MODE_PLUGIN_DIR . '/views/shop-message.php';

        $message = ob_get_clean();

        if (wc_has_notice($message, $type)) {
            return;
        }

        wc_add_notice($message, $type);
    }

    /**
     * Print WooCommerce shop login message HTML if enabled
     *
     * Run on `woocommerce_login_form_start` action.
     *
     * @return void
     */
    public function printShopLoginMessage(): void
    {
        if (!static::isActive()) {
            return;
        }

        include CGIT_WP_MAINTENANCE_MODE_PLUGIN_DIR . '/views/shop-login-message.php';
    }

    /**
     * Prevent product purchases
     *
     * Run on `woocommerce_is_purchasable` filter.
     *
     * @param mixed $purchasable
     * @param mixed $product
     * @return mixed
     */
    public function isProductPurchasable($purchasable, $product = null)
    {
        if (static::isActive()) {
            return false;
        }

        return $purchasable;
    }

    /**
     * Allow user registration?
     *
     * Run on `option_users_can_register`. Disables user registration while
     * maintenance mode is active.
     *
     * @param mixed $value
     * @param string $option
     * @return mixed
     */
    public function getUsersCanRegister($value, $option)
    {
        if (static::isActive()) {
            return false;
        }

        return $value;
    }

    /**
     * Registration has errors?
     *
     * Run on `registration_errors` to prevent programmatic user registrations
     * while maintenance mode is active.
     *
     * @param WP_Error $errors
     * @param string $sanitized_user_login
     * @param string $user_email
     * @return WP_Error
     */
    public function getRegistrationErrors(WP_Error $errors, string $sanitized_user_login, string $user_email): WP_Error
    {
        if (static::isActive()) {
            $errors->add('cgit_wp_maintenance_mode', __('Registration is disabled while the site is undergoing essential maintenance.'));
        }

        return $errors;
    }
}
