<?php

namespace App\Services;

use App\Models\ServiceSlot;
use DateInterval;
use DatePeriod;
use DateTime;
use Illuminate\Support\Facades\DB;

class SlotGeneratorService
{
    /**
     * Generate a bunch of service slots to save in DB.
     * date_start -> Data de inicio do período que você quer gerar
     * date_end -> Data do ultimo dia do período que você quer gerar
     * start_time -> Hora de inicio que você quer gerar para cada dia
     * end_time -> ultima hora que será gerada para cada dia
     * slot_duration -> intervalo entre cada slot
     * capacity -> capacidade de cliente por slot
     */
    public function generate(array $input): array
    {
        $slots = [];

        $period = new DatePeriod(
            new DateTime($input['date_start']),
            new DateInterval('P1D'),
            (new DateTime($input['date_end'] ?? $input['date_start']))->modify('+1 day')
        );

        DB::transaction(function () use ($input, $period, &$slots){
            foreach ($period as $day){
                $start = new DateTime($input['start_time']);
                $end = new DateTime($input['end_time']);

                while($start < $end){
                    $slotEnd = (clone $start)->modify("+{$input['slot_duration']} minutes");

                    if($slotEnd > $end) break;

                    $slots[] = ServiceSlot::create([
                        'service_id' => $input['service_id'],
                        'date' => $day->format('Y-m-d'),
                        'start_time' => $start->format('H:i:s'),
                        'end_time' => $slotEnd->format('H:i:s'),
                        'capacity' => $input['capacity'],
                        'status' => 'available',
                    ]);

                    $start = $slotEnd;
                }
            }
        });

        return $slots;
    }
}