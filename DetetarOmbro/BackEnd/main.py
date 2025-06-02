from flask import Flask, Response
from flask_cors import CORS
import cv2
import mediapipe as mp
import numpy as np
import time
import requests
from collections import deque

app = Flask(__name__)
CORS(app)

mp_pose = mp.solutions.pose
pose = mp_pose.Pose()
mp_draw = mp.solutions.drawing_utils

def calculate_angle(a, b, c):
    a, b, c = np.array(a), np.array(b), np.array(c)
    radians = np.arctan2(c[1] - b[1], c[0] - b[0]) - np.arctan2(a[1] - b[1], a[0] - b[0])
    angle = np.abs(radians * 180.0 / np.pi)
    return 360 - angle if angle > 180.0 else angle

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

erro_inicio = None
erros_validos = 0
ERROS_LIMITE = 3
DURACAO_MINIMA_ERRO = 3
historico_erros = deque(maxlen=10)
INTERVALO_MAXIMO = 30

def gen_frames():
    global erro_inicio, erros_validos
    atividade_iniciada = False
    ultimo_movimento = time.time()

    cap = cv2.VideoCapture(0)
    cap.set(cv2.CAP_PROP_FRAME_WIDTH, 1280)
    cap.set(cv2.CAP_PROP_FRAME_HEIGHT, 720)

    while True:
        success, frame = cap.read()
        if not success:
            break

        rgb = cv2.cvtColor(frame, cv2.COLOR_BGR2RGB)
        results = pose.process(rgb)

        status = ""
        color = (255, 255, 255)

        estado_esq = "desconhecido"
        estado_dir = "desconhecido"

        if results.pose_landmarks:
            mp_draw.draw_landmarks(frame, results.pose_landmarks, mp_pose.POSE_CONNECTIONS)
            lm = results.pose_landmarks.landmark

            # Coordenadas
            l_shoulder = [lm[mp_pose.PoseLandmark.LEFT_SHOULDER].x, lm[mp_pose.PoseLandmark.LEFT_SHOULDER].y]
            l_elbow = [lm[mp_pose.PoseLandmark.LEFT_ELBOW].x, lm[mp_pose.PoseLandmark.LEFT_ELBOW].y]
            l_wrist = [lm[mp_pose.PoseLandmark.LEFT_WRIST].x, lm[mp_pose.PoseLandmark.LEFT_WRIST].y]
            l_hip = [lm[mp_pose.PoseLandmark.LEFT_HIP].x, lm[mp_pose.PoseLandmark.LEFT_HIP].y]

            r_shoulder = [lm[mp_pose.PoseLandmark.RIGHT_SHOULDER].x, lm[mp_pose.PoseLandmark.RIGHT_SHOULDER].y]
            r_elbow = [lm[mp_pose.PoseLandmark.RIGHT_ELBOW].x, lm[mp_pose.PoseLandmark.RIGHT_ELBOW].y]
            r_wrist = [lm[mp_pose.PoseLandmark.RIGHT_WRIST].x, lm[mp_pose.PoseLandmark.RIGHT_WRIST].y]
            r_hip = [lm[mp_pose.PoseLandmark.RIGHT_HIP].x, lm[mp_pose.PoseLandmark.RIGHT_HIP].y]

            ang_ombro_esq = calculate_angle(l_hip, l_shoulder, l_elbow)
            ang_cotovelo_esq = calculate_angle(l_shoulder, l_elbow, l_wrist)
            ang_ombro_dir = calculate_angle(r_hip, r_shoulder, r_elbow)
            ang_cotovelo_dir = calculate_angle(r_shoulder, r_elbow, r_wrist)

            estado_esq = lado_estado(ang_ombro_esq, ang_cotovelo_esq)
            estado_dir = lado_estado(ang_ombro_dir, ang_cotovelo_dir)

            # Espera e início
            if not atividade_iniciada:
                if estado_esq == "inicial" and estado_dir == "inicial":
                    atividade_iniciada = True
                    ultimo_movimento = time.time()
                    status = "✅ Novo exercício iniciado"
                    color = (0, 200, 0)
                else:
                    status = "🕒 Em espera: assuma a posição inicial para começar"
                    color = (160, 160, 160)
            else:
                if estado_esq in ["inicial", "subida", "descida", "topo"] or estado_dir in ["inicial", "subida", "descida", "topo"]:
                    ultimo_movimento = time.time()
                elif time.time() - ultimo_movimento > 5:
                    atividade_iniciada = False
                    erros_validos = 0
                    status = "⏸ Exercício terminado — aguardar nova posição inicial"
                    color = (180, 180, 180)

            # Lógica de erro
            if atividade_iniciada:
                erro_detectado = (estado_esq == "erro" or estado_dir == "erro")
                if erro_detectado:
                    if erro_inicio is None:
                        erro_inicio = time.time()
                    else:
                        duracao = time.time() - erro_inicio
                        if duracao >= DURACAO_MINIMA_ERRO:
                            agora = time.time()
                            while historico_erros and agora - historico_erros[0] > INTERVALO_MAXIMO:
                                historico_erros.popleft()

                            if len(historico_erros) < 3:
                                erros_validos += 1
                                erro_inicio = None
                                historico_erros.append(agora)

                                payload = {
                                    'error_type': 'Execução Incorreta',
                                    'details': f'Esq: {estado_esq}, Dir: {estado_dir}'
                                }
                                try:
                                    requests.post(
                                        'http://localhost/DetetarOmbro/FrontEnd/api_guardar_erro.php',
                                        data=payload,
                                        timeout=2
                                    )
                                except Exception as e:
                                    print("Erro ao enviar alerta:", e)
                            else:
                                erro_inicio = None
                else:
                    erro_inicio = None

                if erros_validos >= ERROS_LIMITE:
                    status = "🚨 Notificação ao PT — execução incorreta repetida"
                    color = (0, 0, 255)
                    erros_validos = 0
                elif estado_esq == estado_dir:
                    if estado_esq == "inicial":
                        status = "✅ Posição Inicial Correta"
                        color = (0, 255, 0)
                    elif estado_esq == "subida":
                        status = "↕️ Subida em curso — OK"
                        color = (0, 255, 255)
                    elif estado_esq == "descida":
                        status = "⬇️ Descida controlada — OK"
                        color = (255, 200, 0)
                    elif estado_esq == "topo":
                        status = "✅ Topo do Movimento"
                        color = (0, 255, 0)
                    else:
                        status = "❌ Execução incorreta em ambos os lados"
                        color = (0, 0, 255)
                else:
                    status = f"⚠️ Execução desigual: Esq={estado_esq} / Dir={estado_dir}"
                    color = (0, 165, 255)

            # Overlay
            cv2.putText(frame, f'Esq: Ombro={int(ang_ombro_esq)}°, Cotovelo={int(ang_cotovelo_esq)}°', (10, 40),
                        cv2.FONT_HERSHEY_SIMPLEX, 0.7, color, 2)
            cv2.putText(frame, f'Dir: Ombro={int(ang_ombro_dir)}°, Cotovelo={int(ang_cotovelo_dir)}°', (10, 70),
                        cv2.FONT_HERSHEY_SIMPLEX, 0.7, color, 2)
            cv2.putText(frame, status, (10, 110),
                        cv2.FONT_HERSHEY_SIMPLEX, 0.95, color, 2)

        ret, buffer = cv2.imencode('.jpg', frame)
        frame = buffer.tobytes()

        yield (b'--frame\r\n'
               b'Content-Type: image/jpeg\r\n\r\n' + frame + b'\r\n')

@app.route('/video_feed')
def video_feed():
    return Response(gen_frames(), mimetype='multipart/x-mixed-replace; boundary=frame')

if __name__ == '__main__':
    app.run(debug=True)
