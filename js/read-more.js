(function($) {
    'use strict';
    
    class ReadMore {
        constructor() {
            this.bindEvents();
            this.loadedPosts = new Set();
        }
        
        bindEvents() {
            $(document).on('click', '.more-link', (e) => {
                e.preventDefault();
                const $link = $(e.currentTarget);
                const postId = this.getPostId($link);
                
                if (postId && !this.loadedPosts.has(postId)) {
                    this.loadContent($link, postId);
                }
            });
        }
        
        getPostId($link) {
            const postId = $link.data('post-id');
            if (!postId) {
                console.warn('ReadMore: Keine Post-ID gefunden');
                return null;
            }
            return postId;
        }
        
        loadContent($link, postId) {
            const $article = $link.closest('article, .post');
            const $container = $link.parent();
            
            $container.addClass('loading');
            
            $.ajax({
                url: readMoreAjax.ajaxurl,
                type: 'POST',
                data: {
                    action: 'load_full_post',
                    post_id: postId,
                    nonce: readMoreAjax.nonce
                },
                success: (response) => {
                    if (response.success) {
                        if (response.data.replace_all) {
                            // Ersetze den gesamten Artikel-Inhalt
                            $article.find('.entry-content, .post-content').html(response.data.content);
                        } else {
                            // Füge nur den neuen Inhalt nach dem Link ein
                            $link.after(response.data.content);
                        }
                        $link.remove();
                        this.loadedPosts.add(postId);
                    }
                },
                error: (xhr, status, error) => {
                    console.error('ReadMore: AJAX error', error);
                    window.location = $link.attr('href');
                },
                complete: () => {
                    $container.removeClass('loading');
                }
            });
        }
    }
    
    $(document).ready(() => new ReadMore());
    
})(jQuery); 