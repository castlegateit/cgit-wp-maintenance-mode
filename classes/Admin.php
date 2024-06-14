<?php

declare(strict_types=1);

namespace Castlegate\MaintenanceMode;

final class Admin
{
    /**
     * Admin page slug
     *
     * @var string
     */
    public const SLUG = 'cgit-wp-maintenance-mode';

    /**
     * Admin page capability
     *
     * @var string
     */
    public const CAPABILITY = 'manage_options';

    /**
     * Form ID key
     *
     * @var string
     */
    public const FORM_ID_KEY = 'form_id';

    /**
     * Form ID
     *
     * @var string
     */
    public const FORM_ID = 'cgit_maintenance_mode_form';

    /**
     * Admin page title
     *
     * @var string
     */
    public readonly string $title;

    /**
     * Active field key
     *
     * @var string
     */
    public string $activeKey;

    /**
     * Login message field key
     *
     * @var string
     */
    public string $loginMessageKey;

    /**
     * Shop message field key
     *
     * @var string
     */
    public string $shopMessageKey;

    /**
     * Construct
     *
     * @return void
     */
    public function __construct()
    {
        $this->title = __('Maintenance Mode');
        $this->activeKey = MaintenanceMode::OPTION_NAME_ACTIVE;
        $this->loginMessageKey = MaintenanceMode::OPTION_NAME_LOGIN_MESSAGE;
        $this->shopMessageKey = MaintenanceMode::OPTION_NAME_SHOP_MESSAGE;
    }

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
    }

    /**
     * Register admin page
     *
     * @return void
     */
    public function registerAdminPage(): void
    {
        add_submenu_page(
            'options-general.php',
            $this->title,
            $this->title,
            static::CAPABILITY,
            static::SLUG,
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
        include CGIT_WP_MAINTENANCE_MODE_PLUGIN_DIR . '/views/admin.php';
    }

    /**
     * Save options
     *
     * @return void
     */
    public function save(): void
    {
        if (($_POST[static::FORM_ID_KEY] ?? null) !== static::FORM_ID) {
            return;
        }

        MaintenanceMode::toggle((bool) ($_POST[$this->activeKey] ?? false));
        MaintenanceMode::setLoginMessage((string) ($_POST[$this->loginMessageKey] ?? ''));

        if (isset($_POST[$this->shopMessageKey])) {
            MaintenanceMode::setShopMessage((string) $_POST[$this->shopMessageKey]);
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
        include CGIT_WP_MAINTENANCE_MODE_PLUGIN_DIR . '/views/saved.php';
    }
}
