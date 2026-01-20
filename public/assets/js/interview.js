let timeLeft = window.INTERVIEW_TIME || 900;

const timer = document.getElementById('timer');
const timeTaken = document.getElementById('time_taken');

const interval = setInterval(() => {
    if (timeLeft <= 0) {
        clearInterval(interval);
        document.getElementById('answerForm').submit();
        return;
    }

    timeLeft--;
    timeTaken.value = (window.INTERVIEW_TIME || 900) - timeLeft;

    const m = Math.floor(timeLeft / 60);
    const s = String(timeLeft % 60).padStart(2, '0');
    timer.innerHTML = `<i class="fas fa-clock"></i> ${m}:${s}`;
}, 1000);

function saveDraft() {
    fetch(window.SAVE_DRAFT_URL, {
        method: 'POST',
        body: new FormData(document.getElementById('answerForm'))
    })
    .then(res => res.json())
    .then(data => alert(data.success ? 'Draft saved' : 'Save failed'));
}
