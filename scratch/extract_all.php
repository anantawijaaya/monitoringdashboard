<?php

$logPath = 'C:\\Users\\Ananta Wijaya\\.gemini\\antigravity-ide\\brain\\0359fecb-f656-4fe8-8509-3a2f4961c3dd\\.system_generated\\logs\\transcript_full.jsonl';
$handle = fopen($logPath, 'r');
if ($handle) {
    $lineNum = 0;
    while (($line = fgets($handle)) !== false) {
        $lineNum++;
        $data = json_decode($line, true);
        if (isset($data['tool_calls'])) {
            foreach ($data['tool_calls'] as $tc) {
                $name = $tc['function']['name'] ?? '';
                $args = $tc['function']['arguments'] ?? [];
                if (is_string($args)) {
                    $args = json_decode($args, true) ?? [];
                }
                if (isset($args['TargetFile'])) {
                    echo "Line $lineNum: $name -> " . $args['TargetFile'] . "\n";
                    if (isset($args['CodeContent'])) {
                        file_put_contents('scratch/saved_' . basename($args['TargetFile']), $args['CodeContent']);
                        echo "  Saved CodeContent (" . strlen($args['CodeContent']) . " bytes)\n";
                    }
                    if (isset($args['ReplacementContent'])) {
                        file_put_contents('scratch/saved_repl_' . $lineNum . '_' . basename($args['TargetFile']), $args['ReplacementContent']);
                        echo "  Saved ReplacementContent (" . strlen($args['ReplacementContent']) . " bytes)\n";
                    }
                }
            }
        }
    }
    fclose($handle);
}
