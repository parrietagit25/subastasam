<?php

require_once __DIR__ . '/db.php';

const ACCESS_CODE_COOKIE = 'grupopcr';

function generate_access_code(): string
{
    $alphabet = 'ABCDEFGHJKLMNPQRSTUVWXYZ23456789';
    $code = '';
    for ($i = 0; $i < 10; $i++) {
        $code .= $alphabet[random_int(0, strlen($alphabet) - 1)];
    }

    return $code;
}

/** Los registros sin aprobar tienen codigo '0', por eso se exige stat = 2. */
function access_code_is_valid(?string $code): bool
{
    $code = strtoupper(trim((string) $code));
    if ($code === '' || $code === '0' || strlen($code) > 20) {
        return false;
    }

    $stmt = db_connect()->prepare('SELECT COUNT(*) FROM cc_subastas WHERE codigo = ? AND stat = 2');
    $stmt->execute([$code]);

    return (int) $stmt->fetchColumn() > 0;
}

function remember_access_code(string $code): void
{
    setcookie(ACCESS_CODE_COOKIE, strtoupper(trim($code)), [
        'expires' => time() + 365 * 24 * 60 * 60,
        'path' => '/',
        'secure' => true,
        'httponly' => true,
        'samesite' => 'Lax',
    ]);
}

function forget_access_code(): void
{
    setcookie(ACCESS_CODE_COOKIE, '', ['expires' => time() - 3600, 'path' => '/']);
}
