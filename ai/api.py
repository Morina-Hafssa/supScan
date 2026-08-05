from flask import Flask, request, jsonify, send_from_directory
from flask_cors import CORS
from dotenv import load_dotenv
import traceback
import os
import uuid
import threading
import time
from datetime import datetime
from concurrent.futures import ThreadPoolExecutor, as_completed
import sys
from pathlib import Path
import argparse

# ============================================================
# FIX: Proper .env loading for both dev and frozen (exe) mode
# ============================================================
def load_env_file():
    """Load .env file from the correct location"""
    
    if getattr(sys, "frozen", False):
        # Running as compiled executable
        base_dir = Path(sys.executable).parent
        print(f"Running as EXE. Base directory: {base_dir}")
    else:
        # Running as Python script
        base_dir = Path(__file__).parent
        print(f"Running as script. Base directory: {base_dir}")
    
    # Try multiple possible .env locations
    possible_env_paths = [
        base_dir / ".env",                          # Same folder as exe/script
        base_dir.parent / ".env",                   # Parent folder
        base_dir.parent.parent / ".env",            # Grandparent folder
        Path(__file__).parent / ".env",            # Original ai folder (for dev)
        Path(__file__).parent.parent / ".env",     # Project root
    ]
    
    for env_path in possible_env_paths:
        if env_path.exists():
            print(f" Found .env at: {env_path}")
            load_dotenv(env_path)
            return True
    
    # Fallback: try standard load_dotenv()
    load_dotenv()
    
    # Check if any .env exists in current directory
    if Path(".env").exists():
        print(f" Found .env in current directory: {Path.cwd() / '.env'}")
        return True
    
    print(" No .env file found in any location")
    return False

# Load the .env file
load_env_file()

# ============================================================
# NOW check for API keys
# ============================================================
api_key = os.getenv("GEMINI_API_KEY") or os.getenv("GEMINI_API_KEY_1")

if api_key:
    print(f" API Key loaded: {api_key[:10]}...")
else:
    print(" No Gemini API keys found in environment!")
    print(f"Current working directory: {os.getcwd()}")
    
    # List all .env files in the directory tree for debugging
    print("\nSearching for .env files:")
    for root, dirs, files in os.walk(Path(__file__).parent.parent):
        if ".env" in files:
            print(f"  Found: {os.path.join(root, '.env')}")
        if len(files) > 20:  # Limit output
            break

# ============================================================
# FIX: Get poppler path dynamically for both dev and packaged mode
# ============================================================
def get_poppler_path():
    """
    Get the poppler path dynamically.
    Works in both development and packaged (frozen) mode.
    """
    if getattr(sys, "frozen", False):
        # Running as compiled executable (SupScanAPI.exe)
        # Poppler should be in the same folder as the exe
        base_dir = Path(sys.executable).parent
        print(f"[Poppler] Running as EXE. Base directory: {base_dir}")
    else:
        # Running as Python script
        base_dir = Path(__file__).parent
        print(f"[Poppler] Running as script. Base directory: {base_dir}")
    
    # Try multiple possible poppler locations
    possible_poppler_paths = [
        # For packaged mode: next to the exe
        base_dir / "poppler" / "Library" / "bin",
        # For development: in the ai folder
        base_dir / "poppler" / "Library" / "bin",
        # For development: in the project root
        base_dir.parent / "poppler" / "Library" / "bin",
        # System install fallback
        Path(r"C:\poppler\Library\bin"),
        Path(r"C:\Program Files\poppler\Library\bin"),
        # Environment variable fallback
        Path(os.environ.get("POPPLER_PATH", "")),
    ]
    
    for poppler_path in possible_poppler_paths:
        if poppler_path.exists() and poppler_path.is_dir():
            # Check if pdfinfo.exe exists in the bin folder
            pdfinfo_exe = poppler_path / "pdfinfo.exe"
            if pdfinfo_exe.exists():
                print(f"[Poppler] Found poppler at: {poppler_path}")
                return str(poppler_path)
            else:
                print(f"[Poppler] Found directory but no pdfinfo.exe in: {poppler_path}")
        else:
            print(f"[Poppler] Path not found: {poppler_path}")
    
    # Last resort: warn and use None (will use system PATH)
    print("[Poppler] WARNING: Poppler not found. Will try system PATH.")
    return None

