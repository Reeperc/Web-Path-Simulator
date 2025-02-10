# Web Path Simulator (WPS)

## Description

Le **Web Path Simulator (WPS)** est un projet développé dans le cadre du **PING 53** (projet ingénieur - année 2024-2025) réalisé par un groupe de six étudiants dont moi-même. Il permet de simuler l’envoi de paquets à travers plusieurs routes réseau dans le monde et d'analyser leurs performances en temps réel.

L’objectif est d’observer la latence et la bande passante sur différentes routes composées de serveurs distants hébergés sur **Azure**. Les données sont ensuite comparées pour identifier le chemin optimal pour la transmission des paquets.

## Interface de l'application web :

### 🔹 Visualisation des chemins configurés

![Visualisation des chemins](img/interface_web_routes.jpg)

### 🔹 Monitoring des chemins

![Monitoring des chemins](img/interface_web_monitoring.jpg)

## Fonctionnalités

- 📡 **Envoi de paquets ICMP** via différentes routes.
- 🔍 **Monitoring en temps réel** des métriques réseau (latence, bande passante).
- 🔗 **Utilisation d’un VPN (OpenVPN)** pour relier plusieurs serveurs distants.
- 🌍 **Serveurs répartis dans différentes régions** (USA, Italie, Corée, Angleterre, Pologne, West Europe).
- 🌐 **Interface Web** pour visualiser et tester les différentes routes.

## Configuration mise en place

- **Serveur Web local** (VM sur VirtualBox) pour l’envoi des paquets.
- **Routage** entre plusieurs serveurs distants via OpenVPN.
- **Analyse des performances réseau** avec `ping`, `iperf` et `tcpdump`.

## Prérequis


- 🔹 Des connaissances en **routage réseau** (`iptables`, `ip route`).
- 🔹 Une compréhension des **VPN et tunnels** (`OpenVPN`).
- 🔹 Outils de mesure réseau (`ping`, `traceroute`, `iperf`).
- 🔹 Développement web.

## Installation

1. **Cloner le projet** :

   ```sh
   git clone https://github.com/Reeperc/Web-Path-Simulator/web-site

   ```

## 🚀 Contributeurs

<table>
  <tr>
    <td align="center"><a href="https://github.com/Reeperc"><img src="https://github.com/Reeperc.png" width="100px;" alt=""/><br /><sub><b>@Reeperc</b></sub></a></td>
    <td align="center"><a href="https://github.com/Rajwa"><img src="https://github.com/Rajwa.png" width="100px;" alt=""/><br /><sub><b>@Rajwa</b></sub></a></td>
    <td align="center"><a href="https://github.com/wang-zhuofan"><img src="https://github.com/wang-zhuofan.png" width="100px;" alt=""/><br /><sub><b>@wang-zhuofan</b></sub></a></td>
    <td align="center"><a href="https://github.com/L01c"><img src="https://github.com/L01c.png" width="100px;" alt=""/><br /><sub><b>@L01c</b></sub></a></td>
    <td align="center"><a href="https://github.com/baptisteproper"><img src="https://github.com/baptisteproper.png" width="100px;" alt=""/><br /><sub><b>@baptisteprope</b></sub></a></td>
    <td align="center"><a href="https://github.com/SiwarR"><img src="https://github.com/SiwarR.png" width="100px;" alt=""/><br /><sub><b>@SiwarR</b></sub></a></td>
  </tr>
</table>
