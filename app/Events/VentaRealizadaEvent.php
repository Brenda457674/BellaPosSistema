<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class VentaRealizadaEvent implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public int $ventaId;
    public string $comprobante;
    public float $total;
    public string $vendedor;
    public int $itemsCount;
    public string $mensaje;
    public string $hora;

    /**
     * Create a new event instance.
     */
    public function __construct(
        int $ventaId,
        string $comprobante,
        float $total,
        string $vendedor,
        int $itemsCount
    ) {
        $this->ventaId = $ventaId;
        $this->comprobante = $comprobante;
        $this->total = round($total, 2);
        $this->vendedor = $vendedor;
        $this->itemsCount = $itemsCount;
        $this->mensaje = "Nueva venta registrada ({$comprobante}) por S/ " . number_format($this->total, 2) . " por {$vendedor}.";
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
        return 'VentaRealizada';
    }
}
