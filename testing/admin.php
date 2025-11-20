<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Testing Suite - Admin | Relantara</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <div class="top-bar">
            <h1>Relantara Testing Suite</h1>
            <a href="relawan.php">Relawan</a>
            <a href="penyelenggara.php">Penyelenggara</a>
            <a href="admin.php" class="active">Admin</a>
        </div>

        <div class="main-content">
            <div class="left-panel">
                <div class="panel-title">Control Panel - Admin</div>

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
                            <option value="verify_penyelenggara">Verify Penyelenggara</option>
                            <option value="update_status_kegiatan">Update Status Kegiatan</option>
                            <option value="get_penyelenggara_pending">Get Penyelenggara Pending</option>
                            <option value="get_kegiatan_pending">Get Kegiatan Pending</option>
                            <option value="get_all_users">Get All Users</option>
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
                <div class="panel-title">Live Database View - tbl_penyelenggara</div>
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
            verify_penyelenggara: [
                {name: 'id_penyelenggara', label: 'ID Penyelenggara', type: 'number', required: true},
                {name: 'status_baru', label: 'Status (Verified/Rejected)', type: 'text', required: true}
            ],
            update_status_kegiatan: [
                {name: 'id_kegiatan', label: 'ID Kegiatan', type: 'number', required: true},
                {name: 'status_baru', label: 'Status (Published/Rejected)', type: 'text', required: true}
            ]
        };

        function updateFunction() {
            const method = document.getElementById('method').value;
            const functionSelect = document.getElementById('function');
            
            if (method === 'GET') {
                functionSelect.innerHTML = `
                    <option value="">-- Pilih Function --</option>
                    <option value="get_penyelenggara_pending">Get Penyelenggara Pending</option>
                    <option value="get_kegiatan_pending">Get Kegiatan Pending</option>
                    <option value="get_all_users">Get All Users</option>
                `;
            } else {
                functionSelect.innerHTML = `
                    <option value="">-- Pilih Function --</option>
                    <option value="login">Login</option>
                    <option value="verify_penyelenggara">Verify Penyelenggara</option>
                    <option value="update_status_kegiatan">Update Status Kegiatan</option>
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
                const response = await fetch('db_viewer.php?table=tbl_penyelenggara');
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
