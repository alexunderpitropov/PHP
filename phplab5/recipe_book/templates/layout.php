<?php
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?php echo safe($pageTitle ?? 'Recipes'); ?></title>
    <link rel="stylesheet" href="<?php echo APP_URL; ?>assets/style.css">
</head>
<body>
    <div class="container">
        <div class="sidebar">
            <h3>Navigation</h3>
            <a href="<?php echo APP_URL; ?>">All Recipes</a>
            <a href="<?php echo APP_URL; ?>?route=create">New Recipe</a>
        </div>
        <div class="content">
            <h1>Recipes</h1>
            <?php echo $pageContent; ?>
        </div>
    </div>
    <footer>
        <p>© 2025 Recipes</p>
    </footer>
</body>
</html>