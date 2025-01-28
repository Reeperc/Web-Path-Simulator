import subprocess
from flask import Flask, request, jsonify

app = Flask(__name__)

ROUTES = {
    '1': 'sudo iptables -t nat -A OUTPUT -d 10.8.0.7 -p icmp -j DNAT --to-destination 10.8.0.4',
    '2': 'sudo iptables -t nat -A OUTPUT -d 10.8.0.7 -p icmp -j DNAT --to-destination 10.8.0.5',
    '3': 'sudo iptables -t nat -A OUTPUT -d 10.8.0.7 -p icmp -j DNAT --to-destination 10.8.0.6'
}

def execute_command(command):
    try:
        subprocess.run(command.split(), check=True)
        return True
    except subprocess.CalledProcessError as e:
        print(f"Erreur lors de l'exécution de la commande: {e}")
        return False

@app.route('/configure', methods=['GET'])
def configure():
    route_code = request.args.get('route')
    if route_code in ROUTES:
        command = ROUTES[route_code]
        if execute_command(command):
            return jsonify({'status': 'success', 'message': f"Route {route_code} configurée avec succès!"})
        return jsonify({'status': 'failure', 'message': "Échec de la configuration de la route."})
    return jsonify({'status': 'failure', 'message': 'Route invalide, utilisez 1, 2 ou 3.'})

if __name__ == '__main__':
    app.run(host='0.0.0.0', port=5000)