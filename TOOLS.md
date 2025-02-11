# 📡 Utilisation des Outils Réseau

Ce guide fournit un aperçu des outils réseau essentiels (`ping`, `iperf`, `tcpdump`,`traceroute`) pour surveiller et analyser la connectivité et la performance d'un réseau.

---

## 🛠 Outils de Test et Surveillance Réseau

### 🔹 `ping` : Vérification de la Latence

Le `ping` est utilisé pour tester la connectivité entre deux machines en mesurant le temps de réponse (latence).

```sh
ping -c 5 <adresse-IP>
```

- `-c 5` : Envoie 5 paquets avant d’arrêter.
- `-i 0.2` : Définit un intervalle de 0.2s entre chaque ping.
- `-s 64` : Spécifie une taille de paquet de 64 octets.

💡 **Exemple d'utilisation :**

```sh
ping -c 5 8.8.8.8
```

---

### 🔹 `iperf` : Mesure de la Bande Passante

`iperf` permet d’analyser la bande passante entre deux hôtes en envoyant des flux de données.

#### 🔸 Lancer un serveur `iperf`

Sur la machine cible (ex: sur la machine Angleterre):

```sh
iperf -s
```

📌 **Note :** Cette commande doit être exécutée manuellement sur la machine cible pour permettre la mesure de la bande passante. Elle peut être automatisée via un script PHP, mais cela n’a pas été implémenté dans notre version du projet.

#### 🔸 Tester la connexion depuis un client

Depuis un autre hôte, exécuter :

```sh
iperf -c <adresse-IP-serveur>
```

💡 **Exemple d'utilisation :**

Dans notre cas on effectuait via un script PHP depuis le Serveur WEB :

```sh
iperf -c 10.8.3.3
iperf -c 10.9.3.3
```

🔹 **Options utiles :**

- `-u` : Mode UDP (par défaut, `iperf` utilise TCP).
- `-b 10M` : Fixe une limite de bande passante (ex: 10 Mbps).
- `-t 30` : Durée du test en secondes.

---

### 🔹 `tcpdump` : Capture et Analyse du Trafic Réseau

`tcpdump` est un outil puissant pour capturer et analyser le trafic réseau en temps réel.

#### 🔸 Capturer tout le trafic sur une interface spécifique

```sh
sudo tcpdump -i eth0
```

#### 🔸 Capturer uniquement les paquets ICMP (ping)

```sh
sudo tcpdump -i eth0 icmp
```

#### 🔸 Enregistrer les paquets pour une analyse ultérieure

```sh
sudo tcpdump -i eth0 -w capture.pcap
```

💡 **Exemple d'utilisation :**

```sh
sudo tcpdump -i tun0 port 443
```

---

### 🔹 `traceroute` : Suivi du Chemin des Paquets

`traceroute` permet d’identifier le chemin exact suivi par les paquets pour atteindre une destination.

```sh
traceroute <adresse-IP>
```

- `-n` : Affiche uniquement les adresses IP (évite la résolution DNS pour plus de rapidité).
- `-I` : Utilise ICMP au lieu d’UDP.
- `-T` : Utilise TCP au lieu d’UDP.

💡 **Exemple d'utilisation :**

Pour vérifier le chemin emprunté vers un serveur cible spécifique, par exemple `10.8.3.3`, exécuter :

```sh
traceroute 10.8.3.3
```

---
