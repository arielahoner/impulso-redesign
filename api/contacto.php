<?php
declare(strict_types=1);

/* =========================================================
   Impulso Estratégico CX & AI — endpoint del formulario
   Envía el lead por email usando la API de Resend.
   Requiere: api/config.php (copia de api/config.example.php)
   ========================================================= */

header('Content-Type: application/json; charset=utf-8');
header('X-Content-Type-Options: nosniff');

function out(int $code, array $payload): void {
    http_response_code($code);
    echo json_encode($payload, JSON_UNESCAPED_UNICODE);
    exit;
}

function clean(string $v, int $max = 2000): string {
    $v = str_replace(["\r", "\0"], '', trim($v));
    return mb_substr($v, 0, $max);
}

if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') {
    out(405, ['ok' => false, 'error' => 'Método no permitido.']);
}

$configPath = __DIR__ . '/config.php';
if (!is_file($configPath)) {
    error_log('contacto.php: falta api/config.php');
    out(500, ['ok' => false, 'error' => 'El formulario aún no está configurado.']);
}
$cfg = require $configPath;

/* ---- Origen permitido ---------------------------------- */
$origin = $_SERVER['HTTP_ORIGIN'] ?? '';
if ($origin !== '' && !empty($cfg['allowed_origins']) && !in_array($origin, $cfg['allowed_origins'], true)) {
    out(403, ['ok' => false, 'error' => 'Origen no permitido.']);
}

/* ---- Honeypot ------------------------------------------ */
if (clean((string)($_POST['website'] ?? '')) !== '') {
    out(200, ['ok' => true]);
}

/* ---- Límite básico por IP (5 envíos / hora) ------------- */
$ip   = $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
$file = sys_get_temp_dir() . '/iecx_' . sha1($ip) . '.txt';
$hits = is_file($file) ? array_filter(array_map('intval', explode(',', (string)file_get_contents($file)))) : [];
$hits = array_values(array_filter($hits, static fn(int $t): bool => $t > time() - 3600));
if (count($hits) >= 5) {
    out(429, ['ok' => false, 'error' => 'Demasiados envíos. Escríbenos directo por email.']);
}
$hits[] = time();
@file_put_contents($file, implode(',', $hits));

/* ---- Validación ---------------------------------------- */
$name    = clean((string)($_POST['name'] ?? ''), 120);
$company = clean((string)($_POST['company'] ?? ''), 160);
$email   = clean((string)($_POST['email'] ?? ''), 160);
$size    = clean((string)($_POST['company_size'] ?? ''), 60);
$phone   = clean((string)($_POST['phone'] ?? ''), 40);
$message = clean((string)($_POST['message'] ?? ''), 5000);

$errors = [];
if ($name === '')                                       { $errors[] = 'Falta el nombre.'; }
if ($company === '')                                    { $errors[] = 'Falta la empresa.'; }
if (!filter_var($email, FILTER_VALIDATE_EMAIL))         { $errors[] = 'El email no es válido.'; }
if (mb_strlen($message) < 10)                           { $errors[] = 'El mensaje es muy corto.'; }
if ($errors) {
    out(422, ['ok' => false, 'error' => implode(' ', $errors)]);
}

/* ---- Composición del correo ---------------------------- */
$e = static fn(string $s): string => htmlspecialchars($s, ENT_QUOTES, 'UTF-8');

$rows = [
    'Nombre'   => $name,
    'Empresa'  => $company,
    'Email'    => $email,
    'Tamaño'   => $size !== '' ? $size : '—',
    'Teléfono' => $phone !== '' ? $phone : '—',
];

$html = '<div style="font-family:-apple-system,Segoe UI,Roboto,Helvetica,Arial,sans-serif;color:#07303A;line-height:1.6">'
      . '<h2 style="margin:0 0 4px;font-size:18px">Nuevo contacto desde el sitio web</h2>'
      . '<p style="margin:0 0 20px;font-size:13px;color:#4F6A74">impulsoestrategicocx.com · ' . $e(date('d/m/Y H:i')) . '</p>'
      . '<table cellpadding="0" cellspacing="0" style="border-collapse:collapse;font-size:14px">';
