# 💼 Configuration d’un Serveur OpenVPN

Ce guide explique comment installer et configurer un **serveur OpenVPN** (et un client VPN) sur une machine distante Azure.

---

## 🛠️ Prérequis

- Un **serveur Ubuntu** (créé sur azure).
- Un accès **SSH** avec un utilisateur ayant des droits `sudo`.
- Une **adresse IP publique** pour le serveur.

---

## 1️⃣ **Installation d’OpenVPN (pour un Serveur VPN)**

Tout d'abord, se connecter en SSH au serveur distant :

```sh
ssh user@ip-du-serveur
```

Télécharger et lancer le script d'installation d’OpenVPN :

```sh
wget https://git.io/vpn -O openvpn-install.sh
chmod +x openvpn-install.sh
sudo ./openvpn-install.sh
```

Pendant l’installation :

- **Adresse du serveur** : laisse par défaut (appuyer sur `Entrée`).
- **Protocole** : utiliser **TCP** (option `2`).
- **Port** : utiliser **443** (si un autre port est choisi, s'assurer de l'ouvrir sur Azure).
- **DNS** : choisis `Current system resolvers` (option `1`).

Une fois l’installation terminée, le service OpenVPN sera automatiquement démarré (même après un redémarrage, il se réactive automatiquement).

---

## 2️⃣ **Configuration du Serveur OpenVPN**

### 📌 Vérifier que le service est actif

```sh
sudo systemctl status openvpn-server@server
```

Si le service n'est pas actif, le démarrer :

```sh
sudo systemctl start openvpn-server@server
```

### 🏠 Choisir un sous-réseau spécifique pour OpenVPN

Par défaut, OpenVPN attribue un réseau `10.8.0.0/24`. Si on manipule plusieurs serveurs VPN, il est préférable de modifier cette valeur pour éviter des conflits :

```sh
sudo nano /etc/openvpn/server/server.conf
```

Modifier la ligne suivante pour attribuer une autre plage d’adresses :

```ini
server 10.8.1.0 255.255.255.0
```

Sauvegarder (`Ctrl + S`, puis `Ctrl + X`), puis redémarrer OpenVPN :

```sh
sudo systemctl restart openvpn-server@server
```

---

## 3️⃣ **Création d’un Client VPN**

Pour connecter un client au serveur OpenVPN, il faut générer un fichier `.ovpn` :

```sh
sudo ./openvpn-install.sh
```

- Choisir **`Add a client`** (option `1`).
- Nommer le client (ex : `US-Italy` pour un client américain se connectant à un serveur en Italie).

Le fichier généré sera stocké dans `/root/US-Italy.ovpn`.

Le déplacer vers le dossier utilisateur :

```sh
sudo mv /root/US-Italy.ovpn /home/user/
```

Modifier le fichier pour éviter les coupures SSH :

```sh
sudo nano /home/user/US-Italy.ovpn
```

Ajouter cette ligne :

```ini
pull-filter ignore "redirect-gateway"
```

Puis envoyer le fichier vers la machine cliente via SCP (ou WinSCP ou FileZilla) :

```sh
scp /home/user/US-Italy.ovpn user@client-ip:/home/user/
```

---

## 4️⃣ **Configuration du Client OpenVPN**

Sur le client (une autre machine Ubuntu) :

1. Installer OpenVPN :

   ```sh
   sudo apt install openvpn
   ```

2. Vérifier que le fichier `.ovpn` a bien été reçu et qu'il se trouve dans `/home/AdminUS/`. Puis lancer la connexion :

   ```sh
   sudo openvpn --config US-Italy.ovpn --daemon
   ```

3. Vérifier que l’interface `tun0` est bien active :
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

Si le fichier n'existe pas, il faut ajouter la ligne `status /var/log/openvpn-status.log` dans `/etc/openvpn/server/server.conf`

### 🔌 Redémarrer OpenVPN

```sh
sudo systemctl restart openvpn-server@server
```

### 🚫 Arrêter toutes les connexions VPN

```sh
sudo pkill -f openvpn
```

Ou arrêter une connexion spécifique :

```sh
sudo pkill -f fichier_client.ovpn
```

---

## 🔗 **Bonus : Configuration du Routage avec iptables**

Pour activer le forwarding des paquets pour permettre aux clients VPN d’accéder à d’autres réseaux :

```sh
echo "net.ipv4.ip_forward=1" | sudo tee -a /etc/sysctl.conf
sudo sysctl -p
```

Ajouter ces règles `iptables` pour permettre le NAT :

```sh
sudo iptables -t nat -A POSTROUTING -o eth0 -j MASQUERADE
```

Rendre cette règle persistante :

```sh
sudo apt install iptables-persistent
sudo netfilter-persistent save
```

---

🚀 **Bon courage !** 🛡
