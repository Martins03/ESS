from flask import Flask, Response
from flask_cors import CORS
import cv2
import mediapipe as mp
import numpy as np
import time
import requests
from collections import deque
import os
from datetime import datetime
import subprocess
import unicodedata

# Inicializo a app Flask para servir o vídeo na web
app = Flask(__name__)
CORS(app)  # Permito acessos CORS (por exemplo, para aceder do frontend)

# Configuro o MediaPipe para análise de postura
mp_pose = mp.solutions.pose
pose = mp_pose.Pose()
mp_draw = mp.solutions.drawing_utils

# Função para calcular o ângulo entre três pontos (por exemplo, para o ombro e cotovelo)
def calculate_angle(a, b, c):
    a, b, c = np.array(a), np.array(b), np.array(c)
    radians = np.arctan2(c[1] - b[1], c[0] - b[0]) - np.arctan2(a[1] - b[1], a[0] - b[1])
    angle = np.abs(radians * 180.0 / np.pi)
    return 360 - angle if angle > 180.0 else angle

# Com base nos ângulos, determino o estado do braço (posição inicial, subida, topo, descida ou erro)
def lado_estado(ang_ombro, ang_cotovelo):
    if 85 <= ang_ombro <= 120 and 85 <= ang_cotovelo <= 120:
        return "inicial"
    elif 120 < ang_ombro < 170 and 120 < ang_cotovelo < 160:
        return "subida"
    elif ang_ombro >= 170 and ang_cotovelo >= 160:
        return "topo"
    elif 120 < ang_ombro < 170 and ang_cotovelo >= 160:
        return "descida"
    else:
        return "erro"

# Função para remover acentos e caracteres não-ASCII (OpenCV não lida bem com acentos)
def remove_acentos(text):
    return ''.join(
        c for c in unicodedata.normalize('NFD', str(text))
        if unicodedata.category(c) != 'Mn' and ord(c) < 128
    )

# Variáveis para controlo dos erros, buffers e configurações
erro_inicio = None
erros_validos = 0
ERROS_LIMITE = 3
DURACAO_MINIMA_ERRO = 3  # segundos a detetar erro antes de considerar
historico_erros = deque(maxlen=10)
INTERVALO_MAXIMO = 30    # segundos entre erros
videos_dir = "../FrontEnd/videos"  # Onde guardo os vídeos dos erros
os.makedirs(videos_dir, exist_ok=True)

# Defino os parâmetros do buffer de frames (guardar vídeo antes e depois do erro)
FPS = 20
SEGUNDOS_ANTES = 4
SEGUNDOS_DEPOIS = 4
BUFFER_SIZE = FPS * SEGUNDOS_ANTES

