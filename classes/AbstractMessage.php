<?php

declare(strict_types=1);

namespace Castlegate\MaintenanceMode;

use Exception;

abstract class AbstractMessage
{
    /**
     * Option name
     *
     * @var string|null
     */
    public const OPTION_NAME = null;

    /**
     * Return message
     *
     * @param bool $default Return default message if message not available
     * @return string
     */
    final public static function getMessage(bool $default = false): string
    {
        static::validateOptionName();
        $message = get_option(static::OPTION_NAME);

        if (!is_string($message)) {
            $message = '';
        }

        if (!$message && $default) {
            return static::getDefaultMessage();
        }

        return $message;
    }

    /**
     * Return default message
     *
     * @return string
     */
    abstract public static function getDefaultMessage(): string;

    /**
     * Set message
     *
     * @param string $message
     * @return void
     */
    final public static function setMessage(string $message): void
    {
        static::validateOptionName();
        update_option(static::OPTION_NAME, $message);
    }

    /**
     * Check for valid option name
     *
     * @return void
     */
    private static function validateOptionName(): void
    {
        if (is_string(static::OPTION_NAME) && static::OPTION_NAME) {
            return;
        }

        throw new Exception('Constant OPTION_NAME not defined');
    }
}
