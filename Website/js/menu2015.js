$(document).ready(function() {
	$(".link").hover(function(e) {		
        $(".parent-menu").parent().find("a.active").removeClass("active");
		$(".child-menu").hide();
		
		$(this).addClass("active");
        $("#SubMenu_"+this.id).show();
		
		$(".menu").mouseleave(function() {
			$(".parent-menu").parent().find("a.active").removeClass("active");
			$(".child-menu").hide();
		});		
    });
});