<!DOCTYPE html>
<html lang="zh-Hant">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SJACS Admin - Assessment Setup</title>
    <script src="https://www.gstatic.com/firebasejs/9.6.10/firebase-app-compat.js"></script>
    <script src="https://www.gstatic.com/firebasejs/9.6.10/firebase-database-compat.js"></script>
    <script src="https://www.gstatic.com/firebasejs/9.6.10/firebase-auth-compat.js"></script>
    <style>
        :root { --admin: #8e44ad; --bg: #f4f7f9; --primary: #2c3e50; --accent: #3498db; }
        body { font-family: 'Segoe UI', Arial, sans-serif; background-color: var(--bg); padding: 20px; }
        .container { max-width: 1400px; margin: auto; background: white; padding: 40px; border-radius: 12px; box-shadow: 0 4px 20px rgba(0,0,0,0.1); }
        
        .header-main { text-align: center; border-bottom: 3px solid var(--primary); padding-bottom: 20px; margin-bottom: 30px; }
        .header-main h1 { font-size: 26px; margin: 0; color: var(--primary); }
        .header-main h2 { font-size: 18px; margin: 10px 0 0 0; font-weight: normal; line-height: 1.4; }

        .section-header { background: var(--admin); color: white; padding: 12px 20px; border-radius: 6px; font-weight: bold; margin-top: 30px; }
        .info-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin-top: 15px; }
        .info-item { display: flex; flex-direction: column; }
        label { font-weight: bold; font-size: 13px; margin-bottom: 5px; color: #555; }
        input, select { padding: 10px; border: 1px solid #ccc; border-radius: 4px; font-size: 14px; }
        
        .code-display { background: #f3e5f5; border: 2px dashed var(--admin); padding: 25px; text-align: center; margin-top: 30px; border-radius: 8px; }
        .code-display h1 { font-size: 42px; margin: 15px 0 0 0; color: var(--admin); letter-spacing: 5px; }

        #admin-dashboard { display:none; margin-top: 50px; border-top: 5px solid var(--admin); padding-top: 30px; }
        .dashboard-section { margin-bottom: 40px; overflow-x: auto; }
        
        table { width: 100%; border-collapse: collapse; margin-top: 15px; background: white; font-size: 12px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: center; }
        th { background: #f8f9fa; color: var(--primary); }
        
        .summary-head { background: #f3e5f5 !important; color: var(--admin); }
        .master-col { background: #f39c12 !important; color: white !important; font-weight: bold; }
        
        .edit-input { width: 35px; text-align: center; border: 1px solid var(--accent); border-radius: 3px; padding: 3px; font-weight: bold; }
        .btn-delete { background: #e74c3c; color: white; border: none; padding: 6px 10px; border-radius: 4px; cursor: pointer; font-size: 11px; }
        .btn-pdf { background: #27ae60; color: white; border: none; padding: 6px 10px; border-radius: 4px; cursor: pointer; font-size: 11px; margin-right: 5px; }
    </style>
</head>
<body>

<div class="container">
    <div class="header-main">
        <h1>聖若瑟英文中學 (St. Joseph’s Anglo-Chinese School)</h1>
        <h2>新入職 / 轉職教師評鑑表 Assessment Form for Newly Employed Teacher / Teacher Applying for Regrading</h2>
    </div>

    <div class="section-header">1. 基本資料 (Basic Information)</div>
    <div class="info-grid">
        <div class="info-item"><label>教師姓名 Teacher Name:</label><input type="text" id="teacher_name"></div>
        <div class="info-item"><label>職級 Rank:</label><select id="current_rank"><option value="GM">GM</option><option value="SGM">SGM</option></select></div>
        <div class="info-item"><label>本校教學年資 Years at SJACS:</label><input type="text" id="sjacs_exp"></div>
        <div class="info-item"><label>到職日期 Appointment Date:</label><input type="date" id="app_date"></div>
    </div>

    <div style="margin-top:20px; padding:15px; background:#f9f9f9; border-radius:8px; border: 1px solid #eee;">
        <label style="color:var(--admin); font-weight:bold;">其他工作項目 (Other Work Items):</label>
        <div class="info-grid" style="margin-top:10px;">
            <div class="info-item"><label>Admin Com I/C:</label><input type="text" id="admin_ic"></div>
            <div class="info-item"><label>Admin Com Assistant:</label><input type="text" id="admin_asst"></div>
            <div class="info-item"><label>Admin Com Member:</label><input type="text" id="admin_mem"></div>
            <div class="info-item"><label>ECA:</label><input type="text" id="eca_duty"></div>
        </div>
    </div>

    <div class="section-header">2. 出勤及進修數據 (Attendance & CPD Data)</div>
    <div class="info-grid">
        <div class="info-item"><label>病假 Sick Leave (Days):</label><input type="number" id="sick-days" step="0.1" value="0"></div>
        <div class="info-item"><label>遲到 Late (Times):</label><input type="number" id="late-times" step="1" value="0"></div>
        <div class="info-item"><label>事假 Private Leave (Days):</label><input type="number" id="private-leave" step="0.1" value="0"></div>
        <div class="info-item"><label>CPD Hours:</label><input type="number" id="cpd-hours" step="0.1" value="0"></div>
    </div>

    <div class="section-header">3. 評核人資料 (Appraiser's Information)</div>
    <div class="info-grid">
        <div class="info-item"><label>評核人姓名 Appraiser Name:</label><input type="text" id="app_name"></div>
        <div class="info-item">
            <label>職位 Position:</label>
            <select id="app_pos">
                <option value="">-- Select Position --</option>
                <option value="AP">AP</option>
                <option value="committee (i/c)">Committee (i/c)</option>
                <option value="ECA (i/c)">ECA (i/c)</option>
                <option value="Subject Panel Head">Subject Panel Head</option>
                <option value="VP (academic)">VP (academic)</option>
                <option value="VP (school administration & resources affairs)">VP (school administration & resources affairs)</option>
                <option value="VP (students nurturing and support)">VP (students nurturing and support)</option>
            </select>
        </div>
    </div>

    <div class="code-display">
        <button onclick="generateCode()" style="padding:15px 30px; background:var(--admin); color:white; border:none; border-radius:8px; cursor:pointer; font-weight:bold; font-size:16px;">Generate 6-Digit Evaluation Code</button>
        <h1 id="output-code">------</h1>
    </div>

    <center style="margin-top:40px;">
        <button onclick="showLogin()" style="background:none; border:none; color:#7f8c8d; cursor:pointer; text-decoration:underline; font-size:14px;">Access Admin Dashboard</button>
    </center>

    <div id="login-gate" style="display:none; text-align:center; margin-top:20px; background:#f1f1f1; padding:20px; border-radius:8px;">
        <input type="email" id="admin-email" placeholder="Admin Email" style="margin-bottom:10px;">
        <input type="password" id="admin-password" placeholder="Password">
        <button onclick="handleLogin()" style="background:var(--primary); color:white; border:none; padding:10px 25px; border-radius:4px; margin-top:10px; cursor:pointer;">Login to Dashboard</button>
    </div>

    <div id="admin-dashboard">
        <div class="dashboard-section">
            <h3 style="color:var(--admin)">1. Teacher Master Summary (Averages)</h3>
            <div id="summary-table-container"></div>
        </div>

        <div class="dashboard-section">
            <h3 style="color:var(--admin)">2. Editable Raw Entry Log</h3>
            <div id="raw-log-container"></div>
        </div>
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
    const itemKeys = ['1A','1B','1C','1D','2A','2B','2C','2D','3A','3B','3C','3D'];

    function generateCode() {
        const code = Math.floor(100000 + Math.random() * 900000);
        const data = {
            teacher_name: document.getElementById('teacher_name').value,
            sjacs_exp: document.getElementById('sjacs_exp').value,
            current_rank: document.getElementById('current_rank').value,
            app_date: document.getElementById('app_date').value,
            admin_ic: document.getElementById('admin_ic').value,
            admin_asst: document.getElementById('admin_asst').value,
            admin_mem: document.getElementById('admin_mem').value,
            eca_duty: document.getElementById('eca_duty').value,
            sick_days: document.getElementById('sick-days').value,
            late_times: document.getElementById('late-times').value,
            private_leave: document.getElementById('private-leave').value,
            cpd_hours: document.getElementById('cpd-hours').value,
            appraiser_name: document.getElementById('app_name').value,
            appraiser_pos: document.getElementById('app_pos').value,
            timestamp: Date.now()
        };
        
        if(!data.teacher_name || !data.appraiser_pos) {
            alert("Please ensure Teacher Name and Appraiser Position are filled.");
            return;
        }

        database.ref('drafts/' + code).set(data).then(() => {
            document.getElementById('output-code').innerText = code;
        });
    }

    function showLogin() { document.getElementById('login-gate').style.display='block'; }
    function handleLogin() {
        auth.signInWithEmailAndPassword(document.getElementById('admin-email').value, document.getElementById('admin-password').value)
        .then(() => {
            document.getElementById('login-gate').style.display='none';
            document.getElementById('admin-dashboard').style.display='block';
            loadDashboardData();
        }).catch(e => alert(e.message));
    }

    function loadDashboardData() {
        database.ref('evaluations').on('value', snap => {
            const data = snap.val();
            if(!data) return;

            const teacherGroups = {};
            let rawHtml = `<table><tr><th>Teacher</th><th>Appraiser</th>${itemKeys.map(k=>`<th>${k}</th>`).join('')}<th>Tot</th><th>Action</th></tr>`;
            
            Object.entries(data).forEach(([key, ev]) => {
                if(!teacherGroups[ev.teacher]) teacherGroups[ev.teacher] = [];
                teacherGroups[ev.teacher].push(ev);

                rawHtml += `<tr>
                    <td><strong>${ev.teacher}</strong></td>
                    <td>${ev.appraiser}</td>
                    ${itemKeys.map(k => `<td><input type="number" class="edit-input" value="${ev.scores[k] || 0}" onchange="updateCell('${key}', '${k}', this.value)"></td>`).join('')}
                    <td><strong id="total-${key}">${ev.total}</strong></td>
                    <td>
                        <button class="btn-pdf" onclick="openPrintView('${key}')">Print/PDF</button>
                        <button class="btn-delete" onclick="deleteEntry('${key}')">Del</button>
                    </td>
                </tr>`;
            });
            document.getElementById('raw-log-container').innerHTML = rawHtml + `</table>`;

            let sumHtml = `<table><tr class="summary-head"><th>Teacher</th>${itemKeys.map(k=>`<th>${k}</th>`).join('')}<th class="master-col">Master</th></tr>`;
            Object.keys(teacherGroups).forEach(name => {
                const evals = teacherGroups[name];
                let rowMasterTotal = 0;
                sumHtml += `<tr><td><strong>${name}</strong></td>`;
                itemKeys.forEach(k => {
                    const avg = evals.reduce((acc, curr) => acc + parseFloat(curr.scores[k] || 0), 0) / evals.length;
                    rowMasterTotal += avg;
                    sumHtml += `<td>${avg.toFixed(1)}</td>`;
                });
                sumHtml += `<td class="master-col">${rowMasterTotal.toFixed(1)}</td></tr>`;
            });
            document.getElementById('summary-table-container').innerHTML = sumHtml + `</table>`;
        });
    }

    function openPrintView(evalId) {
        database.ref('evaluations/' + evalId).once('value').then(snap => {
            const val = snap.val();
            if(!val) return;
            
            const itemDescriptions = {
                '1A': 'A. Curriculum Design & Planning: 課程編排具系統性，學習目標清晰，授課過程、學習活動及進度評核能相互配合；課程能顧及學生的需要、經驗和能力。(Systematic course design; objective-based lesson planning; integration of teaching with activities and assessment; consideration of students’ needs, experience and abilities.)',
                '1B': 'B. Classroom Management & Interaction: 能有效執行課室規則、常規和流程；妥善安排學生分組活動；並能透過適量的師/生及生/生互動，確保課節的活力。(Effective enforcement of established class rules, routines and procedures; systematic organization and monitoring of groups activities; moderate T/S and S/S interaction for class momentum.)',
                '1C': 'C. Implementation of Teaching: 能清晰及生動地傳達與課題有關的知識、概念和學習目標；善用提問及學生的回應以加強互動；並能適切運用各種教學資源。(Clear and stimulating delivery of information; effective use of questioning; appropriate deployment of resources.)',
                '1D': 'D. Subject Knowledge: 具充分的學科知識及理念，並瞭解其最新發展；明白課節內容與整體學科課程的關係。(Has clear concept and good general knowledge of the discipline.)',
                '2A': 'A. Professional Image: 衣著整潔得體，善解人意，處事得體，能與各同事相處融洽，有禮貌。(Dresses neatly and appropriately; sensitive to others\' feelings; tactful.)',
                '2B': 'B. Attendance & Punctuality: 沒有遲到，早退，曠缺課現象。(No record of leaving early, being late or unexcused absence.)',
                '2C': 'C. Work Attitude: 工作態度認真，自主性強，善提意見，勇於承擔，愛護及扶掖學生。(Adopts serious attitude in work; seeks and readily accepts responsibility.)',
                '2D': 'D. Values Education: 主動參與學校德育工作及國家安全教育工作，關注學生個人成長。(Participates in school moral education and National Security Education.)',
                '3A': 'A. Training & Qualifications: 參加新教師試用期培訓...以取得相應的教師資格。(Participates in induction training to obtain qualifications.)',
                '3B': 'B. Team Collaboration: 在科主任或行政組別負責人的指導下，按要求完成任務，虛心好學。(Completes tasks as required; open-minded and eager to learn.)',
                '3C': 'C. Pedagogical Research: 參加校內外各項教研活動，認真學習教學相關理論或策略。(Participates in various teaching and research activities.)',
                '3D': 'D. Catholic Core Values: 參加校內外有關天主教教育五大核心價值活動。(Participates in activities about the 5 core values of Catholic education.)'
            };

            const printWindow = window.open('', '_blank');
            printWindow.document.write(`
                <html>
                <head>
                    <title>Evaluation Report - ${val.teacher}</title>
                    <style>
                        body { font-family: "Segoe UI", Arial, sans-serif; padding: 30px; color: #000; line-height: 1.4; }
                        .header { text-align: center; border-bottom: 2px solid #000; margin-bottom: 20px; padding-bottom: 10px; }
                        .header h1 { font-size: 22px; margin: 0; }
                        .header h2 { font-size: 16px; margin: 5px 0; font-weight: normal; }
                        table { width: 100%; border-collapse: collapse; margin-top: 10px; table-layout: fixed; }
                        th, td { border: 1px solid #000; padding: 8px; font-size: 12px; text-align: left; word-wrap: break-word; }
                        .label-cell { background: #f2f2f2; font-weight: bold; width: 25%; }
                        .section-title { background: #2c3e50; color: #fff; padding: 8px; font-weight: bold; margin-top: 20px; font-size: 13px; }
                        .item-title { background: #eee; font-weight: bold; }
                        .score-cell { text-align: center; font-weight: bold; width: 60px; font-size: 14px; }
                        .footer { margin-top: 30px; font-size: 10px; border-top: 1px solid #ccc; padding-top: 10px; }
                        @media print { .no-print { display: none; } }
                    </style>
                </head>
                <body>
                    <div class="no-print" style="background:#fff9c4; padding:10px; border:1px solid #fbc02d; margin-bottom:20px; text-align:center;">
                        <strong>Print View Mode:</strong> Press <b>Ctrl + P</b> to save as PDF.
                        <button onclick="window.print()" style="margin-left:10px; cursor:pointer;">Print Now</button>
                    </div>

                    <div class="header">
                        <h1>聖若瑟英文中學 St. Joseph's Anglo-Chinese School</h1>
                        <h2>教師評核報告 Teacher Evaluation Report</h2>
                    </div>

                    <div class="section-title">1. 基本資料 (Basic Information)</div>
                    <table>
                        <tr>
                            <td class="label-cell">教師姓名 Teacher Name:</td><td>${val.teacher || '-'}</td>
                            <td class="label-cell">職級 Rank:</td><td>${val.current_rank || '-'}</td>
                        </tr>
                        <tr>
                            <td class="label-cell">本校教學年資 Years at SJACS:</td><td>${val.sjacs_exp || '-'}</td>
                            <td class="label-cell">到職日期 Appointment Date:</td><td>${val.app_date || '-'}</td>
                        </tr>
                    </table>
                    <div style="font-weight:bold; font-size:12px; margin-top:10px;">其他工作項目 (Other Work Items):</div>
                    <table>
                        <tr>
                            <td class="label-cell">Admin Com I/C:</td><td>${val.admin_ic || '-'}</td>
                            <td class="label-cell">Admin Com Assistant:</td><td>${val.admin_asst || '-'}</td>
                        </tr>
                        <tr>
                            <td class="label-cell">Admin Com Member:</td><td>${val.admin_mem || '-'}</td>
                            <td class="label-cell">ECA:</td><td>${val.eca_duty || '-'}</td>
                        </tr>
                    </table>

                    <div class="section-title">2. 出勤及進修數據 (Attendance & CPD Data)</div>
                    <table>
                        <tr>
                            <td class="label-cell">病假 Sick Leave (Days):</td><td>${val.sick_days || '0'}</td>
                            <td class="label-cell">遲到 Late (Times):</td><td>${val.late_times || '0'}</td>
                        </tr>
                        <tr>
                            <td class="label-cell">事假 Private Leave (Days):</td><td>${val.private_leave || '0'}</td>
                            <td class="label-cell">CPD Hours:</td><td>${val.cpd_hours || '0'}</td>
                        </tr>
                        <tr>
                            <td class="label-cell">評核人 Appraiser:</td><td>${val.appraiser || '-'}</td>
                            <td class="label-cell">職位 Position:</td><td>${val.appraiser_pos || '-'}</td>
                        </tr>
                    </table>

                    <div class="section-title">3. 評核指標 (Part 2: Performance Indicators)</div>
                    <table>
                        <thead>
                            <tr style="background:#eee;">
                                <th>評核項目及具體描述 (Assessment Items & Descriptions)</th>
                                <th class="score-cell">得分 (Score)</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr class="item-title"><td colspan="2">1. 教學表現及課室管理</td></tr>
                            ${['1A','1B','1C','1D'].map(k => `<tr><td>${itemDescriptions[k]}</td><td class="score-cell">${val.scores[k] || 0}</td></tr>`).join('')}
                            
                            <tr class="item-title"><td colspan="2">2. 師德及學生培育</td></tr>
                            ${['2A','2B','2C','2D'].map(k => `<tr><td>${itemDescriptions[k]}</td><td class="score-cell">${val.scores[k] || 0}</td></tr>`).join('')}
                            
                            <tr class="item-title"><td colspan="2">3. 教師持續專業發展</td></tr>
                            ${['3A','3B','3C','3D'].map(k => `<tr><td>${itemDescriptions[k]}</td><td class="score-cell">${val.scores[k] || 0}</td></tr>`).join('')}
                        </tbody>
                        <tfoot>
                            <tr style="font-size: 14px; background:#f9f9f9;">
                                <td style="text-align:right; font-weight:bold; padding: 10px;">總分 (GRAND TOTAL):</td>
                                <td class="score-cell">${val.total || 0}</td>
                            </tr>
                        </tfoot>
                    </table>

                    <div class="footer">
                        This report was generated via the SJACS Admin System. Record ID: ${evalId}<br>
                        Timestamp: ${new Date(val.timestamp).toLocaleString()}
                    </div>
                </body>
                </html>
            `);
            printWindow.document.close();
        });
    }

    function updateCell(evalId, itemKey, newValue) {
        database.ref(`evaluations/${evalId}/scores/${itemKey}`).set(newValue).then(() => {
            database.ref(`evaluations/${evalId}/scores`).once('value', s => {
                const scores = s.val();
                const newTotal = Object.values(scores).reduce((a, b) => parseFloat(a || 0) + parseFloat(b || 0), 0);
                database.ref(`evaluations/${evalId}/total`).set(newTotal);
            });
        });
    }

    function deleteEntry(id) {
        if(confirm("Permanently delete?")) database.ref('evaluations/' + id).remove();
    }
</script>
</body>
</html>