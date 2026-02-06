<?php
// Reusable toast component (server-rendered messages only).
// Usage: set `$error`, `$success`, or provide `$toasts = [['type'=>'error'|'success','message'=>...], ...]`.
$toasts = $toasts ?? [];
if (!empty($error)) {
    $toasts[] = ['type' => 'error', 'message' => $error];
}
if (!empty($success)) {
    $toasts[] = ['type' => 'success', 'message' => $success];
}

if (empty($toasts)) {
    return;
}
?>
<div class="toast-stack fixed top-4 right-4 z-50 flex flex-col gap-3 pointer-events-none" role="status" aria-live="polite" aria-atomic="true">
    <?php foreach ($toasts as $t):
        $type = $t['type'] ?? 'error';
        $msg = htmlspecialchars($t['message'] ?? '', ENT_QUOTES, 'UTF-8');
        if ($type === 'success'):
    ?>
        <div class="toast toast-success text-lg opacity-0 -translate-y-2 transform transition-opacity transition-transform duration-200 max-w-sm rounded-md px-4 py-3 shadow-xl pointer-events-auto bg-white text-gray-800 border-2 border-green-500">
            <div class="flex items-center gap-3">
                <div class="flex-1 pr-2"><?php echo $msg; ?></div>
                <button type="button" class="toast-close ml-2 text-gray-600 hover:text-gray-800 p-1 rounded focus:outline-none focus:ring-1 focus:ring-gray-200" aria-label="Close" title="Close">&times;</button>
            </div>
        </div>
    <?php else: ?>
        <div class="toast toast-error text-lg opacity-0 -translate-y-2 transform transition-opacity transition-transform duration-200 max-w-sm rounded-md px-4 py-3 shadow-xl pointer-events-auto bg-white text-gray-800 border-2 border-red-500">
            <div class="flex items-center gap-3">
                <div class="flex-1 pr-2"><?php echo $msg; ?></div>
                <button type="button" class="toast-close ml-2 text-gray-600 hover:text-gray-800 p-1 rounded focus:outline-none focus:ring-1 focus:ring-gray-200" aria-label="Close" title="Close">&times;</button>
            </div>
        </div>
    <?php endif; endforeach; ?>
</div>
