<!-- Simplified Language Switcher Component -->
<!-- File: app/Views/components/language_switcher_simple.php -->

<?php
$currentLang = session()->get('user_language') ?? 'en';

$languages = [
    'en' => ['name' => 'English', 'native_name' => 'English', 'flag' => '🇬🇧'],
    'hi' => ['name' => 'Hindi', 'native_name' => 'हिन्दी', 'flag' => '🇮🇳'],
    'ta' => ['name' => 'Tamil', 'native_name' => 'தமிழ்', 'flag' => '🇮🇳'],
    'te' => ['name' => 'Telugu', 'native_name' => 'తెలుగు', 'flag' => '🇮🇳'],
    'kn' => ['name' => 'Kannada', 'native_name' => 'ಕನ್ನಡ', 'flag' => '🇮🇳'],
    'ml' => ['name' => 'Malayalam', 'native_name' => 'മലയാളം', 'flag' => '🇮🇳'],
];
?>

<div class="language-switcher">
    <div class="dropdown">
        <button class="btn btn-link dropdown-toggle language-btn" type="button" id="languageDropdown" 
                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
            <i class="fas fa-globe"></i>
            <span class="lang-flag"><?= $languages[$currentLang]['flag'] ?></span>
            <span class="lang-name"><?= $languages[$currentLang]['native_name'] ?></span>
        </button>
        <div class="dropdown-menu dropdown-menu-right language-dropdown" aria-labelledby="languageDropdown">
            <?php foreach ($languages as $code => $lang): ?>
                <a class="dropdown-item <?= $code === $currentLang ? 'active' : '' ?>" 
                   href="#"
                   data-lang="<?= $code ?>"
                   onclick="switchLanguage('<?= $code ?>'); return false;">
                    <span class="lang-flag"><?= $lang['flag'] ?></span>
                    <span class="lang-name"><?= $lang['native_name'] ?></span>
                    <?php if ($code === $currentLang): ?>
                        <i class="fas fa-check float-right"></i>
                    <?php endif; ?>
                </a>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<style>
.language-switcher {
    position: relative;
}

.language-btn {
    color: #333;
    text-decoration: none;
    padding: 8px 12px;
    border-radius: 8px;
    transition: all 0.3s;
}

.language-btn:hover {
    background-color: #f0f0f0;
    text-decoration: none;
}

.lang-flag {
    font-size: 20px;
    margin-right: 8px;
}

.lang-name {
    font-weight: 500;
}

.language-dropdown {
    min-width: 200px;
    border-radius: 8px;
    box-shadow: 0 4px 15px rgba(0,0,0,0.1);
    padding: 8px 0;
}

.language-dropdown .dropdown-item {
    padding: 10px 20px;
    transition: all 0.2s;
}

.language-dropdown .dropdown-item:hover {
    background-color: #f8f9fa;
}

.language-dropdown .dropdown-item.active {
    background-color: #e3f2fd;
    color: #1976d2;
    font-weight: 600;
}

@media (max-width: 768px) {
    .lang-name {
        display: none;
    }
}
</style>

<script>
function switchLanguage(lang) {
    console.log('Switching to language:', lang);
    
    // Show loading indicator
    const btn = document.querySelector('.language-btn');
    const originalHTML = btn.innerHTML;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Changing...';
    btn.disabled = true;
    
    // Send AJAX request to switch language
    fetch('<?= base_url('language/switch/') ?>' + lang, {
        method: 'POST',
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Content-Type': 'application/json'
        }
    })
    .then(response => {
        console.log('Response status:', response.status);
        return response.json();
    })
    .then(data => {
        console.log('Response data:', data);
        if (data.success) {
            // Success - reload page to apply new language
            window.location.reload();
        } else {
            // Error
            alert('Failed to change language: ' + (data.message || 'Unknown error'));
            btn.innerHTML = originalHTML;
            btn.disabled = false;
        }
    })
    .catch(error => {
        console.error('Language switch error:', error);
        alert('Error changing language. Check console for details.');
        btn.innerHTML = originalHTML;
        btn.disabled = false;
    });
}
</script>