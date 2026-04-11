<?php
$content = file_get_contents('app/Views/candidate/dashboard.php');
$cutMarker = '<' . '?php endif; ?' . '>';
$idx = strrpos($content, $cutMarker);
if ($idx === false) { die('Cut marker not found'); }
$clean = substr($content, 0, $idx + strlen($cutMarker));
$addition = file_get_contents('fix_dashboard_addition.html');
file_put_contents('app/Views/candidate/dashboard.php', $clean . $addition);
echo 'Done';
