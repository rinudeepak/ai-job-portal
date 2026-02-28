<?php

namespace App\Libraries;

class ResumeTemplateRenderer
{
    public function getTemplates(): array
    {
        return [
            'modern_professional' => [
                'label' => 'Modern Professional',
                'description' => 'Clean layout with strong summary and metric-friendly sections.',
                'tier' => 'free',
                'accent' => '#2563eb',
                'badge' => 'ATS Friendly',
                'preview_class' => 'modern',
            ],
            'executive_sidebar' => [
                'label' => 'Executive Sidebar',
                'description' => 'Two-column layout with a premium sidebar for skills and credentials.',
                'tier' => 'pro',
                'accent' => '#0f766e',
                'badge' => 'Leadership',
                'preview_class' => 'sidebar',
            ],
            'minimal_timeline' => [
                'label' => 'Minimal Timeline',
                'description' => 'Editorial layout focused on experience progression and clarity.',
                'tier' => 'free',
                'accent' => '#1d4ed8',
                'badge' => 'Career Story',
                'preview_class' => 'timeline',
            ],
        ];
    }

    public function getTemplateLabel(?string $templateKey): string
    {
        $templates = $this->getTemplates();
        $key = $templateKey ?: 'modern_professional';

        return $templates[$key]['label'] ?? $templates['modern_professional']['label'];
    }

    public function decodeStoredContent(string $content, array $fallback = []): array
    {
        $content = trim($content);
        $decoded = json_decode($content, true);

        if (is_array($decoded) && isset($decoded['sections'])) {
        return [
            'template_key' => (string) ($decoded['template_key'] ?? 'modern_professional'),
            'name' => (string) ($decoded['name'] ?? ($fallback['name'] ?? 'Candidate')),
            'target_role' => (string) ($decoded['target_role'] ?? ($fallback['target_role'] ?? '')),
            'summary' => (string) ($decoded['summary'] ?? ($fallback['summary'] ?? '')),
            'highlight_skills' => array_values(array_filter(array_map('trim', (array) ($decoded['highlight_skills'] ?? ($fallback['highlight_skills'] ?? []))))),
            'sections' => $decoded['sections'],
            'legacy_text' => '',
        ];
        }

        return [
            'template_key' => 'modern_professional',
            'name' => (string) ($fallback['name'] ?? 'Candidate'),
            'target_role' => (string) ($fallback['target_role'] ?? ''),
            'summary' => (string) ($fallback['summary'] ?? ''),
            'highlight_skills' => array_values(array_filter(array_map('trim', (array) ($fallback['highlight_skills'] ?? [])))),
            'sections' => [],
            'legacy_text' => $content,
        ];
    }

    public function renderPreview(string $content, array $fallback = []): string
    {
        $resume = $this->decodeStoredContent($content, $fallback);
        if ($resume['legacy_text'] !== '') {
            return '<div class="resume-template-shell"><pre style="white-space:pre-wrap;margin:0;font-family:inherit;">'
                . esc($resume['legacy_text'])
                . '</pre></div>';
        }

        return $this->renderHtml($resume, false);
    }

    public function renderDocument(string $content, array $fallback = []): string
    {
        $resume = $this->decodeStoredContent($content, $fallback);
        if ($resume['legacy_text'] !== '') {
            return '<!doctype html><html><head><meta charset="utf-8"><title>Resume</title></head><body><pre style="white-space:pre-wrap;font-family:Arial,sans-serif;">'
                . esc($resume['legacy_text'])
                . '</pre></body></html>';
        }

        return '<!doctype html><html><head><meta charset="utf-8"><title>'
            . esc(($resume['name'] ?: 'Candidate') . ' Resume')
            . '</title><style>' . $this->documentStyles() . '</style></head><body>'
            . $this->renderHtml($resume, true)
            . '</body></html>';
    }

