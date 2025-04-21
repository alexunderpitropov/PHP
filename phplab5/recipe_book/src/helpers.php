<?php
function redirect(string $url): void {
    header("Location: $url");
    exit;
}

function safe(?string $text): string {
    return htmlspecialchars($text ?? '', ENT_QUOTES, 'UTF-8');
}

function generateCsrfToken(): string {
    $token = bin2hex(random_bytes(32));
    $_SESSION['csrf_token'] = $token;
    return $token;
}

function verifyCsrfToken(string $token): bool {
    return isset($_SESSION['csrf_token']) && $token === $_SESSION['csrf_token'];
}