# 💼 Configuration du Routage et des Règles iptables

Ce guide décrit la configuration du routage et des règles `iptables` pour permettre la transmission des paquets entre plusieurs interfaces réseau et assurer un bon fonctionnement du VPN.

---

## 🛠️ Prérequis

- Un **serveur Linux** avec `iptables` installé.
- OpenVPN ou tout autre VPN configuré sur les interfaces `tunX`.
- Accès root (`sudo`) pour modifier la configuration réseau.

---

## 1️⃣ **Activation du Forwarding des Paquets**

Avant de configurer `iptables`, il faut activer le forwarding IP pour que les paquets puissent être relayés entre interfaces :

```sh
echo "net.ipv4.ip_forward=1" | sudo tee -a /etc/sysctl.conf
sudo sysctl -p
```

Vérifier que la modification est bien prise en compte :

```sh
cat /proc/sys/net/ipv4/ip_forward
```

La valeur `1` doit être affichée.

---

## 2️⃣ **Configuration de iptables pour le Routage**

### 🛡️ Autoriser le transfert de paquets entre les interfaces VPN

Sur le serveur ayant plusieurs interfaces VPN (`tunX`), ajouter ces règles :

```sh
sudo iptables -A FORWARD -i tun0 -o tun1 -j ACCEPT
sudo iptables -A FORWARD -i tun1 -o tun0 -j ACCEPT
```

### 🛡️ Activation du NAT pour la sortie des paquets

Utilise la chaîne `POSTROUTING` pour masquer l'adresse source des paquets sortants :

```sh
sudo iptables -t nat -A POSTROUTING -o eth0 -j MASQUERADE
```

Cette règle permet aux paquets de sortir avec l'adresse IP de l'interface `eth0`.

### 🛡️ Redirection des paquets vers une adresse cible

Par exemple, pour rediriger tous les paquets ICMP (ping) venant d'une machine en Corée vers une adresse cible en Angleterre via un serveur en Italie :

```sh
sudo iptables -t nat -A PREROUTING -s <ip_source_Corée> -d <ip_destination_Italie> -p icmp -j DNAT --to-destination <ip_serveur_UK>
```

### 🛡️ Rendre les règles `iptables` persistantes

Les règles `iptables` disparaissent après un redémarrage. Pour les sauvegarder de manière permanente :

```sh
sudo apt install iptables-persistent
sudo netfilter-persistent save
sudo netfilter-persistent reload
```

---

## 3️⃣ **Vérification et Dépannage**

### 🔎 Voir les règles `iptables` actuelles

```sh
sudo iptables -L -v -n
sudo iptables -t nat -L -v -n
```

### 🔄 Supprimer une règle précise

```sh
sudo iptables -D FORWARD -i tun0 -o tun1 -j ACCEPT
```

### 🚫 Réinitialiser toutes les règles `iptables`

```sh
sudo iptables -F
sudo iptables -t nat -F
```

---

## ✅ **Conclusion**

Tu as maintenant une configuration `iptables` fonctionnelle pour router les paquets entre tes serveurs VPN et assurer un bon transfert des données.

📈 **Prochaines étapes :**

- Ajouter des règles pour la journalisation des paquets (`LOG`).
- Tester différents scénarios de routage avec `traceroute` et `tcpdump`.
- Implémenter un système de gestion dynamique des routes.

🚀 **Bon routage !** 🛡
