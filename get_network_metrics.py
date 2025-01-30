# -*- coding: utf-8 -*-
import subprocess
import re
import json
 
# IP de destination pour tester le r�seau
DEST_IP = "10.8.0.7"
 
# Fonction pour ex�cuter une commande et r�cup�rer la sortie
def run_command(command):
    try:
        result = subprocess.run(command, capture_output=True, text=True, shell=True, timeout=5)
        return result.stdout.strip()
    except Exception as e:
        return None
 
# Fonction pour r�cup�rer la latence depuis traceroute
def get_traceroute_latency():
    output = run_command(f"traceroute -I -n {DEST_IP}")
    if output:
        latencies = [float(x) for x in re.findall(r"(\d+\.\d+) ms", output)]
        return round(sum(latencies) / len(latencies), 2) if latencies else None
    return None
 
# Fonction pour r�cup�rer les m�triques depuis iPerf (ancienne version)
def get_iperf_metrics():
    output = run_command(f"iperf -c {DEST_IP}")
    if not output:  # Vérifier que la sortie n'est pas vide
        return None
    
    print("iPerf Output:", output)  # DEBUG : Voir exactement la sortie de iperf

    # Rechercher la ligne contenant "Mbits/sec" et extraire la dernière valeur numérique avant "Mbits/sec"
    for line in output.split("\n"):
        if "Mbits/sec" in line:
            parts = line.split()
            try:
                bandwidth = float(parts[-2])  # Dernier nombre avant "Mbits/sec"
                return bandwidth
            except ValueError:
                pass

    return None

 
# Fonction pour d�terminer la couleur en fonction des m�triques
def get_status_color(latency, bandwidth):
    if latency is None or bandwidth is None:
        return "gray"  # Si aucune donn�e, mettre la ligne en gris
    latency_score = "green" if latency < 50 else "orange" if latency <= 150 else "red"
    bandwidth_score = "green" if bandwidth > 50 else "orange" if bandwidth >= 10 else "red"
 
    if latency_score == "green" and bandwidth_score == "green":
        return "green"
    elif "red" in [latency_score, bandwidth_score]:
        return "red"
    else:
        return "orange"
 
# Ex�cution des commandes
latency = get_traceroute_latency()
bandwidth = get_iperf_metrics()
 
# Si aucune m�trique n'est disponible, d�finir le statut comme hors ligne
if latency is None and bandwidth is None:
    connection_status = "Offline"
    color_status = "gray"
else:
    connection_status = "Online"
    color_status = get_status_color(latency, bandwidth)
 
# Retourner les r�sultats sous forme de JSON
metrics = {
    "robot_id": "robot_1",
    "latency": latency,
    "bandwidth_usage": bandwidth,
    "connection_status": connection_status,
    "status_color": color_status
}
 
print(json.dumps(metrics))