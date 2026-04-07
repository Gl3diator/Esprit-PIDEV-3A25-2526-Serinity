import { Controller } from '@hotwired/stimulus';

export default class extends Controller {
    static targets = [
        'form',
        'submitButton',
        'cancelEditButton',
        'formHeading',
        'formHelper',
        'moodLevelValue',
        'weeklyCount',
        'weeklyAverage',
        'mostUsedType',
        'summaryRange',
        'topEmotion',
        'topInfluence',
        'historyList',
        'historyEmpty',
        'historyMeta',
        'pageLabel',
        'prevPageButton',
        'nextPageButton',
        'filterSearch',
        'filterMomentType',
        'filterFromDate',
    ];

    static values = {
        createUrl: String,
        updateUrlTemplate: String,
        deleteUrlTemplate: String,
        historyUrl: String,
        summaryUrl: String,
    };

    connect() {
        this.page = 1;
        this.limit = 20;
        this.loadingHistory = false;
        this.editingEntryId = null;

        this.ensureHistoryCardStyles();
        this.loadSummary();
        this.loadHistory();
    }

    updateMoodLevelLabel(event) {
        if (!this.hasMoodLevelValueTarget) {
            return;
        }

        this.moodLevelValueTarget.textContent = `${event.currentTarget.value}`;
    }

    async submitEntry(event) {
        event.preventDefault();

        const payload = {
            momentType: this.formTarget.elements.momentType.value,
            moodLevel: Number(this.formTarget.elements.moodLevel.value),
            emotionKeys: this.selectedValues('emotionKeys'),
            influenceKeys: this.selectedValues('influenceKeys'),
        };

        const isEditing = this.editingEntryId !== null;
        const url = isEditing ? this.buildEntryUrl(this.updateUrlTemplateValue, this.editingEntryId) : this.createUrlValue;
        const method = isEditing ? 'PUT' : 'POST';

        this.setSubmitState(true);

        try {
            const response = await fetch(url, {
                method,
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(payload),
            });
            const body = await this.readJson(response);

            if (!response.ok || !body?.success) {
                throw new Error(body?.message || (isEditing ? 'Failed to update mood entry.' : 'Failed to create mood entry.'));
            }

            this.showToast(body.message || (isEditing ? 'Mood entry updated successfully.' : 'Mood entry created successfully.'), 'success');
            this.resetFormState();
            this.page = 1;
            await Promise.all([this.loadSummary(), this.loadHistory()]);
        } catch (error) {
            this.showToast(error.message || (isEditing ? 'Failed to update mood entry.' : 'Failed to create mood entry.'), 'error');
        } finally {
            this.setSubmitState(false);
        }
    }

    applyFilters(event) {
        event.preventDefault();
        this.page = 1;
        this.loadHistory();
    }

    prevPage() {
        if (this.page <= 1 || this.loadingHistory) {
            return;
        }

        this.page -= 1;
        this.loadHistory();
    }

    nextPage() {
        if (this.loadingHistory) {
            return;
        }

        this.page += 1;
        this.loadHistory();
    }

    startEdit(entry) {
        this.editingEntryId = String(entry.id);
        this.formTarget.elements.momentType.value = entry.momentType || 'MOMENT';
        this.formTarget.elements.moodLevel.value = String(entry.moodLevel ?? 3);
        this.moodLevelValueTarget.textContent = String(entry.moodLevel ?? 3);

        this.toggleCheckboxGroup('emotionKeys', entry.emotions || []);
        this.toggleCheckboxGroup('influenceKeys', entry.influences || []);

        this.formHeadingTarget.textContent = 'Edit mood entry';
        this.formHelperTarget.textContent = `Editing entry from ${this.formatEntryDate(entry.entryDate)}`;
        this.cancelEditButtonTarget.hidden = false;
        this.submitButtonTarget.textContent = 'Update entry';

        this.formTarget.scrollIntoView({ behavior: 'smooth', block: 'start' });
    }

