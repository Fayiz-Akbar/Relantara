<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Testing Suite - Relawan | Relantara</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <div class="top-bar">
            <h1>Relantara Testing Suite</h1>
            <a href="relawan.php" class="active">Relawan</a>
            <a href="penyelenggara.php">Penyelenggara</a>
            <a href="admin.php">Admin</a>
        </div>

        <div class="main-content">
            <div class="left-panel">
                <div class="panel-title">Control Panel - Relawan</div>

                <div class="session-info" id="sessionInfo" style="display:none;">
                    <div class="label">Logged In As</div>
                    <div class="value" id="sessionUser"></div>
                    <button class="btn-logout" onclick="logout()">Logout</button>
                </div>

                <form id="testForm" onsubmit="sendRequest(event)">
                    <div class="form-group">
                        <label>HTTP Method</label>
                        <select id="method" onchange="updateFunction()">
                            <option value="POST">POST</option>
                            <option value="GET">GET</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Function</label>
                        <select id="function" onchange="updateInputs()">
                            <option value="">-- Pilih Function --</option>
                            <option value="login">Login</option>
                            <option value="register_relawan">Register Relawan</option>
                            <option value="update_profil_relawan">Update Profil</option>
                            <option value="apply_kegiatan">Apply Kegiatan</option>
                            <option value="get_relawan_data">Get Data Relawan</option>
                            <option value="get_riwayat_relawan">Get Riwayat Pendaftaran</option>
                            <option value="logout">Logout</option>
                        </select>
                    </div>

                    <div id="dynamicInputs"></div>

                    <button type="submit" class="btn-send" id="btnSend">Send Request</button>
                </form>
            </div>

            <div class="right-panel">
                <div class="panel-title">Response Viewer</div>
                <div class="response-container">
                    <div class="response-header">
                        <span class="response-status" id="responseStatus">Waiting...</span>
                        <span class="response-time" id="responseTime"></span>
                    </div>
                    <div class="response-body">
                        <pre id="responseBody">No response yet. Select a function and send a request.</pre>
                    </div>
                </div>
            </div>

            <div class="bottom-panel">
                <div class="panel-title">Live Database View - tbl_relawan</div>
                <div id="databaseView" class="loading">Loading database...</div>
            </div>
        </div>
    </div>

    <script>
        const inputConfigs = {
            login: [
                {name: 'email_username', label: 'Email/Username', type: 'text', required: true},
                {name: 'password', label: 'Password', type: 'password', required: true}
            ],
            register_relawan: [
                {name: 'nama_lengkap', label: 'Nama Lengkap', type: 'text', required: true},
                {name: 'email', label: 'Email', type: 'email', required: true},
                {name: 'password', label: 'Password', type: 'password', required: true},
                {name: 'confirm_password', label: 'Konfirmasi Password', type: 'password', required: true}
            ],
            update_profil_relawan: [
                {name: 'id_relawan', label: 'ID Relawan', type: 'number', required: true},
                {name: 'nama_lengkap', label: 'Nama Lengkap', type: 'text', required: true},
                {name: 'bio', label: 'Bio', type: 'textarea', required: false},
                {name: 'keahlian', label: 'Keahlian', type: 'text', required: false},
                {name: 'foto_profil', label: 'Foto Profil', type: 'file', required: false, accept: 'image/*'}
            ],
            apply_kegiatan: [
                {name: 'id_relawan', label: 'ID Relawan', type: 'number', required: true},
                {name: 'id_kegiatan', label: 'ID Kegiatan', type: 'number', required: true}
            ],
            get_relawan_data: [
                {name: 'id_relawan', label: 'ID Relawan', type: 'number', required: true}
            ],
            get_riwayat_relawan: [
                {name: 'id_relawan', label: 'ID Relawan', type: 'number', required: true}
            ]
        };

        function updateFunction() {
            const method = document.getElementById('method').value;
            const functionSelect = document.getElementById('function');
            
            if (method === 'GET') {
                functionSelect.innerHTML = `
                    <option value="">-- Pilih Function --</option>
                    <option value="get_relawan_data">Get Data Relawan</option>
                    <option value="get_riwayat_relawan">Get Riwayat Pendaftaran</option>
                `;
            } else {
                functionSelect.innerHTML = `
                    <option value="">-- Pilih Function --</option>
                    <option value="login">Login</option>
                    <option value="register_relawan">Register Relawan</option>
                    <option value="update_profil_relawan">Update Profil</option>
                    <option value="apply_kegiatan">Apply Kegiatan</option>
                    <option value="logout">Logout</option>
                `;
            }
            updateInputs();
        }

        function updateInputs() {
            const selectedFunction = document.getElementById('function').value;
            const dynamicInputsContainer = document.getElementById('dynamicInputs');
            
            if (!selectedFunction || !inputConfigs[selectedFunction]) {
                dynamicInputsContainer.innerHTML = '';
                return;
            }

            const inputs = inputConfigs[selectedFunction];
            let html = '';

            inputs.forEach(input => {
                if (input.type === 'textarea') {
                    html += `
                        <div class="form-group">
                            <label>${input.label}${input.required ? ' *' : ''}</label>
                            <textarea name="${input.name}" ${input.required ? 'required' : ''}></textarea>
                        </div>
                    `;
                } else if (input.type === 'file') {
                    html += `
                        <div class="form-group">
                            <label>${input.label}${input.required ? ' *' : ''}</label>
                            <input type="file" name="${input.name}" ${input.accept ? 'accept="' + input.accept + '"' : ''} ${input.required ? 'required' : ''}>
                        </div>
                    `;
                } else {
                    html += `
                        <div class="form-group">
                            <label>${input.label}${input.required ? ' *' : ''}</label>
                            <input type="${input.type}" name="${input.name}" ${input.required ? 'required' : ''}>
                        </div>
                    `;
                }
            });

            dynamicInputsContainer.innerHTML = html;
        }

        async function sendRequest(e) {
            e.preventDefault();
            
            const method = document.getElementById('method').value;
            const action = document.getElementById('function').value;
            
            if (!action) {
                alert('Pilih function terlebih dahulu!');
                return;
            }

            const btnSend = document.getElementById('btnSend');
            btnSend.disabled = true;
            btnSend.textContent = 'Sending...';

            const formData = new FormData(e.target);
            formData.append('action', action);

            try {
                let response;
                if (method === 'GET') {
                    const params = new URLSearchParams(formData);
                    response = await fetch(`api_handler.php?${params.toString()}`, {
                        method: 'GET'
                    });
                } else {
                    response = await fetch('api_handler.php', {
                        method: 'POST',
                        body: formData
                    });
                }

                const result = await response.json();
                displayResponse(result);
                
                if (action === 'login' && result.status === 'success') {
                    updateSessionInfo(result.data);
                } else if (action === 'logout') {
                    clearSessionInfo();
                }
                
                loadDatabaseView();
                
            } catch (error) {
                displayResponse({
                    status: 'error',
                    message: 'Network Error: ' + error.message,
                    data: null,
                    execution_time: null
                });
            } finally {
                btnSend.disabled = false;
                btnSend.textContent = 'Send Request';
            }
        }

        function displayResponse(result) {
            const statusEl = document.getElementById('responseStatus');
            const timeEl = document.getElementById('responseTime');
            const bodyEl = document.getElementById('responseBody');

            statusEl.textContent = result.status.toUpperCase();
            statusEl.className = 'response-status ' + result.status;
            
            timeEl.textContent = result.execution_time || '';
            
            bodyEl.textContent = JSON.stringify(result, null, 2);
        }

        function updateSessionInfo(data) {
            const sessionInfo = document.getElementById('sessionInfo');
            const sessionUser = document.getElementById('sessionUser');
            
            sessionUser.textContent = `${data.nama} (${data.role})`;
            sessionInfo.style.display = 'block';
        }

        function clearSessionInfo() {
            document.getElementById('sessionInfo').style.display = 'none';
        }

        async function logout() {
            const formData = new FormData();
            formData.append('action', 'logout');
            
            const response = await fetch('api_handler.php', {
                method: 'POST',
                body: formData
            });
            
            const result = await response.json();
            displayResponse(result);
            clearSessionInfo();
            loadDatabaseView();
        }

        async function loadDatabaseView() {
            const dbView = document.getElementById('databaseView');
            dbView.innerHTML = '<div class="loading">Loading database...</div>';

            try {
                const response = await fetch('../config/db_connect.php');
                
                const fetchResponse = await fetch('api_handler.php?action=get_relawan_data&id_relawan=0');
                
                const conn = new XMLHttpRequest();
                conn.open('GET', 'db_viewer.php?table=tbl_relawan', true);
                conn.onload = function() {
                    if (conn.status === 200) {
                        dbView.innerHTML = conn.responseText;
                    } else {
                        fetchTableData();
                    }
                };
                conn.onerror = function() {
                    fetchTableData();
                };
                conn.send();
                
            } catch (error) {
                fetchTableData();
            }
        }

        async function fetchTableData() {
            const dbView = document.getElementById('databaseView');
            
            try {
                const response = await fetch('db_viewer.php?table=tbl_relawan');
                const html = await response.text();
                dbView.innerHTML = html;
            } catch (error) {
                dbView.innerHTML = '<div class="empty-state">Failed to load database view</div>';
            }
        }

        loadDatabaseView();
        setInterval(loadDatabaseView, 5000);
    </script>
</body>
</html>
