document.observe('dom:loaded', function() {

    function moreLessClick(event)
    {
        event.preventDefault();
        var id = 'filter-more-less-' + this.readAttribute('data-num');
        var status = this.readAttribute('data-status');
        if (status == 'less') {
            this.innerHTML = moreLess.less;
            this.setAttribute('data-status', 'more');
        } else {
            this.innerHTML = moreLess.more;
            this.setAttribute('data-status', 'less');
        }
        Effect.toggle(id, 'blind', {duration: 0.5});
    }

    $$('.filter-more-less-btn').each(
        function(element) {
          Event.observe(element, "click", moreLessClick);
        }
    );
});