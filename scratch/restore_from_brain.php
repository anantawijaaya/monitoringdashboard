<?php

$brainDir = 'C:\\Users\\Ananta Wijaya\\.gemini\\antigravity-ide\\brain';
$iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($brainDir));

$latestFiles = [];

foreach ($iterator as $file) {
    if ($file->isFile() && ($file->getFilename() === 'transcript_full.jsonl' || $file->getFilename() === 'transcript.jsonl')) {
        $filePath = $file->getPathname();
        $handle = fopen($filePath, 'r');
        if ($handle) {
            while (($line = fgets($handle)) !== false) {
                if (strpos($line, 'index.blade.php') !== false) {
                    $data = json_decode($line, true);
                    if (isset($data['tool_calls'])) {
                        foreach ($data['tool_calls'] as $tc) {
                            $args = $tc['function']['arguments'] ?? [];
                            if (is_string($args)) {
                                $args = json_decode($args, true) ?? [];
                            }
                            if (isset($args['TargetFile']) && isset($args['CodeContent'])) {
                                $tf = $args['TargetFile'];
                                if (strpos($tf, 'budget-bk') !== false) {
                                    $latestFiles[$tf] = $args['CodeContent'];
                                    echo "Found " . $tf . " (" . strlen($args['CodeContent']) . " bytes) in " . $filePath . "\n";
                                }
                            }
                        }
                    }
                }
            }
            fclose($handle);
        }
    }
}

foreach ($latestFiles as $targetFile => $content) {
    echo "Restoring " . $targetFile . " (" . strlen($content) . " bytes)\n";
    file_put_contents($targetFile, $content);
}