    public function createPdfFile(string $content, array $fallback, string $filenameBase): string
    {
        require_once APPPATH . '../vendor/autoload.php';
        $resume = $this->decodeStoredContent($content, $fallback);

        $pdf = new \TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
        $pdf->SetCreator('AI Job Portal');
        $pdf->SetAuthor('AI Job Portal');
        $pdf->SetTitle($filenameBase);
        $pdf->setPrintHeader(false);
        $pdf->setPrintFooter(false);
        $pdf->SetMargins(12, 12, 12);
        $pdf->SetAutoPageBreak(true, 12);
        $pdf->AddPage();
        $pdf->SetFont('helvetica', '', 10);
        $pdf->writeHTML($this->renderPdfHtml($resume), true, false, true, false, '');

        $directory = WRITEPATH . 'uploads/resume_versions/';
        if (!is_dir($directory)) {
            mkdir($directory, 0755, true);
        }

        $filename = $this->sanitizeFilename($filenameBase) . '.pdf';
        $filepath = $directory . $filename;
        $pdf->Output($filepath, 'F');

        return $filepath;
    }

    private function renderPdfHtml(array $resume): string
    {
        if ($resume['legacy_text'] !== '') {
            return '<div style="font-family:helvetica;font-size:10pt;line-height:1.5;">'
                . nl2br(esc($resume['legacy_text']))
                . '</div>';
        }

        $sections = (array) ($resume['sections'] ?? []);
        $html = '';
        $html .= '<table cellpadding="0" cellspacing="0" border="0" width="100%" style="font-family:helvetica;">';
        $html .= '<tr>';
        $html .= '<td width="65%" style="font-size:24pt;font-weight:bold;color:#0f172a;">' . esc((string) ($resume['name'] ?? 'Candidate')) . '</td>';
        $html .= '<td width="35%" align="right" style="font-size:10pt;color:#475569;">'
            . ($resume['target_role'] !== '' ? esc((string) $resume['target_role']) : '&nbsp;')
            . '</td>';
        $html .= '</tr>';
        $html .= '</table>';

        if (!empty($resume['highlight_skills'])) {
            $html .= '<div style="margin-top:6px;font-size:9pt;color:#1d4ed8;">'
                . esc(implode(' | ', (array) $resume['highlight_skills']))
                . '</div>';
        }

        $html .= '<hr style="border:0;border-top:1px solid #cbd5e1;margin:10px 0 12px 0;">';

        if (trim((string) ($resume['summary'] ?? '')) !== '') {
            $html .= $this->renderPdfSectionTitle('Professional Summary');
            $html .= '<div style="font-size:10.2pt;line-height:1.6;color:#334155;">'
                . nl2br(esc((string) $resume['summary']))
                . '</div>';
        }

        if (!empty($sections['skills']['groups'])) {
            $html .= $this->renderPdfSkills((array) $sections['skills']);
        }

        foreach (['experience', 'projects', 'education', 'certifications'] as $key) {
            if (empty($sections[$key])) {
                continue;
            }

            $section = (array) $sections[$key];
            if (!empty($section['items'])) {
                $html .= $this->renderPdfItemsSection((string) ($section['title'] ?? ucfirst($key)), (array) $section['items']);
            }
        }

        return $html;
    }

    private function renderPdfSectionTitle(string $title): string
    {
        return '<div style="margin-top:14px;margin-bottom:6px;font-size:11pt;font-weight:bold;letter-spacing:.4px;color:#0f172a;text-transform:uppercase;border-bottom:1px solid #dbe3ee;padding-bottom:3px;">'
            . esc($title)
            . '</div>';
    }

    private function renderPdfSkills(array $section): string
    {
        $groups = (array) ($section['groups'] ?? []);
        if (empty($groups)) {
            return '';
        }

        $html = $this->renderPdfSectionTitle((string) ($section['title'] ?? 'Technical Skills'));
        $html .= '<table cellpadding="4" cellspacing="0" border="0" width="100%">';
        foreach ($groups as $group) {
            $label = trim((string) ($group['label'] ?? ''));
            $items = array_values(array_filter(array_map('trim', (array) ($group['items'] ?? []))));
            if ($label === '' || empty($items)) {
                continue;
            }

            $html .= '<tr>';
            $html .= '<td width="24%" style="font-size:9.5pt;font-weight:bold;color:#0f766e;">' . esc($label) . '</td>';
            $html .= '<td width="76%" style="font-size:9.8pt;color:#334155;">' . esc(implode(' | ', $items)) . '</td>';
            $html .= '</tr>';
        }
        $html .= '</table>';

        return $html;
    }

