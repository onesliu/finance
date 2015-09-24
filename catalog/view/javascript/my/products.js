define(['mustache', 'delayImg', 'requires'],
	function(Mustache, dimg, requires) {
		
	return {
		uri: $.baseUrl + 'index.php?route=rest/products',
		appuri: $.baseUrl + 'index.php?route=rest/apply',
		dataAll: null,
		productAll: null,
		pTitle: null,
		pList: null,
		pTemp: null,
		cur_li: null,
		cur_cat: 0,
		no_image: "",
		applyDialog: $("#applyDialog"),
		pAppLimit: $("#applimit"),
		pAppPeriod: $("#appperiod"),
		btnApply: $("#applyBtn"),
		productAjax: function(cat_id, limit) {
			if (cat_id == null) return;
            var $this = this;
			if (!limit) limit = 0;
			
            $.ajax({
                url: $this.uri,
                type: 'POST',
                dataType: 'json',
				data: {
					category: cat_id,
					limit: limit
				},
                success: function(data) {
                    if (data.result == '0') {
						$this.no_image = data.no_image;
						if ($this.dataAll == null)
							$this.dataAll = {};
                        $this.dataAll[cat_id] = data;
                        $this.buildData(data.info);
						$this.showList(cat_id);
                    } else {
                    }
                }
            })
        },
		buildData: function(data) {
			var $this = this;
			if (data.length == 0) {
                return false;
            }
			if ($this.productAll == null)
				$this.productAll = [];
            [].forEach.call(data, function(item, i) {
				item.minlimit = item.minlimit-0;
				item.maxlimit = item.maxlimit-0;
				item.minperiod = item.minperiod-0;
				item.maxperiod = item.maxperiod-0;
				item.minrate = item.minrate-0;
				item.maxrate = item.maxrate-0;
				item.type = function() {
					if ((item.minlimit > 0 && item.minlimit < 1) && 
						(item.maxlimit > 0 && item.maxlimit < 1))
						return 1;  //抵押
					return 0; //信贷
				};
				item.limit = function() {
					var ret;
					if (item.type == 1)
						ret = "评估价" + item.minlimit*100 + "%-" + item.maxlimit*100 + "%";
					else
						ret = item.minlimit + "-" + item.maxlimit + "万";
					return ret;
				};
				item.rate = function() {
					var rate;
					if (item.maxrate != "0")
						rate = item.minrate + "%-" + item.maxrate + "%";
					else
						rate = item.minrate + "%";
					return rate;
				};
				item.period = function() {
					var sp = "";
					item.periods = [];
					for(var i = (item.minperiod-0); i <= (item.maxperiod-0); i += (item.periodstep-0)) {
						sp = sp + i + "/";
						item.periods.push(i);
					}
					return sp + "月";
				};
				item.age = function() {
					return item.minage + "-" + item.maxage;
				};
				item.match = function() {
					if (item.matching)
						return item.matching + "%";
					else
						return "0%";
				};
				item.no_image = $this.no_image;
				$this.productAll[item.product_id] = item;
            });
		},
		showList: function(cat_id) {
			var $this = this;
			if ($this.pList == null) return;
			if ($this.dataAll == null) return;
			
			var dataArr = {info: null};
			dataArr.info = $this.dataAll[cat_id].info;

			if (dataArr.info != null) {
				var itm = Mustache.render($this.pTemp, dataArr);
				$this.pList.html(itm);
				$this.pList.find('.btnWrap').bind($.sClick, function(e) {
					e.stopPropagation();
					$this.cur_li = $(this).parent();
					$this.showProduct($this.cur_li.attr("product_id"));
				});
				$this.pList.listview( "refresh" );
				dimg.init($this.pList.find('li'));
			}
		},
		showProduct: function(product_id) {
			var $this = this;
			$this.pDetail = $("#detailpage");
			
			var pTitle = $("#productName"),
				tInfo1 = $("#productInfo1").html(),
				tInfo2 = $("#productInfo2").html(),
				pInfo1 = $("#pinfo1"),
				pInfo2 = $("#pinfo2"),
				pMaterial = $("#material"),
				pRemark = $("#remark");
				
			var product = $this.productAll[product_id];
			pTitle.html(product.name);
			var srender = Mustache.render(tInfo1, product);
			pInfo1.html(srender);
			requires.getrpAjax(product_id, function() {
				
				$.mobile.navigate("#detailpage");
				var req = {};
				req.require = requires.getByProduct(product_id);
				var srender = Mustache.render(tInfo2, req);
				pInfo2.html(srender);
				$this.pDetail.find("input").each(function(){
					$(this).textinput();
					//$(this).off("change");
					$(this).on("change", function(){
						requires.setRequire($(this).attr("rid"), $(this).attr("rvid"), $(this).val());
					});
				});
				$this.pDetail.find("select").each(function(){
					$(this).selectmenu();
					//$(this).off("change");
					$(this).on("change", function(){
						requires.setRequire($(this).attr("rid"), $(this).val());
					});
				});
			});
			if (product.material.length > 0)
				pMaterial.html(product.material);
			else
				pMaterial.hide();
			if (product.remark.length > 0)
				pRemark.html(product.remark);
			else
				pRemark.hide();
			
			//以下是申请对话框初始化
			$this.pAppError($this.pAppLimit);
			$this.pAppError($this.pAppPeriod);
			$this.pAppError($this.btnApply);
			if (product.type() == 1) {
				$this.pAppLimit.val(product.maxlimit * 10 + "成");
				$this.pAppLimit.attr("readonly");
			} else {
				$this.pAppLimit.removeAttr("readonly");
				$this.pAppLimit.val(product.maxlimit);
			}
			
			if ($this.pAppPeriod.length > 0) {
				$this.pAppPeriod.empty();
				$this.pAppPeriod.append("<option value='0'>还款期限（月）</option>");
				$.each(product.periods, function(index, val) {
					$this.pAppPeriod.append("<option value='"+val+"'>"+val+"</option>");
				});
				$this.pAppPeriod.selectmenu();
				$this.pAppPeriod.selectmenu("refresh");
			}
			
			$this.btnApply.off($.sClick);
			$this.btnApply.on($.sClick, function(){
				$this.pAppLimit.tiptxt.hide();
				$this.pAppPeriod.tiptxt.hide();
				var limit = $this.pAppLimit.val()-1;
				if (product.type() == 0) {
					if (limit > product.maxlimit || limit < product.minlimit) {
						$this.pAppLimit.tiptxt.html("申请额度要在"+ product.limit() + "之间");
						$this.pAppLimit.tiptxt.show();
						return;
					}
				}
				var period = $this.pAppPeriod.val();
				if (!period || period <= 0) {
					$this.pAppPeriod.tiptxt.html("请选择还款期限！");
					$this.pAppPeriod.tiptxt.show();
					return;
				}
				
				$this.Apply(product, limit, period);
			});
		},
		pAppError: function(input) {
			if (!input.tiptxt)
				input.tiptxt = input.next("p");
			input.tiptxt.hide();
		},
		Apply: function(product, limit, period) {
            var $this = this;
			if (!product) return;
			if (!limit) limit = product.maxlimit;
			if (product.type() == 1) limit = product.maxlimit;
			if (!period) period = product.maxperiod;
			
            $.ajax({
                url: $this.appuri + "/apply",
                type: 'POST',
                dataType: 'json',
				data: {
					product_id: product.product_id,
					limit: limit,
					period: period
				},
                success: function(data) {
                    if (data.result == '0') {
						//$this.applyDialog.popup("close");
						$.mobile.navigate("#applylistpage");
                    } else {
						$this.pAppError($this.btnApply);
						$this.btnApply.tiptxt.html(data.result);
						$this.btnApply.tiptxt.show();
                    }
                }
            })
		},
		show: function(cat_id) {
			var $this = this;
			$this.cur_cat = cat_id;
			$.mobile.navigate("#productpage");

			if (!$this.pTitle)
				$this.pTitle = $('#plistName');
			if (!$this.pList)
				$this.pList = $('#productlist');
			if (!$this.pTemp)
				$this.pTemp = $('#productItem').html();
			
			$this.pTitle.text(cat_id);
			//$this.productAjax(cat_id);
		},
		init: function(cat_arr, limit) {
			var $this = this;

			$(document).off("pagebeforeshow", "#productpage");
			$(document).on("pagebeforeshow", "#productpage", function() {
				$this.pList.html("");
				$this.productAjax($this.cur_cat, limit);
			});
			$(document).off("pageshow", "#productpage");
			$(document).on("pageshow", "#productpage", function() {
				dimg.init($this.pList.find('li'));
			});
		}
	};

})