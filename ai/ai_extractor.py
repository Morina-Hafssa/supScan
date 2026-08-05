"""
AI Extractor using Gemini Vision - Direct image processing without OCR
Supports multiple API keys with independent clients for true parallel processing.
Automatically disables invalid keys (401 UNAUTHENTICATED) during runtime.
Uses the new Google GenAI SDK for proper client isolation.
"""
import os
import json
import time
import re
import threading
from PIL import Image
from dotenv import load_dotenv
from concurrent.futures import ThreadPoolExecutor, as_completed

from google import genai
from google.genai import types

load_dotenv()

# ============================================================
# API KEY CONFIGURATION - Load all available keys
# ============================================================

API_KEYS = []

# Check for numbered keys (GEMINI_API_KEY_1, GEMINI_API_KEY_2, etc.)
i = 1
while True:
    key = os.getenv(f"GEMINI_API_KEY_{i}")
    if key and key.strip() and key not in API_KEYS:
        API_KEYS.append(key.strip())
        i += 1
    else:
        break


i = 1
while True:
    key = os.getenv(f"GEMINI_API_KEY{i}")
    if key and key.strip() and key not in API_KEYS:
        API_KEYS.append(key.strip())
        i += 1
    else:
        break

# Fallback to primary key if no numbered keys found
if not API_KEYS:
    primary_key = os.getenv("GEMINI_API_KEY")
    if primary_key and primary_key.strip():
        API_KEYS.append(primary_key.strip())

i = 1
while True:
    key = os.getenv(f"GEMINI_API_KEY_{i}_ALT")
    if key and key.strip() and key not in API_KEYS:
        API_KEYS.append(key.strip())
        i += 1
    else:
        break

if API_KEYS:
    print(f" Loaded {len(API_KEYS)} Gemini API key(s)")
    for idx, key in enumerate(API_KEYS, 1):
        print(f"   Key {idx}: {key[:10]}...")
else:
    print(" No Gemini API keys found! Check your .env file.")


class IndependentClient:
    """
    A wrapper around the new Google GenAI client that owns its own API key.
    No global configuration - each instance is completely independent.
    """
    
    def __init__(self, api_key, model_name="gemini-3.1-flash-lite"):
        self.api_key = api_key
        self.model_name = model_name
        self.is_valid = True  
        
        self.client = genai.Client(api_key=api_key)
        print(f"    Created independent client: {api_key[:10]}...")
    
    def generate_content(self, contents, generation_config=None):
        """
        Generate content using this client's independent API key.
        """
        try:
            response = self.client.models.generate_content(
                model=self.model_name,
                contents=contents,
                config=generation_config
            )
            return response
        except Exception as e:
            
            error_msg = str(e)
            if "401" in error_msg or "UNAUTHENTICATED" in error_msg or "API key not valid" in error_msg:
                self.is_valid = False
                print(f"    Key {self.api_key[:10]}... is INVALID! Marking as disabled.")
            raise e


class KeyRotator:
    """
    Thread-safe round-robin key rotator with independent clients.
    Each key has its own independent client instance.
    Can dynamically disable invalid keys during runtime.
    """
    
    def __init__(self, keys, model_name="gemini-3.1-flash-lite"):
        self.model_name = model_name
        self._lock = threading.Lock()
        self._counter = 0
        
        # Create independent clients for each key
        self.clients = []
        self.keys = []
        
        for key in keys:
            try:
                client = IndependentClient(key, model_name)
                self.clients.append(client)
                self.keys.append(key)
                print(f"    Initialized client for key: {key[:10]}...")
            except Exception as e:
                print(f"    Failed to create client for key {key[:10]}...: {e}")
                # Skip this key
                continue
        
        self.num_keys = len(self.clients)
        
        if self.num_keys == 0:
            raise ValueError("No valid API clients could be initialized")
    
    def disable_key(self, api_key):
        """
        Disable a specific API key permanently during runtime.
        Removes it from the rotation pool.
        """
        with self._lock:
            if api_key in self.keys:
                idx = self.keys.index(api_key)
                # Remove the key and its client
                self.keys.pop(idx)
                self.clients.pop(idx)
                self.num_keys = len(self.keys)
                print(f"    Permanently removed invalid key: {api_key[:10]}...")
                print(f"    {self.num_keys} key(s) remaining")
                
                # Reset counter to avoid index issues
                self._counter = 0
                return True
            return False
    
    def get_next_client(self):
        """
        Get the next valid client in round-robin order.
        Skips any disabled/invalid clients.
        Returns a tuple of (client, key_name) for tracking.
        """
        with self._lock:
            if self.num_keys == 0:
                raise ValueError("No valid API keys available")
            
            # Try to find a valid client
            attempts = 0
            while attempts < self.num_keys:
                idx = self._counter % self.num_keys
                self._counter += 1
                client = self.clients[idx]
                key = self.keys[idx]
                
                # Check if this client is still valid
                if client.is_valid:
                    return client, key
                else:
                    # This key is invalid, remove it
                    self.keys.pop(idx)
                    self.clients.pop(idx)
                    self.num_keys = len(self.keys)
                    print(f"    Removed invalid key: {key[:10]}...")
                    print(f"    {self.num_keys} key(s) remaining")
                    attempts += 1
            
            raise ValueError("No valid API keys available")
    
    def get_client_for_index(self, index):
        """Get a client for a specific index (for consistent mapping)"""
        with self._lock:
            if self.num_keys == 0:
                raise ValueError("No valid API keys available")
            
            idx = index % self.num_keys
            client = self.clients[idx]
            key = self.keys[idx]
            
            if not client.is_valid:
                # This key is invalid, remove it and get the next valid one
                self.keys.pop(idx)
                self.clients.pop(idx)
                self.num_keys = len(self.keys)
                print(f"    Removed invalid key: {key[:10]}...")
                print(f"    {self.num_keys} key(s) remaining")
                return self.get_next_client()
            
            return client, key
    
    def get_key_count(self):
        return self.num_keys
    
    def get_key_info(self):
        """Get information about all keys (for debugging)"""
        with self._lock:
            return [{"index": i, "key": key[:10] + "...", "valid": client.is_valid} 
                    for i, (key, client) in enumerate(zip(self.keys, self.clients))]
    
    def get_all_clients(self):
        """Get all valid clients (for batch operations)"""
        with self._lock:
            return [(client, key) for client, key in zip(self.clients, self.keys) if client.is_valid]


