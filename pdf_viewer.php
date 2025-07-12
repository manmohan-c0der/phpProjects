<?php
$uploadDir = "uploads/";
if (!is_dir($uploadDir)) {
    mkdir($uploadDir);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['pdfFile'])) {
    $file = $_FILES['pdfFile'];
    $fileName = basename($file['name']);
    $targetPath = $uploadDir . $fileName;

    if (move_uploaded_file($file['tmp_name'], $targetPath)) {
        echo json_encode(['status' => 'success', 'path' => $targetPath]);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Upload failed.']);
    }
    exit;
}

$pdfFiles = array_diff(scandir($uploadDir), ['.', '..']);
$latestPDF = end($pdfFiles);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>PDF Viewer</title>
  <style>
    body {
      font-family: 'Segoe UI', sans-serif;
      background: #f0f0f0;
      padding: 30px;
      display: flex;
      flex-direction: column;
      align-items: center;
    }
    .container {
      background: #fff;
      padding: 20px 30px;
      border-radius: 10px;
      box-shadow: 0 5px 15px rgba(0,0,0,0.1);
      width: 600px;
      text-align: center;
    }
    input[type="file"] {
      margin: 15px 0;
    }
    iframe {
      width: 100%;
      height: 500px;
      border: 1px solid #ccc;
      border-radius: 8px;
      margin-top: 20px;
    }
    .btn {
      padding: 10px 20px;
      background: #007BFF;
      color: white;
      border: none;
      border-radius: 5px;
      cursor: pointer;
    }
    .btn:hover {
      background: #0056b3;
    }
  </style>
</head>
<body>
  <div class="container">
    <h2>📄 PDF Viewer</h2>
    <form id="pdfForm" enctype="multipart/form-data">
      <input type="file" name="pdfFile" accept="application/pdf" required>
      <br>
      <button type="submit" class="btn">Upload & View</button>
    </form>

    <div id="viewer">
      <?php if ($latestPDF): ?>
        <iframe src="<?= $uploadDir . $latestPDF ?>"></iframe>
      <?php else: ?>
        <p>No PDF uploaded yet.</p>
      <?php endif; ?>
    </div>
  </div>

  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script>
    $('#pdfForm').on('submit', function(e) {
      e.preventDefault();
      var formData = new FormData(this);

      $.ajax({
        url: '',
        type: 'POST',
        data: formData,
        contentType: false,
        processData: false,
        success: function(resp) {
          const res = JSON.parse(resp);
          if (res.status === 'success') {
            $('#viewer').html('<iframe src="' + res.path + '"></iframe>');
          } else {
            alert(res.message || "Upload failed.");
          }
        }
      });
    });
  </script>
</body>
</html>
