<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$validator = validator([
    'date' => '2026-06-19',
    'source_branch_id' => 1,
    'source_warehouse_id' => 1,
    'destination_branch_id' => 1,
    'destination_warehouse_id' => 2,
    'items' => [
        [
            'product_id' => 1,
            'qty' => '1000.50'
        ]
    ]
], [
    'date' => ['required', 'date'],
    'source_branch_id' => ['required', 'exists:branches,id'],
    'source_warehouse_id' => ['required', 'exists:warehouses,id'],
    'destination_branch_id' => ['required', 'exists:branches,id'],
    'destination_warehouse_id' => ['required', 'exists:warehouses,id', 'different:source_warehouse_id'],
    'notes' => ['nullable', 'string'],
    
    'items' => ['required', 'array', 'min:1'],
    'items.*.product_id' => ['required', 'exists:products,id'],
    'items.*.qty' => ['required', 'numeric', 'min:0.01'],
    'items.*.notes' => ['nullable', 'string'],
]);

if ($validator->fails()) {
    echo "Validation failed:\n";
    print_r($validator->errors()->toArray());
} else {
    echo "Validation passed!\n";
}
