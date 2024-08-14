<?php

declare(strict_types=1);

namespace Castlegate\MaintenanceMode;

use DateTime;
use Exception;

abstract class AbstractDateTime
{
    /**
     * Option name
     *
     * @var string|null
     */
    public const OPTION_NAME = null;

    /**
     * Date and time format
     *
     * @var string
     */
    final public const DATE_TIME_FORMAT = 'Y-m-d\TH:i';

    /**
     * Return date and time as DateTime instance
     *
     * @return DateTime|null
     */
    final public static function getDateTime(): ?DateTime
    {
        $value = static::getDateTimeString();

        if (is_string($value)) {
            $date = static::parseDateTimeString($value);

            if ($date instanceof DateTime) {
                return $date;
            }
        }

        return null;
    }

    /**
     * Return date and time as string
     *
     * @return string
     */
    final public static function getDateTimeString(): string
    {
        static::validateOptionName();
        $value = get_option(static::OPTION_NAME);

        if (is_string($value) && static::isValidDateTimeString($value)) {
            return $value;
        }

        return '';
    }

    /**
     * Set date and time based on DateTime instance
     *
     * @param DateTime|null $date
     * @return void
     */
    final public static function setDateTime(?DateTime $date): void
    {
        $value = null;

        if ($date instanceof DateTime) {
            $value = $date->format(static::DATE_TIME_FORMAT);
        }

        static::setDateTimeString($value);
    }

    /**
     * Set date and time based on string
     *
     * @param string|null $value
     * @return void
     */
    final public static function setDateTimeString(?string $value): void
    {
        static::validateOptionName();

        if (!is_string($value) || !static::isValidDateTimeString($value)) {
            $value = null;
        }

        update_option(static::OPTION_NAME, $value);
    }

    /**
     * Parse date and time string
     *
     * @param string $str
     * @return DateTime|null
     */
    final public static function parseDateTimeString(string $str): ?DateTime
    {
        if (static::isValidDateTimeString($str)) {
            return DateTime::createFromFormat(static::DATE_TIME_FORMAT, $str);
        }

        return null;
    }

    /**
     * String is a valid date value?
     *
     * @param string $value
     * @return bool
     */
    final public static function isValidDateTimeString(string $str): bool
    {
        return (bool) preg_match('/^\d{4}-\d{2}-\d{2}T\d{2}:\d{2}$/', $str);
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
