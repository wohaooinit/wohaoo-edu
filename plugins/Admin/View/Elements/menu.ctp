<div id="menu">
	<ul id="menuBar" class="dropdown dropdown-horizontal">
		<?php if(!empty($menuItems['items'])) echo $this->Menu->list_items($menuItems['items']); ?>
	</ul>
</div>
<script type="text/javascript">
    $(document).ready(function(){

    	if($("ul.dropdown").length) {
    		$("ul.dropdown li").dropdown();
    	}

    });

    $.fn.dropdown = function() {

    	return this.each(function() {

    		$(this).hover(function(){
    			$(this).addClass("hover");
    			$('> .dir',this).addClass("open");
    			$('ul:first',this).css('visibility', 'visible');
    		},function(){
    			$(this).removeClass("hover");
    			$('.open',this).removeClass("open");
    			$('ul:first',this).css('visibility', 'hidden');
    		});

    	});

    }
</script>