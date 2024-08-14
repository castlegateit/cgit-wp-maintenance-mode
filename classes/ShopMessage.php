<?php

declare(strict_types=1);

namespace Castlegate\MaintenanceMode;

class ShopMessage extends AbstractMessage
{
    /**
     * Option name
     *
     * @var string|null
     */
    public const OPTION_NAME = 'cgit_wp_maintenance_mode_shop_message';

    /**
     * Return default message
     *
     * @return string
     */
    public static function getDefaultMessage(): string
    {
        return __('This site is currently undergoing essential maintenance. You will be able to purchase products from the shop again when the maintenance work is complete.');
    }
}
