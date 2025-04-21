<?php
ob_start();
?>
<h2>Edit Recipe</h2>
<form method="POST" action="<?php echo APP_URL; ?>?route=update&id=<?php echo $recipe['id']; ?>">
    <input type="hidden" name="csrf_token" value="<?php echo safe($csrfToken); ?>">
    <div>
        <label>Title</label>
        <input type="text" name="title" value="<?php echo safe($recipe['title']); ?>">
        <?php if (isset($errors['title'])): ?>
            <p class="error"><?php echo safe($errors['title']); ?></p>
        <?php endif; ?>
    </div>
    <div>
        <label>Category</label>
        <select name="category_id">
            <?php foreach ($categories as $cat): ?>
                <option value="<?php echo $cat['id']; ?>" <?php echo $cat['id'] == $recipe['category_id'] ? 'selected' : ''; ?>>
                    <?php echo safe($cat['name']); ?>
                </option>
            <?php endforeach; ?>
        </select>
        <?php if (isset($errors['category_id'])): ?>
            <p class="error"><?php echo safe($errors['category_id']); ?></p>
        <?php endif; ?>
    </div>
    <div>
        <label>Ingredients</label>
        <textarea name="ingredients"><?php echo safe($recipe['ingredients']); ?></textarea>
        <?php if (isset($errors['ingredients'])): ?>
            <p class="error"><?php echo safe($errors['ingredients']); ?></p>
        <?php endif; ?>
    </div>
    <div>
        <label>Description</label>
        <textarea name="description"><?php echo safe($recipe['description'] ?? ''); ?></textarea>
    </div>
    <div>
        <label>Steps</label>
        <textarea name="steps"><?php echo safe($recipe['steps']); ?></textarea>
        <?php if (isset($errors['steps'])): ?>
            <p class="error"><?php echo safe($errors['steps']); ?></p>
        <?php endif; ?>
    </div>
    <button type="submit">Update</button>
</form>
<?php
$pageContent = ob_get_clean();
$pageTitle = 'Edit: ' . safe($recipe['title']);
require __DIR__ . '/../layout.php';