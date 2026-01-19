<?php

namespace App\Enums;

class AppointmentStatus
{
    const SCHEDULED = 1;
    const CONFIRMED = 2;
    const CANCELLED = 3;

    public static function from($status)
    {
        switch ($status) {
            case 1:
                return self::SCHEDULED;
            case 2:
                return self::CONFIRMED;
            case 3:
                return self::CANCELLED;
            default:
            // return a default value or throw an exception if needed
            return null;
        }

    }

    // public static function color($status)
    // {
    //     return match($status) {
    //         AppointmentStatus::SCHEDULED => 'primary',
    //         AppointmentStatus::CONFIRMED => 'success',
    //         AppointmentStatus::CANCELLED => 'danger',
    //     };
    // }

    public function color(): string
    {
        return match($this) {
            AppointmentStatus::SCHEDULED => 'primary',
            AppointmentStatus::CONFIRMED => 'success',
            AppointmentStatus::CANCELLED => 'danger',
        };
    }

    // this functin is used to appointment status controller, this replace the enums of php because enums is not working in php version 8 and below
    public static function cases()
    {
        $cases = [
            [
                "name"  => "SCHEDULED",
                "value" => 1,
                "color" => "primary",
            ],
            [
                "name"  => "CONFIRMED",
                "value" => 2,
                "color" => "success",
            ],
            [
                "name"  => "CANCELLED",
                "value" => 3,
                "color" => "danger",
            ]
        ];
        return $cases;
    }
}
