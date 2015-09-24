define(['mustache', 'products', 'requires', 'delayImg'],
	function(Mustache, products, requires, dimg) {

    return {
        uri: $.baseUrl + 'index.php?route=rest/category',
        dataAll: null,
        catlist: $('#categorylist'),
        itemTmp: $('#categoryItem').html(),
        categoryAjax: function(limit) {
            var $this = this;
			var url = $this.uri;
			if (limit) url += "/match";
            $.ajax({
                url: url,
                type: 'GET',
                dataType: 'json',
                success: function(data) {
					$this.dataAll = null;
                    if (data.result == "nologin"){
						if (limit)
							$.mobile.navigate("#loginpage");
                    } else {
                        $this.dataAll = data;
						products.init(data.info, limit);
						$this.show();
                    }
                }
            })
        },
        show: function() {
			var $this = this;
			if ($this.dataAll.total > 0) {
				$this.catlist.html(Mustache.render($this.itemTmp, $this.dataAll));
				$this.catlist.find('li').bind($.sClick, function(e) {
					e.stopPropagation();
					var category_id = $(this).attr("category_id");
					products.show(category_id);
				});
				$this.catlist.listview( "refresh" );
				dimg.init($this.catlist.find('li'));
			} else {
				$this.catlist.html("<p><center>没有匹配的产品</center></p>");
				$this.catlist.listview( "refresh" );
			}
		},
		showClassRequire: function() {
			requires.getClassRequire(function(data) {
				var tmpInfo2 = $("#productInfo2").html(),
					baseRequire = $("#baseRequire");
				var d = {};
				d.require = data;

				$.mobile.navigate("#searchpage");
				var srender = Mustache.render(tmpInfo2, d);
				baseRequire.html(srender);
				$("#searchpage").find("input").each(function(){
					$(this).textinput();
					$(this).on("change", function(){
						requires.setRequire($(this).attr("rid"), $(this).attr("rvid"), $(this).val());
					});
				});
				$("#searchpage").find("select").each(function(){
					$(this).selectmenu();
					$(this).on("change", function(){
						requires.setRequire($(this).attr("rid"), $(this).val());
					});
				})
			});
		},
        init: function() {
            var $this = this;
			var btnProduct = $("#btnProduct");
			var btnProductFilter = $("#btnProductFilter");
			var btnSite = $("#btnSite");
			var btnFaq = $("#btnFaq");
			var btnFilter = $("#btnFilter");
			
			btnProduct.off($.sClick);
			btnProduct.on($.sClick, function() {
				$this.categoryAjax();
			});
			btnProductFilter.off($.sClick);
			btnProductFilter.on($.sClick, function() {
				$this.showClassRequire();
				btnFilter.off($.sClick);
				btnFilter.on($.sClick, function() {
					$this.categoryAjax(1);
				})
			});
			btnSite.off($.sClick);
			btnSite.on($.sClick, function() {
				$.alertPopup.mypopup("营业网点地图正在开发中...");
			});
			btnFaq.off($.sClick);
			btnFaq.on($.sClick, function() {
				$.mobile.navigate("#faqpage");
			});
			
            $this.categoryAjax();
        }
    };
})
