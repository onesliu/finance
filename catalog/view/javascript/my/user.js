define(['invitecode'], function(invitecode) {
	var	isMobile = window.navigator.userAgent.toLowerCase().indexOf('mobile') != -1,
		sClick = isMobile ? 'tap' : 'click';
		
	return {
		userInfo: null,
		uri: $.baseUrl + 'index.php?route=rest/user',
		
		btnloginback: $("#loginpage").find('#btnloginback'),
		btnlogin: $("#loginpage").find('#btnlogin'),
		input_user: $("#loginpage").find("input[name='user']"),
		input_pwd: $("#loginpage").find("input[name='pwd']"),
		nextpage: null,

		logininfo: $("#logininfopage").find("#login_info"),
		new_email: $("#logininfopage").find('#new_email'),
		new_name: $("#logininfopage").find("#new_name"),
		btnmodify: $("#logininfopage").find('#btnmodify'),
		btnnewpwd: $("#logininfopage").find("#btnnewpwd"),
		btnlogout: $("#logininfopage").find('#btnlogout'),
		
		input_newpwd1: $("#newpwdpage").find("input[name='newpwd1']"),
		input_newpwd2: $("#newpwdpage").find("input[name='newpwd2']"),
		btn_newpwd_ok: $("#newpwdpage").find("#btn_newpwd_ok"),

		btnregister: $("#registerpage").find('#btnregister'),
		input_email: $("#registerpage").find("input[name='email']"),
		input_telephone: $("#registerpage").find("input[name='telephone']"),
		input_username: $("#registerpage").find("input[name='username']"),
		input_password: $("#registerpage").find("input[name='password']"),
		input_password2: $("#registerpage").find("input[name='password2']"),
		input_invitation: $("#registerpage").find("input[name='invitecode']"),

		txt_loginUser: $("#homepage").find("#loginStatus").find("h2"),
		txt_loginType: $("#homepage").find("#loginStatus").find("p"),
		link_userlevel: $("#homepage").find("#userlevellink"),
		link_loginStatus: $("#homepage").find("#loginStatus"),

		showLoginPage: function(back, next) {
			var $this = this;
			$this.btnloginback.attr("href", back);
			$this.nextpage = next;
			$.mobile.navigate("#loginpage");
		},
		resetLoginPage: function() {
			var $this = this;
			$this.btnloginback.attr("href", "#homepage");
			$this.nextpage = null;
		},
		load: function() {
			var $this = this;
			$.ajax({
                url: $this.uri,
                type: 'GET',
                dataType: 'json',
                success: function(data) {
                    if (data.result == '0') {
						$this.userInfo = data;
						$this.login_status(true, data);
                    }
					else {
						$this.login_status(false);
					}
                }
            });
		},
		login_status: function(islogin, data) {
			var $this = this;
			var cur_page = $( ":mobile-pagecontainer" ).pagecontainer("getActivePage");
			if (islogin && islogin == true) {
				if (cur_page.eq(0).attr("id") == "loginpage") {
					$this.input_user.textinput("disable");
					$this.input_user.val(data.user);
					$this.input_pwd.textinput("disable");
					$this.input_pwd.val("");
					$this.btnlogin.hide();
				}
				$this.txt_loginUser.html(data.user + " " + data.name);
				$this.txt_loginType.html(data.typename);
				$this.logininfo.html(data.user + " " + data.typename);
				$this.new_email.val(data.email);
				$this.new_name.val(data.name);
				$this.link_loginStatus.attr("href", "#logininfopage");
				if (data.usertype > 0)
					$this.link_userlevel.show();
				else
					$this.link_userlevel.hide();
				invitecode.init(data.usertype);
			}
			else {
				if (cur_page.eq(0).attr("id") == "loginpage") {
					$this.input_user.textinput("enable");
					$this.input_user.val("");
					$this.input_pwd.textinput("enable");
					$this.input_pwd.val("");
					$this.btnlogin.show();
				}
				$this.txt_loginUser.html("未登录");
				$this.txt_loginType.html("普通会员");
				$this.link_loginStatus.attr("href", "#loginpage");
				$this.link_userlevel.hide();
				invitecode.init();
			}
		},
		login: function() {
			var $this = this;
			var user = $this.input_user.val();
			var pwd = $this.input_pwd.val();
			
			if (!user || !pwd) {
				$.alertPopup.mypopup('请输入帐号和密码');
				return;
			}

			$.ajax({
                url: $this.uri + '/login',
                type: 'POST',
                dataType: 'json',
				data: {
					user: user,
					password: pwd
				},
                success: function(data) {
                    if (data.result == '0') {
						$this.userInfo = data;
						$this.login_status(true, data);
						if ($this.nextpage) {
							$.mobile.navigate($this.nextpage);
						}
						else {
							$.mobile.back();
						}
                    } else {
						$this.login_status(false);
						$.alertPopup.mypopup('登录失败');
					}
                }
            });
		},
		logout: function() {
			var $this = this;
			$.ajax({
                url: $this.uri + '/logout',
                type: 'GET',
                dataType: 'json',
                success: function(data) {
                    if (data.result == '0') {
						$this.userInfo = null;
						$this.login_status(false);
						$.mobile.back();
                    } else {
						$.alertPopup.mypopup('退出登录失败');
					}
                }
            });
		},
		changepwd: function() {
			var $this = this;
			
			var password = $this.input_newpwd1.val();
			var password2 = $this.input_newpwd2.val();
			
			if (!password) {
				$.alertPopup.mypopup('请输入新密码！');
				return;
			}
			
			if (password != password2) {
				$.alertPopup.mypopup('两次输入的密码不相同！');
				return;
			}

			$.ajax({
                url: $this.uri + '/changepwd',
                type: 'POST',
                dataType: 'json',
				data: {
					newpwd: password
				},
                success: function(data) {
                    if (data.result == '0') {
						$.alertPopup.mypopup('修改密码成功！', function() {
							$.mobile.back();
						});
                    } else {
						$.alertPopup.mypopup(data.error);
					}
                }
            });
		},
		savemodify: function() {
			var $this = this;
			var new_name = $this.new_name.val();
			var new_email = $this.new_email.val();
			$.ajax({
                url: $this.uri + '/saveinfo',
                type: 'POST',
                dataType: 'json',
				data: {
					username: new_name,
					email: new_email
				},
                success: function(data) {
                    if (data.result == '0') {
						$.alertPopup.mypopup('保存成功！');
                    } else {
						$.alertPopup.mypopup(data.error);
					}
                }
            });
		},
		register: function() {
			var $this = this;
			var email = $this.input_email.val();
			var telephone = $this.input_telephone.val();
			var username = $this.input_username.val();
			var password = $this.input_password.val();
			var password2 = $this.input_password2.val();
			var invitation = $this.input_invitation.val();
			
			if (!telephone || !password || !password2 || !username || !email || !invitation) {
				$.alertPopup.mypopup('请输入完整的注册信息');
				return;
			}
			
			if (password != password2) {
				$.alertPopup.mypopup('两次输入的密码不相同');
				return;
			}
			
			$.ajax({
                url: $this.uri + '/register',
                type: 'POST',
                dataType: 'json',
				data: {
					telephone: telephone,
					password: password,
					email: email,
					username: username,
					invitecode: invitation
				},
                success: function(data) {
                    if (data.result == '0') {
						$.alertPopup.mypopup('注册成功！', function() {
							$this.input_user.val(telephone);
							$this.input_pwd.val("");
							$.mobile.back();
						});
                    } else {
						$.alertPopup.mypopup(data.error);
					}
                }
            });
		},
		init: function() {
			var $this = this;
			if ($this.btnlogin.length > 0) {
				$this.btnlogin.off(sClick);
				$this.btnlogin.on(sClick, function() {
					$this.login();
				});
			}
			if ($this.btnlogout.length > 0) {
				$this.btnlogout.off(sClick);
				$this.btnlogout.on(sClick, function() {
					$this.logout();
				});
			}
			if ($this.btnregister.length > 0) {
				$this.btnregister.off(sClick);
				$this.btnregister.on(sClick, function() {
					$this.register();
				});
			}
			if ($this.btn_newpwd_ok.length > 0) {
				$this.btn_newpwd_ok.off(sClick);
				$this.btn_newpwd_ok.on(sClick, function() {
					$this.changepwd();
				});
			}
			
			if ($this.btnmodify.length > 0) {
				$this.btnmodify.off($.sClick);
				$this.btnmodify.on($.sClick, function() {
					$this.savemodify();
				});
			}
			
			$("#newpwdpage").off("pagebeforeshow");
			$("#newpwdpage").on("pagebeforeshow", function() {
				$this.input_newpwd1.val("");
				$this.input_newpwd2.val("");
			});

			$("#loginpage").off("pagebeforeshow");
			$("#loginpage").on("pagebeforeshow", function() {
				$this.load();
			});
			$("#loginpage").off("pagehide");
			$("#loginpage").on("pagehide", function() {
				$this.resetLoginPage();
			})
		}
	}
})
