<?php

declare(strict_types=1);

namespace Castlegate\MaintenanceMode;

final class Admin
{
    /**
     * Initialization
     *
     * @return void
     */
    public static function init(): void
    {
        $admin = new static();

        add_action('admin_init', [$admin, 'save']);
        add_action('admin_menu', [$admin, 'registerAdminPage']);
        add_action('admin_notices', [$admin, 'renderStatusMessage']);
        add_action('admin_notices', [$admin, 'renderErrorMessage']);
    }

    /**
     * Register admin page
     *
     * @return void
     */
    public function registerAdminPage(): void
    {
        $title = __('Maintenance Mode');

        add_submenu_page(
            'options-general.php',
            $title,
            $title,
            'manage_options',
            'cgit-wp-maintenance-mode',
            [$this, 'renderAdminPage']
        );
    }

    /**
     * Render admin page
     *
     * @return void
     */
    public function renderAdminPage(): void
    {
        include CGIT_WP_MAINTENANCE_MODE_PLUGIN_DIR . '/views/settings.php';
    }

    /**
     * Save options
     *
     * @return void
     */
    public function save(): void
    {
        if (($_POST['form_id'] ?? null) !== 'cgit_wp_maintenance_mode_admin') {
            return;
        }

        MaintenanceMode::toggle((bool) ($_POST['cgit_wp_maintenance_mode_enabled'] ?? false));
        StartDateTime::setDateTimeString($_POST['cgit_wp_maintenance_mode_start_date_time'] ?? null);
        EndDateTime::setDateTimeString($_POST['cgit_wp_maintenance_mode_end_date_time'] ?? null);

        if (isset($_POST['cgit_wp_maintenance_mode_login_message'])) {
            LoginMessage::setMessage((string) $_POST['cgit_wp_maintenance_mode_login_message']);
        }

        if (isset($_POST['cgit_wp_maintenance_mode_shop_message'])) {
            ShopMessage::setMessage((string) $_POST['cgit_wp_maintenance_mode_shop_message']);
        }

        add_action('admin_notices', [$this, 'renderSaveMessage']);
    }

    /**
     * Render save message
     *
     * @return void
     */
    public function renderSaveMessage(): void
    {
        include CGIT_WP_MAINTENANCE_MODE_PLUGIN_DIR . '/views/settings-saved-notice.php';
    }

    /**
     * Render status message
     *
     * @return void
     */
    public function renderStatusMessage(): void
    {
        if (!MaintenanceMode::isEnabled() || !MaintenanceMode::hasValidStartEnd()) {
            return;
        }

        $parts = [];
        $start = MaintenanceMode::getStartDateTime();
        $end = MaintenanceMode::getEndDateTime();

        if (MaintenanceMode::isActive()) {
            $parts[] = __('Maintenance mode is active.');
        }

        if ($start) {
            $parts[] = sprintf(__('Maintenance mode is scheduled to start at %s.'), $start->format('H:i \o\n j F Y'));
        }

        if ($end) {
            $parts[] = sprintf(__('Maintenance mode is scheduled to end at %s.'), $end->format('H:i \o\n j F Y'));
        }

        if (!$parts) {
            return;
        }

        $message = implode(' ', $parts);

        include CGIT_WP_MAINTENANCE_MODE_PLUGIN_DIR . '/views/status-notice.php';
    }

    /**
     * Render error message
     *
     * @return void
     */
    public function renderErrorMessage(): void
    {
        if (!MaintenanceMode::isEnabled() || MaintenanceMode::hasValidStartEnd()) {
            return;
        }

        include CGIT_WP_MAINTENANCE_MODE_PLUGIN_DIR . '/views/date-error-notice.php';
    }
}
