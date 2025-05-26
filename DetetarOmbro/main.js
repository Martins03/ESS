import {
    PoseLandmarker,
    FilesetResolver,
    DrawingUtils
  } from '@mediapipe/tasks-vision';
  
  const video = document.getElementById('video');
  const canvas = document.getElementById('canvas');
  const ctx = canvas.getContext('2d');
  const drawingUtils = new DrawingUtils(ctx);
  
  async function setupCamera() {
    const stream = await navigator.mediaDevices.getUserMedia({ video: true });
    video.srcObject = stream;
    return new Promise(resolve => {
      video.onloadedmetadata = () => resolve(video);
    });
  }
  
  async function main() {
    await setupCamera();
    video.play();
  
    const filesetResolver = await FilesetResolver.forVisionTasks('./node_modules/@mediapipe/tasks-vision/wasm');
    const landmarker = await PoseLandmarker.createFromOptions(filesetResolver, {
      baseOptions: {
        modelAssetPath: './pose_landmarker_lite.task',
        delegate: 'GPU'
      },
      runningMode: 'VIDEO',
      numPoses: 1
    });
  
    function detectPose() {
      ctx.clearRect(0, 0, canvas.width, canvas.height);
      ctx.drawImage(video, 0, 0, canvas.width, canvas.height);
  
      landmarker.detectForVideo(video, performance.now(), (result) => {
        if (result.landmarks.length > 0) {
          drawingUtils.drawLandmarks(result.landmarks[0]);
        }
      });
  
      requestAnimationFrame(detectPose);
    }
  
    detectPose();
  }
  
  main();
  