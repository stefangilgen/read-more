(function($) {
    'use strict';
    
    class ReadMore {
        constructor() {
            this.bindEvents();
            this.loadedPosts = new Set();
        }
        
        bindEvents() {
            console.log('ReadMore: Binding events...'); // Debug
            $(document).on('click', '.more-link', (e) => {
                console.log('ReadMore: Link clicked'); // Debug
                e.preventDefault();
                const $link = $(e.currentTarget);
                const postId = this.getPostId($link);
                
                console.log('ReadMore: Post ID:', postId); // Debug
                
                if (postId && !this.loadedPosts.has(postId)) {
                    this.loadContent($link, postId);
                }
            });
        }
        
        getPostId($link) {
            // Erste Priorität: data-post-id am Link selbst
            const linkPostId = $link.data('post-id');
            if (linkPostId) return linkPostId;

            // Zweite Priorität: Suche nach article mit ID
            const $article = $link.closest('article');
            const articleId = $article.attr('id')?.replace('post-', '');
            if (articleId) return articleId;

            // Dritte Priorität: Suche nach verstecktem Input-Feld
            const $hidden = $link.closest('.post').find('input[name="post_id"]');
            const hiddenId = $hidden.val();
            if (hiddenId) return hiddenId;

            console.warn('ReadMore: Konnte keine Post-ID finden für:', $link);
            return null;
        }
        
        loadContent($link, postId) {
            console.log('ReadMore: Loading content for post', postId); // Debug
            const $container = $link.parent();
            
            $container.addClass('loading');
            
            $.ajax({
                url: readMoreAjax.ajaxurl,
                type: 'POST',
                data: {
                    action: 'load_more_content',
                    post_id: postId,
                    nonce: readMoreAjax.nonce
                },
                success: (response) => {
                    console.log('ReadMore: AJAX response', response); // Debug
                    if (response.success) {
                        this.insertContent($container, $link, response.data.content);
                        this.loadedPosts.add(postId);
                    }
                },
                error: (xhr, status, error) => {
                    console.error('ReadMore: AJAX error', error); // Debug
                    // Fallback: Zur Einzelansicht navigieren
                    window.location = $link.attr('href');
                },
                complete: () => {
                    $container.removeClass('loading');
                }
            });
        }
        
        insertContent($container, $link, content) {
            const $content = $('<div class="more-content"></div>').html(content);
            
            // Smooth Animation
            $content.hide();
            $link.after($content);
            $content.slideDown();
            $link.fadeOut(() => $link.remove());
            
            // Event triggern für andere Plugins
            $(document).trigger('readmore:loaded', [$content]);
        }
    }
    
    // Initialisierung wenn DOM bereit
    $(document).ready(() => new ReadMore());
    
})(jQuery); 