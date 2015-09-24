define(['mustache', 'delayImg'], function(Mustache, dimg) {
	
	return {
		uri: $.baseUrl + 'index.php?route=rest/user/userlevel',
		userlist: $("#userlevelpage").find("#userlist"),
		itemTemp: $("#userlevelpage").find("#userlistItem").html(),
		btnBack: $("#userlevelpage").find("#userlevelback"),
		dataAll: null,
		curUserId: [0],
		getUsersAjax: function(userid) {
			var $this = this;
			$.ajax({
                url: $this.uri,
                type: 'POST',
                dataType: 'json',
				data: {
					userid: userid
				},
                success: function(data) {
                    if (data.result == '0') {
						$this.buildData(data);
						$this.refreshList();
                    }
					else {
					}
                }
            });
		},
		buildData: function(data) {
			var $this = this;
			$this.dataAll = data;
			[].forEach.call($this.dataAll.info, function(item, i) {
				item.iscustomer = function() {
					return item.usertype == 0;
				};
				item.isuser = function() {
					return item.usertype > 0;
				};
			});
		},
		refreshList: function() {
			var $this = this;
			if ($this.dataAll == null) return;
			
			if ($this.dataAll.cnt > 0) {
				var itm = Mustache.render($this.itemTemp, $this.dataAll);
				$this.userlist.html(itm);
				$this.userlist.find('li').bind($.sClick, function(e) {
					e.stopPropagation();
					var curid = $(this).attr("userid") - 0;
					var subcnt = $(this).attr("subcnt") - 0;
					if (subcnt > 0) {
						$this.curUserId.unshift(curid);
						$this.getUsersAjax(curid);
					}
				});
				$this.userlist.listview( "refresh" );
				dimg.init($this.userlist.find('li'));
			} else {
				$this.userlist.html("<p><center>没有下级帐户</center></p>");
				$this.userlist.listview( "refresh" );
			}
		},
		init: function() {
			var $this = this;
			$("#userlevelpage").off("pagebeforeshow");
			$("#userlevelpage").on("pagebeforeshow", function() {
				$this.getUsersAjax($this.curUserId[0]);
			});
			$("#userlevelpage").off("pageshow");
			$("#userlevelpage").on("pageshow", function() {
				dimg.init($this.userlist.find('li'));
			});
			
			$this.btnBack.off($.sClick);
			$this.btnBack.on($.sClick, function() {
				if ($this.curUserId[0] == 0)
					$.mobile.back();
				else {
					$this.curUserId.shift();
					$this.getUsersAjax($this.curUserId[0]);
				}
			});
		}
	}
});