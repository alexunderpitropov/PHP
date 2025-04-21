<?php
ob_start();
?>
<h2>All Recipes</h2>
<?php if (!$recipes): ?>
    <p>No recipes found.</p>
<?php else: ?>
    <ul class="recipe-list">
        <?php foreach ($recipes as $recipe): ?>
            <li>
                <a href="<?php echo APP_URL; ?>?route=show&id=<?php echo $recipe['id']; ?>">
                    <?php echo safe($recipe['title']); ?>
                </a>
                (<?php echo safe($categories[$recipe['category_id']]['name']); ?>)
            </li>
        <?php endforeach; ?>
    </ul>
    <div class="pagination">
        <?php if ($page > 1): ?>
            <a href="<?php echo APP_URL; ?>?page=<?php echo $page - 1; ?>">« Previous</a>
        <?php endif; ?>
        <span>Page <?php echo $page; ?> of <?php echo $totalPages; ?></span>
        <?php if ($page < $totalPages): ?>
            <a href="<?php echo APP_URL; ?>?page=<?php echo $page + 1; ?>">Next »</a>
        <?php endif; ?>
    </div>
<?php endif; ?>
<?php
$pageContent = ob_get_clean();
$pageTitle = 'All Recipes';
require __DIR__ . '/layout.php';