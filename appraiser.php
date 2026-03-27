<!DOCTYPE html>
<html lang="zh-Hant">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Appraiser Portal - SJACS</title>
    <script src="https://www.gstatic.com/firebasejs/9.6.10/firebase-app-compat.js"></script>
    <script src="https://www.gstatic.com/firebasejs/9.6.10/firebase-database-compat.js"></script>
    <style>
        :root { --primary: #2c3e50; --accent: #3498db; --bg: #f4f7f9; --success: #27ae60; --danger: #e74c3c; }
        body { font-family: 'Segoe UI', Tahoma, sans-serif; background: var(--bg); margin: 0; padding: 20px; color: #333; line-height: 1.6; }
        .container { max-width: 1100px; margin: auto; background: white; padding: 30px; border-radius: 12px; box-shadow: 0 4px 20px rgba(0,0,0,0.1); }
        .header-box { background: var(--primary); color: white; padding: 20px; border-radius: 8px; margin-bottom: 20px; text-align: center; }
        .info-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 15px; margin-bottom: 20px; background: #eef2f5; padding: 15px; border-radius: 8px; border-left: 5px solid var(--accent); }
        .section-title { border-bottom: 2px solid var(--primary); padding-bottom: 5px; margin-top: 30px; color: var(--primary); font-size: 1.3em; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; table-layout: fixed; }
        th { background: #ecf0f1; color: var(--primary); padding: 12px; border: 1px solid #dee2e6; }
        td { border: 1px solid #dee2e6; padding: 15px; vertical-align: top; word-wrap: break-word; font-size: 0.95em; }
        .category-row { background: #f8f9fa; font-weight: bold; color: #2c3e50; font-size: 1.1em; }
        
        .score-input { width: 70px; padding: 10px; border: 2px solid var(--accent); text-align: center; border-radius: 4px; font-weight: bold; font-size: 1.2em; transition: all 0.2s; }
        .score-input:focus { outline: none; box-shadow: 0 0 8px rgba(52, 152, 219, 0.5); }
        .invalid-flash { border-color: var(--danger) !important; background-color: #ffdce0 !important; }
        
        .locked { background: #eee !important; color: #666; border: 1px solid #ccc; cursor: not-allowed; }
        .lang-en { color: #666; font-style: italic; display: block; margin-top: 5px; font-size: 0.9em; }
        
        #sig-container { margin-top: 10px; border: 2px dashed #ccc; background: #fafafa; width: 100%; max-width: 500px; height: 150px; }
        #sig-canvas { cursor: crosshair; width: 100%; height: 100%; touch-action: none; }
        .btn-submit { width: 100%; background: var(--success); color: white; padding: 20px; border: none; cursor: pointer; font-size: 18px; border-radius: 8px; margin-top: 30px; font-weight: bold; }
    </style>
</head>
<body>

<div class="container">
    <div class="header-box">
        <h2 style="margin:0;">教師表現評核表 (Teacher Performance Evaluation)</h2>
        <p style="margin:5px 0 0 0;">聖若瑟英文中學 (SJACS)</p>
    </div>

    <div id="loading-area" style="text-align:center; padding: 50px;">
        <h3>請輸入啟動碼... (Please enter code)</h3>
    </div>

    <form id="evaluationForm" style="display:none;">
        <h3 class="section-title">第一部分：基本資料 (Part 1: Basic Information)</h3>
        <div class="info-grid">
            <div><strong>教師姓名:</strong> <br><span id="display-name"></span></div>
            <div><strong>職級:</strong> <br><span id="display-rank"></span></div>
            <div><strong>本校教學年資:</strong> <br><span id="display-exp"></span></div>
            <div><strong>到職日期:</strong> <br><span id="display-app-date"></span></div>
            <div><strong>遲到:</strong> <br><span id="display-late"></span> 次</div>
            <div><strong>病假:</strong> <br><span id="display-sick"></span> 日</div>
            <div><strong>事假:</strong> <br><span id="display-private"></span> 日</div>
            <div><strong>進修時數:</strong> <br><span id="display-cpd"></span> 小時</div>
        </div>

        <div class="info-grid" style="background: #fdfdfd; border-left-color: #95a5a6;">
            <div><strong>Admin Com I/C:</strong> <br><span id="display-admin-ic"></span></div>
            <div><strong>ECA Duty:</strong> <br><span id="display-eca"></span></div>
        </div>

        <div class="info-grid" style="background: #fff3e0; border-left-color: #ff9800;">
            <div><strong>評核人姓名:</strong> <br><span id="display-appraiser-name"></span></div>
            <div><strong>評核人職位:</strong> <br><span id="display-appraiser-pos"></span></div>
        </div>

        <h3 class="section-title">第二部分：評核指標 (Part 2: Performance Indicators)</h3>
        <table>
            <thead>
                <tr>
                    <th style="width:160px;">項目 (Item)</th>
                    <th>評核指標</th>
                    <th style="width:110px;">評分 (1-8)</th>
                </tr>
            </thead>
            <tbody>
                <tr class="category-row"><td colspan="3">1. 教學表現及課室管理</td></tr>
                <tr>
                    <td>A. Curriculum Design & Planning</td>
                    <td>課程編排具系統性，學習目標清晰，授課過程、學習活動及進度評核能相互配合；課程能顧及學生的需要、經驗和能力。<span class="lang-en">(Systematic course design; objective-based lesson planning; integration of teaching with activities and assessment; consideration of students’ needs, experience and abilities.)</span></td>
                    <td><input type="number" class="score-input" name="q1A" placeholder="1-8" required></td>
                </tr>
                <tr>
                    <td>B. Classroom Management & Interaction</td>
                    <td>能有效執行課室規則、常規和流程；妥善安排學生分組活動；並能透過適量的師/生及生/生互動，確保課節的活力。<span class="lang-en">(Effective enforcement of established class rules, routines and procedures; systematic organization and monitoring of groups activities; moderate T/S and S/S interaction for class momentum.)</span></td>
                    <td><input type="number" class="score-input" name="q1B" placeholder="1-8" required></td>
                </tr>
                <tr>
                    <td>C. Implementation of Teaching</td>
                    <td>能清晰及生動地傳達與課題有關的知識、概念和學習目標；善用提問及學生的回應以加強互動；並能適切運用各種教學資源。<span class="lang-en">(Clear and stimulating delivery of information; effective use of questioning; appropriate deployment of resources.)</span></td>
                    <td><input type="number" class="score-input" name="q1C" placeholder="1-8" required></td>
                </tr>
                <tr>
                    <td>D. Subject Knowledge</td>
                    <td>具充分的學科知識及理念，並瞭解其最新發展；明白課節內容與整體學科課程的關係。<span class="lang-en">(Has clear concept and good general knowledge of the discipline.)</span></td>
                    <td><input type="number" class="score-input" name="q1D" placeholder="1-8" required></td>
                </tr>

                <tr class="category-row"><td colspan="3">2. 師德及學生培育</td></tr>
                <tr>
                    <td>A. Professional Image</td>
                    <td>衣著整潔得體，善解人意，處事得體，能與各同事相處融洽，有禮貌。<span class="lang-en">(Dresses neatly and appropriately; sensitive to others' feelings; tactful.)</span></td>
                    <td><input type="number" class="score-input" name="q2A" placeholder="1-8" required></td>
                </tr>
                <tr>
                    <td>B. Attendance & Punctuality</td>
                    <td>沒有遲到，早退，曠缺課現象。<span class="lang-en">(No record of leaving early, being late or unexcused absence.)</span><br><small style="color:blue;">* 系統自動評分</small></td>
                    <td><input type="number" id="score-2B" class="score-input locked" readonly></td>
                </tr>
                <tr>
                    <td>C. Work Attitude</td>
                    <td>工作態度認真，自主性強，善提意見，勇於承擔，愛護及扶掖學生。<span class="lang-en">(Adopts serious attitude in work; seeks and readily accepts responsibility.)</span></td>
                    <td><input type="number" class="score-input" name="q2C" placeholder="1-8" required></td>
                </tr>
                <tr>
                    <td>D. Values Education</td>
                    <td>主動參與學校德育工作及國家安全教育工作，關注學生個人成長。<span class="lang-en">(Participates in school moral education and National Security Education.)</span></td>
                    <td><input type="number" class="score-input" name="q2D" placeholder="1-8" required></td>
                </tr>

                <tr class="category-row"><td colspan="3">3. 教師持續專業發展</td></tr>
                <tr>
                    <td>A. Training & Qualifications</td>
                    <td>參加新教師試用期培訓...以取得相應的教師資格。<br><small style="color:blue;">* 系統自動評分</small></td>
                    <td><input type="number" id="score-3A" class="score-input locked" readonly></td>
                </tr>
                <tr>
                    <td>B. Team Collaboration</td>
                    <td>在科主任或行政組別負責人的指導下，按要求完成任務，虛心好學。<span class="lang-en">(Completes tasks as required; open-minded and eager to learn.)</span></td>
                    <td><input type="number" class="score-input" name="q3B" placeholder="1-8" required></td>
                </tr>
                <tr>
                    <td>C. Pedagogical Research</td>
                    <td>參加校內外各項教研活動，認真學習教學相關理論或策略。<span class="lang-en">(Participates in various teaching and research activities.)</span></td>
                    <td><input type="number" class="score-input" name="q3C" placeholder="1-8" required></td>
                </tr>
                <tr>
                    <td>D. Catholic Core Values</td>
                    <td>參加校內外有關天主教教育五大核心價值活動。<span class="lang-en">(Participates in activities about the 5 core values of Catholic education.)</span></td>
                    <td><input type="number" class="score-input" name="q3D" placeholder="1-8" required></td>
                </tr>
            </tbody>
        </table>

        <div style="margin-top:30px;">
            <h3>評核人簽署 (Appraiser Signature)</h3>
            <div id="sig-container">
                <canvas id="sig-canvas"></canvas>
            </div>
            <button type="button" onclick="clearSignature()" style="margin-top:10px; padding: 8px 15px; cursor:pointer;">重設簽署 (Clear)</button>
        </div>

        <button type="submit" class="btn-submit">提交評核報告 (Submit Evaluation)</button>
    </form>
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
    let currentTeacherData = null;
    let currentCode = "";

    window.onload = function() {
        const code = prompt("Enter 6-digit code:");
        if (!code) return;
        database.ref('drafts/' + code).once('value').then(snap => {
            const d = snap.val();
            if (d) {
                currentTeacherData = d;
                currentCode = code;
                populateForm(d);
                initSignature();
                setupScoreValidation();
            } else { alert("Invalid Code"); location.reload(); }
        });
    };

    function setupScoreValidation() {
        document.querySelectorAll('.score-input:not(.locked)').forEach(input => {
            input.addEventListener('input', function() {
                const val = this.value;
                if (val !== "" && (!Number.isInteger(Number(val)) || val < 1 || val > 8 || val.includes('.'))) {
                    this.value = "";
                    this.classList.add('invalid-flash');
                    setTimeout(() => this.classList.remove('invalid-flash'), 500);
                }
            });
        });
    }

    function populateForm(d) {
        document.getElementById('loading-area').style.display = 'none';
        document.getElementById('evaluationForm').style.display = 'block';
        
        // Mapped directly to admin.php keys
        document.getElementById('display-name').innerText = d.teacher_name || "---";
        document.getElementById('display-rank').innerText = d.current_rank || "---";
        document.getElementById('display-exp').innerText = d.sjacs_exp || "---";
        document.getElementById('display-app-date').innerText = d.app_date || "---";
        document.getElementById('display-late').innerText = d.late_times || 0;
        document.getElementById('display-sick').innerText = d.sick_days || 0;
        document.getElementById('display-private').innerText = d.private_leave || 0;
        document.getElementById('display-cpd').innerText = d.cpd_hours || 0;
        document.getElementById('display-admin-ic').innerText = d.admin_ic || "None";
        document.getElementById('display-eca').innerText = d.eca_duty || "None";
        document.getElementById('display-appraiser-name').innerText = d.appraiser_name || "---";
        document.getElementById('display-appraiser-pos').innerText = d.appraiser_pos || "---";

        // Auto-Calc logic
        const idx = (parseFloat(d.sick_days)||0) + (parseFloat(d.private_leave)||0) + ((parseInt(d.late_times)||0)*4);
        document.getElementById('score-2B').value = (idx === 0) ? 8 : (idx < 3) ? 7 : (idx < 6) ? 6 : (idx < 10) ? 5 : (idx < 14) ? 4 : (idx < 18) ? 3 : (idx < 22) ? 2 : 1;
        const h = parseFloat(d.cpd_hours) || 0;
        document.getElementById('score-3A').value = (h >= 75) ? 8 : (h >= 60) ? 7 : (h >= 50) ? 6 : (h >= 40) ? 5 : (h >= 30) ? 4 : (h >= 25) ? 3 : (h >= 20) ? 2 : 1;
    }

    let canvas, ctx, drawing = false;
    function initSignature() {
        canvas = document.getElementById('sig-canvas');
        ctx = canvas.getContext('2d');
        canvas.width = canvas.offsetWidth; canvas.height = canvas.offsetHeight;
        ['mousedown','touchstart'].forEach(t => canvas.addEventListener(t, (e) => { drawing = true; draw(e); e.preventDefault(); }));
        ['mousemove','touchmove'].forEach(t => canvas.addEventListener(t, (e) => { draw(e); e.preventDefault(); }));
        ['mouseup','touchend'].forEach(t => window.addEventListener(t, () => { drawing = false; ctx.beginPath(); }));
    }
    function draw(e) {
        if (!drawing) return;
        const rect = canvas.getBoundingClientRect();
        const x = (e.clientX || (e.touches ? e.touches[0].clientX : 0)) - rect.left;
        const y = (e.clientY || (e.touches ? e.touches[0].clientY : 0)) - rect.top;
        ctx.lineWidth = 2; ctx.lineCap = 'round'; ctx.strokeStyle = '#000';
        ctx.lineTo(x, y); ctx.stroke(); ctx.beginPath(); ctx.moveTo(x, y);
    }
    function clearSignature() { ctx.clearRect(0,0,canvas.width,canvas.height); ctx.beginPath(); }

    document.getElementById('evaluationForm').onsubmit = function(e) {
        e.preventDefault();
        const scores = {};
        // Map q1A, q1B, score-2B, etc to the short keys 1A, 1B used in Admin dashboard
        scores['1A'] = document.getElementsByName('q1A')[0].value;
        scores['1B'] = document.getElementsByName('q1B')[0].value;
        scores['1C'] = document.getElementsByName('q1C')[0].value;
        scores['1D'] = document.getElementsByName('q1D')[0].value;
        scores['2A'] = document.getElementsByName('q2A')[0].value;
        scores['2B'] = document.getElementById('score-2B').value;
        scores['2C'] = document.getElementsByName('q2C')[0].value;
        scores['2D'] = document.getElementsByName('q2D')[0].value;
        scores['3A'] = document.getElementById('score-3A').value;
        scores['3B'] = document.getElementsByName('q3B')[0].value;
        scores['3C'] = document.getElementsByName('q3C')[0].value;
        scores['3D'] = document.getElementsByName('q3D')[0].value;

        const total = Object.values(scores).reduce((a, b) => parseFloat(a) + parseFloat(b), 0);
        
        database.ref('evaluations').push({
            teacher: currentTeacherData.teacher_name,
            appraiser: currentTeacherData.appraiser_name,
            appraiser_pos: currentTeacherData.appraiser_pos,
            scores: scores,
            total: total,
            signature: canvas.toDataURL(),
            timestamp: Date.now()
        }).then(() => {
            database.ref('drafts/' + currentCode).remove();
            alert("Submitted Successfully");
            location.reload();
        });
    };
</script>
</body>
</html>