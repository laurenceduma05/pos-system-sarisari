<?php

namespace App\Http\Controllers\Admin;

use App\Enums\AppointmentStatus;
use App\Http\Controllers\Controller;
use App\Models\Appointment;
use Illuminate\Http\Request;

class AppointmentmentStatusController extends Controller
{

    public function getStatusWithCount()
    {
        $cases = AppointmentStatus::cases();
        // $appointmentStatus = new AppointmentStatus();
        // dd($test(1));

        $result = collect($cases)->map(function ($status) {
            $appointmentStatus = AppointmentStatus::from($status['value']);
            return [
                'name' => $status['name'],
                'value' => $status['value'],
                'count' => Appointment::where('status', $status['value'])->count(),
                'color' => $status['color'] ?? 'default',
            ];
        });
        return $result;
    }
}
