<?php

$logPath = 'C:\\Users\\Ananta Wijaya\\.gemini\\antigravity-ide\\brain\\0359fecb-f656-4fe8-8509-3a2f4961c3dd\\.system_generated\\logs\\transcript_full.jsonl';
$handle = fopen($logPath, 'r');
if ($handle) {
    while (($line = fgets($handle)) !== false) {
        if (strpos($line, 'tool_calls') !== false && strpos($line, 'index.blade.php') !== false) {
            $data = json_decode($line, true);
            if (isset($data['tool_calls'])) {
                echo "Line has tool_calls count: " . count($data['tool_calls']) . "\n";
                foreach ($data['tool_calls'] as $tc) {
                    print_r($tc);
                }
            }
        }
    }
    fclose($handle);
}
