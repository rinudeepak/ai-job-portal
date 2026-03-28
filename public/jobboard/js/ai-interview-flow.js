(function () {
    const root = document.querySelector('[data-ai-interview-flow]');
    if (!root) return;

    const flow = window.aiInterviewFlow || {};
    const round1Questions = Array.isArray(flow.round1_questions) ? flow.round1_questions : [];
    const round2Sections = Array.isArray(flow.round2_sections) ? flow.round2_sections : (Array.isArray(flow.sections) ? flow.sections : []);
    if (!round1Questions.length && !round2Sections.length) return;

    const interviewBaseUrl = String(root.dataset.interviewBaseUrl || '/interview').replace(/\/+$/, '');
    const applicationId = Number(root.dataset.applicationId || 0);
    const totalTimerSeconds = Number(root.dataset.totalTimerSeconds || flow.total_timer_seconds || 1800);
    const questionTimerDefault = Number(root.dataset.timerSeconds || flow.timer_seconds || 60);

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
    const roundBadge = document.getElementById('roundBadge');
    const round1AnswerPanel = document.getElementById('round1AnswerPanel');
    const round1OptionList = document.getElementById('round1OptionList');
    const round1TextAnswer = document.getElementById('round1TextAnswer');
    const saveRound1AnswerBtn = document.getElementById('saveRound1AnswerBtn');

    const state = {
        started: false,
        finished: false,
        phase: 'round1',
        round1Index: 0,
        round1SavedCount: 0,
        sectionIndex: 0,
        questionIndex: 0,
        remainingSeconds: totalTimerSeconds,
        timerHandle: null,
        stream: null,
        recorder: null,
        chunks: [],
        clips: [],
        interviewSessionId: null,
        recordingStartAt: null,
        recordingSupported: !!window.MediaRecorder,
        mimeType: '',
        speechRecognition: null,
        speechSupported: !!(window.SpeechRecognition || window.webkitSpeechRecognition),
        transcriptBuffer: '',
        transcriptFinalParts: [],
        speechError: '',
        speechStopRequested: false,
    };

    function currentSection() {
        return round2Sections[state.sectionIndex] || null;
    }

    function currentQuestion() {
        const section = currentSection();
        return section && Array.isArray(section.questions) ? section.questions[state.questionIndex] || null : null;
    }

    function currentRound1Question() {
        return round1Questions[state.round1Index] || null;
    }

    function totalQuestionsInSection(section) {
        return Array.isArray(section?.questions) ? section.questions.length : 0;
    }

    function totalQuestionsBeforeSection(sectionIndex) {
        return round2Sections.slice(0, sectionIndex).reduce((sum, section) => sum + totalQuestionsInSection(section), 0);
    }

    function totalQuestions() {
        return round1Questions.length + round2Sections.reduce((sum, section) => sum + totalQuestionsInSection(section), 0);
    }

    function currentQuestionNumber() {
        if (state.phase === 'round1') return state.round1Index + 1;
        return round1Questions.length + totalQuestionsBeforeSection(state.sectionIndex) + state.questionIndex + 1;
    }

    function completedQuestions() {
        return state.round1SavedCount + state.clips.length;
    }

    function sectionCompletedCount(sectionIndex) {
        return state.clips.filter((clip) => clip.sectionIndex === sectionIndex).length;
    }

    function formatSeconds(totalSeconds) {
        const safe = Math.max(0, Number(totalSeconds) || 0);
        const minutes = Math.floor(safe / 60);
        const seconds = safe % 60;
        return `${minutes}:${String(seconds).padStart(2, '0')}`;
    }

    function setRecordingState(text, tone) {
        if (!recordingState) return;
        recordingState.textContent = text;
        recordingState.className = 'badge badge-light ai-interview-flow-recording-state';
        if (tone === 'recording') recordingState.classList.add('is-recording');
        if (tone === 'complete') recordingState.classList.add('is-complete');
        if (tone === 'warning') recordingState.classList.add('is-warning');
    }

    function updateTimer() {
        if (timerValue) timerValue.textContent = formatSeconds(state.remainingSeconds);
    }

    function updateSectionNav() {
        sectionButtons.forEach((button) => {
            const index = Number(button.dataset.sectionIndex || 0);
            button.classList.toggle('is-active', state.phase === 'round2' && index === state.sectionIndex);
            button.classList.toggle('is-complete', state.phase === 'round2' && index < state.sectionIndex);
            button.disabled = state.phase !== 'round2';
        });
    }

    function renderRound1AnswerUI(question) {
        if (!question) return;
        if (round1OptionList) {
            round1OptionList.innerHTML = '';
            const options = Array.isArray(question.options) ? question.options : [];
            options.forEach((option, index) => {
                const id = `r1_opt_${state.round1Index}_${index}`;
                const wrapper = document.createElement('div');
                wrapper.className = 'form-check mb-1';
                wrapper.innerHTML = `<input class="form-check-input" type="radio" name="round1_option" id="${id}" value="${String(option)}"><label class="form-check-label" for="${id}">${String(option)}</label>`;
                round1OptionList.appendChild(wrapper);
            });
            round1OptionList.style.display = options.length ? 'block' : 'none';
        }
        if (round1TextAnswer) {
            round1TextAnswer.value = '';
            round1TextAnswer.style.display = question.question_type === 'fill_blank' ? 'block' : 'none';
        }
    }

    function updateQuestionPanel() {
        const section = currentSection();
        const question = currentQuestion();
        const round1Question = currentRound1Question();

        if (!state.started && !state.finished) {
            if (preStartInstructions) preStartInstructions.style.display = 'block';
            if (questionText) questionText.style.display = 'none';
            if (questionHint) questionHint.style.display = 'none';
            if (round1AnswerPanel) round1AnswerPanel.style.display = 'none';
            if (sectionLabel) sectionLabel.textContent = 'Interview ready';
            if (sectionTitle) sectionTitle.textContent = 'Start when you are ready';
            if (sectionSubtitle) sectionSubtitle.textContent = 'Round 1 (written) then Round 2 (verbal). Total time: 30 minutes.';
            if (questionCountBadge) questionCountBadge.textContent = 'Waiting to start';
            if (roundBadge) roundBadge.textContent = 'Round 1 · Written';
            if (previousQuestionBtn) previousQuestionBtn.disabled = true;
            if (nextQuestionBtn) nextQuestionBtn.disabled = true;
            if (finishSessionBtn) finishSessionBtn.disabled = true;
            return;
        }

        if (preStartInstructions) preStartInstructions.style.display = 'none';
        if (questionText) questionText.style.display = 'block';
        if (questionHint) questionHint.style.display = 'block';

        if (state.phase === 'round1' && round1Question && !state.finished) {
            if (roundBadge) roundBadge.textContent = 'Round 1 · Written';
            if (sectionLabel) sectionLabel.textContent = 'Round 1 of 2';
            if (sectionTitle) sectionTitle.textContent = 'Written Screening';
            if (sectionSubtitle) sectionSubtitle.textContent = 'MCQ / fill-blank questions';
            if (questionText) questionText.textContent = round1Question.question_text || 'Question';
            if (questionHint) questionHint.textContent = 'Save your answer to continue.';
            if (questionCountBadge) questionCountBadge.textContent = `Question ${currentQuestionNumber()} / ${totalQuestions()}`;
            if (round1AnswerPanel) round1AnswerPanel.style.display = 'block';
            renderRound1AnswerUI(round1Question);
            if (previousQuestionBtn) previousQuestionBtn.disabled = true;
            if (nextQuestionBtn) nextQuestionBtn.disabled = true;
            if (finishSessionBtn) finishSessionBtn.disabled = false;
            return;
        }

        if (round1AnswerPanel) round1AnswerPanel.style.display = 'none';

        if (!section || !question) {
            if (sectionLabel) sectionLabel.textContent = 'Interview complete';
            if (sectionTitle) sectionTitle.textContent = 'You have finished the flow';
            if (sectionSubtitle) sectionSubtitle.textContent = 'Responses saved successfully.';
            if (questionText) questionText.textContent = 'Great work. Your interview session is complete.';
            if (questionHint) questionHint.textContent = 'You can return to Applications.';
            if (questionCountBadge) questionCountBadge.textContent = `Completed ${completedQuestions()} of ${totalQuestions()} questions`;
            if (roundBadge) roundBadge.textContent = 'Completed';
            if (previousQuestionBtn) previousQuestionBtn.disabled = true;
            if (nextQuestionBtn) nextQuestionBtn.disabled = true;
            if (finishSessionBtn) finishSessionBtn.disabled = true;
            if (startSessionBtn) {
                startSessionBtn.disabled = true;
                startSessionBtn.innerHTML = '<i class="fas fa-check mr-1"></i> Session Complete';
            }
            return;
        }

        if (roundBadge) roundBadge.textContent = 'Round 2 · Verbal';
        if (sectionLabel) sectionLabel.textContent = `Round 2 · Section ${state.sectionIndex + 1} of ${round2Sections.length}`;
        if (sectionTitle) sectionTitle.textContent = section.title || `Section ${state.sectionIndex + 1}`;
        if (sectionSubtitle) sectionSubtitle.textContent = section.subtitle || '';
        if (questionText) questionText.textContent = question.question || 'Question pending...';
        if (questionHint) questionHint.textContent = question.hint || 'Answer in a clear structure and keep it concise.';
        if (questionCountBadge) questionCountBadge.textContent = `Question ${currentQuestionNumber()} / ${totalQuestions()}`;
        if (previousQuestionBtn) previousQuestionBtn.disabled = true;
        if (nextQuestionBtn) nextQuestionBtn.disabled = !state.started;
        if (finishSessionBtn) finishSessionBtn.disabled = !state.started;
    }

    function updateClipList() {
        if (!recordingList || !emptyRecordings) return;
        const clips = state.clips;
        emptyRecordings.style.display = clips.length ? 'none' : 'flex';

        round2Sections.forEach((section, index) => {
            const total = totalQuestionsInSection(section);
            const completed = sectionCompletedCount(index);
            const progressText = document.getElementById(`sectionProgressText${index}`);
            const progressFill = document.getElementById(`sectionProgressFill${index}`);
            const progressBadge = document.getElementById(`sectionProgressBadge${index}`);
            const percent = total > 0 ? Math.min(100, Math.round((completed / total) * 100)) : 0;
            if (progressText) progressText.textContent = `${completed} / ${total} completed`;
            if (progressFill) progressFill.style.width = `${percent}%`;
            if (progressBadge) progressBadge.textContent = completed === 0 ? 'Not started' : (completed >= total ? 'Complete' : 'In progress');
        });

        if (capturedResponsesCount) capturedResponsesCount.textContent = `${completedQuestions()} / ${totalQuestions()}`;
        if (currentStepStatus) {
            if (!state.started) currentStepStatus.textContent = 'Waiting to start';
            else if (state.finished) currentStepStatus.textContent = 'Interview complete';
            else currentStepStatus.textContent = state.phase === 'round1' ? `Round 1 - Question ${state.round1Index + 1}` : `${currentSection()?.title || 'Section'} - Question ${state.questionIndex + 1}`;
        }
    }

    function updateStatusBanner(message, tone) {
        let banner = root.querySelector('.ai-interview-flow-banner');
        if (!banner) {
            banner = document.createElement('div');
            banner.className = 'ai-interview-flow-banner alert alert-info';
            const shell = root.querySelector('.container');
            if (shell) shell.insertBefore(banner, shell.children[1] || null);
            else root.prepend(banner);
        }
        banner.className = `ai-interview-flow-banner alert ${tone === 'warning' ? 'alert-warning' : (tone === 'success' ? 'alert-success' : 'alert-info')}`;
        banner.textContent = message;
    }

    async function postInterviewJson(endpoint, payload) {
        const response = await fetch(endpoint, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
            credentials: 'same-origin',
            body: JSON.stringify(payload || {}),
        });
        const data = await response.json().catch(() => ({}));
        if (!response.ok || data.success === false) throw new Error(data.message || 'Interview API request failed.');
        return data;
    }

    async function postInterviewForm(endpoint, formData) {
        const response = await fetch(endpoint, { method: 'POST', credentials: 'same-origin', body: formData });
        const data = await response.json().catch(() => ({}));
        if (!response.ok || data.success === false) throw new Error(data.message || 'Interview API request failed.');
        return data;
    }

    function interviewEndpoint(path) {
        return `${interviewBaseUrl}/${String(path).replace(/^\/+/, '')}`;
    }

    function chooseMimeType() {
        if (!window.MediaRecorder) return '';
        const candidates = ['video/webm;codecs=vp9,opus', 'video/webm;codecs=vp8,opus', 'video/webm'];
        return candidates.find((type) => MediaRecorder.isTypeSupported(type)) || '';
    }

    async function ensureStream() {
        if (state.stream) return state.stream;
        if (!navigator.mediaDevices || !navigator.mediaDevices.getUserMedia) {
            throw new Error('Your browser does not support camera and microphone capture.');
        }
        const stream = await navigator.mediaDevices.getUserMedia({ video: { width: { ideal: 1280 }, height: { ideal: 720 } }, audio: true });
        state.stream = stream;
        if (cameraPreview) cameraPreview.srcObject = stream;
        if (previewPlaceholder) previewPlaceholder.style.display = 'none';
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
        updateTimer();
        state.timerHandle = window.setInterval(() => {
            state.remainingSeconds -= 1;
            updateTimer();
            if (state.remainingSeconds <= 0) finishSession();
        }, 1000);
    }

    function startSpeechRecognition() {
        if (!state.speechSupported || state.speechRecognition || state.phase !== 'round2') return;
        const SpeechApi = window.SpeechRecognition || window.webkitSpeechRecognition;
        if (!SpeechApi) return;
        try {
            const recognition = new SpeechApi();
            recognition.continuous = true;
            recognition.interimResults = true;
            recognition.lang = 'en-US';
            recognition.onresult = (event) => {
                let interim = '';
                for (let i = event.resultIndex; i < event.results.length; i += 1) {
                    const result = event.results[i];
                    const part = (result && result[0] && result[0].transcript) ? String(result[0].transcript).trim() : '';
                    if (!part) continue;
                    if (result.isFinal) {
                        const lastFinal = state.transcriptFinalParts[state.transcriptFinalParts.length - 1] || '';
                        if (lastFinal !== part) state.transcriptFinalParts.push(part);
                    } else {
                        interim = part;
                    }
                }
                state.transcriptBuffer = [...state.transcriptFinalParts, interim].filter(Boolean).join(' ').trim();
            };
            recognition.onerror = (event) => {
                const errorType = String(event?.error || '').toLowerCase();
                if (state.speechStopRequested || errorType === 'aborted') return;
                if (errorType === 'not-allowed' || errorType === 'service-not-allowed' || errorType === 'audio-capture') state.speechError = 'permission_or_device';
                else if (errorType === 'network') state.speechError = 'engine_network';
                else if (errorType === 'no-speech') state.speechError = 'no_speech';
                else state.speechError = 'speech_recognition_error';
                stopSpeechRecognition();
            };
            recognition.onend = () => {
                if (state.started && !state.finished && state.phase === 'round2' && state.recorder && state.recorder.state !== 'inactive') {
                    try { recognition.start(); } catch (e) { stopSpeechRecognition(); }
                }
            };
            recognition.start();
            state.speechRecognition = recognition;
        } catch (e) {
            state.speechError = 'speech_recognition_init_failed';
            state.speechRecognition = null;
        }
    }

    function stopSpeechRecognition() {
        if (!state.speechRecognition) return;
        try {
            state.speechStopRequested = true;
            state.speechRecognition.onend = null;
            state.speechRecognition.stop();
        } catch (e) {
            // noop
        }
        state.speechRecognition = null;
    }

    function buildTranscriptForSubmit() {
        const transcript = String(state.transcriptBuffer || '').trim();
        if (transcript) return transcript;
        if (!state.speechSupported) return '[Transcript unavailable: browser speech recognition is not supported.]';
        if (state.speechError === 'permission_or_device') return '[Transcript unavailable: speech recognition permission/engine issue.]';
        if (state.speechError === 'engine_network') return '[Transcript unavailable: speech recognition service/network issue.]';
        return '[Transcript unavailable: no speech detected for this answer.]';
    }

    function startRecorder() {
        if (state.phase !== 'round2') return;
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
            state.recorder = state.mimeType ? new MediaRecorder(state.stream, { mimeType: state.mimeType }) : new MediaRecorder(state.stream);
        } catch (error) {
            setRecordingState('Recording unavailable', 'warning');
            return;
        }

        state.recordingStartAt = Date.now();
        state.transcriptBuffer = '';
        state.transcriptFinalParts = [];
        state.speechError = '';
        state.speechStopRequested = false;
        state.recorder.ondataavailable = (event) => {
            if (event.data && event.data.size > 0) state.chunks.push(event.data);
        };
        state.recorder.onstop = () => {
            if (!state.chunks.length) return;
            const blob = new Blob(state.chunks, { type: state.mimeType || 'video/webm' });
            const url = URL.createObjectURL(blob);
            const durationSeconds = Math.max(1, Math.round((Date.now() - (state.recordingStartAt || Date.now())) / 1000));
            state.clips.push({
                url,
                fileName: `ai-interview-${recordingSection?.key || 'section'}-${recordingQuestionNumber}.webm`,
                sectionTitle: recordingSection?.title || 'Interview Section',
                questionNumber: recordingQuestionNumber,
                durationLabel: `${durationSeconds}s`,
                questionText: recordingQuestion?.question || '',
                sectionIndex: state.sectionIndex,
            });

            if (applicationId > 0 && state.interviewSessionId) {
                const formData = new FormData();
                formData.append('interview_session_id', String(state.interviewSessionId));
                formData.append('section_key', String(recordingSection?.key || ''));
                formData.append('question_index', String(recordingQuestionIndex));
                formData.append('question_text', String(recordingQuestion?.question || ''));
                formData.append('answer_type', 'mixed');
                formData.append('duration_seconds', String(durationSeconds));
                formData.append('started_at', String(Math.floor((state.recordingStartAt || Date.now()) / 1000)));
                formData.append('transcript', String(buildTranscriptForSubmit()));
                formData.append('video_blob', blob, `answer-${recordingSection?.key || 'section'}-${recordingQuestionNumber}.webm`);
                postInterviewForm(interviewEndpoint(`answer/${applicationId}`), formData).catch((error) => {
                    console.warn('Unable to save interview answer:', error);
                    updateStatusBanner('Answer captured locally, but autosave failed. You can continue the interview.', 'warning');
                });
            }

            updateClipList();
            setRecordingState('Clip saved', 'complete');
        };

        state.recorder.start();
        startSpeechRecognition();
        setRecordingState('Recording live', 'recording');
    }

    function stopRecorder() {
        stopSpeechRecognition();
        if (state.recorder && state.recorder.state !== 'inactive') state.recorder.stop();
        state.recorder = null;
    }

    function startCurrentQuestion() {
        if (!state.started || state.finished) return;
        if (state.phase === 'round1') {
            renderState();
            return;
        }
        const section = currentSection();
        const question = currentQuestion();
        if (!section || !question) {
            finishSession();
            return;
        }
        renderState();
        startRecorder();
    }

    function advanceQuestion() {
        if (state.phase === 'round1') {
            if (state.round1Index < round1Questions.length - 1) {
                state.round1Index += 1;
            } else {
                state.phase = 'round2';
                state.sectionIndex = 0;
                state.questionIndex = 0;
                updateStatusBanner('Round 1 completed. Round 2 verbal questions started.', 'success');
                ensureStream().catch(() => {
                    updateStatusBanner('Camera/mic access is required for Round 2 recording.', 'warning');
                });
            }
            renderState();
            startCurrentQuestion();
            return;
        }

        const section = currentSection();
        if (!section) return;
        if (state.questionIndex < totalQuestionsInSection(section) - 1) {
            state.questionIndex += 1;
        } else if (state.sectionIndex < round2Sections.length - 1) {
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
        stopRecorder();
        renderState();
        if (autoAdvance && !state.finished) {
            setTimeout(() => advanceQuestion(), 300);
        }
    }

    async function saveRound1Answer() {
        const question = currentRound1Question();
        if (!question || !state.interviewSessionId || applicationId <= 0) return;
        let selectedAnswer = '';
        if (question.question_type === 'fill_blank') {
            selectedAnswer = String(round1TextAnswer?.value || '').trim();
        } else {
            const selected = document.querySelector('input[name="round1_option"]:checked');
            selectedAnswer = selected ? String(selected.value || '').trim() : '';
        }
        if (!selectedAnswer) {
            updateStatusBanner('Please provide an answer before continuing.', 'warning');
            return;
        }

        try {
            await postInterviewJson(interviewEndpoint(`round1-answer/${applicationId}`), {
                interview_session_id: state.interviewSessionId,
                section_key: question.section_key || 'reasoning',
                question_type: question.question_type || 'mcq',
                question_text: question.question_text || '',
                selected_answer: selectedAnswer,
                correct_answer: question.correct_answer || '',
            });
            state.round1SavedCount += 1;
            advanceQuestion();
        } catch (error) {
            updateStatusBanner(error.message || 'Unable to save Round 1 answer.', 'warning');
        }
    }

    async function startSession() {
        if (state.started) return;
        if (applicationId <= 0) {
            updateStatusBanner('Application context is missing. Please reopen interview from Applications.', 'warning');
            return;
        }
        try {
            await ensureStream();
        } catch (error) {
            updateStatusBanner(error.message || 'Camera and microphone access is required to start the session.', 'warning');
            return;
        }
        try {
            const beginResponse = await postInterviewJson(interviewEndpoint(`begin/${applicationId}`), {
                started_at: Math.floor(Date.now() / 1000),
            });
            state.interviewSessionId = Number(beginResponse.interview_session_id || 0) || null;
        } catch (error) {
            updateStatusBanner(error.message || 'Unable to start interview session.', 'warning');
            return;
        }
        state.started = true;
        startTimer();
        if (startSessionBtn) startSessionBtn.innerHTML = '<i class="fas fa-redo mr-1"></i> Session Running';
        updateStatusBanner('Interview started. Complete Round 1 first, then Round 2 verbal responses.', 'success');
        renderState();
    }

    async function finishSession() {
        state.finished = true;
        stopTimer();
        stopRecorder();
        state.sectionIndex = round2Sections.length;
        state.questionIndex = 0;
        if (state.stream) {
            state.stream.getTracks().forEach((track) => track.stop());
            state.stream = null;
        }
        if (cameraPreview) cameraPreview.srcObject = null;
        if (previewPlaceholder) previewPlaceholder.style.display = 'flex';
        setRecordingState('Session complete', 'complete');
        updateStatusBanner('Interview session complete. Your responses have been captured successfully.', 'success');

        if (applicationId > 0 && state.interviewSessionId) {
            try {
                await postInterviewJson(interviewEndpoint(`complete/${applicationId}`), {
                    interview_session_id: state.interviewSessionId,
                    completed_at: Math.floor(Date.now() / 1000),
                });
            } catch (error) {
                updateStatusBanner('Interview finished, but final sync failed. Please refresh and check applications status.', 'warning');
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
        if (startSessionBtn) startSessionBtn.addEventListener('click', startSession);
        if (nextQuestionBtn) {
            nextQuestionBtn.addEventListener('click', () => {
                if (!state.started || state.phase !== 'round2') return;
                stopCurrentQuestion(true);
            });
        }
        if (finishSessionBtn) {
            finishSessionBtn.addEventListener('click', () => {
                if (!state.started) return;
                finishSession();
            });
        }
        if (saveRound1AnswerBtn) saveRound1AnswerBtn.addEventListener('click', saveRound1Answer);

        sectionButtons.forEach((button) => {
            button.addEventListener('click', () => {
                if (!state.started || state.phase !== 'round2') return;
                const index = Number(button.dataset.sectionIndex || 0);
                if (index < 0 || index >= round2Sections.length) return;
                stopRecorder();
                state.sectionIndex = index;
                state.questionIndex = 0;
                renderState();
                startCurrentQuestion();
            });
        });

        window.addEventListener('beforeunload', () => {
            stopTimer();
            stopRecorder();
            if (state.stream) state.stream.getTracks().forEach((track) => track.stop());
        });
    }

    function init() {
        state.mimeType = chooseMimeType();
        renderState();
        bindEvents();
        if (!state.recordingSupported) {
            setRecordingState('Recording not supported', 'warning');
        } else {
            setRecordingState('Ready to begin', 'light');
        }
    }

    init();
})();
