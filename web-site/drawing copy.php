<?php
// animation_subtile.php
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <title>Chemin entre serveurs - Animation Subtile</title>
  <style>
    /* Style général */
    body {
      margin: 0;
      padding: 0;
      background: #2c3e50;
      display: flex;
      justify-content: center;
      align-items: center;
      height: 100vh;
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }

    svg {
      overflow: visible;
    }

    /* Styles des serveurs */
    .server {
      fill: url(#gradient);
      stroke: #3498db;
      stroke-width: 2;
      filter: drop-shadow(3px 3px 5px rgba(0,0,0,0.5));
    }
    .server-text {
      fill: #ecf0f1;
      font-size: 16px;
      font-weight: bold;
      pointer-events: none;
    }

    /* Animation de pulsation subtile sur les serveurs */
    .pulse {
      /* Assure que l'animation s'effectue autour du centre de l'élément */
      transform-origin: center;
      transform-box: fill-box;
      animation: pulse 2s infinite;
    }
    @keyframes pulse {
      0%   { transform: scale(1);   opacity: 0.95; }
      50%  { transform: scale(1.02); opacity: 1; }
      100% { transform: scale(1);   opacity: 0.95; }
    }

    /* Styles et animation des lignes de connexion */
    .line {
      fill: none;
      stroke: #e74c3c;
      stroke-width: 4;
      stroke-dasharray: 10;
      stroke-dashoffset: 0;
      animation: dash 2s linear infinite;
    }
    @keyframes dash {
      to { stroke-dashoffset: -20; }
    }
  </style>
</head>
<body>
  <svg width="900" height="300">
    <defs>
      <!-- Dégradé radial pour l'effet lumineux sur les serveurs -->
      <radialGradient id="gradient" cx="50%" cy="50%" r="50%">
        <stop offset="0%" stop-color="#1abc9c"/>
        <stop offset="100%" stop-color="#16a085"/>
      </radialGradient>
    </defs>

    <!-- Serveur 1 -->
    <g class="pulse">
      <rect class="server" x="50" y="100" width="120" height="60" rx="10" ry="10"/>
      <text x="110" y="135" text-anchor="middle" class="server-text">Serveur 1</text>
    </g>

    <!-- Serveur 2 -->
    <g class="pulse">
      <rect class="server" x="230" y="50" width="120" height="60" rx="10" ry="10"/>
      <text x="290" y="85" text-anchor="middle" class="server-text">Serveur 2</text>
    </g>

    <!-- Serveur 3 -->
    <g class="pulse">
      <rect class="server" x="410" y="100" width="120" height="60" rx="10" ry="10"/>
      <text x="470" y="135" text-anchor="middle" class="server-text">Serveur 3</text>
    </g>

    <!-- Serveur 4 -->
    <g class="pulse">
      <rect class="server" x="590" y="50" width="120" height="60" rx="10" ry="10"/>
      <text x="650" y="85" text-anchor="middle" class="server-text">Serveur 4</text>
    </g>

    <!-- Connexions entre les serveurs (sans flèches) -->
    <!-- La courbe est dessinée pour relier les bords respectifs (approximativement) -->
    <path class="line" d="M170,130 C200,80 250,80 230,80"/>
    <path class="line" d="M350,80 C380,130 410,130 410,130"/>
    <path class="line" d="M530,130 C560,80 600,80 590,80"/>
  </svg>
</body>
</html>