# Get the poppler path once at startup
POPPLER_PATH = get_poppler_path()
print(f"[Poppler] Using path: {POPPLER_PATH}")

# ============================================================
# IMPORTANT: Using Vision-based extractor - NO OCR!
# ============================================================
# OCR imports are completely removed. We now use:
# - ai_extractor_vision: Direct image → Gemini Vision extraction
# - No PaddleOCR, no Tesseract, no OCR at all
# ============================================================

from ai_extractor import (
    extract_invoice_from_image,
    extract_batch_invoices_optimized
)
from validator import valider_donnees_facture, ErreurValidation
from pdf_processor import get_pdf_page_count
from pdf2image import convert_from_path

app = Flask(__name__)
CORS(app)
UPLOAD_FOLDER = "uploads"
os.makedirs(UPLOAD_FOLDER, exist_ok=True)

# Serve files from uploads folder
@app.route('/uploads/<path:filename>')
def uploaded_file(filename):
    """Serve files from the uploads directory"""
    return send_from_directory('uploads', filename)

# In-memory job storage
jobs = {}

# Thread pool for parallel AI processing (OCR removed, only AI remains)
BATCH_EXECUTOR = ThreadPoolExecutor(max_workers=8)

# ============================================================
# GLOBAL ERROR HANDLER - Catches ALL exceptions
# ============================================================
@app.errorhandler(Exception)
def handle_error(error):
    """Global error handler that categorizes errors and returns structured responses"""
    error_type = 'SERVER_ERROR'
    status_code = 500
    error_message = str(error)
    
    print(f" GLOBAL ERROR HANDLER: {error_type} - {error_message}")
    traceback.print_exc()
    
    # Detect error type from message
    error_lower = error_message.lower()
    
    if 'quota' in error_lower or 'rate limit' in error_lower or 'exceeded' in error_lower:
        error_type = 'QUOTA_EXCEEDED'
        status_code = 429
    elif 'timeout' in error_lower or 'timed out' in error_lower:
        error_type = 'TIMEOUT'
        status_code = 504
    elif 'network' in error_lower or 'connection' in error_lower or 'unreachable' in error_lower:
        error_type = 'NETWORK_ERROR'
        status_code = 503
    elif 'invalid api key' in error_lower or 'authentication' in error_lower or 'unauthorized' in error_lower:
        error_type = 'AUTH_ERROR'
        status_code = 401
    elif 'not found' in error_lower or '404' in error_lower:
        error_type = 'NOT_FOUND'
        status_code = 404
    elif 'validation' in error_lower or 'invalid' in error_lower:
        error_type = 'VALIDATION_ERROR'
        status_code = 400
    
    # Return structured error response for API clients
    return jsonify({
        'success': False,
        'error_type': error_type,
        'message': error_message,
        'code': status_code,
        'timestamp': datetime.now().isoformat()
    }), status_code


@app.route("/ping")
def ping():
    return jsonify({"message": "pong"})


@app.route("/upload", methods=["POST"])
def upload():
    """
    Endpoint to upload files and create a job.
    Returns immediately with a job_id.
    """
    print("=== UPLOAD STARTED ===")
    print("FILES:", request.files)
    print("FORM :", request.form)
    
    try:
        if "invoices[]" not in request.files:
            return jsonify({
                "success": False,
                "message": "No invoice uploaded."
            }), 400

        files = request.files.getlist("invoices[]")
        files = [f for f in files if f.filename != ""]
        
        if not files:
            return jsonify({
                "success": False,
                "message": "Empty filename(s)."
            }), 400

        # File size check removed - accepting any file size

        mode = request.form.get('mode', 'first_page')
        print(f"Mode: {mode}")
        print(f"Number of files uploaded: {len(files)}")

        job_id = str(uuid.uuid4())
        
        saved_files = []
        for file in files:
            extension = os.path.splitext(file.filename)[1]
            filename = f"{job_id}_{uuid.uuid4()}{extension}"
            filepath = os.path.join(UPLOAD_FOLDER, filename)
            file.save(filepath)
            saved_files.append({
                'path': filepath,
                'filename': file.filename,
                'extension': extension.lower(),
                'is_pdf': extension.lower() == '.pdf'
            })
            print(f"File saved: {filepath}")

        jobs[job_id] = {
            'id': job_id,
            'status': 'uploaded',
            'mode': mode,
            'files': saved_files,
            'created_at': datetime.now().isoformat(),
            'result': None,
            'error': None,
            'progress': 0,
            'total_pages': 0,
            'processed_pages': 0
        }
        
        print(f"Job created: {job_id}")
        
        # Start background processing with VISION pipeline (NO OCR)
        thread = threading.Thread(
            target=process_job_vision,
            args=(job_id,)
        )
        thread.daemon = True
        thread.start()
        
        print(f"Background thread started for job: {job_id}")
        
        return jsonify({
            "success": True,
            "job_id": job_id,
            "status": "uploaded",
            "message": "File uploaded successfully. Processing started in background."
        })
        
    except Exception as e:
        # Let the global error handler catch this
        raise


