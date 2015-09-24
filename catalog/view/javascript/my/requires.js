define([], function() {
    var	isMobile = window.navigator.userAgent.toLowerCase().indexOf('mobile') != -1,
		sClick = isMobile ? 'tap' : 'click';

    return {
        uri: $.baseUrl + 'index.php?route=rest/requires',
        requireAll: null,
		productRequire: null,
		getByProduct: function(product_id) {
			return this.productRequire[product_id];
		},
		buildData: function(data) {
			var $this = this;
			if (data.length == 0)
                return false;
			if ($this.requireAll == null)
				$this.requireAll = [];
			[].forEach.call(data, function(item, i) {
				item.isset = function() {
					return (item.value_type == "set") || (item.value_type == "order");
				};
				item.isnumber = function() {
					return item.value_type == "number";
				};
				item.uuid = function() {
					var myDate = new Date();
					return item.require_id + "_" + myDate.getTime();
				};
				[].forEach.call(item.rvs, function(ri, j) {
					ri.selected = function() {
						return item.rvalue_id == ri.rvalue_id;
					}
				});
				$this.requireAll[item.require_id] = item;
			});
		},
		getallAjax: function() {
            var $this = this;
            $.ajax({
                url: $this.uri,
                type: 'GET',
                dataType: 'json',
                success: function(data) {
                    if (data.result == '0') {
                        $this.buildData(data.info);
                    } else if (data.result == "nologin"){
                        $.mobile.navigate("#loginpage");
                    }
                }
            })
        },
		getrpAjax: function(product_id, dofunc) {
			if (!product_id) return;
			var $this = this;
            $.ajax({
                url: $this.uri + "/productreq",
                type: 'POST',
                dataType: 'json',
				data: {
					product_id: product_id
				},
                success: function(data) {
                    if (data.result == '0') {
						$this.buildData(data.info);
						if (!$this.productRequire)
							$this.productRequire = [];
                        $this.productRequire[product_id] = data.info;
						dofunc($this.productRequire[product_id]);
                    } else if (data.result == "nologin"){
                        $.mobile.navigate("#loginpage");
                    }
                }
            })
		},
		setRequire: function(req_id, rvalue_id, crvalue) {
			if (!req_id || isNaN(req_id)) return;
			if (!rvalue_id || isNaN(rvalue_id)) return;
			if (!crvalue) crvalue = "";
			var $this = this;
            $.ajax({
                url: $this.uri + "/set",
                type: 'POST',
                dataType: 'json',
				data: {
					require_id: req_id,
					rvalue_id: rvalue_id,
					crvalue: crvalue
				},
                success: function(data) {
                    if (data.result == '0') {
                    } else {
                    }
                }
            })
		},
		getClassRequire: function(dofunc) {
			var $this = this;
			$.ajax({
                url: $this.uri + "/classreq",
                type: 'POST',
                dataType: 'json',
				data: {
					class_id: 0
				},
                success: function(data) {
                    if (data.result == '0') {
						$this.buildData(data.info);
						dofunc(data.info);
                    } else if (data.result == "nologin"){
                        $.mobile.navigate("#loginpage");
                    }
                }
            })
		}
    };
})