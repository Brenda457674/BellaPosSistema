<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class StockBajoEvent implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public int $productoId;
    public string $productoNombre;
    public string $codigo;
    public float $stockActual;
    public float $stockMinimo;
    public string $mensaje;
    public string $hora;

    /**
     * Create a new event instance.
     */
    public function __construct(
        int $productoId,
        string $productoNombre,
        string $codigo,
        float $stockActual,
        float $stockMinimo
    ) {
        $this->productoId = $productoId;
        $this->productoNombre = $productoNombre;
        $this->codigo = $codigo;
        $this->stockActual = round($stockActual, 2);
        $this->stockMinimo = round($stockMinimo, 2);
        $this->mensaje = "El producto '{$productoNombre}' tiene stock bajo ({$this->stockActual} disponibles, mínimo: {$this->stockMinimo}).";
        $this->hora = now()->format('H:i:s');
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new Channel('pos-canal'),
        ];
    }

    /**
     * Nombre del evento emitido vía WebSocket.
     */
    public function broadcastAs(): string
    {
        return 'StockBajo';
    }
}
