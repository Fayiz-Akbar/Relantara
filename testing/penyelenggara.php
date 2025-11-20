<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Testing Suite - Penyelenggara | Relantara</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <div class="top-bar">
            <h1>Relantara Testing Suite</h1>
            <a href="relawan.php">Relawan</a>
            <a href="penyelenggara.php" class="active">Penyelenggara</a>
            <a href="admin.php">Admin</a>
        </div>

        <div class="main-content">
            <div class="left-panel">
                <div class="panel-title">Control Panel - Penyelenggara</div>

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
                            <option value="register_penyelenggara">Register Penyelenggara</option>
                            <option value="create_kegiatan">Create Kegiatan</option>
                            <option value="update_kegiatan">Update Kegiatan</option>
                            <option value="delete_kegiatan">Delete Kegiatan</option>
                            <option value="update_status_pendaftar">Update Status Pendaftar</option>
                            <option value="get_kegiatan_list">Get Kegiatan List</option>
                            <option value="get_pendaftar_list">Get Pendaftar List</option>
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
                <div class="panel-title">Live Database View - tbl_kegiatan</div>
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
            register_penyelenggara: [
                {name: 'nama_organisasi', label: 'Nama Organisasi', type: 'text', required: true},
                {name: 'email', label: 'Email', type: 'email', required: true},
                {name: 'password', label: 'Password', type: 'password', required: true},
                {name: 'confirm_password', label: 'Konfirmasi Password', type: 'password', required: true}
            ],
            create_kegiatan: [
                {name: 'id_penyelenggara', label: 'ID Penyelenggara', type: 'number', required: true},
                {name: 'judul', label: 'Judul Kegiatan', type: 'text', required: true},
                {name: 'deskripsi', label: 'Deskripsi', type: 'textarea', required: true},
                {name: 'benefit', label: 'Benefit', type: 'textarea', required: false},
                {name: 'lokasi', label: 'Lokasi', type: 'text', required: true},
                {name: 'kuota', label: 'Kuota', type: 'number', required: true},
                {name: 'tanggal_mulai', label: 'Tanggal Mulai', type: 'date', required: true},
                {name: 'tanggal_selesai', label: 'Tanggal Selesai', type: 'date', required: true},
                {name: 'gambar_poster', label: 'Poster Kegiatan', type: 'file', required: false, accept: 'image/*'}
            ],
            update_kegiatan: [
                {name: 'id_kegiatan', label: 'ID Kegiatan', type: 'number', required: true},
                {name: 'id_penyelenggara', label: 'ID Penyelenggara', type: 'number', required: true},
                {name: 'judul', label: 'Judul Kegiatan', type: 'text', required: true},
                {name: 'deskripsi', label: 'Deskripsi', type: 'textarea', required: true},
                {name: 'benefit', label: 'Benefit', type: 'textarea', required: false},
                {name: 'lokasi', label: 'Lokasi', type: 'text', required: true},
                {name: 'kuota', label: 'Kuota', type: 'number', required: true},
                {name: 'tanggal_mulai', label: 'Tanggal Mulai', type: 'date', required: true},
                {name: 'tanggal_selesai', label: 'Tanggal Selesai', type: 'date', required: true},
                {name: 'gambar_poster', label: 'Poster Kegiatan (Optional)', type: 'file', required: false, accept: 'image/*'}
            ],
            delete_kegiatan: [
                {name: 'id_kegiatan', label: 'ID Kegiatan', type: 'number', required: true},
                {name: 'id_penyelenggara', label: 'ID Penyelenggara', type: 'number', required: true}
            ],
            update_status_pendaftar: [
                {name: 'id_penyelenggara', label: 'ID Penyelenggara', type: 'number', required: true},
                {name: 'id_pendaftaran', label: 'ID Pendaftaran', type: 'number', required: true},
                {name: 'status', label: 'Status (Diterima/Ditolak)', type: 'text', required: true}
            ],
            get_kegiatan_list: [
                {name: 'id_penyelenggara', label: 'ID Penyelenggara', type: 'number', required: false}
            ],
            get_pendaftar_list: [
                {name: 'id_kegiatan', label: 'ID Kegiatan', type: 'number', required: true}
            ]
        };

        function updateFunction() {
            const method = document.getElementById('method').value;
            const functionSelect = document.getElementById('function');
            
            if (method === 'GET') {
                functionSelect.innerHTML = `
                    <option value="">-- Pilih Function --</option>
                    <option value="get_kegiatan_list">Get Kegiatan List</option>
                    <option value="get_pendaftar_list">Get Pendaftar List</option>
                `;
            } else {
                functionSelect.innerHTML = `
                    <option value="">-- Pilih Function --</option>
                    <option value="login">Login</option>
                    <option value="register_penyelenggara">Register Penyelenggara</option>
                    <option value="create_kegiatan">Create Kegiatan</option>
                    <option value="update_kegiatan">Update Kegiatan</option>
                    <option value="delete_kegiatan">Delete Kegiatan</option>
                    <option value="update_status_pendaftar">Update Status Pendaftar</option>
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
                const response = await fetch('db_viewer.php?table=tbl_kegiatan');
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