    cancelEdit() {
        this.resetFormState();
    }

    async deleteEntryById(entryId) {
        if (!window.confirm('Delete this mood entry?')) {
            return;
        }

        if (this.editingEntryId === String(entryId)) {
            this.resetFormState();
        }

        try {
            const response = await fetch(this.buildEntryUrl(this.deleteUrlTemplateValue, entryId), {
                method: 'DELETE',
            });
            const body = await this.readJson(response);

            if (!response.ok || !body?.success) {
                throw new Error(body?.message || 'Failed to delete mood entry.');
            }

            this.showToast(body.message || 'Mood entry deleted successfully.', 'success');

            if (this.page > 1 && this.historyListTarget.childElementCount <= 1) {
                this.page -= 1;
            }

            await Promise.all([this.loadSummary(), this.loadHistory()]);
        } catch (error) {
            this.showToast(error.message || 'Failed to delete mood entry.', 'error');
        }
    }

    async loadSummary() {
        try {
            const response = await fetch(`${this.summaryUrlValue}?days=7`);
            const body = await this.readJson(response);

            if (!response.ok || !body?.success || !body.data) {
                throw new Error(body?.message || 'Failed to load mood summary.');
            }

            this.renderSummary(body.data);
        } catch (error) {
            this.showToast(error.message || 'Failed to load mood summary.', 'error');
        }
    }

    async loadHistory() {
        this.loadingHistory = true;
        this.historyMetaTarget.textContent = 'Loading history...';
        this.prevPageButtonTarget.disabled = true;
        this.nextPageButtonTarget.disabled = true;

        const query = new URLSearchParams({
            page: String(this.page),
            limit: String(this.limit),
        });

        const search = this.filterSearchTarget.value.trim();
        if (search !== '') {
            query.set('search', search);
        }

        const momentType = this.filterMomentTypeTarget.value;
        if (momentType !== '') {
            query.set('momentType', momentType);
        }

        const fromDate = this.filterFromDateTarget.value;
        if (fromDate !== '') {
            query.set('fromDate', fromDate);
        }

        try {
            const response = await fetch(`${this.historyUrlValue}?${query.toString()}`);
            const body = await this.readJson(response);

            if (!response.ok || !body?.success || !body.data) {
                throw new Error(body?.message || 'Failed to load mood history.');
            }

            this.renderHistory(body.data);
        } catch (error) {
            if (this.page > 1) {
                this.page -= 1;
            }

            this.historyMetaTarget.textContent = 'Failed to load history.';
            this.showToast(error.message || 'Failed to load mood history.', 'error');
        } finally {
            this.loadingHistory = false;
        }
    }

    renderSummary(data) {
        this.weeklyCountTarget.textContent = String(data.weeklyCount ?? 0);
        this.weeklyAverageTarget.textContent = data.weeklyAverageMood ?? '--';
        this.mostUsedTypeTarget.textContent = data.mostUsedType ?? 'No data';
        this.summaryRangeTarget.textContent = `${data.fromDate ?? '--'} → ${data.toDate ?? '--'}`;
        this.topEmotionTarget.textContent = this.formatTop(data.topEmotion);
        this.topInfluenceTarget.textContent = this.formatTop(data.topInfluence);
    }

