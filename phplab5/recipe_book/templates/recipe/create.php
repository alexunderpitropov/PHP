<?php
ob_start();
?>
<h2>Add Recipe</h2>
<form method="POST" action="<?php echo APP_URL; ?>?route=save">
    <input type="hidden" name="csrf_token" value="<?php echo safe($csrfToken); ?>">
    <div>
        <label>Title</label>
        <input type="text" name="title" value="<?php echo isset($_POST['title']) ? safe($_POST['title']) : ''; ?>">
        <?php if (isset($errors['title'])): ?>
            <p class="error"><?php echo safe($errors['title']); ?></p>
        <?php endif; ?>
    </div>
    <div>
        <label>Category</label>
        <select name="category_id">
            <?php foreach ($categories as $cat): ?>
                <option value="<?php echo $cat['id']; ?>" <?php echo (isset($_POST['category_id']) && $_POST['category_id'] == $cat['id']) ? 'selected' : ''; ?>>
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
        <textarea name="ingredients"><?php echo isset($_POST['ingredients']) ? safe($_POST['ingredients']) : ''; ?></textarea>
        <?php if (isset($errors['ingredients'])): ?>
            <p class="error"><?php echo safe($errors['ingredients']); ?></p>
        <?php endif; ?>
    </div>
    <div>
        <label>Description</label>
        <textarea name="description"><?php echo isset($_POST['description']) ? safe($_POST['description']) : ''; ?></textarea>
    </div>
    <div>
        <label>Steps</label>
        <textarea name="steps"><?php echo isset($_POST['steps']) ? safe($_POST['steps']) : ''; ?></textarea>
        <?php if (isset($errors['steps'])): ?>
            <p class="error"><?php echo safe($errors['steps']); ?></p>
        <?php endif; ?>
    </div>
    <button type="submit">Save</button>
</form>
<?php
$pageContent = ob_get_clean();
$pageTitle = 'New Recipe';
require __DIR__ . '/../layout.php';