    private function renderPdfItemsSection(string $title, array $items): string
    {
        if (empty($items)) {
            return '';
        }

        $html = $this->renderPdfSectionTitle($title);
        foreach ($items as $item) {
            $headline = trim((string) ($item['headline'] ?? ''));
            $subhead = trim((string) ($item['subhead'] ?? ''));
            $meta = trim((string) ($item['meta'] ?? ''));
            $bullets = array_values(array_filter(array_map('trim', (array) ($item['bullets'] ?? []))));

            $html .= '<table cellpadding="0" cellspacing="0" border="0" width="100%" style="margin-top:6px;">';
            $html .= '<tr>';
            $html .= '<td width="68%" style="font-size:10.5pt;font-weight:bold;color:#111827;">' . esc($headline) . '</td>';
            $html .= '<td width="32%" align="right" style="font-size:9pt;color:#64748b;">' . esc($meta) . '</td>';
            $html .= '</tr>';
            if ($subhead !== '') {
                $html .= '<tr><td colspan="2" style="font-size:9.6pt;color:#1d4ed8;font-weight:bold;padding-top:1px;">' . esc($subhead) . '</td></tr>';
            }
            $html .= '</table>';

            if (!empty($bullets)) {
                $html .= '<ul style="margin-top:4px;margin-bottom:8px;">';
                foreach ($bullets as $bullet) {
                    $html .= '<li style="font-size:9.8pt;line-height:1.6;color:#334155;">' . esc($bullet) . '</li>';
                }
                $html .= '</ul>';
            }
        }

        return $html;
    }

    private function renderHtml(array $resume, bool $fullDocument): string
    {
        $templateKey = (string) ($resume['template_key'] ?? 'modern_professional');

        if ($templateKey === 'executive_sidebar') {
            return $this->renderExecutiveSidebar($resume, $fullDocument);
        }

        if ($templateKey === 'minimal_timeline') {
            return $this->renderMinimalTimeline($resume, $fullDocument);
        }

        return $this->renderModernProfessional($resume, $fullDocument);
    }

    private function renderModernProfessional(array $resume, bool $fullDocument): string
    {
        $sections = $resume['sections'];
        $skills = $resume['highlight_skills'];

        $html = '<div class="resume-template-shell template-modern">';
        $html .= '<div class="resume-hero">';
        $html .= '<div><div class="resume-name">' . esc($resume['name']) . '</div>';
        if ($resume['target_role'] !== '') {
            $html .= '<div class="resume-role">' . esc($resume['target_role']) . '</div>';
        }
        $html .= '</div>';
        $html .= '<div class="resume-chip-row">' . $this->renderSkillChips($skills) . '</div>';
        $html .= '</div>';
        $html .= $this->renderSummaryBlock($resume['summary']);
        if (!empty($sections['skills']['groups'])) {
            $html .= $this->renderSkillGroupsSection((string) ($sections['skills']['title'] ?? 'Technical Skills'), (array) $sections['skills']['groups']);
        }
        $html .= $this->renderSectionList($sections, ['experience', 'projects', 'education', 'certifications']);
        $html .= '</div>';

        return $html;
    }