# Import threading for the lock
import threading

# Model configuration
MODEL_NAME = "gemini-3.1-flash-lite"

# System prompt for invoice extraction from images
PROMPT_SYSTEME = """
You are an invoice extraction system.

Read the attached invoice image carefully.

Extract the following fields:
- vendor: The company name or person who issued the invoice
- invoice_date: The invoice date in YYYY-MM-DD format
- reference: The invoice number or reference number
- amount: The total amount due (numeric, without currency symbol)
- currency: The invoice currency code (MAD, EUR, USD, GBP, etc.).

Currency extraction rules:
- If the currency is clearly visible on the invoice, return its ISO code (MAD, EUR, USD, GBP, etc.).
- If the currency is missing, unreadable, ambiguous, or you are not reasonably confident about it, return "MAD".
- Do NOT guess a foreign currency unless it is explicitly indicated on the invoice.

Return ONLY JSON with these fields.
If a field doesn't exist in the invoice, return null.
Exception: the "currency" field must never be null. If uncertain, return "MAD".

Never explain or add additional text.
The response must be valid JSON.

Example:
{
  "vendor": "ACME Corp",
  "invoice_date": "2024-01-15",
  "reference": "INV-2024-001",
  "amount": 1250.50,
  "currency": "MAD"
}
"""

# Initialize the key rotator with independent clients
if API_KEYS:
    try:
        key_rotator = KeyRotator(API_KEYS, MODEL_NAME)
        print(f" Key rotator initialized with {key_rotator.get_key_count()} clients")
    except Exception as e:
        print(f" Failed to initialize key rotator: {e}")
        key_rotator = None
else:
    key_rotator = None
    print(" No API keys available for rotation!")

# ============================================================
# COMPUTED FIELDS
# ============================================================

def _add_computed_fields(data):
    """
    Add computed fields that don't come from the invoice.
    These are deterministic and don't require AI.
    """
    data["tax_code"] = "V0"
    
    if data.get("reference") and data.get("vendor"):
        data["description"] = f"F°{data['reference']} {data['vendor']}"
    else:
        data["description"] = None
    
    return data


# ============================================================
# MAIN EXTRACTION FUNCTION - Now uses independent clients!
# ============================================================

