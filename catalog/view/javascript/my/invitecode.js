define([], function() {
	var	isMobile = window.navigator.userAgent.toLowerCase().indexOf('mobile') != -1,
		sClick = isMobile ? 'tap' : 'click';
		
	return {
		uri: $.baseUrl + 'index.php?route=rest/invitecode',
		invitelink: $("#invitelink"),
		userlevel0: $("#userlevel0"),
		userlevel1: $("#userlevel1"),
		userlevel2: $("#userlevel2"),
		invite: function(usertype) {
			var $this = this;
			var btn = $("#invitebtn" + usertype),
				label = $("#codelabel" + usertype);

			$.ajax({
                url: $this.uri,
                type: 'POST',
                dataType: 'json',
				data: {
					usertype: usertype
				},
                success: function(data) {
                    if (data.result == '0') {
						//btn.button("disable");
						label.val(data.code);
                    } else {
						$.alertPopup.mypopup(data.msg);
					}
                }
            });
		},
		init: function(usertype) {
			var $this = this;
			
			$this.userlevel0.hide();
			if (usertype == 2) {
				$this.invitelink.show();
				$this.userlevel1.show();
				$this.userlevel2.show();
			}
			else {
				$this.invitelink.hide();
				$this.userlevel1.hide();
				$this.userlevel2.hide();
			}
			
			for(var i = 0; i < 3; i++) {
				var btn = $("#invitebtn" + i);
				if (btn.length > 0) {
					btn[0].utypeid = i;
					btn.off(sClick);
					btn.on(sClick, function() {
						$this.invite($(this)[0].utypeid);
					});
				}
			}
		}
	}
})