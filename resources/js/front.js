/**
 * Laravella CMS — public theme interactivity (Alpine.js + fetch).
 *
 * Replaces the legacy jQuery/Bootstrap front bundle (like.js, comment.js,
 * nice-select, sticky nav, magnific lightbox). Everything hits the SAME
 * backend routes; only the client implementation changed.
 *
 * CSRF: read from <meta name="csrf-token"> and sent as the X-CSRF-TOKEN header.
 */

function csrfToken() {
    const el = document.head.querySelector('meta[name="csrf-token"]');
    return el ? el.getAttribute('content') : '';
}

async function postJson(url, method, body) {
    const res = await fetch(url, {
        method,
        headers: {
            'X-CSRF-TOKEN': csrfToken(),
            'Content-Type': 'application/json',
            Accept: 'application/json',
        },
        credentials: 'same-origin',
        body: body ? JSON.stringify(body) : undefined,
    });
    return res.json();
}

export function registerFrontComponents(Alpine) {
    /**
     * Post "like" toggle. Mirrors the old like.js contract: the backend
     * returns the localized "like added" / "like deleted" strings, and we
     * recompute the human-readable like summary locally.
     */
    Alpine.data('postLike', (config) => ({
        liked: config.liked,
        likes: config.likes,
        busy: false,
        lang: config.lang, // localized strings injected from blade

        get summary() {
            if (this.liked) {
                if (this.likes > 1) {
                    return this.lang.youAndPre + ' ' + (this.likes - 1) + ' ' + this.lang.youAndAfter;
                }
                return this.lang.youOnly;
            }
            if (this.likes > 0) {
                return this.likes + ' ' + this.lang.othersAfter;
            }
            return this.lang.nobody;
        },

        get label() {
            return this.liked ? this.lang.dislike : this.lang.like;
        },

        async toggle() {
            if (this.busy) return;
            this.busy = true;
            try {
                const message = await postJson(config.url, 'POST', {
                    postId: config.postId,
                    userId: config.userId,
                });
                if (message === this.lang.likeAdded) {
                    this.liked = true;
                    this.likes += 1;
                } else if (message === this.lang.likeDeleted) {
                    this.liked = false;
                    this.likes = Math.max(0, this.likes - 1);
                }
            } catch (e) {
                // Network/permission failure: leave UI untouched.
                console.error(e);
            } finally {
                this.busy = false;
            }
        },
    }));

    /**
     * Comment thread interactivity: reply (prefill + scroll), edit (open
     * dialog), delete (AJAX remove). Same routes as the old comment.js.
     */
    Alpine.data('commentThread', (config) => ({
        deleteUrl: config.deleteUrl,
        confirmText: config.confirmText,

        reply(name) {
            const parentId = this.$event.currentTarget.dataset.commentId;
            const field = document.getElementById('comment-field');
            const parentInput = document.getElementById('comment_parent_id');
            if (field && parentInput) {
                parentInput.value = parentId;
                field.value = name + ', ';
                field.focus();
            }
            const area = document.getElementById('comment-area');
            if (area) area.scrollIntoView({ behavior: 'smooth' });
        },

        edit() {
            const btn = this.$event.currentTarget;
            this.$dispatch('open-edit-comment', {
                id: btn.dataset.commentId,
                comment: btn.dataset.comment,
            });
        },

        async remove() {
            const btn = this.$event.currentTarget;
            if (!window.confirm(this.confirmText)) return;
            try {
                const message = await postJson(this.deleteUrl, 'DELETE', {
                    commentId: btn.dataset.commentId,
                    username: btn.dataset.username,
                });
                if (message === 'Comment has been deleted') {
                    const card = btn.closest('[data-comment-card]');
                    if (card) {
                        const next = card.nextElementSibling;
                        if (next && next.hasAttribute('data-comment-replies')) next.remove();
                        card.remove();
                    }
                } else {
                    console.warn(message);
                }
            } catch (e) {
                console.error(e);
            }
        },
    }));

    /**
     * Edit-comment dialog (replaces the Bootstrap modal). Uses the native
     * <dialog> element; submits to update_post_comment via a normal form POST.
     */
    Alpine.data('editCommentDialog', () => ({
        open: false,
        init() {
            this.$watch('open', (v) => {
                const dlg = this.$refs.dialog;
                if (!dlg) return;
                if (v && !dlg.open) dlg.showModal();
                if (!v && dlg.open) dlg.close();
            });
        },
        show(detail) {
            this.$refs.field.value = detail.comment || '';
            this.$refs.id.value = detail.id || '';
            this.open = true;
        },
        close() {
            this.open = false;
        },
    }));

    /**
     * Avatar upload + live preview (replaces the legacy userprofile.js).
     * Shows the chosen image immediately; falls back gracefully if no file.
     */
    Alpine.data('avatarUpload', (initialSrc) => ({
        preview: initialSrc,
        pick() {
            const file = this.$event.target.files && this.$event.target.files[0];
            if (!file) return;
            if (!/\.(gif|jpe?g|png|webp)$/i.test(file.name)) return;
            this.preview = URL.createObjectURL(file);
        },
    }));

    /**
     * Scroll-reveal. Adds .is-revealed when the element enters the viewport.
     * Honors reduced-motion (no-op there; content is visible by default).
     */
    Alpine.data('reveal', (delay = 0) => ({
        init() {
            const reduce = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
            if (reduce) {
                this.$el.classList.add('is-revealed');
                return;
            }
            const io = new IntersectionObserver(
                (entries) => {
                    entries.forEach((entry) => {
                        if (entry.isIntersecting) {
                            setTimeout(() => entry.target.classList.add('is-revealed'), delay);
                            io.unobserve(entry.target);
                        }
                    });
                },
                { threshold: 0.12, rootMargin: '0px 0px -8% 0px' }
            );
            io.observe(this.$el);
        },
    }));
}