    renderHistory(data) {
        const groups = Object.values(data.groups ?? {});
        const pagination = data.pagination ?? { page: 1, totalPages: 1, total: 0 };

        this.page = Number(pagination.page || 1);
        this.pageLabelTarget.textContent = `Page ${pagination.page || 1}`;
        this.historyMetaTarget.textContent = `${pagination.total || 0} entries`;

        this.prevPageButtonTarget.disabled = this.page <= 1;
        this.nextPageButtonTarget.disabled = this.page >= Number(pagination.totalPages || 1);

        this.historyListTarget.replaceChildren();
        this.historyEmptyTarget.hidden = groups.length > 0;

        if (groups.length === 0) {
            return;
        }

        groups.forEach((group) => {
            const groupSection = document.createElement('article');
            groupSection.className = 'ac-mood-history-group ac-mood-history-group--refined';

            const groupTitle = document.createElement('h4');
            groupTitle.className = 'ac-mood-history-group-title';
            groupTitle.textContent = group.label || 'Unknown';
            groupSection.appendChild(groupTitle);

            (group.entries || []).forEach((entry) => {
                const card = document.createElement('article');
                card.className = 'ac-mood-entry-row ac-mood-entry-card ac-mood-entry-card--refined';

                const rowHead = document.createElement('div');
                rowHead.className = 'ac-row-between ac-mood-entry-header ac-mood-entry-header--refined';

                const rowLeft = document.createElement('div');
                rowLeft.className = 'ac-mood-entry-left ac-mood-entry-left--refined';

                const typeBadge = document.createElement('span');
                typeBadge.className = `ac-badge ac-badge-${entry.momentType === 'DAY' ? 'primary' : 'secondary'} ac-mood-type-badge ac-mood-type-badge--${entry.momentType === 'DAY' ? 'day' : 'moment'}`;
                typeBadge.textContent = entry.momentType || 'MOMENT';

                const timeMeta = document.createElement('p');
                timeMeta.className = 'ac-muted ac-mood-entry-time';
                timeMeta.textContent = this.formatEntryDate(entry.entryDate);

                rowLeft.appendChild(typeBadge);
                rowLeft.appendChild(timeMeta);

                const rowRight = document.createElement('div');
                rowRight.className = 'ac-row-end ac-mood-entry-right ac-mood-entry-right--refined';

                const levelContainer = document.createElement('div');
                levelContainer.className = 'ac-mood-level-pill ac-mood-level-pill--refined';

                const level = document.createElement('strong');
                level.className = 'ac-mood-level-text';
                level.textContent = `Level ${entry.moodLevel ?? '-'}/5`;

                const levelTrack = document.createElement('div');
                levelTrack.className = 'ac-mood-level-track';

                const levelFill = document.createElement('span');
                levelFill.className = 'ac-mood-level-fill';
                levelFill.style.width = `${Math.max(0, Math.min(5, Number(entry.moodLevel) || 0)) * 20}%`;
                levelTrack.appendChild(levelFill);
                levelContainer.appendChild(level);
                levelContainer.appendChild(levelTrack);

                const actions = document.createElement('div');
                actions.className = 'ac-mood-entry-actions';

                const editButton = document.createElement('button');
                editButton.type = 'button';
                editButton.className = 'ac-ghost-btn ac-mood-edit-btn ac-mood-action-btn';
                editButton.textContent = 'Edit';
                editButton.addEventListener('click', () => this.startEdit(entry));

                const deleteButton = document.createElement('button');
                deleteButton.type = 'button';
                deleteButton.className = 'ac-ghost-btn ac-mood-delete-btn ac-mood-action-btn';
                deleteButton.textContent = 'Delete';
                deleteButton.addEventListener('click', () => this.deleteEntryById(entry.id));

                actions.appendChild(editButton);
                actions.appendChild(deleteButton);
                rowRight.appendChild(levelContainer);
                rowRight.appendChild(actions);

                rowHead.appendChild(rowLeft);
                rowHead.appendChild(rowRight);

                const emotionsSection = this.buildTagList('Emotions', entry.emotions || []);
                const influencesSection = this.buildTagList('Influences', entry.influences || []);

                [emotionsSection, influencesSection].forEach((section) => {
                    section.classList.add('ac-mood-tag-row--refined');

                    const sectionLabel = section.querySelector('.ac-mood-tag-label');
                    if (sectionLabel) {
                        sectionLabel.classList.add('ac-mood-tag-label--refined');
                    }

                    const tags = section.querySelector('.ac-mood-tags');
                    if (tags) {
                        tags.classList.add('ac-mood-tags--refined');
                    }

                    section.querySelectorAll('.ac-badge').forEach((tag) => {
                        tag.classList.add('ac-mood-tag-pill');
                    });
                });

                card.appendChild(rowHead);
                card.appendChild(emotionsSection);
                card.appendChild(influencesSection);

                groupSection.appendChild(card);
            });

            this.historyListTarget.appendChild(groupSection);
        });
    }

