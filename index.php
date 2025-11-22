<?php
if (!file_exists('data')) mkdir('data');
if (!file_exists('data/comments')) mkdir('data/comments');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['title'])) {
    $title = trim($_POST['title']);
    $content = trim($_POST['content']);
    
    if (!empty($title) && !empty($content)) {
        $posts = file_exists('data/posts.json') ? json_decode(file_get_contents('data/posts.json'), true) : [];
        
        $newPost = [
            'id' => uniqid(),
            'title' => $title,
            'content' => $content,
            'date' => date('Y-m-d H:i:s')
        ];
        
        $posts[] = $newPost;
        file_put_contents('data/posts.json', json_encode($posts));
        
        header('Location: ?success=1');
        exit;
    }
}

$posts = [];
if (file_exists('data/posts.json')) {
    $allPosts = json_decode(file_get_contents('data/posts.json'), true) ?: [];
    $posts = array_slice(array_reverse($allPosts), 0, 5);
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Анонимная доска</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <h1>Анонимная доска</h1>
        
        <?php if (isset($_GET['success'])): ?>
            <div class="alert success">Пост успешно опубликован!</div>
        <?php endif; ?>
        
        <div class="buttons">
            <a href="posts.php" class="btn">Все посты</a>
        </div>
        
        <h2>Создать новый пост</h2>
        <form method="POST">
            <div class="form-group">
                <label for="title">Заголовок:</label>
                <input type="text" id="title" name="title" required>
            </div>
            <div class="form-group">
                <label for="content">Содержание:</label>
                <textarea id="content" name="content" required></textarea>
            </div>
            <button type="submit" class="btn">Опубликовать анонимно</button>
        </form>
        
        <h2>Последние посты</h2>
        <?php if (empty($posts)): ?>
            <p>Пока нет ни одного поста. Будьте первым!</p>
        <?php else: ?>
            <div class="posts">
                <?php foreach ($posts as $post): ?>
                    <div class="post">
                        <h3><a href="post.php?id=<?= $post['id'] ?>"><?= htmlspecialchars($post['title']) ?></a></h3>
                        <div class="meta">
                            <span class="date"><?= date('d.m.Y H:i', strtotime($post['date'])) ?></span>
                        </div>
                        <p><?= nl2br(htmlspecialchars($post['content'])) ?></p>
                        <a href="post.php?id=<?= $post['id'] ?>" class="btn small">Комментарии</a>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</body>

</html>
