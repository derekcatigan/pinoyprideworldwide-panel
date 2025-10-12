<?php

namespace App\Exports;

use App\Models\Container;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;

class ContainerExport implements FromArray, WithHeadings, WithTitle
{
    protected $container;

    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    public function title(): string
    {
        return 'CONTAINER ' . $this->container->container_number;
    }

    public function headings(): array
    {
        return [
            'Container Number',
            'Branch',
            'Invoice Number',
            'Sender',
            'Receiver',
            'Box Type',
            'Quantity',
            'Dimensions (LxHxW)',
            'Fee',
        ];
    }

    public function array(): array
    {
        $rows = [];

        foreach ($this->container->containerOrders as $containerOrder) {
            $order = $containerOrder->order;

            foreach ($order->orderBoxes as $box) {
                $rows[] = [
                    $this->container->container_number,
                    $this->container->branch?->branch_name ?? 'N/A',
                    $order->invoice_number,
                    $order->sender_name,
                    $order->receiver_name,
                    ucfirst($box->box_type),
                    $box->quantity,
                    "{$box->length}x{$box->height}x{$box->width}",
                    $box->fee,
                ];
            }
        }

        return $rows;
    }
}
