define(['mustache', 'delayImg', 'user'], function(Mustache, dimg, user) { 
	return {
		uri: $.baseUrl + 'index.php?route=rest/apply',
		appList: null,
		appDetail: null,
		stepData: null,
		listTemp: $("#applyItem").html(),
		stepTemp: $("#stepItem").html(),
		detailTemp: $("#applydetail").html(),
		applyList: $("#applylist"),
		stepList: $("#steplist"),
		applyDetail: $("#detailContent"),
		applyFooter: $(".pfooter"),
		getAppCnt: function() {
			var $this = this;
			if ($this.appList) {
				return $this.appList.length;
			}
			return 0;
		},
		getAppList: function(listname) {
			var $this = this;
			$.ajax({
				url: $this.uri + "/" + listname,
				type: 'GET',
				dataType: 'json',
				success: function(data) {
					if (data.result == '0') {
						if ($this.appList == null)
							$this.appList = {};
						$this.appList = data;
						$this.buildAppData(data);
						$this.showList();
					} else if (data.result == "nologin") {
						$this.applyList.html("");
						user.showLoginPage("#homepage", "#applylistpage");
					} else {
						$this.applyList.html("<p><center>" + data.result + "</center></p>");
					}
				}
            })
		},
		getStepList: function(app_id) {
			var $this = this;
			$.ajax({
				url: $this.uri + "/appstep",
				type: 'POST',
				dataType: 'json',
				data: {
					app_id: app_id
				},
				success: function(data) {
					if (data.result == '0') {
						if ($this.stepData == null)
							$this.stepData = {};
						$this.stepData = data;
						$this.buildStepData(data, app_id);
						$this.showStep();
					} else if (data.result == "nologin") {
						$.mobile.navigate("#loginpage");
					} else {
						$this.stepList.html("<center>" + data.result + "</center>");
					}
				}
            })
		},
		getDetailList: function(app_id) {
			var $this = this;
			$.ajax({
				url: $this.uri + "/appdetail",
				type: 'POST',
				dataType: 'json',
				data: {
					app_id: app_id
				},
				success: function(data) {
					if (data.result == '0') {
						if ($this.appDetail == null)
							$this.appDetail = {};
						$this.appDetail = data;
						$this.buildDetailData(data);
						$this.showDetail();
					} else if (data.result == "nologin") {
						$.mobile.navigate("#loginpage");
					} else {
						$this.applyDetail.html("<center>" + data.result + "</center>");
					}
				}
            })
		},
		buildAppData: function(data) {
			var $this = this;
			$.each(data.info, function(index, item) {
				item.isover = function() {
					return item.app_status == 2;
				};
				item.isuser = function() {
					return typeof item.belong !== "undefined"
				};
				item.iscustomer = function() {
					return typeof item.belong === "undefined"
				};
				item.ishandling = function() {
					return item.app_status != 0;
				}
				item.no_image = data.no_image;
			});
		},
		buildStepData: function(data, app_id) {
			var $this = this;
			var app = null;
			$.each($this.appList.info, function(index, item) {
				if (app_id == item.app_id) {
					app = item;
				}
			});
			$.each(data.info, function(index, item) {
				item.isuser = function() {
					return typeof item.belong !== "undefined"
				};
				item.iscustomer = function() {
					return typeof item.belong === "undefined"
				};
				item.iscurstep = function() {
					if (app)
						return item.step_id == app.cur_step_id;
					else
						return false;
				};
				item.namecolor = function() {
					return (item.iscurstep())?"color:blue;":"";
				}
				item.actioncolor = function() {
					return (item.iscurstep())?"color:blue;":"color:gray;";
				}
			});
		},
		buildDetailData: function(data) {
			var $this = this;
			data.isover = function() {
				return data.app_status == 2;
			};
			data.isuser = function() {
				return typeof data.belong !== "undefined"
			};
			data.iscustomer = function() {
				return typeof data.belong === "undefined"
			};
			data.ishandling = function() {
				return data.app_status != 0;
			}
		},
		getCustomerList: function() {
			this.getAppList("customerlist");
		},
		getUserList: function() {
			this.getAppList("userlist");
		},
		getNewList: function() {
			this.getAppList("newapplist");
		},
		showList: function() {
			var $this = this;
			if ($this.appList.info != null) {
				var itm = Mustache.render($this.listTemp, $this.appList);
				$this.applyList.html(itm);
				$this.applyList.find('li').off($.sClick);
				$this.applyList.find('li').on($.sClick, function(e) {
					e.stopPropagation();
					$this.getDetailList($(this).attr("app_id"));
				});
				$this.applyList.find('.btnWrap').off($.sClick);
				$this.applyList.find('.btnWrap').on($.sClick, function(e) {
					e.stopPropagation();
					var curli = $(this).parent();
					$this.getStepList(curli.attr("app_id"));
				});
				$this.applyList.listview( "refresh" );
				dimg.init($this.applyList.find('li'));
			}
		},
		showStep: function() {
			var $this = this;
			if ($this.stepData.info != null) {
				$.mobile.navigate("#applysteppage");
				var itm = Mustache.render($this.stepTemp, $this.stepData);
				$this.stepList.html(itm);
				$this.stepList.listview( "refresh" );
			}
		},
		showDetail: function() {
			var $this = this;
			$.mobile.navigate("#applydetailpage");
			var itm = Mustache.render($this.detailTemp, $this.appDetail);
			$this.applyDetail.html(itm);
		},
		init: function() {
			var $this = this;
			$("#applylistpage").off("pagebeforeshow");
			$("#applylistpage").on("pagebeforeshow", function() {
				$this.getCustomerList();
			});
			
			$("#applylistpage").off("pageshow");
			$("#applylistpage").on("pageshow", function() {
				dimg.init($this.applyList.find('li'));
			});
			/*
			if ($this.applyFooter.length > 0) {
				var appBtn = $this.applyFooter.find("a");
				appBtn.attr("href", "javascript:;");
				appBtn.off($.sClick);
				appBtn.on($.sClick, function() {
					$this.getCustomerList();
					$.mobile.navigate("#applylistpage");
				});
			}*/
		}
	}
});