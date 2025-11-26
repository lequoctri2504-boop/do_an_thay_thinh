<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Nhân viên - PhoneShop</title>
    <link rel="stylesheet" href="{{('css/style.css')}}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="dashboard-body">
    <!-- Dashboard Header -->
    <header class="dashboard-header">
        <div class="dashboard-brand">
            <img src="{{('img/logo_LQT1.png')}}" alt="" width="50px">
            <span class="role-badge staff">Xin Chào Nhân viên</span>
        </div>
        <div class="dashboard-user">
            <div class="notifications">
                <button class="notif-btn">
                    <i class="fas fa-bell"></i>
                    <span class="badge">5</span>
                </button>
            </div>
            <div class="user-dropdown">
                <button class="user-btn">
                    <i class="fas fa-user-circle"></i>
                    <span>Trần Thị B</span>
                    <i class="fas fa-chevron-down"></i>
                </button>
                <div class="dropdown-menu">
                    <a href="#"><i class="fas fa-user"></i> Thông tin cá nhân</a>
                    <a href="#"><i class="fas fa-cog"></i> Cài đặt</a>
                    <a href="index.html"><i class="fas fa-sign-out-alt"></i> Đăng xuất</a>
                </div>
            </div>
        </div>
    </header>

    <div class="dashboard-container">
        <!-- Sidebar -->
        <aside class="dashboard-sidebar">
            <nav class="sidebar-nav">
                <a href="#" class="nav-item active" data-section="overview">
                    <i class="fas fa-chart-line"></i>
                    <span>Tổng quan</span>
                </a>
                <a href="#" class="nav-item" data-section="orders">
                    <i class="fas fa-shopping-bag"></i>
                    <span>Quản lý đơn hàng</span>
                    <span class="badge-count">12</span>
                </a>
                <a href="#" class="nav-item" data-section="products">
                    <i class="fas fa-box"></i>
                    <span>Quản lý sản phẩm</span>
                </a>
                <a href="#" class="nav-item" data-section="customers">
                    <i class="fas fa-users"></i>
                    <span>Quản lý khách hàng</span>
                </a>
                <a href="#" class="nav-item" data-section="reports">
                    <i class="fas fa-chart-bar"></i>
                    <span>Báo cáo - Thống kê</span>
                </a>
                <a href="#" class="nav-item" data-section="support">
                    <i class="fas fa-headset"></i>
                    <span>Hỗ trợ khách hàng</span>
                </a>
            </nav>
        </aside>

        <!-- Main Content -->
        <main class="dashboard-main">
            <!-- Overview Section -->
            <section class="dashboard-section active" id="overview">
                <div class="section-header">
                    <h1>Tổng quan</h1>
                    <div class="header-actions">
                        <select class="form-select">
                            <option>Hôm nay</option>
                            <option>7 ngày qua</option>
                            <option>30 ngày qua</option>
                            <option>Tháng này</option>
                        </select>
                    </div>
                </div>

                <!-- Stats Cards -->
                <div class="stats-grid">
                    <div class="stat-card">
                        <div class="">
                            <i class="fas fa-shopping-cart"></i>
                        </div>
                        <div class="stat-content">
                            <h3>Đơn hàng mới</h3>
                            <div class="stat-value">24</div>
                            <div class="stat-change positive">
                                <i class="fas fa-arrow-up"></i> 12% so với hôm qua
                            </div>
                        </div>
                    </div>

                    <div class="stat-card">
                        <div class="">
                            <i class="fas fa-clock"></i>
                        </div>
                        <div class="stat-content">
                            <h3>Đang xử lý</h3>
                            <div class="stat-value">12</div>
                            <div class="stat-change">
                                <i class="fas fa-minus"></i> Không đổi
                            </div>
                        </div>
                    </div>

                    <div class="stat-card">
                        <div class="">
                            <i class="fas fa-check-circle"></i>
                        </div>
                        <div class="stat-content">
                            <h3>Hoàn thành</h3>
                            <div class="stat-value">156</div>
                            <div class="stat-change positive">
                                <i class="fas fa-arrow-up"></i> 8% so với tuần trước
                            </div>
                        </div>
                    </div>

                    <div class="stat-card">
                        <div class="">
                            <i class="fas fa-times-circle"></i>
                        </div>
                        <div class="stat-content">
                            <h3>Đã hủy</h3>
                            <div class="stat-value">3</div>
                            <div class="stat-change negative">
                                <i class="fas fa-arrow-down"></i> 50% so với hôm qua
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Charts & Tables Row -->
                <div class="dashboard-row">
                    <!-- Recent Orders -->
                    <div class="dashboard-card col-8">
                        <div class="card-header">
                            <h3><i class="fas fa-shopping-bag"></i> Đơn hàng gần đây</h3>
                            <a href="#" class="view-all">Xem tất cả</a>
                        </div>
                        <div class="table-responsive">
                            <table class="data-table">
                                <thead>
                                    <tr>
                                        <th>Mã đơn</th>
                                        <th>Khách hàng</th>
                                        <th>Sản phẩm</th>
                                        <th>Tổng tiền</th>
                                        <th>Trạng thái</th>
                                        <th>Thao tác</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td><strong>#DH12356</strong></td>
                                        <td>Nguyễn Văn A</td>
                                        <td>iPhone 15 Pro Max</td>
                                        <td>28.990.000₫</td>
                                        <td><span class="status-badge processing">Đang xử lý</span></td>
                                        <td>
                                            <button class="btn-icon" title="Xem"><i class="fas fa-eye"></i></button>
                                            <button class="btn-icon" title="Xác nhận"><i class="fas fa-check"></i></button>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td><strong>#DH12355</strong></td>
                                        <td>Trần Thị B</td>
                                        <td>Samsung Galaxy S24</td>
                                        <td>26.990.000₫</td>
                                        <td><span class="status-badge shipping">Đang giao</span></td>
                                        <td>
                                            <button class="btn-icon" title="Xem"><i class="fas fa-eye"></i></button>
                                            <button class="btn-icon" title="Theo dõi"><i class="fas fa-truck"></i></button>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td><strong>#DH12354</strong></td>
                                        <td>Lê Văn C</td>
                                        <td>Xiaomi 14 Pro</td>
                                        <td>19.990.000₫</td>
                                        <td><span class="status-badge delivered">Hoàn thành</span></td>
                                        <td>
                                            <button class="btn-icon" title="Xem"><i class="fas fa-eye"></i></button>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td><strong>#DH12353</strong></td>
                                        <td>Phạm Thị D</td>
                                        <td>OPPO Find X7</td>
                                        <td>22.990.000₫</td>
                                        <td><span class="status-badge processing">Đang xử lý</span></td>
                                        <td>
                                            <button class="btn-icon" title="Xem"><i class="fas fa-eye"></i></button>
                                            <button class="btn-icon" title="Xác nhận"><i class="fas fa-check"></i></button>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td><strong>#DH12352</strong></td>
                                        <td>Hoàng Văn E</td>
                                        <td>iPhone 14 Pro</td>
                                        <td>24.990.000₫</td>
                                        <td><span class="status-badge cancelled">Đã hủy</span></td>
                                        <td>
                                            <button class="btn-icon" title="Xem"><i class="fas fa-eye"></i></button>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Quick Actions -->
                    <div class="dashboard-card col-4">
                        <div class="card-header">
                            <h3><i class="fas fa-bolt"></i> Thao tác nhanh</h3>
                        </div>
                        <div class="quick-actions">
                            <button class="">
                                <i class="fas fa-plus-circle"></i>
                                <span>Tạo đơn hàng mới</span>
                            </button>
                            <button class="">
                                <i class="fas fa-box"></i>
                                <span>Cập nhật tồn kho</span>
                            </button>
                            <button class="">
                                <i class="fas fa-user-plus"></i>
                                <span>Thêm khách hàng</span>
                            </button>
                            <button class="">
                                <i class="fas fa-file-alt"></i>
                                <span>Xuất báo cáo</span>
                            </button>
                        </div>

                        <div class="card-header mt-4">
                            <h3><i class="fas fa-exclamation-triangle"></i> Sản phẩm sắp hết</h3>
                        </div>
                        <div class="low-stock-list">
                            <div class="stock-item">
                                <div class="stock-info">
                                    <strong>iPhone 15 Pro Max 256GB</strong>
                                    <span class="stock-qty danger">Còn 3</span>
                                </div>
                            </div>
                            <div class="stock-item">
                                <div class="stock-info">
                                    <strong>Samsung Galaxy S24 Ultra</strong>
                                    <span class="stock-qty warning">Còn 8</span>
                                </div>
                            </div>
                            <div class="stock-item">
                                <div class="stock-info">
                                    <strong>OPPO Reno11 F 5G</strong>
                                    <span class="stock-qty warning">Còn 12</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Orders Section -->
            <section class="dashboard-section" id="orders">
                <div class="section-header">
                    <h1>Quản lý đơn hàng</h1>
                    <div class="header-actions">
                        <input type="text" class="form-control" placeholder="Tìm kiếm đơn hàng...">
                        <button class="btn btn-primary"><i class="fas fa-filter"></i> Lọc</button>
                    </div>
                </div>

                <div class="order-tabs-staff">
                    <button class="tab-btn active">Tất cả (195)</button>
                    <button class="tab-btn">Đang xử lý (12)</button>
                    <button class="tab-btn">Đang giao (45)</button>
                    <button class="tab-btn">Hoàn thành (135)</button>
                    <button class="tab-btn">Đã hủy (3)</button>
                </div>

                <div class="dashboard-card">
                    <div class="table-responsive">
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th><input type="checkbox"></th>
                                    <th>Mã đơn</th>
                                    <th>Khách hàng</th>
                                    <th>Sản phẩm</th>
                                    <th>Ngày đặt</th>
                                    <th>Tổng tiền</th>
                                    <th>Thanh toán</th>
                                    <th>Trạng thái</th>
                                    <th>Thao tác</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td><input type="checkbox"></td>
                                    <td><strong>#DH12356</strong></td>
                                    <td>Nguyễn Văn A<br><small>0987654321</small></td>
                                    <td>iPhone 15 Pro Max 256GB</td>
                                    <td>15/12/2024<br><small>10:30</small></td>
                                    <td><strong>28.990.000₫</strong></td>
                                    <td><span class="payment-badge cod">COD</span></td>
                                    <td><span class="status-badge processing">Đang xử lý</span></td>
                                    <td>
                                        <button class="btn btn-sm btn-success" title="Xác nhận"><i class="fas fa-check"></i></button>
                                        <button class="btn btn-sm btn-danger" title="Hủy"><i class="fas fa-times"></i></button>
                                    </td>
                                </tr>
                                <tr>
                                    <td><input type="checkbox"></td>
                                    <td><strong>#DH12355</strong></td>
                                    <td>Trần Thị B<br><small>0912345678</small></td>
                                    <td>Samsung Galaxy S24 Ultra</td>
                                    <td>15/12/2024<br><small>09:15</small></td>
                                    <td><strong>26.990.000₫</strong></td>
                                    <td><span class="payment-badge bank">Chuyển khoản</span></td>
                                    <td><span class="status-badge shipping">Đang giao</span></td>
                                    <td>
                                        <button class="btn btn-sm btn-primary" title="Theo dõi"><i class="fas fa-truck"></i></button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="table-pagination">
                        <div class="pagination-info">Hiển thị 1-10 trong 195 đơn hàng</div>
                        <div class="pagination-controls">
                            <button class="page-btn disabled"><i class="fas fa-chevron-left"></i></button>
                            <button class="page-btn active">1</button>
                            <button class="page-btn">2</button>
                            <button class="page-btn">3</button>
                            <button class="page-btn">...</button>
                            <button class="page-btn">20</button>
                            <button class="page-btn"><i class="fas fa-chevron-right"></i></button>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Products Section -->
            <section class="dashboard-section" id="products">
                <div class="section-header">
                    <h1>Quản lý sản phẩm</h1>
                    <div class="header-actions">
                        <input type="text" class="form-control" placeholder="Tìm kiếm sản phẩm...">
                        <button class="btn btn-secondary"><i class="fas fa-file-export"></i> Xuất Excel</button>
                    </div>
                </div>

                <div class="dashboard-card">
                    <div class="table-responsive">
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th>Hình ảnh</th>
                                    <th>Tên sản phẩm</th>
                                    <th>SKU</th>
                                    <th>Giá bán</th>
                                    <th>Tồn kho</th>
                                    <th>Trạng thái</th>
                                    <th>Thao tác</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td><img src="ip17promax.png" alt="Product" class="product-thumb"></td>
                                    <td><strong>iPhone 17 Pro Max 256GB</strong><br><small>Titan Đen</small></td>
                                    <td>IP15PM256</td>
                                    <td><strong>28.990.000₫</strong></td>
                                    <td><span class="stock-badge danger">3</span></td>
                                    <td><span class="status-badge active">Đang bán</span></td>
                                    <td>
                                        <button class="btn btn-sm btn-primary" title="Cập nhật"><i class="fas fa-edit"></i></button>
                                    </td>
                                </tr>
                                <tr>
                                    <td><img src="https://images.unsplash.com/photo-1610945415295-d9bbf067e59c?w=60&h=60&fit=crop" alt="Product" class="product-thumb"></td>
                                    <td><strong>Samsung Galaxy S24 Ultra</strong><br><small>Titanium Gray</small></td>
                                    <td>SS24U12</td>
                                    <td><strong>26.990.000₫</strong></td>
                                    <td><span class="stock-badge warning">8</span></td>
                                    <td><span class="status-badge active">Đang bán</span></td>
                                    <td>
                                        <button class="btn btn-sm btn-primary" title="Cập nhật"><i class="fas fa-edit"></i></button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </section>

            <!-- Customers Section -->
            <section class="dashboard-section" id="customers">
                <div class="section-header">
                    <h1>Quản lý khách hàng</h1>
                    <div class="header-actions">
                        <input type="text" class="form-control" placeholder="Tìm kiếm khách hàng...">
                    </div>
                </div>

                <div class="dashboard-card">
                    <div class="table-responsive">
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th>Khách hàng</th>
                                    <th>Email</th>
                                    <th>Số điện thoại</th>
                                    <th>Tổng đơn</th>
                                    <th>Tổng chi tiêu</th>
                                    <th>Ngày tạo</th>
                                    <th>Thao tác</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td><strong>Nguyễn Văn A</strong></td>
                                    <td>nguyenvana@email.com</td>
                                    <td>0987654321</td>
                                    <td>5</td>
                                    <td><strong>85.000.000₫</strong></td>
                                    <td>10/11/2024</td>
                                    <td>
                                        <button class="btn btn-sm btn-primary" title="Xem chi tiết"><i class="fas fa-eye"></i></button>
                                        <button class="btn btn-sm btn-success" title="Chat"><i class="fas fa-comment"></i></button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </section>

            <!-- Reports Section -->
            <section class="dashboard-section" id="reports">
                <div class="section-header">
                    <h1>Báo cáo - Thống kê</h1>
                    <div class="header-actions">
                        <select class="form-select">
                            <option>Tháng này</option>
                            <option>Tháng trước</option>
                            <option>Quý này</option>
                            <option>Năm nay</option>
                        </select>
                        <button class="btn btn-primary"><i class="fas fa-download"></i> Tải báo cáo</button>
                    </div>
                </div>

                <div class="stats-grid">
                    <div class="stat-card">
                        <div class="">
                            <i class="fas fa-money-bill-wave"></i>
                        </div>
                        <div class="stat-content">
                            <h3>Doanh thu tháng này</h3>
                            <div class="stat-value">1.250.000.000₫</div>
                            <div class="stat-change positive">
                                <i class="fas fa-arrow-up"></i> 15% so với tháng trước
                            </div>
                        </div>
                    </div>

                    <div class="stat-card">
                        <div class="">
                            <i class="fas fa-shopping-cart"></i>
                        </div>
                        <div class="stat-content">
                            <h3>Tổng đơn hàng</h3>
                            <div class="stat-value">195</div>
                            <div class="stat-change positive">
                                <i class="fas fa-arrow-up"></i> 12% so với tháng trước
                            </div>
                        </div>
                    </div>

                    <div class="stat-card">
                        <div class="">
                            <i class="fas fa-users"></i>
                        </div>
                        <div class="stat-content">
                            <h3>Khách hàng mới</h3>
                            <div class="stat-value">48</div>
                            <div class="stat-change positive">
                                <i class="fas fa-arrow-up"></i> 20% so với tháng trước
                            </div>
                        </div>
                    </div>

                    <div class="stat-card">
                        <div class="">
                            <i class="fas fa-chart-line"></i>
                        </div>
                        <div class="stat-content">
                            <h3>Giá trị TB đơn hàng</h3>
                            <div class="stat-value">6.410.000₫</div>
                            <div class="stat-change negative">
                                <i class="fas fa-arrow-down"></i> 5% so với tháng trước
                            </div>
                        </div>
                    </div>
                </div>

                <div class="dashboard-row">
                    <div class="dashboard-card col-12">
                        <div class="card-header">
                            <h3><i class="fas fa-chart-bar"></i> Top 10 sản phẩm bán chạy</h3>
                        </div>
                        <div class="table-responsive">
                            <table class="data-table">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Sản phẩm</th>
                                        <th>Đã bán</th>
                                        <th>Doanh thu</th>
                                        <th>Tỷ lệ</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>1</td>
                                        <td><strong>iPhone 15 Pro Max 256GB</strong></td>
                                        <td>45</td>
                                        <td>1.304.550.000₫</td>
                                        <td>
                                            <div class="progress-bar">
                                                <div class="progress-fill" style="width: 85%"></div>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>2</td>
                                        <td><strong>Samsung Galaxy S24 Ultra</strong></td>
                                        <td>38</td>
                                        <td>1.025.620.000₫</td>
                                        <td>
                                            <div class="progress-bar">
                                                <div class="progress-fill" style="width: 72%"></div>
                                            </div>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Support Section -->
            <section class="dashboard-section" id="support">
                <div class="section-header">
                    <h1>Hỗ trợ khách hàng</h1>
                </div>

                <div class="dashboard-row">
                    <div class="dashboard-card col-12">
                        <div class="support-container">
                            <div class="chat-list">
                                <div class="chat-header">
                                    <h3>Tin nhắn</h3>
                                    <input type="text" placeholder="Tìm kiếm...">
                                </div>
                                <div class="chat-item active">
                                    <div class="chat-avatar">N</div>
                                    <div class="chat-info">
                                        <strong>Nguyễn Văn A</strong>
                                        <p>Sản phẩm khi nào về hàng?</p>
                                    </div>
                                    <span class="chat-time">10:30</span>
                                </div>
                                <div class="chat-item">
                                    <div class="chat-avatar">T</div>
                                    <div class="chat-info">
                                        <strong>Trần Thị B</strong>
                                        <p>Cảm ơn bạn đã hỗ trợ!</p>
                                    </div>
                                    <span class="chat-time">09:15</span>
                                </div>
                            </div>
                            <div class="chat-box">
                                <div class="chat-box-header">
                                    <strong>Nguyễn Văn A</strong>
                                    <span>Online</span>
                                </div>
                                <div class="chat-messages">
                                    <div class="message customer">
                                        <p>Xin chào! Cho mình hỏi iPhone 15 Pro Max màu đen khi nào về hàng ạ?</p>
                                        <span class="message-time">10:28</span>
                                    </div>
                                    <div class="message staff">
                                        <p>Chào anh! Sản phẩm dự kiến về trong 3-5 ngày nữa ạ. Anh có muốn đặt trước không?</p>
                                        <span class="message-time">10:30</span>
                                    </div>
                                </div>
                                <div class="chat-input">
                                    <input type="text" placeholder="Nhập tin nhắn...">
                                    <button class="btn btn-primary"><i class="fas fa-paper-plane"></i></button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </main>
    </div>

    <script src="{{('main.js')}}"></script>
</body>
</html>