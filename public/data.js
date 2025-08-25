// Global data storage
let dashboardData = {
    shops: [],
    metrics: []
};

let filesUploaded = {
    shops: false,
    metrics: false
};

const button = document.getElementById("submit-button");


// Initialize file upload handlers
function setupFileUploads() {
    const shopsFile = document.getElementById('shops-file');
    const metricsFile = document.getElementById('metrics-file');
    const shopsUpload = document.getElementById('shops-upload');
    const metricsUpload = document.getElementById('metrics-upload');

    // File change event listeners
    shopsFile.addEventListener('change', function (e) {
        handleFileUpload(e.target.files[0], 'shops');
    });

    metricsFile.addEventListener('change', function (e) {
        handleFileUpload(e.target.files[0], 'metrics');
    });

    // Setup drag and drop
    setupDragAndDrop(shopsUpload, 'shops');
    setupDragAndDrop(metricsUpload, 'metrics');
}

function setupDragAndDrop(uploadDiv, type) {
    uploadDiv.addEventListener('dragover', function (e) {
        e.preventDefault();
        e.stopPropagation();
        uploadDiv.classList.add('drag-over');
    });

    uploadDiv.addEventListener('dragleave', function (e) {
        e.preventDefault();
        e.stopPropagation();
        uploadDiv.classList.remove('drag-over');
    });

    uploadDiv.addEventListener('drop', function (e) {
        e.preventDefault();
        e.stopPropagation();
        uploadDiv.classList.remove('drag-over');

        const files = e.dataTransfer.files;
        if (files.length > 0) {
            const fileInput = document.getElementById(`${type}-file`);
            fileInput.files = files;
            handleFileUpload(files[0], type);
        }
    });
}

function handleFileUpload(file, type) {
    if (!file) return;

    const statusElement = document.getElementById(`${type}-status`);
    const uploadElement = document.getElementById(`${type}-upload`);
    const progressIndicator = document.getElementById('progress-indicator');
    const progressFill = document.getElementById('progress-fill');

    const fileInput = document.getElementById(`${type}-file`);

    // Check if elements exist
    if (!statusElement || !uploadElement) {
        console.error(`Upload elements not found for type: ${type}`);
        return;
    }

    // Show progress
    if (progressIndicator && progressFill) {
        progressIndicator.style.display = 'block';
        progressFill.style.width = '10%';
    }

    // Validate file type
    if (!file.name.toLowerCase().endsWith('.json')) {
        statusElement.textContent = '❌ Please select a JSON file';
        statusElement.className = 'file-status error';
        if (progressIndicator) progressIndicator.style.display = 'none';
        return;
    }

    statusElement.textContent = 'Reading file...';
    statusElement.className = 'file-status loading';
    if (progressFill) progressFill.style.width = '30%';

    const reader = new FileReader();
    reader.onload = function (e) {
        try {
            if (progressFill) progressFill.style.width = '60%';
            const data = JSON.parse(e.target.result);

            // Validate data structure
            if (type === 'shops') {
                validateShopsData(data);
                dashboardData.shops = data;
            } else if (type === 'metrics') {
                validateMetricsData(data);
                dashboardData.metrics = data;
            }

            if (progressFill) progressFill.style.width = '100%';
            filesUploaded[type] = true;
            statusElement.textContent = `✅ ${file.name} loaded successfully (${data.length} records)`;
            statusElement.className = 'file-status success';
            uploadElement.classList.add('uploaded');

            if (progressIndicator) {
                setTimeout(() => {
                    progressIndicator.style.display = 'none';
                }, 1000);
            }

            // Use setTimeout to ensure DOM is ready
            setTimeout(() => {
                checkAllFilesUploaded();
            }, 100);

        } catch (error) {
            if (progressFill) progressFill.style.width = '0%';
            statusElement.textContent = `❌ Error: ${error.message}`;
            statusElement.className = 'file-status error';
            uploadElement.classList.remove('uploaded');
            filesUploaded[type] = false;

            // ❌ Validation failed → clear the input so it won’t be submitted
            fileInput.value = "";
            filesUploaded[type] = false;

            if (progressIndicator) {
                setTimeout(() => {
                    progressIndicator.style.display = 'none';
                }, 2000);
            }
        }
    };

    reader.onerror = function () {
        statusElement.textContent = '❌ Error reading file';
        statusElement.className = 'file-status error';
        if (progressIndicator) progressIndicator.style.display = 'none';
        filesUploaded[type] = false;
    };

    reader.readAsText(file);
}

function validateShopsData(data) {
    if (!Array.isArray(data)) {
        throw new Error('Shops data must be an array');
    }
    if (data.length === 0) {
        throw new Error('Shops file is empty');
    }
    for (let i = 0; i < data.length; i++) {
        const shop = data[i];
        if (!shop.shop_id || !shop.shop_name) {
            throw new Error(`Shop at index ${i} is missing required fields (shop_id, shop_name)`);
        }
    }

    button.classList.add("load-demo-data");
    button.disabled = false;

}

function validateMetricsData(data) {
    if (!Array.isArray(data)) {
        throw new Error('Metrics data must be an array');
    }
    if (data.length === 0) {
        throw new Error('Metrics file is empty');
    }
    for (let i = 0; i < data.length; i++) {
        const metric = data[i];
        if (!metric.shop_id || !metric.date ||
            typeof metric.gmv_usd !== 'number' ||
            typeof metric.followers !== 'number' ||
            typeof metric.ctor !== 'number') {
            throw new Error(`Metric at index ${i} is missing or has invalid fields`);
        }
    }

    button.classList.add("load-demo-data");
    button.disabled = false;
}

function checkAllFilesUploaded() {
    const initButton = document.getElementById('init-dashboard');

    // Check if button exists before trying to modify it
    if (!initButton) {
        console.warn('Initialize button not found in DOM');
        return;
    }

    if (filesUploaded.shops && filesUploaded.metrics) {
        initButton.disabled = false;
        initButton.textContent = '📊 Initialize Dashboard';
    } else {
        initButton.disabled = true;
        const missing = [];
        if (!filesUploaded.shops) missing.push('shops.json');
        if (!filesUploaded.metrics) missing.push('metrics.json');
        initButton.textContent = `Upload ${missing.join(' & ')} first`;
    }
}
// Initialize when page loads
document.addEventListener('DOMContentLoaded', function () {
    setupFileUploads();
});
