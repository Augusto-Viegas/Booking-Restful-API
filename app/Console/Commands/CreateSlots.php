<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\ServiceSlot\SlotGeneratorService;
use App\Http\Requests\GenerateServiceSlotRequest;
use Illuminate\Support\Facades\Validator;

class CreateSlots extends Command
{
    /**
     * The name and signature of the console command.
     * use example:
     * php artisan make:serviceSlots ""
     *
     * @var string
     */
    protected $signature = 'slot:generate
                            {service_id? : ID of the service}
                            {date_start? : Inicial date (Y-m-d)}
                            {date_end? : final date (Y-m-d)}
                            {start_time? : initial time (H:i)}
                            {end_time? : final time (H:i)}
                            {slot_duration? : Duration of each slot in minutes}
                            {capacity? : Customer capacity per slot}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'create multiple slots for a service informed by ID';

    protected SlotGeneratorService $generator;

    public function __construct(SlotGeneratorService $generator)
    {
        parent::__construct();
        $this->generator = $generator;
    }

    /**
     * Execute the console command.
     */
    public function handle(): int
    {   
        //
        $input = [
            'service_id' => $this->argument('service_id') ?? $this->ask('Enter the service ID'),
            'date_start' => $this->argument('date_start') ?? $this->ask('Enter the start date (Y-m-d)'),
            'date_end' => $this->argument('date_end') ?? $this->ask('Enter the end date (Y-m-d)'),
            'start_time' => $this->argument('start_time') ?? $this->ask('Enter the start time (H:i)'),
            'end_time' => $this->argument('end_time') ?? $this->ask('Enter the end time (H:i)'),
            'slot_duration' => $this->argument('slot_duration') ?? $this->ask('Enter slot duration (minutes) | min: 5'),
            'capacity' => $this->argument('capacity') ?? $this->ask('Enter customer capacity per slot | min:1'),
        ];

        //converter para inteiro dps de preencher
        $input['slot_duration'] = (int) $input['slot_duration'];
        $input['capacity'] = (int) $input['capacity'];

        //Pegando as regras para validação
        $rules = (new GenerateServiceSlotRequest())->rules();

        $validator = Validator::make($input, $rules);

        if($validator->fails()){
            foreach($validator->errors()->all() as $error){
                $this->error($error);
            }
            return self::FAILURE;
        }

        $slots = $this->generator->generate($input);

        $this->info(count($slots)." service slots have been successfully created!");

        return self::SUCCESS;
    }
}
