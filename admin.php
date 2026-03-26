<!DOCTYPE html>
<html lang="zh-Hant">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Setup - SJACS</title>
    <script src="https://www.gstatic.com/firebasejs/9.6.10/firebase-app-compat.js"></script>
    <script src="https://www.gstatic.com/firebasejs/9.6.10/firebase-database-compat.js"></script>
    <script src="https://www.gstatic.com/firebasejs/9.6.10/firebase-auth-compat.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
    <style>
        :root { --primary: #2c3e50; --admin: #8e44ad; --bg: #f4f7f9; --success: #27ae60; --danger: #e74c3c; }
        body { font-family: 'Segoe UI', Arial, sans-serif; background-color: var(--bg); padding: 20px; color: #333; }
        .container { max-width: 1200px; margin: auto; background: white; padding: 40px; border-radius: 12px; box-shadow: 0 4px 20px rgba(0,0,0,0.1); }
        .section-header { background: var(--admin); color: white; padding: 12px 20px; margin-top: 30px; border-radius: 6px; font-weight: bold; }
        .info-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-top: 20px; }
        .info-item { display: flex; flex-direction: column; margin-bottom: 10px; }
        label { font-weight: bold; margin-bottom: 5px; font-size: 14px; }
        input, textarea, select { padding: 10px; border: 1px solid #ccc; border-radius: 4px; }
        .code-box { background: #f3e5f5; border: 2px dashed var(--admin); padding: 20px; text-align: center; margin-top: 20px; border-radius: 8px; }
        .btn-admin { background: var(--admin); color: white; padding: 12px 25px; border: none; border-radius: 6px; cursor: pointer; font-weight: bold; }
        #admin-page { display: none; margin-top: 50px; border-top: 5px solid var(--admin); padding-top: 20px; }
        table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        th, td { border: 1px solid #dee2e6; padding: 10px; text-align: center; font-size: 13px; color: black; }
        th { background: #f8f9fa; }
    </style>
</head>
<body>

<div class="container">
    <header style="text-align:center; border-bottom: 3px solid var(--admin); padding-bottom: 10px;">
        <h1>SJACS Administration Panel</h1>
        <h2>Phase 1: Teacher Setup & Attendance</h2>
    </header>

    <div class="section-header">1. Applicant Basic Information</div>
    <div class="info-grid">
        <div class="info-item"><label>教師姓名：</label><input type="text" id="teacher_name"></div>
        <div class="info-item"><label>本校教學年資：</label><input type="text" id="sjacs_exp"></div>
        <div class="info-item"><label>現在職級：</label><select id="current_rank"><option value="GM">GM</option><option value="SGM">SGM</option></select></div>
        <div class="info-item"><label>入職年份：</label><input type="date" id="app_date"></div>
    </div>
    <div class="info-item" style="margin-top:15px;"><label>任教班級及科目：</label><textarea id="subjects" rows="2"></textarea></div>

    <div class="section-header">2. Attendance & CPD (Items 2B & 3A)</div>
    <div class="info-grid" style="background: #fff9db; padding: 15px; border-radius: 8px;">
        <div class="info-item"><label>Sick Leave (Days):</label><input type="number" id="sick-days" step="0.1" value="0"></div>
        <div class="info-item"><label>Late (Times):</label><input type="number" id="late-times" step="1" value="0"></div>
        <div class="info-item"><label>Private Leave (Days):</label><input type="number" id="private-leave" step="0.1" value="0"></div>
        <div class="info-item"><label>CPD Hours (Total):</label><input type="number" id="cpd-hours" step="0.1" value="0"></div>
    </div>

    <div class="section-header">3. Appraiser Assignment</div>
    <div class="info-grid">
        <div class="info-item"><label>評核人員姓名：</label><input type="text" id="appraiser_name"></div>
        <div class="info-item">
            <label>職位 (Position)：</label>
            <select id="appraiser-position">
                <option value="Subject Panel Head">Subject Panel Head</option>
                <option value="VP (Academic)">VP (Academic)</option>
                <option value="VP (Students Nurturing and Support)">VP (Students Nurturing and Support)</option>
                <option value="AP">AP</option>
            </select>
        </div>
    </div>

    <div class="code-box">
        <button class="btn-admin" onclick="generateLink()">Save & Generate 6-Digit Code</button>
        <h1 id="link-output" style="color:var(--admin); margin-top:15px;">------</h1>
        <p>Give this code to the appraiser to start the evaluation.</p>
    </div>

    <hr style="margin: 50px 0;">
    <div style="text-align: center;">
        <button class="btn-admin" onclick="document.getElementById('login-gate').style.display='block'">Login to Admin Dashboard</button>
    </div>

    <div id="login-gate" style="display:none; margin-top:20px; text-align:center;">
        <input type="email" id="admin-email" placeholder="Admin Email">
        <input type="password" id="admin-password" placeholder="Pass">
        <button onclick="handleAdminLogin()" class="btn-admin">Login</button>
    </div>

    <div id="admin-page">
        <h2>Teacher Master Summary</h2>
        <div id="teacher-averages-container"></div>
        <h2>Raw Entry Log</h2>
        <div id="admin-data-container"></div>
    </div>
</div>

<script>
    const firebaseConfig = {
        apiKey: "AIzaSyAuS7S9jyCEGi6sO1yrXDar1w0NKa-DWQk",
        authDomain: "sjacs-evaluation.firebaseapp.com",
        databaseURL: "https://sjacs-evaluation-default-rtdb.asia-southeast1.firebasedatabase.app",
        projectId: "sjacs-evaluation",
        storageBucket: "sjacs-evaluation.firebasestorage.app",
        messagingSenderId: "889239849713",
        appId: "1:889239849713:web:ee35b33bc778b7c6eb7750"
    };
    firebase.initializeApp(firebaseConfig);
    const database = firebase.database();
    const auth = firebase.auth();

    function generateLink() {
        const code = Math.floor(100000 + Math.random() * 900000);
        const data = {
            teacher_name: document.getElementById('teacher_name').value,
            sjacs_exp: document.getElementById('sjacs_exp').value,
            current_rank: document.getElementById('current_rank').value,
            app_date: document.getElementById('app_date').value,
            subjects: document.getElementById('subjects').value,
            appraiser_name: document.getElementById('appraiser_name').value,
            'appraiser-position': document.getElementById('appraiser-position').value,
            'sick-days': document.getElementById('sick-days').value,
            'late-times': document.getElementById('late-times').value,
            'private-leave': document.getElementById('private-leave').value,
            'cpd-hours': document.getElementById('cpd-hours').value
        };
        database.ref('drafts/' + code).set(data).then(() => {
            document.getElementById('link-output').innerText = code;
            alert("Draft Saved. Copy the code.");
        });
    }

    function handleAdminLogin() {
        auth.signInWithEmailAndPassword(document.getElementById('admin-email').value, document.getElementById('admin-password').value)
        .then(() => { 
            document.getElementById('login-gate').style.display='none'; 
            document.getElementById('admin-page').style.display='block'; 
            fetchAdminData(); 
        }).catch(err => alert("Login Failed."));
    }

    function fetchAdminData() {
        const itemKeys = ['1A','1B','1C','1D','2A','2B','2C','2D','3A','3B','3C','3D'];
        database.ref('evaluations').on('value', snap => {
            const data = snap.val();
            if(!data) return;
            let logHtml = `<table><tr><th>Teacher</th><th>Appraiser</th><th>Total</th></tr>`;
            Object.entries(data).forEach(([id, e]) => {
                logHtml += `<tr><td>${e.teacher}</td><td>${e.appraiser}</td><td><strong>${e.total}</strong></td></tr>`;
            });
            document.getElementById('admin-data-container').innerHTML = logHtml + `</table>`;
        });
    }
</script>
</body>
</html>