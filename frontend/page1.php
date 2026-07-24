<?php
    session_start();
    include("Sidebar.php");
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Accueil - Remplir Sans Doute</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        :root {
            --main-color: #780000;
            --seondary-color: #003049;
            --accent-color: #4ca380;
            --white: white;
            --light-bg: #f8f9fa;
            --text-color: #2d3748;
            --border-color: #e2e8f0;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: var(--light-bg);
            color: var(--text-color);
            display: flex;
            min-height: 100vh;
        }

        .main-content {
            margin-left: 70px;
            padding: 40px;
            flex: 1;
            min-height: 100vh;
        }

        .page-header {
            margin-bottom: 40px;
            animation: fadeInDown 0.6s ease;
        }

        .page-header .title {
            font-size: 36px;
            font-weight: 700;
            color: var(--seondary-color);
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .page-header .title i {
            color: var(--accent-color);
            font-size: 32px;
        }

        .upload-container {
            background: white;
            border-radius: 20px;
            padding: 50px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.06);
            max-width: 800px;
            margin: 0 auto;
            transition: all 0.3s ease;
            animation: fadeInUp 0.8s ease;
        }

        .upload-container:hover {
            box-shadow: 0 15px 50px rgba(0, 0, 0, 0.1);
        }

        .drop-zone {
            border: 3px dashed var(--border-color);
            border-radius: 16px;
            padding: 60px 40px;
            text-align: center;
            transition: all 0.3s ease;
            background: var(--light-bg);
            position: relative;
            cursor: pointer;
        }

        .drop-zone:hover {
            border-color: var(--accent-color);
            background: #f0fdf4;
            transform: scale(1.01);
        }

        .drop-zone.dragover {
            border-color: var(--main-color);
            background: #fef2f2;
            transform: scale(1.02);
        }

        .drop-zone i {
            font-size: 64px;
            color: var(--accent-color);
            margin-bottom: 20px;
            display: block;
            transition: all 0.3s ease;
        }

        .drop-zone:hover i {
            transform: translateY(-5px) scale(1.05);
        }

        .drop-zone h3 {
            font-size: 22px;
            color: var(--seondary-color);
            margin-bottom: 10px;
        }

        .drop-zone p {
            color: #718096;
            font-size: 15px;
            margin-bottom: 20px;
        }

        .drop-zone .or-divider {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 20px;
            margin: 20px 0;
            color: #a0aec0;
            font-size: 13px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .drop-zone .or-divider::before,
        .drop-zone .or-divider::after {
            content: '';
            flex: 1;
            height: 1px;
            background: var(--border-color);
        }

        .file-input-wrapper {
            position: relative;
            display: inline-block;
        }

        .file-input-wrapper input[type="file"] {
            position: absolute;
            left: 0;
            top: 0;
            opacity: 0;
            width: 100%;
            height: 100%;
            cursor: pointer;
        }

        .btn-select {
            background: var(--main-color);
            color: var(--white);
            border: none;
            padding: 14px 40px;
            border-radius: 50px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 12px;
            position: relative;
            overflow: hidden;
            line-height: 1;
            vertical-align: middle;
        }

        .btn-select:hover {
            background: #660000;
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(120, 0, 0, 0.3);
        }

        .btn-select:active {
            transform: scale(0.95);
        }

        .btn-select i {
            font-size: 18px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            line-height: 1;
            height: 18px;
            width: 18px;
            flex-shrink: 0;
        }

        .file-info {
            margin-top: 20px;
            padding: 15px 20px;
            background: white;
            border-radius: 12px;
            display: none;
            align-items: center;
            justify-content: space-between;
            animation: slideIn 0.4s ease;
            border: 1px solid var(--border-color);
        }

        .file-info.show {
            display: flex;
        }

        .file-info .file-details {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .file-info .file-details i {
            font-size: 28px;
            color: var(--accent-color);
        }

        .file-info .file-details .file-name {
            font-weight: 600;
            color: var(--seondary-color);
        }

        .file-info .file-details .file-size {
            font-size: 13px;
            color: #718096;
        }

        .btn-remove {
            background: none;
            border: none;
            color: #fc8181;
            cursor: pointer;
            font-size: 20px;
            transition: all 0.3s ease;
            padding: 5px 10px;
            border-radius: 8px;
        }

        .btn-remove:hover {
            background: #fff5f5;
            transform: scale(1.1);
        }

        /* Next Button */
        .btn-next-wrapper {
            margin-top: 25px;
            text-align: center;
            display: flex;
            justify-content: center;
        }
        .btn-next-wrapper button{
            display: none;
        }
        .btn-next {
            background: var(--seondary-color);
            color: var(--white);
            border: none;
            padding: 14px 50px;
            border-radius: 50px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            display: inline-flex;
            justify-content: center;
            align-items: center;
            gap: 12px;
            opacity: 0.5;
            pointer-events: none;
        }

        .btn-next.active {
            opacity: 1;
            pointer-events: auto;
        }

        .btn-next.active:hover {
            background: #002538;
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(0, 48, 73, 0.3);
        }

        .btn-next:active {
            transform: scale(0.95);
        }

        .btn-next i {
            font-size: 18px;
        }

        /* Animations */
        @keyframes fadeInDown {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Responsive */
        @media (max-width: 768px) {
            .main-content {
                margin-left: 220px;
                padding: 25px;
            }

            .page-header .title {
                font-size: 28px;
            }

            .upload-container {
                padding: 30px 20px;
            }

            .drop-zone {
                padding: 40px 20px;
            }

            .drop-zone i {
                font-size: 48px;
            }

            .drop-zone h3 {
                font-size: 18px;
            }

            .btn-select, .btn-next {
                padding: 12px 30px;
                font-size: 14px;
            }
        }

        @media (max-width: 480px) {
            .main-content {
                margin-left: 0;
                padding: 20px;
            }

            .page-header .title {
                font-size: 24px;
                flex-wrap: wrap;
            }

            .upload-container {
                padding: 20px 15px;
            }

            .drop-zone {
                padding: 30px 15px;
            }
        }
    </style>
</head>
<body>
    <div class="main-content">
        <!-- Header -->
        <div class="page-header">
            <div class="title">
                <i class="fas fa-file-upload"></i>
                Remplir Sans Doute
            </div>
        </div>

        <!-- Upload Container -->
        <form id="uploadForm">
            <div class="upload-container">
                <div class="drop-zone" id="dropZone">
                    <i class="fas fa-cloud-upload-alt"></i>
                    <h3>Déposez votre document ici</h3>
                    <p>Glissez-déposez votre fichier ou cliquez sur le bouton ci-dessous</p>

                    <div class="or-divider">ou</div>

                    <div class="file-input-wrapper">
                        <button type="button" class="btn-select" onclick="document.getElementById('fileInput').click()">

                            Sélectionner un fichier
                        </button>
                        <input type="file" id="fileInput" name="document[]" multiple accept=".pdf,.doc,.docx,.txt,.xls,.xlsx,.jpg,.png,.jpeg" required>
                    </div>

                    <!-- File Info -->
                    <div class="file-info" id="fileInfo">
                        <div class="file-details">
                            <i class="fas fa-file-pdf"></i>
                            <div>
                                <div class="file-name" id="fileName">document.pdf</div>
                                <div class="file-size" id="fileSize">2.4 MB</div>
                            </div>
                        </div>
                        <button type="button" class="btn-remove" onclick="removeFile()">
                            <i class="fas fa-times" style="font-size: 20px;"></i>
                        </button>
                    </div>

                    <!-- Next Button -->
                    <div class="btn-next-wrapper">
                        <button type="submit" class="btn-next" id="nextBtn" disabled>

                            Suivant
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <script>
        const dropZone = document.getElementById('dropZone');
        const fileInput = document.getElementById('fileInput');
        const fileInfo = document.getElementById('fileInfo');
        const fileName = document.getElementById('fileName');
        const fileSize = document.getElementById('fileSize');
        const nextBtn = document.getElementById('nextBtn');
        const uploadForm = document.getElementById('uploadForm');

        // Prevent default drag behaviors
        ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
            dropZone.addEventListener(eventName, preventDefaults, false);
            document.body.addEventListener(eventName, preventDefaults, false);
        });

        function preventDefaults(e) {
            e.preventDefault();
            e.stopPropagation();
        }

        ['dragenter', 'dragover'].forEach(eventName => {
            dropZone.addEventListener(eventName, () => {
                dropZone.classList.add('dragover');
            });
        });

        ['dragleave', 'drop'].forEach(eventName => {
            dropZone.addEventListener(eventName, () => {
                dropZone.classList.remove('dragover');
            });
        });

        dropZone.addEventListener('drop', handleDrop);
        fileInput.addEventListener('change', handleFileSelect);

        function handleDrop(e) {
            const dt = e.dataTransfer;
            const files = dt.files;
            if (files.length > 0) {
                fileInput.files = files;
                handleFileSelect();
            }
        }

        function handleFileSelect(e) {
            const files = e ? e.target.files : fileInput.files;
            if (files.length > 0) {
                handleFile(files[0]);
            }
        }

        function handleFile(file) {
            // Check file size (max 10MB)
            if (file.size > 10 * 1024 * 1024) {
                alert('Le fichier est trop volumineux. Taille maximale : 10 MB');
                fileInput.value = '';
                nextBtn.disabled = true;
                nextBtn.classList.remove('active');
                return;
            }

            // Display file info
            fileName.textContent = file.name;
            fileSize.textContent = (file.size / 1024 / 1024).toFixed(2) + ' MB';
            fileInfo.classList.add('show');

            // Change icon based on file type
            const fileIcon = fileInfo.querySelector('.file-details i');
            const extension = file.name.split('.').pop().toLowerCase();
            const iconMap = {
                'pdf': 'fa-file-pdf',
                'doc': 'fa-file-word',
                'docx': 'fa-file-word',
                'txt': 'fa-file-alt',
                'xls': 'fa-file-excel',
                'xlsx': 'fa-file-excel',
                'jpg': 'fa-file-image',
                'jpeg': 'fa-file-image',
                'png': 'fa-file-image'
            };
            fileIcon.className = 'fas ' + (iconMap[extension] || 'fa-file');

            // Enable next button
            nextBtn.disabled = false;
            nextBtn.classList.add('active');

            // Change button text
            document.querySelector('.btn-select').innerHTML = 'Fichier sélectionné';
            nextBtn.style.display = "block";
        }

        function removeFile() {
            fileInfo.classList.remove('show');
            fileInput.value = '';
            nextBtn.disabled = true;
            nextBtn.classList.remove('active');
            document.querySelector('.btn-select').innerHTML = 'Sélectionner un fichier';
        }

        // Click on drop zone to open file dialog
        dropZone.addEventListener('click', (e) => {
            if (e.target === dropZone || e.target.closest('.drop-zone') && !e.target.closest('.file-input-wrapper')) {
                fileInput.click();
            }
        });
        uploadForm.addEventListener("submit", async function(e) {

        e.preventDefault();

        if (fileInput.files.length === 0) {
            alert("Veuillez sélectionner une facture.");
            return;
        }

        const formData = new FormData();
        formData.append("invoice", fileInput.files[0]);

        try {

            const response = await fetch(
                "http://127.0.0.1:8000/api/invoices/upload",
                {
                    method: "POST",
                    body: formData
                }
            );

            const data = await response.json();

            console.log(data);
            window.location.href =
                "process_animation.php?id=" + data.invoice_id;

        } catch (error) {

            console.error(error);
            alert("Erreur lors de l'upload.");

        }

    });
        // Auto-submit when next is clicked (already handled by form)
    </script>
</body>
</html>
