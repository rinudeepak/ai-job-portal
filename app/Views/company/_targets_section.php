<?php if (session()->get('logged_in') && session()->get('role') === 'candidate'): ?>
<div id="myTargetCompanies" class="detail-card companies-targets-card mt-4">
    <div class="panel-header d-flex align-items-center">
        <i class="fas fa-star mr-2 text-warning"></i>
        <h5 class="mb-0">Your Target Companies</h5>
        <span id="targetsBadge" class="badge badge-light ml-2" style="display:none;"></span>
        <div class="ml-auto">
            <button id="refreshTargetsBtn" class="btn btn-sm btn-outline-primary">
                <i class="fas fa-sync-alt mr-1"></i>Refresh All
            </button>
            <button id="loadTargetsBtn" class="btn btn-sm btn-light" style="display:none;">
                <i class="fas fa-spinner fa-spin mr-1"></i>Loading...
            </button>
        </div>
    </div>
    <div class="panel-body">
        <div id="targetsContent">
            <p class="text-muted mb-0"><i class="fas fa-info-circle mr-1"></i>Search companies above to automatically add them here. Refresh live jobs from career pages anytime.</p>
        </div>
    </div>
</div>
<script>
$(function() {
    $(document).on('click', '#viewTargetsLink', function(e) {
        e.preventDefault();
        $('html, body').animate({
            scrollTop: $('#myTargetCompanies').offset().top - 100
        }, 500);
        $('#myTargetCompanies').addClass('shadow border-warning');
        setTimeout(() => $('#myTargetCompanies').removeClass('shadow border-warning'), 3000);
    });
});
</script>
<?php endif; ?>
