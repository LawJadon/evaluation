<!DOCTYPE html>
<html lang="zh-Hant">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Appraiser Portal - SJACS</title>
    <script src="https://www.gstatic.com/firebasejs/9.6.10/firebase-app-compat.js"></script>
    <script src="https://www.gstatic.com/firebasejs/9.6.10/firebase-database-compat.js"></script>
    <style>
        :root { --primary: #2c3e50; --accent: #3498db; --bg: #f4f7f9; }
        body { font-family: 'Segoe UI', sans-serif; background: var(--bg); padding: 20px; }
        .container { max-width: 1200px; margin: auto; background: white; padding: 40px; border-radius: 12px; }
        .locked { background: #eee !important; color: #666; font-weight: bold; text-align: center; border: 1px solid #ccc; }
        .score-input { width: 60px; padding: 8px; border: 2px solid var(--accent); text-align: center; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #dee2e6; padding: 10px; }
        #sig-canvas { border: 2px dashed #ccc; width: 100%; height: 150px; cursor: crosshair; touch-action: none; }
    </style>
</head>
<body>

<div class="container" id="main-content">
    <h2 id="teacher-header">Enter 6-Digit Code to Begin</h2>
    <form id="evaluationForm" style="display:none;">
        <p>Teacher: <strong id="name-display"></strong> | Rank: <span id="rank-display"></span></p>
        
        <label>Appraiser Name:</label>
        <input type="text" id="appraiser_name" required style="padding:10px; width:300px;">
        <br><br>

        <table>
            <tr><th>Item</th><th>Description</th><th>Score (1-8)</th></tr>
            <tr><td>1A-1D</td><td>Teaching Performance</td><td><input type="number" class="score-input teaching-input" data-item="1A" min="1" max="8"> (etc...)</td></tr>
            
            <tr>
                <td>2B</td>
                <td>Attendance & Punctuality (Calculated from Admin Data)</td>
                <td><input type="number" id="score-2B" class="score-input locked" readonly></td>
            </tr>

            <tr>
                <td>3A</td>
                <td>CPD Hours (Calculated from Admin Data)</td>
                <td><input type="number" id="score-3A" class="score-input locked" readonly></td>
            </tr>
            </table>

        <h3>Signature</h3>
        <canvas id="sig-canvas"></canvas>
        <button type="submit" style="width:100%; background:#27ae60; color:white; padding:20px; border:none; cursor:pointer; font-size:18px; margin-top:20px;">Submit Evaluation</button>
    </form>
</div>

<script>
const firebaseConfig = {
  apiKey: "AIza...",
  authDomain: "your-project.firebaseapp.com",
  databaseURL: "https://your-project.firebaseio.com",
  projectId: "your-project",
  storageBucket: "your-project.appspot.com",
  messagingSenderId: "12345",
  appId: "1:12345:web:6789"
};
    firebase.initializeApp(firebaseConfig);
    const database = firebase.database();

    window.onload = function() {
        const code = prompt("Please enter the 6-digit evaluation code:");
        if (code) {
            database.ref('drafts/' + code).once('value').then(snap => {
                const d = snap.val();
                if (d) {
                    document.getElementById('evaluationForm').style.display = 'block';
                    document.getElementById('name-display').innerText = d.teacher_name;
                    document.getElementById('rank-display').innerText = d.current_rank;
                    
                    // Logic for 2B (Attendance)
                    const s = parseFloat(d.sick_days) || 0;
                    const l = parseInt(d.late_times) || 0;
                    const p = parseFloat(d.private_leave) || 0;
                    const idx = s + p + (l * 4);
                    const score2B = (idx === 0) ? 8 : (idx < 3) ? 7 : (idx < 6) ? 6 : (idx < 10) ? 5 : (idx < 14) ? 4 : (idx < 18) ? 3 : (idx < 22) ? 2 : 1;
                    document.getElementById('score-2B').value = score2B;

                    // Logic for 3A (CPD)
                    const h = parseFloat(d.cpd_hours) || 0;
                    const score3A = (h >= 75) ? 8 : (h >= 60) ? 7 : (h >= 50) ? 6 : (h >= 40) ? 5 : (h >= 30) ? 4 : (h >= 25) ? 3 : (h >= 20) ? 2 : 1;
                    document.getElementById('score-3A').value = score3A;

                } else { alert("Invalid Code"); location.reload(); }
            });
        }
    };

    // Signature and Form Submission logic goes here (Identical to previous versions)
</script>
</body>
</html>