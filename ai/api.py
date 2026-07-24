from flask import Flask, request, jsonify
from flask_cors import CORS
from dotenv import load_dotenv
import traceback
import os
import uuid

from ocr import extraire_texte_ocr
from ai_extractor import extract_invoice_data_with_ai
from validator import valider_donnees_facture, ErreurValidation

# Load .env
load_dotenv()

app = Flask(__name__)
CORS(app)
UPLOAD_FOLDER = "uploads"
os.makedirs(UPLOAD_FOLDER, exist_ok=True)

@app.route("/ping")
def ping():
    return jsonify({"message": "pong"})


@app.route("/extract", methods=["POST"])
def extract():
    filepath = None  # Initialize filepath
    print("FILES:", request.files)
    print("FORM :", request.form)
    try:
        # Check if a file was uploaded
        if "invoice" not in request.files:
            return jsonify({
                "success": False,
                "message": "No invoice uploaded."
            }), 400

        file = request.files["invoice"]

        if file.filename == "":
            return jsonify({
                "success": False,
                "message": "Empty filename."
            }), 400

        # Save temporarily
        extension = os.path.splitext(file.filename)[1]
        filename = f"{uuid.uuid4()}{extension}"
        filepath = os.path.join(UPLOAD_FOLDER, filename)
        
        print("1. File received")
        file.save(filepath)
        print("2. File saved:", filepath)

        # OCR
        print("3. Starting OCR")
        resultat_ocr = extraire_texte_ocr(filepath)
        print("4. OCR finished")
        
        texte = resultat_ocr["texte_complet"]

        if not texte.strip():
            return jsonify({
                "success": False,
                "message": "No text detected."
            }), 400

        # AI extraction
        print("5. Starting Gemini")
        data = extract_invoice_data_with_ai(texte)
        print("\n===== GEMINI OUTPUT =====")
        print(data)
        print("=========================\n")
        print("6. Gemini finished")

        # Validation
        print("7. Starting validation")
        data = valider_donnees_facture(data)
        print("\n===== AFTER VALIDATOR =====")
        print(data)
        print("===========================\n")
        print("8. Validation finished")

        # Return success response
        return jsonify({
            "success": True,
            "ocr_text": texte,
            "data": data
        })

    except ErreurValidation as e:
        return jsonify({
            "success": False,
            "message": str(e)
        }), 400

    except Exception as e:
        traceback.print_exc()
        return jsonify({
            "success": False,
            "message": str(e)
        }), 500

    finally:
        # Clean up temporary file
        if filepath and os.path.exists(filepath):
            try:
                os.remove(filepath)
            except Exception as e:
                print(f"Error removing file: {e}")


if __name__ == "__main__":
    app.run(
        host="127.0.0.1",
        port=5000,
        debug=False,
        threaded=True
    )