    ensureHistoryCardStyles() {
        if (document.getElementById('ac-mood-history-refined-styles')) {
            return;
        }

        const style = document.createElement('style');
        style.id = 'ac-mood-history-refined-styles';
        style.textContent = `
            .ac-mood-history-group--refined {
                background: #E6F2F1;
                border-radius: 14px;
                padding: 12px;
                margin-bottom: 14px;
            }
            .ac-mood-history-group-title {
                margin: 0 0 10px 0;
                font-size: 1rem;
                font-weight: 700;
                color: #2F6F6D;
            }
            .ac-mood-entry-card--refined {
                background: #fff;
                border: 1px solid rgba(47, 111, 109, 0.12);
                border-radius: 14px;
                padding: 14px 16px;
                margin-bottom: 10px;
                box-shadow: 0 8px 18px rgba(47, 111, 109, 0.08);
                transition: transform 0.2s ease, box-shadow 0.2s ease;
            }
            .ac-mood-entry-card--refined:hover {
                transform: translateY(-2px);
                box-shadow: 0 14px 28px rgba(47, 111, 109, 0.14);
            }
            .ac-mood-entry-header--refined {
                display: flex;
                justify-content: space-between;
                align-items: center;
                gap: 10px;
                margin-bottom: 8px;
            }
            .ac-mood-entry-left--refined {
                display: flex;
                flex-direction: column;
                gap: 4px;
            }
            .ac-mood-type-badge {
                align-self: flex-start;
                padding: 3px 9px;
                border-radius: 999px;
                font-size: 0.7rem;
                font-weight: 700;
                letter-spacing: 0.02em;
                color: #fff;
            }
            .ac-mood-type-badge--day {
                background: #2F6F6D;
            }
            .ac-mood-type-badge--moment {
                background: #88BDBC;
            }
            .ac-mood-entry-time {
                margin: 0;
                font-size: 0.8rem;
                color: rgba(47, 111, 109, 0.72);
            }
            .ac-mood-entry-right--refined {
                display: flex;
                align-items: center;
                gap: 8px;
                min-width: 208px;
            }
            .ac-mood-level-pill--refined {
                width: 112px;
                display: flex;
                flex-direction: column;
                gap: 4px;
            }
            .ac-mood-level-text {
                font-size: 0.78rem;
                font-weight: 700;
                color: #2F6F6D;
                line-height: 1.1;
            }
            .ac-mood-level-track {
                width: 100%;
                height: 7px;
                border-radius: 999px;
                background: #E6F2F1;
                overflow: hidden;
            }
            .ac-mood-level-fill {
                display: block;
                height: 100%;
                background: #88BDBC;
                border-radius: 999px;
            }
            .ac-mood-entry-actions {
                display: flex;
                align-items: center;
                gap: 6px;
            }
            .ac-mood-action-btn {
                padding: 4px 8px;
                border-radius: 9px;
                font-weight: 600;
                font-size: 0.78rem;
                cursor: pointer;
                transition: all 0.2s ease;
            }
            .ac-mood-edit-btn {
                border: 1px solid #2F6F6D;
                background: #fff;
                color: #2F6F6D;
            }
            .ac-mood-edit-btn:hover {
                background: #E6F2F1;
            }
            .ac-mood-delete-btn {
                border: 1px solid #F3C9C9;
                background: #FFF8F8;
                color: #A85B5B;
            }
            .ac-mood-delete-btn:hover {
                background: #FDEDED;
                border-color: #E9B8B8;
            }
            .ac-mood-tag-row--refined {
                display: flex;
                align-items: center;
                flex-wrap: wrap;
                gap: 6px;
                margin-top: 7px;
            }
            .ac-mood-tag-label--refined {
                font-size: 0.74rem;
                font-weight: 700;
                color: rgba(47, 111, 109, 0.75);
                flex: 0 0 auto;
            }
            .ac-mood-tags--refined {
                display: inline-flex;
                flex-wrap: wrap;
                gap: 6px;
                align-items: center;
            }
            .ac-mood-tag-pill {
                padding: 4px 8px;
                border-radius: 999px;
                background: #E6F2F1;
                color: #2F6F6D;
                border: 1px solid rgba(47, 111, 109, 0.12);
                font-size: 0.72rem;
                font-weight: 600;
                line-height: 1.1;
                transition: background 0.2s ease;
            }
            .ac-mood-tag-pill:hover {
                background: #88BDBC;
            }
        `;

        document.head.appendChild(style);
    }

