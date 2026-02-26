@extends('layouts.app')

@section('header', 'User Manual')

@push('styles')
<style>
    :root { --manual-accent: #0d9488; --manual-accent-light: rgba(13,148,136,.1); }
    #manual-toc { position: sticky; top: 5.5rem; max-height: calc(100vh - 6.5rem); overflow-y: auto; font-size: .8125rem; }
    #manual-toc .toc-link { display: block; padding: .3rem .75rem; color: #64748b; text-decoration: none; border-left: 3px solid transparent; border-radius: 0 4px 4px 0; transition: all .2s; line-height: 1.4; }
    #manual-toc .toc-link:hover, #manual-toc .toc-link.active { color: var(--manual-accent); border-left-color: var(--manual-accent); background: var(--manual-accent-light); }
    #manual-toc .toc-section { font-size: .7rem; font-weight: 700; letter-spacing: .08em; text-transform: uppercase; color: #94a3b8; padding: .6rem .75rem .2rem; margin-top: .25rem; }
    #manual-toc .toc-sub { padding-left: 1.5rem; font-size: .78rem; }
    .manual-section { scroll-margin-top: 5.5rem; }
    .step-list { counter-reset: step-counter; list-style: none; padding: 0; }
    .step-list li { counter-increment: step-counter; position: relative; padding: .75rem 1rem .75rem 3.5rem; margin-bottom: .5rem; background: #fff; border: 1px solid #e2e8f0; border-radius: .5rem; font-size: .9rem; }
    .step-list li::before { content: counter(step-counter); position: absolute; left: .75rem; top: 50%; transform: translateY(-50%); width: 2rem; height: 2rem; background: var(--manual-accent); color: #fff; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: .8rem; font-weight: 700; text-align: center; }
    .callout { border-left: 4px solid; border-radius: 0 .5rem .5rem 0; padding: .85rem 1.1rem; margin: 1rem 0; }
    .callout-tip { border-color: #059669; background: #ecfdf5; }
    .callout-warn { border-color: #f59e0b; background: #fffbeb; }
    .callout-info { border-color: #0ea5e9; background: #f0f9ff; }
    .callout .callout-title { font-weight: 700; font-size: .82rem; text-transform: uppercase; letter-spacing: .05em; margin-bottom: .3rem; }
    .callout-tip .callout-title { color: #059669; }
    .callout-info .callout-title { color: #0369a1; }
    .diagram-block { border: 1px solid #e2e8f0; border-radius: .5rem; padding: .75rem 1rem; margin: .25rem 0; background: #f8fafc; font-size: .9rem; }
    .diagram-block-title { font-weight: 600; font-size: .7rem; text-transform: uppercase; letter-spacing: .05em; color: #64748b; margin-bottom: .35rem; }
    .diagram-arrow { text-align: center; color: #94a3b8; padding: .25rem 0; font-size: 1rem; }
    @media print { #manual-toc { display: none !important; } }
</style>
@endpush

@section('content')
<div class="space-y-6">
    <div class="flex flex-wrap items-center justify-between gap-3">
        <div>
            <h2 class="text-xl font-bold text-slate-800">EDMS — User Manual</h2>
            <p class="text-slate-500 text-sm mt-0.5">Complete guide based on the actual application interface</p>
        </div>
        <button type="button" onclick="window.print()" class="inline-flex items-center gap-1.5 px-3 py-2 text-sm font-medium text-slate-700 bg-white border border-slate-300 rounded-lg hover:bg-slate-50">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
            Print / Save PDF
        </button>
    </div>

    <div class="flex flex-col lg:flex-row gap-6">
        {{-- Sticky TOC --}}
        <aside class="lg:w-64 flex-shrink-0 hidden lg:block">
            <div id="manual-toc" class="card border border-slate-200 overflow-hidden">
                <div class="p-3">
                    <div class="px-2 py-1 font-bold text-sm text-teal-700"><span class="text-slate-400">▸</span> Contents</div>
                    <div class="toc-section">Getting Started</div>
                    <a href="#sec-login" class="toc-link">1. Logging In</a>
                    <a href="#sec-dashboard" class="toc-link">2. Dashboard</a>
                    <div class="toc-section">Dashboards by Role</div>
                    <a href="#sec-officer" class="toc-link toc-sub">Officer / Clerk</a>
                    <a href="#sec-chief-officer" class="toc-link toc-sub">Chief Officer</a>
                    <a href="#sec-director" class="toc-link toc-sub">Director / Deputy Director / Dept PS</a>
                    <a href="#sec-ps" class="toc-link toc-sub">Principal Secretary</a>
                    <a href="#sec-minister" class="toc-link toc-sub">Minister / Deputy Minister</a>
                    <a href="#sec-records" class="toc-link toc-sub">Records Officer</a>
                    <a href="#sec-auditor" class="toc-link toc-sub">Auditor</a>
                    <a href="#sec-admin" class="toc-link toc-sub">System / Dept Admin</a>
                    <div class="toc-section">Modules</div>
                    <a href="#sec-drive" class="toc-link">3. File Manager (Drive)</a>
                    <a href="#sec-edms" class="toc-link">4. Official Documents</a>
                    <a href="#sec-document-show" class="toc-link toc-sub">Document detail page</a>
                    <a href="#sec-memos" class="toc-link">5. Memos</a>
                    <a href="#sec-memo-create" class="toc-link toc-sub">Create Memo</a>
                    <a href="#sec-approvals" class="toc-link">6. Pending Approvals</a>
                    <a href="#sec-search" class="toc-link">7. Search</a>
                    <a href="#sec-admin-module" class="toc-link">8. Administration</a>
                    <div class="toc-section">Reference</div>
                    <a href="#sec-hierarchy" class="toc-link">9. Roles & Hierarchy</a>
                    <a href="#sec-quick" class="toc-link">10. Quick Reference</a>
                </div>
            </div>
        </aside>

        {{-- Content --}}
        <div class="flex-1 min-w-0 space-y-6">
            {{-- Hero --}}
            <div class="card overflow-hidden bg-gradient-to-br from-slate-800 to-slate-900 text-white">
                <div class="p-6 flex flex-wrap items-center gap-6">
                    <div class="flex h-16 w-16 items-center justify-center rounded-xl bg-teal-500/20 text-teal-400">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    </div>
                    <div>
                        <h3 class="text-lg font-bold">Malawi Government EDMS</h3>
                        <p class="text-slate-300 text-sm mt-1">This manual describes the real UI. Use the Table of Contents to jump to any topic.</p>
                    </div>
                </div>
            </div>

            {{-- 1. Logging In --}}
            <div id="sec-login" class="card overflow-hidden manual-section">
                <div class="px-5 py-3 border-b border-slate-200 bg-white">
                    <h3 class="font-bold text-slate-800"><span class="inline-flex items-center justify-center w-6 h-6 rounded-full bg-teal-600 text-white text-xs font-bold mr-2">1</span> Logging In</h3>
                </div>
                <div class="p-5">
                    <p class="text-slate-600 text-sm mb-4">The login page uses a <strong>guest layout</strong>: centered on a light slate background. The EDMS logo (slate-900 box with amber icon) and app name appear at the top. A white card with border and shadow contains the form.</p>
                    <div class="bg-slate-50 rounded-xl p-6 border border-slate-200 mb-4">
                        <p class="text-xs font-semibold text-slate-500 uppercase mb-3">Layout (actual structure)</p>
                        <ul class="text-sm text-slate-700 space-y-2">
                            <li><strong>Logo area</strong> — flex items-center gap-2.5: icon box (h-12 w-12, rounded-xl, bg-slate-900, text-amber-400) + "EDMS" text (text-2xl font-bold)</li>
                            <li><strong>Form card</strong> — w-full sm:max-w-md, px-8 py-6, bg-white, rounded-2xl, border border-slate-200/80</li>
                            <li><strong>Fields</strong> — Email (x-text-input), Password (x-text-input), Remember me checkbox, Forgot password link (text-sm underline)</li>
                            <li><strong>Submit</strong> — x-primary-button "Log in" (teal)</li>
                        </ul>
                    </div>
                    <ol class="step-list">
                        <li>Go to <code class="text-xs bg-slate-100 px-1 rounded">/login</code></li>
                        <li>Enter Email and Password</li>
                        <li>Optionally tick Remember me</li>
                        <li>Click <strong>Log in</strong></li>
                    </ol>
                </div>
            </div>

            {{-- 2. Dashboard --}}
            <div id="sec-dashboard" class="card overflow-hidden manual-section">
                <div class="px-5 py-3 border-b border-slate-200 bg-white">
                    <h3 class="font-bold text-slate-800"><span class="inline-flex items-center justify-center w-6 h-6 rounded-full bg-teal-600 text-white text-xs font-bold mr-2">2</span> Dashboard</h3>
                </div>
                <div class="p-5">
                    <p class="text-slate-600 text-sm mb-4">After login you see <strong>Welcome back, {name}</strong> (text-xl font-semibold text-slate-800). The main content is a card with <strong>tabs</strong> in the header and role-based content below.</p>
                    <p class="text-sm text-slate-700 mb-2"><strong>Actual UI structure:</strong></p>
                    <ul class="text-sm text-slate-600 space-y-1 mb-4 list-disc pl-5">
                        <li>Tab bar: <code class="text-xs">flex border-b border-slate-200 bg-slate-50</code> — each tab is a button with <code class="text-xs">px-6 py-3 text-sm border-b-2</code>; active tab has <code class="text-xs">border-blue-600 text-blue-700 font-medium</code></li>
                        <li>Content area: <code class="text-xs">p-6</code> — shows one tab at a time via Alpine x-show</li>
                        <li>Profile card at bottom: header "Your profile" (page-title), then 4-col grid: Ministry, Department, Role, Email (dt/dd with text-xs uppercase for labels)</li>
                    </ul>
                    <p class="text-slate-600 text-sm">Session success messages appear as <code class="text-xs bg-emerald-50 px-1 rounded text-emerald-800">card card-body bg-emerald-50 border-emerald-200</code>.</p>
                </div>
            </div>

            {{-- Dashboards by Role --}}
            <div class="space-y-4">
                <h3 class="text-lg font-bold text-slate-800">Dashboards by Role</h3>

                <div id="sec-officer" class="card overflow-hidden manual-section">
                    <div class="px-5 py-3 border-b border-slate-200 bg-slate-50"><h4 class="font-bold text-slate-800">Officer / Clerk</h4></div>
                    <div class="p-5">
                        <p class="text-slate-600 text-sm mb-2">Tabs: <strong>My Files</strong>, <strong>Drafts</strong>, <strong>My Tasks</strong>.</p>
                        <ul class="text-sm text-slate-700 space-y-1 mb-2">
                            <li><strong>My Files</strong> — flex items-center gap-3 p-3 rounded-lg hover:bg-slate-50; file icon (w-8 h-8 text-slate-400), name, date. "View all files →" link.</li>
                            <li><strong>Drafts</strong> — same row layout; links to documents.show. "View all drafts →" or "No drafts" with link to documents.index.</li>
                            <li><strong>My Tasks</strong> — documents (amber clock icon) + Personal Memos (document icon). Each: flex gap-3 p-3 rounded-lg hover:bg-slate-50.</li>
                        </ul>
                    </div>
                </div>

                <div id="sec-chief-officer" class="card overflow-hidden manual-section">
                    <div class="px-5 py-3 border-b border-slate-200 bg-slate-50"><h4 class="font-bold text-slate-800">Chief Officer</h4></div>
                    <div class="p-5">
                        <p class="text-slate-600 text-sm">Tabs: <strong>Section Files</strong>, <strong>Pending Approvals</strong>. Section Files: "View files in your scope via Drive or EDMS" + <code class="text-xs">btn-primary</code> "Go to Drive" (bg-blue-600) and <code class="text-xs">btn-secondary</code> "EDMS". Pending Approvals: list of workflow steps (amber icon) with doc title, step name, document type; "View approval queue →".</p>
                    </div>
                </div>

                <div id="sec-director" class="card overflow-hidden manual-section">
                    <div class="px-5 py-3 border-b border-slate-200 bg-slate-50"><h4 class="font-bold text-slate-800">Director / Deputy Director / Department PS</h4></div>
                    <div class="p-5">
                        <p class="text-slate-600 text-sm">Tabs: <strong>Department Files</strong>, <strong>Approval Queue</strong>. Same pattern as Chief Officer (Drive/EDMS links, approval list).</p>
                    </div>
                </div>

                <div id="sec-ps" class="card overflow-hidden manual-section">
                    <div class="px-5 py-3 border-b border-slate-200 bg-slate-50"><h4 class="font-bold text-slate-800">Principal Secretary (Ministry)</h4></div>
                    <div class="p-5">
                        <p class="text-slate-600 text-sm">Tabs: <strong>Ministry Overview</strong>, <strong>Dept Approvals</strong>. Same Drive/EDMS + approvals pattern.</p>
                    </div>
                </div>

                <div id="sec-minister" class="card overflow-hidden manual-section">
                    <div class="px-5 py-3 border-b border-slate-200 bg-slate-50"><h4 class="font-bold text-slate-800">Minister / Deputy Minister</h4></div>
                    <div class="p-5">
                        <p class="text-slate-600 text-sm">Tabs: <strong>Cross-Ministry Docs</strong>, <strong>Final Approvals</strong>.</p>
                    </div>
                </div>

                <div id="sec-records" class="card overflow-hidden manual-section">
                    <div class="px-5 py-3 border-b border-slate-200 bg-slate-50"><h4 class="font-bold text-slate-800">Records Officer</h4></div>
                    <div class="p-5">
                        <p class="text-slate-600 text-sm">Tabs: <strong>Retention</strong>, <strong>Legal Hold</strong>, <strong>Archiving</strong>. Retention: link to admin.retention-rules.index. Legal Hold/Archived: lists with badge-red "Hold" or archive icon.</p>
                    </div>
                </div>

                <div id="sec-auditor" class="card overflow-hidden manual-section">
                    <div class="px-5 py-3 border-b border-slate-200 bg-slate-50"><h4 class="font-bold text-slate-800">Auditor</h4></div>
                    <div class="p-5">
                        <p class="text-slate-600 text-sm">Tabs: <strong>Audit Logs</strong>, <strong>Approvals</strong>. Link to admin.audit-logs.index.</p>
                    </div>
                </div>

                <div id="sec-admin" class="card overflow-hidden manual-section">
                    <div class="px-5 py-3 border-b border-slate-200 bg-slate-50"><h4 class="font-bold text-slate-800">System Administrator / Department Administrator</h4></div>
                    <div class="p-5">
                        <p class="text-slate-600 text-sm mb-2"><strong>System Admin</strong> — stat cards (Ministries, Departments, Active Users, Active Documents), Recent Admin Activity, Document Types, User Administration, Retention Rules, quick links. Cards use <code class="text-xs">card p-6 hover:shadow-lg</code>.</p>
                        <p class="text-slate-600 text-sm"><strong>Dept Admin</strong> — "Department Admin Dashboard" title, grid of 4 cards: Sections (amber), Active Users (emerald), Pending Approvals (amber), Active Documents (sky).</p>
                    </div>
                </div>
            </div>

            {{-- 3. File Manager (Drive) --}}
            <div id="sec-drive" class="card overflow-hidden manual-section">
                <div class="px-5 py-3 border-b border-slate-200 bg-white">
                    <h3 class="font-bold text-slate-800"><span class="inline-flex items-center justify-center w-6 h-6 rounded-full bg-teal-600 text-white text-xs font-bold mr-2">3</span> File Manager (Drive)</h3>
                </div>
                <div class="p-5 space-y-4">
                    <p class="text-slate-600 text-sm">Header: <strong>File Manager — {Space name}</strong> or "Shared with you", "Trash", etc. depending on view.</p>

                    <p class="text-sm font-semibold text-slate-800">Space tabs (top)</p>
                    <p class="text-slate-600 text-sm">Horizontal tabs: <code class="text-xs">px-4 py-2.5 rounded-lg text-sm font-medium</code>. Active: <code class="text-xs">bg-indigo-700 text-white shadow-sm</code>. Inactive: <code class="text-xs">bg-white border border-slate-200 text-slate-600</code>. Links to files.index with space param.</p>

                    <p class="text-sm font-semibold text-slate-800">Nextcloud-style layout (default)</p>
                    <p class="text-slate-600 text-sm mb-2">Main area: <code class="text-xs">nc-drive</code> — white, rounded-xl, border. Two columns: collapsible sidebar (nc-sidebar) + main content.</p>
                    <ul class="text-sm text-slate-700 space-y-1 mb-2">
                        <li><strong>Sidebar</strong> — w-52 or w-14 when collapsed; "Navigate" header; nav items: My Files, Shared Space, Shared by me, Shared with you, Trash, Locked, Favorites. Active: <code class="text-xs">bg-blue-100 text-blue-700</code>. Collapse button (chevron).</li>
                        <li><strong>Toolbar</strong> — breadcrumbs (text-sm, / separators), search (scope=files), details toggle, view switcher (list/grid, blue-50 when active), <strong>New</strong> button (bg-blue-600). New dropdown: New folder (expandable form), File upload.</li>
                        <li><strong>Bulk toolbar</strong> — appears when items selected: <code class="text-xs">nc-bulk-toolbar bg-blue-50 border-b border-blue-100</code>. "X selected", Copy, Cut, Paste, Delete, Move (dropdown), Download (single file), Clear.</li>
                        <li><strong>File list</strong> — table: checkbox, Name (folder icon / file icon + star for favorite, version), Size, Modified. Row: <code class="text-xs">table-row</code>. Actions: Download, Promote, New version, Delete (or Restore/Delete permanently in Trash).</li>
                        <li><strong>Context menu</strong> — right-click: Open, Rename, Cut, Paste, Share, Lock/Unlock, Delete (folders); Download, Rename, Copy, Cut, Favorites, Share, Lock/Unlock, Promote to document, Delete (files).</li>
                        <li><strong>Rename modal</strong> — fixed overlay, centered card, "Rename" heading, input, Rename + Cancel buttons.</li>
                        <li><strong>Share modal</strong> — "Share with" user search, Permission (view/edit), "Anyone with the link" + Copy link button.</li>
                        <li><strong>Preview panel</strong> — fixed right, slate-800 header (gov-drive-preview-header), file name, favorite, Open, Download, Lock/Unlock, close. Preview area (iframe for PDF/office, img for images). Right sidebar: metadata, versions, tags, Promote.</li>
                    </ul>

                    <p class="text-sm font-semibold text-slate-800">Hub view (Department / Ministry space)</p>
                    <p class="text-slate-600 text-sm">When space is department or ministry: "Sections in {Dept}" or "Departments in {Ministry}" with cards. Each section links to its storage space. Department hub: sections grid. Ministry hub: departments as grouped sections.</p>

                    <p class="text-sm font-semibold text-slate-800">Actions</p>
                    <p class="text-slate-600 text-sm">Drag-and-drop to move files/folders. Copy/Cut/Paste via toolbar or context menu. Promote links to documents/promote form.</p>
                </div>
            </div>

            {{-- 4. Official Documents --}}
            <div id="sec-edms" class="card overflow-hidden manual-section">
                <div class="px-5 py-3 border-b border-slate-200 bg-white">
                    <h3 class="font-bold text-slate-800"><span class="inline-flex items-center justify-center w-6 h-6 rounded-full bg-teal-600 text-white text-xs font-bold mr-2">4</span> Official Documents</h3>
                </div>
                <div class="p-5 space-y-4">
                    <p class="text-slate-600 text-sm">Header: <strong>Official Documents</strong>. Filter row: <code class="text-xs">flex flex-wrap gap-3</code> with selects (status, document_type_id, department_id, sensitivity_level_id) — <code class="text-xs">input-field w-auto min-w-[140px]</code>, onchange submits form.</p>
                    <p class="text-slate-600 text-sm">Table: <code class="text-xs">card overflow-hidden</code>, <code class="text-xs">table-header</code> (bg-slate-50), <code class="text-xs">table-row</code>. Columns: Title (link to show), Type, Status (badge-gray/yellow/green), Owner, Sensitivity, View link.</p>
                    <p class="text-slate-600 text-sm">Empty: "No documents. Promote a file from the File Manager."</p>
                </div>
            </div>

            {{-- 4b Document detail page --}}
            <div id="sec-document-show" class="card overflow-hidden manual-section">
                <div class="px-5 py-3 border-b border-slate-200 bg-slate-50"><h4 class="font-bold text-slate-800">Document detail page</h4></div>
                <div class="p-5 space-y-4">
                    <p class="text-slate-600 text-sm">Header: document title + status badge. <strong>3-panel grid</strong> (grid-cols-3 gap-4):</p>
                    <ul class="text-sm text-slate-700 space-y-1">
                        <li><strong>Document Metadata</strong> — card, header "Document Metadata" (bg-slate-50), dl: Title, Document Type, Ministry, Department, Owner, Sensitivity, Version, Legal Hold badge if set, Checked out by if applicable.</li>
                        <li><strong>Workflow & Approval</strong> — step list: green circle+check (approved), blue circle (current step you can approve), grey circle (pending). Per step: name, role. If you can approve: Comment input + Approve (emerald) + Reject (red) buttons.</li>
                        <li><strong>Audit Trail</strong> — list of events: date, action label, "by {user}".</li>
                    </ul>
                    <p class="text-slate-600 text-sm">Version History section. Action buttons: Download, Check Out / Check In, Submit for Review, Promote to Approved, Apply/Remove Legal Hold, Archive, View in File Manager.</p>
                </div>
            </div>

            {{-- 5. Memos --}}
            <div id="sec-memos" class="card overflow-hidden manual-section">
                <div class="px-5 py-3 border-b border-slate-200 bg-white">
                    <h3 class="font-bold text-slate-800"><span class="inline-flex items-center justify-center w-6 h-6 rounded-full bg-teal-600 text-white text-xs font-bold mr-2">5</span> Memos</h3>
                </div>
                <div class="p-5 space-y-4">
                    <p class="text-slate-600 text-sm">Header: <strong>Memos</strong>. Top row: <code class="text-xs">btn-primary</code> "+ New Memo", then filter buttons: Upward, Downward, Personal — <code class="text-xs">px-4 py-2 rounded-lg text-sm font-medium</code>; active <code class="text-xs">bg-blue-600 text-white</code>, inactive <code class="text-xs">bg-slate-100 text-slate-600</code>.</p>
                    <p class="text-slate-600 text-sm">Table: Title (link), Direction (badge-gray), To, Status (badge-blue/green/gray for sent/acknowledged/draft), Date, View link.</p>
                </div>
            </div>

            {{-- 5b Create Memo --}}
            <div id="sec-memo-create" class="card overflow-hidden manual-section">
                <div class="px-5 py-3 border-b border-slate-200 bg-slate-50"><h4 class="font-bold text-slate-800">Create Memo</h4></div>
                <div class="p-5">
                    <p class="text-slate-600 text-sm">Tabs: Send Memo Upward, Send Memo Downward, Personal Memo, Recent Memos (link). Form: Title (input-field), To (select, hidden for personal), Subject/Body (textarea), "Require approval workflow" checkbox. Create Memo + Cancel buttons. Right column: "My Memos" list (recent 10).</p>
                </div>
            </div>

            {{-- 6. Pending Approvals --}}
            <div id="sec-approvals" class="card overflow-hidden manual-section">
                <div class="px-5 py-3 border-b border-slate-200 bg-white">
                    <h3 class="font-bold text-slate-800"><span class="inline-flex items-center justify-center w-6 h-6 rounded-full bg-teal-600 text-white text-xs font-bold mr-2">6</span> Pending Approvals</h3>
                </div>
                <div class="p-5">
                    <p class="text-slate-600 text-sm">Header: <strong>Approval Dashboard</strong>. Intro text. Table: Document (link), Type, Owner, Step (name + role), Review button (btn-primary text-sm py-1.5). Empty: "No pending approvals."</p>
                </div>
            </div>

            {{-- 7. Search --}}
            <div id="sec-search" class="card overflow-hidden manual-section">
                <div class="px-5 py-3 border-b border-slate-200 bg-white">
                    <h3 class="font-bold text-slate-800"><span class="inline-flex items-center justify-center w-6 h-6 rounded-full bg-teal-600 text-white text-xs font-bold mr-2">7</span> Search</h3>
                </div>
                <div class="p-5">
                    <p class="text-slate-600 text-sm">Header: <strong>Search</strong>. Card with form: text input (name=q, placeholder "Search files and documents..."), Search button. Filter row: type (All/Files/Documents), status (Draft/Approved), document_type_id, year. Results: list of links — title, type + space + status + date. Pagination below.</p>
                </div>
            </div>

            {{-- 8. Administration --}}
            <div id="sec-admin-module" class="card overflow-hidden manual-section">
                <div class="px-5 py-3 border-b border-slate-200 bg-white">
                    <h3 class="font-bold text-slate-800"><span class="inline-flex items-center justify-center w-6 h-6 rounded-full bg-teal-600 text-white text-xs font-bold mr-2">8</span> Administration</h3>
                </div>
                <div class="p-5">
                    <p class="text-slate-600 text-sm mb-2">Sidebar sections: System Admin sees Admin, Roles, Document Types, Sensitivity Levels, Workflows, Retention Rules, Audit Logs. Dept Admin sees Department Dashboard, Users, Sections, Roles, Workflow Settings, Reports, Audit Logs, Settings.</p>
                    <p class="text-slate-600 text-sm">Standard admin pages: card layout, table-header/table-row tables, btn-primary for actions, input-field for forms.</p>
                </div>
            </div>

            {{-- 9. Roles & Hierarchy --}}
            <div id="sec-hierarchy" class="card overflow-hidden manual-section">
                <div class="px-5 py-3 border-b border-slate-200 bg-white">
                    <h3 class="font-bold text-slate-800"><span class="inline-flex items-center justify-center w-6 h-6 rounded-full bg-teal-600 text-white text-xs font-bold mr-2">9</span> Roles & Hierarchy</h3>
                </div>
                <div class="p-5">
                    <div class="space-y-2">
                        <div class="diagram-block"><div class="diagram-block-title">Ministry level</div>Minister / Deputy Minister, Principal Secretary → Final approvals, ministry overview</div>
                        <div class="diagram-arrow">↓</div>
                        <div class="diagram-block"><div class="diagram-block-title">Department level</div>Director / Deputy Director / Principal Secretary → Department approval queue</div>
                        <div class="diagram-arrow">↓</div>
                        <div class="diagram-block"><div class="diagram-block-title">Unit / section level</div>Chief Officer → Section files, pending approvals</div>
                        <div class="diagram-arrow">↓</div>
                        <div class="diagram-block"><div class="diagram-block-title">Daily operations</div>Officer / Clerk → My Files, Drafts, Tasks</div>
                        <div class="grid grid-cols-2 gap-2 mt-2">
                            <div class="diagram-block"><div class="diagram-block-title">Records Officer</div>Retention, Legal Hold, Archiving</div>
                            <div class="diagram-block"><div class="diagram-block-title">Auditor</div>Read-only audit logs</div>
                        </div>
                    </div>
                    <p class="text-slate-600 text-sm mt-4">Workflow: Chief Officer → Director → Principal Secretary → Minister.</p>
                </div>
            </div>

            {{-- 10. Quick Reference --}}
            <div id="sec-quick" class="card overflow-hidden manual-section">
                <div class="px-5 py-3 border-b border-slate-200 bg-white">
                    <h3 class="font-bold text-slate-800"><span class="inline-flex items-center justify-center w-6 h-6 rounded-full bg-teal-600 text-white text-xs font-bold mr-2">10</span> Quick Reference</h3>
                </div>
                <div class="p-5">
                    <div class="grid md:grid-cols-2 gap-6">
                        <div>
                            <h4 class="font-semibold text-slate-800 mb-2">Key URLs</h4>
                            <table class="w-full text-sm">
                                <tr><td class="py-1.5 text-slate-600">Dashboard</td><td class="py-1.5"><code class="text-xs bg-slate-100 px-1 rounded">/dashboard</code></td></tr>
                                <tr><td class="py-1.5 text-slate-600">File Manager</td><td class="py-1.5"><code class="text-xs bg-slate-100 px-1 rounded">/files</code></td></tr>
                                <tr><td class="py-1.5 text-slate-600">Documents</td><td class="py-1.5"><code class="text-xs bg-slate-100 px-1 rounded">/documents</code></td></tr>
                                <tr><td class="py-1.5 text-slate-600">Memos</td><td class="py-1.5"><code class="text-xs bg-slate-100 px-1 rounded">/memos</code></td></tr>
                                <tr><td class="py-1.5 text-slate-600">Approvals</td><td class="py-1.5"><code class="text-xs bg-slate-100 px-1 rounded">/approvals</code></td></tr>
                                <tr><td class="py-1.5 text-slate-600">Search</td><td class="py-1.5"><code class="text-xs bg-slate-100 px-1 rounded">/search</code></td></tr>
                                <tr><td class="py-1.5 text-slate-600">User Manual</td><td class="py-1.5"><code class="text-xs bg-slate-100 px-1 rounded">/manual</code></td></tr>
                            </table>
                        </div>
                        <div>
                            <h4 class="font-semibold text-slate-800 mb-2">UI components (Tailwind)</h4>
                            <p class="text-slate-600 text-xs mb-2">card, card-body, table-header, table-row, btn-primary (teal), btn-secondary, input-field, badge badge-gray/yellow/green/red/blue</p>
                        </div>
                    </div>
                    <div class="mt-6 pt-6 border-t border-slate-200 text-center text-slate-500 text-sm">
                        EDMS User Manual · Based on actual application code · {{ now()->format('F Y') }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
(function(){
    const links = document.querySelectorAll('#manual-toc .toc-link');
    const sections = Array.from(links).map(l => document.querySelector(l.getAttribute('href'))).filter(Boolean);
    const observer = new IntersectionObserver(entries => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                links.forEach(l => l.classList.remove('active'));
                const active = document.querySelector('#manual-toc a[href="#' + entry.target.id + '"]');
                if (active) active.classList.add('active');
            }
        });
    }, { rootMargin: '-15% 0px -70% 0px' });
    sections.forEach(s => observer.observe(s));
})();
</script>
@endpush