foreach ($rows as $label => $value) {
    $html .= '<tr>'
           . '<td style="padding:6px 18px 6px 0;color:#4F6A74;white-space:nowrap">' . $e((string)$label) . '</td>'
           . '<td style="padding:6px 0;font-weight:600">' . $e((string)$value) . '</td>'
           . '</tr>';
}
$html .= '</table>'
       . '<p style="margin:24px 0 6px;font-size:12px;text-transform:uppercase;letter-spacing:.1em;color:#4F6A74">Su caso</p>'
       . '<div style="white-space:pre-wrap;background:#F5F9FB;border:1px solid #E0EBF0;border-radius:10px;padding:16px;font-size:14px">' . $e($message) . '</div>'
       . '</div>';

/* ---- Envío vía Resend ---------------------------------- */
function resend(array $cfg, array $payload): array {
    $body = json_encode($payload, JSON_UNESCAPED_UNICODE);

    if (function_exists('curl_init')) {
        $ch = curl_init('https://api.resend.com/emails');
        curl_setopt_array($ch, [
            CURLOPT_POST           => true,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT        => 15,
            CURLOPT_HTTPHEADER     => [
                'Authorization: Bearer ' . $cfg['resend_api_key'],
                'Content-Type: application/json',
            ],
            CURLOPT_POSTFIELDS     => $body,
        ]);
        $res  = curl_exec($ch);
        $code = (int)curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        return [$code, (string)$res];
    }

    $ctx = stream_context_create(['http' => [
        'method'        => 'POST',
        'header'        => "Authorization: Bearer {$cfg['resend_api_key']}\r\nContent-Type: application/json\r\n",
        'content'       => $body,
        'timeout'       => 15,
        'ignore_errors' => true,
    ]]);
    $res  = @file_get_contents('https://api.resend.com/emails', false, $ctx);
    $code = 0;
    foreach ($http_response_header ?? [] as $h) {
        if (preg_match('#HTTP/\S+\s+(\d{3})#', $h, $m)) { $code = (int)$m[1]; }
    }
    return [$code, (string)$res];
}

[$code, $res] = resend($cfg, [
    'from'     => $cfg['from'],
    'to'       => $cfg['to'],
    'reply_to' => $email,
    'subject'  => 'Nuevo contacto · ' . $company . ' · ' . $name,
    'html'     => $html,
]);

if ($code < 200 || $code >= 300) {
    error_log('contacto.php: Resend ' . $code . ' ' . $res);
    out(502, ['ok' => false, 'error' => 'No pudimos enviar tu mensaje. Escríbenos a ariel.saez@impulsoestrategicocx.com.']);
}

/* ---- Respuesta automática al visitante ----------------- */
if (!empty($cfg['autoreply'])) {
    $first  = explode(' ', $name)[0];
    $accuse = '<div style="font-family:-apple-system,Segoe UI,Roboto,Helvetica,Arial,sans-serif;color:#07303A;line-height:1.65;font-size:15px">'
            . '<p>Hola ' . $e($first) . ',</p>'
            . '<p>Recibimos tu mensaje. Lo lee una persona del equipo, no un bot, y te respondemos en menos de 24 horas hábiles.</p>'
            . '<p>Mientras tanto, si quieres ver el tipo de proyectos que construimos: '
            . '<a href="https://impulsoestrategicocx.com/casos.html" style="color:#0084A8">impulsoestrategicocx.com/casos</a>.</p>'
            . '<p style="margin-top:28px">Ariel Sáez y Federico Orduz<br />'
            . '<span style="color:#4F6A74;font-size:13px">Impulso Estratégico CX &amp; AI</span></p>'
            . '</div>';

    resend($cfg, [
        'from'    => $cfg['autoreply_from'] ?? $cfg['from'],
        'to'      => [$email],
        'subject' => 'Recibimos tu mensaje · Impulso Estratégico CX & AI',
        'html'    => $accuse,
    ]);
}

out(200, ['ok' => true]);
