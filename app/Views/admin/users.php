<!DOCTYPE html>
<html>
<head>
    <title>User Management</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
     <?= view('Layouts/styles') ?>
</head>
<body class="bg-light">

<div class="container py-4">
<?= view('Layouts/header') ?>
    <h3 class="mb-4">User Management</h3>

    <!-- FILTER -->
    <form method="get" class="row g-2 mb-3 align-items-stretch">

    <div class="col-md-4 position-relative">
        <input type="text" 
               name="search" 
               id="searchInput"
               value="<?= esc($search) ?>"
               class="form-control h-100"
               placeholder="Search by name...">

        <div id="suggestionsBox" 
             class="list-group position-absolute w-100 shadow"
             style="z-index: 9999; background: #fff;">
        </div> 
    </div>

    <div class="col-md-3 d-flex">
        <select name="role" class="form-select h-100">
            <option value="">All Roles</option>
            <option value="candidate" <?= ($role=='candidate')?'selected':'' ?>>Candidate</option>
            <option value="recruiter" <?= ($role=='recruiter')?'selected':'' ?>>Recruiter</option>
        </select>
    </div>

    <div class="col-md-2 d-flex">
        <button class="btn btn-primary w-100 h-100">Filter</button>
    </div>

</form>

    <!-- TABLE -->
   <div class="card shadow-sm">
    <div class="table-responsive" style="max-height: 900px; overflow-y: auto;">
        
        <table class="table table-bordered mb-0">
            <thead class="table-light" style="position: sticky; top: 0; z-index: 2;">
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Phone</th>
                    <th>Role</th>
                    <th>Created</th>
                </tr>
            </thead>

            <tbody>
                <?php if(empty($users)): ?>
                    <tr><td colspan="6" class="text-center text-muted">No users found</td></tr>
                <?php else: ?>
                    <?php foreach($users as $u): ?>
                        <tr>
                            <td><?= $u['id'] ?></td>
                            <td><?= esc($u['name']) ?></td>
                            <td><?= esc($u['email']) ?></td>
                            <td><?= esc($u['phone']) ?></td>
                            <td>
                                <span class="badge bg-<?= $u['role']=='recruiter' ? 'primary' : 'secondary' ?>">
                                    <?= ucfirst($u['role']) ?>
                                </span>
                            </td>
                            <td><?= $u['created_at'] ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>

        </table>

    </div>
</div>

</div>

<!-- JS -->
<script>
const input = document.getElementById('searchInput');
const box = document.getElementById('suggestionsBox');

input.addEventListener('keyup', function() {
    let value = this.value;

    if(value.length < 2){
        box.innerHTML = '';
        return;
    }

    fetch("<?= base_url('admin/users/suggestions') ?>?term=" + value)
    .then(res => res.json())
    .then(data => {

        box.innerHTML = '';

        data.forEach(item => {
            let el = document.createElement('a');
            el.classList.add('list-group-item', 'list-group-item-action');
            el.innerText = item.name;

            el.onclick = function(){
                input.value = item.name;
                box.innerHTML = '';
            };

            box.appendChild(el);
        });
    });
});

// Hide suggestions on click outside
document.addEventListener('click', function(e){
    if(!input.contains(e.target)){
        box.innerHTML = '';
    }
});
</script>
<?= view('Layouts/footer') ?>
</body>
</html>