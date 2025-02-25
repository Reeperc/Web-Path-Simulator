# Web Path Simulator (WPS)

## Description

Le **Web Path Simulator (WPS)** est un projet développé dans le cadre du **PING 53** (projet ingénieur - année 2024-2025) réalisé par un groupe de six étudiants dont moi-même. Il permet de simuler l’envoi de paquets à travers plusieurs routes réseau dans le monde et d'analyser leurs performances en temps réel.

L’objectif est d’observer la latence et la bande passante sur différentes routes composées de serveurs distants hébergés sur **Azure**. Les données sont ensuite comparées pour identifier le chemin optimal pour la transmission de paquets.

**Il est important de noter que cette version n'est pas aboutie et reste éloignée des attentes de la version finale du WPS.** Avec l'essor des procédures médicales robotisées, la nécessité d'un contrôle à distance des équipements devient cruciale, notamment pour lutter contre les **déserts médicaux**. Le **WebPathSimulator** (dans sa version finale) vise à tester et améliorer la transmission des données entre un praticien et un **robot UR** en simulant divers scénarios de communication longue distance (200 à 1000 km). Le projet a été initié par **Benjamin Castaneda**, spécialisé en ingénierie biomédicale.

## Interface de l'application web :

### 🔹 Visualisation des chemins configurés

![Visualisation des chemins](img/interface_web_routes.jpg)

### 🔹 Monitoring des chemins

![Monitoring des chemins](img/interface_web_monitoring.jpg)

## Fonctionnalités de cette version de l'application :

- **Envoi de paquets ICMP** via différentes routes.
- **Monitoring en temps réel** des métriques réseau (latence, bande passante).
- **Utilisation d’un VPN (OpenVPN)** pour relier plusieurs serveurs distants.
- **Serveurs répartis dans différentes régions du monde** (USA, Italie, Corée, Angleterre, Pologne, Portugal).
- **Interface Web** pour visualiser et tester les différentes routes.

## Configuration mise en place

- **Serveur Web local** (VM sur VirtualBox) pour l’envoi des paquets.
- **Routage** entre plusieurs serveurs distants via OpenVPN (cf [config routage](/CONFIGURATION_ROUTAGE.md) et [config VPN](/CONFIGURATION_VPN.md)).
- **Analyse des performances réseau** avec `ping`, `iperf` et `tcpdump` (cf [outils](/TOOLS.md)).

## Installation

**Travailler sur le site web du projet** :

🔹 Si vous souhaitez uniquement travailler sur l'interface web du projet, le dossier **web-site** de ce dépôt est l'équivalent du dossier **html** habituellement utilisé avec apache (`/var/www/html`).

```sh
git clone https://github.com/Reeperc/Web-Path-Simulator
cd Web-Path-Simulator/web-site
```

🔹 Copier le dossier web-site dans /var/www/html :

```sh
cd Web-Path-Simulator
sudo cp -r web-site /var/www/html
```

🔹 Supprimer le dossier html, copier web-site dans /var/www et le renommer en html :

⚠️ Attention : Cette commande supprimera complètement le dossier /var/www/html original.

```sh
cd Web-Path-Simulator
sudo rm -rf /var/www/html && sudo cp -r web-site /var/www/html
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
