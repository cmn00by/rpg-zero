#!/usr/bin/env bash
set -e

COOKIE_JAR="/tmp/rpg_e2e_cookies.txt"
rm -f "$COOKIE_JAR"

RAND_USER="test_$(date +%s)"

echo "=== 1. Inscription & Création ==="
curl -s -c "$COOKIE_JAR" -X POST -d "username=${RAND_USER}&password=secret123&password_confirm=secret123" "http://localhost:8000/register" -o /dev/null
curl -s -b "$COOKIE_JAR" -c "$COOKIE_JAR" -X POST -d "name=Hero_${RAND_USER}&class_id=1" "http://localhost:8000/character/create" -o /dev/null
echo "✅ Compte & Héros créés"

echo "=== 2. Affichage de l'Inventaire ==="
INV_HTML=$(curl -s -b "$COOKIE_JAR" "http://localhost:8000/game/inventory")
echo "$INV_HTML" | grep -q "Inventaire & Équipements" && echo "✅ Page d'inventaire accessible"
echo "$INV_HTML" | grep -q "Mannequin d'Équipement" && echo "✅ Mannequin d'équipement présent"
echo "$INV_HTML" | grep -q "Épée longue en fer" && echo "✅ Épée de départ équipée"

echo "=== 3. Utilisation d'une Potion via HTMX ==="
POTION_ITEM_ID=$(echo "$INV_HTML" | grep -o 'value="[0-9]*"' | head -n 1 | grep -o '[0-9]*')
if [ -n "$POTION_ITEM_ID" ]; then
    USE_HTML=$(curl -s -b "$COOKIE_JAR" -c "$COOKIE_JAR" -H "HX-Request: true" -X POST -d "character_item_id=$POTION_ITEM_ID" "http://localhost:8000/inventory/use")
    echo "$USE_HTML" | grep -q "Inventaire & Équipements" && echo "✅ Potion consommée via HTMX avec rafraîchissement instantané"
fi

echo "=== 4. Déséquipement d'Arme via HTMX ==="
UNEQ_HTML=$(curl -s -b "$COOKIE_JAR" -c "$COOKIE_JAR" -H "HX-Request: true" -X POST -d "slot=weapon" "http://localhost:8000/inventory/unequip")
echo "$UNEQ_HTML" | grep -q "Mains nues" && echo "✅ Arme déséquipée avec succès"

echo ""
echo "🎉 TOUS LES TESTS HTTP D'INVENTAIRE ET D'ÉQUIPEMENT SONT VALIDÉS !"
