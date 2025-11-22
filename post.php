<?php
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header('Location: index.php');
    exit;
}

$postId = $_GET['id'];

$post = null;
if (file_exists('data/posts.json')) {
    $allPosts = json_decode(file_get_contents('data/posts.json'), true) ?: [];
    foreach ($allPosts as $p) {
        if ($p['id'] === $postId) {
            $post = $p;
            break;
        }
    }
}

if (!$post) {
    header('Location: index.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['comment'])) {
    $author = trim($_POST['author'] ?? 'Аноним');
    $comment = trim($_POST['comment']);
    
    if (!empty($comment)) {
        $commentFile = "data/comments/{$postId}.json";
        $comments = file_exists($commentFile) ? json_decode(file_get_contents($commentFile), true) : [];
        
        $comments[] = [
            'id' => uniqid(),
            'author' => $author,
            'text' => $comment,
            'date' => date('Y-m-d H:i:s')
        ];
        
        file_put_contents($commentFile, json_encode($comments));
        header("Location: post.php?id={$postId}");
        exit;
    }
}

$comments = [];
$commentFile = "data/comments/{$postId}.json";
if (file_exists($commentFile)) {
    $comments = json_decode(file_get_contents($commentFile), true) ?: [];
    $comments = array_reverse($comments); 
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($post['title']) ?> | Анонимная доска</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <a href="index.php" class="btn">На главную</a>
        <a href="posts.php" class="btn">Все посты</a>
        
        <div class="post">
            <h2><?= htmlspecialchars($post['title']) ?></h2>
            <div class="meta">
                <span class="date"><?= date('d.m.Y H:i', strtotime($post['date'])) ?></span>
            </div>
            <p><?= nl2br(htmlspecialchars($post['content'])) ?></p>
        </div>
        
        <h3>Комментарии (<?= count($comments) ?>)</h3>
        
        <form method="POST" class="comment-form">
            <div class="form-group">
                <label for="author">Имя (необязательно):</label>
                <input type="text" id="author" name="author" placeholder="Аноним">
            </div>
            <div class="form-group">
                <label for="comment">Комментарий:</label>
                <textarea id="comment" name="comment" required></textarea>
            </div>
            <button type="submit" class="btn">Отправить</button>
        </form>
        
        <div class="comments">
            <?php if (empty($comments)): ?>
                <p>Пока нет комментариев. Будьте первым!</p>
            <?php else: ?>
                <?php foreach ($comments as $comment): ?>
                    <div class="comment">
                        <div class="comment-meta">
                            <span class="author"><?= htmlspecialchars($comment['author']) ?></span>
                            <span class="date"><?= date('d.m.Y H:i', strtotime($comment['date'])) ?></span>
                        </div>
                        <p><?= nl2br(htmlspecialchars($comment['text'])) ?></p>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</body>

</html>
