# 📡 Configuration du Routage et des Règles iptables

Ce guide détaille la configuration du routage et des règles `iptables` appliquées dans le projet pour assurer la connectivité entre les différents serveurs VPN et le bon acheminement des paquets.

---

## 🛠 Activation du Forwarding des Paquets

Avant de configurer `iptables` et les routes, il faut activer le forwarding IP pour permettre le passage des paquets entre interfaces :

```sh
echo "net.ipv4.ip_forward=1" | sudo tee -a /etc/sysctl.conf
sudo sysctl -p
```

Vérification :

```sh
cat /proc/sys/net/ipv4/ip_forward
```

La valeur `1` doit être affichée.

---

## 🌍 Configuration du Routage Dynamique

Dans le projet, la machine **USA** dispose de plusieurs chemins pour atteindre la machine **Angleterre** :

- Via **10.8.3.0/24** (passant par la Corée)
- Via **10.9.3.0/24** (passant par la Pologne et West Europe)

### 📌 Ajout des routes sur **USA** :

```sh
sudo ip route add 10.8.3.0/24 via 10.8.2.2 dev tun1  # Passer par la Corée
sudo ip route add 10.9.3.0/24 via 10.9.2.2 dev tun2  # Passer par la Pologne
```

Ces routes permettent à **USA** de relayer le trafic en fonction des interfaces disponibles.

---

## 🔥 Configuration des Règles `iptables`

### 🔹 Serveur **USA**

Autoriser le transfert des paquets entre les interfaces VPN :

```sh
sudo iptables -A FORWARD -i tun0 -o tun1 -j ACCEPT
sudo iptables -A FORWARD -i tun1 -o tun0 -j ACCEPT
sudo iptables -A FORWARD -i tun0 -o tun2 -j ACCEPT
sudo iptables -A FORWARD -i tun2 -o tun0 -j ACCEPT
```

Activer le NAT pour masquer l'adresse IP source :

```sh
sudo iptables -t nat -A POSTROUTING -o tun1 -j MASQUERADE
sudo iptables -t nat -A POSTROUTING -o tun2 -j MASQUERADE
```

---

### 🔹 Serveur **Corée du Sud**

```sh
sudo iptables -A FORWARD -i tun0 -o tun1 -j ACCEPT
sudo iptables -A FORWARD -i tun1 -o tun0 -j ACCEPT
sudo iptables -t nat -A POSTROUTING -o tun1 -j MASQUERADE
```

---

### 🔹 Serveur **Italie**

Redirection des paquets ICMP venant de la Corée et visant l'Italie, puis renvoyés vers la cible UK :

```sh
sudo iptables -t nat -A PREROUTING -s 10.8.3.2 -d 10.8.2.1 -p icmp -j DNAT --to-destination <IP_UK>
```

Activer le NAT :

```sh
sudo iptables -t nat -A POSTROUTING -o tun0 -j MASQUERADE
```

---

## 🔄 Sauvegarde et Persistance des Règles

Pour rendre les règles `iptables` persistantes après redémarrage :

```sh
sudo apt install iptables-persistent
sudo netfilter-persistent save
sudo netfilter-persistent reload
```

Vérification des règles appliquées :

```sh
sudo iptables -L -v -n
sudo iptables -t nat -L -v -n
```

---

## 🔧 Dépannage

### ❌ Supprimer une règle précise

```sh
sudo iptables -D FORWARD -i tun0 -o tun1 -j ACCEPT
```

### 🔄 Réinitialiser toutes les règles `iptables`

```sh
sudo iptables -F
sudo iptables -t nat -F
```
