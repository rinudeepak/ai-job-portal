(function () {
    const root = document.querySelector('[data-ai-interview-flow]');
    if (!root) {
        return;
    }

    const flow = window.aiInterviewFlow || {};
    const sections = Array.isArray(flow.sections) ? flow.sections : [];
    if (!sections.length) {
        return;
    }

    const applicationId = Number(root.dataset.applicationId || 0);
    const timerDefault = Number(root.dataset.timerSeconds || flow.timer_seconds || 60);
    const sectionButtons = Array.from(document.querySelectorAll('[data-section-index]'));
    const cameraPreview = document.getElementById('cameraPreview');
    const previewPlaceholder = document.getElementById('previewPlaceholder');
    const recordingState = document.getElementById('recordingState');
    const timerValue = document.getElementById('timerValue');
    const sectionLabel = document.getElementById('sectionLabel');
    const sectionTitle = document.getElementById('sectionTitle');
    const sectionSubtitle = document.getElementById('sectionSubtitle');
    const questionCountBadge = document.getElementById('questionCountBadge');
    const questionText = document.getElementById('questionText');
    const questionHint = document.getElementById('questionHint');
    const preStartInstructions = document.getElementById('preStartInstructions');
    const startSessionBtn = document.getElementById('startSessionBtn');
    const previousQuestionBtn = document.getElementById('previousQuestionBtn');
    const nextQuestionBtn = document.getElementById('nextQuestionBtn');
    const finishSessionBtn = document.getElementById('finishSessionBtn');
    const recordingList = document.getElementById('recordingList');
    const emptyRecordings = document.getElementById('emptyRecordings');
    const capturedResponsesCount = document.getElementById('capturedResponsesCount');
    const currentStepStatus = document.getElementById('currentStepStatus');

    const state = {
        started: false,
        finished: false,
        sectionIndex: 0,
        questionIndex: 0,
        remainingSeconds: timerDefault,
        timerHandle: null,
        autoAdvanceLock: false,
        stream: null,
        recorder: null,
        chunks: [],
        clips: [],
        interviewSessionId: null,
        recordingStartAt: null,
        recordingSupported: !!window.MediaRecorder,
        mimeType: '',
    };

    function currentSection() {
        return sections[state.sectionIndex] || null;
    }

    function currentQuestion() {
        const section = currentSection();
        if (!section || !Array.isArray(section.questions)) {
            return null;
        }

        return section.questions[state.questionIndex] || null;
    }

    function totalQuestionsInSection(section) {
        return Array.isArray(section?.questions) ? section.questions.length : 0;
    }

    function totalQuestionsBeforeSection(sectionIndex) {
        return sections
            .slice(0, sectionIndex)
            .reduce((sum, section) => sum + totalQuestionsInSection(section), 0);
    }

    function totalQuestions() {
        return sections.reduce((sum, section) => sum + totalQuestionsInSection(section), 0);
    }

    function currentQuestionNumber() {
        return totalQuestionsBeforeSection(state.sectionIndex) + state.questionIndex + 1;
    }

    function completedQuestions() {
        return state.clips.length;
    }

    function sectionCompletedCount(sectionIndex) {
        return state.clips.filter((clip) => clip.sectionIndex === sectionIndex).length;
    }

    function formatSeconds(totalSeconds) {
        const safe = Math.max(0, Number(totalSeconds) || 0);
        const minutes = Math.floor(safe / 60);
        const seconds = safe % 60;
        return minutes > 0 ? `${minutes}:${String(seconds).padStart(2, '0')}` : `${seconds}s`;
    }

    function setRecordingState(text, tone) {
        if (!recordingState) {
            return;
        }

        recordingState.textContent = text;
        recordingState.className = 'badge badge-light ai-interview-flow-recording-state';
        if (tone === 'recording') {
            recordingState.classList.add('is-recording');
        } else if (tone === 'complete') {
            recordingState.classList.add('is-complete');
        } else if (tone === 'warning') {
            recordingState.classList.add('is-warning');
        }
    }

    function updateTimer() {
        if (!timerValue) {
            return;
        }

        timerValue.textContent = formatSeconds(state.remainingSeconds);
    }

    function updateSectionNav() {
        sectionButtons.forEach((button) => {
            const index = Number(button.dataset.sectionIndex || 0);
            button.classList.toggle('is-active', index === state.sectionIndex);
            button.classList.toggle('is-complete', index < state.sectionIndex || (state.finished && index <= state.sectionIndex));
        });
    }

    function updateQuestionPanel() {
        const section = currentSection();
        const question = currentQuestion();

        if (!state.started && !state.finished) {
            if (preStartInstructions) preStartInstructions.style.display = 'block';
            if (questionText) questionText.style.display = 'none';
            if (questionHint) questionHint.style.display = 'none';
            if (sectionLabel) sectionLabel.textContent = 'Interview ready';
            if (sectionTitle) sectionTitle.textContent = 'Start when you are ready';
            if (sectionSubtitle) sectionSubtitle.textContent = 'We will show one question at a time after the session begins.';
            if (questionCountBadge) questionCountBadge.textContent = 'Waiting to start';
            if (previousQuestionBtn) previousQuestionBtn.disabled = true;
            if (nextQuestionBtn) nextQuestionBtn.disabled = true;
            if (finishSessionBtn) finishSessionBtn.disabled = true;
            return;
        }

        if (preStartInstructions) preStartInstructions.style.display = 'none';
        if (questionText) questionText.style.display = 'block';
        if (questionHint) questionHint.style.display = 'block';

        if (!section || !question) {
            if (sectionLabel) sectionLabel.textContent = 'Interview complete';
            if (sectionTitle) sectionTitle.textContent = 'You have finished the flow';
            if (sectionSubtitle) sectionSubtitle.textContent = 'You can return to your applications once the interview is complete.';
            if (questionText) questionText.textContent = 'Great work. Your interview session is complete.';
            if (questionHint) questionHint.textContent = 'Your responses have been captured for this session.';
            if (questionCountBadge) questionCountBadge.textContent = `Completed ${completedQuestions()} of ${totalQuestions()} questions`;
            if (previousQuestionBtn) previousQuestionBtn.disabled = true;
            if (nextQuestionBtn) nextQuestionBtn.disabled = true;
            if (finishSessionBtn) finishSessionBtn.disabled = true;
            if (startSessionBtn) {
                startSessionBtn.disabled = true;
                startSessionBtn.innerHTML = '<i class="fas fa-check mr-1"></i> Session Complete';
            }
            return;
        }

        if (sectionLabel) {
            sectionLabel.textContent = `Section ${state.sectionIndex + 1} of ${sections.length}`;
        }
        if (sectionTitle) {
            sectionTitle.textContent = section.title || `Section ${state.sectionIndex + 1}`;
        }
        if (sectionSubtitle) {
            sectionSubtitle.textContent = section.subtitle || '';
        }
        if (questionText) {
            questionText.textContent = question.question || 'Question pending...';
        }
        if (questionHint) {
            questionHint.textContent = question.hint || 'Answer in a clear structure and keep it concise.';
        }
        if (questionCountBadge) {
            questionCountBadge.textContent = `Question ${currentQuestionNumber()} / ${totalQuestions()}`;
        }

        if (previousQuestionBtn) {
            previousQuestionBtn.disabled = !state.started || (state.sectionIndex === 0 && state.questionIndex === 0);
        }
        if (nextQuestionBtn) {
            nextQuestionBtn.disabled = !state.started;
        }
        if (finishSessionBtn) {
            finishSessionBtn.disabled = !state.started;
        }
    }

    function updateClipListLegacy() {
        if (!recordingList || !emptyRecordings) {
            return;
        }

        const clips = state.clips;
        recordingList.querySelectorAll('[data-recording-item]').forEach((node) => node.remove());

        if (!clips.length) {
            emptyRecordings.style.display = 'flex';
            return;
        }

        emptyRecordings.style.display = 'none';
        clips.forEach((clip) => {
            const item = document.createElement('div');
            item.className = 'ai-interview-flow-recording-item';
            item.setAttribute('data-recording-item', '1');
            item.innerHTML = `
                <div class="ai-interview-flow-recording-item-copy">
                    <strong>${clip.sectionTitle}</strong>
                    <span>Question ${clip.questionNumber} · ${clip.durationLabel}</span>
                </div>
                <div class="ai-interview-flow-recording-item-actions">
                    <a href="${clip.url}" target="_blank" rel="noopener" class="btn btn-sm btn-outline-primary">Play</a>
                    <a href="${clip.url}" download="${clip.fileName}" class="btn btn-sm btn-outline-secondary">Download</a>
                </div>
            `;
            recordingList.appendChild(item);
        });
    }

    function updateClipList() {
        if (!recordingList || !emptyRecordings) {
            return;
        }

        const clips = state.clips;
        emptyRecordings.style.display = clips.length ? 'none' : 'flex';

        sections.forEach((section, index) => {
            const total = totalQuestionsInSection(section);
            const completed = sectionCompletedCount(index);
            const progressText = document.getElementById(`sectionProgressText${index}`);
            const progressFill = document.getElementById(`sectionProgressFill${index}`);
            const progressBadge = document.getElementById(`sectionProgressBadge${index}`);
            const percent = total > 0 ? Math.min(100, Math.round((completed / total) * 100)) : 0;

            if (progressText) {
                progressText.textContent = `${completed} / ${total} completed`;
            }

            if (progressFill) {
                progressFill.style.width = `${percent}%`;
            }

            if (progressBadge) {
                if (completed === 0) {
                    progressBadge.textContent = 'Not started';
                } else if (completed >= total) {
                    progressBadge.textContent = 'Complete';
                } else {
                    progressBadge.textContent = 'In progress';
                }
            }
        });

        if (capturedResponsesCount) {
            capturedResponsesCount.textContent = `${clips.length} / ${totalQuestions()}`;
        }

        if (currentStepStatus) {
            if (!state.started) {
                currentStepStatus.textContent = 'Waiting to start';
            } else if (state.finished) {
                currentStepStatus.textContent = 'Interview complete';
            } else {
                currentStepStatus.textContent = `${currentSection()?.title || 'Section'} - Question ${state.questionIndex + 1}`;
            }
        }
    }

    function updateStatusBanner(message, tone) {
        if (!root) {
            return;
        }

        let banner = root.querySelector('.ai-interview-flow-banner');
        if (!banner) {
            banner = document.createElement('div');
            banner.className = 'ai-interview-flow-banner alert alert-info';
            const shell = root.querySelector('.container');
            if (shell) {
                shell.insertBefore(banner, shell.children[1] || null);
            } else {
                root.prepend(banner);
            }
        }

        banner.className = 'ai-interview-flow-banner alert alert-info';
        if (tone === 'warning') {
            banner.classList.remove('alert-info');
            banner.classList.add('alert-warning');
        } else if (tone === 'success') {
            banner.classList.remove('alert-info');
            banner.classList.add('alert-success');
        }

        banner.textContent = message;
    }

    async function postInterviewJson(endpoint, payload) {
        const response = await fetch(endpoint, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
            },
            credentials: 'same-origin',
            body: JSON.stringify(payload || {}),
        });

        let data = {};
        try {
            data = await response.json();
        } catch (error) {
            data = {};
        }

        if (!response.ok || data.success === false) {
            const message = (data && data.message) ? data.message : 'Interview API request failed.';
            throw new Error(message);
        }

        return data;
    }

    function chooseMimeType() {
        if (!window.MediaRecorder) {
            return '';
        }

        const candidates = [
            'video/webm;codecs=vp9,opus',
            'video/webm;codecs=vp8,opus',
            'video/webm',
        ];

        return candidates.find((type) => MediaRecorder.isTypeSupported(type)) || '';
    }

    async function ensureStream() {
        if (state.stream) {
            return state.stream;
        }

        if (!navigator.mediaDevices || !navigator.mediaDevices.getUserMedia) {
            throw new Error('Your browser does not support camera and microphone capture.');
        }

        const stream = await navigator.mediaDevices.getUserMedia({
            video: { width: { ideal: 1280 }, height: { ideal: 720 } },
            audio: true,
        });

        state.stream = stream;
        if (cameraPreview) {
            cameraPreview.srcObject = stream;
        }
        if (previewPlaceholder) {
            previewPlaceholder.style.display = 'none';
        }

        return stream;
    }

    function stopTimer() {
        if (state.timerHandle) {
            clearInterval(state.timerHandle);
            state.timerHandle = null;
        }
    }

    function startTimer() {
        stopTimer();
        state.remainingSeconds = Number(currentSection()?.time_limit || timerDefault);
        updateTimer();
        state.timerHandle = window.setInterval(() => {
            state.remainingSeconds -= 1;
            updateTimer();
            if (state.remainingSeconds <= 0) {
                stopCurrentQuestion(true);
            }
        }, 1000);
    }

    function startRecorder() {
        if (!state.recordingSupported || !state.stream) {
            setRecordingState('Recording unavailable', 'warning');
            return;
        }

        state.chunks = [];
        state.mimeType = chooseMimeType();
        const recordingSection = currentSection();
        const recordingQuestion = currentQuestion();
        const recordingQuestionNumber = currentQuestionNumber();
        const recordingQuestionIndex = state.questionIndex;

        try {
            state.recorder = state.mimeType
                ? new MediaRecorder(state.stream, { mimeType: state.mimeType })
                : new MediaRecorder(state.stream);
        } catch (error) {
            console.warn('Unable to start recorder:', error);
            setRecordingState('Recording unavailable', 'warning');
            return;
        }

        state.recordingStartAt = Date.now();
        state.recorder.ondataavailable = (event) => {
            if (event.data && event.data.size > 0) {
                state.chunks.push(event.data);
            }
        };
        state.recorder.onstop = () => {
            if (!state.chunks.length) {
                return;
            }

            const blob = new Blob(state.chunks, {
                type: state.mimeType || 'video/webm',
            });
            const url = URL.createObjectURL(blob);
            const durationSeconds = Math.max(1, Math.round((Date.now() - (state.recordingStartAt || Date.now())) / 1000));

            state.clips.push({
                url,
                fileName: `ai-interview-${recordingSection?.key ?? 'section'}-${recordingQuestionNumber}.webm`,
                sectionTitle: recordingSection?.title || 'Interview Section',
                questionNumber: recordingQuestionNumber,
                durationLabel: `${durationSeconds}s`,
                questionText: recordingQuestion?.question || '',
                sectionIndex: state.sectionIndex,
            });

            if (applicationId > 0 && state.interviewSessionId) {
                postInterviewJson(`/interview/answer/${applicationId}`, {
                    interview_session_id: state.interviewSessionId,
                    section_key: recordingSection?.key || '',
                    question_index: recordingQuestionIndex,
                    question_text: recordingQuestion?.question || '',
                    answer_type: 'mixed',
                    duration_seconds: durationSeconds,
                    started_at: Math.floor((state.recordingStartAt || Date.now()) / 1000),
                    transcript: '',
                }).catch((error) => {
                    console.warn('Unable to save interview answer:', error);
                    updateStatusBanner('Answer captured locally, but autosave failed. You can continue the interview.', 'warning');
                });
            }

            updateClipList();
            setRecordingState('Clip saved', 'complete');
        };

        state.recorder.start();
        setRecordingState('Recording live', 'recording');
    }

    function stopRecorder() {
        if (state.recorder && state.recorder.state !== 'inactive') {
            state.recorder.stop();
        }
        state.recorder = null;
    }

    function hasNextQuestion() {
        const section = currentSection();
        if (!section) {
            return false;
        }

        if (state.questionIndex < totalQuestionsInSection(section) - 1) {
            return true;
        }

        return state.sectionIndex < sections.length - 1;
    }

    function focusSection(index) {
        if (index < 0 || index >= sections.length) {
            return;
        }

        stopTimer();
        stopRecorder();
        state.sectionIndex = index;
        state.questionIndex = 0;
        renderState();
        if (state.started) {
            startCurrentQuestion();
        }
    }

    function advanceQuestion() {
        const section = currentSection();
        if (!section) {
            return;
        }

        if (state.questionIndex < totalQuestionsInSection(section) - 1) {
            state.questionIndex += 1;
        } else if (state.sectionIndex < sections.length - 1) {
            state.sectionIndex += 1;
            state.questionIndex = 0;
        } else {
            finishSession();
            return;
        }

        renderState();
        startCurrentQuestion();
    }

    function stopCurrentQuestion(autoAdvance = false) {
        if (state.autoAdvanceLock) {
            return;
        }

        state.autoAdvanceLock = true;
        stopTimer();
        stopRecorder();
        renderState();

        window.setTimeout(() => {
            state.autoAdvanceLock = false;
            if (autoAdvance && !state.finished) {
                advanceQuestion();
            }
        }, 350);
    }

    function startCurrentQuestion() {
        const section = currentSection();
        const question = currentQuestion();
        if (!section || !question) {
            finishSession();
            return;
        }

        renderState();
        startTimer();
        startRecorder();
    }

    async function startSession() {
        if (state.started) {
            return;
        }

        if (applicationId <= 0) {
            updateStatusBanner('Application context is missing. Please reopen this interview from your applications page.', 'warning');
            return;
        }

        try {
            await ensureStream();
        } catch (error) {
            updateStatusBanner(error.message || 'Camera and microphone access is required to start the interview.', 'warning');
            return;
        }

        try {
            const beginResponse = await postInterviewJson(`/interview/begin/${applicationId}`, {
                started_at: Math.floor(Date.now() / 1000),
            });
            state.interviewSessionId = Number(beginResponse.interview_session_id || 0) || null;
        } catch (error) {
            updateStatusBanner(error.message || 'Unable to start interview session.', 'warning');
            return;
        }

        state.started = true;
        if (startSessionBtn) {
            startSessionBtn.innerHTML = '<i class="fas fa-redo mr-1"></i> Restart Session';
        }

        updateStatusBanner('Recording started. Answer each question before the timer ends.', 'success');
        startCurrentQuestion();
    }

    async function finishSession() {
        state.finished = true;
        stopTimer();
        stopRecorder();
        state.sectionIndex = sections.length;
        state.questionIndex = 0;
        if (state.stream) {
            state.stream.getTracks().forEach((track) => track.stop());
            state.stream = null;
        }
        if (cameraPreview) {
            cameraPreview.srcObject = null;
        }
        if (previewPlaceholder) {
            previewPlaceholder.style.display = 'flex';
        }
        setRecordingState('Session complete', 'complete');
        updateStatusBanner('Interview session complete. Your responses have been captured successfully.', 'success');

        if (applicationId > 0 && state.interviewSessionId) {
            try {
                await postInterviewJson(`/interview/complete/${applicationId}`, {
                    interview_session_id: state.interviewSessionId,
                    completed_at: Math.floor(Date.now() / 1000),
                });
            } catch (error) {
                console.warn('Unable to complete interview session:', error);
                updateStatusBanner('Interview finished, but final sync failed. Please refresh and check your applications status.', 'warning');
            }
        }

        renderState();
    }

    function renderState() {
        updateSectionNav();
        updateQuestionPanel();
        updateTimer();
        updateClipList();
    }

    function bindEvents() {
        if (startSessionBtn) {
            startSessionBtn.addEventListener('click', startSession);
        }

        if (previousQuestionBtn) {
            previousQuestionBtn.addEventListener('click', () => {
                if (!state.started) {
                    return;
                }

                if (state.questionIndex > 0) {
                    stopCurrentQuestion(false);
                    state.questionIndex -= 1;
                    renderState();
                    startCurrentQuestion();
                } else if (state.sectionIndex > 0) {
                    stopCurrentQuestion(false);
                    state.sectionIndex -= 1;
                    state.questionIndex = totalQuestionsInSection(currentSection()) - 1;
                    renderState();
                    startCurrentQuestion();
                }
            });
        }

        if (nextQuestionBtn) {
            nextQuestionBtn.addEventListener('click', () => {
                if (!state.started) {
                    return;
                }

                stopCurrentQuestion(true);
            });
        }

        if (finishSessionBtn) {
            finishSessionBtn.addEventListener('click', () => {
                if (!state.started) {
                    return;
                }

                finishSession();
            });
        }

        sectionButtons.forEach((button) => {
            button.addEventListener('click', () => {
                if (!state.started) {
                    updateStatusBanner('Start the session first to begin recording.', 'warning');
                    return;
                }

                const index = Number(button.dataset.sectionIndex || 0);
                focusSection(index);
            });
        });

        window.addEventListener('beforeunload', () => {
            stopTimer();
            stopRecorder();
            if (state.stream) {
                state.stream.getTracks().forEach((track) => track.stop());
            }
        });
    }

    function init() {
        state.mimeType = chooseMimeType();
        renderState();
        updateClipList();
        bindEvents();
        if (!state.recordingSupported) {
            setRecordingState('Recording not supported', 'warning');
            updateStatusBanner('This browser does not support MediaRecorder. You can still review the interview flow, but responses will not be captured.', 'warning');
        } else {
            setRecordingState('Ready to begin', 'light');
        }
    }

    init();
})();