def extract_invoice_from_image(image_path, max_retries=3, key_index=None):
    """
    Extract invoice data directly from an image using Gemini Vision.
    Uses independent clients - NO global state interference!
    Automatically disables invalid keys on 401 errors.
    
    Args:
        image_path: Path to the image file
        max_retries: Number of retries on failure
        key_index: Optional specific key index to use
    
    Returns:
        dict: Complete invoice data with 7 fields
    """
    if not os.path.exists(image_path):
        raise FileNotFoundError(f"Image not found: {image_path}")
    
    if not key_rotator:
        raise ValueError("No API keys available")
    
    # Get independent client (NO global configure!)
    try:
        if key_index is not None:
            client, key_used = key_rotator.get_client_for_index(key_index)
        else:
            client, key_used = key_rotator.get_next_client()
    except ValueError as e:
        print(f"    No valid API keys available: {e}")
        empty_data = {
            'vendor': None,
            'invoice_date': None,
            'reference': None,
            'amount': None,
            'currency': None,
            '_error': 'No valid API keys available'
        }
        return _add_computed_fields(empty_data)
    
    print(f"    Using key: {key_used[:10]}... (index: {key_index if key_index is not None else 'rotated'})")
    
    # Prepare generation config
    generation_config = types.GenerateContentConfig(
        temperature=0.0,
        top_p=0.95,
        top_k=40,
        max_output_tokens=1024,
    )
    
    for attempt in range(max_retries):
        try:
            # Load and optimize image
            image = Image.open(image_path)
            
            # Convert to RGB if necessary
            if image.mode != 'RGB':
                image = image.convert('RGB')
            
            # Generate response using independent client
            response = client.generate_content(
                contents=[image, PROMPT_SYSTEME],
                generation_config=generation_config
            )
            
            # Parse response
            if response.text:
                # Clean response (remove markdown code blocks if present)
                text = response.text.strip()
                if text.startswith('```json'):
                    text = text[7:]
                if text.startswith('```'):
                    text = text[3:]
                if text.endswith('```'):
                    text = text[:-3]
                text = text.strip()
                
                try:
                    # Parse JSON from Gemini
                    data = json.loads(text)
                    
                    # Ensure all 5 fields exist
                    fields = ['vendor', 'invoice_date', 'reference', 'amount', 'currency']
                    for field in fields:
                        if field not in data:
                            data[field] = None
                    
                    # Convert amount to float if possible
                    if data.get('amount') is not None:
                        try:
                            data['amount'] = float(data['amount'])
                        except (ValueError, TypeError):
                            data['amount'] = None
                    
                    # Add computed fields
                    data = _add_computed_fields(data)
                    data['_key_used'] = key_used[:10] + '...'
                    data['_attempt'] = attempt + 1
                    
                    return data
                    
                except json.JSONDecodeError as e:
                    print(f"    JSON parsing error: {e}")
                    print(f"   Response text: {text[:200]}...")
                    
                    # Try to extract JSON from text
                    json_match = re.search(r'\{.*\}', text, re.DOTALL)
                    if json_match:
                        try:
                            data = json.loads(json_match.group())
                            data = _add_computed_fields(data)
                            data['_key_used'] = key_used[:10] + '...'
                            return data
                        except:
                            pass
                    
                    if attempt < max_retries - 1:
                        # On retry, try a DIFFERENT key
                        try:
                            client, key_used = key_rotator.get_next_client()
                            print(f"    Retrying with different key: {key_used[:10]}... ({attempt + 1}/{max_retries})")
                            time.sleep(1)
                            continue
                        except ValueError:
                            print(f"    No more valid keys available")
                            break
                    else:
                        empty_data = {
                            'vendor': None,
                            'invoice_date': None,
                            'reference': None,
                            'amount': None,
                            'currency': None,
                            '_error': 'Failed to parse response',
                            '_key_used': key_used[:10] + '...'
                        }
                        return _add_computed_fields(empty_data)
            else:
                if attempt < max_retries - 1:
                    # Try a DIFFERENT key
                    try:
                        client, key_used = key_rotator.get_next_client()
                        print(f"    Empty response, retrying with different key: {key_used[:10]}... ({attempt + 1}/{max_retries})")
                        time.sleep(1)
                        continue
                    except ValueError:
                        print(f"    No more valid keys available")
                        break
                else:
                    empty_data = {
                        'vendor': None,
                        'invoice_date': None,
                        'reference': None,
                        'amount': None,
                        'currency': None,
                        '_error': 'Empty response from API',
                        '_key_used': key_used[:10] + '...'
                    }
                    return _add_computed_fields(empty_data)
                    
        except Exception as e:
            error_msg = str(e)
            print(f"    Error processing image: {error_msg[:100]}")
            
            # Check if this key is invalid (401)
            if "401" in error_msg or "UNAUTHENTICATED" in error_msg or "API key not valid" in error_msg:
                print(f"    Key {key_used[:10]}... is INVALID! Removing from pool.")
                key_rotator.disable_key(key_used)
                
                if attempt < max_retries - 1:
                    try:
                        # Get a new valid key
                        client, key_used = key_rotator.get_next_client()
                        print(f"    Retrying with new valid key: {key_used[:10]}... ({attempt + 1}/{max_retries})")
                        time.sleep(1)
                        continue
                    except ValueError:
                        print(f"    No more valid keys available")
                        break
            
            # Check if it's a quota error
            if "429" in error_msg or "quota" in error_msg.lower():
                if attempt < max_retries - 1:
                    try:
                        # Try a DIFFERENT key on quota errors
                        client, key_used = key_rotator.get_next_client()
                        print(f"    Quota error, trying different key: {key_used[:10]}... ({attempt + 1}/{max_retries})")
                        time.sleep(2 ** attempt)  # Exponential backoff
                        continue
                    except ValueError:
                        print(f"    No more valid keys available")
                        break
            
            if attempt < max_retries - 1:
                print(f"   Retrying... ({attempt + 1}/{max_retries})")
                time.sleep(2 ** attempt)
                continue
            else:
                empty_data = {
                    'vendor': None,
                    'invoice_date': None,
                    'reference': None,
                    'amount': None,
                    'currency': None,
                    '_error': error_msg,
                    '_key_used': key_used[:10] + '...'
                }
                return _add_computed_fields(empty_data)
    
    empty_data = {
        'vendor': None,
        'invoice_date': None,
        'reference': None,
        'amount': None,
        'currency': None,
        '_error': 'Max retries exceeded or no valid keys'
    }
    return _add_computed_fields(empty_data)


