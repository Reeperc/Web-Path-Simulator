# 📡 Configuration du Routage et des Règles iptables

Ce guide décrit en détail `iptables`, son utilisation, ses options principales et la configuration nécessaire pour assurer le routage des paquets, en particulier pour un serveur VPN avec une seule interface `tun0`.

---

## 🛠 Introduction à `iptables`

`iptables` est un outil permettant de configurer le pare-feu et le routage des paquets sous Linux. Il utilise des **chaînes** et des **tables** pour filtrer, rediriger ou modifier le trafic réseau.

### 🔹 Tables principales dans `iptables`

- `filter` : Gère le filtrage des paquets (INPUT, OUTPUT, FORWARD).
- `nat` : Modifie les adresses sources ou destinations des paquets.
- `mangle` : Utilisée pour la modification des en-têtes des paquets.
- `raw` : Permet de configurer des exceptions de suivi de connexion.

### 🔹 Chaînes principales

- `INPUT` : Gère les paquets entrant dans le serveur.
- `OUTPUT` : Gère les paquets sortants.
- `FORWARD` : Gère les paquets transitant par le serveur.
- `PREROUTING` : Modifie les paquets avant qu’ils ne soient routés.
- `POSTROUTING` : Modifie les paquets après qu’ils aient été routés.

---

## 1️⃣ Activation du Forwarding des Paquets

Avant de configurer `iptables`, il faut activer le forwarding IP pour que les paquets puissent être relayés entre interfaces :

```sh
echo "net.ipv4.ip_forward=1" | sudo tee -a /etc/sysctl.conf
sudo sysctl -p
```

Vérifie que la modification est bien prise en compte :

```sh
cat /proc/sys/net/ipv4/ip_forward
```

La valeur `1` doit être affichée.

---

## 2️⃣ Configuration de iptables pour le Routage

### 🛡️ Autoriser le transfert de paquets entre les interfaces VPN

Sur un serveur ayant plusieurs interfaces VPN (`tunX`), ajouter ces règles :

```sh
sudo iptables -A FORWARD -i tun0 -o tun1 -j ACCEPT
sudo iptables -A FORWARD -i tun1 -o tun0 -j ACCEPT
```

### 🔹 Cas spécifique : Serveur VPN avec une seule interface `tun0`

Si le serveur VPN n’a qu’une seule interface `tun0` et que les machines clientes sont connectées uniquement à lui, il faut activer le routage des paquets entre elles :

```sh
sudo iptables -A FORWARD -i tun0 -o tun0 -j ACCEPT
sudo iptables -t nat -A POSTROUTING -o tun0 -j MASQUERADE
```

Ces règles permettent aux machines clientes d'échanger du trafic via le serveur VPN sans fuite d’adresses IP internes.

### 🔹 Redirection des paquets vers une adresse cible

Si un paquet ICMP (ping) doit être redirigé depuis une machine en Corée vers une machine en Angleterre via un serveur en Italie :

```sh
sudo iptables -t nat -A PREROUTING -s <ip_source_Corée> -d <ip_destination_Italie> -p icmp -j DNAT --to-destination <ip_serveur_UK>
```

### 🔹 Configuration du NAT et du routage avancé

Si l’on veut permettre aux clients d’accéder à Internet via le serveur VPN :

```sh
sudo iptables -t nat -A POSTROUTING -o eth0 -j MASQUERADE
```

Cela permet aux paquets sortants de prendre l’adresse IP publique du serveur VPN.

---

## 3️⃣ Rendre les Règles iptables Persistantes

Les règles `iptables` disparaissent après un redémarrage. Pour les sauvegarder de manière permanente :

```sh
sudo apt install iptables-persistent
sudo netfilter-persistent save
sudo netfilter-persistent reload
```

---

## 4️⃣ Vérification et Dépannage

### 🔎 Voir les règles `iptables` actuelles

```sh
sudo iptables -L -v -n
sudo iptables -t nat -L -v -n
```

### 🔧 Supprimer une règle précise

```sh
sudo iptables -D FORWARD -i tun0 -o tun0 -j ACCEPT
```

### ❌ Réinitialiser toutes les règles `iptables`

```sh
sudo iptables -F
sudo iptables -t nat -F
```

---
