<?php
$dirs = [__DIR__ . '/student', __DIR__ . '/teacher', __DIR__ . '/admin'];

foreach ($dirs as $dir) {
    $actor = basename($dir);
    $files = glob($dir . '/*.php');
    
    foreach ($files as $file) {
        $content = file_get_contents($file);
        
        // Skip if already refactored
        if (strpos($content, "require __DIR__ . '/../layouts/header.php';") !== false) {
            continue;
        }

        // Extract title
        $title = 'Dashboard';
        if (preg_match('/<title>(.*?)<\/title>/is', $content, $m)) {
            $title = trim($m[1]);
        }
        
        // Extract extraHead (styles, external scripts like chart.js)
        $extraHead = '';
        if (preg_match('/<\/title>\s*(.*?)\s*<\/head>/is', $content, $m)) {
            $headContent = $m[1];
            // remove standard style.css if it's there
            $headContent = preg_replace('/<link rel="stylesheet"[^>]+style\.css">/i', '', $headContent);
            $extraHead = trim($headContent);
        }

        $bodyContent = '';
        if (preg_match('/<main[^>]*>(.*?)<\/main>/is', $content, $m)) {
            $bodyContent = $m[1];
        } else if (preg_match('/<div class="container">(.*?)<script/is', $content, $m) && strpos($file, 'course-payment') !== false) {
            $bodyContent = '<div class="container">' . $m[1];
        } else if (preg_match('/<body>(.*?)<script src="\/assets\/js\/api\.js">/is', $content, $m)) {
            // For learning.php which has learning-layout
            $bodyContent = preg_replace('/<nav.*?<\/nav>/is', '', $m[1]); // Remove old nav
        } else {
            echo "Body content not found for $file\n";
            continue;
        }

        // Extract extra scripts
        $extraScripts = '';
        if (preg_match('/<script src="\/assets\/js\/app\.js"><\/script>\s*(.*?)<\/body>/is', $content, $m)) {
            $extraScripts = trim($m[1]);
        } else if (preg_match('/<script[^>]*>(?!.*(api\.js|app\.js)).*?<\/body>/is', $content, $m)) {
            // fallback
        }

        // Build new content
        $newContent = "<?php\n";
        $newContent .= "\$pageTitle = '" . addslashes($title) . "';\n";
        // course-payment or learning might not have a sidebar in the new design if actor=guest, but actually they do have sidebars, or they have a specialized layout?
        // Wait, course-payment and learning don't have sidebars, they use guest-like layout or custom layout.
        if (strpos($file, 'course-payment') !== false || strpos($file, 'learning') !== false) {
            $newContent .= "\$actor = 'guest';\n"; // No sidebar
        } else {
            $newContent .= "\$actor = '$actor';\n";
        }

        if ($extraHead) {
            $newContent .= "ob_start();\n?>\n$extraHead\n<?php\n\$extraHead = ob_get_clean();\n";
        }
        $newContent .= "require __DIR__ . '/../layouts/header.php';\n";
        $newContent .= "?>\n\n";
        
        $newContent .= trim($bodyContent) . "\n\n";

        if ($extraScripts) {
            $newContent .= "<?php ob_start(); ?>\n";
            $newContent .= $extraScripts . "\n";
            $newContent .= "<?php\n\$extraScripts = ob_get_clean();\n?>\n";
        }
        
        $newContent .= "<?php require __DIR__ . '/../layouts/footer.php'; ?>\n";

        file_put_contents($file, $newContent);
        echo "Refactored Dashboard: $file\n";
    }
}
