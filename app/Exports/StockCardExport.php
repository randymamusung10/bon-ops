<?php

namespace App\Exports;

use App\Models\Logistic\Inventory\InventoryMovement;
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

class StockCardExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithStyles, WithColumnFormatting, WithStrictNullComparison
{
    protected $request;

    public function __construct($request)
    {
        $this->request = $request;
    }

    public function collection()
    {
        $tenantId = Auth::user()->tenant_id ?? 1;

        $query = InventoryMovement::with(['branch', 'warehouse', 'product.unit'])
            ->where('tenant_id', $tenantId);

        if (!empty($this->request['warehouse_id'])) {
            $query->where('warehouse_id', $this->request['warehouse_id']);
        }
        
        if (!empty($this->request['product_id'])) {
            $query->where('product_id', $this->request['product_id']);
        }

        $data = $query->orderBy('date', 'asc')->orderBy('id', 'asc')->get();

        $totalIn = 0;
        $totalOut = 0;
        foreach ($data as $item) {
            $totalIn += $item->qty_in ? (float) $item->qty_in : 0;
            $totalOut += $item->qty_out ? (float) $item->qty_out : 0;
        }

        $data->push((object)[
            'is_footer' => true,
            'total_in' => $totalIn,
            'total_out' => $totalOut,
        ]);

        return $data;
    }

    public function headings(): array
    {
        $tenantName = Auth::user()->tenant->name ?? 'BON-OPS ERP';
        
        $printDate = Carbon::now()->format('d M Y H:i');
        $printedBy = Auth::user()->name ?? 'System';

        return [
            [strtoupper($tenantName)],
            ['LAPORAN KARTU STOK (PERGERAKAN BARANG)'],
            ['Semua Riwayat Pergerakan'],
            ['Dicetak Pada: ' . $printDate . ' | Oleh: ' . $printedBy],
            [],
            [
                'Tanggal', 'Nomor Dokumen', 'Tipe Dokumen', 'Produk', 'Gudang', 
                'Saldo Awal', 'Masuk', 'Keluar', 'Saldo Akhir'
            ]
        ];
    }

    public function map($movement): array
    {
        if (isset($movement->is_footer) && $movement->is_footer) {
            return [
                'TOTAL MUTASI (MASUK / KELUAR)',
                '',
                '',
                '',
                '',
                '',
                $movement->total_in,
                $movement->total_out,
                ''
            ];
        }

        return [
            Carbon::parse($movement->date)->format('Y-m-d'),
            $movement->reference_number,
            $movement->reference_type,
            $movement->product ? $movement->product->name : '-',
            $movement->warehouse ? $movement->warehouse->name : '-',
            ($movement->balance_after - $movement->qty_in + $movement->qty_out) ? (float) ($movement->balance_after - $movement->qty_in + $movement->qty_out) : 0,
            $movement->qty_in ? (float) $movement->qty_in : 0,
            $movement->qty_out ? (float) $movement->qty_out : 0,
            $movement->balance_after ? (float) $movement->balance_after : 0
        ];
    }

    public function columnFormats(): array
    {
        return [
            'F' => '#,##0.00', // Saldo Awal
            'G' => '#,##0.00', // Masuk
            'H' => '#,##0.00', // Keluar
            'I' => '#,##0.00', // Saldo Akhir
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

        $sheet->mergeCells("A{$lastRow}:F{$lastRow}");
        $sheet->getStyle("A{$lastRow}:I{$lastRow}")->getFont()->setBold(true);
        $sheet->getStyle("A{$lastRow}")->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
        $sheet->getStyle("A{$lastRow}:I{$lastRow}")->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('FFF8FAFC');

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
