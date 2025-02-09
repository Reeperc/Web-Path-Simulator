<?php
// routes.php
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <title>Routes entre serveurs</title>
  <style>
    /* Style général de la page */
    body {
      margin: 0;
      padding: 20px;
      background: #2c3e50;
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      color: #ecf0f1;
    }
    h2 {
      text-align: center;
      margin-top: 40px;
    }
    .route-container {
      margin-bottom: 60px;
      display: flex;
      justify-content: center;
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
    /* Animation de pulsation subtile pour les serveurs */
    .pulse {
      transform-origin: center;
      transform-box: fill-box;
      animation: pulse 2s infinite;
    }
    @keyframes pulse {
      0%   { transform: scale(1);   opacity: 0.95; }
      50%  { transform: scale(1.02); opacity: 1; }
      100% { transform: scale(1);   opacity: 0.95; }
    }
    /* Styles et animation des lignes */
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
  <!-- Route 1 : US - Italy - Korea - UK -->
  <h2>Route 1 : US - Italy - Korea - UK</h2>
  <div class="route-container">
    <svg width="900" height="300">
      <defs>
        <!-- Dégradé radial pour l'effet lumineux -->
        <radialGradient id="gradient" cx="50%" cy="50%" r="50%">
          <stop offset="0%" stop-color="#1abc9c"/>
          <stop offset="100%" stop-color="#16a085"/>
        </radialGradient>
      </defs>
      <!-- Serveur 1 : US -->
      <g class="pulse">
        <rect class="server" x="50" y="100" width="120" height="60" rx="10" ry="10"/>
        <text x="110" y="135" text-anchor="middle" class="server-text">US</text>
      </g>
      <!-- Serveur 2 : Italy -->
      <g class="pulse">
        <rect class="server" x="230" y="50" width="120" height="60" rx="10" ry="10"/>
        <text x="290" y="85" text-anchor="middle" class="server-text">Italy</text>
      </g>
      <!-- Serveur 3 : Korea -->
      <g class="pulse">
        <rect class="server" x="410" y="100" width="120" height="60" rx="10" ry="10"/>
        <text x="470" y="135" text-anchor="middle" class="server-text">Korea</text>
      </g>
      <!-- Serveur 4 : UK -->
      <g class="pulse">
        <rect class="server" x="590" y="50" width="120" height="60" rx="10" ry="10"/>
        <text x="650" y="85" text-anchor="middle" class="server-text">UK</text>
      </g>
      <!-- Liaisons entre serveurs -->
      <path class="line" d="M170,130 C200,80 250,80 230,80"/>
      <path class="line" d="M350,80 C380,130 410,130 410,130"/>
      <path class="line" d="M530,130 C560,80 600,80 590,80"/>
    </svg>
  </div>

  <!-- Route 2 : US - Poland - Portugal - UK -->
  <h2>Route 2 : US - Poland - Portugal - UK</h2>
  <div class="route-container">
    <svg width="900" height="300">
      <defs>
        <radialGradient id="gradient" cx="50%" cy="50%" r="50%">
          <stop offset="0%" stop-color="#1abc9c"/>
          <stop offset="100%" stop-color="#16a085"/>
        </radialGradient>
      </defs>
      <!-- Serveur 1 : US -->
      <g class="pulse">
        <rect class="server" x="50" y="100" width="120" height="60" rx="10" ry="10"/>
        <text x="110" y="135" text-anchor="middle" class="server-text">US</text>
      </g>
      <!-- Serveur 2 : Poland -->
      <g class="pulse">
        <rect class="server" x="230" y="50" width="120" height="60" rx="10" ry="10"/>
        <text x="290" y="85" text-anchor="middle" class="server-text">Poland</text>
      </g>
      <!-- Serveur 3 : Portugal -->
      <g class="pulse">
        <rect class="server" x="410" y="100" width="120" height="60" rx="10" ry="10"/>
        <text x="470" y="135" text-anchor="middle" class="server-text">Portugal</text>
      </g>
      <!-- Serveur 4 : UK -->
      <g class="pulse">
        <rect class="server" x="590" y="50" width="120" height="60" rx="10" ry="10"/>
        <text x="650" y="85" text-anchor="middle" class="server-text">UK</text>
      </g>
      <!-- Liaisons entre serveurs -->
      <path class="line" d="M170,130 C200,80 250,80 230,80"/>
      <path class="line" d="M350,80 C380,130 410,130 410,130"/>
      <path class="line" d="M530,130 C560,80 600,80 590,80"/>
    </svg>
  </div>
</body>
</html>
