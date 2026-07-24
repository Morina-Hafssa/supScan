"""
Module d'extraction IA - SupScan
==================================

Ce module envoie le texte OCR brut à Gemini 2.5 Flash-Lite, qui a pour
seul rôle de COMPRENDRE le contenu de la facture et d'en extraire les
champs importants, en se basant sur le contexte et les mots-clés
(jamais sur la position du texte, puisque PaddleOCR ne fournit qu'un
texte linéaire).
"""

import os
import json
import logging

from google import genai

logger = logging.getLogger("supscan.ai_extractor")


# ---------------------------------------------------------------------------
# Prompt système : décrit précisément la tâche à Gemini.
# ---------------------------------------------------------------------------
PROMPT_SYSTEME = """Tu es un expert-comptable spécialisé dans la lecture de factures.

Les factures peuvent être en français, anglais, espagnol, allemand ou toute autre langue. Utilise uniquement le sens des mots, jamais leur position.

Le texte provient d'un OCR. Certains mots peuvent être mal reconnus, incomplets ou dans le désordre. Déduis les informations à partir du contexte.

On te fournit le texte OCR d'une ou plusieurs factures.
Pour chaque facture, utilise uniquement son propre contenu.

Ta mission : extraire exactement 5 champs et retourner UNIQUEMENT un objet
JSON strict, sans aucun texte autour, sans bloc Markdown, sans explication :

{
  "vendor": null,
  "invoice_date": null,
  "reference": null,
  "amount": null,
  "currency": null
}

Règles pour chaque champ :

1. vendor
Entreprise qui émet la facture (jamais le client).
Le fournisseur est généralement l'entreprise située dans l'en-tête. Ne jamais retourner le nom du client, même s'il apparaît en premier.
Ignorer : Client, Facturé à, Bill To, Customer, Ship To, Buyer, Consignee.
Indices : Supplier, Vendor, Seller, Société, Company.

2. invoice_date
Date de la facture uniquement.
Ne jamais prendre : Due Date, Delivery Date, Order Date, PO Date, Date d'échéance.
Indices : Date, Invoice Date, Date Facture, Document Date.

3. reference
Numéro de facture uniquement.
Indices : Facture, Facture N°, Invoice, Invoice No, Invoice Number, Invoice #, Document No, Reference, Ref.
Formats courants : FA08267, FAC/2026/1363, TKFA0390/26, 4000205790, HX26178, IN-4R300175.
Ne jamais retourner : PO, BC, BL, ICE, IF, RC, CNSS, TVA, Patente, Customer No, Vendor No.

4. amount
Montant final à payer figurant sur la facture. Si plusieurs totaux existent, choisir le dernier total payable.
Priorité : Net à payer, Total TTC, Grand Total, Amount Due, Amount Payable, Net Amount, Total Invoice, Total.
Ignorer : HT, Subtotal, TVA, Discount, Unit Price, Montant ligne.

5. currency
Retourner uniquement un code ISO.
Correspondances :€ → EUR DH → MAD $ → USD £ → GBP CHF → CHF CAD → CAD ¥ → JPY

Si un champ ne peut pas être identifié avec une confiance suffisante,
retourne null pour ce champ. N'invente JAMAIS une valeur.

Réponds UNIQUEMENT avec l'objet JSON, rien d'autre."""


def _construire_prompt_utilisateur(texte_ocr: str) -> str:
    """Construit le message utilisateur envoyé à Gemini."""
    return f"""Texte OCR :

{texte_ocr}

Extrais les champs demandés et retourne uniquement le JSON."""


def _nettoyer_reponse(texte_reponse: str) -> str:
    """
    Nettoie la réponse de Gemini au cas où elle contiendrait malgré
    tout des balises Markdown (```json ... ```) ou des espaces superflus.
    """
    reponse = texte_reponse.strip()

    if reponse.startswith("```"):
        # Retire les éventuelles balises ```json ... ```
        reponse = reponse.strip("`")
        if reponse.lower().startswith("json"):
            reponse = reponse[4:]
        reponse = reponse.strip()

    return reponse


def extract_invoice_data_with_ai(ocr_text: str) -> dict:
    """
    Envoie le texte OCR à Gemini 2.5 Flash-Lite et récupère les champs
    extraits de la facture.

    Args:
        ocr_text: texte brut renvoyé par PaddleOCR.

    Returns:
        dict avec les clés : vendor, invoice_date, reference, amount, currency,
        tax_code, description.

    Raises:
        RuntimeError: si la clé API est manquante ou si l'appel Gemini échoue.
        ValueError: si la réponse de Gemini n'est pas un JSON valide.
    """
    api_key = os.getenv("GEMINI_API_KEY")
    model_name = os.getenv("GEMINI_MODEL", "gemini-2.5-flash-lite")

    if not api_key:
        raise RuntimeError(
            "[ERREUR] GEMINI_API_KEY manquante. Vérifiez votre fichier .env"
        )

    if not ocr_text or not ocr_text.strip():
        raise ValueError("[ERREUR] Le texte OCR est vide, impossible d'extraire les champs.")

    client = genai.Client(api_key=api_key)

    prompt_utilisateur = _construire_prompt_utilisateur(ocr_text)

    try:
        reponse = client.models.generate_content(
            model=model_name,
            contents=prompt_utilisateur,
            config={
                "system_instruction": PROMPT_SYSTEME,
                "temperature": 0,
                "top_p": 0,  # Make extraction more deterministic
                "response_mime_type": "application/json",
                "response_schema": {
                    "type": "object",
                    "properties": {
                        "vendor": {"type": "string"},
                        "invoice_date": {"type": "string"},
                        "reference": {"type": "string"},
                        "amount": {"type": "number"},
                        "currency": {"type": "string"}
                    }
                }
            },
        )
    except Exception as exc:
        raise RuntimeError(f"[ERREUR] Échec de l'appel à Gemini : {exc}") from exc

    logger.info("[INFO] Appel Gemini effectué")

    texte_brut = reponse.text or ""
    texte_nettoye = _nettoyer_reponse(texte_brut)

    try:
        donnees = json.loads(texte_nettoye)
    except json.JSONDecodeError as exc:
        raise ValueError(
            f"[ERREUR] Réponse Gemini invalide, JSON illisible : {texte_brut!r}"
        ) from exc

    # Validate and ensure only expected fields are kept
    required = {
        "vendor",
        "invoice_date",
        "reference",
        "amount",
        "currency"
    }
    
    # Filter to only keep expected fields
    donnees = {k: donnees.get(k) for k in required}
    
    # Add the additional fields
    donnees["tax_code"] = "A0"
    
    if donnees.get("reference") and donnees.get("vendor"):
        donnees["description"] = f"F°{donnees['reference']} {donnees['vendor']}"
    else:
        donnees["description"] = None

    logger.info("[INFO] Données extraites et validées")

    return donnees