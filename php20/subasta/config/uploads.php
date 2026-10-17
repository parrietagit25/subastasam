<?php

const UPLOAD_MAX_BYTES = 50 * 1024 * 1024;

const UPLOAD_ALLOWED_TYPES = [
    'pdf'  => ['application/pdf'],
    'jpg'  => ['image/jpeg'],
    'jpeg' => ['image/jpeg'],
    'jfif' => ['image/jpeg'],
    'png'  => ['image/png'],
    'gif'  => ['image/gif'],
    'bmp'  => ['image/bmp', 'image/x-ms-bmp'],
    'webp' => ['image/webp'],
    'tif'  => ['image/tiff'],
    'tiff' => ['image/tiff'],
    'heic' => ['image/heic', 'image/heif'],
    'doc'  => ['application/msword', 'application/vnd.ms-office', 'application/cdfv2', 'application/x-ole-storage'],
    'xls'  => ['application/vnd.ms-excel', 'application/vnd.ms-office', 'application/cdfv2', 'application/x-ole-storage'],
    'docx' => ['application/vnd.openxmlformats-officedocument.wordprocessingml.document', 'application/zip', 'application/octet-stream'],
    'xlsx' => ['application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', 'application/zip', 'application/octet-stream'],
    'xlsm' => ['application/vnd.ms-excel.sheet.macroenabled.12', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', 'application/zip', 'application/octet-stream'],
];

/**
 * @return array{ok: bool, path?: string, error?: string}
 */
function store_uploaded_document(array $file, string $targetDir): array
{
    $tmpName = (string) ($file['tmp_name'] ?? '');

    if (($file['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK || !is_uploaded_file($tmpName)) {
        return ['ok' => false, 'error' => 'No se recibió el archivo correctamente.'];
    }

    if ((int) $file['size'] > UPLOAD_MAX_BYTES) {
        return ['ok' => false, 'error' => 'El archivo supera el tamaño máximo de 50 MB.'];
    }

    $extension = strtolower(pathinfo((string) ($file['name'] ?? ''), PATHINFO_EXTENSION));
    if (!isset(UPLOAD_ALLOWED_TYPES[$extension])) {
        return ['ok' => false, 'error' => 'Tipo de archivo no permitido. Formatos aceptados: PDF, imágenes, Word o Excel.'];
    }

    $mime = strtolower((string) (new finfo(FILEINFO_MIME_TYPE))->file($tmpName));
    if (!in_array($mime, UPLOAD_ALLOWED_TYPES[$extension], true)) {
        return ['ok' => false, 'error' => 'El contenido del archivo no corresponde a su extensión.'];
    }

    $targetDir = rtrim($targetDir, '/') . '/';
    if (!is_dir($targetDir) && !mkdir($targetDir, 0755, true)) {
        return ['ok' => false, 'error' => 'No se pudo preparar la carpeta de destino.'];
    }

    $targetFile = $targetDir . bin2hex(random_bytes(16)) . '.' . $extension;
    if (!move_uploaded_file($tmpName, $targetFile)) {
        return ['ok' => false, 'error' => 'No se pudo guardar el archivo.'];
    }

    return ['ok' => true, 'path' => $targetFile];
}
