<!-- templates/scan.html -->
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Scan QR</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body class="bg-light">
  <div class="container py-5 text-center">
    <h3>Scan QR Code Absensi</h3>
    <p class="text-muted">Upload gambar QR Code yang diberikan dosen</p>

    <form method="POST" enctype="multipart/form-data">
      <input type="file" name="qr_image" accept="image/*" required class="form-control mb-3">
      <button type="submit" class="btn btn-success">Upload & Scan</button>
    </form>

    {% if result %}
      <div class="alert alert-info mt-4">
        <strong>Hasil:</strong> {{ result }}
      </div>
    {% endif %}

    <a href="{{ url_for('dashboard') }}" class="btn btn-secondary mt-4">Kembali ke Dashboard</a>
  </div>
</body>
</html>
