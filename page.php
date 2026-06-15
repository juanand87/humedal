<?php
include 'panel/config.php';

$slug = $_GET['slug'] ?? '';

if (empty($slug)) {
    header('Location: index.php');
    exit();
}

$stmt = executeQuery("SELECT * FROM pages WHERE slug = ?", [$slug]);
$page = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$page) {
    header('Location: index.php');
    exit();
}

require __DIR__ . '/includes/header.php';
?>

<main class="py-5">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <article>
                    <h1 class="display-4 fw-bold mb-4"><?php echo htmlspecialchars($page['title']); ?></h1>
                    <div class="content">
                        <?php echo $page['content']; ?>
                    </div>
                </article>
            </div>
        </div>
    </div>
</main>

<?php
require __DIR__ . '/includes/footer.php';
?>
