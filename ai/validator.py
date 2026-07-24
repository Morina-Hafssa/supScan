"""
Module de validation - SupScan
================================

Ce module valide strictement les données renvoyées par Gemini avant
de les considérer comme fiables et de les sauvegarder.

Aucune donnée n'est inventée ici : on vérifie seulement la cohérence
de ce que Gemini a retourné, et on lève une erreur claire si le
résultat n'est pas exploitable.
"""

import re
import logging
from datetime import datetime

logger = logging.getLogger("supscan.validator")

CHAMPS_ATTENDUS = [
    "vendor",
    "invoice_date",
    "reference",
    "tax_code",
    "amount",
    "currency",
    "description"
]
DEVISES_VALIDES = {"EUR", "MAD", "USD", "GBP"}

# Formats de date acceptés (on essaie chacun dans l'ordre)
FORMATS_DATE = ["%d/%m/%Y", "%d-%m-%Y", "%Y-%m-%d", "%d/%m/%y"]


class ErreurValidation(Exception):
    """Exception levée quand les données extraites ne sont pas valides."""


def _valider_chaine_ou_null(valeur, nom_champ: str):
    if valeur is None:
        return None
    if not isinstance(valeur, str) or not valeur.strip():
        raise ErreurValidation(f"[ERREUR] Champ '{nom_champ}' invalide : {valeur!r}")
    return valeur.strip()

def _valider_tax_code_ou_null(valeur):
    if valeur is None:
        return None

    if not isinstance(valeur, str):
        raise ErreurValidation(f"[ERREUR] Champ 'tax_code' invalide : {valeur!r}")

    valeur = valeur.strip().upper()

    if valeur != "A0":
        raise ErreurValidation(f"[ERREUR] Code taxe invalide : {valeur!r}")

    return valeur
def _valider_date_ou_null(valeur):
    if valeur is None:
        return None
    if not isinstance(valeur, str):
        raise ErreurValidation(f"[ERREUR] Champ 'invoice_date' invalide : {valeur!r}")

    for fmt in FORMATS_DATE:
        try:
            date = datetime.strptime(valeur.strip(), fmt)
            return date.strftime("%Y-%m-%d")
        except ValueError:
            continue

    raise ErreurValidation(
        f"[ERREUR] Champ 'invoice_date' n'est pas une date valide : {valeur!r}"
    )


def _valider_montant_ou_null(valeur):
    if valeur is None:
        return None
    if isinstance(valeur, bool):
        raise ErreurValidation(f"[ERREUR] Champ 'amount' invalide : {valeur!r}")
    if isinstance(valeur, (int, float)):
        return float(valeur)
    # Tolérance : si Gemini renvoie quand même une chaîne du type "5 245,00"
    if isinstance(valeur, str):
        nettoye = re.sub(r"[^\d,.\-]", "", valeur).replace(",", ".")
        try:
            return float(nettoye)
        except ValueError:
            raise ErreurValidation(f"[ERREUR] Champ 'amount' invalide : {valeur!r}")
    raise ErreurValidation(f"[ERREUR] Champ 'amount' invalide : {valeur!r}")


def _valider_devise_ou_null(valeur):
    if valeur is None:
        return None
    if not isinstance(valeur, str):
        raise ErreurValidation(f"[ERREUR] Champ 'currency' invalide : {valeur!r}")

    code = valeur.strip().upper()

    correspondances = {"€": "EUR", "DH": "MAD", "$": "USD", "£": "GBP"}
    code = correspondances.get(valeur.strip(), code)

    if code not in DEVISES_VALIDES:
        raise ErreurValidation(f"[ERREUR] Devise non supportée : {valeur!r}")

    return code


def valider_donnees_facture(donnees: dict) -> dict:
    """
    Valide strictement les données extraites par Gemini.

    Args:
        donnees: dict brut renvoyé par extract_invoice_data_with_ai().

    Returns:
        dict validé et normalisé, prêt à être sauvegardé en JSON.

    Raises:
        ErreurValidation: si une donnée est incohérente ou si des champs
            attendus sont manquants.
    """
    if not isinstance(donnees, dict):
        raise ErreurValidation("[ERREUR] La réponse de Gemini n'est pas un objet JSON.")

    champs_manquants = [c for c in CHAMPS_ATTENDUS if c not in donnees]
    if champs_manquants:
        raise ErreurValidation(
            f"[ERREUR] Champs manquants dans la réponse Gemini : {champs_manquants}"
        )

    donnees_validees = {
        "vendor": _valider_chaine_ou_null(donnees.get("vendor"), "vendor"),
        "invoice_date": _valider_date_ou_null(donnees.get("invoice_date")),
        "reference": _valider_chaine_ou_null(donnees.get("reference"), "reference"),
        "tax_code": _valider_tax_code_ou_null(donnees.get("tax_code")),
        "amount": _valider_montant_ou_null(donnees.get("amount")),
        "currency": _valider_devise_ou_null(donnees.get("currency")),
        "description": _valider_chaine_ou_null(donnees.get("description"), "description"),
    }

    logger.info("[INFO] Validation terminée")

    return donnees_validees
