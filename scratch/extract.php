<?php

$logPath = 'C:\\Users\\Ananta Wijaya\\.gemini\\antigravity-ide\\brain\\0359fecb-f656-4fe8-8509-3a2f4961c3dd\\.system_generated\\logs\\transcript_full.jsonl';
$handle = fopen($logPath, 'r');
$saved = [];
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
                        $saved[$tf] = $args['CodeContent'];
                    }
                }
            }
        }
    }
    fclose($handle);
}

foreach ($saved as $file => $content) {
    echo "Writing " . $file . " (" . strlen($content) . " bytes)\n";
    file_put_contents($file, $content);
}
