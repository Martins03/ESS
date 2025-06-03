<!DOCTYPE html>
<html lang="pt">
<head>
  <meta charset="UTF-8">
  <title>Câmara ao Vivo</title>
  <link rel="icon" type="image/png" href="css/logo.png">
  <link rel="stylesheet" href="css/style.css">
</head>
<body class="camera-bg">
  <img src="http://127.0.0.1:5000/video_feed" alt="Câmara ao vivo"
    onerror="this.style.display='none'; document.body.innerHTML += '<p style=\'color:white;\'>⚠️ Erro: Câmara não encontrada</p>';"/>
</body>

</html>
