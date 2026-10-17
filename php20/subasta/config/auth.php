<?php

function require_login(): void
{
    if (!isset($_SESSION['email'])) {
        header('Location: index.php');
        exit();
    }
}

function user_has_role(string ...$roles): bool
{
    return in_array($_SESSION['tipo_user'] ?? '', $roles, true);
}

function require_role(string ...$roles): void
{
    if (!user_has_role(...$roles)) {
        http_response_code(403);
        exit('No tiene permisos para realizar esta acción.');
    }
}
