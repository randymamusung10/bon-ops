<?php

namespace App\Exports;

use App\Models\Logistic\Purchasing\SupplierInvoiceItem;
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

class ItemizedPurchaseReportExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithStyles, WithColumnFormatting, WithStrictNullComparison
{
    protected $request;

    public function __construct($request)
    {
        $this->request = $request;
    }

    public function collection()
    {
        $tenantId = Auth::user()->tenant_id ?? 1;

        $query = SupplierInvoiceItem::with(['supplierInvoice.supplier', 'product', 'unit'])
            ->whereHas('supplierInvoice', function ($q) use ($tenantId) {
                $q->where('tenant_id', $tenantId);
            });

        if (!empty($this->request['start_date']) && !empty($this->request['end_date'])) {
            $query->whereHas('supplierInvoice', function ($q) {
                $q->whereBetween('date', [$this->request['start_date'], $this->request['end_date']]);
            });
        }

        if ($this->request['status'] && $this->request['status'] !== 'all') {
            $query->whereHas('supplierInvoice', function ($q) {
                $q->where('status', $this->request['status']);
            });
        }

        $data = $query->orderBy('created_at', 'desc')->get();

        $totalQty = 0;
        $totalSubtotal = 0;
        foreach ($data as $item) {
            $totalQty += $item->quantity ? (float) $item->quantity : 0;
            $totalSubtotal += $item->total_price ? (float) $item->total_price : 0;
        }

        $data->push((object)[
            'is_footer' => true,
            'total_qty' => $totalQty,
            'total_subtotal' => $totalSubtotal,
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
            ['LAPORAN PEMBELIAN PER PRODUK'],
            ['Periode: ' . $periode],
            ['Dicetak Pada: ' . $printDate . ' | Oleh: ' . $printedBy],
            [],
            [
                'Tanggal', 'Nomor Invoice', 'Supplier', 
                'Produk', 'Qty', 'Satuan', 'Harga Beli', 'Subtotal', 'Status'
            ]
        ];
    }

    public function map($item): array
    {
        if (isset($item->is_footer) && $item->is_footer) {
            return [
                'GRAND TOTAL',
                '',
                '',
                '',
                $item->total_qty,
                '',
                '',
                $item->total_subtotal,
                ''
            ];
        }

        $invoice = $item->supplierInvoice;
        $product = $item->product;

        return [
            $invoice ? Carbon::parse($invoice->date)->format('Y-m-d') : '-',
            $invoice ? $invoice->supplier_invoice_number : '-',
            $invoice && $invoice->supplier ? $invoice->supplier->name : '-',
            $product ? $product->name : '-',
            $item->quantity ? (float) $item->quantity : 0,
            $item->unit ? $item->unit->code : '',
            $item->unit_price ? (float) $item->unit_price : 0,
            $item->total_price ? (float) $item->total_price : 0,
            $invoice ? $invoice->status : '-'
        ];
    }

    public function columnFormats(): array
    {
        return [
            'E' => '#,##0.00', // Qty
            'G' => '#,##0.00', // Harga Beli
            'H' => '#,##0.00', // Subtotal
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
