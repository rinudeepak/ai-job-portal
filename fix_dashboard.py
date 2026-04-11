f = open('app/Views/candidate/dashboard.php', 'rb')
content = f.read()
f.close()

# Find the clean cut point - right after </div>\n\n</div> closing the dashboard-jobboard div
# and before any script tags we added
cut_marker = b'<?php endif; ?>'
idx = content.find(cut_marker)
if idx == -1:
    print('cut marker not found')
else:
    # Keep everything up to and including <?php endif; ?>
    clean = content[:idx + len(cut_marker)]
    
    # Append clean footer + JS
    addition = b"""

<?= view('Layouts/candidate_footer') ?>

<script>
$(document).ready(function() {
    var fetchUrl = '<?= base_url("target-companies/fetch-jobs") ?>';
    var csrfName = '<?= csrf_token() ?>';
    var csrfHash = '<?= csrf_hash() ?>';

    $(document).on('click', '.load-jobs-btn', function() {
        var btn      = $(this);
        var company  = btn.data('company');
        var platform = btn.data('platform');
        var slug     = btn.data('slug');
        var targetId = btn.data('target');
        var limit    = btn.siblings('.job-limit-select').val() || 10;
        var container = $('#' + targetId);

        btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-1"></i> Loading...');
        container.html('<p class="text-muted small">Fetching live jobs from ' + company + '...</p>');

        var postData = { company_name: company, platform: platform, slug: slug, limit: limit };
        postData[csrfName] = csrfHash;

        $.ajax({
            url: fetchUrl, type: 'POST', data: postData, dataType: 'json',
            success: function(res) {
                btn.prop('disabled', false).html('<i class="fas fa-sync mr-1"></i> Refresh');
                if (!res.jobs || res.jobs.length === 0) {
                    container.html('<p class="text-muted small">No open positions found at ' + company + ' right now.</p>');
                    return;
                }
                var html = '<div class="table-responsive"><table class="table table-sm mb-0">';
                html += '<thead><tr><th>Role</th><th>Location</th><th>Department</th><th></th></tr></thead><tbody>';
                $.each(res.jobs, function(i, job) {
                    html += '<tr>';
                    html += '<td><strong>' + escHtml(job.title) + '</strong></td>';
                    html += '<td><i class="fas fa-map-pin mr-1 text-muted"></i>' + escHtml(job.location) + '</td>';
                    html += '<td><span class="badge badge-light">' + escHtml(job.department || '-') + '</span></td>';
                    html += '<td><a href="' + escHtml(job.apply_url) + '" target="_blank" rel="noopener" class="btn btn-sm btn-primary">';
                    html += '<i class="fas fa-external-link-alt mr-1"></i>View &amp; Apply</a></td>';
                    html += '</tr>';
                });
                html += '</tbody></table></div>';
                html += '<small class="text-muted">' + res.count + ' open position(s) &mdash; clicking opens the official ' + escHtml(company) + ' careers page.</small>';
                container.html(html);
            },
            error: function(xhr) {
                btn.prop('disabled', false).html('<i class="fas fa-sync mr-1"></i> Retry');
                container.html('<p class="text-danger small">Error ' + xhr.status + ': Failed to fetch jobs. Please try again.</p>');
            }
        });
    });

    // Autocomplete
    var $input = $('#companyNameInput');
    var $box   = $('#companySuggestBox');
    var allCompanies = [];
    $.get('<?= base_url("target-companies/suggest") ?>', function(data) { allCompanies = data || []; });

    $input.on('input', function() {
        var val = $(this).val().toLowerCase().trim();
        $box.empty();
        if (val.length < 1) { $box.hide(); return; }
        var matches = allCompanies.filter(function(c) { return c.toLowerCase().indexOf(val) !== -1; });
        if (matches.length === 0) { $box.hide(); return; }
        $.each(matches.slice(0, 8), function(i, name) {
            $('<div>').text(name).css({ padding: '8px 12px', cursor: 'pointer' })
                .hover(function() { $(this).css('background','#f0f4ff'); }, function() { $(this).css('background','#fff'); })
                .on('click', function() { $input.val(name); $box.hide(); })
                .appendTo($box);
        });
        $box.show();
    });

    $(document).on('click', function(e) {
        if (!$(e.target).closest('#companyNameInput,#companySuggestBox').length) $box.hide();
    });

    function escHtml(str) { return $('<div>').text(str || '').html(); }
});
</script>
"""
    
    open('app/Views/candidate/dashboard.php', 'wb').write(clean + addition)
    print('Done. File ends at byte:', len(clean + addition))
