<!DOCTYPE html>
<html lang="pt">
<head>
  <meta charset="UTF-8">
  <title>Câmara ao Vivo</title>
  <style>
    body {
      margin: 0;
      background: #000;
      display: flex;
      justify-content: center;
      align-items: center;
      height: 100vh;
    }
    img {
      width: 100%;
      max-width: 960px;
      height: auto;
      aspect-ratio: 16/9;
      border-radius: 16px;
      border: 4px solid #255C99;
      box-shadow: 0 6px 28px 0 #23272b22;
    }
  </style>
</head>
<body>
  <img src="http://127.0.0.1:5000/video_feed" alt="Câmara ao vivo" onerror="this.style.display='none'; document.body.innerHTML += '<p style=\'color:white;\'>⚠️ Erro: Câmara não encontrada</p>';">
</body>
</html>
