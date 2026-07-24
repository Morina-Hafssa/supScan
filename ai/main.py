"""
SupScan - Pipeline principale
===============================

Enchaîne les étapes :

    Image de facture
        -> PaddleOCR (ocr.py)
        -> Texte OCR complet
        -> Gemini 2.5 Flash-Lite (ai_extractor.py)
        -> Extraction intelligente des champs
        -> Validation Python (validator.py)
        -> JSON final (output/invoice_data.json)

Usage :
    python src/main.py
"""

import os
import json
import logging

from dotenv import load_dotenv

from ocr import extraire_texte_ocr
from ai_extractor import extract_invoice_data_with_ai
from validator import valider_donnees_facture, ErreurValidation

# ---------------------------------------------------------------------------
# Configuration des logs
# ---------------------------------------------------------------------------
logging.basicConfig(
    level=logging.INFO,
    format="%(message)s",
)
logger = logging.getLogger("supscan.main")

# ---------------------------------------------------------------------------
# Chemins du projet
# ---------------------------------------------------------------------------
RACINE_PROJET = os.path.dirname(os.path.dirname(os.path.abspath(__file__)))
CHEMIN_IMAGE = os.path.join(RACINE_PROJET, "new_invoice", "invoice.jpg")
CHEMIN_SORTIE = os.path.join(RACINE_PROJET, "output", "invoice_data.json")


def sauvegarder_json(donnees: dict, chemin: str) -> None:
    """Sauvegarde le dict de données validées dans un fichier JSON."""
    os.makedirs(os.path.dirname(chemin), exist_ok=True)
    with open(chemin, "w", encoding="utf-8") as fichier:
        json.dump(donnees, fichier, ensure_ascii=False, indent=2)
    logger.info("[INFO] Fichier JSON créé")


def main() -> None:
    # Charge les variables d'environnement depuis .env
    load_dotenv()

    try:
        # Étape 1 & 2 : OCR
        resultat_ocr = extraire_texte_ocr(CHEMIN_IMAGE)
        texte_ocr = resultat_ocr["texte_complet"]

        if not texte_ocr.strip():
            logger.error("[ERREUR] Aucun texte détecté sur la facture, arrêt du traitement.")
            return

        # Étape 3 : extraction intelligente avec Gemini
        donnees_brutes = extract_invoice_data_with_ai(texte_ocr)

        # Étape 4 : validation stricte
        donnees_validees = valider_donnees_facture(donnees_brutes)

        # Étape 5 : sauvegarde du JSON final
        sauvegarder_json(donnees_validees, CHEMIN_SORTIE)

        logger.info(f"[RESULTAT] {json.dumps(donnees_validees, ensure_ascii=False, indent=2)}")

    except FileNotFoundError as exc:
        logger.error(str(exc))
    except ErreurValidation as exc:
        logger.error(str(exc))
    except (RuntimeError, ValueError) as exc:
        logger.error(str(exc))
    except Exception as exc:  # filet de sécurité pour toute erreur imprévue
        logger.error(f"[ERREUR] Erreur inattendue : {exc}")


if __name__ == "__main__":
    main()
