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
            'Sender Name',
            'Sender Contact',
            'Sender Email',
            'Sender Address',
            'Receiver Name',
            'Receiver Contact',
            'Receiver Email',
            'Receiver Address',
            'Box Type',
            'Quantity',
            'Dimensions (LxHxW)',
            'Fee (per box)',
            'Total Fee',
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
                    $order->sender_contact,
                    $order->sender_email,
                    $order->sender_address,
                    $order->receiver_name,
                    $order->receiver_contact,
                    $order->receiver_email,
                    $order->receiver_address,
                    ucfirst($box->box_type),
                    $box->quantity,
                    "{$box->length}x{$box->height}x{$box->width}",
                    $box->fee,
                    $box->fee * $box->quantity,
                ];
            }
        }

        return $rows;
    }
}
