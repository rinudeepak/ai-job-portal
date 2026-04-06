(function () {
    const root = document.querySelector('[data-ai-interview-flow]');
    if (!root) return;

    const flow = window.aiInterviewFlow || {};
    const round1Questions = Array.isArray(flow.round1_questions) ? flow.round1_questions : [];
    const round2Sections = Array.isArray(flow.round2_sections) ? flow.round2_sections : (Array.isArray(flow.sections) ? flow.sections : []);
    if (!round1Questions.length && !round2Sections.length) return;

    const interviewBaseUrl = String(root.dataset.interviewBaseUrl || '/interview').replace(/\/+$/, '');
    const candidateName = String(root.dataset.candidateName || 'Candidate');
    const applicationId = Number(root.dataset.applicationId || 0);
    const jobId = Number(root.dataset.jobId || 0);
    const resumeVersionId = Number(root.dataset.resumeVersionId || 0);
    const totalTimerSeconds = Number(root.dataset.totalTimerSeconds || flow.total_timer_seconds || 1800);
    const questionTimerDefault = Number(root.dataset.timerSeconds || flow.timer_seconds || 60);
    const interviewerPersona = flow.persona && typeof flow.persona === 'object' ? flow.persona : {};

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
    const syncingIndicator = document.getElementById('syncingIndicator');
    const recordingList = document.getElementById('recordingList');
    const emptyRecordings = document.getElementById('emptyRecordings');
    const capturedResponsesCount = document.getElementById('capturedResponsesCount');
    const currentStepStatus = document.getElementById('currentStepStatus');
    const overallProgressText = document.getElementById('overallProgressText');
    const overallProgressFill = document.getElementById('overallProgressFill');
    const questionTransition = document.getElementById('questionTransition');
    const questionTransitionLabel = document.getElementById('questionTransitionLabel');
    const questionTransitionCountdown = document.getElementById('questionTransitionCountdown');
    const sessionRestoreBanner = document.getElementById('sessionRestoreBanner');
    const roundBadge = document.getElementById('roundBadge');
    const round1AnswerPanel = document.getElementById('round1AnswerPanel');
    const round1OptionList = document.getElementById('round1OptionList');
    const round1TextAnswer = document.getElementById('round1TextAnswer');
    const saveRound1AnswerBtn = document.getElementById('saveRound1AnswerBtn');

    const state = {
        started: false,
        finished: false,
        needsResume: false,
        phase: 'round1',
        round1Index: 0,
        round1SavedCount: 0,
        sectionIndex: 0,
        questionIndex: 0,
        remainingSeconds: totalTimerSeconds,
        sessionEndsAt: null,
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
        round1DraftAnswer: '',
        round1SelectedOption: '',
        round1PasteEventCount: 0,
        round1PastedCharacterCount: 0,
        round1CopyPasteDetected: false,
        round1PasteMeta: null,
        round1LastTextValue: '',
        round1LastInputAt: null,
        round1LargeInsertCount: 0,
        round1LargeInsertCharacterCount: 0,
        round1LargeInsertDetected: false,
        round1LargeInsertMeta: null,
        pendingSaveCount: 0,
        lastRecorderStopPromiseResolve: null,
        followupPending: false,
        followupQuestion: null,
        followupType: '',
        followupBaseQuestionIndex: null,
        transitioning: false,
        transitionMessage: '',
        transitionCountdown: 0,
        transitionHandle: null,
        tabSwitchCount: 0,
        hiddenDurationSeconds: 0,
        hiddenAt: null,
        integrityWarningCount: 0,
        reconnectCount: 0,
        timeoutWarnings: {},
        inactivityWarnings: {},
        lastActivityAt: null,
        lastActivityEventAt: null,
        lastTabTransitionAt: null,
        lastTabTransitionType: '',
        recordingIntegrity: null,
        lastIntegrityEventAt: null,
        recordingIntegrityStart: null,
        sessionBannerVisible: false,
        sessionBannerMessage: '',
        sessionBannerTone: 'info',
        sessionBannerKind: '',
        sessionBannerTimer: null,
        lastSessionBannerAt: null,
        lastSessionBannerKey: '',
    };

    function storageKey() {
        return `aiInterviewFlow:${applicationId}:${jobId}:${resumeVersionId}`;
    }

    function getRemainingSeconds() {
        if (state.sessionEndsAt) {
            return Math.max(0, Math.ceil((Number(state.sessionEndsAt) - Date.now()) / 1000));
        }
        return Math.max(0, Number(state.remainingSeconds) || 0);
    }

    function persistProgress() {
        if (applicationId <= 0 || !window.localStorage) return;
        const payload = {
            started: state.started,
            finished: state.finished,
            needsResume: state.needsResume,
            phase: state.phase,
            round1Index: state.round1Index,
            round1SavedCount: state.round1SavedCount,
            sectionIndex: state.sectionIndex,
            questionIndex: state.questionIndex,
            remainingSeconds: getRemainingSeconds(),
            sessionEndsAt: state.sessionEndsAt,
            interviewSessionId: state.interviewSessionId,
            round1DraftAnswer: state.round1DraftAnswer,
            round1SelectedOption: state.round1SelectedOption,
            round1PasteEventCount: state.round1PasteEventCount,
            round1PastedCharacterCount: state.round1PastedCharacterCount,
            round1CopyPasteDetected: state.round1CopyPasteDetected,
            round1PasteMeta: state.round1PasteMeta,
            round1LastTextValue: state.round1LastTextValue,
            round1LastInputAt: state.round1LastInputAt,
            round1LargeInsertCount: state.round1LargeInsertCount,
            round1LargeInsertCharacterCount: state.round1LargeInsertCharacterCount,
            round1LargeInsertDetected: state.round1LargeInsertDetected,
            round1LargeInsertMeta: state.round1LargeInsertMeta,
            followupPending: state.followupPending,
            followupQuestion: state.followupQuestion,
            followupType: state.followupType,
            followupBaseQuestionIndex: state.followupBaseQuestionIndex,
            tabSwitchCount: state.tabSwitchCount,
            hiddenDurationSeconds: state.hiddenDurationSeconds,
            hiddenAt: state.hiddenAt,
            integrityWarningCount: state.integrityWarningCount,
            reconnectCount: state.reconnectCount,
            timeoutWarnings: state.timeoutWarnings,
            inactivityWarnings: state.inactivityWarnings,
            lastActivityAt: state.lastActivityAt,
            lastActivityEventAt: state.lastActivityEventAt,
            lastTabTransitionAt: state.lastTabTransitionAt,
            lastTabTransitionType: state.lastTabTransitionType,
            recordingIntegrity: state.recordingIntegrity,
            lastIntegrityEventAt: state.lastIntegrityEventAt,
            sessionBannerVisible: state.sessionBannerVisible,
            sessionBannerMessage: state.sessionBannerMessage,
            sessionBannerTone: state.sessionBannerTone,
            sessionBannerKind: state.sessionBannerKind,
            lastSessionBannerAt: state.lastSessionBannerAt,
            lastSessionBannerKey: state.lastSessionBannerKey,
            transcriptBuffer: state.transcriptBuffer,
            transcriptFinalParts: state.transcriptFinalParts,
            savedAt: Date.now(),
        };
        try {
            localStorage.setItem(storageKey(), JSON.stringify(payload));
        } catch (e) {
            // ignore storage failures
        }
    }

    function loadPersistedProgress() {
        if (applicationId <= 0 || !window.localStorage) return null;
        try {
            const raw = localStorage.getItem(storageKey());
            return raw ? JSON.parse(raw) : null;
        } catch (e) {
            return null;
        }
    }

    function clearPersistedProgress() {
        if (applicationId <= 0 || !window.localStorage) return;
        try {
            localStorage.removeItem(storageKey());
        } catch (e) {
            // ignore storage failures
        }
    }

    function clearQuestionTransition() {
        if (state.transitionHandle) {
            clearInterval(state.transitionHandle);
            state.transitionHandle = null;
        }
        state.transitioning = false;
        state.transitionMessage = '';
        state.transitionCountdown = 0;
    }

    function mergeIntegrityFlags(flags) {
        const list = new Set(Array.isArray(state.recordingIntegrity?.flags) ? state.recordingIntegrity.flags : []);
        (Array.isArray(flags) ? flags : [flags]).filter(Boolean).forEach((flag) => list.add(String(flag)));
        return Array.from(list);
    }

    function canRecordTabTransition(type) {
        const now = Date.now();
        const lastAt = Number(state.lastTabTransitionAt || 0);
        const lastType = String(state.lastTabTransitionType || '');
        if (lastAt > 0 && (now - lastAt) < 750) {
            if (lastType === type || ['blur', 'tab_hidden'].includes(lastType) || ['focus', 'tab_visible'].includes(lastType)) {
                return false;
            }
        }
        state.lastTabTransitionAt = now;
        state.lastTabTransitionType = type;
        return true;
    }

    function updateLocalIntegrityStats(eventType, details = {}) {
        const type = String(eventType || '').toLowerCase();
        if (type === 'tab_hidden' || type === 'blur') {
            state.tabSwitchCount += 1;
            state.hiddenAt = Date.now();
        } else if (type === 'tab_visible' || type === 'focus') {
            state.tabSwitchCount += 1;
            if (state.hiddenAt) {
                state.hiddenDurationSeconds += Math.max(0, Math.round((Date.now() - Number(state.hiddenAt)) / 1000));
                state.hiddenAt = null;
            }
        } else if (type === 'resume' || type === 'reconnect' || type === 'session_reconnected') {
            state.reconnectCount += 1;
        } else if (type === 'warning' || type === 'timeout_warning') {
            state.integrityWarningCount += 1;
        }

        state.lastIntegrityEventAt = Date.now();
        if (details && typeof details === 'object') {
            const nextFlags = Array.isArray(details.flags) ? details.flags : (details.flag ? [details.flag] : []);
            if (nextFlags.length) {
                const merged = mergeIntegrityFlags(nextFlags);
                state.recordingIntegrity = {
                    ...(state.recordingIntegrity && typeof state.recordingIntegrity === 'object' ? state.recordingIntegrity : {}),
                    flags: merged,
                };
            }
        }
        persistProgress();
    }

    async function sendIntegrityEvent(eventType, details = {}, tone = 'warning') {
        if (!state.started || state.finished || applicationId <= 0 || !state.interviewSessionId) return null;
        updateLocalIntegrityStats(eventType, details);
        try {
            const response = await postInterviewJson(interviewEndpoint(`integrity/${applicationId}`), {
                interview_session_id: state.interviewSessionId,
                event_type: eventType,
                severity: tone,
                details,
                client_time: Math.floor(Date.now() / 1000),
            });
            if (response && typeof response.integrity_warning_count !== 'undefined') {
                state.integrityWarningCount = Number(response.integrity_warning_count) || state.integrityWarningCount;
            }
            if (response && typeof response.tab_switch_count !== 'undefined') {
                state.tabSwitchCount = Number(response.tab_switch_count) || state.tabSwitchCount;
            }
            if (response && typeof response.hidden_duration_seconds !== 'undefined') {
                state.hiddenDurationSeconds = Number(response.hidden_duration_seconds) || state.hiddenDurationSeconds;
            }
            if (response && typeof response.reconnect_count !== 'undefined') {
                state.reconnectCount = Number(response.reconnect_count) || state.reconnectCount;
            }
            persistProgress();
            return response;
        } catch (error) {
            console.warn('Unable to log interview integrity event:', error);
            return null;
        }
    }

    function checkTimeoutWarnings() {
        const secondsLeft = getRemainingSeconds();
        const thresholds = [300, 120, 60, 15];
        thresholds.forEach((threshold) => {
            if (secondsLeft <= threshold && !state.timeoutWarnings[String(threshold)]) {
                state.timeoutWarnings[String(threshold)] = true;
                const label = threshold >= 60 ? `${Math.round(threshold / 60)} minute${threshold >= 120 ? 's' : ''}` : `${threshold} seconds`;
                updateStatusBanner(`Time warning: about ${label} left in this session.`, 'warning');
                sendIntegrityEvent('timeout_warning', { seconds_left: secondsLeft, threshold }, 'warning');
            }
        });
    }

    function registerActivity() {
        const now = Date.now();
        state.lastActivityEventAt = now;
        const previousActivityAt = state.lastActivityAt;
        state.lastActivityAt = now;
        if (!state.started || state.finished) return;

        if (!previousActivityAt) {
            persistProgress();
            return;
        }

        const idleSeconds = Math.max(0, Math.round((now - Number(previousActivityAt)) / 1000));
        if (idleSeconds >= 10 && (state.inactivityWarnings.review || state.inactivityWarnings.critical)) {
            void sendIntegrityEvent('inactivity_resumed', {
                idle_seconds: idleSeconds,
                section_key: currentSection()?.key || '',
                question_index: state.questionIndex,
                phase: state.phase,
            }, 'info');
            updateStatusBanner(`Activity resumed after ${idleSeconds} seconds of inactivity.`, 'info');
            state.inactivityWarnings = {};
        }
        persistProgress();
    }

    function checkInactivityWarnings() {
        if (!state.started || state.finished || !state.lastActivityAt) return;
        const idleSeconds = Math.max(0, Math.round((Date.now() - Number(state.lastActivityAt)) / 1000));
        const warningThreshold = 120;
        const criticalThreshold = 300;

        if (idleSeconds >= criticalThreshold && !state.inactivityWarnings.critical) {
            state.inactivityWarnings.critical = true;
            updateStatusBanner('No activity detected for a while. Please stay on the interview and continue when ready.', 'warning');
            void sendIntegrityEvent('inactivity_critical', {
                idle_seconds: idleSeconds,
                section_key: currentSection()?.key || '',
                question_index: state.questionIndex,
                phase: state.phase,
                reason: 'extended_inactivity',
            }, 'critical');
            return;
        }

        if (idleSeconds >= warningThreshold && !state.inactivityWarnings.review) {
            state.inactivityWarnings.review = true;
            updateStatusBanner('We have not detected activity for a short while. Please continue when you are ready.', 'warning');
            void sendIntegrityEvent('inactivity_warning', {
                idle_seconds: idleSeconds,
                section_key: currentSection()?.key || '',
                question_index: state.questionIndex,
                phase: state.phase,
                reason: 'idle_period',
            }, 'warning');
        }
    }

    async function refreshActiveSessionFromServer(options = {}) {
        const announceReconnect = !!options.announceReconnect;
        if (!state.started || state.finished || applicationId <= 0 || !state.interviewSessionId) return;
        try {
            const response = await fetch(interviewEndpoint(`begin/${applicationId}`), {
                method: 'GET',
                headers: { 'Accept': 'application/json' },
                credentials: 'same-origin',
            });
            const data = await response.json().catch(() => ({}));
            if (data && data.success && data.snapshot) {
                applyProgressSnapshot(data.snapshot);
                state.started = true;
                state.needsResume = !!data.snapshot.needs_resume || state.needsResume;
                state.reconnectCount += 1;
                persistProgress();
                renderState();
                if (announceReconnect) {
                    const reconnectMessage = state.needsResume
                        ? 'Your interview session is still active. Continue from the saved point.'
                        : 'Your interview session has been synced after reconnecting.';
                    showSessionRestoreBanner(reconnectMessage, 'success', 'reconnect', 5000);
                }
                if (state.phase === 'round2' && !state.stream) {
                    ensureStream().catch(() => {});
                }
            }
        } catch (error) {
            // best effort reconnect only
        }
    }

    function beginQuestionTransition(message, callback, countdownSeconds = 3) {
        clearQuestionTransition();
        if (typeof callback === 'function') {
            callback();
        } else {
            renderState();
        }
    }

    function trackPendingSave(promise) {
        state.pendingSaveCount += 1;
        persistProgress();
        return Promise.resolve(promise)
            .catch((error) => {
                throw error;
            })
            .finally(() => {
                state.pendingSaveCount = Math.max(0, state.pendingSaveCount - 1);
                persistProgress();
            });
    }

    function applyProgressSnapshot(snapshot) {
        if (!snapshot || typeof snapshot !== 'object') return;

        const sameSession = !state.interviewSessionId || !snapshot.interview_session_id || Number(state.interviewSessionId) === Number(snapshot.interview_session_id);
        const draftAnswer = sameSession ? state.round1DraftAnswer : '';
        const selectedOption = sameSession ? state.round1SelectedOption : '';
        const transcriptBuffer = sameSession ? state.transcriptBuffer : '';
        const transcriptFinalParts = sameSession ? state.transcriptFinalParts : [];

        state.started = false;
        state.finished = !!snapshot.finished;
        state.needsResume = !state.finished && snapshot.phase === 'round2';
        state.phase = snapshot.phase || state.phase;
        state.round1Index = Number(snapshot.round1_index ?? state.round1Index ?? 0) || 0;
        state.round1SavedCount = Number(snapshot.round1_saved_count ?? state.round1SavedCount ?? 0) || 0;
        state.sectionIndex = Number(snapshot.section_index ?? state.sectionIndex ?? 0) || 0;
        state.questionIndex = Number(snapshot.question_index ?? state.questionIndex ?? 0) || 0;
        const snapshotRemaining = snapshot.remaining_seconds !== undefined && snapshot.remaining_seconds !== null
            ? Number(snapshot.remaining_seconds)
            : Number(state.remainingSeconds);
        state.remainingSeconds = Number.isFinite(snapshotRemaining) ? snapshotRemaining : totalTimerSeconds;
        state.sessionEndsAt = snapshot.session_ends_at ? Number(snapshot.session_ends_at) * 1000 : state.sessionEndsAt;
        state.interviewSessionId = Number(snapshot.interview_session_id ?? state.interviewSessionId ?? 0) || null;
        state.round1DraftAnswer = draftAnswer;
        state.round1SelectedOption = selectedOption;
        state.round1PasteEventCount = Number(snapshot.round1PasteEventCount ?? snapshot.round1_paste_event_count ?? state.round1PasteEventCount ?? 0) || 0;
        state.round1PastedCharacterCount = Number(snapshot.round1PastedCharacterCount ?? snapshot.round1_pasted_character_count ?? state.round1PastedCharacterCount ?? 0) || 0;
        state.round1CopyPasteDetected = !!(snapshot.round1CopyPasteDetected ?? snapshot.round1_copy_paste_detected ?? state.round1CopyPasteDetected);
        state.round1PasteMeta = snapshot.round1PasteMeta && typeof snapshot.round1PasteMeta === 'object'
            ? snapshot.round1PasteMeta
            : (snapshot.round1_paste_meta && typeof snapshot.round1_paste_meta === 'object' ? snapshot.round1_paste_meta : state.round1PasteMeta);
        state.round1LastTextValue = String(snapshot.round1LastTextValue ?? snapshot.round1_last_text_value ?? state.round1LastTextValue ?? '');
        state.round1LastInputAt = snapshot.round1LastInputAt ?? snapshot.round1_last_input_at ?? state.round1LastInputAt ?? null;
        state.round1LargeInsertCount = Number(snapshot.round1LargeInsertCount ?? snapshot.round1_large_insert_count ?? state.round1LargeInsertCount ?? 0) || 0;
        state.round1LargeInsertCharacterCount = Number(snapshot.round1LargeInsertCharacterCount ?? snapshot.round1_large_insert_character_count ?? state.round1LargeInsertCharacterCount ?? 0) || 0;
        state.round1LargeInsertDetected = !!(snapshot.round1LargeInsertDetected ?? snapshot.round1_large_insert_detected ?? state.round1LargeInsertDetected);
        state.round1LargeInsertMeta = snapshot.round1LargeInsertMeta && typeof snapshot.round1LargeInsertMeta === 'object'
            ? snapshot.round1LargeInsertMeta
            : (snapshot.round1_large_insert_meta && typeof snapshot.round1_large_insert_meta === 'object' ? snapshot.round1_large_insert_meta : state.round1LargeInsertMeta);
        state.followupPending = !!snapshot.followup_pending;
        state.followupQuestion = snapshot.followup_question
            ? {
                question: String(snapshot.followup_question),
                question_text: String(snapshot.followup_question),
                followup_type: String(snapshot.followup_type || 'clarify'),
                reason: String(snapshot.followup_reason || ''),
            }
            : null;
        state.followupType = String(snapshot.followup_type || '');
        state.followupBaseQuestionIndex = snapshot.followup_question_index !== undefined && snapshot.followup_question_index !== null
            ? Number(snapshot.followup_question_index)
            : null;
        state.tabSwitchCount = Number(snapshot.tab_switch_count ?? snapshot.tabSwitchCount ?? state.tabSwitchCount ?? 0) || 0;
        state.hiddenDurationSeconds = Number(snapshot.hidden_duration_seconds ?? snapshot.hiddenDurationSeconds ?? state.hiddenDurationSeconds ?? 0) || 0;
        state.hiddenAt = snapshot.hidden_at ?? snapshot.hiddenAt ?? state.hiddenAt ?? null;
        state.integrityWarningCount = Number(snapshot.integrity_warning_count ?? snapshot.integrityWarningCount ?? state.integrityWarningCount ?? 0) || 0;
        state.reconnectCount = Number(snapshot.reconnect_count ?? snapshot.reconnectCount ?? state.reconnectCount ?? 0) || 0;
        state.timeoutWarnings = snapshot.timeout_warnings && typeof snapshot.timeout_warnings === 'object' ? snapshot.timeout_warnings : (snapshot.timeoutWarnings && typeof snapshot.timeoutWarnings === 'object' ? snapshot.timeoutWarnings : state.timeoutWarnings);
        state.inactivityWarnings = snapshot.inactivity_warnings && typeof snapshot.inactivity_warnings === 'object' ? snapshot.inactivity_warnings : (snapshot.inactivityWarnings && typeof snapshot.inactivityWarnings === 'object' ? snapshot.inactivityWarnings : state.inactivityWarnings);
        state.lastActivityAt = snapshot.last_activity_at ?? snapshot.lastActivityAt ?? state.lastActivityAt ?? null;
        state.lastActivityEventAt = snapshot.last_activity_event_at ?? snapshot.lastActivityEventAt ?? state.lastActivityEventAt ?? null;
        state.lastTabTransitionAt = snapshot.last_tab_transition_at ?? snapshot.lastTabTransitionAt ?? state.lastTabTransitionAt ?? null;
        state.lastTabTransitionType = String(snapshot.last_tab_transition_type ?? snapshot.lastTabTransitionType ?? state.lastTabTransitionType ?? '');
        state.recordingIntegrity = snapshot.recording_integrity && typeof snapshot.recording_integrity === 'object' ? snapshot.recording_integrity : (snapshot.recordingIntegrity && typeof snapshot.recordingIntegrity === 'object' ? snapshot.recordingIntegrity : state.recordingIntegrity);
        state.lastIntegrityEventAt = snapshot.last_integrity_event_at ?? snapshot.lastIntegrityEventAt ?? state.lastIntegrityEventAt ?? null;
        state.transcriptBuffer = transcriptBuffer;
        state.transcriptFinalParts = transcriptFinalParts;
    }

    function currentSection() {
        return round2Sections[state.sectionIndex] || null;
    }

    function currentQuestion() {
        if (state.phase === 'round2' && state.followupPending && state.followupQuestion) {
            return state.followupQuestion;
        }
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

    function formatPersonaText(text) {
        return String(text || '')
            .replace(/\{candidate_name\}/g, candidateName)
            .replace(/\{role_title\}/g, String(flow.job_title || 'the role'))
            .trim();
    }

    function questionPrompt(question) {
        return String(question?.question_text || question?.question || '').trim();
    }

    function round1QuestionHint(question) {
        if (state.needsResume) {
            return 'Session restored. Your current answer is still here.';
        }

        const type = String(question?.question_type || 'mcq').toLowerCase();
        if (type === 'fill_blank') {
            return 'Type the missing word or phrase, then save to continue.';
        }

        if (type === 'mcq') {
            return 'Choose the best option, then save to continue.';
        }

        return formatPersonaText(interviewerPersona.transition_message || 'Answer directly, then save to continue.');
    }

    function resetRound1PasteTracking() {
        state.round1PasteEventCount = 0;
        state.round1PastedCharacterCount = 0;
        state.round1CopyPasteDetected = false;
        state.round1PasteMeta = null;
        state.round1LastTextValue = '';
        state.round1LastInputAt = null;
        state.round1LargeInsertCount = 0;
        state.round1LargeInsertCharacterCount = 0;
        state.round1LargeInsertDetected = false;
        state.round1LargeInsertMeta = null;
    }

    function registerRound1Paste(event) {
        const clipboardText = String(event?.clipboardData?.getData('text/plain') || '').trim();
        const pastedCharacters = clipboardText.length;
        state.round1PasteEventCount += 1;
        state.round1PastedCharacterCount += pastedCharacters;
        state.round1CopyPasteDetected = true;
        state.round1PasteMeta = {
            paste_event_count: state.round1PasteEventCount,
            pasted_character_count: state.round1PastedCharacterCount,
            last_pasted_character_count: pastedCharacters,
            detected_at: Date.now(),
        };
        persistProgress();
        updateStatusBanner('Paste detected in the written answer. This response will be marked for review.', 'warning');
    }

    function getRound1LargeInsertThresholds(question, currentValue) {
        const questionType = String(question?.question_type || '').toLowerCase();
        const answerLength = String(currentValue || '').trim().length;

        if (questionType === 'fill_blank') {
            if (answerLength <= 8) {
                return { largeJump: 8, rapidJump: 6, rapidWindowMs: 900 };
            }
            if (answerLength <= 16) {
                return { largeJump: 7, rapidJump: 5, rapidWindowMs: 1100 };
            }
            return { largeJump: 6, rapidJump: 5, rapidWindowMs: 1200 };
        }

        if (answerLength <= 12) {
            return { largeJump: 10, rapidJump: 6, rapidWindowMs: 1000 };
        }

        return { largeJump: 8, rapidJump: 6, rapidWindowMs: 1200 };
    }

    function registerRound1LargeInsert(currentValue, question, inputType = '') {
        const nextValue = String(currentValue || '');
        const previousValue = String(state.round1LastTextValue || '');
        const now = Date.now();
        const delta = nextValue.length - previousValue.length;
        const elapsedMs = state.round1LastInputAt ? Math.max(0, now - Number(state.round1LastInputAt)) : null;
        const insertedText = delta > 0 ? nextValue.slice(previousValue.length) : '';
        const insertedLength = insertedText.length;
        const typeHint = String(inputType || '').toLowerCase();
        const thresholds = getRound1LargeInsertThresholds(question, nextValue);
        const hasLargeJump = delta >= thresholds.largeJump || (nextValue.length >= 15 && delta >= Math.max(5, thresholds.largeJump - 2));
        const hasRapidJump = elapsedMs !== null && elapsedMs <= thresholds.rapidWindowMs && delta >= thresholds.rapidJump;
        const hasAutoFillHint = ['insertfrompaste', 'insertreplacementtext', 'inserttranspose', 'insertfromdrop'].some((hint) => typeHint.includes(hint));

        state.round1LastTextValue = nextValue;
        state.round1LastInputAt = now;

        if (!hasLargeJump && !hasRapidJump && !hasAutoFillHint) {
            persistProgress();
            return false;
        }

        state.round1LargeInsertCount += 1;
        state.round1LargeInsertCharacterCount += Math.max(delta, insertedLength, 0);
        state.round1LargeInsertDetected = true;
        state.round1LargeInsertMeta = {
            large_insert_count: state.round1LargeInsertCount,
            large_insert_character_count: state.round1LargeInsertCharacterCount,
            last_insert_length: Math.max(delta, insertedLength, 0),
            input_type: typeHint || 'input',
            elapsed_ms: elapsedMs,
            thresholds,
            detected_at: now,
        };
        state.round1CopyPasteDetected = true;
        state.round1PasteMeta = {
            ...(state.round1PasteMeta && typeof state.round1PasteMeta === 'object' ? state.round1PasteMeta : {}),
            suspicious_large_insert: true,
            large_insert_count: state.round1LargeInsertCount,
            large_insert_character_count: state.round1LargeInsertCharacterCount,
            last_insert_length: Math.max(delta, insertedLength, 0),
            input_type: typeHint || 'input',
            elapsed_ms: elapsedMs,
            thresholds,
            detected_at: now,
        };
        persistProgress();
        updateStatusBanner('A large answer insert was detected and will be marked for review.', 'warning');
        return true;
    }

    function isFollowupQuestion() {
        return !!(state.phase === 'round2' && state.followupPending && state.followupQuestion);
    }

    function setRecordingState(text, tone) {
        if (!recordingState) return;
        recordingState.textContent = text;
        recordingState.className = 'badge badge-light ai-interview-flow-recording-state';
        if (tone === 'recording') recordingState.classList.add('is-recording');
        if (tone === 'complete') recordingState.classList.add('is-complete');
        if (tone === 'warning') recordingState.classList.add('is-warning');
    }

    function updateSyncingIndicator() {
        if (!syncingIndicator) return;
        syncingIndicator.style.display = state.pendingSaveCount > 0 && !state.finished ? 'block' : 'none';
    }

    function stopLiveMedia() {
        if (state.stream) {
            state.stream.getTracks().forEach((track) => track.stop());
            state.stream = null;
        }
        if (cameraPreview) cameraPreview.srcObject = null;
        if (previewPlaceholder) previewPlaceholder.style.display = 'flex';
    }

    function updateTimer() {
        state.remainingSeconds = getRemainingSeconds();
        if (timerValue) timerValue.textContent = formatSeconds(state.remainingSeconds);
    }

    function updateSectionNav() {
        const followupActive = isFollowupQuestion();
        sectionButtons.forEach((button) => {
            const index = Number(button.dataset.sectionIndex || 0);
            button.classList.toggle('is-active', state.phase === 'round2' && index === state.sectionIndex);
            button.classList.toggle('is-complete', state.phase === 'round2' && index < state.sectionIndex);
            button.disabled = state.phase !== 'round2' || state.needsResume || followupActive || state.transitioning;
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
                const checked = state.round1SelectedOption === String(option) ? ' checked' : '';
                wrapper.innerHTML = `<input class="form-check-input" type="radio" name="round1_option" id="${id}" value="${String(option)}"${checked}><label class="form-check-label" for="${id}">${String(option)}</label>`;
                round1OptionList.appendChild(wrapper);
            });
            round1OptionList.style.display = options.length ? 'block' : 'none';
        }
        if (round1TextAnswer) {
            round1TextAnswer.value = question.question_type === 'fill_blank' ? state.round1DraftAnswer : '';
            round1TextAnswer.style.display = question.question_type === 'fill_blank' ? 'block' : 'none';
        }
    }

    function updateQuestionPanel() {
        const section = currentSection();
        const question = currentQuestion();
        const round1Question = currentRound1Question();
        const showRound2Nav = state.started && !state.finished && state.phase === 'round2' && !state.needsResume;
        const followupActive = isFollowupQuestion();
        const promptText = questionPrompt(question);

        if (previousQuestionBtn) previousQuestionBtn.style.display = showRound2Nav ? 'inline-flex' : 'none';
        if (nextQuestionBtn) nextQuestionBtn.style.display = showRound2Nav ? 'inline-flex' : 'none';
        if (finishSessionBtn) finishSessionBtn.style.display = state.started && !state.finished && state.phase === 'round2' ? 'inline-flex' : 'none';

        if (!state.started && !state.finished) {
            if (preStartInstructions) preStartInstructions.style.display = 'block';
            if (questionText) questionText.style.display = 'none';
            if (questionHint) {
                questionHint.style.display = 'block';
                questionHint.textContent = 'Click Start Interview to begin. Camera and microphone permission will be requested immediately before the session starts.';
            }
            if (round1AnswerPanel) round1AnswerPanel.style.display = 'none';
            if (sectionLabel) sectionLabel.textContent = interviewerPersona.name || 'Interviewer';
            if (sectionTitle) sectionTitle.textContent = interviewerPersona.opening_title || 'Interview overview';
            if (sectionSubtitle) {
                const structureLine = interviewerPersona.transition_message || 'We will begin with a short written screening and then move into role-specific responses.';
                sectionSubtitle.textContent = formatPersonaText(structureLine);
            }
            if (questionCountBadge) questionCountBadge.textContent = 'Waiting to start';
            if (roundBadge) roundBadge.textContent = 'Round 1 · Written';
            if (startSessionBtn) startSessionBtn.innerHTML = '<i class="fas fa-video mr-1"></i> Start Interview';
            if (previousQuestionBtn) previousQuestionBtn.disabled = true;
            if (nextQuestionBtn) nextQuestionBtn.disabled = true;
            if (finishSessionBtn) finishSessionBtn.disabled = true;
            if (overallProgressText) overallProgressText.textContent = `${Math.min(100, Math.round((completedQuestions() / Math.max(1, totalQuestions())) * 100))}%`;
            if (overallProgressFill) overallProgressFill.style.width = `${Math.min(100, Math.round((completedQuestions() / Math.max(1, totalQuestions())) * 100))}%`;
            if (questionTransition) questionTransition.style.display = 'none';
            return;
        }

        if (preStartInstructions) preStartInstructions.style.display = 'none';
        if (questionText) questionText.style.display = 'block';
        if (questionHint) questionHint.style.display = 'block';

        if (startSessionBtn && state.started && !state.finished) {
            startSessionBtn.innerHTML = state.needsResume
                ? '<i class="fas fa-play mr-1"></i> Resume Session'
                : '<i class="fas fa-redo mr-1"></i> Session Running';
        }

        if (state.phase === 'round1' && round1Question && !state.finished) {
            if (roundBadge) roundBadge.textContent = 'Round 1 · Written';
            if (sectionLabel) sectionLabel.textContent = interviewerPersona.name || 'Interviewer';
            if (sectionTitle) sectionTitle.textContent = 'Written Screening';
            if (sectionSubtitle) sectionSubtitle.textContent = formatPersonaText(interviewerPersona.style || 'MCQ / fill-blank questions');
            if (questionText) questionText.textContent = questionPrompt(round1Question) || 'Question';
            if (questionHint) questionHint.textContent = round1QuestionHint(round1Question);
            if (questionCountBadge) questionCountBadge.textContent = `Question ${currentQuestionNumber()} / ${totalQuestions()}`;
            if (round1AnswerPanel) round1AnswerPanel.style.display = 'block';
            renderRound1AnswerUI(round1Question);
            if (previousQuestionBtn) previousQuestionBtn.disabled = true;
            if (nextQuestionBtn) nextQuestionBtn.disabled = true;
            if (finishSessionBtn) finishSessionBtn.disabled = true;
            if (overallProgressText) overallProgressText.textContent = `${Math.min(100, Math.round((completedQuestions() / Math.max(1, totalQuestions())) * 100))}%`;
            if (overallProgressFill) overallProgressFill.style.width = `${Math.min(100, Math.round((completedQuestions() / Math.max(1, totalQuestions())) * 100))}%`;
            if (questionTransition) questionTransition.style.display = 'none';
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
            if (overallProgressText) overallProgressText.textContent = '100%';
            if (overallProgressFill) overallProgressFill.style.width = '100%';
            if (questionTransition) questionTransition.style.display = 'none';
            return;
        }

        if (state.transitioning) {
            if (roundBadge) roundBadge.textContent = state.phase === 'round1' ? 'Round 1 · Transition' : 'Round 2 · Transition';
            if (sectionLabel) sectionLabel.textContent = interviewerPersona.name || 'Interviewer';
            if (sectionTitle) sectionTitle.textContent = 'Preparing next question';
            if (sectionSubtitle) sectionSubtitle.textContent = state.transitionMessage || 'Taking a short pause before the next prompt.';
            if (questionText) questionText.textContent = `Next question in ${state.transitionCountdown}s`;
            if (questionHint) questionHint.textContent = 'Take a breath. The interview will continue shortly.';
            if (questionCountBadge) questionCountBadge.textContent = 'Transitioning';
            if (round1AnswerPanel) round1AnswerPanel.style.display = 'none';
            if (previousQuestionBtn) previousQuestionBtn.disabled = true;
            if (nextQuestionBtn) nextQuestionBtn.disabled = true;
            if (finishSessionBtn) finishSessionBtn.disabled = true;
            if (overallProgressText) overallProgressText.textContent = `${Math.min(100, Math.round((completedQuestions() / Math.max(1, totalQuestions())) * 100))}%`;
            if (overallProgressFill) overallProgressFill.style.width = `${Math.min(100, Math.round((completedQuestions() / Math.max(1, totalQuestions())) * 100))}%`;
            if (questionTransition) {
                questionTransition.style.display = 'grid';
                if (questionTransitionLabel) questionTransitionLabel.textContent = state.transitionMessage || 'Preparing the next question...';
                if (questionTransitionCountdown) questionTransitionCountdown.textContent = String(state.transitionCountdown);
            }
            return;
        }

        if (roundBadge) roundBadge.textContent = followupActive ? 'Round 2 · Follow-up' : 'Round 2 · Verbal';
        if (sectionLabel) sectionLabel.textContent = interviewerPersona.name || 'Interviewer';
        if (sectionTitle) sectionTitle.textContent = followupActive
            ? `${section.title || `Section ${state.sectionIndex + 1}`} Follow-up`
            : (section.title || `Section ${state.sectionIndex + 1}`);
        if (sectionSubtitle) {
            const stylePrefix = interviewerPersona.style ? `${interviewerPersona.style}. ` : '';
            sectionSubtitle.textContent = formatPersonaText(stylePrefix + (section.subtitle || ''));
        }
        if (questionText) questionText.textContent = promptText || 'Question pending...';
        if (questionHint) questionHint.textContent = state.needsResume
            ? 'Session restored. Resume recording to continue this question.'
            : formatPersonaText(
                followupActive
                    ? (state.followupQuestion?.reason || 'This follow-up is based on your previous answer.')
                    : (question.hint || interviewerPersona.transition_message || 'Answer in a clear structure and keep it concise.')
            );
        if (questionCountBadge) {
            questionCountBadge.textContent = followupActive
                ? `Follow-up on Question ${currentQuestionNumber()} / ${totalQuestions()}`
                : `Question ${currentQuestionNumber()} / ${totalQuestions()}`;
        }
        if (previousQuestionBtn) previousQuestionBtn.disabled = !showRound2Nav || followupActive || (state.sectionIndex === 0 && state.questionIndex === 0);
        if (nextQuestionBtn) nextQuestionBtn.disabled = !state.started || state.needsResume;
        if (finishSessionBtn) finishSessionBtn.disabled = !state.started || state.needsResume || state.phase !== 'round2';
        if (overallProgressText) overallProgressText.textContent = `${Math.min(100, Math.round((completedQuestions() / Math.max(1, totalQuestions())) * 100))}%`;
        if (overallProgressFill) overallProgressFill.style.width = `${Math.min(100, Math.round((completedQuestions() / Math.max(1, totalQuestions())) * 100))}%`;
        if (questionTransition) questionTransition.style.display = 'none';
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
            else if (state.transitioning) currentStepStatus.textContent = 'Transitioning to next question';
            else if (state.phase === 'round1') currentStepStatus.textContent = `Round 1 - Question ${state.round1Index + 1}`;
            else if (isFollowupQuestion()) currentStepStatus.textContent = `${currentSection()?.title || 'Section'} - Follow-up`;
            else currentStepStatus.textContent = `${currentSection()?.title || 'Section'} - Question ${state.questionIndex + 1}`;
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

    function clearSessionBannerTimer() {
        if (state.sessionBannerTimer) {
            window.clearTimeout(state.sessionBannerTimer);
            state.sessionBannerTimer = null;
        }
    }

    function hideSessionRestoreBanner() {
        clearSessionBannerTimer();
        state.sessionBannerVisible = false;
        state.sessionBannerMessage = '';
        state.sessionBannerTone = 'info';
        state.sessionBannerKind = '';
        state.lastSessionBannerAt = null;
        state.lastSessionBannerKey = '';
        renderSessionRestoreBanner();
        persistProgress();
    }

    function showSessionRestoreBanner(message, tone = 'info', kind = 'resume', autoHideMs = 0) {
        const text = String(message || '').trim();
        if (!text || state.finished) {
            hideSessionRestoreBanner();
            return;
        }

        const nextKey = `${String(kind || 'resume')}:${String(tone || 'info')}:${text}`;
        const now = Date.now();
        if (state.lastSessionBannerKey === nextKey && state.lastSessionBannerAt && (now - Number(state.lastSessionBannerAt)) < 1200) {
            return;
        }

        clearSessionBannerTimer();
        state.sessionBannerVisible = true;
        state.sessionBannerMessage = text;
        state.sessionBannerTone = tone;
        state.sessionBannerKind = String(kind || 'resume');
        state.lastSessionBannerAt = now;
        state.lastSessionBannerKey = nextKey;
        renderSessionRestoreBanner();
        persistProgress();

        const hideAfter = Math.max(0, Number(autoHideMs) || 0);
        if (hideAfter > 0) {
            state.sessionBannerTimer = window.setTimeout(() => {
                if (state.lastSessionBannerKey === nextKey) {
                    hideSessionRestoreBanner();
                }
            }, hideAfter);
        }
    }

    function renderSessionRestoreBanner() {
        if (!sessionRestoreBanner) return;
        if (!state.sessionBannerVisible || !state.sessionBannerMessage || state.finished) {
            sessionRestoreBanner.style.display = 'none';
            sessionRestoreBanner.textContent = '';
            return;
        }

        const toneClass = state.sessionBannerTone === 'warning'
            ? 'alert-warning'
            : (state.sessionBannerTone === 'success' ? 'alert-success' : 'alert-info');
        const title = state.sessionBannerKind === 'reconnect' ? 'Connection restored' : 'Session restored';
        sessionRestoreBanner.className = `alert ${toneClass} ai-interview-flow-session-banner d-flex align-items-start justify-content-between gap-3`;
        sessionRestoreBanner.style.display = 'flex';
        sessionRestoreBanner.innerHTML = `
            <div class="d-flex align-items-start gap-2">
                <i class="fas fa-sync-alt mt-1"></i>
                <div>
                    <strong>${title}</strong>
                    <div class="small mb-0">${state.sessionBannerMessage}</div>
                </div>
            </div>
            <button type="button" class="close" aria-label="Dismiss session notice" data-session-banner-dismiss>
                <span aria-hidden="true">&times;</span>
            </button>
        `;

        const dismissButton = sessionRestoreBanner.querySelector('[data-session-banner-dismiss]');
        if (dismissButton) {
            dismissButton.addEventListener('click', hideSessionRestoreBanner);
        }
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
            updateTimer();
            if (state.started && !state.finished) {
                checkTimeoutWarnings();
                checkInactivityWarnings();
            }
            if (state.remainingSeconds <= 0) finishSession();
            else persistProgress();
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
        const recordingVariant = isFollowupQuestion() ? 'followup' : 'base';
        const recordingParentIndex = isFollowupQuestion() ? Number(state.followupBaseQuestionIndex ?? state.questionIndex) : null;
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
        state.recordingIntegrityStart = {
            tabSwitchCount: state.tabSwitchCount,
            hiddenDurationSeconds: state.hiddenDurationSeconds,
            startedAt: Date.now(),
            visibilityState: document.visibilityState || 'visible',
        };
        state.recorder.ondataavailable = (event) => {
            if (event.data && event.data.size > 0) state.chunks.push(event.data);
        };
        state.recorder.onstop = () => {
            let savePromise = Promise.resolve();
            if (!state.chunks.length) {
                state.recordingIntegrityStart = null;
                state.lastRecorderStopPromiseResolve?.(savePromise);
                state.lastRecorderStopPromiseResolve = null;
                return;
            }
            const blob = new Blob(state.chunks, { type: state.mimeType || 'video/webm' });
            const url = URL.createObjectURL(blob);
            const durationSeconds = Math.max(1, Math.round((Date.now() - (state.recordingStartAt || Date.now())) / 1000));
            const integrityStart = state.recordingIntegrityStart || {};
            const visibilityDuringClip = document.visibilityState || 'visible';
            const clipTabSwitches = Math.max(0, Number(state.tabSwitchCount || 0) - Number(integrityStart.tabSwitchCount || 0));
            const clipHiddenSeconds = Math.max(0, Number(state.hiddenDurationSeconds || 0) - Number(integrityStart.hiddenDurationSeconds || 0));
            const recordingIntegrity = {
                status: clipTabSwitches > 0 || clipHiddenSeconds > 0 || visibilityDuringClip === 'hidden' ? 'review' : 'ok',
                flags: mergeIntegrityFlags([
                    clipTabSwitches > 0 ? 'tab_switch_during_answer' : null,
                    clipHiddenSeconds > 0 ? 'window_hidden_during_answer' : null,
                    state.speechError ? 'speech_recognition_issue' : null,
                    !state.stream ? 'stream_missing' : null,
                    durationSeconds < 3 ? 'very_short_clip' : null,
                ]),
                tab_switch_count: clipTabSwitches,
                hidden_duration_seconds: clipHiddenSeconds,
                visibility_state: visibilityDuringClip,
                recorder_state: state.recorder?.state || 'inactive',
                mime_type: state.mimeType || 'video/webm',
            };
            state.recordingIntegrity = recordingIntegrity;
            state.clips.push({
                url,
                fileName: `ai-interview-${recordingSection?.key || 'section'}-${recordingQuestionNumber}${recordingVariant === 'followup' ? '-followup' : ''}.webm`,
                sectionTitle: recordingSection?.title || 'Interview Section',
                questionNumber: recordingQuestionNumber,
                durationLabel: `${durationSeconds}s`,
                questionText: questionPrompt(recordingQuestion),
                sectionIndex: state.sectionIndex,
            });

            if (applicationId > 0 && state.interviewSessionId) {
                const formData = new FormData();
                formData.append('interview_session_id', String(state.interviewSessionId));
                formData.append('section_key', String(recordingSection?.key || ''));
                formData.append('question_index', String(recordingQuestionIndex));
                formData.append('question_text', String(questionPrompt(recordingQuestion)));
                formData.append('answer_type', 'mixed');
                formData.append('answer_variant', recordingVariant);
                if (recordingParentIndex !== null) {
                    formData.append('parent_question_index', String(recordingParentIndex));
                }
                formData.append('duration_seconds', String(durationSeconds));
                formData.append('started_at', String(Math.floor((state.recordingStartAt || Date.now()) / 1000)));
                formData.append('transcript', String(buildTranscriptForSubmit()));
                formData.append('client_context', JSON.stringify({
                    visibility_state: visibilityDuringClip,
                    tab_switch_count: clipTabSwitches,
                    hidden_duration_seconds: clipHiddenSeconds,
                    integrity_status: recordingIntegrity.status,
                    speech_error: state.speechError || '',
                    recorder_state: recordingIntegrity.recorder_state,
                }));
                formData.append('integrity_flags', JSON.stringify(recordingIntegrity.flags || []));
                formData.append('tab_switch_count', String(clipTabSwitches));
                formData.append('hidden_duration_seconds', String(clipHiddenSeconds));
                formData.append('recording_health', String(recordingIntegrity.status));
                formData.append('recording_metrics', JSON.stringify(recordingIntegrity));
                formData.append('video_blob', blob, `answer-${recordingSection?.key || 'section'}-${recordingQuestionNumber}.webm`);
                savePromise = trackPendingSave(
                    postInterviewForm(interviewEndpoint(`answer/${applicationId}`), formData)
                        .then((response) => {
                            if (response && response.next_action === 'followup' && response.followup_question) {
                                state.followupPending = true;
                                state.followupType = String(response.followup_question.followup_type || 'clarify');
                                state.followupBaseQuestionIndex = recordingQuestionIndex;
                                state.followupQuestion = {
                                    question: String(response.followup_question.question_text || ''),
                                    question_text: String(response.followup_question.question_text || ''),
                                    followup_type: String(response.followup_question.followup_type || 'clarify'),
                                    reason: String(response.followup_question.reason || ''),
                                };
                                state.needsResume = false;
                                persistProgress();
                                renderState();
                                setTimeout(() => startCurrentQuestion(), 250);
                                return response;
                            }

                            if (recordingVariant === 'followup') {
                                state.followupPending = false;
                                state.followupQuestion = null;
                                state.followupType = '';
                                state.followupBaseQuestionIndex = null;
                                persistProgress();
                                renderState();
                                setTimeout(() => advanceQuestion(true), 250);
                                return response;
                            }

                            return response;
                        })
                ).catch((error) => {
                    console.warn('Unable to save interview answer:', error);
                    updateStatusBanner('Answer captured locally, but autosave failed. You can continue the interview.', 'warning');
                });
            }

            updateClipList();
            state.recordingIntegrityStart = null;
            setRecordingState('Clip saved', 'complete');
            state.lastRecorderStopPromiseResolve?.(savePromise);
            state.lastRecorderStopPromiseResolve = null;
        };

        state.recorder.start();
        startSpeechRecognition();
        setRecordingState('Recording live', 'recording');
    }

    function stopRecorder() {
        const stoppedPromise = new Promise((resolve) => {
            state.lastRecorderStopPromiseResolve = resolve;
        });
        const hadActiveRecorder = !!(state.recorder && state.recorder.state !== 'inactive');
        stopSpeechRecognition();
        if (hadActiveRecorder) state.recorder.stop();
        state.recorder = null;
        if (!hadActiveRecorder) {
            setTimeout(() => {
                state.lastRecorderStopPromiseResolve?.(Promise.resolve());
                state.lastRecorderStopPromiseResolve = null;
            }, 0);
        }
        return stoppedPromise;
    }

    function startCurrentQuestion() {
        if (!state.started || state.finished) return;
        if (state.transitioning) {
            renderState();
            return;
        }
        if (state.phase === 'round1') {
            renderState();
            return;
        }
        if (state.needsResume) {
            renderState();
            return;
        }
        if (state.sessionBannerVisible && state.sessionBannerKind === 'resume') {
            hideSessionRestoreBanner();
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

    function advanceQuestion(paced = false) {
        if (state.phase === 'round1') {
            if (state.round1Index < round1Questions.length - 1) {
                state.round1Index += 1;
                state.round1DraftAnswer = '';
                state.round1SelectedOption = '';
                resetRound1PasteTracking();
                persistProgress();
                renderState();
                startCurrentQuestion();
                return;
            }
            const beginRound2 = () => {
                state.phase = 'round2';
                state.sectionIndex = 0;
                state.questionIndex = 0;
                state.needsResume = false;
                updateStatusBanner('Round 1 complete. Beginning the verbal round.', 'success');
                ensureStream().catch(() => {
                    updateStatusBanner('Camera and microphone access is required for Round 2 recording.', 'warning');
                });
                persistProgress();
                renderState();
                startCurrentQuestion();
            };
            if (paced) {
                beginQuestionTransition('Round 1 complete. Preparing the verbal round...', beginRound2, 3);
            } else {
                beginRound2();
            }
            return;
        }

        const section = currentSection();
        if (!section) return;
        const moveNext = () => {
            if (state.questionIndex < totalQuestionsInSection(section) - 1) {
                state.questionIndex += 1;
            } else if (state.sectionIndex < round2Sections.length - 1) {
                state.sectionIndex += 1;
                state.questionIndex = 0;
            } else {
                finishSession();
                return;
            }
            state.needsResume = false;
            persistProgress();
            renderState();
            startCurrentQuestion();
        };

        if (paced) {
            const nextSection = state.questionIndex < totalQuestionsInSection(section) - 1
                ? section
                : (round2Sections[state.sectionIndex + 1] || null);
            const nextLabel = nextSection?.title || 'the next question';
            beginQuestionTransition(`Next question: ${nextLabel}`, moveNext, 3);
            return;
        }

        moveNext();
    }

    function movePreviousQuestion() {
        if (!state.started || state.finished || state.phase !== 'round2' || state.needsResume || isFollowupQuestion()) return;

        stopRecorder();

        if (state.questionIndex > 0) {
            state.questionIndex -= 1;
        } else if (state.sectionIndex > 0) {
            state.sectionIndex -= 1;
            const previousSection = round2Sections[state.sectionIndex] || null;
            state.questionIndex = Math.max(0, totalQuestionsInSection(previousSection) - 1);
        } else {
            return;
        }

        persistProgress();
        renderState();
        startCurrentQuestion();
    }

    async function stopCurrentQuestion(autoAdvance = false) {
        const stopPromise = stopRecorder();
        renderState();
        persistProgress();
        try {
            const maybeSavePromise = await stopPromise;
            if (maybeSavePromise && typeof maybeSavePromise.then === 'function') {
                await maybeSavePromise.catch(() => {});
            }
        } catch (error) {
            // ignore stop errors here; the recorder path already reports save issues
        }

        if (autoAdvance && !state.finished) {
            setTimeout(() => advanceQuestion(true), 50);
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
                client_context: JSON.stringify({
                    paste_event_count: state.round1PasteEventCount,
                    pasted_character_count: state.round1PastedCharacterCount,
                    copy_paste_detected: state.round1CopyPasteDetected,
                    large_insert_count: state.round1LargeInsertCount,
                    large_insert_character_count: state.round1LargeInsertCharacterCount,
                    large_insert_detected: state.round1LargeInsertDetected,
                    last_paste_at: state.round1PasteMeta?.detected_at || null,
                    last_large_insert_at: state.round1LargeInsertMeta?.detected_at || null,
                }),
                integrity_flags: JSON.stringify([
                    state.round1PasteEventCount > 0 ? 'text_paste_detected' : null,
                    state.round1LargeInsertDetected ? 'suspicious_text_insert' : null,
                ].filter(Boolean)),
                paste_event_count: state.round1PasteEventCount,
                pasted_character_count: state.round1PastedCharacterCount,
                large_insert_count: state.round1LargeInsertCount,
                large_insert_character_count: state.round1LargeInsertCharacterCount,
                copy_paste_detected: (state.round1CopyPasteDetected || state.round1LargeInsertDetected) ? 1 : 0,
                large_insert_detected: state.round1LargeInsertDetected ? 1 : 0,
            });
            state.round1SavedCount += 1;
            state.round1DraftAnswer = '';
            state.round1SelectedOption = '';
            resetRound1PasteTracking();
            persistProgress();
            advanceQuestion(true);
        } catch (error) {
            updateStatusBanner(error.message || 'Unable to save Round 1 answer.', 'warning');
        }
    }

    async function startSession(confirmed = false) {
        if (applicationId <= 0) {
            updateStatusBanner('Application context is missing. Please reopen interview from Applications.', 'warning');
            return;
        }

        if (state.started && state.interviewSessionId && !state.finished) {
            if (state.phase === 'round2' && !state.stream) {
                try {
                    await ensureStream();
                } catch (error) {
                    updateStatusBanner(error.message || 'Camera and microphone access is required to resume the session.', 'warning');
                    return;
                }
            }
            state.needsResume = false;
            state.inactivityWarnings = {};
            state.lastActivityAt = Date.now();
            state.lastActivityEventAt = Date.now();
            startTimer();
            renderState();
            showSessionRestoreBanner('Your previous progress is still available. Continue from where you left off.', 'info', 'resume', 0);
            if (state.phase === 'round2') {
                startCurrentQuestion();
            }
            await sendIntegrityEvent('resume', {
                resume_reason: 'reload_or_return',
                phase: state.phase,
                section_key: currentSection()?.key || '',
                question_index: state.questionIndex,
            }, 'info');
            persistProgress();
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
        state.finished = false;
        state.needsResume = false;
        state.tabSwitchCount = 0;
        state.hiddenDurationSeconds = 0;
        state.hiddenAt = null;
        state.integrityWarningCount = 0;
        state.reconnectCount = 0;
        state.timeoutWarnings = {};
        state.inactivityWarnings = {};
        state.lastActivityAt = Date.now();
        state.lastActivityEventAt = Date.now();
        state.lastTabTransitionAt = null;
        state.lastTabTransitionType = '';
        resetRound1PasteTracking();
        state.recordingIntegrity = null;
        state.recordingIntegrityStart = null;
        state.sessionEndsAt = Date.now() + (totalTimerSeconds * 1000);
        state.remainingSeconds = totalTimerSeconds;
        startTimer();
        if (startSessionBtn) startSessionBtn.innerHTML = '<i class="fas fa-redo mr-1"></i> Session Running';
        updateStatusBanner('Interview started. Complete Round 1 first, then Round 2 verbal responses.', 'success');
        hideSessionRestoreBanner();
        await sendIntegrityEvent('session_started', {
            started_from: 'browser',
            job_id: jobId,
            resume_version_id: resumeVersionId,
        }, 'info');
        persistProgress();
        renderState();
    }

    async function finishSession() {
        clearQuestionTransition();
        const recorderStopResult = await stopRecorder();
        if (recorderStopResult && typeof recorderStopResult.then === 'function') {
            await recorderStopResult.catch(() => {});
        }
        if (state.pendingSaveCount > 0) {
            updateStatusBanner('Saving the last response. Please wait a moment...', 'warning');
            updateSyncingIndicator();
            const startedAt = Date.now();
            while (state.pendingSaveCount > 0 && (Date.now() - startedAt) < 10000) {
                await new Promise((resolve) => setTimeout(resolve, 150));
                updateSyncingIndicator();
            }
        }

        stopTimer();
        state.sectionIndex = round2Sections.length;
        state.questionIndex = 0;
        stopLiveMedia();
        setRecordingState('Finalizing session', 'warning');
        updateStatusBanner('Submitting your final response...', 'warning');

        if (applicationId > 0 && state.interviewSessionId) {
            try {
                await postInterviewJson(interviewEndpoint(`complete/${applicationId}`), {
                    interview_session_id: state.interviewSessionId,
                    completed_at: Math.floor(Date.now() / 1000),
                });
                await sendIntegrityEvent('session_completed', {
                    tab_switch_count: state.tabSwitchCount,
                    hidden_duration_seconds: state.hiddenDurationSeconds,
                    reconnect_count: state.reconnectCount,
                    integrity_warning_count: state.integrityWarningCount,
                }, 'info');
            } catch (error) {
                updateStatusBanner('Interview finished, but final sync failed. Please refresh and check applications status.', 'warning');
            }
        }

        state.finished = true;
        state.needsResume = false;
        state.pendingSaveCount = 0;
        updateSyncingIndicator();
        stopLiveMedia();
        setRecordingState('Session complete', 'complete');
        updateStatusBanner('Interview session complete. Your responses have been captured successfully.', 'success');
        clearPersistedProgress();
        renderState();
    }

    function renderState() {
        updateSectionNav();
        updateQuestionPanel();
        updateTimer();
        updateClipList();
        updateSyncingIndicator();
        renderSessionRestoreBanner();
        if (state.started && !state.finished) {
            persistProgress();
        }
    }

    async function handleVisibilityChange() {
        if (!state.started || state.finished) return;
        const visibilityState = document.visibilityState || 'visible';
        if (visibilityState === 'hidden') {
            if (!canRecordTabTransition('tab_hidden')) return;
            updateStatusBanner('The interview tab was hidden. Please keep the session visible while answering.', 'warning');
            await sendIntegrityEvent('tab_hidden', {
                visibility_state: visibilityState,
                section_key: currentSection()?.key || '',
                question_index: state.questionIndex,
                phase: state.phase,
            }, 'warning');
            return;
        }

        const hiddenDurationSeconds = state.hiddenAt
            ? Math.max(0, Math.round((Date.now() - Number(state.hiddenAt)) / 1000))
            : 0;
        if (!canRecordTabTransition('tab_visible')) return;
        registerActivity();
        await sendIntegrityEvent('tab_visible', {
            visibility_state: visibilityState,
            hidden_duration_seconds: hiddenDurationSeconds,
            section_key: currentSection()?.key || '',
            question_index: state.questionIndex,
            phase: state.phase,
        }, 'info');

        if (state.phase === 'round2' && !state.stream) {
            try {
                await ensureStream();
            } catch (error) {
                updateStatusBanner('Camera and microphone access is required to continue the interview.', 'warning');
            }
        }
        await refreshActiveSessionFromServer({ announceReconnect: true });
        renderState();
    }

    async function handleWindowBlur() {
        if (!state.started || state.finished) return;
        if (document.hidden) return;
        if (!canRecordTabTransition('blur')) return;
        if (!state.hiddenAt) state.hiddenAt = Date.now();
        updateStatusBanner('The interview window lost focus. Please stay on the interview while answering.', 'warning');
        await sendIntegrityEvent('blur', {
            visibility_state: document.visibilityState || 'visible',
            section_key: currentSection()?.key || '',
            question_index: state.questionIndex,
            phase: state.phase,
        }, 'warning');
    }

    async function handleWindowFocus() {
        if (!state.started || state.finished) return;
        if (document.hidden) return;
        if (state.hiddenAt === null && state.lastTabTransitionType !== 'blur') {
            registerActivity();
            return;
        }
        if (!canRecordTabTransition('focus')) return;
        const hiddenDurationSeconds = state.hiddenAt
            ? Math.max(0, Math.round((Date.now() - Number(state.hiddenAt)) / 1000))
            : 0;
        registerActivity();
        await sendIntegrityEvent('focus', {
            visibility_state: document.visibilityState || 'visible',
            hidden_duration_seconds: hiddenDurationSeconds,
            section_key: currentSection()?.key || '',
            question_index: state.questionIndex,
            phase: state.phase,
        }, 'info');
        await refreshActiveSessionFromServer({ announceReconnect: true });
        renderState();
    }

    async function handleConnectivityChange() {
        if (!state.started || state.finished) return;
        if (!navigator.onLine) {
            updateStatusBanner('You appear to be offline. The interview will keep recording locally and reconnect when the network returns.', 'warning');
            await sendIntegrityEvent('offline', { online: false }, 'warning');
            return;
        }

        registerActivity();
        await sendIntegrityEvent('online', { online: true }, 'info');
        await refreshActiveSessionFromServer({ announceReconnect: true });
    }

    function bindActivityTracking() {
        const mark = () => registerActivity();
        ['pointerdown', 'keydown', 'touchstart', 'click', 'scroll'].forEach((eventName) => {
            window.addEventListener(eventName, mark, { passive: true, capture: true });
        });
        document.addEventListener('input', mark, { passive: true, capture: true });
        document.addEventListener('change', mark, { passive: true, capture: true });
    }

    function bindEvents() {
        bindActivityTracking();
        if (startSessionBtn) startSessionBtn.addEventListener('click', () => startSession(true));
        if (round1TextAnswer) {
            round1TextAnswer.addEventListener('input', () => {
                const nextValue = String(round1TextAnswer.value || '');
                registerRound1LargeInsert(nextValue, currentRound1Question(), '');
                state.round1DraftAnswer = nextValue;
                persistProgress();
            });
            round1TextAnswer.addEventListener('paste', (event) => {
                registerRound1Paste(event);
            });
        }
        if (round1OptionList) {
            round1OptionList.addEventListener('change', (event) => {
                const target = event.target;
                if (!target || target.name !== 'round1_option') return;
                state.round1SelectedOption = String(target.value || '');
                persistProgress();
            });
        }
        if (nextQuestionBtn) {
            nextQuestionBtn.addEventListener('click', async () => {
                if (!state.started || state.phase !== 'round2') return;
                nextQuestionBtn.disabled = true;
                await stopCurrentQuestion(true);
            });
        }
        if (previousQuestionBtn) {
            previousQuestionBtn.addEventListener('click', movePreviousQuestion);
        }
        if (finishSessionBtn) {
            finishSessionBtn.addEventListener('click', () => {
                if (!state.started) return;
                const confirmText = 'Are you sure you want to finish the interview now? All unsaved progress for the current question will be lost.';
                if (!window.confirm(confirmText)) return;
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
            persistProgress();
            stopTimer();
            stopRecorder();
            clearQuestionTransition();
            stopLiveMedia();
        });
        document.addEventListener('visibilitychange', () => {
            void handleVisibilityChange();
        });
        window.addEventListener('blur', () => {
            void handleWindowBlur();
        });
        window.addEventListener('focus', () => {
            void handleWindowFocus();
        });
        window.addEventListener('online', () => {
            void handleConnectivityChange();
        });
        window.addEventListener('offline', () => {
            void handleConnectivityChange();
        });
    }

    async function init() {
        state.mimeType = chooseMimeType();
        const localSnapshot = loadPersistedProgress();
        if (localSnapshot) {
            applyProgressSnapshot(localSnapshot);
        }
        try {
            const serverResponse = await fetch(interviewEndpoint(`begin/${applicationId}`), {
                method: 'GET',
                headers: { 'Accept': 'application/json' },
                credentials: 'same-origin',
            });
                const serverData = await serverResponse.json().catch(() => ({}));
                if (serverData && serverData.success && serverData.snapshot) {
                    const localSessionId = Number(localSnapshot?.interviewSessionId || 0) || Number(localSnapshot?.interview_session_id || 0) || 0;
                    const serverSessionId = Number(serverData.snapshot.interview_session_id || 0) || 0;
                    if (!localSessionId || localSessionId !== serverSessionId) {
                        applyProgressSnapshot(serverData.snapshot);
                    } else {
                        const localAgeMs = Number(localSnapshot?.savedAt || 0) ? (Date.now() - Number(localSnapshot.savedAt)) : Number.POSITIVE_INFINITY;
                        const preferLocalTimer = Number.isFinite(localAgeMs) && localAgeMs < 15000;
                        state.started = false;
                        state.finished = !!serverData.snapshot.finished;
                        state.phase = serverData.snapshot.phase || state.phase;
                        state.round1Index = Number(localSnapshot.round1Index ?? serverData.snapshot.round1_index ?? state.round1Index ?? 0) || 0;
                        state.round1SavedCount = Number(localSnapshot.round1SavedCount ?? serverData.snapshot.round1_saved_count ?? state.round1SavedCount ?? 0) || 0;
                        state.sectionIndex = Number(localSnapshot.sectionIndex ?? serverData.snapshot.section_index ?? state.sectionIndex ?? 0) || 0;
                        state.questionIndex = Number(localSnapshot.questionIndex ?? serverData.snapshot.question_index ?? state.questionIndex ?? 0) || 0;
                        const mergedRemaining = preferLocalTimer && localSnapshot.remainingSeconds !== undefined && localSnapshot.remainingSeconds !== null
                            ? Number(localSnapshot.remainingSeconds)
                            : (serverData.snapshot.remaining_seconds !== undefined && serverData.snapshot.remaining_seconds !== null
                                ? Number(serverData.snapshot.remaining_seconds)
                                : Number(state.remainingSeconds));
                        state.remainingSeconds = Number.isFinite(mergedRemaining) ? mergedRemaining : totalTimerSeconds;
                        state.sessionEndsAt = serverData.snapshot.session_ends_at
                            ? Number(serverData.snapshot.session_ends_at) * 1000
                            : (Number(localSnapshot.sessionEndsAt ?? 0) || state.sessionEndsAt);
                        state.interviewSessionId = Number(serverData.snapshot.interview_session_id || localSessionId || 0) || null;
                        state.needsResume = !!(serverData.snapshot.finished ? false : (state.sectionIndex > 0 || state.questionIndex > 0 || state.round1SavedCount > 0));
                        state.round1DraftAnswer = String(localSnapshot.round1DraftAnswer || state.round1DraftAnswer || '');
                        state.round1SelectedOption = String(localSnapshot.round1SelectedOption || state.round1SelectedOption || '');
                        state.transcriptBuffer = String(localSnapshot.transcriptBuffer || state.transcriptBuffer || '');
                    state.transcriptFinalParts = Array.isArray(localSnapshot.transcriptFinalParts) ? localSnapshot.transcriptFinalParts : state.transcriptFinalParts;
                        state.lastActivityAt = Number(localSnapshot.lastActivityAt ?? localSnapshot.last_activity_at ?? state.lastActivityAt ?? 0) || state.lastActivityAt;
                        state.lastActivityEventAt = Number(localSnapshot.lastActivityEventAt ?? localSnapshot.last_activity_event_at ?? state.lastActivityEventAt ?? 0) || state.lastActivityEventAt;
                        state.inactivityWarnings = localSnapshot.inactivityWarnings && typeof localSnapshot.inactivityWarnings === 'object' ? localSnapshot.inactivityWarnings : state.inactivityWarnings;
                }
            }
        } catch (error) {
            // stay with local snapshot or fresh state
        }
        if (!state.started) {
            state.needsResume = false;
        }
        if (state.started && !state.finished && !state.lastActivityAt) {
            state.lastActivityAt = Date.now();
        }
        if (!state.finished && (state.needsResume || state.round1SavedCount > 0 || state.sectionIndex > 0 || state.questionIndex > 0)) {
            showSessionRestoreBanner(
                state.needsResume
                    ? 'Your previous progress is still available. Resume the interview when you are ready.'
                    : 'Your interview progress has been restored.',
                'info',
                'resume',
                0
            );
        }
        renderState();
        bindEvents();
        if (!state.recordingSupported) {
            setRecordingState('Recording not supported', 'warning');
        } else if (state.started && state.needsResume) {
            setRecordingState('Session restored. Resume recording.', 'warning');
        } else {
            setRecordingState('Ready to begin', 'light');
        }
        persistProgress();
    }

    init();
})();
