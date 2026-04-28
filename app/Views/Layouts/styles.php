        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

<style>
   
body {
    background: #f4f7fb;
    font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
    color: #2d3748;
     padding-bottom: 70px; 
}

/* Navbar */
.navbar {
    border-radius: 14px;
    padding: 12px 16px;
}

/* Page Title */
h3 {
    font-weight: 700;
    letter-spacing: -0.3px;
}

/* Cards */
.card {
    border: none;
    border-radius: 18px;
    box-shadow: 0 6px 25px rgba(0,0,0,0.05);
    overflow: hidden;
}

.card:hover {
    transform: translateY(-3px);
    box-shadow: 0 6px 25px rgba(0,0,0,0.08);
}

.card-header {
    background: transparent;
    border-bottom: 1px solid #eee;
    font-weight: 600;
}

.alert-info {
    background: #e8f3ff;
    border: none;
    color: #1d4ed8;
    border-radius: 12px;
}

/* Table */
.table {
    font-size: 14px;
}

.table thead {
    background: #f8fafc;
    font-weight: 600;
    color: #6b7280;
}

.table th {
    border-bottom: 1px solid #e5e7eb;
    padding: 14px;
}

.table td {
    padding: 14px;
    vertical-align: middle;
}

.table tbody tr {
    transition: all 0.15s ease;
}

.table tbody tr:hover {
    background: #f9fbfd;
}

/* Badge */
.badge {
    padding: 6px 10px;
    border-radius: 8px;
    font-weight: 500;
    font-size: 12px;
}

/* Inputs */
.form-control {
    border-radius: 12px;
    border: 1px solid #e5e7eb;
    box-shadow: none !important; /* remove subtle top shadow */
    padding: 10px 12px;
    font-size: 14px;
    transition: 0.2s;
}

.form-control:focus {
    border-color: #4f46e5;
    box-shadow: 0 0 0 2px rgba(79,70,229,0.1);
}

/* Select */
.form-select {
    border-radius: 12px;
    border: 1px solid #e5e7eb;
}

/* Buttons */
.btn {
    border-radius: 12px;
    padding: 8px 14px;
    font-size: 14px;
    font-weight: 500;
    transition: 0.2s;
}

.btn-primary {
    background: #4f46e5;
    border: none;
}

.btn-primary:hover {
    background: #4338ca;
}

.btn-outline-danger {
    border-radius: 10px;
}

/* Search suggestions */
#suggestionsBox {
    border-radius: 12px;
    overflow: hidden;
    border: 1px solid #e5e7eb;
    margin-top: 4px;
}

#suggestionsBox a {
    font-size: 14px;
    padding: 10px 12px;
}

#suggestionsBox a:hover {
    background: #eef2ff;
}

/* Empty state */
.text-muted {
    color: #9ca3af !important;
}
.table thead.sticky-header {
    position: sticky;
    top: 0;
    z-index: 2;
    background: #f8fafc;
}

.table thead.sticky-header::before {
    content: "";
    position: absolute;
    top: -6px;
    left: 0;
    width: 100%;
    height: 6px;
    box-shadow: 0 -4px 8px rgba(0,0,0,0.08);
    pointer-events: none;
}
/* Footer spacing */
 .fixed-footer {
    position: fixed;
    bottom: 0;
    left: 0;
    width: 100%;
    z-index: 1000;
}

.footer-box {
    background: #ffffff;
    border-radius: 12px 12px 0 0;
    box-shadow: 0 -4px 20px rgba(0,0,0,0.05);
    font-size: 14px;
    color: #6c757d;
}
.filter-bar {
    background: #f5f7fb; /* same as body OR white */
    padding: 10px;
    border-radius: 12px;
    position: relative;
    z-index: 5;
}
#suggestionsBox {
    display: none;
    border: none !important;
}

#suggestionsBox.active {
    display: block;
}
</style>    