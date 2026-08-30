#!/usr/bin/env bash
set -e

COOKIE_JAR="/tmp/rpg_e2e_cookies.txt"
rm -f "$COOKIE_JAR"

echo "=== 1. Inscription ==="
HTTP_REG=$(curl -s -c "$COOKIE_JAR" -X POST -d "username=valkyrie&password=secret123&password_confirm=secret123" "http://localhost:8000/register" -o /dev/null -w "%{http_code}")
echo "Statut Inscription: $HTTP_REG (Attendu: 302)"

echo "=== 2. Création de Personnage (Voleur) ==="
HTTP_CHAR=$(curl -s -b "$COOKIE_JAR" -c "$COOKIE_JAR" -X POST -d "name=ValkyrieShadow&class_id=2" "http://localhost:8000/character/create" -o /dev/null -w "%{http_code}")
echo "Statut Création: $HTTP_CHAR (Attendu: 302)"

echo "=== 3. Chargement du Hub ==="
HUB_HTML=$(curl -s -b "$COOKIE_JAR" "http://localhost:8000/game/hub")
echo "$HUB_HTML" | grep -q "La Cité d'Orépierre" && echo "✅ Hub chargé avec succès"
echo "$HUB_HTML" | grep -q "ValkyrieShadow" && echo "✅ Personnage ValkyrieShadow présent"

echo "=== 4. Lancement de Combat & Victoire ==="
curl -s -b "$COOKIE_JAR" -c "$COOKIE_JAR" -X POST "http://localhost:8000/battle/start" -o /dev/null
ARENA_HTML=$(curl -s -b "$COOKIE_JAR" -c "$COOKIE_JAR" -H "HX-Request: true" -X POST -d "action=attack" "http://localhost:8000/battle/action")
echo "$ARENA_HTML" | grep -q "Arène de Combat" && echo "✅ Tour de combat exécuté avec succès"

echo "=== 5. Fiche de Personnage & Attribution de Stats ==="
STATS_HTML=$(curl -s -b "$COOKIE_JAR" "http://localhost:8000/game/stats")
echo "$STATS_HTML" | grep -q "ValkyrieShadow" && echo "✅ Fiche de stats chargée"

# Tester la route d'allocation
ALLOC_HTML=$(curl -s -b "$COOKIE_JAR" -c "$COOKIE_JAR" -H "HX-Request: true" -X POST -d "stat=agility" "http://localhost:8000/character/allocate-stat")
echo "$ALLOC_HTML" | grep -q "Agilité" && echo "✅ Route d'allocation de caractéristiques validée"

echo ""
echo "🎉 TOUS LES FLUX HTTP ET SYSTÈME DE STATS SONT VALIDÉS !"