# ============================================================
# BATCH PROCESSING
# ============================================================

def extract_batch_invoices_optimized(image_paths, batch_size=5, use_key_rotation=True):
    """
    Optimized batch processing with independent clients.
    Each worker gets its own independent client - NO shared state!
    Automatically handles key invalidation.
    
    Args:
        image_paths: List of image file paths
        batch_size: Number of images to process in parallel
        use_key_rotation: If True, distribute requests across all available keys
    
    Returns:
        list: List of extracted invoice data
    """
    if not key_rotator:
        print(" No API keys available!")
        return [{
            'vendor': None,
            'invoice_date': None,
            'reference': None,
            'amount': None,
            'currency': None,
            'tax_code': 'V0',
            'description': None,
            '_error': 'No API keys available'
        }] * len(image_paths)
    
    results = [None] * len(image_paths)
    
    # IMPORTANT: Max workers should NOT exceed number of keys
    # to avoid overloading individual keys
    num_keys = key_rotator.get_key_count()
    max_workers = min(batch_size, num_keys)
    
    # Ensure at least 1 worker
    max_workers = max(1, max_workers)
    
    print(f"    Using {max_workers} workers with {num_keys} available key(s)")
    
    with ThreadPoolExecutor(max_workers=max_workers) as executor:
        future_to_index = {}
        
        for i, path in enumerate(image_paths):
            if use_key_rotation and key_rotator:
                # Assign specific key index for consistent mapping
                key_index = i % num_keys
                future = executor.submit(extract_invoice_from_image, path, 2, key_index)
            else:
                future = executor.submit(extract_invoice_from_image, path, 2)
            future_to_index[future] = i
        
        for future in as_completed(future_to_index):
            idx = future_to_index[future]
            try:
                result = future.result(timeout=180)
                results[idx] = result
                print(f"    Image {idx + 1}/{len(image_paths)} processed")
            except Exception as e:
                print(f"    Image {idx + 1} failed: {e}")
                empty_data = {
                    'vendor': None,
                    'invoice_date': None,
                    'reference': None,
                    'amount': None,
                    'currency': None,
                    'tax_code': 'V0',
                    'description': None,
                    '_error': str(e)
                }
                results[idx] = empty_data
    
    return results


def get_key_status():
    """Get status information about all configured API keys."""
    if not key_rotator:
        return {"status": "No keys configured", "keys": []}
    
    keys_info = key_rotator.get_key_info()
    return {
        "status": "OK",
        "total_keys": len(keys_info),
        "keys": keys_info
    }


# ============================================================
# BACKWARD COMPATIBILITY WRAPPERS
# ============================================================

def extract_invoice_data_with_ai(image_path):
    """Wrapper for backward compatibility"""
    return extract_invoice_from_image(image_path)


def extract_batch_invoices_with_ai(image_paths):
    """Wrapper for backward compatibility"""
    return extract_batch_invoices_optimized(image_paths, batch_size=5)


# ============================================================
# TEST FUNCTION
# ============================================================
if __name__ == "__main__":
    import sys
    
    # Print key status on startup
    print("\n" + "="*50)
    print("API KEY STATUS")
    print("="*50)
    status = get_key_status()
    print(f"Status: {status['status']}")
    print(f"Total keys: {status['total_keys']}")
    for key in status.get('keys', []):
        status_text = "Y" if key.get('valid', True) else "N"
        print(f"  {status_text} Key {key['index'] + 1}: {key['key']}")
    print("="*50 + "\n")
    
    if len(sys.argv) > 1:
        image_path = sys.argv[1]
        print(f"Testing extraction on: {image_path}")
        result = extract_invoice_from_image(image_path)
        print("\nExtracted data:")
        print(json.dumps(result, indent=2, ensure_ascii=False))
    else:
        print("Usage: python ai_extractor_vision.py <image_path>")
        print("Example: python ai_extractor_vision.py invoice.jpg")