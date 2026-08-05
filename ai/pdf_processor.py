"""
Module de traitement PDF - SupScan
===================================

Ce module gère le traitement des fichiers PDF, y compris:
- Extraction des pages en images
- Détection du nombre de pages
- Traitement selon différents modes (première page, tout le PDF, chaque page)
"""

import os
import logging
from pdf2image import convert_from_path
import tempfile

logger = logging.getLogger("supscan.pdf_processor")


def get_pdf_page_count(pdf_path: str) -> int:
    """
    Récupère le nombre de pages d'un PDF.
    
    Args:
        pdf_path: chemin vers le fichier PDF
        
    Returns:
        int: nombre de pages
    """
    try:
        from PyPDF2 import PdfReader
        reader = PdfReader(pdf_path)
        return len(reader.pages)
    except Exception as e:
        logger.error(f"Erreur lors de la lecture du PDF: {e}")
        return 1


def convert_pdf_to_images(pdf_path: str, output_folder: str = None) -> list:
    """
    Convertit un PDF en images (une par page).
    
    Args:
        pdf_path: chemin vers le fichier PDF
        output_folder: dossier de sortie (si None, utilise un dossier temporaire)
        
    Returns:
        list: chemins des images créées
    """
    try:
        if output_folder is None:
            output_folder = tempfile.mkdtemp()
        
        # Convertir PDF en images
        images = convert_from_path(pdf_path, dpi=200)
        
        image_paths = []
        for i, image in enumerate(images, 1):
            image_path = os.path.join(output_folder, f"page_{i}.jpg")
            image.save(image_path, "JPEG", quality=85)
            image_paths.append(image_path)
            logger.info(f"Page {i} convertie en image: {image_path}")
        
        return image_paths
        
    except Exception as e:
        logger.error(f"Erreur lors de la conversion du PDF: {e}")
        raise


def process_pdf_by_mode(pdf_path: str, mode: str) -> list:
    """
    Traite un PDF selon le mode spécifié.
    
    Args:
        pdf_path: chemin vers le fichier PDF
        mode: 'first_page', 'full_document', 'each_page'
        
    Returns:
        list: chemins des images à traiter
    """
    page_count = get_pdf_page_count(pdf_path)
    logger.info(f"PDF a {page_count} page(s)")
    
    if mode == 'first_page':
        # Seulement la première page
        logger.info("Mode: Première page uniquement")
        images = convert_pdf_to_images(pdf_path)
        return [images[0]] if images else []
        
    elif mode == 'full_document':
        # Tout le PDF = une seule facture
        # Seulement la première page est analysée
        logger.info("Mode: PDF complet = une facture (première page uniquement)")
        
        images = convert_pdf_to_images(pdf_path)
        
        return [images[0]] if images else []
        
    elif mode == 'each_page':
        # Chaque page = une facture
        logger.info("Mode: Chaque page = une facture")
        return convert_pdf_to_images(pdf_path)
        
    else:
        raise ValueError(f"Mode inconnu: {mode}")