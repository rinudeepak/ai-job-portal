<div id="google_translate_element"></div>

<style>
/* Style the Google Translate widget */
#google_translate_element {
    position: fixed;
    top: 20px;
    right: 20px;
    z-index: 9999;
    background: white;
    padding: 10px;
    border-radius: 8px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
}

/* Hide Google Translate branding (optional) */
.goog-te-banner-frame {
    display: none !important;
}

body {
    top: 0 !important;
}

/* Style the dropdown */
.goog-te-gadget {
    font-family: Arial, sans-serif;
}

.goog-te-gadget-simple {
    background-color: #fff;
    border: 1px solid #ddd;
    border-radius: 4px;
    padding: 8px 12px;
    font-size: 14px;
    cursor: pointer;
}

.goog-te-gadget-simple:hover {
    background-color: #f8f9fa;
}

/* Mobile responsive */
@media (max-width: 768px) {
    #google_translate_element {
        top: 10px;
        right: 10px;
        padding: 5px;
    }
}
</style>

<script type="text/javascript">
function googleTranslateElementInit() {
    new google.translate.TranslateElement({
        pageLanguage: 'en',
        includedLanguages: 'en,hi,ta,te,kn,ml,mr,gu,bn,pa', // Indian languages
        layout: google.translate.TranslateElement.InlineLayout.SIMPLE,
        autoDisplay: false
    }, 'google_translate_element');
}
</script>

<script type="text/javascript" src="//translate.google.com/translate_a/element.js?cb=googleTranslateElementInit"></script>