    private function renderExecutiveSidebar(array $resume, bool $fullDocument): string
    {
        $sections = $resume['sections'];
        $skills = $resume['highlight_skills'];
        $education = $sections['education']['items'] ?? [];
        $certifications = $sections['certifications']['items'] ?? [];

        $html = '<div class="resume-template-shell template-sidebar">';
        $html .= '<div class="resume-sidebar">';
        $html .= '<div class="resume-name">' . esc($resume['name']) . '</div>';
        if ($resume['target_role'] !== '') {
            $html .= '<div class="resume-role">' . esc($resume['target_role']) . '</div>';
        }
        $html .= $this->renderSummaryBlock($resume['summary']);
        $html .= !empty($sections['skills']['groups'])
            ? $this->renderSkillGroupsSection((string) ($sections['skills']['title'] ?? 'Technical Skills'), (array) $sections['skills']['groups'])
            : $this->renderNamedList('Technical Skills', $skills);
        $html .= $this->renderCompactList('Education', $education);
        $html .= $this->renderCompactList('Certifications', $certifications);
        $html .= '</div>';
        $html .= '<div class="resume-main">';
        $html .= $this->renderSectionList($sections, ['experience', 'projects']);
        $html .= '</div>';
        $html .= '</div>';

        return $html;
    }

    private function renderMinimalTimeline(array $resume, bool $fullDocument): string
    {
        $sections = $resume['sections'];
        $html = '<div class="resume-template-shell template-timeline">';
        $html .= '<div class="resume-name">' . esc($resume['name']) . '</div>';
        if ($resume['target_role'] !== '') {
            $html .= '<div class="resume-role">' . esc($resume['target_role']) . '</div>';
        }
        $html .= $this->renderSummaryBlock($resume['summary']);
        if (!empty($sections['skills']['groups'])) {
            $html .= $this->renderSkillGroupsSection((string) ($sections['skills']['title'] ?? 'Technical Skills'), (array) $sections['skills']['groups']);
        } else {
            $html .= $this->renderNamedList('Technical Skills', $resume['highlight_skills']);
        }
        $html .= $this->renderTimelineSection($sections['experience']['title'] ?? 'Professional Experience', $sections['experience']['items'] ?? []);
        $html .= $this->renderTimelineSection($sections['projects']['title'] ?? 'Projects', $sections['projects']['items'] ?? []);
        $html .= $this->renderCompactList($sections['education']['title'] ?? 'Education', $sections['education']['items'] ?? []);
        $html .= $this->renderCompactList($sections['certifications']['title'] ?? 'Certifications', $sections['certifications']['items'] ?? []);
        $html .= '</div>';

        return $html;
    }

    private function renderSummaryBlock(string $summary): string
    {
        if (trim($summary) === '') {
            return '';
        }

        return '<div class="resume-summary">' . nl2br(esc($summary)) . '</div>';
    }

    private function renderSectionList(array $sections, array $order): string
    {
        $html = '';
        foreach ($order as $key) {
            if (empty($sections[$key])) {
                continue;
            }

            $section = $sections[$key];
            if (!empty($section['items'])) {
                $html .= $this->renderItemsSection((string) ($section['title'] ?? ucfirst($key)), (array) $section['items']);
            } elseif (!empty($section['body'])) {
                $html .= $this->renderTextSection((string) ($section['title'] ?? ucfirst($key)), (string) $section['body']);
            }
        }

        return $html;
    }

    private function renderItemsSection(string $title, array $items): string
    {
        if (empty($items)) {
            return '';
        }

        $html = '<section class="resume-section"><h3>' . esc($title) . '</h3>';
        foreach ($items as $item) {
            $headline = trim((string) ($item['headline'] ?? ''));
            $subhead = trim((string) ($item['subhead'] ?? ''));
            $meta = trim((string) ($item['meta'] ?? ''));
            $bullets = array_values(array_filter(array_map('trim', (array) ($item['bullets'] ?? []))));

            $html .= '<div class="resume-item">';
            if ($headline !== '' || $subhead !== '') {
                $html .= '<div class="resume-item-head">';
                $html .= '<div class="resume-item-title">' . esc($headline) . '</div>';
                if ($subhead !== '') {
                    $html .= '<div class="resume-item-subhead">' . esc($subhead) . '</div>';
                }
                $html .= '</div>';
            }
            if ($meta !== '') {
                $html .= '<div class="resume-item-meta">' . esc($meta) . '</div>';
            }
            if (!empty($bullets)) {
                $html .= '<ul>';
                foreach ($bullets as $bullet) {
                    $html .= '<li>' . esc($bullet) . '</li>';
                }
                $html .= '</ul>';
            }
            $html .= '</div>';
        }
        $html .= '</section>';

        return $html;
    }

