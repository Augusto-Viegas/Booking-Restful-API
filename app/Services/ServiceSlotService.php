<?php

namespace App\Services;

use App\Models\ServiceSlot;
use App\Http\Resources\ServiceSlotResource;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Redis;
use Illuminate\Pagination\LengthAwarePaginator;

class ServiceSlotService
{
    protected int $defaultTtlMinutes = 5;

    /**
     * Retorna slots disponíveis para um serviço (com paginação),
     * usando cache redis por página.
     * 
     * @param int $serviceId
     * @param string|null $date // 'YYYY-MM-DD' ou null
     * @param int $perPage
     * @param int $page
     * @return array ['date' => [...], 'meta' => [...]]
    **/
    public function getAvailableSlots(int $serviceId, ?string $date = null, int $perPage = 10, int $page = 1): array
    {
        $cacheKey = $this->makeCacheKey($serviceId, $date, $perPage, $page);

        //armazena o resultado (array) por X minutos no Redis
        $payload = Cache::store('redis')->remember($cacheKey, now()->addMinutes($this->defaultTtlMinutes), function ()
        use ($serviceId, $date, $perPage, $page){
            $query = ServiceSlot::query()
                ->with('service')
                ->where('status', 'available')
                ->where('service_id', $serviceId)
                ->orderBy('date')
                ->orderBy('start_time');
            
            if($date){
                $query->where('date', $date);
            }

            //Pagina e transforma com Resource (para padronizar formato)
            $paginator = $query->paginate($perPage, ['*'], 'page', $page);

            //Usamos o Resource para transformar cada slot
            $data = ServiceSlotResource::collection($paginator)->resolve();

            return [
                'data' => $data,
                'meta' =>[
                    'current_page' => $paginator->currentPage(),
                    'per_page' => $paginator->perPage(),
                    'total' => $paginator->total(),
                ],
            ];
        });

        Redis::sadd($this->cacheKeysSet($serviceId), $cacheKey);

        return $payload;
    }

    /**
     * Invalida caches relacionados a um serviço (opcionalmente filtrando por date).
    **/
    public function clearCacheForService(int $serviceId, ?string $date = null): void
    {
        $set = $this->cacheKeysSet($serviceId);
        $keys = Redis::smembers($set) ?: [];

        foreach($keys as $key){
            //se date foi passado, só remove chaves desse date
            if($date && strpos($key, "date={$date}") === false){
                continue;
            }
        }

        Redis::del($key);
        Redis::srem($set, $key);
    }

    public function makeCacheKey(int $serviceId, ?string $date, int $perPage, int $page): string
    {
        $datePart = $date ? "date={$date}" : "date=all";
        return "service:{$serviceId}:slots:{$datePart}:per={$perPage}:page={$page}";
    }

    public function cacheKeysSet(int $serviceId): string
    {
        return "service:{$serviceId}:slot_cache_keys";
    }

    public function createServiceSlot(array $data): ServiceSlot
    {
        return ServiceSlot::create($data);
    }

    public function updateServiceSlot(ServiceSlot $serviceSlot, array $data): bool
    {
        return $serviceSlot->update($data);
    }

    public function deleteServiceSlot(ServiceSlot $serviceSlot): bool
    {
        return $serviceSlot->delete();
    }

}