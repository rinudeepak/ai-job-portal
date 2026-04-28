<!DOCTYPE html>
<html>
<head>
    <title>Jobs Management</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <?= view('Layouts/styles') ?>
</head>

<body class="bg-light">

<div class="container py-4">

<?= view('Layouts/header') ?>

<h3 class="mb-4 fw-bold">Jobs</h3>

<!-- FILTER -->
<form method="get" class="row g-2 mb-3 align-items-stretch">

    <div class="col-md-4 position-relative">
        <input type="text" 
               name="search" 
               id="searchInput"
               value="<?= esc($search) ?>"
               class="form-control"
               placeholder="Search job title...">

        <!-- Suggestions -->
        <div id="suggestionsBox" 
             class="list-group position-absolute w-100"
             style="z-index:9999; display:none;">
        </div>
    </div>

    <div class="col-md-2 d-flex">
        <button class="btn btn-primary w-100">Search</button>
    </div>

</form>

<!-- TABLE -->
<div class="card shadow-sm">
<div class="table-responsive" style="max-height:450px; overflow-y:auto;">

<table class="table mb-0">
<thead class="table-light sticky-header">
<tr>
    <th>ID</th>
    <th>Title</th>
    <th>Company</th>
    <th>Location</th>
    <th>Status</th>
</tr>
</thead>

<tbody>
<?php foreach($jobs as $j): ?>
<tr class="job-row" data-id="<?= $j['id'] ?>" style="cursor:pointer;">
    <td><?= $j['id'] ?></td>
    <td><?= esc($j['title']) ?></td>
    <td><?= esc($j['company']) ?></td>
    <td><?= esc($j['location']) ?></td>
    <td>
        <span class="badge bg-<?= $j['status']=='open'?'success':'secondary' ?>">
            <?= ucfirst($j['status']) ?>
        </span>
    </td>
</tr>
<?php endforeach; ?>
</tbody>

</table>
</div>
</div>

</div>

<!-- MODAL -->
<div class="modal fade" id="jobModal">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4">

            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold">Job Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body" id="jobDetails">
                Loading...
            </div>

        </div>
    </div>
</div>

<!-- JS -->
<script>
const input = document.getElementById('searchInput');
const box = document.getElementById('suggestionsBox');

// Suggestions
input.addEventListener('keyup', function() {
    let val = this.value;

    if(val.length < 2){
        box.style.display = 'none';
        box.innerHTML = '';
        return;
    }

    fetch("<?= base_url('admin/jobs/suggestions') ?>?term=" + val)
    .then(res => res.json())
    .then(data => {
        box.innerHTML = '';

        if(data.length === 0){
            box.style.display = 'none';
            return;
        }

        box.style.display = 'block';

        data.forEach(item => {
            let el = document.createElement('a');
            el.className = 'list-group-item list-group-item-action';
            el.innerText = item.title;

            el.onclick = () => {
                input.value = item.title;
                box.style.display = 'none';
            };

            box.appendChild(el);
        });
    });
});

// Hide suggestions
document.addEventListener('click', function(e){
    if(!input.contains(e.target)){
        box.style.display = 'none';
    }
});

// Click row → modal
document.querySelectorAll('.job-row').forEach(row => {
    row.addEventListener('click', function(){
        let id = this.dataset.id;

        fetch("<?= base_url('admin/job') ?>/" + id)
        .then(res => res.json())
        .then(data => {

            let html = `
                <h4 class="fw-bold mb-2">${data.title}</h4>
                <p class="text-muted">${data.company} • ${data.location}</p>

                <hr>

                <p><b>Experience:</b> ${data.experience_level ?? '-'}</p>
                <p><b>Salary:</b> ${data.salary_range ?? '-'}</p>
                <p><b>Status:</b> ${data.status}</p>

                <p class="mt-3"><b>Description:</b><br>${data.description ?? '-'}</p>

                <p><b>Skills:</b><br>${data.required_skills ?? '-'}</p>
            `;

            document.getElementById('jobDetails').innerHTML = html;

            new bootstrap.Modal(document.getElementById('jobModal')).show();
        });
    });
});
</script>

<?= view('Layouts/footer') ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>