    private function renderTimelineSection(string $title, array $items): string
    {
        if (empty($items)) {
            return '';
        }

        $html = '<section class="resume-section"><h3>' . esc($title) . '</h3><div class="timeline">';
        foreach ($items as $item) {
            $html .= '<div class="timeline-item">';
            $html .= '<div class="timeline-dot"></div><div class="timeline-body">';
            $html .= '<div class="resume-item-title">' . esc((string) ($item['headline'] ?? '')) . '</div>';
            if (!empty($item['subhead'])) {
                $html .= '<div class="resume-item-subhead">' . esc((string) $item['subhead']) . '</div>';
            }
            if (!empty($item['meta'])) {
                $html .= '<div class="resume-item-meta">' . esc((string) $item['meta']) . '</div>';
            }
            if (!empty($item['bullets'])) {
                $html .= '<ul>';
                foreach ((array) $item['bullets'] as $bullet) {
                    $html .= '<li>' . esc((string) $bullet) . '</li>';
                }
                $html .= '</ul>';
            }
            $html .= '</div></div>';
        }
        $html .= '</div></section>';

        return $html;
    }

    private function renderCompactList(string $title, array $items): string
    {
        if (empty($items)) {
            return '';
        }

        $html = '<section class="resume-section compact-section"><h3>' . esc($title) . '</h3><div class="compact-list">';
        foreach ($items as $item) {
            $line = trim(implode(' | ', array_filter([
                (string) ($item['headline'] ?? ''),
                (string) ($item['subhead'] ?? ''),
                (string) ($item['meta'] ?? ''),
            ])));
            if ($line !== '') {
                $html .= '<div>' . esc($line) . '</div>';
            }
        }
        $html .= '</div></section>';

        return $html;
    }

    private function renderNamedList(string $title, array $items): string
    {
        $items = array_values(array_filter(array_map('trim', $items)));
        if (empty($items)) {
            return '';
        }

        return '<section class="resume-section compact-section"><h3>' . esc($title) . '</h3><div class="resume-chip-row">'
            . $this->renderSkillChips($items)
            . '</div></section>';
    }

    private function renderSkillGroupsSection(string $title, array $groups): string
    {
        if (empty($groups)) {
            return '';
        }

        $html = '<section class="resume-section"><h3>' . esc($title) . '</h3><div class="skill-groups">';
        foreach ($groups as $group) {
            $label = trim((string) ($group['label'] ?? ''));
            $items = array_values(array_filter(array_map('trim', (array) ($group['items'] ?? []))));
            if ($label === '' || empty($items)) {
                continue;
            }

            $html .= '<div class="skill-group">';
            $html .= '<div class="skill-group-label">' . esc($label) . '</div>';
            $html .= '<div class="skill-group-items">' . esc(implode(' | ', $items)) . '</div>';
            $html .= '</div>';
        }
        $html .= '</div></section>';

        return $html;
    }

    private function renderTextSection(string $title, string $body): string
    {
        if (trim($body) === '') {
            return '';
        }

        return '<section class="resume-section"><h3>' . esc($title) . '</h3><div class="resume-text">'
            . nl2br(esc($body))
            . '</div></section>';
    }

    private function renderSkillChips(array $skills): string
    {
        $html = '';
        foreach (array_values(array_filter(array_map('trim', $skills))) as $skill) {
            $html .= '<span class="resume-chip">' . esc($skill) . '</span>';
        }

        return $html;
    }

