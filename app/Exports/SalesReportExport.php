<?php

namespace App\Exports;

use App\Models\Operational\Pos\PosOrder;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithStrictNullComparison;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class SalesReportExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithStyles, WithColumnFormatting, WithStrictNullComparison
{
    protected $request;

    public function __construct($request)
    {
        $this->request = $request;
    }

    public function collection()
    {
        $tenantId = Auth::user()->tenant_id ?? 1;
        $query = PosOrder::with('creator')->where('tenant_id', $tenantId);

        if (!empty($this->request['start_date']) && !empty($this->request['end_date'])) {
            $query->whereBetween('date', [$this->request['start_date'], $this->request['end_date']]);
        }

        if ($this->request['payment_status'] && $this->request['payment_status'] !== 'all') {
            $query->where('payment_status', $this->request['payment_status']);
        }

        $data = $query->orderBy('created_at', 'desc')->get();

        $totalAmount = 0;
        foreach ($data as $item) {
            $totalAmount += $item->grand_total ? (float) $item->grand_total : 0;
        }

        $data->push((object)[
            'is_footer' => true,
            'total_amount' => $totalAmount,
        ]);

        return $data;
    }

    public function headings(): array
    {
        $tenantName = Auth::user()->tenant->name ?? 'BON-OPS ERP';
        
        $periode = 'Semua Waktu';
        if (!empty($this->request['start_date']) && !empty($this->request['end_date'])) {
            $startDate = Carbon::parse($this->request['start_date'])->format('d M Y');
            $endDate = Carbon::parse($this->request['end_date'])->format('d M Y');
            $periode = "$startDate - $endDate";
        }

        $printDate = Carbon::now()->format('d M Y H:i');
        $printedBy = Auth::user()->name ?? 'System';

        return [
            [strtoupper($tenantName)],
            ['LAPORAN PENJUALAN'],
            ['Periode: ' . $periode],
            ['Dicetak Pada: ' . $printDate . ' | Oleh: ' . $printedBy],
            [],
            [
                'Tanggal', 'Nomor Order', 'Pelanggan', 'Kasir', 
                'Total Penjualan', 'Metode Pembayaran', 'Status Pembayaran'
            ]
        ];
    }

    public function map($order): array
    {
        if (isset($order->is_footer) && $order->is_footer) {
            return [
                'GRAND TOTAL',
                '',
                '',
                '',
                $order->total_amount,
                '',
                ''
            ];
        }

        return [
            Carbon::parse($order->date)->format('Y-m-d'),
            $order->order_number,
            $order->customer_name ?? 'Umum',
            $order->creator ? $order->creator->name : '-',
            $order->grand_total ? (float) $order->grand_total : 0,
            $order->payment_method ?? '-',
            $order->payment_status
        ];
    }

    public function columnFormats(): array
    {
        return [
            'E' => '#,##0.00', // Total Penjualan
        ];
    }

    public function styles(Worksheet $sheet)
    {
        $lastRow = $sheet->getHighestRow();
        $lastCol = $sheet->getHighestColumn();
        $range = 'A6:' . $lastCol . $lastRow;

        $sheet->mergeCells('A1:' . $lastCol . '1');
        $sheet->mergeCells('A2:' . $lastCol . '2');
        $sheet->mergeCells('A3:' . $lastCol . '3');
        $sheet->mergeCells('A4:' . $lastCol . '4');

        $sheet->mergeCells("A{$lastRow}:D{$lastRow}");
        $sheet->getStyle("A{$lastRow}:G{$lastRow}")->getFont()->setBold(true);
        $sheet->getStyle("A{$lastRow}")->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
        $sheet->getStyle("A{$lastRow}:G{$lastRow}")->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('FFF8FAFC');

        return [
            1 => [
                'font' => ['bold' => true, 'size' => 14, 'color' => ['argb' => 'FF1F2937']],
                'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER]
            ],
            2 => [
                'font' => ['bold' => true, 'size' => 16, 'color' => ['argb' => 'FF111827']],
                'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER]
            ],
            3 => [
                'font' => ['size' => 11, 'color' => ['argb' => 'FF4B5563']],
                'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER]
            ],
            4 => [
                'font' => ['italic' => true, 'size' => 10, 'color' => ['argb' => 'FF6B7280']],
                'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER]
            ],
            6 => [
                'font' => ['bold' => true, 'color' => ['argb' => 'FFFFFFFF']],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['argb' => 'FF334155']
                ],
                'alignment' => [
                    'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                    'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                ],
            ],
            $range => [
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                        'color' => ['argb' => 'FFCBD5E1'],
                    ],
                ],
            ],
        ];
    }
}
