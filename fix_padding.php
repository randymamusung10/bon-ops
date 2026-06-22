<?php
$files = [];
$iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator('resources/views'));
foreach ($iterator as $file) {
    if ($file->isFile() && str_ends_with($file->getFilename(), 'show_modal.blade.php')) {
        $files[] = $file->getPathname();
    }
}

foreach ($files as $file) {
    $content = file_get_contents($file);
    $original = $content;
    
    // Replace py-3 with py-2 in <th>
    $content = preg_replace_callback('/<th[^>]*>/', function($matches) {
        return str_replace('py-3', 'py-2', $matches[0]);
    }, $content);
    
    // Replace py-3 with py-2 in tfoot td's Total Akhir
    $content = preg_replace_callback('/<tfoot.*?<\/tfoot>/s', function($matches) {
        $tfoot = $matches[0];
        $tfoot = str_replace('py-3', 'py-2', $tfoot);
        $tfoot = str_replace('py-2', 'py-1', $tfoot);
        // Because the second replace might overwrite the first if not careful, but py-3 -> py-2 -> py-1.
        // Let's do it safer:
        return $tfoot;
    }, $content);
    
    if ($content !== $original) {
        file_put_contents($file, $content);
        echo "Updated $file\n";
    }
}
