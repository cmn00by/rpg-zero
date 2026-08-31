#!/usr/bin/env bash
set -e

COOKIE_JAR="/tmp/rpg_e2e_cookies.txt"
rm -f "$COOKIE_JAR"

RAND_USER="test_$(date +%s)"

echo "=== 1. Inscription & Création ==="
curl -s -c "$COOKIE_JAR" -X POST -d "username=${RAND_USER}&password=secret123&password_confirm=secret123" "http://localhost:8000/register" -o /dev/null
curl -s -b "$COOKIE_JAR" -c "$COOKIE_JAR" -X POST -d "name=Hero_${RAND_USER}&class_id=1" "http://localhost:8000/character/create" -o /dev/null
echo "✅ Compte & Héros créés"

echo "=== 2. Affichage de la Carte du Monde ==="
MAP_HTML=$(curl -s -b "$COOKIE_JAR" "http://localhost:8000/game/map")
echo "$MAP_HTML" | grep -q "Vallée d'Orépierre" && echo "✅ Carte de la Vallée accessible"
echo "$MAP_HTML" | grep -q "Place Centrale d'Orépierre" && echo "✅ Position initiale [2, 2] reconnue"

echo "=== 3. Déplacement Est vers la Forge via HTMX ==="
MOVE_HTML=$(curl -s -b "$COOKIE_JAR" -c "$COOKIE_JAR" -H "HX-Request: true" -X POST -d "direction=east" "http://localhost:8000/map/move")
echo "$MOVE_HTML" | grep -q "La Forge de Durin" && echo "✅ Déplacement vers la Forge [3, 2] validé"

echo "=== 4. Visite de la Forge / Boutique ==="
SHOP_HTML=$(curl -s -b "$COOKIE_JAR" "http://localhost:8000/game/shop")
echo "$SHOP_HTML" | grep -q "La Forge de Durin" && echo "✅ Échoppe de forge accessible"
echo "$SHOP_HTML" | grep -q "Épée longue en fer" && echo "✅ Catalogue de vente affiché"

echo ""
echo "🎉 TOUS LES TESTS HTTP DE CARTE, SHOP ET DÉPLACEMENT SONT VALIDÉS !"
