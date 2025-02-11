# 📡 Utilisation des Outils Réseau

Ce guide fournit un aperçu des outils réseau essentiels (`ping`, `iperf`, `tcpdump`) pour surveiller et analyser la connectivité et la performance d'un réseau.

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

Sur la machine cible :

```sh
iperf -s
```

#### 🔸 Tester la connexion depuis un client

Depuis un autre hôte, exécuter :

```sh
iperf -c <adresse-IP-serveur>
```

🔹 **Options utiles :**

- `-u` : Mode UDP (par défaut, `iperf` utilise TCP).
- `-b 10M` : Fixe une limite de bande passante (ex: 10 Mbps).
- `-t 30` : Durée du test en secondes.

💡 **Exemple d'utilisation :**

```sh
iperf -c 192.168.1.10 -t 10 -i 1
```

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

## ✅ Conclusion

Ces outils sont essentiels pour diagnostiquer et optimiser la performance d’un réseau.

🔹 **Prochaines étapes :**

- Automatiser les tests avec des scripts.
- Combiner `tcpdump` et `Wireshark` pour une analyse approfondie.
- Tester différentes tailles de paquets et débits avec `iperf`.

🚀 **Bon monitoring réseau !**
