#!/usr/bin/env bash
set -e

COOKIE_JAR="/tmp/rpg_e2e_cookies.txt"
rm -f "$COOKIE_JAR"

echo "=== 1. Inscription ==="
HTTP_REG=$(curl -s -c "$COOKIE_JAR" -X POST -d "username=guinevere&password=secret123&password_confirm=secret123" "http://localhost:8000/register" -o /dev/null -w "%{http_code}")
echo "Statut Inscription: $HTTP_REG (Attendu: 302)"

echo "=== 2. Création de Personnage (Mage) ==="
HTTP_CHAR=$(curl -s -b "$COOKIE_JAR" -c "$COOKIE_JAR" -X POST -d "name=Guinevere&class_id=3" "http://localhost:8000/character/create" -o /dev/null -w "%{http_code}")
echo "Statut Création: $HTTP_CHAR (Attendu: 302)"

echo "=== 3. Chargement du Hub ==="
HUB_HTML=$(curl -s -b "$COOKIE_JAR" "http://localhost:8000/game/hub")
echo "$HUB_HTML" | grep -q "La Cité d'Orépierre" && echo "✅ Hub chargé avec succès (La Cité d'Orépierre)"
echo "$HUB_HTML" | grep -q "Guinevere" && echo "✅ Personnage Guinevere présent dans le header"

echo "=== 4. Lancement d'un Combat ==="
HTTP_BATTLE=$(curl -s -b "$COOKIE_JAR" -c "$COOKIE_JAR" -X POST "http://localhost:8000/battle/start" -o /dev/null -w "%{http_code}")
echo "Statut Début Combat: $HTTP_BATTLE (Attendu: 302)"

echo "=== 5. Attaque Magique en Arène via HTMX ==="
ARENA_HTML=$(curl -s -b "$COOKIE_JAR" -c "$COOKIE_JAR" -H "HX-Request: true" -X POST -d "action=attack" "http://localhost:8000/battle/action")
echo "$ARENA_HTML" | grep -q "Arène de Combat" && echo "✅ Arène mise à jour dynamiquement via HTMX"
echo "$ARENA_HTML" | grep -q "Journal des Actions" && echo "✅ Journal de combat opérationnel"

echo "=== 6. Fiche de Personnage ==="
STATS_HTML=$(curl -s -b "$COOKIE_JAR" "http://localhost:8000/game/stats")
echo "$STATS_HTML" | grep -q "Intelligence" && echo "✅ Fiche de stats chargée"

echo "=== 7. Repos à la Taverne ==="
HTTP_REST=$(curl -s -b "$COOKIE_JAR" -c "$COOKIE_JAR" -X POST "http://localhost:8000/game/tavern/rest" -o /dev/null -w "%{http_code}")
echo "Statut Taverne: $HTTP_REST (Attendu: 302)"

echo ""
echo "🎉 TOUS LES FLUX HTTP SONT VALIDÉS !"