@app.route("/status/<job_id>", methods=["GET"])
def get_status(job_id):
    """
    Endpoint to check job status.
    """
    try:
        if job_id not in jobs:
            raise Exception('Job not found')
        
        job = jobs[job_id]
        
        response = {
            "success": True,
            "job_id": job_id,
            "status": job['status'],
            "progress": job.get('progress', 0),
            "created_at": job.get('created_at')
        }
        
        if job['status'] == 'completed' and job.get('result'):
            response['invoices'] = job['result']
        
        if job['status'] == 'failed' and job.get('error'):
            response['error'] = job['error']
            # Add error_type for frontend handling
            error_msg = job['error'].lower()
            if 'quota' in error_msg or 'rate limit' in error_msg:
                response['error_type'] = 'QUOTA_EXCEEDED'
            elif 'timeout' in error_msg:
                response['error_type'] = 'TIMEOUT'
            elif 'network' in error_msg:
                response['error_type'] = 'NETWORK_ERROR'
            else:
                response['error_type'] = 'SERVER_ERROR'
        
        return jsonify(response)
        
    except Exception as e:
        # Let global error handler catch this
        raise


@app.route("/start/<job_id>", methods=["POST"])
def start_job(job_id):
    """
    Endpoint to manually start a job (optional).
    """
    try:
        if job_id not in jobs:
            raise Exception('Job not found')
        
        job = jobs[job_id]
        
        if job['status'] != 'uploaded':
            raise Exception(f"Job already in status: {job['status']}")
        
        thread = threading.Thread(
            target=process_job_vision,
            args=(job_id,)
        )
        thread.daemon = True
        thread.start()
        
        return jsonify({
            "success": True,
            "message": "Job started",
            "job_id": job_id
        })
        
    except Exception as e:
        raise


def convert_pdf_to_images(pdf_path, poppler_path=None):
    """
    Convert PDF to images and save them to disk.
    Uses PNG format for better quality (no compression artifacts).
    Resizes images to optimize for Gemini.
    """
    try:
        # Use the global POPPLER_PATH if not provided
        if poppler_path is None:
            poppler_path = POPPLER_PATH
        
        print(f"[convert_pdf_to_images] Using poppler path: {poppler_path}")
        
        # Try to convert with provided poppler path
        try:
            pages = convert_from_path(pdf_path, poppler_path=poppler_path)
        except Exception as e:
            # If poppler_path is None or invalid, try without it
            if poppler_path is None:
                print("[convert_pdf_to_images] No poppler path, trying system PATH...")
                pages = convert_from_path(pdf_path)
            else:
                print(f"[convert_pdf_to_images] Error with poppler path: {e}")
                # Try without poppler_path as fallback
                print("[convert_pdf_to_images] Trying without poppler path...")
                pages = convert_from_path(pdf_path)
        
        image_paths = []
        temp_dir = os.path.join(UPLOAD_FOLDER, "temp_pages")
        os.makedirs(temp_dir, exist_ok=True)
        
        for i, page in enumerate(pages):
            # Resize to reduce upload size while maintaining readability
            # Gemini doesn't need 3500x5000 - 1600x1600 is sufficient
            page.thumbnail((1600, 1600))
            
            # Use PNG for text-heavy documents (no compression artifacts)
            image_filename = f"page_{uuid.uuid4()}_{i+1}.png"
            image_path = os.path.join(temp_dir, image_filename)
            
            page.save(image_path, "PNG")
            image_paths.append(image_path)
            print(f"   Saved page {i+1} to: {image_path} (PNG, resized)")
        
        return image_paths
        
    except Exception as e:
        print(f"Error converting PDF to images: {e}")
        raise


