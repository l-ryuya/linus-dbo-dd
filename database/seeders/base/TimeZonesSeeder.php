<?php

declare(strict_types=1);

namespace Database\Seeders\base;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TimeZonesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::statement("ALTER TABLE time_zones ALTER COLUMN time_zone_id RESTART WITH 1");

        DB::table('time_zones')->insert([
            ['tz_name' => 'UTC', 'utc_offset' => '+00:00', 'display_label' => '(UTC+00:00) Coordinated Universal Time'],
            ['tz_name' => 'Europe/London', 'utc_offset' => '+00:00', 'display_label' => '(UTC+00:00) London'],
            ['tz_name' => 'Africa/Casablanca', 'utc_offset' => '+00:00', 'display_label' => '(UTC+00:00) Casablanca'],
            ['tz_name' => 'Africa/Accra', 'utc_offset' => '+00:00', 'display_label' => '(UTC+00:00) Accra'],
            ['tz_name' => 'Europe/Paris', 'utc_offset' => '+01:00', 'display_label' => '(UTC+01:00) Paris'],
            ['tz_name' => 'Europe/Berlin', 'utc_offset' => '+01:00', 'display_label' => '(UTC+01:00) Berlin'],
            ['tz_name' => 'Europe/Madrid', 'utc_offset' => '+01:00', 'display_label' => '(UTC+01:00) Madrid'],
            ['tz_name' => 'Europe/Rome', 'utc_offset' => '+01:00', 'display_label' => '(UTC+01:00) Rome'],
            ['tz_name' => 'Europe/Amsterdam', 'utc_offset' => '+01:00', 'display_label' => '(UTC+01:00) Amsterdam'],
            ['tz_name' => 'Europe/Brussels', 'utc_offset' => '+01:00', 'display_label' => '(UTC+01:00) Brussels'],
            ['tz_name' => 'Europe/Copenhagen', 'utc_offset' => '+01:00', 'display_label' => '(UTC+01:00) Copenhagen'],
            ['tz_name' => 'Europe/Zurich', 'utc_offset' => '+01:00', 'display_label' => '(UTC+01:00) Zurich'],
            ['tz_name' => 'Europe/Vienna', 'utc_offset' => '+01:00', 'display_label' => '(UTC+01:00) Vienna'],
            ['tz_name' => 'Europe/Stockholm', 'utc_offset' => '+01:00', 'display_label' => '(UTC+01:00) Stockholm'],
            ['tz_name' => 'Europe/Warsaw', 'utc_offset' => '+01:00', 'display_label' => '(UTC+01:00) Warsaw'],
            ['tz_name' => 'Africa/Algiers', 'utc_offset' => '+01:00', 'display_label' => '(UTC+01:00) Algiers'],
            ['tz_name' => 'Africa/Lagos', 'utc_offset' => '+01:00', 'display_label' => '(UTC+01:00) Lagos'],
            ['tz_name' => 'Europe/Athens', 'utc_offset' => '+02:00', 'display_label' => '(UTC+02:00) Athens'],
            ['tz_name' => 'Europe/Bucharest', 'utc_offset' => '+02:00', 'display_label' => '(UTC+02:00) Bucharest'],
            ['tz_name' => 'Europe/Helsinki', 'utc_offset' => '+02:00', 'display_label' => '(UTC+02:00) Helsinki'],
            ['tz_name' => 'Europe/Kyiv', 'utc_offset' => '+02:00', 'display_label' => '(UTC+02:00) Kyiv'],
            ['tz_name' => 'Asia/Jerusalem', 'utc_offset' => '+02:00', 'display_label' => '(UTC+02:00) Jerusalem'],
            ['tz_name' => 'Asia/Beirut', 'utc_offset' => '+02:00', 'display_label' => '(UTC+02:00) Beirut'],
            ['tz_name' => 'Africa/Cairo', 'utc_offset' => '+02:00', 'display_label' => '(UTC+02:00) Cairo'],
            ['tz_name' => 'Africa/Johannesburg', 'utc_offset' => '+02:00', 'display_label' => '(UTC+02:00) Johannesburg'],
            ['tz_name' => 'Africa/Khartoum', 'utc_offset' => '+02:00', 'display_label' => '(UTC+02:00) Khartoum'],
            ['tz_name' => 'Africa/Harare', 'utc_offset' => '+02:00', 'display_label' => '(UTC+02:00) Harare'],
            ['tz_name' => 'Europe/Moscow', 'utc_offset' => '+03:00', 'display_label' => '(UTC+03:00) Moscow'],
            ['tz_name' => 'Europe/Istanbul', 'utc_offset' => '+03:00', 'display_label' => '(UTC+03:00) Istanbul'],
            ['tz_name' => 'Asia/Qatar', 'utc_offset' => '+03:00', 'display_label' => '(UTC+03:00) Doha'],
            ['tz_name' => 'Asia/Riyadh', 'utc_offset' => '+03:00', 'display_label' => '(UTC+03:00) Riyadh'],
            ['tz_name' => 'Africa/Addis_Ababa', 'utc_offset' => '+03:00', 'display_label' => '(UTC+03:00) Addis Ababa'],
            ['tz_name' => 'Africa/Nairobi', 'utc_offset' => '+03:00', 'display_label' => '(UTC+03:00) Nairobi'],
            ['tz_name' => 'Asia/Dubai', 'utc_offset' => '+04:00', 'display_label' => '(UTC+04:00) Dubai'],
            ['tz_name' => 'Asia/Karachi', 'utc_offset' => '+05:00', 'display_label' => '(UTC+05:00) Karachi'],
            ['tz_name' => 'Asia/Yekaterinburg', 'utc_offset' => '+05:00', 'display_label' => '(UTC+05:00) Yekaterinburg'],
            ['tz_name' => 'Asia/Tashkent', 'utc_offset' => '+05:00', 'display_label' => '(UTC+05:00) Tashkent'],
            ['tz_name' => 'Asia/Kolkata', 'utc_offset' => '+05:30', 'display_label' => '(UTC+05:30) Kolkata'],
            ['tz_name' => 'Asia/Kathmandu', 'utc_offset' => '+05:45', 'display_label' => '(UTC+05:45) Kathmandu'],
            ['tz_name' => 'Asia/Dhaka', 'utc_offset' => '+06:00', 'display_label' => '(UTC+06:00) Dhaka'],
            ['tz_name' => 'Asia/Yangon', 'utc_offset' => '+06:30', 'display_label' => '(UTC+06:30) Yangon'],
            ['tz_name' => 'Asia/Bangkok', 'utc_offset' => '+07:00', 'display_label' => '(UTC+07:00) Bangkok'],
            ['tz_name' => 'Asia/Novosibirsk', 'utc_offset' => '+07:00', 'display_label' => '(UTC+07:00) Novosibirsk'],
            ['tz_name' => 'Asia/Singapore', 'utc_offset' => '+08:00', 'display_label' => '(UTC+08:00) Singapore'],
            ['tz_name' => 'Asia/Kuala_Lumpur', 'utc_offset' => '+08:00', 'display_label' => '(UTC+08:00) Kuala Lumpur'],
            ['tz_name' => 'Asia/Ulaanbaatar', 'utc_offset' => '+08:00', 'display_label' => '(UTC+08:00) Ulaanbaatar'],
            ['tz_name' => 'Asia/Irkutsk', 'utc_offset' => '+08:00', 'display_label' => '(UTC+08:00) Irkutsk'],
            ['tz_name' => 'Asia/Hong_Kong', 'utc_offset' => '+08:00', 'display_label' => '(UTC+08:00) Hong Kong'],
            ['tz_name' => 'Asia/Taipei', 'utc_offset' => '+08:00', 'display_label' => '(UTC+08:00) Taipei'],
            ['tz_name' => 'Asia/Shanghai', 'utc_offset' => '+08:00', 'display_label' => '(UTC+08:00) Shanghai'],
            ['tz_name' => 'Australia/Perth', 'utc_offset' => '+08:00', 'display_label' => '(UTC+08:00) Perth'],
            ['tz_name' => 'Asia/Tokyo', 'utc_offset' => '+09:00', 'display_label' => '(UTC+09:00) Tokyo'],
            ['tz_name' => 'Asia/Seoul', 'utc_offset' => '+09:00', 'display_label' => '(UTC+09:00) Seoul'],
            ['tz_name' => 'Asia/Chita', 'utc_offset' => '+09:00', 'display_label' => '(UTC+09:00) Chita'],
            ['tz_name' => 'Australia/Adelaide', 'utc_offset' => '+09:30', 'display_label' => '(UTC+09:30) Adelaide'],
            ['tz_name' => 'Australia/Darwin', 'utc_offset' => '+09:30', 'display_label' => '(UTC+09:30) Darwin'],
            ['tz_name' => 'Asia/Vladivostok', 'utc_offset' => '+10:00', 'display_label' => '(UTC+10:00) Vladivostok'],
            ['tz_name' => 'Australia/Brisbane', 'utc_offset' => '+10:00', 'display_label' => '(UTC+10:00) Brisbane'],
            ['tz_name' => 'Australia/Hobart', 'utc_offset' => '+10:00', 'display_label' => '(UTC+10:00) Hobart'],
            ['tz_name' => 'Australia/Sydney', 'utc_offset' => '+10:00', 'display_label' => '(UTC+10:00) Sydney'],
            ['tz_name' => 'Pacific/Port_Moresby', 'utc_offset' => '+10:00', 'display_label' => '(UTC+10:00) Port Moresby'],
            ['tz_name' => 'Pacific/Guam', 'utc_offset' => '+10:00', 'display_label' => '(UTC+10:00) Guam'],
            ['tz_name' => 'Asia/Magadan', 'utc_offset' => '+11:00', 'display_label' => '(UTC+11:00) Magadan'],
            ['tz_name' => 'Pacific/Fiji', 'utc_offset' => '+12:00', 'display_label' => '(UTC+12:00) Fiji'],
            ['tz_name' => 'Pacific/Auckland', 'utc_offset' => '+12:00', 'display_label' => '(UTC+12:00) Auckland'],
            ['tz_name' => 'Pacific/Tongatapu', 'utc_offset' => '+13:00', 'display_label' => "(UTC+13:00) Nuku'alofa"],
            ['tz_name' => 'Pacific/Apia', 'utc_offset' => '+13:00', 'display_label' => '(UTC+13:00) Apia'],
            ['tz_name' => 'Pacific/Kiritimati', 'utc_offset' => '+14:00', 'display_label' => '(UTC+14:00) Kiritimati'],
            ['tz_name' => 'America/Sao_Paulo', 'utc_offset' => '-03:00', 'display_label' => '(UTC-03:00) São Paulo'],
            ['tz_name' => 'America/Montevideo', 'utc_offset' => '-03:00', 'display_label' => '(UTC-03:00) Montevideo'],
            ['tz_name' => 'America/Argentina/Buenos_Aires', 'utc_offset' => '-03:00', 'display_label' => '(UTC-03:00) Buenos Aires'],
            ['tz_name' => 'America/Recife', 'utc_offset' => '-03:00', 'display_label' => '(UTC-03:00) Recife'],
            ['tz_name' => 'America/St_Johns', 'utc_offset' => '-03:30', 'display_label' => "(UTC-03:30) St. John's"],
            ['tz_name' => 'America/Halifax', 'utc_offset' => '-04:00', 'display_label' => '(UTC-04:00) Halifax'],
            ['tz_name' => 'America/Puerto_Rico', 'utc_offset' => '-04:00', 'display_label' => '(UTC-04:00) San Juan'],
            ['tz_name' => 'America/La_Paz', 'utc_offset' => '-04:00', 'display_label' => '(UTC-04:00) La Paz'],
            ['tz_name' => 'America/Santiago', 'utc_offset' => '-04:00', 'display_label' => '(UTC-04:00) Santiago'],
            ['tz_name' => 'America/Caracas', 'utc_offset' => '-04:00', 'display_label' => '(UTC-04:00) Caracas'],
            ['tz_name' => 'America/Guyana', 'utc_offset' => '-04:00', 'display_label' => '(UTC-04:00) Georgetown'],
            ['tz_name' => 'America/Asuncion', 'utc_offset' => '-04:00', 'display_label' => '(UTC-04:00) Asunción'],
            ['tz_name' => 'America/Detroit', 'utc_offset' => '-05:00', 'display_label' => '(UTC-05:00) Detroit'],
            ['tz_name' => 'America/New_York', 'utc_offset' => '-05:00', 'display_label' => '(UTC-05:00) New York'],
            ['tz_name' => 'America/Toronto', 'utc_offset' => '-05:00', 'display_label' => '(UTC-05:00) Toronto'],
            ['tz_name' => 'America/Bogota', 'utc_offset' => '-05:00', 'display_label' => '(UTC-05:00) Bogotá'],
            ['tz_name' => 'America/Lima', 'utc_offset' => '-05:00', 'display_label' => '(UTC-05:00) Lima'],
            ['tz_name' => 'America/Quito', 'utc_offset' => '-05:00', 'display_label' => '(UTC-05:00) Quito'],
            ['tz_name' => 'America/Guayaquil', 'utc_offset' => '-05:00', 'display_label' => '(UTC-05:00) Guayaquil'],
            ['tz_name' => 'America/Chicago', 'utc_offset' => '-06:00', 'display_label' => '(UTC-06:00) Chicago'],
            ['tz_name' => 'America/Mexico_City', 'utc_offset' => '-06:00', 'display_label' => '(UTC-06:00) Mexico City'],
            ['tz_name' => 'America/Denver', 'utc_offset' => '-07:00', 'display_label' => '(UTC-07:00) Denver'],
            ['tz_name' => 'America/Phoenix', 'utc_offset' => '-07:00', 'display_label' => '(UTC-07:00) Phoenix'],
            ['tz_name' => 'America/Whitehorse', 'utc_offset' => '-07:00', 'display_label' => '(UTC-07:00) Whitehorse'],
            ['tz_name' => 'America/Los_Angeles', 'utc_offset' => '-08:00', 'display_label' => '(UTC-08:00) Los Angeles'],
            ['tz_name' => 'America/Las_Vegas', 'utc_offset' => '-08:00', 'display_label' => '(UTC-08:00) Las Vegas'],
            ['tz_name' => 'America/Anchorage', 'utc_offset' => '-09:00', 'display_label' => '(UTC-09:00) Anchorage'],
            ['tz_name' => 'Pacific/Honolulu', 'utc_offset' => '-10:00', 'display_label' => '(UTC-10:00) Honolulu'],
            ['tz_name' => 'America/Adak', 'utc_offset' => '-10:00', 'display_label' => '(UTC-10:00) Adak'],
        ]);

        $maxId = DB::table('time_zones')->max('time_zone_id') ?? 0;
        $nextId = $maxId + 1;

        // シーケンスの再始動
        DB::statement("ALTER TABLE time_zones ALTER COLUMN time_zone_id RESTART WITH {$nextId}");
    }
}
