<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hệ Thống Linh Kiện Điện Tử</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    {{-- Chống lưu cache trang khi bấm nút Back --}}
    <script>
        window.addEventListener("pageshow", function (event) {
            if (event.persisted) {
                window.location.reload();
            }
        });
    </script>
</head>
<body>
    {{-- Thanh điều hướng --}}
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark mb-4">
        <div class="container">
            <a class="navbar-brand" href="{{ route('welcome') }}">Linh Kiện Shop</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto align-items-center">
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('categories.index') }}">Danh mục linh kiện</a>
                    </li>

                    @auth
                        @if(Auth::user()->role === 'admin')
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('admin.dashboard') }}">Dashboard</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('admin.users.index') }}">Người dùng</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('admin.orders.index') }}">Đơn hàng</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('admin.sales.index') }}">Doanh số</a>
                            </li>
                        @endif
                    @endauth
                </ul>

                <ul class="navbar-nav ms-auto align-items-center">
                    @auth
                        <li class="nav-item">
                            <span class="nav-link text-light">Xin chào, {{ Auth::user()->name }}</span>
                        </li>
                        <li class="nav-item">
                            <form action="{{ route('logout') }}" method="POST" class="d-inline">
                                @csrf
                                <button type="submit" class="btn btn-link nav-link text-danger" style="text-decoration: none;">Đăng xuất</button>
                            </form>
                        </li>
                    @endauth
                    @guest
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('login') }}">Đăng nhập</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('register') }}">Đăng ký</a>
                        </li>
                    @endguest
                </ul>
            </div>
        </div>
    </nav>

    {{-- Nội dung chính --}}
    <div class="container">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @yield('content')
    </div>

    {{-- LIVECHAT POPUP --}}
    @auth
        @if(Auth::user()->role === 'admin')
            {{-- Giao diện Chat dành cho ADMIN --}}
            <div id="admin-chat-box" style="position: fixed; bottom: 20px; right: 20px; z-index: 9999;">
                <button id="admin-chat-toggle" class="btn btn-dark shadow p-3 rounded-circle">💬 Chat KH</button>

                <div id="admin-chat-popup" class="card shadow-lg" style="display: none; width: 500px; height: 450px;">
                    <div class="card-header bg-dark text-white d-flex justify-content-between align-items-center">
                        <strong>Hỗ trợ trực tuyến (Admin)</strong>
                        <button id="admin-chat-close" class="btn btn-sm btn-light">X</button>
                    </div>
                    <div class="card-body p-0 d-flex" style="height: 340px;">
                        <div id="user-list" class="border-end overflow-auto" style="width: 35%; background: #f8f9fa;">
                            <div class="p-2 text-center text-muted"><small>Đang tải danh sách...</small></div>
                        </div>
                        <div id="admin-chat-messages" class="p-3 overflow-auto" style="width: 65%;">
                            <div class="text-center mt-5 text-muted"><small>Chọn một khách hàng để xem tin nhắn</small></div>
                        </div>
                    </div>
                    <div class="card-footer bg-white">
                        <div class="input-group">
                            <input type="text" id="admin-chat-input" class="form-control form-control-sm" placeholder="Nhập câu trả lời..." autocomplete="off">
                            <button id="admin-send-btn" class="btn btn-success btn-sm">Gửi</button>
                        </div>
                    </div>
                </div>
            </div>
        @else
            {{-- Giao diện Chat dành cho USER THƯỜNG --}}
            <div id="user-chat-box" style="position: fixed; bottom: 20px; right: 20px; z-index: 9999;">
                <button id="user-chat-toggle" class="btn btn-primary rounded-circle shadow p-3">💬 Chat</button>

                <div id="user-chat-popup" class="card shadow-lg" style="display: none; width: 320px; border-radius: 10px;">
                    <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                        <span>Hỗ trợ khách hàng</span>
                        <button id="user-chat-close" class="btn btn-sm btn-light">X</button>
                    </div>
                    <div id="user-chat-messages" class="card-body overflow-auto" style="height: 300px;">
                        <div class="text-center text-muted mt-3"><small>Đang tải lịch sử...</small></div>
                    </div>
                    <div class="card-footer bg-white">
                        <div class="input-group">
                            <input type="text" id="user-chat-input" class="form-control" placeholder="Nhập tin nhắn..." autocomplete="off">
                            <button id="user-send-btn" class="btn btn-success">Gửi</button>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    @endauth

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    {{-- SCRIPT XỬ LÝ SỰ KIỆN LIVECHAT --}}
    @auth
    <script>
    document.addEventListener("DOMContentLoaded", function () {
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

        @if(Auth::user()->role === 'admin')
            /* ================= LOGIC CHAT CHO ADMIN ================= */
let currentUserId = null;
const adminPopup = document.getElementById("admin-chat-popup");
const adminToggle = document.getElementById("admin-chat-toggle");
const adminClose = document.getElementById("admin-chat-close");
const adminMessages = document.getElementById("admin-chat-messages");
const adminInput = document.getElementById("admin-chat-input");
const adminSendBtn = document.getElementById("admin-send-btn");

adminToggle.onclick = () => {
    adminPopup.style.display = "block";
    adminToggle.style.display = "none";
    loadUsers(true); // Tham số true để tự động chọn user đầu tiên khi mở
};

adminClose.onclick = () => {
    adminPopup.style.display = "none";
    adminToggle.style.display = "block";
};

function loadUsers(autoSelectFirst = false) {
    fetch("{{ route('admin.chat.users') }}", {
        headers: { 
            "Accept": "application/json",
            "X-CSRF-TOKEN": csrfToken 
        }
    })
    .then(res => res.json())
    .then(users => {
        let html = "";
        if (Array.isArray(users) && users.length > 0) {
            users.forEach((user, index) => {
                let activeClass = (currentUserId == user.id) ? 'bg-primary text-white' : '';
                html += `
                    <div class="user-item p-2 border-bottom ${activeClass}" 
                         style="cursor: pointer;" 
                         onclick="selectUser(${user.id}, this)">
                        <small><strong>${user.name}</strong></small>
                    </div>
                `;
            });

            // Nếu chưa chọn user nào mà có tham số autoSelectFirst, tự chọn user đầu tiên
            if ((!currentUserId || autoSelectFirst) && users.length > 0) {
                currentUserId = users[0].id;
                loadAdminMessages();
            }
        } else {
            html = '<div class="p-2 text-muted text-center"><small>Chưa có cuộc trò chuyện nào</small></div>';
        }
        document.getElementById("user-list").innerHTML = html;
    })
    .catch(err => {
        console.error("Lỗi tải danh sách user:", err);
        document.getElementById("user-list").innerHTML = '<div class="p-2 text-danger"><small>Lỗi tải danh sách</small></div>';
    });
}

window.selectUser = function(userId, element) {
    currentUserId = userId;
    document.querySelectorAll('.user-item').forEach(el => el.classList.remove('bg-primary', 'text-white'));
    if (element) element.classList.add('bg-primary', 'text-white');
    loadAdminMessages();
};

function loadAdminMessages() {
    if (!currentUserId) return;

    fetch(`/admin/chat/messages/${currentUserId}`, {
        headers: { 
            "Accept": "application/json",
            "X-CSRF-TOKEN": csrfToken 
        }
    })
    .then(res => res.json())
    .then(messages => {
        let html = "";
        if (Array.isArray(messages) && messages.length > 0) {
            messages.forEach(msg => {
                let isMe = msg.sender_id == "{{ Auth::id() }}";
                html += `
                    <div class="msg-row mb-2 ${isMe ? 'text-end' : 'text-start'}">
                        <small style="font-size: 0.85rem;">
                            <strong>${isMe ? 'Bạn' : 'Khách hàng'}:</strong> ${msg.content}
                        </small>
                    </div>
                `;
            });
        } else {
            html = '<div class="text-center mt-5 text-muted"><small>Chưa có tin nhắn nào</small></div>';
        }
        adminMessages.innerHTML = html;
        adminMessages.scrollTop = adminMessages.scrollHeight;
    })
    .catch(err => console.error("Lỗi tải tin nhắn Admin:", err));
}

function sendAdminMessage() {
    let message = adminInput.value.trim();
    if (!message || !currentUserId) return;

    fetch("{{ route('admin.chat.send') }}", {
        method: "POST",
        headers: {
            "Content-Type": "application/json",
            "Accept": "application/json",
            "X-CSRF-TOKEN": csrfToken
        },
        body: JSON.stringify({ message: message, user_id: currentUserId })
    })
    .then(res => res.json())
    .then(data => {
        adminInput.value = "";
        loadAdminMessages();
    })
    .catch(err => console.error("Lỗi gửi tin Admin:", err));
}

adminSendBtn.onclick = sendAdminMessage;
adminInput.addEventListener("keypress", (e) => { if (e.key === 'Enter') sendAdminMessage(); });

// Tự động làm mới danh sách và tin nhắn mỗi 3 giây
setInterval(() => {
    if (adminPopup.style.display === "block") {
        loadUsers(false);
        if (currentUserId) {
            loadAdminMessages();
        }
    }
}, 3000);

        @else
            /* ================= LOGIC CHAT CHO USER THƯỜNG ================= */
            const userToggle = document.getElementById("user-chat-toggle");
            const userPopup = document.getElementById("user-chat-popup");
            const userClose = document.getElementById("user-chat-close");
            const userSendBtn = document.getElementById("user-send-btn");
            const userInput = document.getElementById("user-chat-input");
            const userMessagesBox = document.getElementById("user-chat-messages");

            userToggle.onclick = () => {
                userPopup.style.display = "block";
                userToggle.style.display = "none";
                loadUserMessages();
            };

            userClose.onclick = () => {
                userPopup.style.display = "none";
                userToggle.style.display = "block";
            };

            function loadUserMessages() {
                fetch("{{ route('user.chat.messages') }}", {
                    headers: {
                        "Accept": "application/json",
                        "X-CSRF-TOKEN": csrfToken
                    }
                })
                .then(res => {
                    if (!res.ok) {
                        throw new Error("Mã lỗi HTTP: " + res.status);
                    }
                    return res.json();
                })
                .then(messages => {
                    let html = "";
                    if (!Array.isArray(messages) || messages.length === 0) {
                        html = "<div class='text-center text-muted mt-3'><small>Bắt đầu cuộc trò chuyện với Admin</small></div>";
                    } else {
                        messages.forEach(msg => {
                            const isMe = msg.sender_id == "{{ Auth::id() }}";
                            html += `
                                <div class="message-row ${isMe ? 'text-end mb-2' : 'text-start mb-2'}">
                                    <small><strong>${isMe ? 'Bạn' : 'Admin'}:</strong> ${msg.content}</small>
                                </div>
                            `;
                        });
                    }
                    userMessagesBox.innerHTML = html;
                    userMessagesBox.scrollTop = userMessagesBox.scrollHeight;
                })
                .catch(err => {
                    console.error("Lỗi tải tin nhắn User:", err);
                    userMessagesBox.innerHTML = "<div class='text-center text-danger mt-3'><small>Lỗi tải tin nhắn. Hãy thử lại!</small></div>";
                });
            }

            function sendUserMessage() {
                let message = userInput.value.trim();
                if (message === "") return;

                userInput.disabled = true;
                userSendBtn.disabled = true;

                fetch("{{ route('user.chat.send') }}", {
                    method: "POST",
                    headers: {
                        "X-CSRF-TOKEN": csrfToken,
                        "Content-Type": "application/json",
                        "Accept": "application/json"
                    },
                    body: JSON.stringify({ message: message })
                })
                .then(res => {
                    if (!res.ok) {
                        throw new Error("Lỗi gửi tin nhắn HTTP: " + res.status);
                    }
                    return res.json();
                })
                .then(data => {
                    userInput.value = "";
                    userInput.disabled = false;
                    userSendBtn.disabled = false;
                    userInput.focus();
                    loadUserMessages();
                })
                .catch(err => {
                    console.error("Lỗi gửi tin User:", err);
                    userInput.disabled = false;
                    userSendBtn.disabled = false;
                    alert("Gửi tin nhắn thất bại!");
                });
            }

            userSendBtn.onclick = sendUserMessage;
            userInput.addEventListener("keypress", function (e) {
                if (e.key === "Enter") sendUserMessage();
            });

            setInterval(() => {
                if (userPopup.style.display === "block") {
                    loadUserMessages();
                }
            }, 3000);
        @endif
    });
    </script>
    @endauth
</body>
</html>