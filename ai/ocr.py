"""
Module OCR - SupScan
=====================

Ce module est responsable UNIQUEMENT de la reconnaissance de texte (OCR)
sur l'image de facture, grâce à PaddleOCR.

Il ne fait AUCUNE interprétation du contenu : il se contente de lire le
texte présent sur l'image et de le renvoyer sous forme de texte brut.

Les coordonnées des blocs de texte sont récupérées uniquement à titre
de debug (affichage / logs) et ne sont JAMAIS utilisées pour déterminer
la valeur d'un champ de la facture.
"""

import os
import logging

logger = logging.getLogger("supscan.ocr")


def verifier_image(chemin_image: str) -> None:
    """
    Vérifie que le fichier image existe avant de lancer l'OCR.

    Args:
        chemin_image: chemin vers le fichier image de la facture.

    Raises:
        FileNotFoundError: si l'image n'existe pas.
    """
    if not os.path.isfile(chemin_image):
        raise FileNotFoundError(
            f"[ERREUR] L'image de facture est introuvable : {chemin_image}"
        )
    logger.info("[INFO] Image trouvée")


def extraire_texte_ocr(chemin_image: str) -> dict:
    """
    Exécute PaddleOCR sur l'image fournie et renvoie le texte détecté.

    Args:
        chemin_image: chemin vers l'image de la facture.

    Returns:
        dict contenant :
            - "texte_complet" (str) : tout le texte OCR concaténé,
              ligne par ligne, prêt à être envoyé à Gemini.
            - "details" (list) : liste de dicts {texte, confiance, coordonnees}
              utilisée uniquement pour le debug/logs.

    Raises:
        FileNotFoundError: si l'image n'existe pas.
        RuntimeError: si PaddleOCR échoue.
    """
    verifier_image(chemin_image)

    # Import différé : PaddleOCR est lourd à charger, on ne le fait
    # qu'au moment où l'on en a réellement besoin.
    try:
        from paddleocr import PaddleOCR
    except ImportError as exc:
        raise RuntimeError(
            "[ERREUR] PaddleOCR n'est pas installé. "
            "Exécutez : pip install paddleocr paddlepaddle"
        ) from exc

    logger.info("[INFO] OCR en cours...")

    try:
        # lang='fr' car les factures traitées sont majoritairement en français,
        # mais PaddleOCR gère aussi très bien les chiffres/anglais mélangés.
        ocr_engine = PaddleOCR(use_angle_cls=True, lang="fr", show_log=False)
        resultat = ocr_engine.ocr(chemin_image, cls=True)
    except Exception as exc:
        raise RuntimeError(f"[ERREUR] Échec de l'OCR PaddleOCR : {exc}") from exc

    if not resultat or resultat[0] is None:
        logger.warning("[AVERTISSEMENT] Aucun texte détecté sur l'image.")
        return {"texte_complet": "", "details": []}

    lignes_texte = []
    details = []

    # resultat[0] est une liste de blocs : [coordonnees, (texte, confiance)]
    for bloc in resultat[0]:
        coordonnees = bloc[0]          # utilisé uniquement pour debug
        texte, confiance = bloc[1]

        lignes_texte.append(texte)
        details.append(
            {
                "texte": texte,
                "confiance": round(float(confiance), 4),
                "coordonnees": coordonnees,  # debug seulement, jamais utilisé pour l'extraction
            }
        )

    texte_complet = "\n".join(lignes_texte)

    logger.info("[INFO] OCR terminé")
    logger.info(f"[INFO] Nombre de textes détectés : {len(details)}")

    return {"texte_complet": texte_complet, "details": details}
