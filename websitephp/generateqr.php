<!-- templates/generate.html -->
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Generate QR</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body class="bg-light">
  <div class="container py-5 text-center">
    <h3>Generate QR Code Absensi</h3>
    <form method="POST" class="my-4">
      <div class="mb-3">
        <input type="text" name="text" class="form-control" placeholder="Masukkan teks absensi (contoh: Absen PTI 2025)" required>
      </div>
      <button type="submit" class="btn btn-primary">Generate</button>
    </form>

    {% if qr_path %}
      <div class="mt-4">
        <h5>Hasil QR Code:</h5>
        <img src="{{ url_for('static', filename='qr.png') }}" alt="QR Code" class="img-fluid mt-3" width="200">
      </div>
    {% endif %}

    <a href="{{ url_for('dashboard') }}" class="btn btn-secondary mt-4">Kembali ke Dashboard</a>
  </div>
</body>
</html>
