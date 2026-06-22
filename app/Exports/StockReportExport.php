<?php

namespace App\Exports;

use App\Models\Logistic\Inventory\InventoryBalance;
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

class StockReportExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithStyles, WithColumnFormatting, WithStrictNullComparison
{
    protected $request;

    public function __construct($request)
    {
        $this->request = $request;
    }

    public function collection()
    {
        $tenantId = Auth::user()->tenant_id ?? 1;

        $query = InventoryBalance::with(['product', 'warehouse'])->where('tenant_id', $tenantId);

        if (!empty($this->request['warehouse_id'])) {
            $query->where('warehouse_id', $this->request['warehouse_id']);
        }

        $data = $query->get();

        $totalValuation = 0;
        foreach ($data as $item) {
            $cost = $item->product && $item->product->cost ? (float) $item->product->cost : 0;
            $valuation = (float) $item->qty * $cost;
            $totalValuation += $valuation;
        }

        $data->push((object)[
            'is_footer' => true,
            'total_valuation' => $totalValuation,
        ]);

        return $data;
    }

    public function headings(): array
    {
        $tenantName = Auth::user()->tenant->name ?? 'BON-OPS ERP';
        
        $printDate = \Carbon\Carbon::now()->format('d M Y H:i');
        $printedBy = Auth::user()->name ?? 'System';

        return [
            [strtoupper($tenantName)],
            ['LAPORAN VALUASI SALDO STOK'],
            ['Kondisi per: ' . \Carbon\Carbon::now()->format('d M Y')],
            ['Dicetak Pada: ' . $printDate . ' | Oleh: ' . $printedBy],
            [],
            [
                'Kode Produk', 'Nama Produk', 'Gudang', 'Qty', 'Harga Pokok (HPP)', 'Total Valuasi'
            ]
        ];
    }

    public function map($balance): array
    {
        if (isset($balance->is_footer) && $balance->is_footer) {
            return [
                'GRAND TOTAL',
                '',
                '',
                '',
                '',
                $balance->total_valuation
            ];
        }

        $qty = $balance->qty ? (float) $balance->qty : 0;
        $cost = $balance->product && $balance->product->cost ? (float) $balance->product->cost : 0;
        $valuation = $qty * $cost;

        return [
            $balance->product ? $balance->product->code : '-',
            $balance->product ? $balance->product->name : '-',
            $balance->warehouse ? $balance->warehouse->name : '-',
            $qty,
            $cost,
            $valuation
        ];
    }

    public function columnFormats(): array
    {
        return [
            'D' => '#,##0.00', // Qty
            'E' => '#,##0.00', // Harga Pokok
            'F' => '#,##0.00', // Total Valuasi
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

        $sheet->mergeCells("A{$lastRow}:E{$lastRow}");
        $sheet->getStyle("A{$lastRow}:F{$lastRow}")->getFont()->setBold(true);
        $sheet->getStyle("A{$lastRow}")->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
        $sheet->getStyle("A{$lastRow}:F{$lastRow}")->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('FFF8FAFC');

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
