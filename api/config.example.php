<?php
/* =========================================================
   Impulso Estratégico CX & AI — configuración del formulario

   1. Copia este archivo y renómbralo a  config.php
      (en la misma carpeta /api). NO borres este ejemplo.
   2. Rellena los valores de abajo.
   3. config.php nunca debe subirse a un repositorio público:
      contiene tu API key.
   ========================================================= */

return [

  // API key de Resend. Se crea en https://resend.com/api-keys
  // Permiso recomendado: "Sending access". Empieza por re_
  'resend_api_key' => 're_XXXXXXXXXXXXXXXXXXXXXXXX',

  // Remitente. El dominio DEBE estar verificado en Resend.
  // Usa un buzón del propio dominio, nunca un Gmail.
  'from' => 'Sitio web Impulso Estratégico <web@impulsoestrategicocx.com>',

  // Quién recibe los formularios. Puedes poner uno o varios.
  'to' => [
    'ariel.saez@impulsoestrategicocx.com',
    'federico.orduz@impulsoestrategicocx.com',
  ],

  // Respuesta automática al visitante que llena el formulario.
  'autoreply'      => true,
  'autoreply_from' => 'Impulso Estratégico CX & AI <hola@impulsoestrategicocx.com>',

  // Dominios permitidos para enviar el formulario (anti-abuso).
  'allowed_origins' => [
    'https://impulsoestrategicocx.com',
    'https://www.impulsoestrategicocx.com',
  ],
];