def get_web_path(file_path):
    """
    Convert a file system path to a web-accessible URL.
    """
    if file_path.startswith(UPLOAD_FOLDER):
        rel_path = file_path.replace(UPLOAD_FOLDER, '').lstrip('/\\')
        rel_path = rel_path.replace('\\', '/')
        return f"/uploads/{rel_path}"
    return file_path


def process_job_vision(job_id):
    """
    Vision-based processing pipeline - NO OCR!
    
    Pipeline:
    1. Upload files
    2. Convert files according to selected mode (first_page, full_document, each_page)
    3. Process images with Gemini Vision (extract invoice data)
    4. Validate extracted data
    
    No OCR step at all!
    """
    print(f"\n=== PROCESSING JOB {job_id} WITH VISION PIPELINE (NO OCR) ===")
    start_time = time.perf_counter()
    
    job = jobs[job_id]
    all_image_paths = []
    
    try:
        job['status'] = 'processing'
        job['progress'] = 5
        print(f"Job {job_id}: Status -> processing")
        
        saved_files = job['files']
        mode = job.get('mode', 'first_page')  # Get mode from job
        total_pages = 0
        processed_files = 0
        failed_files = 0
        
        # ============================================================
        # STEP 1: Convert files according to selected mode
        # ============================================================
        print(f"Step 1: Converting files with mode: {mode}")
        print(f"[Poppler] Using path: {POPPLER_PATH}")
        
        for idx, file_info in enumerate(saved_files):
            filepath = file_info['path']
            is_pdf = file_info['is_pdf']
            filename = file_info['filename']
            
            print(f"   Processing file: {filename}")
            
            if is_pdf:
                # Use the global POPPLER_PATH (dynamic resolution)
                poppler_path = POPPLER_PATH
                
                # ============================================================
                # MODE HANDLING: Convert PDF based on selected mode
                # ============================================================
                if mode == "full_document":
                    print("    Mode full_document: extracting only first page")
                    pages = convert_pdf_to_images(filepath, poppler_path)
                    # Only take the first page
                    image_paths = pages[:1] if pages else []
                    
                elif mode == "first_page":
                    print("    Mode first_page: extracting only first page")
                    pages = convert_pdf_to_images(filepath, poppler_path)
                    # Only take the first page
                    image_paths = pages[:1] if pages else []
                    
                elif mode == "each_page":
                    print("    Mode each_page: extracting every page")
                    image_paths = convert_pdf_to_images(filepath, poppler_path)
                    
                else:
                    raise ValueError(f"Unknown mode: {mode}")
                
                all_image_paths.extend(image_paths)
                total_pages += len(image_paths)
                print(f"    Converted {len(image_paths)} page(s) from {filename}")
                
            else:
                # It's already an image - resize and save as PNG for consistency
                from PIL import Image
                try:
                    img = Image.open(filepath)
                    
                    # Convert to RGB if necessary
                    if img.mode != 'RGB':
                        img = img.convert('RGB')
                    
                    # Resize
                    img.thumbnail((1600, 1600))
                    
                    # Save as PNG in temp folder
                    temp_dir = os.path.join(UPLOAD_FOLDER, "temp_pages")
                    os.makedirs(temp_dir, exist_ok=True)
                    
                    image_filename = f"image_{uuid.uuid4()}_{idx+1}.png"
                    image_path = os.path.join(temp_dir, image_filename)
                    img.save(image_path, "PNG")
                    
                    all_image_paths.append(image_path)
                    total_pages += 1
                    print(f"    Resized and saved image: {image_path}")
                except Exception as e:
                    print(f"    Error processing image: {e}")
                    # Fallback: use original
                    all_image_paths.append(filepath)
                    total_pages += 1
        
        job['total_pages'] = total_pages
        job['progress'] = 20
        print(f"\n Total images to process: {total_pages}")
        
        if not all_image_paths:
            job['status'] = 'failed'
            job['error'] = "No images could be extracted from the uploaded files."
            return
        
        # ============================================================
        # STEP 2: Process images with Gemini Vision (NO OCR!)
        # ============================================================
        print("\nStep 2: Processing images with Gemini Vision (NO OCR)...")
        ai_start = time.perf_counter()
        
        job['status'] = 'extracting'
        job['progress'] = 30
        
        # Start with conservative batch size for reliability
        # Start with 1-5 images per batch, then scale up
        if len(all_image_paths) <= 5:
            batch_size = len(all_image_paths)
        elif len(all_image_paths) <= 20:
            batch_size = 5
        elif len(all_image_paths) <= 50:
            batch_size = 8
        else:
            batch_size = 10
        
        print(f"Using batch size: {batch_size}")
        print(f"Total batches: {(len(all_image_paths) + batch_size - 1) // batch_size}")
        
        all_ai_results = []
        
        # Process images in batches
        for i in range(0, len(all_image_paths), batch_size):
            batch = all_image_paths[i:i + batch_size]
            batch_num = i // batch_size + 1
            
            print(f"\n    Processing batch {batch_num} ({len(batch)} images)...")
            
            try:
                # Use optimized batch processing with parallel workers
                batch_results = extract_batch_invoices_optimized(batch, batch_size)
                
                # Add metadata to results
                for j, result in enumerate(batch_results):
                    if j < len(batch):
                        result['preview'] = get_web_path(batch[j])
                        result['fileType'] = 'png'
                        result['fileName'] = os.path.basename(batch[j])
                        result['page'] = i + j + 1
                        all_ai_results.append(result)
                
                print(f"    Batch {batch_num} completed ({len(batch_results)} invoices)")
                
                # Update progress
                progress = 30 + (i + len(batch)) / len(all_image_paths) * 50
                job['progress'] = int(progress)
                
            except Exception as e:
                print(f"    Batch {batch_num} failed: {e}")
                print(f"     Processing individually...")
                
                # Process individually as fallback
                for j, image_path in enumerate(batch):
                    try:
                        print(f"      Processing image {j+1}/{len(batch)}...")
                        single_result = extract_invoice_from_image(image_path)
                        single_result['preview'] = get_web_path(image_path)
                        single_result['fileType'] = 'png'
                        single_result['fileName'] = os.path.basename(image_path)
                        all_ai_results.append(single_result)
                    except Exception as e2:
                        print(f"      Individual extraction failed: {e2}")
                        failed_files += 1
        
        ai_time = time.perf_counter() - ai_start
        print(f"\n AI extraction completed in {ai_time:.2f}s for {len(all_ai_results)} invoices")
        
        job['progress'] = 85
        
        # ============================================================
        # STEP 3: Validate all results
        # ============================================================
        print("\nStep 3: Validating all results...")
        val_start = time.perf_counter()
        
        validated_invoices = []
        for invoice_data in all_ai_results:
            try:
                validated = valider_donnees_facture(invoice_data)
                validated_invoices.append(validated)
                processed_files += 1
            except ErreurValidation as e:
                print(f"    Validation failed: {e}")
                failed_files += 1
        
        val_time = time.perf_counter() - val_start
        print(f" Validation completed in {val_time:.2f}s")
        
        job['result'] = validated_invoices
        
        # ============================================================
        # COMPLETED
        # ============================================================
        total_time = time.perf_counter() - start_time
        print(f"\n{'='*50}")
        print(f" JOB {job_id} COMPLETED")
        print(f"{'='*50}")
        print(f" Total time: {total_time:.2f}s ({total_time/60:.1f} minutes)")
        print(f"   AI time: {ai_time:.2f}s")
        print(f"   Validation time: {val_time:.2f}s")
        print(f"   Successfully processed: {processed_files}")
        print(f"   Failed: {failed_files}")
        print(f"   Total invoices extracted: {len(validated_invoices)}")
        print(f"   Mode used: {mode}")
        print(f"{'='*50}")
        
        job['status'] = 'completed'
        job['progress'] = 100
        job['completed_at'] = datetime.now().isoformat()
        
        if not validated_invoices:
            job['status'] = 'failed'
            job['error'] = "No invoices could be validated from the extracted data."
        
        print(f"Job {job_id}: Status -> {job['status']}")
        
    except ErreurValidation as e:
        print(f" Job {job_id}: Validation error - {str(e)}")
        job['status'] = 'failed'
        job['error'] = str(e)
        
    except Exception as e:
        print(f" Job {job_id}: Error - {str(e)}")
        traceback.print_exc()
        job['status'] = 'failed'
        job['error'] = str(e)
        
    finally:
        # Clean up temporary page images
        print("\nCleaning up temporary files...")
        for img_path in all_image_paths:
            if os.path.exists(img_path):
                try:
                    os.remove(img_path)
                    print(f"   Removed: {os.path.basename(img_path)}")
                except Exception as e:
                    print(f"   Error removing {img_path}: {e}")

        # Clean up the ORIGINAL uploaded files (PDFs/images saved in /upload)
        for file_info in job.get('files', []):
            original_path = file_info['path']
            if os.path.exists(original_path):
                try:
                    os.remove(original_path)
                    print(f"   Removed original upload: {os.path.basename(original_path)}")
                except Exception as e:
                    print(f"   Error removing {original_path}: {e}")

        # Clean up temp_pages directory if empty
        temp_dir = os.path.join(UPLOAD_FOLDER, "temp_pages")
        if os.path.exists(temp_dir) and not os.listdir(temp_dir):
            try:
                os.rmdir(temp_dir)
                print(f"   Removed empty temp directory")
            except:
                pass


