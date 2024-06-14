<?php

declare(strict_types=1);

namespace Castlegate\MaintenanceMode;

final class Plugin
{
    /**
     * Initialization
     *
     * @return void
     */
    public static function init(): void
    {
        Admin::init();
        MaintenanceMode::init();
    }
}