# Função principal que lê da webcam, analisa os movimentos e serve as imagens via HTTP
def gen_frames():
    global erro_inicio, erros_validos
    atividade_iniciada = False  # Flag para saber se a pessoa já começou o exercício
    ultimo_movimento = time.time()

    cap = cv2.VideoCapture(0)
    cap.set(cv2.CAP_PROP_FRAME_WIDTH, 1280)
    cap.set(cv2.CAP_PROP_FRAME_HEIGHT, 720)

    buffer_frames = deque(maxlen=BUFFER_SIZE)  # Buffer circular para guardar os frames antes do erro

    while True:
        success, frame = cap.read()
        if not success:
            break

        buffer_frames.append(frame.copy())  # Guardar sempre o frame mais recente

        rgb = cv2.cvtColor(frame, cv2.COLOR_BGR2RGB)
        results = pose.process(rgb)

        status = ""
        color = (255, 255, 255)

        estado_esq = "desconhecido"
        estado_dir = "desconhecido"

        # Só continuo se o MediaPipe encontrou landmarks (detetou corpo)
        if results.pose_landmarks:
            mp_draw.draw_landmarks(frame, results.pose_landmarks, mp_pose.POSE_CONNECTIONS)
            lm = results.pose_landmarks.landmark

            # Recolho as coordenadas dos principais pontos dos braços esquerdo e direito
            l_shoulder = [lm[mp_pose.PoseLandmark.LEFT_SHOULDER].x, lm[mp_pose.PoseLandmark.LEFT_SHOULDER].y]
            l_elbow = [lm[mp_pose.PoseLandmark.LEFT_ELBOW].x, lm[mp_pose.PoseLandmark.LEFT_ELBOW].y]
            l_wrist = [lm[mp_pose.PoseLandmark.LEFT_WRIST].x, lm[mp_pose.PoseLandmark.LEFT_WRIST].y]
            l_hip = [lm[mp_pose.PoseLandmark.LEFT_HIP].x, lm[mp_pose.PoseLandmark.LEFT_HIP].y]

            r_shoulder = [lm[mp_pose.PoseLandmark.RIGHT_SHOULDER].x, lm[mp_pose.PoseLandmark.RIGHT_SHOULDER].y]
            r_elbow = [lm[mp_pose.PoseLandmark.RIGHT_ELBOW].x, lm[mp_pose.PoseLandmark.RIGHT_ELBOW].y]
            r_wrist = [lm[mp_pose.PoseLandmark.RIGHT_WRIST].x, lm[mp_pose.PoseLandmark.RIGHT_WRIST].y]
            r_hip = [lm[mp_pose.PoseLandmark.RIGHT_HIP].x, lm[mp_pose.PoseLandmark.RIGHT_HIP].y]

            # Calculo os ângulos de ombro e cotovelo para ambos os lados
            ang_ombro_esq = calculate_angle(l_hip, l_shoulder, l_elbow)
            ang_cotovelo_esq = calculate_angle(l_shoulder, l_elbow, l_wrist)
            ang_ombro_dir = calculate_angle(r_hip, r_shoulder, r_elbow)
            ang_cotovelo_dir = calculate_angle(r_shoulder, r_elbow, r_wrist)

            # Determino o estado de cada braço (inicial, subida, topo, descida, erro)
            estado_esq = lado_estado(ang_ombro_esq, ang_cotovelo_esq)
            estado_dir = lado_estado(ang_ombro_dir, ang_cotovelo_dir)

            # Parte de lógica para o início do exercício
            if not atividade_iniciada:
                if estado_esq == "inicial" and estado_dir == "inicial":
                    atividade_iniciada = True
                    ultimo_movimento = time.time()
                    status = "Novo exercicio iniciado"
                    color = (0, 200, 0)
                else:
                    status = "Em espera: assuma a posicao inicial para comecar"
                    color = (160, 160, 160)
            else:
                # Se estiver a fazer o exercício corretamente, atualizo o tempo do último movimento
                if estado_esq in ["inicial", "subida", "descida", "topo"] or estado_dir in ["inicial", "subida", "descida", "topo"]:
                    ultimo_movimento = time.time()
                # Se ficar parado mais de 5s, considero o exercício terminado
                elif time.time() - ultimo_movimento > 5:
                    atividade_iniciada = False
                    erros_validos = 0
                    status = "Exercicio terminado — aguardar nova posicao inicial"
                    color = (180, 180, 180)

            # Detetar erros durante o exercício
            if atividade_iniciada:
                erro_detectado = (estado_esq == "erro" or estado_dir == "erro")
                if erro_detectado:
                    if erro_inicio is None:
                        erro_inicio = time.time()
                    else:
                        duracao = time.time() - erro_inicio
                        if duracao >= DURACAO_MINIMA_ERRO:
                            agora = time.time()
                            # Limpo o histórico de erros antigos
                            while historico_erros and agora - historico_erros[0] > INTERVALO_MAXIMO:
                                historico_erros.popleft()

                            if len(historico_erros) < 3:
                                erros_validos += 1
                                erro_inicio = None
                                historico_erros.append(agora)

                                # Aqui gravo o vídeo do erro (frames antes e depois)
                                nome_base = f"erro_{datetime.now().strftime('%Y%m%d_%H%M%S')}"
                                nome_video = f"{nome_base}.mp4"
                                path_video = os.path.join(videos_dir, nome_video)
                                fourcc = cv2.VideoWriter_fourcc(*'mp4v')
                                out = cv2.VideoWriter(path_video, fourcc, FPS, (1280, 720))

                                print(f"[INFO] A gravar video do erro (buffer + pos-erro): {nome_video}")

                                # 1. Gravo o buffer dos frames antes do erro
                                for bf in buffer_frames:
                                    bf = cv2.resize(bf, (1280, 720))
                                    out.write(bf)

                                # 2. Gravo mais alguns frames após o erro
                                for _ in range(FPS * SEGUNDOS_DEPOIS):
                                    ret, frame_err = cap.read()
                                    if not ret:
                                        break
                                    frame_err = cv2.resize(frame_err, (1280, 720))
                                    out.write(frame_err)
                                out.release()
                                print(f"[INFO] Guardado em {path_video}")

                                # Converto para H.264 (compatibilidade e qualidade)
                                nome_convertido = f"{nome_base}_h264.mp4"
                                path_convertido = os.path.join(videos_dir, nome_convertido)
                                subprocess.run([
                                    "ffmpeg", "-y", "-i", path_video,
                                    "-vcodec", "libx264", "-acodec", "aac", path_convertido
                                ])
                                print(f"[INFO] Video convertido para H.264: {path_convertido}")

                                # Limpo o buffer
                                buffer_frames.clear()

                                # Envio alerta para o backend com o vídeo do erro
                                payload = {
                                    'error_type': 'Execucao Incorreta',
                                    'details': f'Esq: {estado_esq}, Dir: {estado_dir}',
                                    'filename': f'videos/{nome_convertido}'
                                }
                                try:
                                    requests.post(
                                        'http://localhost/DetetarOmbro/FrontEnd/api_guardar_erro.php',
                                        data=payload,
                                        timeout=8
                                    )
                                except Exception as e:
                                    print("Erro ao enviar alerta:", e)
                            else:
                                erro_inicio = None
                else:
                    erro_inicio = None

                # Se chegar ao limite de erros, envio notificação especial
                if erros_validos >= ERROS_LIMITE:
                    status = "Notificacao ao PT — execucao incorreta repetida"
                    color = (0, 0, 255)
                    erros_validos = 0
                # Mensagens diferentes conforme o estado detetado
                elif estado_esq == estado_dir:
                    if estado_esq == "inicial":
                        status = "Posicao Inicial Correta"
                        color = (0, 255, 0)
                    elif estado_esq == "subida":
                        status = "Subida em curso — OK"
                        color = (0, 255, 255)
                    elif estado_esq == "descida":
                        status = "Descida controlada — OK"
                        color = (255, 200, 0)
                    elif estado_esq == "topo":
                        status = "Topo do Movimento"
                        color = (0, 255, 0)
                    else:
                        status = "Execucao incorreta em ambos os lados"
                        color = (0, 0, 255)
                else:
                    status = f"Execucao desigual: Esq={estado_esq} / Dir={estado_dir}"
                    color = (0, 165, 255)

            # Escrevo as informações no vídeo (sem acentos para evitar "??")
            texto_esq = f'Esq: Ombro={int(ang_ombro_esq)}, Cotovelo={int(ang_cotovelo_esq)}'
            texto_dir = f'Dir: Ombro={int(ang_ombro_dir)}, Cotovelo={int(ang_cotovelo_dir)}'

            cv2.putText(frame, remove_acentos(texto_esq), (10, 40),
                        cv2.FONT_HERSHEY_SIMPLEX, 0.7, color, 2)
            cv2.putText(frame, remove_acentos(texto_dir), (10, 70),
                        cv2.FONT_HERSHEY_SIMPLEX, 0.7, color, 2)
            cv2.putText(frame, remove_acentos(status), (10, 110),
                        cv2.FONT_HERSHEY_SIMPLEX, 0.95, color, 2)

        # Codifico o frame para JPEG e devolvo na stream HTTP
        ret, buffer = cv2.imencode('.jpg', frame)
        frame = buffer.tobytes()

        yield (b'--frame\r\n'
               b'Content-Type: image/jpeg\r\n\r\n' + frame + b'\r\n')

# Endpoint Flask que serve o vídeo ao vivo (para usar no Frontend)
@app.route('/video_feed')
def video_feed():
    return Response(gen_frames(), mimetype='multipart/x-mixed-replace; boundary=frame')

# Inicio a aplicação Flask
if __name__ == '__main__':
    app.run(debug=True)
