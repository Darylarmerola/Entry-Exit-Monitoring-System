@extends('layouts.app')
@section('title','Scan QR')
@section('content')
<div class="row justify-content-center"><div class="col-lg-7"><div class="card p-4 text-center"><h2 class="fw-bold">Scan attendance QR</h2><p class="text-secondary">Allow camera access and point it at your teacher's attendance QR code.</p><div id="reader" class="mx-auto"></div><div class="camera-controls mt-3 d-flex flex-wrap gap-2 justify-content-center"><button type="button" id="start-camera" class="btn btn-primary">Start camera</button><button type="button" id="switch-camera" class="btn btn-outline-primary" disabled>Switch camera</button><button type="button" id="stop-camera" class="btn btn-outline-secondary" disabled>Stop camera</button></div><div id="scan-result" class="alert alert-info mt-3">Camera ready when you start scanning.</div><div id="attendance-actions" class="mt-3" hidden><p id="time-in-detail" class="text-success fw-semibold mb-2"></p><button type="button" id="time-out-button" class="btn btn-warning btn-lg">Time out</button></div><a href="{{ route('student.dashboard') }}" class="btn btn-outline-secondary mt-2">Back to dashboard</a></div></div></div>
@endsection
@push('scripts')
<script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>
<script>
const resultBox = document.getElementById('scan-result');
const initialToken = @json($token);
const startButton = document.getElementById('start-camera');
const switchButton = document.getElementById('switch-camera');
const stopButton = document.getElementById('stop-camera');
const attendanceActions = document.getElementById('attendance-actions');
const timeInDetail = document.getElementById('time-in-detail');
const timeOutButton = document.getElementById('time-out-button');
let currentToken = initialToken;
let scanner = null;
let cameras = [];
let cameraIndex = 0;
let running = false;
let submitting = false;

function showMessage(message, type = 'info') {
    resultBox.className = `alert alert-${type} mt-3`;
    resultBox.textContent = message;
}
function showAttendanceState(data) {
    attendanceActions.hidden = !data.can_time_out;
    timeOutButton.disabled = false;
    timeInDetail.textContent = data.time_in ? `Time-in recorded (Philippine time): ${data.time_in}` : '';
    if (data.status === 'completed') {
        attendanceActions.hidden = true;
        if (data.time_out) timeInDetail.textContent += ` · Time-out: ${data.time_out}`;
    }
}
function decoded(text) {
    try { const url = new URL(text); return url.searchParams.get('token') || text.trim(); }
    catch (_) { return text.trim(); }
}
async function submitToken(token) {
    if (submitting || !token) return;
    submitting = true;
    currentToken = token;
    showMessage('Submitting time-in...', 'info');
    try {
        const response = await fetch(@json(route('student.attendance.scan')), {
            method: 'POST',
            headers: {'Content-Type':'application/json','Accept':'application/json','X-CSRF-TOKEN':document.querySelector('meta[name="csrf-token"]').content},
            body: JSON.stringify({token})
        });
        const data = await response.json();
        showMessage(data.message || (response.ok ? 'Attendance recorded.' : 'Unable to record attendance.'), response.ok ? 'success' : 'danger');
        if (response.ok) showAttendanceState(data);
        else attendanceActions.hidden = true;
        await stopCamera();
    } catch (_) {
        showMessage('Unable to submit attendance. Check your connection and try again.', 'danger');
    } finally { submitting = false; }
}
async function submitTimeOut() {
    if (!currentToken || timeOutButton.disabled) return;
    timeOutButton.disabled = true;
    showMessage('Recording your time-out...', 'info');
    try {
        const response = await fetch(@json(route('student.attendance.time-out')), {
            method: 'POST',
            headers: {'Content-Type':'application/json','Accept':'application/json','X-CSRF-TOKEN':document.querySelector('meta[name="csrf-token"]').content},
            body: JSON.stringify({token: currentToken})
        });
        const data = await response.json();
        showMessage(data.message || (response.ok ? 'Time-out recorded.' : 'Unable to record time-out.'), response.ok ? 'success' : 'danger');
        if (response.ok) showAttendanceState(data);
        else timeOutButton.disabled = false;
    } catch (_) {
        showMessage('Unable to record time-out. Check your connection and try again.', 'danger');
        timeOutButton.disabled = false;
    }
}
async function stopCamera() {
    if (scanner && running) {
        try { await scanner.stop(); } catch (_) {}
        running = false;
    }
    startButton.disabled = false;
    stopButton.disabled = true;
    switchButton.disabled = cameras.length < 2;
}
async function startCamera(index = cameraIndex) {
    if (!window.isSecureContext && location.hostname !== 'localhost' && location.hostname !== '127.0.0.1') {
        showMessage('Camera access requires HTTPS. Open the secure https:// address (for example, your ngrok HTTPS URL).', 'danger');
        return;
    }
    try {
        if (!scanner) scanner = new Html5Qrcode('reader', {verbose: false});
        cameraIndex = index;
        const camera = cameras[cameraIndex];
        const config = {fps: 12, qrbox: (viewWidth, viewHeight) => {
            const edge = Math.floor(Math.min(viewWidth, viewHeight, 280));
            return {width: edge, height: edge};
        }, aspectRatio: 1.0};
        await scanner.start(camera ? camera.id : {facingMode: 'environment'}, config,
            async text => { await stopCamera(); await submitToken(decoded(text)); },
            () => {});
        running = true;
        startButton.disabled = true;
        stopButton.disabled = false;
        switchButton.disabled = cameras.length < 2;
        showMessage(`Camera active: ${camera?.label || 'back camera'}. Point it at the attendance QR code.`, 'success');
    } catch (error) {
        running = false;
        showMessage(error?.message || 'Camera could not start. Allow camera permission and use HTTPS.', 'danger');
        startButton.disabled = false;
        stopButton.disabled = true;
    }
}
async function loadCameras() {
    try {
        cameras = await Html5Qrcode.getCameras();
        // Prefer the rear/environment camera on phones; use the first available camera on laptops.
        const backIndex = cameras.findIndex(camera => /back|rear|environment|world/i.test(camera.label || ''));
        cameraIndex = backIndex >= 0 ? backIndex : 0;
        switchButton.disabled = cameras.length < 2;
        if (!cameras.length) showMessage('No camera was found. Connect or enable a camera, then try again.', 'danger');
        else showMessage('Camera detected. Select Start camera to begin scanning.', 'info');
    } catch (_) {
        // Camera labels may be unavailable until permission is granted; start() will request access.
        cameras = [];
        showMessage('Select Start camera and allow camera permission when prompted.', 'info');
    }
}
startButton.addEventListener('click', () => startCamera(cameraIndex));
timeOutButton.addEventListener('click', submitTimeOut);
stopButton.addEventListener('click', stopCamera);
switchButton.addEventListener('click', async () => {
    if (cameras.length < 2) return;
    const next = (cameraIndex + 1) % cameras.length;
    await stopCamera();
    await startCamera(next);
});
if (initialToken) submitToken(initialToken);
else loadCameras();
window.addEventListener('pagehide', () => { if (scanner && running) scanner.stop().catch(() => {}); });
</script>
@endpush
