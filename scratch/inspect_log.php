<?php

$logPath = 'C:\\Users\\Ananta Wijaya\\.gemini\\antigravity-ide\\brain\\0359fecb-f656-4fe8-8509-3a2f4961c3dd\\.system_generated\\logs\\transcript_full.jsonl';
$handle = fopen($logPath, 'r');
if ($handle) {
    $lineNum = 0;
    while (($line = fgets($handle)) !== false) {
        $lineNum++;
        if (strpos($line, 'index.blade.php') !== false) {
            echo "Line " . $lineNum . " contains index.blade.php (len: " . strlen($line) . ")\n";
            $data = json_decode($line, true);
            echo "Keys: " . implode(', ', array_keys($data ?? [])) . "\n";
            if (isset($data['content'])) {
                if (is_array($data['content'])) {
                    foreach ($data['content'] as $c) {
                        if (is_array($c) && isset($c['text'])) {
                            echo "Content text sample: " . substr($c['text'], 0, 100) . "\n";
                        }
                    }
                } elseif (is_string($data['content'])) {
                    echo "Content string sample: " . substr($data['content'], 0, 100) . "\n";
                }
            }
        }
    }
    fclose($handle);
}