    buildTagList(label, items) {
        const wrapper = document.createElement('div');
        wrapper.className = 'ac-mood-tag-row';

        const title = document.createElement('span');
        title.className = 'ac-mood-tag-label';
        title.textContent = `${label}:`;
        wrapper.appendChild(title);

        const tags = document.createElement('div');
        tags.className = 'ac-mood-tags';

        items.forEach((item) => {
            const tag = document.createElement('span');
            tag.className = 'ac-badge ac-badge-secondary ac-mood-tag-pill';
            tag.textContent = item.label || item.key || '';
            tags.appendChild(tag);
        });

        wrapper.appendChild(tags);

        return wrapper;
    }

    toggleCheckboxGroup(name, items) {
        const selectedKeys = new Set((items || []).map((item) => item.key));

        this.formTarget.querySelectorAll(`input[name="${name}"]`).forEach((field) => {
            field.checked = selectedKeys.has(field.value);
        });
    }

    selectedValues(name) {
        return Array.from(this.formTarget.querySelectorAll(`input[name="${name}"]:checked`))
            .map((field) => field.value);
    }

    setSubmitState(isLoading) {
        this.submitButtonTarget.disabled = isLoading;
        this.submitButtonTarget.textContent = isLoading
            ? (this.editingEntryId ? 'Updating...' : 'Saving...')
            : (this.editingEntryId ? 'Update entry' : 'Save entry');

        this.cancelEditButtonTarget.disabled = isLoading;
    }

    resetFormState() {
        this.editingEntryId = null;
        this.formTarget.reset();
        this.formTarget.elements.moodLevel.value = '3';
        this.moodLevelValueTarget.textContent = '3';
        this.formHeadingTarget.textContent = 'New mood entry';
        this.formHelperTarget.textContent = '1 = very low, 5 = very high';
        this.submitButtonTarget.textContent = 'Save entry';
        this.cancelEditButtonTarget.hidden = true;
        this.cancelEditButtonTarget.disabled = false;
    }

    formatTop(item) {
        if (!item || !item.label || item.label === 'No data') {
            return 'No data';
        }

        return `${item.label} (${item.usageCount ?? 0})`;
    }

    formatEntryDate(value) {
        if (!value) {
            return '';
        }

        const date = new Date(value.replace(' ', 'T'));
        if (Number.isNaN(date.getTime())) {
            return value;
        }

        return date.toLocaleString();
    }

    buildEntryUrl(template, entryId) {
        return template.replace('__ID__', String(entryId));
    }

    async readJson(response) {
        return response.json().catch(() => null);
    }

    showToast(message, type = 'success') {
        const toast = document.createElement('div');
        toast.className = `ac-toast ac-toast-${type}`;
        toast.textContent = message;
        toast.style.bottom = '2rem';

        document.body.appendChild(toast);
        setTimeout(() => {
            toast.style.animation = 'slideOutRight 300ms ease';
            setTimeout(() => toast.remove(), 300);
        }, 3000);
    }
}
