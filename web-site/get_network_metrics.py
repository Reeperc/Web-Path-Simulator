# -*- coding: utf-8 -*-
 
import subprocess
 
import re
 
import json
 
# IP de destination pour tester le r�seau
 
DEST_IP = "10.9.3.3"
 
# Fonction pour ex�cuter une commande et r�cup�rer la sortie
 
def run_command(command):
 
    try:
 
        result = subprocess.run(command, capture_output=True, text=True, shell=True)
 
        return result.stdout
 
    except Exception as e:
 
        return None
 
# Fonction pour r�cup�rer la latence depuis traceroute
 
def get_traceroute_latency():
 
    output = run_command(f"traceroute -n {DEST_IP}")
 
    if output:
 
        latencies = [float(x) for x in re.findall(r"(\d+\.\d+) ms", output)]
 
        return round(sum(latencies) / len(latencies), 2) if latencies else None
 
    return None
 
# Fonction pour r�cup�rer les m�triques depuis iPerf
 
def get_iperf_metrics():
 
    output = run_command(f"iperf -c {DEST_IP}")
 
    if not output:  # V�rifier que la sortie n'est pas vide
 
        return None, None, None
 
    # Recherche uniquement du d�bit (Bandwidth)
 
    bandwidth_match = re.search(r"(\d+\.\d+|\d+)\s+Mbits/sec", output)
 
    # Extraction des valeurs
 
    bandwidth = float(bandwidth_match.group(1)) if bandwidth_match else None
 
    # Retourne le d�bit, et None pour jitter et packet_loss (non pr�sents dans la sortie)
 
    return bandwidth
# Fonction pour d�terminer la couleur en fonction de la latence uniquement
def get_status_color(latency):
    if latency is None:
        return "gray"  # Si aucune donn�e, mettre la ligne en gris
   
    if latency < 200:
        return "green"
    elif latency <= 350:
        return "orange"
    else:
        return "red"
     
# Ex�cution des commandes
 
latency = get_traceroute_latency()
 
bandwidth = get_iperf_metrics()
 
connection_status = "Online" if latency is not None else "Offline"
status_color = get_status_color(latency)
 
# Retourner les r�sultats sous forme de JSON
 
metrics = {
    "robot_id": "robot_1",
    "latency": latency,
    "bandwidth_usage": bandwidth,
    "connection_status": connection_status,
    "status_color": status_color
}
 
print(json.dumps(metrics))
 
 
 