    private function documentStyles(): string
    {
        return '
            body{margin:0;padding:24px;background:#edf2f7;font-family:Segoe UI,Arial,sans-serif;color:#0f172a}
            .resume-template-shell{background:#fff;border-radius:28px;padding:34px;box-shadow:0 24px 70px rgba(15,23,42,.1);max-width:980px;margin:0 auto}
            .template-modern{background:linear-gradient(180deg,#ffffff 0%,#f9fbff 100%)}
            .resume-hero{display:flex;justify-content:space-between;gap:24px;align-items:flex-start;margin-bottom:22px;padding-bottom:20px;border-bottom:1px solid #dbe7f3}
            .resume-name{font-size:2.2rem;font-weight:800;line-height:1.05;color:#0f172a;letter-spacing:-.03em}
            .resume-role{font-size:1rem;font-weight:700;color:#0f766e;margin-top:8px;text-transform:uppercase;letter-spacing:.08em}
            .resume-summary{font-size:1rem;line-height:1.75;color:#334155;margin-bottom:20px;max-width:72ch}
            .resume-section{margin-top:24px}
            .resume-section h3{font-size:.82rem;letter-spacing:.16em;text-transform:uppercase;color:#64748b;margin-bottom:14px}
            .resume-item{padding:15px 0;border-top:1px solid #eef2f7}
            .resume-item:first-child{border-top:0;padding-top:0}
            .resume-item-head{display:flex;justify-content:space-between;gap:12px;flex-wrap:wrap}
            .resume-item-title{font-size:1rem;font-weight:800;color:#111827}
            .resume-item-subhead{font-size:.94rem;font-weight:700;color:#2563eb}
            .resume-item-meta{font-size:.84rem;color:#64748b;margin-top:5px}
            .resume-item ul{margin:10px 0 0 18px;padding:0}
            .resume-item li{margin:0 0 7px;color:#334155;line-height:1.62}
            .resume-chip-row{display:flex;flex-wrap:wrap;gap:8px}
            .resume-chip{display:inline-flex;align-items:center;padding:7px 12px;border-radius:999px;background:#eff6ff;color:#1d4ed8;font-size:.8rem;font-weight:800}
            .skill-groups{display:grid;gap:12px}
            .skill-group{display:grid;grid-template-columns:170px 1fr;gap:16px;padding:10px 0;border-top:1px solid #eef2f7}
            .skill-group:first-child{border-top:0;padding-top:0}
            .skill-group-label{font-size:.82rem;font-weight:800;text-transform:uppercase;letter-spacing:.08em;color:#0f766e}
            .skill-group-items{font-size:.95rem;color:#334155;line-height:1.7}
            .template-sidebar{display:grid;grid-template-columns:300px 1fr;gap:28px;background:#fff}
            .resume-sidebar{background:linear-gradient(180deg,#0f172a 0%,#172554 100%);color:#fff;border-radius:22px;padding:26px}
            .resume-sidebar .resume-name,.resume-sidebar .resume-role,.resume-sidebar .resume-section h3,.resume-sidebar .resume-summary,.resume-sidebar .compact-list div{color:#fff}
            .resume-sidebar .resume-chip{background:rgba(255,255,255,.12);color:#fff}
            .resume-sidebar .skill-group{border-top-color:rgba(255,255,255,.16)}
            .resume-sidebar .skill-group-label,.resume-sidebar .skill-group-items{color:#fff}
            .resume-main{padding-top:6px}
            .template-timeline{background:linear-gradient(180deg,#ffffff 0%,#f8fafc 100%)}
            .template-timeline .timeline{position:relative;padding-left:24px}
            .template-timeline .timeline:before{content:"";position:absolute;left:6px;top:0;bottom:0;width:2px;background:#dbeafe}
            .timeline-item{position:relative;padding:0 0 18px 10px}
            .timeline-dot{position:absolute;left:-24px;top:4px;width:14px;height:14px;border-radius:50%;background:#0f766e;box-shadow:0 0 0 4px #ccfbf1}
            .compact-section .compact-list div{margin-bottom:8px;color:#334155;line-height:1.5}
            .resume-text{line-height:1.7;color:#334155}
        ';
    }

    private function sanitizeFilename(string $value): string
    {
        $value = strtolower(trim($value));
        $value = preg_replace('/[^a-z0-9]+/i', '-', $value);
        $value = trim((string) $value, '-');

        return $value !== '' ? $value : 'resume';
    }
}