@app.route("/extract", methods=["POST"])
def extract():
    """
    Legacy endpoint - kept for compatibility.
    Use /upload for the new async workflow.
    """
    return jsonify({
        "success": False,
        "message": "This endpoint is deprecated. Please use /upload for asynchronous processing."
    }), 410


@app.route("/extract_batch", methods=["POST"])
def extract_batch():
    """
    Legacy endpoint - kept for compatibility.
    """
    return jsonify({
        "success": False,
        "message": "This endpoint is deprecated. Please use /upload for asynchronous processing."
    }), 410


@app.route("/pdf_info", methods=["POST"])
def pdf_info():
    """
    Endpoint to get PDF information (number of pages).
    """
    filepath = None
    try:
        if "file" not in request.files:
            return jsonify({
                "success": False,
                "message": "No file uploaded."
            }), 400
        
        file = request.files["file"]
        if file.filename == "":
            return jsonify({
                "success": False,
                "message": "Empty filename."
            }), 400
        
        extension = os.path.splitext(file.filename)[1]
        filename = f"{uuid.uuid4()}{extension}"
        filepath = os.path.join(UPLOAD_FOLDER, filename)
        file.save(filepath)
        
        if extension.lower() != '.pdf':
            return jsonify({
                "success": True,
                "is_pdf": False,
                "message": "File is not a PDF"
            })
        
        # FIXED: Pass the poppler path to get_pdf_page_count (2 arguments)
        page_count = get_pdf_page_count(filepath, POPPLER_PATH)
        
        return jsonify({
            "success": True,
            "is_pdf": True,
            "pages": page_count,
            "filename": file.filename
        })
        
    except Exception as e:
        # Let global error handler catch this
        raise
        
    finally:
        if filepath and os.path.exists(filepath):
            try:
                os.remove(filepath)
            except:
                pass


# ============================================================
# HEALTH CHECK ENDPOINT
# ============================================================
@app.route("/api/health", methods=["GET"])
def health():
    """
    Health check endpoint for network error detection.
    """
    return jsonify({
        'status': 'OK',
        'timestamp': datetime.now().isoformat(),
        'jobs': len(jobs)
    })


if __name__ == "__main__":
    parser = argparse.ArgumentParser()
    parser.add_argument("--port", type=int, default=5000, help="Port to run the server on")
    args = parser.parse_args()
    
    print(f"[API] Starting server on port {args.port}")
    print(f"[Poppler] Final path: {POPPLER_PATH}")
    
    app.run(
        host="127.0.0.1",
        port=args.port,
        debug=False,
        threaded=True
    )