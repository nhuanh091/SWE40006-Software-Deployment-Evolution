<?php
declare(strict_types=1);
date_default_timezone_set('Asia/Bangkok');

$file = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'phpnotes.json';
$notes = file_exists($file) ? (json_decode((string)file_get_contents($file), true) ?: []) : [];

function e(string $s): string {
    return htmlspecialchars($s, ENT_QUOTES, 'UTF-8');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'add') {
        $text  = trim((string)($_POST['text'] ?? ''));
        $color = in_array($_POST['color'] ?? '', ['yellow', 'pink', 'blue', 'green'], true) ? $_POST['color'] : 'yellow';
        if ($text !== '') {
            $text = function_exists('mb_substr') ? mb_substr($text, 0, 200) : substr($text, 0, 200);
            array_unshift($notes, [
                'id'    => uniqid(),
                'text'  => $text,
                'color' => $color,
                'time'  => date('d M Y, H:i'),
            ]);
        }
    } elseif ($action === 'delete') {
        $id = (string)($_POST['id'] ?? '');
        $notes = array_values(array_filter($notes, fn($n) => $n['id'] !== $id));
    } elseif ($action === 'clear') {
        $notes = [];
    }

    file_put_contents($file, json_encode($notes, JSON_INVALID_UTF8_SUBSTITUTE), LOCK_EX);
    header('Location: ' . strtok($_SERVER['REQUEST_URI'], '?'));
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>PHP Notes Board</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="style.css">
</head>
<body>
<main class="wrap">
    <header class="hero">
        <div class="logo"><i class="bi bi-pin-angle-fill"></i></div>
        <div>
            <h1>PHP Notes Board</h1>
            <p>Pin quick thoughts. Powered by PHP <?= e(PHP_VERSION) ?> on Azure.</p>
        </div>
    </header>

    <section class="panel">
        <form method="post" class="add">
            <input type="hidden" name="action" value="add">
            <input type="text" name="text" maxlength="200" placeholder="Write a note..." required>
            <div class="colors">
                <?php foreach (['yellow', 'pink', 'blue', 'green'] as $i => $c): ?>
                    <label class="dot <?= $c ?>" title="<?= ucfirst($c) ?>">
                        <input type="radio" name="color" value="<?= $c ?>" <?= $i === 0 ? 'checked' : '' ?>>
                        <span></span>
                    </label>
                <?php endforeach; ?>
            </div>
            <button class="btn"><i class="bi bi-plus-lg"></i> Pin</button>
        </form>
    </section>

    <?php if (empty($notes)): ?>
        <div class="empty">
            <i class="bi bi-sticky"></i>
            <p>No notes yet. Pin your first one above.</p>
        </div>
    <?php else: ?>
        <section class="grid">
            <?php foreach ($notes as $n): ?>
                <article class="note <?= e($n['color']) ?>">
                    <p><?= e($n['text']) ?></p>
                    <footer>
                        <small><i class="bi bi-clock"></i> <?= e($n['time']) ?></small>
                        <form method="post">
                            <input type="hidden" name="action" value="delete">
                            <input type="hidden" name="id" value="<?= e($n['id']) ?>">
                            <button class="trash" title="Delete"><i class="bi bi-trash3"></i></button>
                        </form>
                    </footer>
                </article>
            <?php endforeach; ?>
        </section>
        <form method="post" class="clear">
            <input type="hidden" name="action" value="clear">
            <button class="link"><i class="bi bi-eraser"></i> Clear all notes</button>
        </form>
    <?php endif; ?>

    <section class="info">
        <h2><i class="bi bi-cpu"></i> Server info</h2>
        <ul>
            <li><span>PHP version</span><strong><?= e(PHP_VERSION) ?></strong></li>
            <li><span>Server API</span><strong><?= e(PHP_SAPI) ?></strong></li>
            <li><span>Operating system</span><strong><?= e(PHP_OS_FAMILY) ?></strong></li>
            <li><span>Web server</span><strong><?= e($_SERVER['SERVER_SOFTWARE'] ?? 'PHP built-in server') ?></strong></li>
            <li><span>Server time</span><strong><?= e(date('d M Y, H:i:s T')) ?></strong></li>
        </ul>
    </section>
</main>
</body>
</html>