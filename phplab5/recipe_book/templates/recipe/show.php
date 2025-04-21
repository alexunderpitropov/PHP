<?php
ob_start();
?>
<h2><?php echo safe($recipe['title']); ?></h2>
<p><strong>Category:</strong> <?php echo safe($category['name']); ?></p>
<p><strong>Ingredients:</strong> <?php echo nl2br(safe($recipe['ingredients'])); ?></p>
<p><strong>Description:</strong> <?php echo nl2br(safe($recipe['description'] ?? '')); ?></p>
<p><strong>Steps:</strong> <?php echo nl2br(safe($recipe['steps'])); ?></p>
<div>
    <a href="<?php echo APP_URL; ?>?route=edit&id=<?php echo $recipe['id']; ?>">Edit</a>
    <form method="POST" action="<?php echo APP_URL; ?>?route=delete&id=<?php echo $recipe['id']; ?>" style="display:inline;">
        <input type="hidden" name="csrf_token" value="<?php echo safe($csrfToken); ?>">
        <button type="submit" onclick="return confirm('Delete recipe?')">Delete</button>
    </form>
</div>
<?php
$pageContent = ob_get_clean();
$pageTitle = safe($recipe['title']);
require __DIR__ . '/../layout.php';