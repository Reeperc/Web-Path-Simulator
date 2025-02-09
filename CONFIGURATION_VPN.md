# 💼 Configuration d’un Serveur OpenVPN

Ce guide explique comment installer et configurer un **serveur OpenVPN** sur une machine distante. Il détaille également la configuration des clients pour établir une connexion sécurisée.

---

## 🛠️ Prérequis

- Un **serveur Ubuntu** (ou Debian).
- Un accès **SSH** avec un utilisateur ayant des droits `sudo`.
- Une **adresse IP publique** pour le serveur.
- `iptables` installé pour le routage.

---

## 1️⃣ **Installation d’OpenVPN sur le serveur**

Tout d'abord, connecte-toi en SSH à ton serveur distant :

```sh
ssh user@ip-du-serveur
```

Télécharge et lance le script d'installation d’OpenVPN :

```sh
wget https://git.io/vpn -O openvpn-install.sh
chmod +x openvpn-install.sh
sudo ./openvpn-install.sh
```

Pendant l’installation :

- **Adresse du serveur** : laisse par défaut (appuie sur `Entrée`).
- **Protocole** : utilise **TCP** (option `2`).
- **Port** : utilise **443** (si un autre port est utilisé, assure-toi de l'ouvrir sur Azure).
- **DNS** : choisis `Current system resolvers` (option `1`).

Une fois l’installation terminée, le service OpenVPN sera automatiquement démarré.

---

## 2️⃣ **Configuration du Serveur OpenVPN**

### 📌 Vérifier que le service est actif

```sh
sudo systemctl status openvpn-server@server
```

Si le service n'est pas actif, démarre-le :

```sh
sudo systemctl start openvpn-server@server
```

### 🏠 Choisir un sous-réseau spécifique pour OpenVPN

Par défaut, OpenVPN attribue un réseau `10.8.0.0/24`. Si tu manipules plusieurs serveurs VPN, il est préférable de modifier cette valeur pour éviter des conflits :

```sh
sudo nano /etc/openvpn/server/server.conf
```

Modifie la ligne suivante pour attribuer une autre plage d’adresses :

```ini
server 10.8.1.0 255.255.255.0
```

Sauvegarde (`Ctrl + X`, puis `Y` et `Entrée`), puis redémarre OpenVPN :

```sh
sudo systemctl restart openvpn-server@server
```

---

## 3️⃣ **Création d’un Client VPN**

Pour connecter un client à ton serveur OpenVPN, il faut générer un fichier `.ovpn` :

```sh
sudo ./openvpn-install.sh
```

- Choisis **`Add a client`** (option `1`).
- Nomme le client (ex : `US-Italy` pour un client américain se connectant à un serveur en Italie).

Le fichier généré sera stocké dans `/root/US-Italy.ovpn`.

Déplace-le vers le dossier utilisateur :

```sh
sudo mv /root/US-Italy.ovpn /home/user/
```

Modifie le fichier pour éviter les coupures SSH :

```sh
sudo nano /home/user/US-Italy.ovpn
```

Ajoute cette ligne :

```ini
pull-filter ignore "redirect-gateway"
```

Puis envoie le fichier vers la machine cliente via SCP :

```sh
scp /home/user/US-Italy.ovpn user@client-ip:/home/user/
```

---

## 4️⃣ **Configuration du Client OpenVPN**

Sur le client (une autre machine Ubuntu) :

1. Installe OpenVPN :

   ```sh
   sudo apt install openvpn
   ```

2. Place le fichier `.ovpn` dans `/etc/openvpn/` et lance la connexion :

   ```sh
   sudo openvpn --config US-Italy.ovpn --daemon
   ```

3. Vérifie que l’interface `tun0` est bien active :
   ```sh
   ip a | grep tun
   ```

---

## 5️⃣ **Vérification et Dépannage**

### 🔎 Vérifier les clients connectés

Sur le serveur OpenVPN :

```sh
sudo cat /var/log/openvpn-status.log
```

### 🔌 Redémarrer OpenVPN

```sh
sudo systemctl restart openvpn-server@server
```

### 🚫 Arrêter toutes les connexions VPN

```sh
sudo pkill -f openvpn
```

---

## 🔗 **Bonus : Configuration du Routage avec iptables**

Si tu veux activer le forwarding des paquets pour permettre aux clients VPN d’accéder à d’autres réseaux :

```sh
echo "net.ipv4.ip_forward=1" | sudo tee -a /etc/sysctl.conf
sudo sysctl -p
```

Ajoute ces règles `iptables` pour permettre le NAT :

```sh
sudo iptables -t nat -A POSTROUTING -o eth0 -j MASQUERADE
```

Rends cette règle persistante :

```sh
sudo apt install iptables-persistent
sudo netfilter-persistent save
```

---

## ✅ **Conclusion**

Tu as maintenant un serveur OpenVPN fonctionnel avec plusieurs clients connectés. Ce VPN peut être utilisé pour sécuriser la transmission des paquets entre différentes machines distantes.

📈 **Prochaines étapes :**

- Ajouter plus de clients VPN.
- Automatiser la configuration avec des scripts.
- Intégrer un monitoring réseau (`tcpdump`, `iperf`) pour analyser les performances.

🚀 **Bon courage !** 🛡
