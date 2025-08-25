<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Upload Interface</title>
    <link rel="stylesheet" href="data.css">
</head>

<body>
    <div class="container">
        <div class="header">
            <a href="{{ route('home') }}" style="text-decoration: none">
                <h1>The Geeky Greens</h1>
            </a>
            <p>Partner Performance Dashboard - Data Upload</p>
        </div>
        <form action="{{ route('upload') }}" method="post" enctype="multipart/form-data">
            @csrf
            <div class="upload-section">
                <h2>📊 Upload Data Files</h2>
                <p>Upload your shops.json and metrics.json files to get started, or use the demo data</p>

                <div class="upload-container">
                    <div class="file-upload" id="shops-upload">
                        <div class="file-upload-icon">📁</div>
                        <h3>Shops Data</h3>
                        <p>Upload shops.json file<br><small>Contains shop information and regions</small></p>
                        <input type="file" id="shops-file" accept=".json" name="shops_file">
                        <button type="button" class="upload-button"
                            onclick="document.getElementById('shops-file').click()">
                            Choose File
                        </button>
                        <div class="file-status" id="shops-status"></div>
                    </div>

                    <div class="file-upload" id="metrics-upload">
                        <div class="file-upload-icon">📈</div>
                        <h3>Metrics Data</h3>
                        <p>Upload metrics.json file<br><small>Contains GMV, followers, and CTOR data</small></p>
                        <input type="file" id="metrics-file" accept=".json" name="metrics_file">
                        <button type="button" class="upload-button"
                            onclick="document.getElementById('metrics-file').click()">
                            Choose File
                        </button>
                        <div class="file-status" id="metrics-status"></div>
                    </div>
                </div>

                <div class="progress-indicator" id="progress-indicator" style="display: none;">
                    <div class="progress-text">Upload Progress</div>
                    <div class="progress-bar">
                        <div class="progress-fill" id="progress-fill" style="width: 0%;"></div>
                    </div>
                </div>

                <div class="action-buttons">
                    <button type="submit" disabled id="submit-button">
                        🚀 Upload
                    </button>
                </div>

                <div class="sample-data">
                    <h4>Expected File Formats:</h4>
                    <div style="margin-bottom: 15px;">
                        <strong>shops.json:</strong>
                        <pre>[{"shop_id":"s1","shop_name":"Shop Alpha","region":"US"}]</pre>
                    </div>
                    <div>
                        <strong>metrics.json:</strong>
                        <pre>[{"shop_id":"s1","date":"2025-07-20","gmv_usd":1200,"followers":400,"ctor":0.12}]</pre>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <script src="data.js"></script>
</body>

</html>
